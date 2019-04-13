<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
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
 * Handle a subscription cancellation event
 *
 * @see         https://paddle.com/docs/subscriptions-event-reference/#subscription_cancelled
 *
 * @since       7.0.0
 */
class SubscriptionCancelled implements SubscriptionCallbackHandlerInterface
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
		if ($requestData['status'] != 'deleted')
		{
			throw new \RuntimeException('Invalid or no subscription status', 403);
		}

		// Stack the callback data to the subscription
		$updates = $this->getStackCallbackUpdate($subscription, $requestData);

		/**
		 * The only bit that's useful to us is the effective cancellation date which MAY be in the future, if the user
		 * has already paid for this billing cycle. Therefore, this is our publish_down date. Note that the
		 * contact_flag in this case is still 3 because I only need to notify the subscriber about their subscription's
		 * expiration, NOT email them a coupon to resubscribe (they asked very clearly to not be subscribers any more).
		 */
		$updates['publish_down'] = $requestData['cancellation_effective_date'];

		$subscription->save($updates);

		return null;
	}
}