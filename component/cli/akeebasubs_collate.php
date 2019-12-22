<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Collates Paddle transactions with subscriptions, notifying us of possible problems
 *
 * Command line parameters:
 *
 * --max-date=DATE_STRING  Process transactions up to this date (default: now)
 * --min-date=DATE_STRING  Process transactions since this date (default: 24 hours ago)
 * --days=INTEGER          Process transactions since this many days before max-date (no default)
 * --level=STRING          Only process transactions for this subscription level
 * --for-real              Apply changes to the database.
 * --no-recurring          Don't go through recurring subscriptions
 * --no-one-off            Don't go through one-off subscriptions
 *
 * Note: --days overrides --min-date if both are set
 */

// Enable Joomla's debug mode
use Akeeba\Subscriptions\Admin\Helper\Email as EmailHelper;
use Akeeba\Subscriptions\Admin\Helper\Message;
use Akeeba\Subscriptions\Site\Model\JoomlaUsers;
use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;
use FOF30\Model\DataModel\Collection as DataCollection;
use FOF30\Model\DataModel\Exception\RecordNotLoaded;
use Joomla\CMS\Factory as JFactory;

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

class AkeebasubsCollateTransactions extends FOFApplicationCLI
{
	/**
	 * Oldest transaction to retrieve.
	 *
	 * Transactions before this date are NOT taken into account.
	 *
	 * @var   int
	 * @since 7.1.0
	 */
	private $minimumTimestamp = 0;

	/**
	 * Newest transaction to retrieve.
	 *
	 * Transactions after this date are NOT taken into account.
	 *
	 * @var   int
	 * @since 7.1.0
	 */
	private $maximumTimestamp = 0;

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
	 * Lists all problem transactions.
	 *
	 * Format: TRANSACTION_ID => REASON
	 *
	 * @var   array
	 * @since 7.1.0
	 */
	private $problemTransactions = [];

	/**
	 * Don't go through recurring subscriptions' transactions.
	 *
	 * @var   bool
	 * @since 7.1.0
	 */
	private $noRecurring = false;

	/**
	 * Don't go through one-off transactions.
	 *
	 * @var   bool
	 * @since 7.1.0
	 */
	private $noOneOff = false;

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
		$this->forReal     = $this->input->getBool('for-real', false);
		$this->noRecurring = $this->input->getBool('no-recurring', false);
		$this->noOneOff    = $this->input->getBool('no-one-off', false);

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

		// Set the date limits from command line parameters
		$this->setDateLimits();

		// Get all Paddle-enabled products
		$productLevels = $this->getProductLevels($container);

		// Apply subscription level limits
		$limitLevel = $this->input->getString('level', '') ?? '';

		if (!empty($limitLevel))
		{
			$productLevels = $productLevels->filter(function (Levels $level) use ($limitLevel) {
				return strtoupper($limitLevel) == strtoupper($level->title);
			});
		}

		// Do I have any levels to process at all?
		if ($productLevels->count() == 0)
		{
			$this->out('No Paddle-enabled products were found.');

			$this->close(201);
		}

		// Process transactions for all levels
		$productLevels->each(function (Levels $level) {
			$this->out(sprintf('Collating transactions for %s', $level->title));

			$this->collateLevel($level);
		});

