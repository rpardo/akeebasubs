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
use FOF30\View\Exception\AccessForbidden;
use Joomla\CMS\HTML\HTMLHelper;

class Fulfillment implements SubscriptionCallbackHandlerInterface
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
		// I need the subscription's user to be my effective user when processing the message
		$needsLogin = $this->container->platform->getUser()->id != $subscription->user_id;

		if ($needsLogin)
		{
			UserLogin::loginUser($subscription->user_id);
		}

		// Prepare the message
		$message = Message::processLanguage($subscription->level->ordertext);
		$message = Message::processSubscriptionTags($message, $subscription);
		$message = HTMLHelper::_('content.prepare', $message);

		// Logout the temporary logged in user (if we have such a user)
		if ($needsLogin)
		{
			UserLogin::logoutUser();
		}

		// Stack the callback data to the subscription
		$updates = $this->getStackCallbackUpdate($subscription, $requestData);

		if ($updates['params'] != $subscription->params)
		{
			$subscription->_dontNotify(true);
			$subscription->save($updates);
		}

		return $message;
	}
}