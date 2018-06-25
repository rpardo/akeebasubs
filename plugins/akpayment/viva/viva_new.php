<?php
/**
 * @package        akeebasubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Model\Levels;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use Akeeba\Subscriptions\Admin\PluginAbstracts\AkpaymentBase;

/**
 * Untested code - new implementation of the VivaPayments plugin based on their documentation. The old code still works,
 * but this new code forces the use of more modern versions of TLS. It's a good idea to eventually test it and replace
 * the old plugin.
 */
class plgAkpaymentViva extends AkpaymentBase
{
	public function __construct(&$subject, $config = array())
	{
		$config = array_merge($config, array(
			'ppName'  => 'viva',
			'ppKey'   => 'PLG_AKPAYMENT_VIVA_TITLE',
			'ppImage' => rtrim(JURI::base(), '/') . '/media/com_akeebasubs/images/frontend/LogoViva.png'
		));

		parent::__construct($subject, $config);
	}

	/**
	 * Returns the payment form to be submitted by the user's browser. The form must have an ID of
	 * "paymentForm" and a visible submit button.
	 *
	 * @param   string        $paymentmethod The currently used payment method. Check it against $this->ppName.
	 * @param   JUser         $user          User buying the subscription
	 * @param   Levels        $level         Subscription level
	 * @param   Subscriptions $subscription  The new subscription's object
	 *
	 * @return  string  The payment form to render on the page. Use the special id 'paymentForm' to have it
	 *                  automatically submitted after 5 seconds.
	 */
	public function onAKPaymentNew($paymentmethod, JUser $user, Levels $level, Subscriptions $subscription)
	{
		if ($paymentmethod != $this->ppName)
		{
			return false;
		}

		$data = array(
			'Email'        => trim($user->email),
			'FullName'     => trim($user->name),
			'RequestLang'  => $this->getLanguage(),
			'Amount'       => (int)($subscription->gross_amount * 100),
			'MerchantTrns' => $subscription->akeebasubs_subscription_id,
			'CustomerTrns' => $level->title
		);

		// Create new order by a REST POST
		try
		{
			$orderResult = $this->httpRequest(
				$this->getRESTHost(),
				'/api/orders',
				$data,
				'POST',
				$this->getRESTPort());
		}
		catch (RuntimeException $e)
		{
			$orderResult = (object)[
				'ErrorCode' => 500,
				'ErrorText' => $e->getMessage()
			];
		}

		if ($orderResult->ErrorCode != 0)
		{
			$errorText = $orderResult->ErrorText;
			$errorUrl = 'index.php?option=com_akeebasubs&view=Level&slug=' . $level->slug;
			$errorUrl = JRoute::_($errorUrl, false);

			$this->container->platform->redirect($errorUrl, 303, $errorText, 'error');
		}

		// Get the order-code and save it as processor key
		$orderCode = $orderResult->OrderCode;
		$subscription->save(array(
			'processor_key' => $orderCode
		));

		// Get the payment URL that is used by the form. $url is read by form.php
		$url = $this->getPaymentURL($orderCode);

		@ob_start();
		include dirname(__FILE__) . '/viva/form.php';
		$html = @ob_get_clean();

		return $html;
	}

