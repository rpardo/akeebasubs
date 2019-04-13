<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;

use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\FixSubscriptionDate;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\RecurringSubscriptions;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\StackCallback;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use Exception;
use FOF30\Container\Container;
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
	use RecurringSubscriptions;
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
		// Calculate the price parameters
		$gross_amount = (float) $requestData['balance_gross'];
		$tax_amount   = (float) $requestData['balance_tax'];
		$fee_amount   = (float) $requestData['balance_fee'];
		$net_amount   = $gross_amount - $tax_amount;
		$tax_percent  = sprintf('%0.2f', 100.00 * $tax_amount / $net_amount);

		// Did I have a paid trial period (e.g. a year at full price before recurring kicks in)?
		$initial_price      = isset($subscription->params['override_initial_price']) ? $subscription->params['override_initial_price'] : null;
		$is_initial_payment = isset($requestData['initial_payment']) && ($requestData['initial_payment'] == 1);
		// The discount amount is ONLY calculated for the paid trial period
		$discount_amount = (!is_null($initial_price) && $is_initial_payment) ? $subscription->prediscount_amount - $net_amount : 0.00;

		if (!is_null($initial_price) && $is_initial_payment)
		{
			$note = 'Initial payment with overridden initial price';

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

		$notes = trim($subscription->notes . "\n" . $note, "\n");

		$jDate      = new Date($requestData['next_bill_date']);
		$plusOneDay = new \DateInterval('P1D');
		$jDate->add($plusOneDay);

		$updates = [
			'processor_key'   => $requestData['order_id'],
			'state'           => 'C',
			// Yeah, enabled is 0. Upon saving it gets updated to 1 and triggers the plugins.
			'enabled'         => 0,
			'payment_method'  => $requestData['payment_method'],
			// This effectively marks the transaction as "don't let the user try to pay again"
			'payment_url'     => '',
			'receipt_url'     => $requestData['receipt_url'],
			'gross_amount'    => $gross_amount,
			'tax_amount'      => $tax_amount,
			'net_amount'      => $net_amount,
			'tax_percent'     => $tax_percent,
			'discount_amount' => $discount_amount,
			'fee_amount'      => $fee_amount,
			'notes'           => $notes,
			'publish_up'      => gmdate('Y-m-d H:i:s'),
			'publish_down'    => $jDate->format('Y-m-d H:i:s'),
			'contact_flag'    => 3,
		];

		// Stack this callback's information to the subscription record
		$updates = array_merge($updates, $this->getStackCallbackUpdate($subscription, $requestData));

		// Store the checkout_id
		$updates['params']['checkout_id'] = $requestData['checkout_id'];

		// Save the Paddle subscription ID
		$updates['params']['subscription_id'] = $requestData['subscription_id'];

		// Handle the recurring subscription switchover
		try
		{
			$updates = $this->handleRecurringSubscription($subscription, $updates);
		}
		catch (Exception $e)
		{
			// Worst case scenario, the previous instalment is overwritten. Who cares?
		}

		/**
		 * Save the changes and trigger the necessary plugin events.
		 *
		 * Important: we use _dontNotify to prevent firing the new subscription email on automatic renewals. The client
		 * has already received notification from Paddle about their successful payment.
		 */
		$this->container->platform->importPlugin('akeebasubs');
		$subscription->_dontNotify(true);
		$subscription->save($updates);
		$subscription->_dontNotify(false);
		$this->container->platform->runPlugins('onAKAfterPaymentCallback', [
			$subscription,
		]);

		// Update the country, if necessary
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