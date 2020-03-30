<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Locate early renewals with recurring subscriptions and mark parent subscriptions with contactflag 3
 *
 * --for-real              Apply changes to the database.
 */

use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use FOF30\Container\Container;
use FOF30\Model\DataModel\Collection as DataCollection;

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

class AkeebasubsFixEarlyRenewal extends FOFApplicationCLI
{
	protected function doExecute()
	{
		$forReal = $this->input->getBool('for-real', false);

		$container = Container::getInstance('com_akeebasubs');
		/** @var Subscriptions $subModel */
		$subModel = $container->factory->model('Subscriptions')->tmpInstance();

		$subModel
			->enabled(1)
			->processor('paddle')
			->recurring(1)
			->paystate(['C'])
			->where('gross_amount', '<=', '0.01');

		/** @var DataCollection $activeRecurringRenewals */
		$activeRecurringRenewals = $subModel->get(true);

		$activeRecurringRenewals->each(function (Subscriptions $sub) use ($subModel, $forReal) {
			/** @var array $params */
			$params = $sub->params;

			if (!array_key_exists('fixdates', $params))
			{
				return;
			}

			if (!array_key_exists('nocontact', $params['fixdates']))
			{
				return;
			}

			if (empty($params['fixdates']['nocontact']))
			{
				return;
			}

			foreach ($params['fixdates']['nocontact'] as $subId)
			{
				/** @var Subscriptions $sub */
				$original = $subModel->tmpInstance();

				try
				{
					$original->findOrFail($subId);
				}
				catch (Exception $e)
				{
					continue;
				}

				if ($original->contact_flag == 3)
				{
					continue;
				}

				$this->out(sprintf('Subscription #%05u (parent of #%05u) has the wrong contact flag (%u).', $subId, $sub->getId(), $original->contact_flag));

				if (!$forReal)
				{
					continue;
				}

				$original->save([
					'contact_flag' => 3,
				]);
			}
		});
	}
}

FOFApplicationCLI::getInstance('AkeebasubsFixEarlyRenewal')->execute();