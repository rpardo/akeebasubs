<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Re-run the integrations for the specified users
 *
 * --source=SOURCE_TYPE where SOURCE_TYPE is one of:
 * * all   All Joomla users
 * * active   All users with active subscriptions
 * * inactive   All users with inactive subscriptions
 * * latest (default)  Users whose subscription became active or expired within the last day
 *
 * --for-real              Apply changes to the database.
 */

use FOF30\Container\Container;

// Enable Joomla's debug mode
define('JDEBUG', 1);

// Boilerplate -- START
define('_JEXEC', 1);

foreach ([__DIR__, getcwd()] as $curdir)
{
	if (file_exists($curdir . '/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/defines.php';

		break;
	}

	if (file_exists($curdir . '/../includes/defines.php'))
	{
		define('JPATH_BASE', realpath($curdir . '/..'));
		require_once $curdir . '/../includes/defines.php';

		break;
	}
}

defined('JPATH_LIBRARIES') || die ('This script must be placed in or run from the cli folder of your site.');

require_once JPATH_LIBRARIES . '/fof30/Cli/Application.php';
// Boilerplate -- END

/**
 * A simplistic exception handler
 *
 * @param   Throwable  $e
 *
 * @since   7.1.1
 */
function akeebasubsCliExceptionHandler(Throwable $e)
{
	$exceptionType = get_class($e);
	echo <<< END

================================================================================
Exception
================================================================================

Exception Type: $exceptionType
Code:           {$e->getCode()} 
Message:        {$e->getMessage()}
File:           {$e->getFile()}
Line:           {$e->getLine()}
Call stack:

{$e->getTraceAsString()}

END;

	$code = $e->getCode();

	if (!$code)
	{
		$code = 255;
	}

	exit($code);
}

set_exception_handler('akeebasubsCliExceptionHandler');

class AkeebasubsRunIntegrations extends FOFApplicationCLI
{
	/**
	 * Should I apply changes automatically, for real?
	 *
	 * @var   bool
	 * @since 7.1.1
	 */
	private $forReal = false;

	protected function doExecute()
	{
		// Get the Akeeba Subscriptions container
		$container = Container::getInstance('com_akeebasubs');

		require_once $container->backEndPath . '/version.php';

		$this->out(sprintf('Akeeba Subscriptions %s', AKEEBASUBS_VERSION));
		$this->out(sprintf('Copyright (c)2010-%s Nicholas K. Dionysopoulos / Akeeba Ltd', substr(AKEEBASUBS_DATE, 0, 4)));
		$this->out('');

		// Am I doing this for real?
		$this->forReal = $this->input->getBool('for-real', false);

		$strategyType = $this->input->get('source', 'latest');
		$strategyType = in_array($strategyType, ['all', 'active', 'inactive', 'latest']) ? $strategyType : 'latest';

		switch ($strategyType)
		{
			case 'all':
				$this->out('Processing all users');
				$userIDs = $this->getAllUsers($container);
				break;

			case 'active':
				$this->out('Processing users with active subscriptions');
				$userIDs = $this->getActiveUsers($container);
				break;

			case 'inactive':
				$this->out('Processing users with expired subscriptions');
				$userIDs = $this->getInactiveUsers($container);
				break;

			case 'latest':
			default:
				$this->out('Processing users with subscription changes in the last day');
				$userIDs = $this->getLatestUsers($container);
				break;
		}

		$this->out();

		if (empty($userIDs))
		{
			$this->out('No users matching the criteria found.');

			return;
		}

		$this->out(sprintf('Found %d user(s).', count($userIDs)));

		// Import plugins
		$container->platform->setAllowPluginsInCli(true);
		$container->platform->importPlugin('akeebasubs');

		array_walk($userIDs, function (?int $userID) use ($container) {
			if (is_null($userID) || ($userID <= 0))
			{
				return;
			}

			$user = $container->platform->getUser($userID);

			if ($user->id != $userID)
			{
				$this->out(sprintf('(!) Invalid user ID %d', $userID));

				return;
			}

			$this->out(sprintf('%s <%s> (%d)', $user->name, $user->email, $user->id));

			if ($this->forReal)
			{
				$container->platform->runPlugins('onAKUserRefresh', [$userID]);
			}
		});
	}

	private function getAllUsers(Container $container): array
	{
		$db = $container->db;
		$q  = $db->getQuery(true)
			->select([
				$db->qn('user_id'),
			])->from($db->qn('#__akeebasubs_subscriptions'))
			->where($db->qn('user_id') . ' > 0')
			->group([
				$db->qn('user_id'),
			]);

		return $db->setQuery($q)->loadColumn() ?? [];
	}

	private function getActiveUsers(Container $container): array
	{
		$db = $container->db;
		$q  = $db->getQuery(true)
			->select([
				$db->qn('user_id'),
			])->from($db->qn('#__akeebasubs_subscriptions'))
			->where($db->qn('user_id') . ' > 0')
			->where($db->qn('enabled') . ' = 1')
			->group([
				$db->qn('user_id'),
			]);

		return $db->setQuery($q)->loadColumn() ?? [];
	}

	private function getInactiveUsers(Container $container)
	{
		$db = $container->db;
		$q  = $db->getQuery(true)
			->select([
				$db->qn('user_id'),
			])->from($db->qn('#__akeebasubs_subscriptions'))
			->where($db->qn('user_id') . ' > 0')
			->where($db->qn('enabled') . ' = 0')
			->group([
				$db->qn('user_id'),
			]);

		return $db->setQuery($q)->loadColumn() ?? [];
	}

	private function getLatestUsers(Container $container)
	{
		$db = $container->db;

		// We are going to get something like:
		//
		// SELECT user_id FROM foo_akeebasubs_subscriptions
		// WHERE
		//	(publish_up >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND publish_up <= CURDATE() AND enabled = 1)
		// OR
		//	(publish_down >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND publish_down <= CURDATE() AND enabled = 0)
		// AND
		//   user_id > 0
		// GROUP BY user_id

		$enabledConditions = [
			$db->qn('publish_up') . ' >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)',
			$db->qn('publish_up') . ' <= CURDATE()',
			$db->qn('enabled') . ' = 1',
		];

		$disabledConditions = [
			$db->qn('publish_down') . ' >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)',
			$db->qn('publish_down') . ' <= CURDATE()',
			$db->qn('enabled') . ' = 0',
		];

		$complexWhere = '(' . implode(' AND ', $enabledConditions) . ') OR (' . implode(' AND ', $disabledConditions) . ')';

		$q  = $db->getQuery(true)
			->select([
				$db->qn('user_id'),
			])->from($db->qn('#__akeebasubs_subscriptions'))
			->where($complexWhere)
			->where($db->qn('user_id') . ' > 0')
			->group([
				$db->qn('user_id'),
			]);

		return $db->setQuery($q)->loadColumn() ?? [];
	}
}

FOFApplicationCLI::getInstance('AkeebasubsRunIntegrations')->execute();