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
use FOF30\View\Exception\AccessForbidden;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Handle a new subscription created event
 *
 * @see         https://paddle.com/docs/subscriptions-event-reference/#subscription_created
 *
 * @since       7.0.0
 */
class SubscriptionCreated implements SubscriptionCallbackHandlerInterface
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
		// Stack the callback data to the subscription
		$updates = $this->getStackCallbackUpdate($subscription, $requestData);

		// Store the subscription update and cancel URLs
		$updates['update_url'] = $requestData['update_url'];
		$updates['cancel_url'] = $requestData['cancel_url'];
		// Do not send emails about automatically recurring subscriptions
		$updates['contact_flag'] = 3;
		// Save the Paddle subscription ID
		$updates['params']['subscription_id'] = $requestData['subscription_id'];

		$subscription->save($updates);

		return null;
	}
}