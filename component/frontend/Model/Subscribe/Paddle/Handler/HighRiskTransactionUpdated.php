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
 * Handle a notification of a high risk transaction
 *
 * @see         https://paddle.com/docs/reference-using-webhooks/#high_risk_transaction_updated
 *
 * @since       7.0.0
 */
class HighRiskTransactionUpdated implements SubscriptionCallbackHandlerInterface
{
	use StackCallback;
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
		if (!in_array($requestData['status'], ['accepted', 'rejected']))
		{
			return null;
		}

		// Create a message
		$eventTime = $requestData['event_time'];
		$caseId    = $requestData['case_id'];
		$riskScore = (float) $requestData['risk_score'];
		$message   = sprintf("High risk transaction %s on %s. Case ID %s, risk score %0.2f",
			$requestData['status'], $eventTime, $caseId, $riskScore);

		switch ($requestData['status'])
		{
			case 'accepted':
				$updates = [
					'state' => 'C',
					'notes' => $subscription->notes . "\n" . $message
				];

				break;

			case 'rejected':
			default:
				$updates = [
					'state' => 'X',
					'cancellation_reason' => 'risk',
					'notes' => $subscription->notes . "\n" . $message
				];

				break;
		}

		// Stack this callback's information to the subscription record
		$updates = $this->fixSubscriptionDates($subscription, $updates);

		// Stack this callback's information to the subscription record
		$updates = array_merge($updates, $this->getStackCallbackUpdate($subscription, $requestData));

		$updates['params'] = array_merge($updates['params'], [
			'risk_case_id'            => $requestData['case_id'],
			'risk_case_created'       => $requestData['created_at'],
			'risk_score'              => $requestData['risk_score'],
			'paddle_customer_user_id' => $requestData['customer_user_id'],
		]);

		// Save the subscription record changes
		$subscription->save($updates);

		// Done. No output to be sent (returns a 200 OK with an empty body)
		return null;
	}
}