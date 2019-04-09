<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Define ourselves as a parent file
define('_JEXEC', 1);
define('JDEBUG', 1);

use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;
use Joomla\CMS\Application\CliApplication;
use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Cli;

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

// Load system defines
$cwd = getcwd();

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}
elseif (file_exists($cwd . '/defines.php'))
{
	include_once $cwd . '/defines.php';
}
else
{
	$path = rtrim(__DIR__, DIRECTORY_SEPARATOR);
	$rpos = strrpos($path, DIRECTORY_SEPARATOR);
	$path = substr($path, 0, $rpos);

	if (!file_exists($path . '/index.php'))
	{
		$path = rtrim($cwd, DIRECTORY_SEPARATOR);
		$rpos = strrpos($path, DIRECTORY_SEPARATOR);
		$path = substr($path, 0, $rpos);
	}

	define('JPATH_BASE', $path);
	include_once JPATH_BASE . '/includes/defines.php';
}

// Load the rest of the framework include files
require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration.php file explicitly
JFactory::getConfig(JPATH_CONFIGURATION . '/configuration.php');

/**
 * Dummy session handler for CLI applications
 *
 * @since       7.0.0
 */
class SessionHandlerCli implements JSessionHandlerInterface
{
	private $started = false;

	private $id = null;

	private $sessionName = 'cli';

	/**
	 * Starts the session.
	 *
	 * @return  boolean  True if started.
	 *
	 * @since   3.5
	 * @throws  RuntimeException If something goes wrong starting the session.
	 */
	public function start()
	{
		$this->started = true;
		$this->id = \Joomla\CMS\User\UserHelper::genRandomPassword(32);
	}

	/**
	 * Checks if the session is started.
	 *
	 * @return  boolean  True if started, false otherwise.
	 *
	 * @since   3.5
	 */
	public function isStarted()
	{
		return $this->started;
	}

	/**
	 * Returns the session ID
	 *
	 * @return  string  The session ID
	 *
	 * @since   3.5
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Sets the session ID
	 *
	 * @param   string $id The session ID
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Returns the session name
	 *
	 * @return  mixed  The session name.
	 *
	 * @since   3.5
	 */
	public function getName()
	{
		return $this->sessionName;
	}

	/**
	 * Sets the session name
	 *
	 * @param   string $name The name of the session
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function setName($name)
	{
		$this->sessionName = $name;
	}

	/**
	 * Regenerates ID that represents this storage.
	 *
	 * Note regenerate+destroy should not clear the session data in memory only delete the session data from persistent storage.
	 *
	 * @param   boolean $destroy    Destroy session when regenerating?
	 * @param   integer $lifetime   Sets the cookie lifetime for the session cookie. A null value will leave the system settings unchanged,
	 *                              0 sets the cookie to expire with browser session. Time is in seconds, and is not a Unix timestamp.
	 *
	 * @return  boolean  True if session regenerated, false if error
	 *
	 * @since   3.5
	 */
	public function regenerate($destroy = false, $lifetime = null)
	{
		$this->id = \Joomla\CMS\User\UserHelper::genRandomPassword(32);
	}

	/**
	 * Force the session to be saved and closed.
	 *
	 * This method must invoke session_write_close() unless this interface is used for a storage object design for unit or functional testing where
	 * a real PHP session would interfere with testing, in which case it should actually persist the session data if required.
	 *
	 * @return  void
	 *
	 * @see     session_write_close()
	 * @since   3.5
	 * @throws  RuntimeException  If the session is saved without being started, or if the session is already closed.
	 */
	public function save()
	{
		$this->started = false;
	}

	/**
	 * Clear all session data in memory.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function clear()
	{
		$this->started = false;
	}
}

/**
 * A debug script which produces dummy callbacks to test our callbacks integration.
 *
 * @since       7.0.0
 */
class AkeebasubsCallbackDebug extends CliApplication
{
	protected function doExecute()
	{
		// Load FOF
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			throw new RuntimeException('FOF 3.0 is not installed', 500);
		}

		// Create a fake session for the CLI application. Required for running integrations.
		$this->createCliSession();

		// Get the Akeeba Subscriptions container
		$container = Container::getInstance('com_akeebasubs');

		// Fake the $_SERVER superglobals so JUri doesn't complain. Required for running integrations.
		$uri                    = new Uri($container->params->get('siteurl'));
		$_SERVER['HTTP_HOST']   = $uri->getHost();
		$_SERVER['SCRIPT_NAME'] = $uri->getPath();

		/** @var Cli $input */
		$input   = $this->input;
		$subId   = $input->getInt('subscription');
		$webhook = $input->getCmd('webhook');

		if (empty($subId) || empty($webhook))
		{
			$this->showUsage();

			$this->close(254);
		}

