<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Locate recurring subscriptions not marked as such and fix the records in our database
 *
 * --for-real              Apply changes to the database.
 *
 * Note: --days overrides --min-date if both are set
 */

use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

// Enable Joomla's debug mode
define('JDEBUG', 1);

// Boilerplate -- START
define('_JEXEC', 1);

foreach ([__DIR__, getcwd()] as $curdir)
{
	if (file_exists($curdir . '/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/defines.php';

		break;
	}

	if (file_exists($curdir . '/../includes/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/../includes/defines.php';

		break;
	}
}

defined('JPATH_LIBRARIES') || die ('This script must be placed in or run from the cli folder of your site.');

require_once JPATH_LIBRARIES . '/fof30/Cli/Application.php';
// Boilerplate -- END

/**
 * A simplistic exception handler
 *
 * @param   Throwable  $e
 *
 * @since   7.0.0
 */
function akeebasubsCliExceptionHandler(Throwable $e)
{
	$exceptionType = get_class($e);
	echo <<< END

================================================================================
Exception
================================================================================

Exception Type: $exceptionType
Code:           {$e->getCode()} 
Message:        {$e->getMessage()}
File:           {$e->getFile()}
Line:           {$e->getLine()}
Call stack:

{$e->getTraceAsString()}

END;

	$code = $e->getCode();

	if (!$code)
	{
		$code = 255;
	}

	exit($code);
}

set_exception_handler('akeebasubsCliExceptionHandler');

class AkeebasubsFixRecurring extends FOFApplicationCLI
{
	/**
	 * Paddle Vendor ID
	 *
	 * @var   int
	 * @since 7.1.0
	 */
	private $vendorId = 0;

	/**
	 * Paddle vendor authentication code
	 *
	 * @var   string
	 * @since 7.1.0
	 */
	private $vendorAuthCode = '';

	/**
	 * Should I apply changes automatically, for real?
	 *
	 * @var   bool
	 * @since 7.1.0
	 */
	private $forReal = false;

	protected function doExecute()
	{
		// Get the Akeeba Subscriptions container
		$container = Container::getInstance('com_akeebasubs');

		// Am I doing this for real?
		$this->forReal = $this->input->getBool('for-real', false);

		// Get the Paddle configuration from the container
		$this->vendorId       = $container->params->get('vendor_id');
		$this->vendorAuthCode = $container->params->get('vendor_auth_code');

		if (empty($this->vendorId) || empty($this->vendorAuthCode))
		{
			$this->out('ERROR: Paddle vendor ID and / or authentication code are not set.');

			$this->close(200);
		}

		// Fake the $_SERVER superglobals so JUri doesn't complain. Required for running integrations.
		$uri                    = new Joomla\CMS\Uri\Uri($container->params->get('siteurl'));
		$_SERVER['HTTP_HOST']   = $uri->getHost();
		$_SERVER['SCRIPT_NAME'] = $uri->getPath();

		// Load Akeeba Subscriptions' plugins
		$container->platform->importPlugin('akeebasubs');

		// Get pertinent subscription records
		$db     = $container->db;
		$query  = $db->getQuery(true)
			->select($db->qn('akeebasubs_subscription_id'))
			->from($db->qn('#__akeebasubs_subscriptions'))
			->where($db->qn('processor') . ' = ' . $db->q('paddle'))
			->where($db->qn('processor_key') . ' LIKE ' . $db->q('%-%'))
			->where($db->qn('state') . ' != ' . $db->q('X'))
			->where($db->qn('enabled') . ' = ' . $db->q(1))
			->where(
				'((' . $db->qn('update_url') . ' = ' . $db->q('') . ') OR (' .
				$db->qn('update_url') . ' IS NULL' . '))'
			)
			->where(
				'((' . $db->qn('cancel_url') . ' = ' . $db->q('') . ') OR (' .
				$db->qn('cancel_url') . ' IS NULL' . '))'
			);
		$subIds = $db->setQuery($query)->loadColumn();

		// Loop through the whole lot
		/** @var Subscriptions $subModel */
		$subModel = $container->factory->model('Subscriptions')->tmpInstance();

		foreach ($subIds as $id)
		{
			$sub    = $subModel->tmpInstance()->findOrFail($id);
			$params = $sub->params;

			if (isset($params['no_longer_recurring']) && ($params['no_longer_recurring'] === 1))
			{
				continue;
			}

			$this->out($sub->getId() . ' -- ' . $sub->level->title . ' -- ' . $sub->processor_key . ' -- ' . $sub->created_on);

			$transaction        = $this->getTransaction($sub->processor_key);
			$paddleSubscription = $this->getSubscriptionInfo($transaction['subscription']['subscription_id']);

			$this->out('  -- ' . $paddleSubscription['state']);

			if ($paddleSubscription['state'] == 'deleted')
			{
				$params = $sub->params;
				$params['no_longer_recurring'] = 1;
				$sub->params = $params;
			}
			else
			{
				$sub->update_url = $paddleSubscription['update_url'];
				$sub->cancel_url = $paddleSubscription['cancel_url'];
			}

			if ($this->forReal)
			{
				$sub->save();
			}
		}
	}

	/**
	 * Retrieves a transaction for the specified order ID (same as our processor_key)
	 *
	 * @param   string  $orderID  Paddle order ID
	 *
	 * @return  array
	 *
	 * @since   7.1.0
	 */
	private function getTransaction(string $orderID)
	{
		$http     = Joomla\CMS\Http\HttpFactory::getHttp();
		$url      = sprintf('https://vendors.paddle.com/api/2.0/order/%s/transactions', $orderID);
		$postData = [
			'vendor_id'        => $this->vendorId,
			'vendor_auth_code' => $this->vendorAuthCode,
		];

		$return = $http->post($url, $postData);

		if ($return->code != 200)
		{
			throw new RuntimeException(sprintf('Paddle API error, HTTP code %d', $return->code));
		}

		$decodedBody = @json_decode($return->body, true);

		// Make sure it's a successful response
		if (!isset($decodedBody['success']) || ($decodedBody['success'] !== true))
		{
			throw new RuntimeException('Paddle API error');
		}

		// Make sure we actually got any transactions back
		if (!isset($decodedBody['response']) || !is_array($decodedBody['response']) || empty($decodedBody['response']))
		{
			throw new RuntimeException('Not found');
		}

		return $decodedBody['response'][0];
	}


	/**
	 * Retrieve Paddle subscription information given its ID
	 *
	 * @param   int  $subscription_id  The Paddle subscription ID
	 *
	 * @return  array
	 *
	 * @since   7.1.0
	 */
	private function getSubscriptionInfo(int $subscription_id): array
	{
		$http     = Joomla\CMS\Http\HttpFactory::getHttp();
		$url      = 'https://vendors.paddle.com/api/2.0/subscription/users';
		$postData = [
			'vendor_id'        => $this->vendorId,
			'vendor_auth_code' => $this->vendorAuthCode,
			'subscription_id'  => $subscription_id,
		];

		$return = $http->post($url, $postData);

		if ($return->code != 200)
		{
			throw new RuntimeException(sprintf('Paddle API error, HTTP code %d', $return->code));
		}

		$decodedBody = @json_decode($return->body, true);

		// Make sure it's a successful response
		if (!isset($decodedBody['success']) || ($decodedBody['success'] !== true))
		{
			throw new RuntimeException('Paddle API error');
		}

		// Make sure we actually got any transactions back
		if (!isset($decodedBody['response']) || !is_array($decodedBody['response']) || empty($decodedBody['response']))
		{
			throw new RuntimeException('Not found');
		}

		return $decodedBody['response'][0];
	}

}

FOFApplicationCLI::getInstance('AkeebasubsFixRecurring')->execute();