<?php
/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Admin\Helper\Debug;
use Akeeba\Subscriptions\Site\Model\Exception\CronCommandMissing;
use Akeeba\Subscriptions\Site\Model\Exception\CronCommandNotFound;
use Akeeba\Subscriptions\Site\Model\Exception\SecretMismatch;
use Akeeba\Subscriptions\Site\Model\Exception\SecretNotConfigured;
use FOF30\Model\Model;
use Joomla\CMS\Crypt\Crypt;
use Joomla\CMS\Log\Log;

class Cron extends Model
{
	/**
	 * Akeeba Subscriptions CRON log filename
	 *
	 * @since 7.1.2
	 */
	const logFilename = 'akeebasubs_cron.php';

	/**
	 * Have I already installed a Joomla logger method for the 'akeebasubs.cron' context?
	 *
	 * @var   bool
	 * @since 7.1.2
	 */
	private $loggerInstalled = false;

	/**
	 * Record an entry in the Akeeba Subscriptions CRON log file.
	 *
	 * The log file is in the site's log directory. Its name is in the logFilename constant in this class.
	 *
	 * @param   string  $entry
	 * @param   int     $level
	 *
	 * @return  void
	 * @since   7.1.2
	 */
	public function log($entry, $level = Log::DEBUG)
	{
		// Install a logger for Akeeba Subscriptions CRON jobs if necessary
		if (!$this->loggerInstalled)
		{
			Debug::registerFileLogger('akeebasubs.cron');

			$this->loggerInstalled = true;
		}

		Log::add($entry, $level, 'akeebasubs.cron');
	}

	/**
	 * Checks if the provided secret key matches the configuration.
	 *
	 * The comparison is performed in a timing safe manner. An exception is thrown if the key does not match or if no
	 * secret key has been configured yet
	 *
	 * @param   string  $key  The secret key to check.
	 *
	 * @return  void
	 *
	 * @throws  SecretNotConfigured
	 * @throws  SecretMismatch
	 *
	 * @since   7.1.2
	 */
	public function checkSecret($key)
	{
		$configuredSecret = trim($this->container->params->get('secret', ''));
		$key              = trim($key);

		if (empty($configuredSecret))
		{
			throw new SecretNotConfigured();
		}

		if (!Crypt::timingSafeCompare($configuredSecret, $key))
		{
			throw new SecretMismatch();
		}
	}

	/**
	 * Tries to execute the given command. Returns true if the command executed successfully.
	 *
	 * @param   string  $command    The command to execute. The filtered command name is written back to this var.
	 * @param   int     $timeLimit  Time limit in seconds. 0 means "no limit", is implemented as a 86400s (24h) limit.
	 *
	 * @return  bool  True on successful execution
	 *
	 * @since   7.1.2
	 */
	public function run(&$command, $timeLimit = 0)
	{
		// Perform more rigorous filtering of the command name
		$command = (string) preg_replace('/[^A-Z0-9_]/i', '', $command);
		$command = strtolower($command);

		if (empty($command))
		{
			throw new CronCommandMissing();
		}

		// Process the time limit
		if ($timeLimit <= 0)
		{
			// No limit is implemented as a ridiculously high 86400 second (24 hour) time limit
			$timeLimit = 86400;
		}
		else
		{
			// In any other case the time limit must be between 1 and 600 seconds
			$timeLimit = max($timeLimit, 1);
			$timeLimit = min($timeLimit, 600);
		}

		$this->log(sprintf('Preparing to execute command “%s”', $command));

		// Make sure the language files are loaded
		$language = $this->container->platform->getLanguage();
		$thisPath = $this->container->platform->isBackend() ? JPATH_ADMINISTRATOR : JPATH_ROOT;
		$altPath  = !$this->container->platform->isBackend() ? JPATH_ADMINISTRATOR : JPATH_ROOT;
		$language->load($this->container->componentName, $altPath, null, true);
		$language->load($this->container->componentName, $thisPath, null, true);

		// Run the command
		$this->container->platform->importPlugin('system');
		$this->container->platform->importPlugin('akeebasubs');
		$result = $this->container->platform->runPlugins('onAkeebasubsCronTask', [
			$command,
			[
				'time_limit' => $timeLimit,
			],
		]);

		$commandFound = array_reduce($result, function ($carry, $item) {
			if ($carry)
			{
				return true;
			}

			return !is_null($item);
		}, false);

		if (!$commandFound)
		{
			throw new CronCommandNotFound();
		}

		$finalResult = array_reduce($result, function ($carry, $item) {
			if (is_null($item))
			{
				return $carry;
			}

			return $item;
		}, false);

		return (bool) $finalResult;
	}
}