	/**
	 * Processes a callback from the payment processor
	 *
	 * @param   string $paymentmethod The currently used payment method. Check it against $this->ppName
	 * @param   array  $data          Input (request) data
	 *
	 * @return  boolean  True if the callback was handled, false otherwise
	 */
	public function onAKPaymentCallback($paymentmethod, $data)
	{
		JLoader::import('joomla.utilities.date');

		// Check if we're supposed to handle this
		if ($paymentmethod != $this->ppName)
		{
			return false;
		}

		$isValid = true;

		/**
		 * If this URL is accessed via GET we need to return the webhook authorization code which we retrieve from
		 * Viva's API.
		 *
		 * @see https://github.com/VivaPayments/API/wiki/Webhooks#Webhook-Url-Verification
		 */
		$verb = $_SERVER['REQUEST_METHOD'];

		if (strtoupper($verb) == 'GET')
		{
			echo $this->httpRequest(
				'www.vivapayments.com',
				'/api/messages/config/token',
				array(),
				'GET',
				443);

			$this->container->platform->closeApplication();
		}

		// Load the relevant subscription row
		$orderCode = $data['s'];
		$subscription = null;

		if (!empty($orderCode))
		{
			/** @var Subscriptions $subscription */
			$subscription = $this->container->factory->model('Subscriptions')->tmpInstance();
			$subscription
				->find([
					'processor' => $this->ppName,
					'state' => 'N',
					'processor_key' => $orderCode
				]);

			$id = (int)$subscription->akeebasubs_subscription_id;

			if (($subscription->akeebasubs_subscription_id <= 0) || ($subscription->processor_key != $orderCode))
			{
				$subscription = null;
				$isValid = false;
			}

			/** @var Levels $level */
			$level = $subscription->level;
		}
		else
		{
			$isValid = false;
		}

		if (!$isValid)
		{
			$data['akeebasubs_failure_reason'] = 'The order code is invalid';
		}

		/** @var Subscriptions $subscription */
		/** @var Levels $level */

		// TODO Is this actually returned?!
		if ($isValid && $data['type'] == 'cancel')
		{
			// Redirect the user to the "cancel" page
			$cancelUrl = JRoute::_('index.php?option=com_akeebasubs&view=Message&slug=' . $level->slug . '&task=cancel&subid=' . $subscription->akeebasubs_subscription_id, false);
			$this->container->platform->redirect($cancelUrl);

			return true;
		}

		// Get all details for transaction by a REST GET
		if ($isValid)
		{
			try
			{
				$transactionResult = $this->httpRequest(
					$this->getRESTHost(),
					'/api/transactions/',
					array('ordercode' => $orderCode),
					'GET',
					$this->getRESTPort());
			}
			catch (RuntimeException $e)
			{
				$transactionResult = (object)[
					'ErrorCode' => 500,
					'ErrorText' => $e->getMessage()
				];
			}

			if ($transactionResult->ErrorCode != 0)
			{
				$isValid = false;
				$data['akeebasubs_failure_reason'] = $transactionResult->ErrorText;;
			}
			else
			{
				$transaction = $transactionResult->Transactions[0];
			}
		}

		// Check subscription ID
		if ($isValid)
		{
			if ($transaction->MerchantTrns != $id)
			{
				$isValid = false;
				$data['akeebasubs_failure_reason'] = "Subscription ID doesn't match";
			}
		}

		// Check order ID
		if ($isValid)
		{
			if ($transaction->Order->OrderCode != $orderCode)
			{
				$isValid = false;
				$data['akeebasubs_failure_reason'] = "Order code doesn't match";
			}
		}

		// Check that transaction has not been previously processed
		if ($isValid && !is_null($subscription))
		{
			if ($subscription->processor_key == $orderCode && in_array($subscription->state, array('C', 'X')))
			{
				$isValid = false;
				$data['akeebasubs_failure_reason'] = "I will not process the same transcation twice";
			}
		}

		// Check that amount is correct
		$isPartialRefund = false;

		if ($isValid && !is_null($subscription))
		{
			$mc_gross = floatval($transaction->Amount);
			$gross = $subscription->gross_amount;

			if ($mc_gross > 0)
			{
				// A positive value means "payment". The prices MUST match!
				// Important: NEVER, EVER compare two floating point values for equality.
				$isValid = ($gross - $mc_gross) < 0.01;
			}
			else
			{
				$isPartialRefund = false;
				$temp_mc_gross = -1 * $mc_gross;
				$isPartialRefund = ($gross - $temp_mc_gross) > 0.01;
			}

			if (!$isValid)
			{
				$data['akeebasubs_failure_reason'] = 'Paid amount does not match the subscription amount';
			}
		}

		// Log the IPN data
		$this->logIPN($data, $isValid);

		// Fraud attempt? Do nothing more!
		if (!$isValid)
		{
			$error_url = 'index.php?option=com_akeebasubs' .
				'&view=Level&slug=' . $level->slug;
			$error_url = JRoute::_($error_url, false);
			$this->container->platform->redirect($error_url, 303, $data['akeebasubs_failure_reason'], 'error');

			return false;
		}

		// Payment status
		switch ($transaction->StatusId)
		{
			// Finalized (paid)
			case 'F':
			// Dispute won (we keep the money)
			case 'MW':
				$newStatus = 'C';
				break;
			// In progress (awaiting)
			case 'A':
				$newStatus = 'P';
				break;
			// All other codes per https://github.com/VivaPayments/API/wiki/GetTransactions
			default:
				$newStatus = 'X';
				break;
		}

		// Update subscription status (this also automatically calls the plugins)
		$updates = array(
			'akeebasubs_subscription_id' => $id,
			'processor_key'              => $orderCode,
			'state'                      => $newStatus,
			'enabled'                    => 0
		);

		if ($newStatus == 'C')
		{
			self::fixSubscriptionDates($subscription, $updates);
		}

		$subscription->save($updates);

		// Run the onAKAfterPaymentCallback events
		$this->container->platform->importPlugin('akeebasubs');
		$this->container->platform->runPlugins('onAKAfterPaymentCallback', array(
			$subscription
		));

		// Redirect the user to the "thank you" page
		$thankyouUrl = JRoute::_('index.php?option=com_akeebasubs&view=Message&slug=' . $level->slug . '&task=thankyou&subid=' . $subscription->akeebasubs_subscription_id, false);
		$this->container->platform->redirect($thankyouUrl);

		return true;
	}

