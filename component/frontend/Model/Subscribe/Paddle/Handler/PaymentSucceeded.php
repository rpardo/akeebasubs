<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
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
	private $container;

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
		if (!isset($updates['params']))
		{
			$updates['params'] = [];
		}

		$updates['params']['checkout_id'] = $requestData['checkout_id'];

		// Fix the subscription publish up / down dates
		$updates = $this->fixSubscriptionDates($subscription, $updates);

		// Save the changes and trigger the necessary plugin events
		$this->container->platform->importPlugin('akeebasubs');
		$subscription->save($updates);
		$this->container->platform->runPlugins('onAKAfterPaymentCallback', [
			$subscription,
		]);

		// Done. No output to be sent (returns a 200 OK with an empty body)
		return null;
	}
}