		// Get the subscription
		/** @var Subscriptions $subscription */
		$subscription = $container->factory->model('Subscriptions')->tmpInstance();
		$subscription->findOrFail($subId);

		$method = $container->inflector->variablize($webhook);

		if (!method_exists($this, $method))
		{
			throw new LogicException(sprintf('Webhook %s cannot be handled because method %s has not been implemented yet.', $webhook, $method));
		}

		$this->out(sprintf("Generating webhook %s for subscription %u", $webhook, $subId));
		$this->out("Subscription information:");
		$info = <<< TXT
#{$subscription->getId()} {$subscription->level->title} -- {$subscription->created_on}
{$subscription->juser->username} ({$subscription->juser->name}) <{$subscription->juser->email}>
TXT;
		$this->out($info);

		$this->out("Getting data");

		$webhookData = call_user_func([$this, $method], $subscription);

		$this->out("Creating URL");

		$callbackUri = new Uri(Uri::base() . 'index.php?option=com_akeebasubs&view=Callback&task=callback');
		$config      = Factory::getConfig();

		if ($config->get('force_ssl', 0) > 0)
		{
			$callbackUri->setScheme('https');
		}

		$callbackUrl = $callbackUri->toString();

		$this->out($callbackUrl);
		$this->out("Sending POST");

		$this->doPost($callbackUrl, $webhookData);
	}

	/**
	 * Show how the script is supposed to be used and exit.
	 *
	 * @since   7.0.0
	 */
	private function showUsage()
	{
		global $argv;



		echo <<< TEXT
Usage: {$argv[0]} --subscription=SUB_ID --webhook=WEBHOOK

Where
	SUB_ID  A numeric subscription ID
	WEBHOOK The name of the webhook to call

Webhooks
	fulfillment
		No parameters
		
	payment_succeeded
		No parameters
	
	payment_refunded
		--type    [full, vat, partial]
		--amount  Amount to refund (when type=partial)
		
	high_risk_transaction_created
		--risk    0.01 to 99.99
		
	high_risk_transaction_updated
		--status  [accepted, rejected]	
	
	payment_dispute_created
		No parameters
		
	payment_dispute_closed
		No parameters
		
	subscription_created
		No parameters
	
	subscription_updated
		No parameters

	subscription_cancelled
		No parameters

	subscription_payment_succeeded
		No parameters

	subscription_payment_failed
		No parameters

	subscription_payment_refunded	
		No parameters

TEXT;

	}

	/**
	 * Create a payment_succeeded webhook message
	 *
	 * @param   Subscriptions  $subscription  The subscription record involved in the callback
	 *
	 * @return  array  Data to send in a POST request
	 *
	 * @since   7.0.0
	 */
	protected function paymentSucceeded(Subscriptions $subscription): array
	{
		$taxInfo        = [
			'GR' => 24,
			'CY' => 19,
			'HU' => 27,
			'IT' => 22,
		];
		$paymentMethods = ['apple-pay', 'card', 'paypal', 'wire-transfer'];

		try
		{
			$randIndex = random_int(0, count($taxInfo) - 1);
			$payIndex  = random_int(0, count($paymentMethods) - 1);
		}
		catch (Exception $e)
		{
			$randIndex = 0;
			$payIndex  = 0;
		}

		$container     = $subscription->getContainer();
		$user          = Factory::getUser($subscription->user_id);
		$countries     = array_keys($taxInfo);
		$country       = $countries[$randIndex];
		$taxRate       = $taxInfo[$country] / 100;
		$paymentMethod = ($subscription->gross_amount == 0) ? 'free' : $paymentMethods[$payIndex];
		$tax           = $taxRate * $subscription->net_amount;
		$gross         = $subscription->net_amount + $tax;
		$fee           = ($subscription->gross_amount == 0) ? 0 : (0.50 + 0.05 * $gross);
		$earnings      = $gross - $tax - $fee;
		$orderId       = $this->uuid_v4();

		return [
			'alert_name'          => 'payment_succeeded',
			'balance_currency'    => $container->params->get('currency', 'EUR'),
			'balance_earnings'    => $earnings,
			'balance_fee'         => $fee,
			'balance_gross'       => $gross,
			'balance_tax'         => $tax,
			'checkout_id'         => $this->uuid_v4(),
			'country'             => $country,
			'coupon'              => '',
			'currency'            => $container->params->get('currency', 'EUR'),
			'customer_name'       => $user->name,
			'earnings'            => $earnings,
			'email'               => $user->email,
			'event_time'          => gmdate('Y-m-d H:i:s'),
			'fee'                 => $fee,
			'ip'                  => $subscription->ip,
			'marketing_consent'   => mt_rand(0, 1),
			'order_id'            => $orderId,
			'passthrough'         => $subscription->getId(),
			'payment_method'      => $paymentMethod,
			'payment_tax'         => $tax,
			'product_id'          => $subscription->level->paddle_product_id,
			'product_name'        => $subscription->level->title,
			'quantity'            => 1,
			'receipt_url'         => 'https://www.example.com/receipt/' . $orderId,
			'sale_gross'          => $gross,
			'used_price_override' => ($gross - $tax - $subscription->level->price < 0.01) ? 'true' : 'false',
			'p_signature'         => $container->params->get('secret'),
		];
	}

