<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;

use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\FixSubscriptionDate;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

class PaymentSucceeded implements SubscriptionCallbackHandlerInterface
{
	use FixSubscriptionDate;

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
	 * @param Subscriptions $subscription The subscription the webhook refers to
	 * @param array         $requestData  The request data minus component, option, view, task
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
		$tax_percent     = sprintf('%0.2f', $tax_amount / $net_amount);
		$discount_amount = $subscription->prediscount_amount - $net_amount;

		$updates = [
			'processor_key'   => $requestData['order_id'],
			'state'           => 'C',
			// Yeah, enabled is 0. Upon saving it gets updated to 1 and triggers the plugins.
			'enabled'         => 0,
			'payment_method'  => $requestData['payment_method'],
			'receipt_url'     => $requestData['receipt_url'],
			'gross_amount'    => $gross_amount,
			'tax_amount'      => $tax_amount,
			'net_amount'      => $net_amount,
			'tax_percent'     => $tax_percent,
			'discount_amount' => $discount_amount,
			'fee_amount'      => $fee_amount,
		];

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