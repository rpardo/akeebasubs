<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Controller;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Admin\Controller\Mixin;
use Akeeba\Subscriptions\Admin\Helper\UserLogin;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use Akeeba\Subscriptions\Site\View\Message\Html;
use FOF30\Container\Container;
use FOF30\Controller\Controller;
use FOF30\Date\Date;
use FOF30\View\Exception\AccessForbidden;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;

class Message extends Controller
{
	use Mixin\PredefinedTaskList;

	/**
	 * Overridden. Limit the tasks we're allowed to execute.
	 *
	 * @param   Container $container
	 * @param   array     $config
	 */
	public function __construct(Container $container, array $config = array())
	{
		// We need to use the Subscriptions model
		$config['modelName'] = 'Subscriptions';

		// Disable token checks (CSRF protection) since this view is called by the payment services, outside our user session
		$config['csrfProtection'] = false;

		parent::__construct($container, $config);

		$this->predefinedTaskList = ['show'];

		// Disable caching. This view may change any time based on the subscription status!
		$this->cacheParams    = [];
		$this->cacheableTasks = [];
	}

	/**
	 * Runs before executing the "show" task. It selects the correct template based on the subscription information.
	 *
	 * @return  void
	 *
	 * @throws  \Exception  On access error
	 */
	public function onBeforeShow()
	{
		// Load the subscription referenced in the URL
		$subid = $this->input->getInt('subid', 0);

		/** @var Subscriptions $subscription */
		$subscription = $this->getModel('Subscriptions')->tmpInstance();
		$subscription->findOrFail($subid);

		// Make sure the user is allowed access to this view (already logged in or provided an activation or access code)
		$user = $this->container->platform->getUser();

		if (!$user->authorise('core.admin') && !$this->validateUser($subscription))
		{
			throw new AccessForbidden();
		}

		// Reload the user privileges. Prevents Joomla user group snafus.
		Access::clearStatics();

		// Set the layout in the input and the object property based on the subscription's status
		switch ($subscription->getFieldValue('state', 'N'))
		{
			case 'N':
				$layout = 'new';
				break;

			case 'P':
				$layout = 'pending';
				break;

			case 'C':
				$layout = 'complete';

				$now = time();
				$then = (new Date($subscription->publish_up))->getTimestamp();

				if (!$subscription->enabled)
				{
					$layout = ($then > $now) ? 'waiting' : 'expired';
				}

				break;

			case 'X':
				$layout = 'canceled';
				break;
		}

		$this->layout = $layout;

		// Assign data to the view
		$this->getView()->subscription = $subscription;
	}

	public function show()
	{
		parent::display(false);
	}

	/**
	 * Makes sure that the client has access to the subscription page.
	 *
	 * @param   Subscriptions  $subscription  The subscription we will be granting access to
	 *
	 * @return  bool
	 *
	 * @since   7.0.0
	 *
	 * @throws  \Exception  If something fails
	 */
	protected function validateUser(Subscriptions $subscription): bool
	{
		$user = $this->container->platform->getUser();

		// Am I logged in?
		if (!$user->guest)
		{
			// If I am already logged in I must be the same user as the one who owns the subscription
			return $subscription->user_id == $user->id;
		}

		// Do I have a blocked user and an activation code?
		$subUser    = $this->container->platform->getUser($subscription->user_id);
		$activation = $this->input->getString('activation', null);

		if ($subUser->block && ($activation === $subUser->activation))
		{
			// Unblock the user
			$subUser->block = 0;
			$subUser->activation = null;
			$subUser->save();

			// Log in the user
			UserLogin::loginUser($subUser->id, true);

			return true;
		}

		// Do I have a direct access authorization code?
		$secret = Factory::getConfig()->get('secret', '');
		$authCode = md5($subscription->getId() . $subscription->user_id . $secret);
		$requestCode = $this->input->getString('authorization', null);

		return $requestCode === $authCode;
	}
}
