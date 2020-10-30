<?php
/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Helper\Debug;
use Akeeba\Subscriptions\Admin\Model\Cron;
use Akeeba\Subscriptions\Site\Model\Exception\CronCommandMissing;
use Akeeba\Subscriptions\Site\Model\Exception\CronCommandNotFound;
use FOF30\Container\Container;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

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

class AkeebasubsCron extends FOFApplicationCLI
{
	protected $template = null;

	protected $_language_filter = false;

	/**
	 * Returns the application Router object. Necessary for fetching emails.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  Router|null  A JRouter object
	 * @since   7.1.2
	 */
	public function getRouter($name = null, array $options = [])
	{
		try
		{
			return Router::getInstance('site', $options);
		}
		catch (Exception $e)
		{
			return null;
		}
	}

	public function getMenu($name = null, $options = [])
	{
		return AbstractMenu::getInstance($name, $options);
	}

	/**
	 * Something strange happened and the Factory wants to enqueue a system message. Let's log it for further inspection
	 *
	 * @param $msg
	 * @param $type
	 */
	public function enqueueMessage($msg, $type)
	{
		parent::enqueueMessage($msg, $type);

		Log::add($msg, Log::NOTICE, 'akeebasubs.cron');
	}

	public function getClientId()
	{
		return 0;
	}

	/**
	 * The main entry point of the application
	 *
	 * @return void
	 * @since  7.1.2
	 */
	public function doExecute()
	{
		$this->out('Akeeba Subscriptions -- CRON Script');
		$this->out('Copyright 2010-' . gmdate('Y') . ' Akeeba Ltd');
		$this->out(str_repeat('=', 79));

		// Load FOF
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			die('FOF 3.0 is not installed');
		}

		// Load version defines
		$path = JPATH_ADMINISTRATOR . '/components/com_akeebasubs/version.php';

		@include_once $path;

		if (!defined('AKEEBASUBS_VERSION'))
		{
			define('AKEEBASUBS_VERSION', 'dev');
		}
		if (!defined('AKEEBASUBS_DATE'))
		{
			define('AKEEBASUBS_DATE', date('Y-m-d'));
		}

		// Work around some misconfigured servers which print out notices
		if (function_exists('error_reporting'))
		{
			$oldLevel = error_reporting(0);
		}

		$container = Container::getInstance('com_akeebasubs');

		if (function_exists('error_reporting'))
		{
			error_reporting($oldLevel);
		}

		// Initializes the CLI session handler. Prevents Joomla from crashing e.g. when fetching the current user.
		$container->session->get('foobar');

		// Initialize routing from the CLI
		$container = Container::getInstance('com_akeebasubs');
		$siteURL   = $container->params->get('siteurl', 'http://www.example.com');

		$this->initCliRouting($siteURL);

		$debug = $this->input->getBool('debug', Debug::getJoomlaDebug());

		if ($debug)
		{
			$this->set('debug', 1);
		}

		if (!defined('JDEBUG'))
		{
			define('JDEBUG', $debug ? 1 : 0);
		}

		$verbose = $this->input->getBool('verbose', $debug);

		if ($verbose)
		{
			Debug::registerCLIOutputLogger('akeebasubs.cron');
			Debug::registerCLIOutputLogger('akeebasubs.emails');
		}

		// Get the command to execute
		$command = $this->input->getCmd('command');

		// Perform more rigorous filtering of the command name
		$command = (string) preg_replace('/[^A-Z0-9_]/i', '', $command);
		$command = strtolower($command);

		if ($verbose)
		{
			$this->out(sprintf(
				'Current memory usage : %s',
				$this->memUsage()
			));
			$this->out();
		}

		if (!empty($command) && !$verbose)
		{
			$this->out(sprintf('Executing “%s” CRON task.', $command));
			$this->out('Tip: Use the --verbose command line switch to follow its progress.');
		}

		$startTime = microtime(true);

		// Run the command
		/** @var Cron $model */
		$model = $container->factory->model('Cron')->tmpInstance();

		$model->log('Starting the CRON job from CLI');

		// Allow plugins to load under CLI
		$container->platform->setAllowPluginsInCli(true);

		$model->log('Executing the command');

		try
		{
			// No time limit for CLI CRON jobs
			$success = $model->run($command, 0);

			$model->log('Exit: CRON execution finished');
		}
		catch (CronCommandMissing $e)
		{
			$model->log('There was no command specified in the command line', Log::ERROR);
			$model->log('Exit: no command');

			$this->out('You did not specify a command in the command line.');
			$this->out('Try adding --command=CRONTaskName where CRONTaskName is the name of the CRON task you want to execute');

			$this->close(1);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}
		catch (CronCommandNotFound $e)
		{
			$model->log(sprintf('The requested command, “%s”, is not implemented', $command), Log::ERROR);
			$model->log('Exit: unknown command');

			$this->out(sprintf('The requested command, “%s”, is not implemented', $command));
			$this->out('Did you forget to enable a plugin?');

			$this->close(2);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}
		catch (Exception $e)
		{
			$model->log(sprintf('An error occurred executing command “%s”', $command), Log::ERROR);
			$model->log($e->getMessage());
			$model->log($e->getFile() . '::' . $e->getLine());

			if (!$verbose)
			{
				$this->out(sprintf('An error occurred executing command “%s”', $command));
				$this->out($e->getMessage());
				$this->out($e->getFile() . '::' . $e->getLine());
			}

			foreach (explode("\n", $e->getTraceAsString()) as $line)
			{
				$model->log($line);

				if (!$verbose)
				{
					$this->out($line);
				}
			}

			$model->log('Exit: error executing command');

			$this->close(255);

			// Technically unnecessary but helps with static code analysis :)
			return;
		}

