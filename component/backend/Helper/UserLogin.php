<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

defined('_JEXEC') or die;

use Exception;
use FOF30\Container\Container;
use FOF30\View\Exception\AccessForbidden;
use JAuthentication;
use JAuthenticationResponse;
use JLoader;
use JLog;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Authentication\AuthenticationResponse;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\User\User;
use RuntimeException;

/**
 * Handles user login and logout, bypassing Joomla.
 *
 * @since       7.0.0
 */
class UserLogin
{
	protected static $container;

	protected static function getContainer(): Container
	{
		if (is_null(self::$container))
		{
			self::$container = Container::getInstance('com_akeebasubs');
		}

		return self::$container;
	}

	/**
	 * Logs in a user, by default without going through the Joomla user plugins
	 *
	 * @param   int   $userId  The ID of the user to log in
	 * @param   bool  $full    Full login? If true, we will go through Joomla's user plugins.
	 *
	 * @throws  \Exception
	 *
	 * @since   7.0.0
	 */
	public static function loginUser(int $userId, bool $full = false): void
	{
		if ($full)
		{
			self::loginUserComplete($userId);

			return;
		}

		// This line returns an empty JUser object
		$newUserObject = new User();

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
		self::getContainer()->platform->setSessionVar('user', $newUserObject);

		// Force Joomla to reload user privileges
		Access::clearStatics();

		// Check to see the the session already exists.
		$db  = self::getContainer()->db;
		$app = Factory::getApplication();
		$app->checkSession();

		// Update the user related fields for the Joomla sessions table.
		$query = $db->getQuery(true)
			->update($db->qn('#__session'))
			->set([
				$db->qn('guest') . ' = ' . $db->q($newUserObject->get('guest')),
				$db->qn('username') . ' = ' . $db->q($newUserObject->get('username')),
				$db->qn('userid') . ' = ' . (int) $newUserObject->get('id'),
			])->where($db->qn('session_id') . ' = ' . $db->q(\JFactory::getSession()->getId()));
		$db->setQuery($query);
		$db->execute();

		// Hit the user last visit field
		$newUserObject->setLastVisit();
	}

	/**
	 * Logs in a user to the site, bypassing the authentication plugins.
	 *
	 * @param   int              $userId  The user ID to log in
	 *
	 * @throws  Exception
	 */
	private static function loginUserComplete($userId)
	{
		// Trick the class auto-loader into loading the necessary classes
		JLoader::import('joomla.user.authentication');
		JLoader::import('joomla.plugin.helper');
		JLoader::import('joomla.user.helper');

		class_exists('JAuthentication', true);
		class_exists('Joomla\\CMS\\Authentication\\Authentication', true);

		// Fake a successful login message
		$app     = Factory::getApplication();
		$isAdmin = $app->isClient('administrator');
		$user    = Factory::getUser($userId);

		// Does the user account have a pending activation?
		if (!empty($user->activation))
		{
			throw new RuntimeException(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));
		}

		// Is the user account blocked?
		if ($user->block)
		{
			throw new RuntimeException(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));
		}

		if (class_exists('Joomla\CMS\Authentication\Authentication'))
		{
			$statusSuccess = Authentication::STATUS_SUCCESS;
		}
		else
		{
			$statusSuccess = JAuthentication::STATUS_SUCCESS;
		}

		$response                = self::getAuthenticationResponseObject();
		$response->status        = $statusSuccess;
		$response->username      = $user->username;
		$response->fullname      = $user->name;
		$response->error_message = '';
		$response->language      = $user->getParam('language');
		$response->type          = 'Joomla';

		if ($isAdmin)
		{
			$response->language = $user->getParam('admin_language');
		}

		/**
		 * Set up the login options.
		 *
		 * The 'remember' element forces the use of the Remember Me feature when logging in with social media, as the
		 * users would expect.
		 *
		 * The 'action' element is actually required by plg_user_joomla. It is the core ACL action the logged in user
		 * must be allowed for the login to succeed. Please note that front-end and back-end logins use a different
		 * action. This allows us to provide the social login button on both front- and back-end and be sure that if a
		 * used with no backend access tries to use it to log in Joomla! will just slap him with an error message about
		 * insufficient privileges - the same thing that'd happen if you tried to use your front-end only username and
		 * password in a back-end login form.
		 */
		$options = [
			'remember' => true,
			'action'   => 'core.login.site',
		];

		if ($isAdmin)
		{
			$options['action'] = 'core.login.admin';
		}

		// Run the user plugins. They CAN block login by returning boolean false and setting $response->error_message.
		self::getContainer()->platform->importPlugin('user');
		$results = self::getContainer()->platform->runPlugins('onUserLogin', [(array) $response, $options]);

		// If there is no boolean FALSE result from any plugin the login is successful.
		if (in_array(false, $results, true) == false)
		{
			// Set the user in the session, letting Joomla! know that we are logged in.
			self::getContainer()->platform->setSessionVar('user', $user);

			// Trigger the onUserAfterLogin event
			$options['user']         = $user;
			$options['responseType'] = $response->type;

			// The user is successfully logged in. Run the after login events
			self::getContainer()->platform->runPlugins('onUserAfterLogin', [$options]);

			return;
		}

		// If we are here the plugins marked a login failure. Trigger the onUserLoginFailure Event.
		self::getContainer()->platform->runPlugins('onUserLoginFailure', [(array) $response]);

		// Log the failure
		Log::add($response->error_message, Log::WARNING, 'jerror');

		// Throw an exception to let the caller know that the login failed
		throw new RuntimeException($response->error_message);
	}

	/**
	 * Returns a (blank) Joomla! authentication response
	 *
	 * @return  JAuthenticationResponse|AuthenticationResponse
	 */
	public static function getAuthenticationResponseObject()
	{
		// Force the class auto-loader to load the JAuthentication class
		JLoader::import('joomla.user.authentication');
		class_exists('JAuthentication', true);

		if (class_exists('Joomla\\CMS\\Authentication\\AuthenticationResponse'))
		{
			return new AuthenticationResponse();
		}

		return new JAuthenticationResponse();
	}

	/**
	 * Log out a user
	 *
	 * @since  7.0.0
	 */
	public static function logoutUser(): void
	{
		$userId = self::getContainer()->platform->getUser()->id;
		$newUserObject = new User();
		$newUserObject->load($userId);

		try
		{
			$app = Factory::getApplication();
		}
		catch (\Exception $e)
		{
			return;
		}

		// Perform the log out.
		$app->logout();

		if ($newUserObject->block)
		{
			$newUserObject->lastvisitDate = self::getContainer()->db->getNullDate();
			$newUserObject->save();
		}
	}
}