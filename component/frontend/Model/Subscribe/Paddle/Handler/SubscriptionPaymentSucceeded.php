<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;

use Akeeba\Subscriptions\Admin\Helper\Format;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\StackCallback;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use Exception;
use FOF30\Date\Date;

/**
 * Handle a successful recurring payment event.
 *
 * @see         https://paddle.com/docs/subscriptions-event-reference/#subscription_payment_succeeded
 *
 * @since       7.0.0
 */
class SubscriptionPaymentSucceeded extends PaymentSucceeded
{
	use StackCallback;

	/**
	 * Handle a webhook callback from the payment service provider about a specific subscription
	 *
	 * @param   Subscriptions  $subscription  The subscription the webhook refers to
	 * @param   array          $requestData   The request data minus component, option, view, task
	 *
	 * @return  string|null  Text to include in the callback response page
	 *
	 * @throws  Exception  In case an error occurs. The exception code will be used as the HTTP status.
	 *
	 * @since  7.0.0
	 */
	public function handleCallback(Subscriptions $subscription, array $requestData): ?string
	{
		/**
		 * IMPORTANT! $subscription always contains the subscription record of the VERY FIRST payment.
		 */

		/**
		 * I will accept a recurring subscription even if the actual record is not marked as such.
		 *
		 * This field is NOT editable in the backend. Every time I manually set up a recurring subscription because
		 * of some failure I was not entering a recurring_plan_id. This invalidates this sanity check.
		 */
		/**
		// Sanity check
		$isRecurring = isset($subscription->params['recurring_plan_id']) && ($subscription->params['recurring_plan_id'] == $subscription->level->paddle_plan_id);

		if (!$isRecurring)
		{
			throw new \RuntimeException('Recurring payment notification was issued for an one-off subscription');
		}
		/**/

		$requestPlanId      = $requestData['subscription_plan_id'];
		$subscriptionPlanId = $subscription->level->paddle_plan_id;

		if ($requestPlanId != $subscriptionPlanId)
		{
			throw new \RuntimeException(sprintf('Callback for plan ID %u was sent for a subscription entry belonging to a level linked to plan ID %u.', $requestPlanId, $subscriptionPlanId));
		}

		// Calculate the price parameters
		$gross_amount = (float) $requestData['balance_gross'];
		$tax_amount   = (float) $requestData['balance_tax'];
		$fee_amount   = (float) $requestData['balance_fee'];
		$net_amount   = $gross_amount - $tax_amount;
		$tax_percent  = sprintf('%0.2f', 100.00 * $tax_amount / $net_amount);

		// Did I have a paid trial period (e.g. a year at full price before recurring kicks in)?
		$initial_price      = isset($subscription->params['override_initial_price']) ? $subscription->params['override_initial_price'] : null;
		$is_initial_payment = isset($requestData['initial_payment']) && ($requestData['initial_payment'] == 1);
		// The prediscount and discount amount are zero EXCEPT for the paid trial period
		$prediscount_amount = 0.00;
		$discount_amount    = 0.00;
		// The discount amount is ONLY calculated for the paid trial period

		if (!is_null($initial_price) && $is_initial_payment)
		{
			$prediscount_amount = $subscription->prediscount_amount;
			$discount_amount    = $subscription->prediscount_amount - $net_amount;
			$note               = 'Initial payment with overridden initial price';

			if (isset($subscription->params['override_trial_days']) && !empty($subscription->params['override_trial_days']))
			{
				$note .= sprintf(', initial period set to %u days.', $subscription->params['override_trial_days']);
			}
		}
		elseif ($is_initial_payment)
		{
			$note = 'Initial payment (first instalment / billing period)';
		}
		else
		{
			$note = sprintf('Instalment (billing period) #%s', $requestData['instalments']);
		}

		$jDate      = new Date($requestData['next_bill_date']);
		$plusOneDay = new \DateInterval('P1D');
		$jDate->add($plusOneDay);

		$updates = [
			'processor_key'      => $requestData['order_id'],
			'state'              => 'C',
			// Yeah, enabled is 0. Upon saving it gets updated to 1 and triggers the plugins.
			'enabled'            => 0,
			'payment_method'     => $requestData['payment_method'],
			// This effectively marks the transaction as "don't let the user try to pay again"
			'payment_url'        => '',
			'receipt_url'        => $requestData['receipt_url'],
			'gross_amount'       => $gross_amount,
			'tax_amount'         => $tax_amount,
			'net_amount'         => $net_amount,
			'tax_percent'        => $tax_percent,
			'prediscount_amount' => $prediscount_amount,
			'discount_amount'    => $discount_amount,
			'fee_amount'         => $fee_amount,
			'notes'              => $note,
			'publish_up'         => gmdate('Y-m-d H:i:s'),
			'publish_down'       => $jDate->format('Y-m-d H:i:s'),
			'contact_flag'       => 3,
		];

		// Handle an automatic subscription update (n-th payment)
		if (!$is_initial_payment)
		{
			// Necessary updates to cancel an existing, active subscription
			$expirationUpdates = [
				'publish_down' => (new Date())->toSql(),
				'enabled' => 0,
				'contact_flag' => 3,
				'notes' => "Automatically renewed subscription on " . Format::date('now'),
			];

			/**
			 * Find the previously active subscription record and immediately expire it.
			 *
			 * There are two possibilities:
			 * -- 2nd payment. There's no params['latest_instalment_subscription']; the latest instalment IS the first
			 *    payment's subscription record (original subscription record created).
			 * -- 3rd...nth payment. The params['latest_instalment_subscription'] points us to the n-1 payment's
			 *    subscription record. Since all previous payment's (1 to n-2) have already been expired we don't need
			 *    to deal with them. We just deal with the n-1 record.
			 *
			 * In all cases we use _dontNotify because we don't want to trigger any plugins due to the (fake) expiration
			 * of the n-1 record. The expiration is fake because the subscription did not *really* expire! The n-th
			 * record picks up where the n-1 record stopped. This is a silent renewal!
			 */
			if (isset($subscription->params['latest_instalment_subscription']))
			{
				// Yes. Load and immediately expire the n-1 payment's subscription record.
				/** @var Subscriptions $oldSub */
				$oldSub = $subscription->getClone();
				$oldSub->load($subscription->params['latest_instalment_subscription']);
				$expirationUpdates['notes'] = $oldSub->notes . "\n" . $expirationUpdates['notes'];
				$oldSub->_dontNotify(true);
				$oldSub->save($expirationUpdates);
				$oldSub->_dontNotify(false);
			}
			else
			{
				// No n-1 record. This is the 2nd payment. Immediately expire the 1st payment's subscription record.
				$subscription->_dontNotify(true);
				$expirationUpdates['notes'] = $subscription->notes . "\n" . $expirationUpdates['notes'];
				$subscription->save($expirationUpdates);
				$subscription->_dontNotify(false);
			}

			/**
			 * Get a reference to the initial instalment's record. I need it to update its
			 * params['latest_instalment_subscription'] after I create a new subscription record ;)
			 */
			/** @var Subscriptions $initialSubscription */
			$initialSubscription = $subscription->getClone();

			/**
			 * At this point $subscription still contains the 1st payment's record. I don't want to update that record,
			 * I need to create a NEW record for my n-th instalment. The way to do that is setting
			 * akeebasubs_subscription_id to 0. This forced the Subscriptions model to create a NEW record.
			 */
			$subscription->akeebasubs_subscription_id = 0;

			// In case this was a legacy record with an Akeeba Subs generated invoice I need to remove it as well.
			$subscription->akeebasubs_invoice_id = 0;

			// Moreover, I need to kill any stacked callback information as they no longer refer to THIS record.
			if (isset($subscription->params['callbacks']))
			{
				$params = $subscription->params;
				unset($params['callbacks']);
				$subscription->params = $params;
			}
		}

		// Stack this callback's information to the subscription record
		$updates = array_merge($updates, $this->getStackCallbackUpdate($subscription, $requestData));

		// Store the checkout_id
		$updates['params']['checkout_id'] = $requestData['checkout_id'];

		// Save the Paddle subscription ID
		$updates['params']['subscription_id'] = $requestData['subscription_id'];

		/**
		 * Save the changes (or create a new record) and trigger the necessary plugin events.
		 *
		 * Important: we use _noemail to prevent firing the new subscription email on automatic renewals. The client
		 * has already received notification from Paddle about their successful payment.
		 */
		$this->container->platform->importPlugin('akeebasubs');
		$subscription->_noemail(true);
		$subscription->save($updates);
		$subscription->_noemail(false);
		$this->container->platform->runPlugins('onAKAfterPaymentCallback', [
			$subscription,
		]);

		/**
		 * If this was the n-th payment I have created a new subscription record. As discussed above, I need to update
		 * the first payment's record with this latest subscription record's ID. This will be used for the n+1 payment,
		 * as it will need to expire the n-th payment's record (the record I just created).
		 */
		if (!$is_initial_payment && isset($initialSubscription))
		{
			$params = $initialSubscription->params;
			$params['latest_instalment_subscription'] = $subscription->getId();
			$initialSubscription->params = $params;
			$initialSubscription->_dontNotify(true);
			$initialSubscription->save();
			$initialSubscription->_dontNotify(false);
		}

		/**
		 * Update the user's country, if necessary.
		 *
		 * Yes, this has to run on every instalment. It's possible that the user originally subscribed as an individual
		 * residing in Greece and two and a half years later he wants to switch his subscription to a business
		 * subscription for a company based in Cyprus. The way to do it is to go to the update_url BEFORE the next
		 * billing cycle and change his country and possibly the VAT number. If I do not update it now I will end up
		 * with stale information about the user in Akeeba Subs' database.
		 */
		$currentCountry = $subscription->juser->getProfileField('akeebasubs.country');
		$newCountry     = $requestData['country'];

		if (is_null($currentCountry))
		{
			$this->updateUserProfile($subscription->user_id, 'akeebasubs.country', $newCountry, true);
		}
		elseif ($newCountry != $currentCountry)
		{
			$this->updateUserProfile($subscription->user_id, 'akeebasubs.country', $newCountry, false);
		}

		// Done. No output to be sent (returns a 200 OK with an empty body)
		return null;
	}
}