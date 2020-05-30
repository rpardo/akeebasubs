<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Email;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use FOF30\Container\Container;
use FOF30\Date\Date;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log as JLog;

/**
 * Sends notification emails to subscribers
 */
class plgAkeebasubsSubscriptionemails extends JPlugin
{
	protected static $langStringPrefix = 'PLG_AKEEBASUBS_SUBSCRIPTIONEMAILS_EMAIL';

	/**
	 * Public constructor. Overridden to load the language strings.
	 */
	public function __construct(&$subject, $config = [])
	{
		if (!is_object($config['params']))
		{
			$config['params'] = new JRegistry($config['params']);
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Called whenever a subscription is modified. Namely, when its enabled status,
	 * payment status or valid from/to dates are changed.
	 *
	 * @param   Subscriptions  $row
	 * @param   array          $info
	 */
	public function onAKSubscriptionChange(Subscriptions $row, array $info)
	{
		JLog::addLogger(['text_file' => "akeebasubs_emails.php"], JLog::ALL, ['akeebasubs.emails']);

		$payState              = $row->getFieldValue('state', 'N');
		$hasModifiedInfo       = isset($info['modified']) && !is_null($info['modified']);
		$hasPreviousInfo       = is_object($info['previous']);
		$isRecurring           = !empty($row->cancel_url) && !empty($row->update_url);
		$isContactAllowed      = $row->contact_flag != 3;
		$isCurrentlyActive     = $row->enabled;
		$isModifiedRecord      = $info['status'] == 'modified';
		$isPaymentStateChanged = $hasModifiedInfo && array_key_exists('state', (array) $info['modified']);
		$isEnabledChanged      = $hasModifiedInfo && array_key_exists('enabled', (array) $info['modified']);
		$isContactFlagChanged  = $hasModifiedInfo && array_key_exists('contact_flag', (array) $info['modified']);
		$wasPreviouslyPending  = $hasPreviousInfo && $info['previous']->getFieldValue('state') == 'P';
		$wasPreviouslyNew      = $hasPreviousInfo && $info['previous']->getFieldValue('state') == 'N';

		// TODO Email recurring subscriptions on first instalment and only if they have a non-zero trial period. These are clients who purchased a new subscription and chose the recurring payment option as well.

		/**
		 * A New subscription record was created: no emails
		 *
		 * Why? We always create a new subscription record with payment state New (N) when a user tries to subscribe,
		 * before showing him the payment UI. No email needs to be sent in this case.
		 *
		 * Moreover, when we manually create subscriptions we save them with enabled = 0. If the payment is completed,
		 * the automatic change code is executed upon saving. Therefore this event handler will run again against the
		 * modified subscription which is now becoming enabled sending the new, paid subscription email.
		 */
		if (!$isModifiedRecord)
		{
			return;
		}

		/**
		 * A modified record without a payment attempt (still New).
		 *
		 * We should have no use case for this but it can happen if a human operator fat-fingers a manually created /
		 * modified subscription.
		 */
		if ($payState == 'N')
		{
			return;
		}

		/**
		 * Recurring subscriptions with payment state change
		 *
		 * Note that Paddle has sent the user an email.
		 *
		 * This does NOT handle a hard failure (past due and gave up on retrying billing). In this case the cancel_url
		 * and update_url is cleared, therefore the subscription is handled as an one-time payment subscription which
		 * just expired (see further below).
		 *
		 * TODO We need to handle some recurring subscription cases which warrant an email from us:
		 * - First installment, non-zero trial period, zero payment: Early renewal into a recurring subscription
		 * - First installment, zero trial period: new recurring subscription, e.g. with a coupon code
		 */
		if ($isRecurring && $isPaymentStateChanged)
		{
			return;
		}

		/**
		 * One-time payment subscription just got paid
		 */
		if ($isPaymentStateChanged && ($payState == 'C'))
		{
			// P => C (enabled = 1): A pending subscription just got paid
			if ($isCurrentlyActive && $wasPreviouslyPending)
			{
				$this->sendEmail($row, 'paid', $info);

				return;
			}

			// N or X => C (enabled=1): A new subscriptions just got paid
			if ($isCurrentlyActive && !$wasPreviouslyPending)
			{
				$this->sendEmail($row, 'new_active', $info);

				return;
			}

			// whatever => C (enabled=0): An early renewal was successfully purchased
			if (!$isCurrentlyActive)
			{
				// Don't send emails for silent renewals. No use case, just a precaution.
				if (!$isContactAllowed)
				{
					return;
				}

				$this->sendEmail($row, 'new_renewal', $info);

				return;
			}
		}

		/**
		 * One-time payment subscription changed to Pending (P)
		 */
		if ($isPaymentStateChanged && ($payState == 'P'))
		{
			// If the subscription is active (LOLWUT?!) or previously had any status other than new: no email
			if ($isCurrentlyActive && !$wasPreviouslyNew)
			{
				return;
			}

			// Pending payment
			$this->sendEmail($row, 'new_pending', $info);
		}

		/**
		 * One-time subscription changed to Cancelled (X)
		 */
		if ($isPaymentStateChanged && ($payState == 'X'))
		{
			// N => X: the payment was refused by Paddle
			if ($wasPreviouslyNew)
			{
				$this->sendEmail($row, 'cancelled_new', $info);

				return;
			}

			/**
			 * P or C => C: the payment was cancelled / refunded
			 *
			 * It will first try to send a different email per cancellation reason. If that fails it will fall back to
			 * the generic subscription cancellation email.
			 */
			if (!$this->sendEmail($row, 'cancelled_existing_' . $row->cancellation_reason, $info))
			{
				$this->sendEmail($row, 'cancelled_existing', $info);
			}

			return;
		}

		/**
		 * A subscription got enabled / disabled but its payment status is not Completed.
		 *
		 * THIS CANNOT HAPPEN. A subscription can only be enabled with a payment status Completed (C). Yet here we are
		 * with a subscription that was either enabled with a non-Completed payment status OR just got enabled with a
		 * non-Completed payment status. Since this is not possible, a human operator screwed up. Next time we read or
		 * save the subscription we will rectify this issue. So, bail out for now.
		 */
		if ($isEnabledChanged && ($payState != 'C'))
		{
			return;
		}

		/**
		 * C (enabled = 1) => C (enabled = 0): expired subscription
		 */
		if ($isEnabledChanged && !$row->enabled)
		{
			/**
			 * Send email unless the contact_flag is 3.
			 *
			 * Hard failure recurring subscriptions set the contact flag to 0 to allow this email to be sent.
			 */
			if (!$isContactAllowed)
			{
				return;
			}

			$this->sendEmail($row, 'expired', $info);

			return;
		}

		/**
		 * C (enabled = 0) => C (enabled = 1): renewal got activated.
		 */
		if ($isEnabledChanged && $row->enabled)
		{
			/**
			 * Do not email if contact_flag is 3.
			 */
			if (!$isContactAllowed)
			{
				return;
			}

			/**
			 * Do not email if it's a recurring subscription. This shouldn't happen but, hey, we'd rather be safe than
			 * sorry.
			 */
			if ($isRecurring)
			{
				return;
			}

			$this->sendEmail($row, 'published', $info);

			return;
		}

		/**
		 * Modified subscription whose enabled state and payment state did not change BUT whose contact_flag changed.
		 *
		 * We assume that only the contact flag has changed and not send any emails.
		 *
		 * This will only be false if a human operator modifies a subscription length AND the contact flag at the same
		 * time.
		 *
		 * THIS SHOULD NEVER HAPPEN since Subscriptions::subNotifiable() exempts contact_flag from triggering this
		 * event. Again, we'd rather be safe than sorry hence this check.
		 */
		if ($isContactFlagChanged)
		{
			return;
		}

		/**
		 * All other cases: generic email detailing change in the subscription.
		 *
		 * Do not send for recurring subscriptions or when we have set the no contact flag (contact_flag == 3)
		 */
		if (!$isContactAllowed || $isRecurring)
		{
			return;
		}

		$this->sendEmail($row, 'generic', $info);
	}

	/**
	 * Sends out the email to the owner of the subscription.
	 *
	 * @param   Subscriptions  $row   The subscription row object
	 * @param   string         $type  The type of the email to send (generic, new,)
	 * @param   array          $info  Subscription modification information (used in children classes)
	 *
	 * @return bool
	 */
	protected function sendEmail(Subscriptions $row, $type = '', array $info = [])
	{
		// Get the user object
		$container = Container::getInstance('com_akeebasubs');

		// Get a preloaded mailer
		$key    = 'plg_akeebasubs_' . $this->_name . '_' . $type;
		$mailer = Email::getPreloadedMailer($row, $key);

		if (is_null($mailer))
		{
			return false;
		}

		try
		{
			$result = $mailer->Send();
		}
		catch (RuntimeException $e)
		{
			return false;
		}

		$mailer = null;

		// Log the email we just sent
		$this->logEmail($row, $type);

		return $result;
	}

	protected function logEmail(Subscriptions $row, $type = '')
	{
		$container = Container::getInstance('com_akeebasubs');

		/** @var \Akeeba\Subscriptions\Admin\Model\JoomlaUsers $user */
		$user = $row->juser ?? $container->factory->model('JoomlaUsers')->tmpInstance()->load($row->user_id);
		/** @var \Akeeba\Subscriptions\Admin\Model\Levels $level */
		$level = $row->level ?? $container->factory->model('Levels')->tmpInstance()->load($row->akeebasubs_level_id);

		// Is this a recurring or one-time subscription?
		$isRecurring   = !empty($row->update_url) && !empty($row->cancel_url);
		$recurringText = $isRecurring ? 'recurring' : 'one-time';

		// Get a human readable payment state
		$payState        = $row->getFieldValue('state');
		$payStateToHuman = [
			'N' => 'New',
			'P' => 'Pending',
			'C' => 'Completed',
			'X' => 'Canceled',
		];
		$payStateHuman   = $payStateToHuman[$payState];

		// Add cancellation reason for canceled subscriptions
		if ($payState == 'X')
		{
			$payStateHuman .= sprintf(' (%s)', $row->cancellation_reason);

			if ($row->cancellation_reason == 'past_due')
			{
				$recurringText = 'recurring';
			}
		}

		// Create the log entry text
		$logEntry = sprintf(
			'%s (%s) to %s <%s> (%s) for %s #%05u %s -- %s %s to %s -- Contact Flag %d',
			$type,
			Text::_(sprintf("%s_%s", self::$langStringPrefix, $type)),
			$user->username,
			$user->email,
			$user->name,
			$payStateHuman,
			$row->akeebasubs_subscription_id,
			$level->title,
			$recurringText,
			Date::getInstance($row->publish_up)->format('Y-m-d H:i:s T'),
			Date::getInstance($row->publish_down)->format('Y-m-d H:i:s T'),
			$row->contact_flag
		);

		// If there has been a transaction recorded append it to the log entry
		if ($payState != 'N')
		{
			$logEntry .= sprintf(' -- %s payment key %s', ucfirst($row->processor), $row->processor_key);
		}

		// Write the log entry
		JLog::add($logEntry, JLog::INFO, 'akeebasubs.emails');
	}
}