	/**
	 * Create a fulfillment webhook message
	 *
	 * @param   Subscriptions  $subscription  The subscription record involved in the callback
	 *
	 * @return  array  Data to send in a POST request
	 *
	 * @since   7.0.0
	 */
	protected function fulfillment(Subscriptions $subscription): array
	{
		$container = $subscription->getContainer();
		$fee = max(0, 0.50 + 0.05 * $subscription->gross_amount);

		return [
			'p_quantity'        => 1,
			'p_coupon_savings'  => 0.00,
			'p_country'         => $this->getCountry($subscription->user_id),
			'p_coupon'          => '',
			'p_tax_amount'      => $subscription->tax_amount,
			'p_currency'        => $container->params->get('currency', 'EUR'),
			'p_paddle_fee'      => $fee,
			'p_price'           => $subscription->net_amount,
			'p_order_id'        => $subscription->processor_key,
			'p_earnings'        => json_encode([
				$container->params->get('vendor_id') => $subscription->gross_amount - $fee,
			]),
			'p_product_id'      => $subscription->level->paddle_product_id,
			'passthrough'       => $subscription->getId(),
			'p_signature'       => $container->params->get('secret'),
			'marketing_consent' => 0,
			'event_time'        => gmdate('Y-m-d H:i:s'),
		];
	}

	/**
	 * Create a subscription refund webhook message
	 *
	 * @param   Subscriptions  $subscription  The subscription record involved in the callback
	 *
	 * @return  array  Data to send in a POST request
	 *
	 * @since   7.0.0
	 */
	protected function paymentRefunded(Subscriptions $subscription): array
	{
		$type      = $this->input->getCmd('type', 'full');
		$container = $subscription->getContainer();

		if (!in_array($type, ['full', 'vat', 'partial']))
		{
			$type = 'full';
		}

		switch ($type)
		{
			case 'partial':
				$amount     = $this->input->getFloat('amount', 1.23);
				$tax_factor = $subscription->tax_amount / $subscription->net_amount;
				$refund_net = $amount / (1 + $tax_factor);
				$refund_tax = $refund_net * $tax_factor;
				break;

			case 'vat':
				$amount     = $subscription->tax_amount;
				$refund_net = 0.00;
				$refund_tax = $subscription->tax_amount;
				break;

			case 'full':
				$amount     = $subscription->gross_amount;
				$refund_net = $subscription->net_amount;
				$refund_tax = $subscription->tax_amount;
		}

		$new_tax      = $subscription->tax_amount - $refund_tax;
		$new_gross    = $subscription->gross_amount - $refund_net - $refund_tax;
		$new_fee      = ($type == 'full') ? 0 : 0.50 * $new_gross;
		$refund_fee   = $subscription->fee_amount - $new_fee;
		$old_earnings = $subscription->gross_amount - $subscription->fee_amount - $subscription->tax_amount;
		$new_earnings = $new_gross - $new_fee - $new_tax;

		return [
			'alert_name'                => 'payment_refunded',
			'amount'                    => $amount,
			'balance_currency'          => $container->params->get('currency', 'EUR'),
			'balance_earnings_decrease' => $old_earnings - $new_earnings,
			'balance_fee_refund'        => $refund_fee,
			'balance_gross_refund'      => $subscription->gross_amount - $new_gross,
			'balance_tax_refund'        => $refund_tax,
			'checkout_id'               => $subscription->params['checkout_id'],
			'currency'                  => $container->params->get('currency', 'EUR'),
			'earnings_decrease'         => $old_earnings - $new_earnings,
			'email'                     => $subscription->juser->email,
			'event_time'                => gmdate('Y-m-d H:i:s'),
			'fee_refund'                => $refund_fee,
			'gross_refund'              => $subscription->gross_amount - $new_gross,
			'marketing_consent'         => 0,
			'order_id'                  => $subscription->processor_key,
			'passthrough'               => $subscription->getId(),
			'quantity'                  => 1,
			'refund_type'               => $type,
			'tax_refund'                => $refund_tax,
			'p_signature'               => $container->params->get('secret'),
		];
	}