		// Report any problems
		$this->sendEmail($container);
		$this->reportProblemTransactions();
	}

	/**
	 * @param   Container  $container
	 *
	 *
	 * @since version
	 */
	protected function sendEmail(Container $container): void
	{
		$html = "<dl>\n";

		foreach ($this->problemTransactions as $transaction => $reason)
		{
			$html .= <<< HTML
	<dt><strong>$transaction</strong></dt>
	<dd>$reason</dd>

HTML;
		}

		$html         .= "</dl>";
		$plugin       = \Joomla\CMS\Plugin\PluginHelper::getPlugin('akeebasubs', 'adminemails');
		$pluginParams = new Joomla\Registry\Registry($plugin->params);
		$emails       = trim($pluginParams->get('emails', ''));
		$emails       = str_replace(';', ',', $emails);
		$emails       = explode(',', $emails);

		if (empty($emails))
		{
			return;
		}

		$emails = array_map('trim', $emails);
		$emails = array_filter($emails, function ($email) {
			return !empty($email);
		});

		if (empty($emails))
		{
			return;
		}

		foreach ($emails as $email)
		{
			/** @var JoomlaUsers $usersModel */
			$usersModel = $container->factory->model('JoomlaUsers')->tmpInstance();
			$myUser     = $usersModel
				->email($email)
				->get(true)
				->first();
			$user       = $container->platform->getUser($myUser->id);
			[$subject, $templateText] = EmailHelper::loadEmailTemplate('plg_akeebasubs_adminemails_problem_transactions', null, $user);

			if (empty($subject))
			{
				return;
			}

			/** @var Subscriptions $fakeSub */
			$fakeSub = $container->factory->model('Subscriptions')->tmpInstance();
			$fakeSub->user_id = $myUser->id;

			$extras       = [
				'[PROBLEM_TRANSACTIONS]' => $html,
			];
			$templateText = Message::processSubscriptionTags($templateText, $fakeSub, $extras);
			$subject      = Message::processSubscriptionTags($subject, $fakeSub, $extras);

			// Get the mailer
			$mailer = $mailer = clone JFactory::getMailer();
			$mailer->IsHTML(true);
			$mailer->CharSet = 'UTF-8';
			$mailer->setSubject($subject);

			// Include inline images
			$pattern           = '/(src)=\"([^"]*)\"/i';
			$number_of_matches = preg_match_all($pattern, $templateText, $matches, PREG_OFFSET_CAPTURE);

			if ($number_of_matches > 0)
			{
				$substitutions = $matches[2];
				$last_position = 0;
				$temp          = '';

				// Loop all URLs
				$imgidx    = 0;
				$imageSubs = [];

				foreach ($substitutions as &$entry)
				{
					// Copy unchanged part, if it exists
					if ($entry[1] > 0)
					{
						$temp .= substr($templateText, $last_position, $entry[1] - $last_position);
					}

					// Examine the current URL
					$url = $entry[0];

					if ((substr($url, 0, 7) == 'http://') || (substr($url, 0, 8) == 'https://'))
					{
						// External link, skip
						$temp .= $url;
					}
					else
					{
						$ext = strtolower(JFile::getExt($url));

						// Commented out as we're not passed a template URL now that the the templates are in the database.
						/*if (!JFile::exists($url))
						{
							// Relative path, make absolute
							$url = dirname($template) . '/' . ltrim($url, '/');
						}*/

						if (!JFile::exists($url) || !in_array($ext, ['jpg', 'png', 'gif']))
						{
							// Not an image or inexistent file
							$temp .= $url;
						}
						else
						{
							// Image found, substitute
							if (!array_key_exists($url, $imageSubs))
							{
								// First time I see this image, add as embedded image and push to
								// $imageSubs array.
								$imgidx++;
								$mailer->AddEmbeddedImage($url, 'img' . $imgidx, basename($url));
								$imageSubs[$url] = $imgidx;
							}

							// Do the substitution of the image
							$temp .= 'cid:img' . $imageSubs[$url];
						}
					}

					// Calculate next starting offset
					$last_position = $entry[1] + strlen($entry[0]);
				}

				// Do we have any remaining part of the string we have to copy?
				if ($last_position < strlen($templateText))
				{
					$temp .= substr($templateText, $last_position);
				}

				// Replace content with the processed one
				$templateText = $temp;
			}

			$mailer->setBody($templateText);
			$mailer->addRecipient($myUser->email, $myUser->name);
			$mailer->Send();
		}
	}

	/**
	 * Set the date ranges to retrieve based on command-line options
	 *
	 * @since   7.1.0
	 */
	private function setDateLimits(): void
	{
		// Default limits: last 24 hours
		$this->maximumTimestamp = time();
		$this->minimumTimestamp = $this->maximumTimestamp - (24 * 3600);

		// Do I have a --max-date parameter?
		$maxDateString = $this->input->getString('max-date', '');

		if ($maxDateString)
		{
			$this->maximumTimestamp = (new Joomla\CMS\Date\Date($maxDateString))->getTimestamp();
		}

		// Do I have a --min-date parameter?
		$minDateString = $this->input->getString('min-date', '');

		if ($minDateString)
		{
			$this->minimumTimestamp = (new Joomla\CMS\Date\Date($minDateString))->getTimestamp();
		}

		// Do I have a --days parameter?
		$days = $this->input->getString('days', 0);

		if ($days > 0)
		{
			$this->minimumTimestamp = $this->maximumTimestamp - ($days * 24 * 3600);
		}
	}

	/**
	 * Get all product levels with Paddle-enabled products
	 *
	 * @param   Container  $container
	 *
	 * @return  DataCollection
	 *
	 * @since   7.1.0
	 */
	private function getProductLevels(Container $container): DataCollection
	{
		/** @var Levels $levelsModel */
		$levelsModel = $container->factory->model('Levels')->tmpInstance();
		$allLevels   = $levelsModel->enabled(1)->get(true);

		return $allLevels->filter(function (Levels $level) {
			return !empty($level->paddle_plan_id) || !empty($level->paddle_product_id);
		});
	}

	/**
	 * Collate transactions for a specific Level
	 *
	 * @param   Levels  $level  Susbcription level to collate transactions for
	 *
	 * @since   7.1.0
	 */
	private function collateLevel(Levels $level): void
	{
		$paddlePlanId    = $level->paddle_plan_id;
		$paddleProductId = $level->paddle_product_id;

		if (!empty($paddlePlanId) && !$this->noRecurring)
		{
			$this->out(sprintf("-- Processing recurring subscriptions (%s)", $paddlePlanId));
			$transactions = $this->getTransactions($paddlePlanId);
			$this->out(sprintf('   -- Collating %d transactions', count($transactions)));
			$this->collateTransactions($level, $transactions);
		}

		if (!empty($paddleProductId) && !$this->noOneOff)
		{
			$this->out(sprintf("-- Processing one-off transactions (%s)", $paddleProductId));
			$transactions = $this->getTransactions($paddleProductId);
			$this->out(sprintf('   -- Collating %d transactions', count($transactions)));
			$this->collateTransactions($level, $transactions);
		}
	}

	/**
	 * Retrieves all transactions for the specified product and date range
	 *
	 * @param   int  $productId  Paddle product or plan ID
	 *
	 * @return  array
	 *
	 * @since   7.1.0
	 */
	private function getTransactions(int $productId)
	{
		$transactions = [];
		$http         = Joomla\CMS\Http\HttpFactory::getHttp();
		$url          = sprintf('https://vendors.paddle.com/api/2.0/product/%d/transactions', $productId);
		$postData     = [
			'vendor_id'        => $this->vendorId,
			'vendor_auth_code' => $this->vendorAuthCode,
			'page'             => 1,
		];
		$page         = 0;

		while (true)
		{
			$postData['page'] = ++$page;
			$return           = $http->post($url, $postData);

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
				break;
			}

			$this->out(sprintf('   (retrieved page %d)', $page));

			foreach ($decodedBody['response'] as $transaction)
			{
				// Have we hit the earliest date we're supposed to handle?
				$creationDate         = new Joomla\CMS\Date\Date($transaction['created_at']);
				$transactionTimestamp = $creationDate->getTimestamp();

				if ($transactionTimestamp < $this->minimumTimestamp)
				{
					// Yeah, return the transactions list
					return $transactions;
				}

				// Is this transaction too new for us?
				if ($transactionTimestamp > $this->maximumTimestamp)
				{
					continue;
				}

				// Transaction with range. Add it to the list.
				$transactions[] = $transaction;
			}
		}

		return $transactions;
	}

	private function collateTransactions(Levels $level, array $transactions)
	{
		if (empty($transactions))
		{
			return;
		}

		/** @var Subscriptions $subModel */
		$subModel = $level->getContainer()->factory->model('Subscriptions')->tmpInstance();

		foreach ($transactions as $transaction)
		{
			$processorKey        = $transaction['order_id'];
			$subscriptionId      = $transaction['passthrough'];
			$receiptUrl          = $transaction['receipt_url'];
			$paddleState         = $transaction['status'];
			$paddleSubscription  = $transaction['subscription'] ?? [];
			$isPaddleRecurring   = !empty($paddleSubscription);
			$isLatestTransaction = true;

			// Try to find the subscription based on the passthrough ID
			try
			{
				/** @var Subscriptions $sub */
				$sub = $subModel->tmpInstance()->findOrFail($subscriptionId);
			}
			catch (RecordNotLoaded $e)
			{
				// This should only stop me if this is NOT a recurring subscription.
				if (empty($paddleSubscription))
				{
					$this->problemTransactions[$processorKey] = sprintf(
						"Could not find record %s. Client email %s.",
						$subscriptionId,
						$transaction['user']['email']
					);

					continue;
				}
			}

			// If it's a Paddle subscription the passthrough is the original subscription. Use the key instead.
			if ($isPaddleRecurring)
			{
				try
				{
					/** @var Subscriptions $sub */
					$sub2 = $subModel->tmpInstance()->findOrFail(['processor_key' => $processorKey]);
					$sub  = $sub2;
				}
				catch (RecordNotLoaded $e)
				{
					$mustBarf     = true;
					$foundSubById = isset($sub) && is_object($sub) && ($sub->getId() != 0);

					if ($foundSubById)
					{
						/**
						 * I found the record by passthrough but not by transaction ID. Possibilities:
						 *
						 * 1. There is no transaction ID in the record. I can fix this later in the code.
						 * 2. It is already refunded?
						 * 2. Someone bought the same recurring subscription twice. That's a problem.
						 * 3. I only found an old subscription record from a previous installment. That's a problem.
						 *
						 * The latter three problems will be caught below.
						 */
						if (empty($sub->processor_key))
						{
							$mustBarf = false;
						}
					}

					// Check if the transaction is already refunded
					if ($mustBarf)
					{
						if ($paddleState == 'refunded')
						{
							// This is a refunded transaction from a double payment. DO NOTHING! GO AWAY!
							continue;
						}
						elseif ($paddleState == 'disputed')
						{
							// This is a disputed transaction. No action until it's resolved.
							continue;
						}
					}

					// Check if the client bought the same subscription twice
					if ($mustBarf && $foundSubById)
					{
						/**
						 * If the subscription status is "deleted" we have a problem. Since we are here, the transaction
						 * is NOT refunded (it'd be caught above). We should refund the poor sod.
						 */
						if ($paddleSubscription['status'] == 'deleted')
						{
							$this->problemTransactions[$processorKey] = sprintf(
								"Client %s (Paddle user ID %s) probably needs a refund on their canceled subscription %s that they bought around %s.",
								$transaction['user']['email'],
								$transaction['user']['user_id'],
								$level->title,
								$transaction['created_at']
							);

							continue;
						}

						// The subscription is active but does not have a matching transaction. What the hell.
						$this->problemTransactions[$processorKey] = sprintf(
							"Client %s (Paddle user ID %s) may have bought the same subscription (%s) multiple times around %s.",
							$transaction['user']['email'],
							$transaction['user']['user_id'],
							$level->title,
							$transaction['created_at']
						);

						continue;
					}

					// Assume that the renewal notification never reached our system
					if ($mustBarf)
					{
						if (!$foundSubById)
						{
							$this->problemTransactions[$processorKey] = sprintf(
								"Recurring transaction %s not found and record %05u not found. Are we missing a subscription record?!",
								$processorKey,
								$subscriptionId
							);
						}
						else
						{
							$this->problemTransactions[$processorKey] = sprintf(
								"Client %s (Paddle user ID %s) has a recurring renewal for %s on %s which has not been entered in our system.",
								$transaction['user']['email'],
								$transaction['user']['user_id'],
								$level->title,
								$transaction['created_at']
							);
						}

						$this->out(sprintf("      Could not find subscription renewal with key %s", $processorKey));
					}

					continue;
				}
			}

			// If the subscription state is Canceled do nothing; I have manually edited it.
			$recordState = $sub->getFieldValue('state');

			if ($recordState == 'X')
			{
				continue;
			}

			// Recurring subscription: is this the latest instalment?
			if ($isPaddleRecurring)
			{
				// First, assume that the currently loaded record is the first transaction and load its parameters.
				$firstSubParams = $sub->params;

				/**
				 * The currently loaded record may NOT be the original recurring subscription record. The
				 * latest_instalment_subscription param key is ONLY present in the first transaction's record. I have to
				 * check the passthrough variable in the Paddle order and try to load the first transaction record from
				 * it. *
				 */
				if ($sub->getId() != $subscriptionId)
				{
					try
					{
						$firstSub       = $subModel->tmpInstance()->findOrFail($subscriptionId);
						$firstSubParams = $firstSub->params;
					}
					catch (Exception $e)
					{
						$firstSubParams = $sub->params;
					}
				}

				/**
				 * If I have the latest_instalment_subscription key we have at least 2 payments in the subscriptions. Is
				 * our record the latest instalment?
				 *
				 * Any changes made to older instalments will have the _dontNotify flag to avoid sending emails.
				 */
				if (array_key_exists('latest_instalment_subscription', $firstSubParams))
				{
					$latestInstallmentId = $firstSubParams['latest_instalment_subscription'];
					$isLatestTransaction = ($latestInstallmentId == $sub->getId());
				}
			}

			$updates = [];

			// Check that the processor key is correct
			if (empty($sub->processor_key))
			{
				$updates['processor_key'] = $processorKey;
			}
			// Does the subscription already have a different processor key...?
			elseif ($sub->processor_key != $processorKey)
			{
				$this->problemTransactions[$processorKey] = sprintf(
					"Subscription #%05u already has a different order ID, %s. Is this a double transaction?",
					$subscriptionId,
					$sub->processor_key
				);

				continue;
			}

			// Check that the receipt URL exists
			if (empty($sub->receipt_url))
			{
				$updates['receipt_url'] = $receiptUrl;
			}

			/**
			 * If Paddle's transaction is completed the state on our side must be 'C' as well
			 *
			 * Note: I do NOT handle "X" (Canceled) records because these are the ones I have manually canceled.
			 */
			if (($paddleState == 'completed') && ($recordState != 'C') && ($recordState != 'X'))
			{
				$updates['state'] = 'C';
			}
			/**
			 * Partial refund
			 *
			 * In this case we want the user to still be subscribed. This is a VAT refund or me helping a user who
			 * accidentally overpaid.
			 *
			 * In very rare cases I may make a partial refund and cancel the subscription. That's why I don't handle
			 * state "X" (Canceled)
			 */
			elseif (($paddleState == 'partially_refunded') && ($recordState != 'C') && ($recordState != 'X'))
			{
				$updates['state'] = 'C';
			}
			/**
			 * Refunded subscriptions are typically, but not always, meant to also be inactive on our site. With some
			 * exceptions.
			 *
			 * In July 2019 I had a couple of clients who paid twice. I refunded the second transaction. However, both
			 * transactions (the one we kept and the one we refunded) point to the SAME record. This should be caught
			 * when we check for the processor_key discrepancy.
			 *
			 * Moreover, we may decide to refund a recurring payment BUT let the user keep their subscription, e.g. if
			 * we billed them in error (it's happened around November 2019) or we gave a special refund for that period
			 * because they couldn't use our software due to a fault of ours, or if there were extenuating
			 * circumstances. There is no way to communicate that through Paddle's API but I *do* put that in notes
			 * fields.
			 *
			 * Since I can not be 100% sure that the subscription needs to be canceled I'd rather have me check it
			 * manually rather than screwing up a client's subscription.
			 */
			elseif (($paddleState == 'refunded') && ($recordState != 'X'))
			{
				if ($isPaddleRecurring)
				{
					$this->problemTransactions[$processorKey] = sprintf(
						"Recurring subscription #%05u is refunded on Paddle but active on our site. Client %s (Paddle user ID %s), level %s. Transaction around %s",
						$subscriptionId,
						$transaction['user']['email'],
						$transaction['user']['user_id'],
						$level->title,
						$transaction['created_at']
					);

					continue;
				}
				else
				{
					$this->problemTransactions[$processorKey] = sprintf(
						"One-off subscription #%05u is fully refunded on Paddle but active on our site. Shouldn't it be canceled? Client %s (Paddle user ID %s), level %s. Transaction around %s.",
						$subscriptionId,
						$transaction['user']['email'],
						$transaction['user']['user_id'],
						$level->title,
						$transaction['created_at']
					);

					continue;
				}
			}

			// Do I have the correct checkout_id?
			$params = isset($params) ? $params : $sub->params;

			// Special handling for recurring subscriptions
			if ($isPaddleRecurring)
			{
				// Has akeebasubs_fix_recurring.php or this script already marked this record as no longer recurring?
				$noLongerRecurring = array_key_exists('no_longer_recurring', $params) && ($params['no_longer_recurring'] == 1);
				$noRecurringInfo   = empty($sub->cancel_url) || empty($sub->update_url);
				$notCanceled       = $sub->getFieldValue('state') != 'X';
				$enabled           = $sub->enabled;

				// Is a recurring subscription not marked as such?
				if ($noRecurringInfo && $notCanceled && $enabled && !$noLongerRecurring)
				{
					try
					{
						$subInfo = $this->getSubscriptionInfo($paddleSubscription['subscription_id']);
					}
					catch (RuntimeException $e)
					{
						$this->problemTransactions[$processorKey] = "Recurring subscription #$subscriptionId is not marked as such and I cannot fix it automatically.";

						continue;
					}

					if ($subInfo['state'] == 'deleted')
					{
						// The subscription has already been canceled. Mark it as no longer recurring so we don't bother again.
						$updates['params'] = array_merge($params, [
							'no_longer_recurring' => 1,
						]);
					}
					else
					{
						// Update the recurring information
						$updates['cancel_url'] = $subInfo['cancel_url'];
						$updates['update_url'] = $subInfo['update_url'];
						$updates['params']     = array_merge($params, [
							'subscription_id'      => $subInfo['subscription_id'],
							'subscription_plan_id' => $subInfo['plan_id'],
						]);
					}

					$params = $updates['params'];
				}
			}

			/**
			 * Only send notification emails and run integrations for the latest transaction.
			 *
			 * "Latest transaction" is defined as either of the following conditions:
			 *
			 * - An one-off payment
			 * - A recurring subscription payment that's either the first instalment OR the latest instalment in the
			 *   subscription range.
			 */
			if (!$isLatestTransaction)
			{
				$updates['_dontNotify'] = 1;
			}

			// Save if dirty and --for-real was specified
			if (!empty($updates))
			{
				if ($this->forReal)
				{
					$sub->save($updates);
				}
				else
				{
					$this->problemTransactions[$processorKey] = sprintf(
						"Subscription record #%05u out-of-date: %s",
						$sub->getId(),
						implode(', ', array_keys($updates))
					);
				}
			}
		}
	}

	private function getSubscriptionInfo($subscription_id): array
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

	/**
	 * Report detected transactions with problems that require a human to process
	 *
	 * @since   7.1.0
	 */
	private function reportProblemTransactions(): void
	{
		if (empty($this->problemTransactions))
		{
			$this->out('No problems to report');

			return;
		}

		$this->out();
		$this->out(str_repeat('=', 80));
		$this->out('PROBLEM TRANSACTIONS');
		$this->out(str_repeat('=', 80));

		foreach ($this->problemTransactions as $transaction => $reason)
		{
			$this->out($transaction);
			$this->out("\t$reason");
			$this->out(str_repeat('-', 80));
		}
	}

	public function isClient()
	{
		return false;
	}
}

FOFApplicationCLI::getInstance('AkeebasubsCollateTransactions')->execute();