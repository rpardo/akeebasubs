<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;


use Akeeba\Subscriptions\Admin\Helper\Message;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;
use FOF30\View\Exception\AccessForbidden;
use Joomla\CMS\HTML\HTMLHelper;

class Fulfillment implements SubscriptionCallbackHandlerInterface
{
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
	 *
	 * @throws \Exception
	 */
	public function handleCallback(Subscriptions $subscription, array $requestData): ?string
	{
		// I need the subscription's user to be my effective user when processing the message
		$needsLogin = $this->container->platform->getUser()->id != $subscription->user_id;

		if ($needsLogin)
		{
			$this->loginUser($subscription->user_id);
		}

		// Prepare the message
		$message = Message::processLanguage($subscription->level->ordertext);
		$message = Message::processSubscriptionTags($message, $subscription);
		$message = HTMLHelper::_('content.prepare', $message);

		// Logout the temporary logged in user (if we have such a user)
		if ($needsLogin)
		{
			$this->logoutUser();
		}

		return $message;
	}

	/**
	 * Logs in a different user than the current one.
	 *
	 * @param   int  $userId
	 *
	 * @throws  \Exception
	 *
	 * @since   7.0.0
	 */
	public function loginUser(int $userId): void
	{
		// This line returns an empty JUser object
		$newUserObject = new \JUser();

		// This line FORCE RELOADS the user record.
		$newUserObject->load($userId);

		if (($newUserObject->id != $userId))
		{
			throw new AccessForbidden;
		}

		// Mark the user as logged in
		$newUserObject->block = 0;
		$newUserObject->set('guest', 0);

		// Register the needed session variables
		$this->container->platform->setSessionVar('user', $newUserObject);

		$db = $this->container->db;

		// Check to see the the session already exists.
		$app = \JFactory::getApplication();
		$app->checkSession();

		// Update the user related fields for the Joomla sessions table.
		$query = $db->getQuery(true)
			->update($db->qn('#__session'))
			->set(array(
				$db->qn('guest') . ' = ' . $db->q($newUserObject->get('guest')),
				$db->qn('username') . ' = ' . $db->q($newUserObject->get('username')),
				$db->qn('userid') . ' = ' . (int)$newUserObject->get('id')
			))->where($db->qn('session_id') . ' = ' . $db->q(\JFactory::getSession()->getId()));
		$db->setQuery($query);
		$db->execute();

		// Hit the user last visit field
		$newUserObject->setLastVisit();
	}

	public function logoutUser()
	{
		$userId = $this->container->platform->getUser()->id;
		$newUserObject = new \JUser();
		$newUserObject->load($userId);

		$app = \JFactory::getApplication();

		// Perform the log out.
		$app->logout();

		if ($newUserObject->block)
		{
			$newUserObject->lastvisitDate = $this->container->db->getNullDate();
			$newUserObject->save();
		}
	}
}