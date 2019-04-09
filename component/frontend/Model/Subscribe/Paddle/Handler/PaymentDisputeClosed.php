<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;

use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\StackCallback;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

/**
 * Handle a notification of a payment dispute
 *
 * @see         https://paddle.com/docs/reference-using-webhooks/#payment_dispute_closed
 *
 * @since       7.0.0
 */
class PaymentDisputeClosed implements SubscriptionCallbackHandlerInterface
{
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
		// Sanity check
		if ($requestData['status'] != 'closed')
		{
			return null;
		}

		// If a refund event has been already processed (state = 'X') we MUST NOT reactivate the subscription.
		if ($subscription->getFieldValue('state') != 'P')
		{
			return null;
		}

		// No refund event has been processed and nobody has manually cancelled the subscription. Reinstate it.
		$updates = [
			'state' => 'C',
			'notes' => $subscription->notes . "\n" . sprintf('Payment Dispute closed on %s; subscription reinstated without modifying its validity dates.', $requestData['event_time'])
		];

		$subscription->save($updates);

		// Done. No output to be sent (returns a 200 OK with an empty body)
		return null;
	}
}