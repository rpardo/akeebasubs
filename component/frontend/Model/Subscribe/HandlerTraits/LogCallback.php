<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits;


use Joomla\CMS\Filesystem\File;
use Joomla\Registry\Registry;

/**
 * Webhook callback information logging
 *
 * @since   7.0.0
 */
trait LogCallback
{
	/**
	 * Maximum size of a log file before triggering automatic rotation
	 *
	 * @var   int
	 *
	 * @since 7.0.0
	 */
	protected static $maxLogFileSize = 1048756;

	/**
	 * Log a webhook callback
	 *
	 * @param   array   $data      The raw data to log.
	 * @param   string  $decision
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	protected function logCallback(array $data, string $decision): void
	{
		$logFile = $this->getLogFilePath();
		$logData = @file_get_contents($logFile);

		if ($logData === false)
		{
			$logData = '';
		}

		// Fulfillment webhooks do not have an alert_name so I add a fake one
		$webhookName = $data['alert_name'] ?? 'fulfillment';
		$webhookType = $this->translateAlertName($webhookName);
		$dateTime    = gmdate('Y-m-d H:i:s T');
		$printData   = print_r($data, true);

		$logData .= <<< TXT
================================================================================
Date / Time  : $dateTime
Webhook Name : $webhookName
Webhook Type : $webhookType
Decision     : $decision

$printData

TXT;

		File::write($logFile, $logData);
	}

	/**
	 * Returns the full path to the webhooks callback log. If the log is over the $maxLogFileSize limit it will be
	 * rotated.
	 *
	 * @return  string
	 *
	 * @since   7.0.0
	 */
	private function getLogFilePath(): string
	{
		/** @var Registry $config */
		$config          = $this->container->platform->getConfig();
		$logpath         = $config->get('log_path');
		$logFile         = $logpath . '/akeebasubs_webhooks.php';
		$logFilenameBase = substr($logFile, 0, -4);

		// If the log file does not exist, create it and return its name
		if (!File::exists($logFile))
		{
			$dummy = "<?php die();\n// Incoming webhook data information ?>\n";

			File::write($logFile, $dummy);

			return $logFile;
		}

		// If the file is under the log rotation limit, return its name
		if (@filesize($logFile) < self::$maxLogFileSize)
		{
			return $logFile;
		}

		$altLog = $logFilenameBase . '-1.php';

		if (File::exists($altLog))
		{
			File::delete($altLog);
		}

		File::copy($logFile, $altLog);
		File::delete($logFile);

		if (File::exists($logFile))
		{
			return $logFile;
		}

		return $this->getLogFilePath();
	}

	/**
	 * Returns a human-readable explanation of what the Paddle webhook alert is about.
	 *
	 * @param   string  $webhookName  The Paddle webhook name.
	 *
	 * @return  string
	 *
	 * @since   7.0.0
	 */
	private function translateAlertName(string $webhookName): string
	{
		switch ($webhookName)
		{
			case 'locker_processed':
				return 'Order created after successful payment';

			case 'payment_succeeded':
				return 'Payment made into our Paddle account';

			case 'payment_refunded':
				return 'Refund, full or partial';

			case 'high_risk_transaction_created':
				return 'Transaction flagged as High Risk';

			case 'high_risk_transaction_updated':
				return 'High Risk transaction has been processed';

			case 'payment_dispute_created':
				return 'Payment dispute / chargeback initiated';

			case 'payment_dispute_closed':
				return 'Payment dispute / chargeback case closed';

			case 'transfer_created':
				return 'Payout created (money will be transferred in the future)';

			case 'transfer_paid':
				return 'Payout complete (money has finished being transferred)';

			case 'new_audience_member':
				return 'Client opted in to marketing communication';

			case 'update_audience_member':
				return 'Client marketing information updated';

			case 'subscription_created':
				return 'New subscription created';

			case 'subscription_updated':
				return 'Subscription changed (upgrade / downgrade)';

			case 'subscription_cancelled':
				return 'Subscription canceled';

			case 'subscription_payment_succeeded':
				return 'Recurring subscription payment succeeded';

			case 'subscription_payment_failed':
				return 'Recurring subscription payment failed';

			case 'subscription_payment_refunded':
				return 'Recurring subscription payment refunded';

			// NB! This is NOT an Paddle webhook name. It's a special string added by logCallback.
			case 'fulfillment':
				return 'Fulfillment webhook';

			default:
				return 'Invalid webhook';
		}
	}
}