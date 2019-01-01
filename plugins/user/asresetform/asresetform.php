<?php

/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
defined('_JEXEC') or die();

JLoader::import('joomla.plugin.plugin');

use FOF30\Container\Container;
use Akeeba\Subscriptions\Admin\Model\Users;

class plgUserAsresetform extends JPlugin
{
	/**
	 * Should this plugin be allowed to run? True if FOF can be loaded and the Akeeba Subscriptions component is enabled
	 *
	 * @var  bool
	 */
	private $enabled = true;

	/**
	 * Public constructor. Overridden to load the language strings.
	 */
	public function __construct(& $subject, $config = array())
	{
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			$this->enabled = false;
		}

		// Do not run if Akeeba Subscriptions is not enabled
		JLoader::import('joomla.application.component.helper');

		if (!JComponentHelper::isEnabled('com_akeebasubs'))
		{
			$this->enabled = false;
		}

		if (!is_object($config['params']))
		{
			JLoader::import('joomla.registry.registry');
			$config['params'] = new JRegistry($config['params']);
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Reset the subscription form after the user logs in
	 */
	public function onUserLogin($response, $options)
	{
		if (!$this->enabled)
		{
			return true;
		}

		$container = Container::getInstance('com_akeebasubs');
		$userid    = JUserHelper::getUserId($response['username']);
		$juser     = $container->platform->getUser($userid);

		/** @var Users $user */
		$user = $container->factory->model('Users')->tmpInstance();
		$user->find(['user_id' => $juser->id]);

		// Mhm... the user was not found inside Akeeba Subscription, better stop here
		if (!$user->akeebasubs_user_id)
		{
			return true;
		}

		/**
		 * This part of the code is only executed for users who already have an akeebasubs_user_id, i.e. they are
		 * already subscribers. This deals with the three possible cases I can think of:
		 *
		 * 1. Existing subscriber. It would be dumb filling in the form when you are already a subscriber. Most likely
		 *    these people are brought to the subscription page before logging in because they clicked on a renewal
		 *    link in the email notifying them of an imminent subscription expiration. We want them to have their form
		 *    pre-filled and this is what this plugin does.
		 *
		 * 2. Existing user who is not a subscriber. They are very likely to start filling in the form before they log
		 *    in. We don't reset their form. The only problem in this case is that they have to re-enter their email
		 *    address and username if they didn't already. This is very slightly annoying. However, scenario #2 is far
		 *    less likely to occur than #1 so this plugin still makes sense _for our site_.
		 *
		 * 3. New user account created outside Akeeba Subs, e.g. user registration form, clicked on the "Already have
		 *    an account" and then created a new user account, account created with Akeeba SocialLogin etc. This is the
		 *    same as case #2.
		 *
		 * 4. A new user account create in Akeeba Subscriptions. They don't log in until _after_ their subscription is
		 *    active, therefore it's the same as case #1.
		 */
		$container->platform->setSessionVar('forcereset', true, 'com_akeebasubs');

		return true;
	}
}
