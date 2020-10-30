<?php
/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

defined('_JEXEC') or die;

use Exception;
use JConfig;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Log\LogEntry;

abstract class Debug
{
	private static $registeredLoggers = [];

	/**
	 * Get Joomla's debug flag
	 *
	 * @return  bool
	 *
	 * @since   3.2.0
	 */
	public static function getJoomlaDebug()
	{
		// If the JDEBUG constant is defined return its value cast as a boolean
		if (defined('JDEBUG'))
		{
			return (bool) JDEBUG;
		}

		// Joomla 3 - Go through the Factory to get the configuration value
		try
		{
			return (bool) (Factory::getConfig()->get('debug', 0));
		}
		catch (Exception $e)
		{
			// Fall through
		}

		// Joomla 3 & 4 – go through the application object to get the application configuration value
		try
		{
			$app = Factory::getApplication();

			if (method_exists($app, 'get'))
			{
				return (bool) ($app->get('debug', 0));
			}
		}
		catch (Exception $e)
		{
			// Fall through
		}

		// Fallback for Joomla 3 & 4: instantiate JConfig directly
		if (class_exists('JConfig'))
		{
			$config = new JConfig();

			if (property_exists($config, 'debug'))
			{
				return (bool) $config->debug;
			}
		}

		return false;
	}

	/**
	 * Register a file logger for the given context if we have not already done so.
	 *
	 * If no file is specified a log file will be created, named after the context. For example, the context 'akeebasubs.cron'
	 * is logged to the file 'akeebasubs_cron.php' in Joomla's configured logs directory.
	 *
	 * The minimum log level to write to the file is determined by Joomla's debug flag. If you have enabled Site Debug
	 * the log level is JLog::All which log everything, including debug information. If Site Debug is disabled the
	 * log level is JLog::INFO which logs everything BUT debug information.
	 *
	 * @param   string       $context
	 * @param   string|null  $file
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public static function registerFileLogger($context, $file = null)
	{
		// Make sure we are not double-registering a logger
		$sig = md5($context . '.file');

		if (in_array($sig, self::$registeredLoggers))
		{
			return;
		}

		self::$registeredLoggers[] = $sig;

		/**
		 * If no file is specified we will create a filename based on the context.
		 *
		 * For example the context 'akeebasubs.cron' results in the log filename 'akeebasubs_cron.php'
		 */
		if (is_null($file))
		{
			$filter          = InputFilter::getInstance();
			$filteredContext = $filter->clean($context, 'cmd');
			$file            = str_replace('.', '_', $filteredContext) . '.php';
		}

		// Register the file logger
		$logLevel = self::getJoomlaDebug() ? Log::ALL : Log::INFO;

		Log::addLogger(['text_file' => $file], $logLevel, [$context]);
	}

	/**
	 * Register a CLI output logger for the given context
	 *
	 * If no callback is specified we will use the default callback which writes the log message to the output, either
	 * using the current CLI app's out() method or, if this is not available, a simple echo.
	 *
	 * The minimum log level to write to the file is determined by Joomla's debug flag. If you have enabled Site Debug
	 * the log level is JLog::All which log everything, including debug information. If Site Debug is disabled the
	 * log level is JLog::INFO which logs everything BUT debug information.
	 *
	 * @param   string         $context
	 * @param   callable|null  $callback
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public static function registerCLIOutputLogger($context, callable $callback = null)
	{
		// Make sure we are not double-registering a logger
		$sig = md5($context . '.file');

		if (in_array($sig, self::$registeredLoggers))
		{
			return;
		}

		self::$registeredLoggers[] = $sig;

		// If no callback was provided we will use our default one
		if (empty($callback) || !is_callable($callback))
		{
			try
			{
				$app = Factory::getApplication();
			}
			catch (Exception $e)
			{
				$app = null;
			}

			if (is_object($app) && !method_exists($app, 'out'))
			{
				$app = null;
			}

			$callback = function (LogEntry $entry) use ($app) {
				if (is_null($app))
				{
					echo $entry->message . "\n";

					return;
				}

				$app->out($entry->message);
			};
		}

		// Register the CLI output logger
		$logLevel = self::getJoomlaDebug() ? Log::ALL : Log::INFO;

		Log::addLogger([
			'logger'   => 'callback',
			'callback' => $callback,
		], $logLevel, [$context]);

	}
}