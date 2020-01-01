<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;

use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\StackCallback;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

/**
 * Handle a full or partial refund event.
 *
 * @see         https://paddle.com/docs/reference-using-webhooks/#payment_refunded
 *
 * @since       7.0.0
 */
class PaymentRefunded implements SubscriptionCallbackHandlerInterface
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
		/**
		 * Before processing the callback I need to check that the order_id matches.
		 *
		 * Practical example why this matters.
		 *
		 * User purchased subscription #12345. However, he accidentally paid twice. He now has two payments:
		 * First (old) payment I need to refund: 987654-123098
		 * Second payment I need to keep:        987654-123123
		 *
		 * The subscription record #12345 has payment_key = 987654-123123.
		 *
		 * What happens if I refund order ID 987654-123098 from Paddle?
		 *
		 * Paddle sends me a refund event with 'passthrough' = 12345 and order_id = 987654-123098.
		 *
		 * The Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\CallbackHandler::handleCallback() method pulls the
		 * record based on the 'passthrough' variable (subscription ID). Therefore the PaymentRefunded callback is
		 * called and will cancel the subscription.
		 *
		 * BUT! I only refunded one of the two payments, even making sure it is the payment that is NOT recorded in the
		 * database. Therefore the subscription MUST NOT be canceled. How do I do that? By checking the order_id.
		 *
		 * If I check the order_id I see that the request says 987654-123098 but my database has 987654-123123. They do
		 * not match, therefore I can terminate execution of this callback, meaning my subscription is not canceled
		 * which is what I wanted to do after all.
		 */
		if (trim($requestData['order_id'] ?? '') != trim($subscription->processor_key ?? ''))
		{
			return null;
		}

		/**
		 * Case A. Full refund.
		 *
		 * In this case the client gets all of their money back. This is typically the result of a chargeback or the
		 * amicable resolution of a dispute. Therefore we need to cancel their subscription and add a note about it.
		 *
		 * In the unlikely event we want them to remain subscribers we can manually re-enable their subscription from
		 * the component's backend. In this unlikely case we can manually notify the user, e.g. by email, that they will
		 * receive automated emails about the subscription cancellation and they need to ignore them.
		 */
		if ($requestData['refund_type'] == 'full')
		{
			$message = sprintf('Full refund of %0.2f %s on %s GMT. Subscription canceled automatically.',
				$requestData['balance_gross_refund'],
				$requestData['balance_currency'],
				gmdate('Y-m-d H:i:s'));


			$updates = [
				// Cancel the subscription
				'state'               => 'X',
				// Set the cancellation reason
				'cancellation_reason' => 'refund',
				// Update the notes
				'notes'               => $subscription->notes . "\n" . $message,
			];

			// Stack this callback's information to the subscription record
			$updates = array_merge($updates, $this->getStackCallbackUpdate($subscription, $requestData));

			$subscription->save($updates);

			return null;
		}

		/**
		 * Case B & C. Partial or VAT-only refund.
		 *
		 * A VAT only refund is rather simple. It gets rid of the VAT and increases our earnings since we pay a smaller
		 * fee to Paddle (fees are calculated on the gross amount).
		 *
		 * A partial refund is, essentially, a post-payment discount to the user. Therefore we need to update all the
		 * pricing information stored about that subscription record.
		 *
		 * In either case we update the stored pricing information and add a note about the partial / VAT refund.
		 */
		$qualifier = ($requestData['refund_type'] == 'vat') ? 'Sales tax (VAT)' : 'Partial';
		$message   = sprintf('%s refund of %0.2f %s on %s GMT. No change in subscription status.',
			$qualifier,
			$requestData['balance_gross_refund'],
			$requestData['balance_currency'],
			gmdate('Y-m-d H:i:s'));

		$rGross = (float) $requestData['balance_gross_refund'];
		$rTax   = (float) $requestData['balance_tax_refund'];
		$rFee   = (float) $requestData['balance_fee_refund'];

		$gross_amount    = $subscription->gross_amount - $rGross;
		$tax_amount      = $subscription->tax_amount - $rTax;
		$fee_amount      = $subscription->fee_amount - $rFee;
		$net_amount      = $gross_amount - $tax_amount;
		$tax_percent     = sprintf('%0.2f', 100.00 * $tax_amount / $net_amount);
		$discount_amount = $subscription->prediscount_amount - $net_amount;

		$updates = [
			// Update the prices
			'gross_amount'    => $gross_amount,
			'tax_amount'      => $tax_amount,
			'net_amount'      => $net_amount,
			'tax_percent'     => $tax_percent,
			'discount_amount' => $discount_amount,
			'fee_amount'      => $fee_amount,
			// Update the notes
			'notes'           => $subscription->notes . "\n" . $message,
		];

		// Stack this callback's information to the subscription record
		$updates = array_merge($updates, $this->getStackCallbackUpdate($subscription, $requestData));

		// Save the changes and trigger the necessary plugin events
		$subscription->save($updates);

		// Done. No output to be sent (returns a 200 OK with an empty body)
		return null;
	}
}