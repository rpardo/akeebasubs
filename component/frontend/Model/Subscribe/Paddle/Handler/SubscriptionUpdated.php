<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;


use Akeeba\Subscriptions\Admin\Helper\Message;
use Akeeba\Subscriptions\Admin\Helper\UserLogin;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\StackCallback;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;
use FOF30\Date\Date;
use FOF30\View\Exception\AccessForbidden;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Handle a subscription update event
 *
 * @see         https://paddle.com/docs/subscriptions-event-reference/#subscription_updated
 *
 * @since       7.0.0
 */
class SubscriptionUpdated implements SubscriptionCallbackHandlerInterface
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
	 * @param   Container  $container  The component container
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
	 *
	 * @throws \Exception
	 */
	public function handleCallback(Subscriptions $subscription, array $requestData): ?string
	{
		switch ($requestData['status'])
		{
			// The price was updated OR we went from trialing to active
			case 'active':
			// Trial mode started
			case 'trialing':
				break;

			// Automatic charge failed -- CANNOT HAPPEN IN THIS EVENT
			case 'past_due':
				// Subscription cancelled -- CANNOT HAPPEN IN THIS EVENT
			case 'deleted':
				throw new \RuntimeException(sprintf('Invalid subscription status “%s”', $requestData['status']), 403);
				break;

			default:
				throw new \RuntimeException('Invalid or no subscription status', 403);
				break;
		}

		// Stack the callback data to the subscription
		$updates = $this->getStackCallbackUpdate($subscription, $requestData);

		// Store the subscription update and cancel URLs
		$updates['update_url'] = $requestData['update_url'];
		$updates['cancel_url'] = $requestData['cancel_url'];

		// Do not send emails about automatically recurring subscriptions
		$updates['contact_flag'] = 3;

		// Subscription plan update
		if (isset($requestData['subscription_plan_id']))
		{
			$updates['params']['recurring_plan_id'] = $requestData['subscription_plan_id'];
		}

		$jDate      = new Date($requestData['next_bill_date']);
		$plusOneDay = new \DateInterval('P1D');
		$jDate->add($plusOneDay);
		$updates['publish_down'] = $jDate->format('Y-m-d H:i:s');

		// TODO If there is a $requestData['subscription_plan_id'] go through handleRecurringSubscription before save().

		$subscription->save($updates);

		return null;
	}
}