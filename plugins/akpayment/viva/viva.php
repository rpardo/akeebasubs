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

		$data = (object)array(
			'Email'        => trim($user->email),
			'FullName'     => trim($user->name),
			'RequestLang'  => $this->getLanguage(),
			'Amount'       => (int)($subscription->gross_amount * 100),
			'MerchantTrns' => $subscription->akeebasubs_subscription_id,
			'CustomerTrns' => $level->title
		);

		// Create new order by a REST POST
		$jsonResult = $this->httpRequest(
			$this->getRESTHost(),
			'/api/orders',
			$data,
			'POST',
			$this->getRESTPort());

		$orderResult = json_decode($jsonResult);

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

		// Get the payment URL that is used by the form
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

		/**
		 * If this URL is accessed via GET we need to return the webhook authorization code which we retrieve from
		 * Viva's API.
		 *
		 * @see https://github.com/VivaPayments/API/wiki/Webhooks#Webhook-Url-Verification
		 */
		$verb = $_SERVER['REQUEST_METHOD'];

		if ((strtoupper($verb) == 'GET') && (!isset($data['s']) || empty($data['s'])))
		{
			echo $this->httpRequest(
				$this->getRESTHost(),
				'/api/messages/config/token',
				array(),
				'GET',
				443);

			$this->container->platform->closeApplication();
		}

		/**
		 * Is this a Webhook message? If so, handle differently.
		 */
		if (($verb == 'POST') && ($data['type'] == 'webhook'))
		{
			$this->_processWebhook();

			return true;
		}

		$isValid = true;

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
		}
		else
		{
			$isValid = false;
		}

		if (!$isValid)
		{
			$data['akeebasubs_failure_reason'] = 'The order code is invalid';
		}


		/** @var Levels $level */
		$level = $subscription->level;

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
			$jsonResult = $this->httpRequest(
				$this->getRESTHost(),
				'/api/transactions',
				array('OrderCode' => $orderCode),
				'GET',
				$this->getRESTPort());
			$transactionResult = json_decode($jsonResult);

			if ($transactionResult->ErrorCode != 0)
			{
				$isValid = false;
				$data['akeebasubs_failure_reason'] = $transactionResult->ErrorText;
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
			case 'F':
				$newStatus = 'C';
				break;
			case 'A':
				$newStatus = 'P';
				break;
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

	private function getLanguage()
	{
		$lang = $this->params->get('lang', 0);

		if ($lang == 'gr')
		{
			return 'el-GR';
		}

		return 'en-US';
	}

	private function getPaymentURL($orderCode)
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox)
		{
			return 'http://demo.vivapayments.com/web/newtransaction.aspx?ref=' . $orderCode;
		}
		else
		{
			return 'https://www.vivapayments.com/web/newtransaction.aspx?ref=' . $orderCode;
		}
	}

	private function getBasicAuthorization()
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox)
		{
			return base64_encode(
				trim($this->params->get('demo_merchant_id', '')) . ':'
				. trim($this->params->get('demo_pw', '')));
		}

		return base64_encode(
			trim($this->params->get('merchant_id', '')) . ':'
			. trim($this->params->get('pw', '')));
	}

	private function httpRequest($host, $path, $params, $method = 'POST', $port = 80)
	{
		// Build the parameter string
		$paramStr = "";

		foreach ($params as $key => $val)
		{
			$paramStr .= $key . "=";
			$paramStr .= urlencode($val);
			$paramStr .= "&";
		}

		$paramStr = substr($paramStr, 0, -1);

		// Create the connection
		$sandbox = $this->params->get('sandbox', 0);
		$sockhost = ($port == 80) ? $host : 'ssl://' . $host;
		$sock = fsockopen($sockhost, $port);

		if (($method == 'GET') && !empty($paramStr))
		{
			$path .= '?' . $paramStr;
		}

		fputs($sock, "$method $path HTTP/1.1\r\n");
		fputs($sock, "Host: $host\r\n");
		fputs($sock, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($sock, "Content-length: " . strlen($paramStr) . "\r\n");
		fputs($sock, "Authorization: Basic " . $this->getBasicAuthorization() . "\r\n");
		fputs($sock, "Connection: close\r\n\r\n");
		fputs($sock, $paramStr);

		// Buffer the result
		$response = "";

		while (!feof($sock))
		{
			$response .= fgets($sock, 1024);
		}

		fclose($sock);

		// Get the json part of the response
		$matches = array();
		$pattern = '/[^{]*(.+)[^}]*/';
		preg_match($pattern, $response, $matches);
		$json = $matches[1];

		return $json;
	}

	/**
	 * Process a Viva Payments webhook postback. We only process refunds through these hooks.
	 *
	 * The postback URL for webhooks is index.php?option=com_akeebasubs&view=callback&paymentmethod=viva&type=webhook
	 *
	 * @return  void
	 *
	 * @see     https://github.com/VivaPayments/API/wiki/Webhooks
	 */
	private function _processWebhook()
	{
		// Get the POST message
		$message = file_get_contents('php://input');

		// No message, no JSON message or empty JSON message? No joy.
		if (empty($message))
		{
			$this->logIPN([
				'akeebasubs_failure_reason' => 'Empty POST message in the webhook notification'
			], false);

			return;
		}

		$message = @json_decode($message, true);

		if (empty($message))
		{
			$this->logIPN([
				'akeebasubs_failure_reason' => 'Empty JSON message in the webhook notification'
			], false);

			return;
		}

		// Check that "EventTypeId" is 1797 (refund)
		if (!isset($message['EventTypeId']))
		{
			$this->logIPN(array_merge($message, [
				'akeebasubs_failure_reason' => 'No EventTypeId key in the webhook notification'
			]), false);

			return;
		}

		$eventTypeId = $message['EventTypeId'];

		if ($eventTypeId != 1797)
		{
			$this->logIPN(array_merge($message, [
				'akeebasubs_failure_reason' => 'Not a refund message (EventTypeId is not 1797)'
			]), false);

			return;
		}

		// Get the EventData
		if (!isset($message['EventData']))
		{
			$this->logIPN(array_merge($message, [
				'akeebasubs_failure_reason' => 'No EventData found in the message'
			]), false);

			return;
		}

		$eventData = $message['EventData'];

		// Double check this is a refund message

		if (!isset($eventData['TransactionTypeId']))
		{
			$this->logIPN(array_merge($message['EventData'], [
				'akeebasubs_failure_reason' => 'No TransactionTypeId key found in the EventData'
			]), false);

			return;
		}

		$transactionTypeId = $eventData['TransactionTypeId'];

		if (!in_array($transactionTypeId, [4, 7, 11, 13, 17]))
		{
			$this->logIPN(array_merge($message['EventData'], [
				'akeebasubs_failure_reason' => 'TransactionTypeId does not indicate a refund message'
			]), false);

			return;
		}

		// Get the payment key
		if (!isset($eventData['OrderCode']))
		{
			$this->logIPN(array_merge($message['EventData'], [
				'akeebasubs_failure_reason' => 'No OrderCode key found in the EventData'
			]), false);

			return;
		}

		$payKey = $eventData['OrderCode'];

		// Load thje subscription
		/** @var Subscriptions $subscription */
		$subscription = $this->container->factory->model('Subscriptions')->tmpInstance();
		try
		{
			$subscription = $subscription->paykey($payKey)->firstOrFail();
		}
		catch (RuntimeException $e)
		{
			$this->logIPN(array_merge($message['EventData'], [
				'akeebasubs_failure_reason' => sprintf('No such subscription (Order ID %s)', $payKey)
			]), false);

			return;
		}

		// Do I have a refunded amount? Also note that the refunded amount is sent as a NEGATIVE number in the request.
		$refundedAmount = isset($eventData['Amount']) ? $eventData['Amount'] : 0.00;
		$refundedAmount = -$refundedAmount;

		if ($refundedAmount < 0.01)
		{
			$this->logIPN(array_merge($message['EventData'], [
				'akeebasubs_failure_reason' => 'The refund amount is zero'
			]), false);

			return;
		}

		$isPartialRefund = abs($subscription->gross_amount - $refundedAmount) > 0.01;

		if ($isPartialRefund)
		{
			$this->logIPN(array_merge($message['EventData'], [
				'akeebasubs_failure_reason' => sprintf('!!! PARTIAL REFUND (%0.2f)!!! The subscription status will not change.', $refundedAmount)
			]), true);

			return;
		}

		// Record the payment notification
		$this->logIPN(array_merge($message['EventData'], [
			'akeebasubs_failure_reason' => '!!! FULL REFUND !!! The subscription is cancelled.'
		]), true);

		// Save the subscription changes
		$subscription->save([
			'state'   => 'X',
			'enabled' => 0,
		]);

		// Run the onAKAfterPaymentCallback events
		$this->container->platform->importPlugin('akeebasubs');
		$this->container->platform->runPlugins('onAKAfterPaymentCallback', array(
			$subscription
		));
	}
}
