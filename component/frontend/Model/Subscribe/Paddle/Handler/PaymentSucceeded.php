<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;

use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\FixSubscriptionDate;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\StackCallback;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

/**
 * Handle a successful payment event.
 *
 * @see         https://paddle.com/docs/reference-using-webhooks/#payment_succeeded
 *
 * @since       7.0.0
 */
class PaymentSucceeded implements SubscriptionCallbackHandlerInterface
{
	use FixSubscriptionDate;
	use StackCallback;

	/**
	 * The component's container
	 *
	 * @var   Container
	 * @since 7.0.0
	 */
	protected $container;

	/**
	 * Constructor
	 *
	 * @param Container $container The component container
	 *
	 * @since  7.0.0
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Handle a webhook callback from the payment service provider about a specific subscription
	 *
	 * @param   Subscriptions  $subscription  The subscription the webhook refers to
	 * @param   array          $requestData   The request data minus component, option, view, task
	 *
	 * @return  string|null  Text to include in the callback response page
	 *
	 * @throws  \RuntimeException  In case an error occurs. The exception code will be used as the HTTP status.
	 *
	 * @since  7.0.0
	 */
	public function handleCallback(Subscriptions $subscription, array $requestData): ?string
	{
		// Sanity check
		$isRecurring = isset($subscription->params['recurring_plan_id']) && ($subscription->params['recurring_plan_id'] == $subscription->level->paddle_plan_id);

		if ($isRecurring)
		{
			throw new \RuntimeException('One-off payment notification was issued for a recurring subscription');
		}

		$requestProductId      = $requestData['product_id'];
		$subscriptionProductId = $subscription->level->paddle_product_id;

		if ($requestProductId != $subscriptionProductId)
		{
			throw new \RuntimeException(sprintf('Callback for product ID %u was sent for a subscription entry belonging to a level linked to product ID %u.', $requestProductId, $subscriptionProductId));
		}

		// Calculate the price parameters
		$gross_amount    = (float) $requestData['balance_gross'];
		$tax_amount      = (float) $requestData['balance_tax'];
		$fee_amount      = (float) $requestData['balance_fee'];
		$net_amount      = $gross_amount - $tax_amount;
		$tax_percent     = sprintf('%0.2f', 100.00 * $tax_amount / $net_amount);
		$discount_amount = $subscription->prediscount_amount - $net_amount;

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
		];

		// Stack this callback's information to the subscription record
		$updates = array_merge($updates, $this->getStackCallbackUpdate($subscription, $requestData));

		// Store the checkout_id
		$updates['params']['checkout_id'] = $requestData['checkout_id'];

		// Fix the subscription publish up / down dates
		$updates = $this->fixSubscriptionDates($subscription, $updates);

		// Save the changes and trigger the necessary plugin events
		$this->container->platform->importPlugin('akeebasubs');
		$subscription->save($updates);
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

	/**
	 * Update a user profile field
	 *
	 * @param   int     $user_id        User ID
	 * @param   string  $profile_key    The profile key to update
	 * @param   string  $profile_value  The new profile value
	 * @param   bool    $new            Is this a new record?
	 *
	 * @return  bool  True on success
	 *
	 * @since   7.0.0
	 */
	protected function updateUserProfile(int $user_id, string $profile_key, string $profile_value, bool $new = false): bool
	{
		$db = $this->container->db;

		if ($new)
		{
			$o = (object) [
				'user_id'       => $user_id,
				'profile_key'   => $profile_key,
				'profile_value' => $profile_value,
			];

			return $db->insertObject('#__user_profiles', $o);
		}

		$query = $db->getQuery(true)
			->update($db->qn('#__user_profiles'))
			->set($db->qn('profile_value') . ' = ' . $db->q($profile_value))
			->where($db->qn('user_id') . ' = ' . $db->q($user_id))
			->where($db->qn('profile_key') . ' = ' . $db->q($profile_key));

		return $db->setQuery($query)->execute() !== false;
	}
}