	/**
	 * Return the hostname for the REST API endpoint. This depends on the sandbox setting.
	 *
	 * @return string
	 */
	private function getRESTHost()
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox)
		{
			return 'demo.vivapayments.com';
		}
		else
		{
			return 'www.vivapayments.com';
		}
	}

	/**
	 * The HTTP port to use. Sandbox always uses plain HTTP (port 80), production uses HTTPS (port 443)
	 *
	 * @return  int
	 */
	private function getRESTPort()
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox)
		{
			return 80;
		}
		else
		{
			return 443;
		}
	}

	/**
	 * Get the VivaPayments interface language code. We map it to either Greek or English (US).
	 *
	 * TODO Maybe we can support more languages...?
	 *
	 * @return  string
	 */
	private function getLanguage()
	{
		$lang = $this->params->get('lang', 0);

		if ($lang == 'gr')
		{
			return 'el-GR';
		}

		return 'en-US';
	}

	/**
	 * Get the VivaPayments redirect URL which lets the user pay for their order
	 *
	 * @param   string  $orderCode  The VivaPayments order code returned by the createOrder API endpoint
	 *
	 * @return  string
	 *
	 * @see     https://github.com/VivaPayments/API/wiki/Redirect-Checkout
	 */
	private function getPaymentURL($orderCode)
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox)
		{
			return 'http://demo.vivapayments.com/web/checkout?ref=' . $orderCode;
		}
		else
		{
			return 'https://www.vivapayments.com/web/checkout?ref=' . $orderCode;
		}
	}

	/**
	 * Returns the authentication string for the API. This is in the form merchant_id:password
	 *
	 * @return  string
	 */
	private function getAuthentication()
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox)
		{
			return trim($this->params->get('demo_merchant_id', '')) . ':'
				. trim($this->params->get('demo_pw', ''));
		}

		return trim($this->params->get('merchant_id', '')) . ':'
			. trim($this->params->get('pw', ''));
	}

	/**
	 * Performs a VIVA payments API HTTP request.
	 *
	 * @param   string  $host    The API hostname
	 * @param   string  $path    The API path
	 * @param   array   $params  Any parameters to pass to the API
	 * @param   string  $method  HTTP method (GET, POST or DELETE, must be all caps)
	 * @param   int     $port    Use 443 to force using SSL
	 *
	 * @return  object  The parsed JSON response as an stdClass object
	 *
	 * @throws  RuntimeException  When there is a cURL error, e.g. a network issue
	 */
	private function httpRequest($host, $path, array $params = [], $method = 'POST', $port = 80)
	{
		$protocol = ($port == 443) ? 'https' : 'http';
		$url      = $protocol . '://' . $host . '/' . ltrim($path, '/');

		// Common cURL options
		$options = [
			CURLOPT_VERBOSE         => false,
			CURLOPT_HEADER          => false,
			CURLINFO_HEADER_OUT     => false,
			CURLOPT_RETURNTRANSFER  => true,
			CURLOPT_HTTPHEADER     => [
				'User-Agent: AkeebaSubscriptions',
				'Connection: Close',
				'Authorization: Basic ' . base64_encode($this->getAuthentication())
			],
			//CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_FORBID_REUSE   => 1,
		];

		// Are we connecting by SSL? Then add some necessary cURL options.
		if ($protocol === 'https')
		{
			$options = array_merge($options, [
				CURLOPT_SSLVERSION      => 6,
				CURLOPT_SSL_VERIFYPEER  => true,
				CURLOPT_SSL_VERIFYHOST  => 2,
				CURLOPT_CAINFO          => JPATH_LIBRARIES . '/fof30/Download/Adapter/cacert.pem',
				// Force the use of TLS (therefore SSLv3 is not used, mitigating POODLE; see https://github.com/paypal/merchant-sdk-php)
				CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
				// This forces the use of TLS 1.x
				CURLOPT_SSLVERSION      => CURL_SSLVERSION_TLSv1,
			]);
		}

		// Additional options handling based on request type
		$extraOptions = [];

		switch ($method)
		{
			case 'POST':
				$extraOptions = [
					CURLOPT_POST       => true,
					CURLOPT_POSTFIELDS => $params,
				];
				break;

			case 'GET':
				$uri = new JUri($url);

				foreach ($params as $k => $v)
				{
					$uri->setVar($k, $v);
				}

				$url = $uri->toString();
				break;

			case 'DELETE':
				$extraOptions = [
					CURLOPT_CUSTOMREQUEST => $method,
					CURLOPT_POSTFIELDS    => $params,
				];
			default:
		}

		if (!empty($extraOptions))
		{
			$options = array_merge($options, $extraOptions);
		}

		// Execute the request
		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$response     = curl_exec($ch);
		$errNo        = curl_errno($ch);
		$error        = curl_error($ch);
		$lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		echo "<h1>FOLA</h1><pre>";
		var_dump($response);die;

		if (is_object(json_decode($resBody)))
		{
			$resultObj = json_decode($resBody);
		}
		else
		{
			preg_match('#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $resHeader, $match);

			throw new RuntimeException("API Call failed! The error was: " . trim($match[1]), 500);
		}

		return $resultObj;
	}
}
