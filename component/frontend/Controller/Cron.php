<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Controller;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\Cron as CronModel;
use Akeeba\Subscriptions\Site\Model\Exception\CronCommandMissing;
use Akeeba\Subscriptions\Site\Model\Exception\CronCommandNotFound;
use Akeeba\Subscriptions\Site\Model\Exception\SecretMismatch;
use Akeeba\Subscriptions\Site\Model\Exception\SecretNotConfigured;
use Exception;
use FOF30\Container\Container;
use FOF30\Controller\Controller;
use FOF30\Controller\Mixin\PredefinedTaskList;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

class Cron extends Controller
{
	use PredefinedTaskList;

	/**
	 * Overridden. Limit the tasks we're allowed to execute.
	 *
	 * @param   Container $container
	 * @param   array     $config
	 */
	public function __construct(Container $container, array $config = array())
	{
		$config['csrfProtection'] = 0;

		parent::__construct($container, $config);

		$this->predefinedTaskList = ['cron'];

		$this->cacheableTasks = [];
	}

	public function cron()
	{
		/** @var CronModel $model */
		$model = $this->getModel();
		$model->log('Starting the CRON job from URL');

		// Get a reference to the application or die trying
		try
		{
			/** @var CMSApplication $app */
			$app = Factory::getApplication();
		}
		catch (\Exception $e)
		{
			$model->log($e->getMessage(), Log::ERROR);
			$model->log('Exit: Cannot instantiate application');
			$this->bellyUp('Internal Server Error', 500);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}

		// Disable caching
		$app->setHeader('Pragma', 'public', true);
		$app->setHeader('Expires', 0, true);
		$app->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$app->setHeader('Cache-Control', 'public', false);
		$app->setHeader('X-Cache-Control', 'False', true);

		// Check the secret key
		$key = $this->input->get->get('secret', '', 'raw');
		$key = $this->input->get->get('key', $key, 'raw');

		$model->log('Checking secret key');

		try
		{
			$model->checkSecret($key);
		}
		catch (SecretMismatch $e)
		{
			$model->log('Provided secret key does not match configuration', Log::ERROR);
			$model->log('Exit: secret key mismatch');
			$this->bellyUp('Forbidden', $e->getCode());

			// Technically unnecessary but helps with static code analysis :)
			return;
		}
		catch (SecretNotConfigured $e)
		{
			$model->log('A secret key has not been configured yet', Log::ERROR);
			$model->log('Exit: secret key not yet configured');
			$this->bellyUp('Service Unavailable', $e->getCode());

			// Technically unnecessary but helps with static code analysis :)
			return;
		}

		$command = $this->input->get->getCmd('command', '');

		// You might ask for a 'cmd' filter but get an array instead
		if (!is_string($command))
		{
			$model->log('The command specified in the URL has an invalid format', Log::ERROR);
			$model->log('Exit: invalid command format');
			$this->bellyUp('Bad Request', 400);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}

		$model->log('Executing the command');

		// Run the command
		try
		{
			$timeLimit = (int) $this->container->params->get('time_limit', 10);
			$success   = $model->run($command, $timeLimit);
		}
		catch (CronCommandMissing $e)
		{
			$model->log('There was no command specified in the URL', Log::ERROR);
			$model->log('Exit: no command');
			$this->bellyUp('Bad Request', 400);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}
		catch (CronCommandNotFound $e)
		{
			$model->log(sprintf('The requested command, “%s”, is not implemented', $command), Log::ERROR);
			$model->log('Exit: unknown command');
			$this->bellyUp('Not Implemented', 501);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}
		catch (Exception $e)
		{
			$model->log(sprintf('An error occurred executing command “%s”', $command), Log::ERROR);
			$model->log($e->getMessage());
			$model->log($e->getFile() . '::' . $e->getLine());

			foreach (explode("\n", $e->getTraceAsString()) as $line)
			{
				$model->log($line);
			}

			$model->log('Exit: error executing command');
			$this->bellyUp('Internal Server Error', 500);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}

		if ($success)
		{
			$model->log('Exit: COMMAND SUCCEEDED');

			$app->setHeader('status', 200);
			$app->sendHeaders();

			echo "OK";

			$app->close(200);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}

		$model->log('Exit: COMMAND FAILED');

		$this->bellyUp('Command failed', 500);
	}

	/**
	 * Exit the script with an error
	 *
	 * @param   string  $reason    The reason we're quitting. Will be included in the HTTP header.
	 * @param   int     $httpCode  HTTP status code, default 500
	 *
	 * @return  void  We actually never return; this is an exit point.
	 * @since   3.2.0
	 */
	private function bellyUp($reason, $httpCode = 500)
	{
		try
		{
			/** @var CMSApplication $app */
			$app = Factory::getApplication();
			$app->setHeader('status', $httpCode);
			$app->setHeader('X-AkeebaSubs-Reason', $reason);
			$app->sendHeaders();
			$app->close($httpCode);
		}
		catch (Exception $e)
		{
			header(sprintf('HTTP/1.1 %u %s', $httpCode, $reason));
			exit ($httpCode);
		}
	}
}