		$this->out();
		$this->out(sprintf(
			'%s -- CRON job finished after approximately %s',
			$success ? 'SUCCESS' : 'FAIL',
			$this->timeAgo($startTime, time(), '', false)
		));

		if ($verbose)
		{
			$this->out();
			$this->out(sprintf(
				'Current memory usage : %s',
				$this->memUsage()
			));
			$this->out(sprintf(
				'Peak memory usage    : %s',
				$this->peakMemUsage()
			));
		}

	}

	/**
	 * Check the client interface by name.
	 *
	 * @param   string  $identifier  String identifier for the application interface
	 *
	 * @return  boolean  True if this application is of the given type client interface.
	 *
	 * @since   3.3.1
	 */
	public function isClient($identifier)
	{
		return $identifier === 'cli';
	}

	/**
	 * Is admin interface?
	 *
	 * @return  boolean  True if this application is administrator.
	 *
	 * @since   3.3.1
	 */
	public function isAdmin()
	{
		return $this->isClient('administrator');
	}

	/**
	 * Is site interface?
	 *
	 * @return  boolean  True if this application is site.
	 *
	 * @since   3.3.1
	 */
	public function isSite()
	{
		return $this->isClient('site');
	}

	protected function initCliRouting($siteURL)
	{
		// Set up the base site URL in JUri
		$uri                    = Uri::getInstance($siteURL);
		$_SERVER['HTTP_HOST']   = $uri->toString(['host', 'port']);
		$_SERVER['REQUEST_URI'] = $uri->getPath();

		$refClass     = new ReflectionClass(Uri::class);
		$refInstances = $refClass->getProperty('instances');
		$refInstances->setAccessible(true);
		$instances           = $refInstances->getValue();
		$instances['SERVER'] = $uri;
		$refInstances->setValue($instances);

		$base = [
			'prefix' => $uri->toString(['scheme', 'host', 'port']),
			'path'   => rtrim($uri->toString(['path']), '/\\'),
		];

		$refBase = $refClass->getProperty('base');
		$refBase->setAccessible(true);
		$refBase->setValue($base);

		// Set up the SEF mode in the router
		if (version_compare(JVERSION, '3.999.999', 'le'))
		{
			$this->getRouter()->setMode($this->get('sef', 0));
		}
	}

	/**
	 * Gets the name of the current template.
	 *
	 * @param   boolean  $params  True to return the template parameters
	 *
	 * @return  string  The name of the template.
	 *
	 * @since   3.2
	 * @throws  \InvalidArgumentException
	 */
	public function getTemplate($params = false)
	{
		if (is_object($this->template))
		{
			if (!file_exists(JPATH_THEMES . '/' . $this->template->template . '/index.php'))
			{
				throw new \InvalidArgumentException(\JText::sprintf('JERROR_COULD_NOT_FIND_TEMPLATE', $this->template->template));
			}

			if ($params)
			{
				return $this->template;
			}

			return $this->template->template;
		}

		// Get the id of the active menu item
		$menu = $this->getMenu();
		$item = $menu->getActive();

		if (!$item)
		{
			$item = $menu->getItem($this->input->getInt('Itemid', null));
		}

		$id = 0;

		if (is_object($item))
		{
			// Valid item retrieved
			$id = $item->template_style_id;
		}

		$tid = $this->input->getUint('templateStyle', 0);

		if (is_numeric($tid) && (int) $tid > 0)
		{
			$id = (int) $tid;
		}

		/** @var \Joomla\CMS\Cache\CacheController $cache */
		$cache = Factory::getCache('com_templates', '');

		$tag = '';

		if ($this->_language_filter)
		{
			$tag = $this->getLanguage()->getTag();
		}

		// Load styles
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id, home, template, s.params')
			->from('#__template_styles as s')
			->where('s.client_id = 0')
			->where('e.enabled = 1')
			->join('LEFT', '#__extensions as e ON e.element=s.template AND e.type=' . $db->quote('template') . ' AND e.client_id=s.client_id');

		$db->setQuery($query);
		$templates = $db->loadObjectList('id');

		foreach ($templates as &$template)
		{
			// Create home element
			if ($template->home == 1 && !isset($template_home) || $this->_language_filter && $template->home == $tag)
			{
				$template_home = clone $template;
			}

			$template->params = new Registry($template->params);
		}

		// Unset the $template reference to the last $templates[n] item cycled in the foreach above to avoid editing it later
		unset($template);

		// Add home element, after loop to avoid double execution
		if (isset($template_home))
		{
			$template_home->params = new Registry($template_home->params);
			$templates[0] = $template_home;
		}

		if (isset($templates[$id]))
		{
			$template = $templates[$id];
		}
		else
		{
			$template = $templates[0];
		}

		// Need to filter the default value as well
		$template->template = InputFilter::getInstance()->clean($template->template, 'cmd');

		// Fallback template
		if (!file_exists(JPATH_THEMES . '/' . $template->template . '/index.php'))
		{
			$this->enqueueMessage(\JText::_('JERROR_ALERTNOTEMPLATE'), 'error');

			// Try to find data for 'beez3' template
			$original_tmpl = $template->template;

			foreach ($templates as $tmpl)
			{
				if ($tmpl->template === 'beez3')
				{
					$template = $tmpl;
					break;
				}
			}

			// Check, the data were found and if template really exists
			if (!file_exists(JPATH_THEMES . '/' . $template->template . '/index.php'))
			{
				throw new \InvalidArgumentException(\JText::sprintf('JERROR_COULD_NOT_FIND_TEMPLATE', $original_tmpl));
			}
		}

		// Cache the result
		$this->template = $template;

		if ($params)
		{
			return $template;
		}

		return $template->template;
	}

}

FOFApplicationCLI::getInstance('AkeebasubsCron')->execute();