	/**
	 * Create a high risk subscription created webhook message
	 *
	 * @param   Subscriptions  $subscription  The subscription record involved in the callback
	 *
	 * @return  array  Data to send in a POST request
	 *
	 * @since   7.0.0
	 */
	protected function highRiskTransactionCreated(Subscriptions $subscription): array
	{
		$container = $subscription->getContainer();

		try
		{
			$riskScore = random_int(0, 9999);
		}
		catch (Exception $e)
		{
			$riskScore = 1234;
		}

		$riskScore = $this->input->getFloat('risk', $riskScore);

		return [
			'alert_name'             => 'high_risk_transaction_created',
			'case_id'                => $this->uuid_v4(),
			'checkout_id'            => $subscription->params['checkout_id'],
			'created_at'             => gmdate('Y-m-d H:i:s', time() - 10),
			'customer_email_address' => $subscription->juser->email,
			'customer_user_id'       => $this->uuid_v4(),
			'event_time'             => gmdate('Y-m-d H:i:s'),
			'marketing_consent'      => 0,
			'passthrough'            => $subscription->getId(),
			'product_id'             => $subscription->level->paddle_product_id,
			'risk_score'             => sprintf('%0.2f', $riskScore / 100.00),
			'status'                 => 'pending',
			'p_signature'            => $container->params->get('secret'),
		];
	}

	/**
	 * Create a high risk subscription updated webhook message
	 *
	 * @param   Subscriptions  $subscription  The subscription record involved in the callback
	 *
	 * @return  array  Data to send in a POST request
	 *
	 * @since   7.0.0
	 */
	protected function highRiskTransactionUpdated(Subscriptions $subscription): array
	{
		$status = $this->input->getCmd('status', 'accepted');

		if (!in_array($status, ['accepted', 'rejected']))
		{
			$status = 'accepted';
		}

		$container = $subscription->getContainer();

		return [
			'alert_name'             => 'high_risk_transaction_updated',
			'case_id'                => $subscription->params['risk_case_id'],
			'checkout_id'            => $subscription->params['checkout_id'],
			'created_at'             => $subscription->params['risk_case_created'],
			'customer_email_address' => $subscription->juser->email,
			'customer_user_id'       => $subscription->params['paddle_customer_user_id'],
			'event_time'             => gmdate('Y-m-d H:i:s'),
			'marketing_consent'      => 0,
			'passthrough'            => $subscription->getId(),
			'product_id'             => $subscription->level->paddle_product_id,
			'risk_score'             => $subscription->params['risk_score'],
			'status'                 => $status,
			'p_signature'            => $container->params->get('secret'),
		];
	}


	/**
	 * Creates a dummy session under the CLI using our special CLI session handler
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	private function createCliSession(): void
	{
		// Get the Joomla configuration settings
		$conf    = Factory::getConfig();
		$handler = $conf->get('session_handler', 'none');

		// Config time is in minutes
		$options['expire'] = ($conf->get('lifetime')) ? $conf->get('lifetime') * 60 : 900;

		$sessionHandler = new SessionHandlerCli();

		$session = Session::getInstance($handler, $options, $sessionHandler);

		if ($session->getState() == 'expired')
		{
			$session->restart();
		}

		Factory::$session = $session;
	}

	/**
	 * Generate a UUID v4
	 *
	 * @return  string
	 *
	 * @since   7.0.0
	 */
	private function uuid_v4(): string
	{
		$data    = Crypt::genRandomBytes(16);

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	/**
	 * Returns the country associated with a user record
	 *
	 * @param   int  $user_id  The user record ID
	 *
	 * @return  string
	 *
	 * @since   7.0.0
	 */
	private function getCountry(int $user_id): string
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('profile_value'))
			->from($db->qn('#__user_profiles'))
			->where($db->qn('user_id') . ' = ' . $db->q($user_id))
			->where($db->qn('profile_key') . ' = ' . $db->q('akeebasubs.country'));

		return $db->setQuery($query)->loadResult() ?? 'XX';
	}

	/**
	 * Send a POST request to the server
	 *
	 * @param   string  $callbackUrl
	 * @param   array   $webhookData
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	protected function doPost(string $callbackUrl, array $webhookData): void
	{
		$ch = curl_init($callbackUrl);

		curl_setopt_array($ch, [
			CURLOPT_FOLLOWLOCATION => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => $webhookData,
			CURLOPT_TIMEOUT        => 5,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER         => 1,
			CURLOPT_USERAGENT      => 'Akeeba Subscriptions Debug',
			CURLOPT_AUTOREFERER    => false,
		]);

		$content = curl_exec($ch);
		$errNo   = curl_errno($ch);
		$error   = curl_error($ch);
		$status  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$this->out("Got HTTP $status");

		if ($errNo)
		{
			$this->out(sprintf('cURL error %u: %s', $errNo, $error));
		}

		print_r($content);
	}
}

CliApplication::getInstance('AkeebasubsCallbackDebug')->execute();