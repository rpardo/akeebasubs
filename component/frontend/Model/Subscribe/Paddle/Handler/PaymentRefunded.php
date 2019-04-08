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


			$subscription->save([
				// Cancel the subscription
				'state' => 'X',
				// Update the notes
				'notes' => $subscription->notes . "\n" . $message,

			]);

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
		$tax_percent     = sprintf('%0.2f', $tax_amount / $net_amount);
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