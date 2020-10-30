<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use FOF30\Container\Container;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use FOF30\Date\Date;

class plgSystemAsexpirationcontrol extends JPlugin
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
		if (!JComponentHelper::isEnabled('com_akeebasubs'))
		{
			$this->enabled = false;
		}

		if (!is_object($config['params']))
		{
			$config['params'] = new JRegistry($config['params']);
		}

		parent::__construct($subject, $config);

		// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
		if (function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set'))
		{
			if (function_exists('error_reporting'))
			{
				$oldLevel = error_reporting(0);
			}
			$serverTimezone = @date_default_timezone_get();
			if (empty($serverTimezone) || !is_string($serverTimezone))
			{
				$serverTimezone = 'UTC';
			}
			if (function_exists('error_reporting'))
			{
				error_reporting($oldLevel);
			}
			@date_default_timezone_set($serverTimezone);
		}

		// Load the language files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_akeebasubs', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_akeebasubs', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_akeebasubs', JPATH_ADMINISTRATOR, null, true);

		$jlang->load('com_akeebasubs', JPATH_SITE, 'en-GB', true);
		$jlang->load('com_akeebasubs', JPATH_SITE, $jlang->getDefault(), true);
		$jlang->load('com_akeebasubs', JPATH_SITE, null, true);
	}

	/**
	 * Called when Joomla! is booting up and checks for expired subscriptions.
	 */
	public function onAfterInitialise()
	{
		if (!$this->enabled)
		{
			return;
		}

		// Check if we need to run
		if (!$this->doIHaveToRun())
		{
			return;
		}

		// I must load the container to register the component's autoloader
		Container::getInstance('com_akeebasubs');

		$this->onAkeebasubsCronTask('expirationcontrol');
	}

	public function onAkeebasubsCronTask($task, $options = array())
	{
		if (!$this->enabled)
		{
			return null;
		}

		if ($task != 'expirationcontrol')
		{
			return null;
		}

		// Update the last run info
		$this->setLastRunTimestamp();

		// Get today's date
		$jNow = new Date();

		/**
		 * Load a list of subscriptions which are about to expire -- FOF does the rest magically!
		 *
		 * Criteria for subscriptions about to expire:
		 * - Active subscription (enabled = 1)
		 * - publish_down <= now (hence expires_to)
		 */
		/** @var Subscriptions $subsModel */
		$container = Container::getInstance('com_akeebasubs');
		$subsModel = $container->factory->model('Subscriptions')->tmpInstance();
		$subs      = $subsModel
			->enabled(1)
			->expires_to($jNow->toSql())
			->get();

		unset($subs);
		unset($subsModel);

		/**
		 * Load a list of renewed subscriptions not already active -- FOF does the rest magically!
		 *
		 * Criteria for subscriptions which got renewed:
		 * - Inactive subscriptions (enabled = 0)
		 * - Valid payment (payment state = C)
		 * - publish_up <= now (hence publish_upto)
		 */
		/** @var Subscriptions $subsModel */
		$subsModel = $container->factory->model('Subscriptions')->tmpInstance();
		$subs = $subsModel
			->enabled(0)
			->paystate('C')
			->publish_upto($jNow->toSql())
			->get();

		unset($subs);
		unset($subsModel);

		return true;
	}

	/**
	 * Fetches the com_akeebasubs component's parameters as a JRegistry instance
	 *
	 * @return JRegistry The component parameters
	 */
	private function getComponentParameters()
	{
		$component = JComponentHelper::getComponent('com_akeebasubs');

		if ($component->params instanceof JRegistry)
		{
			$cparams = $component->params;
		}
		elseif (!empty($component->params))
		{
			$cparams = new JRegistry($component->params);
		}
		else
		{
			$cparams = new JRegistry('{}');
		}

		return $cparams;
	}

	/**
	 * "Do I have to run?" - the age old question. Let it be answered by checking the
	 * last execution timestamp, stored in the component's configuration.
	 */
	private function doIHaveToRun()
	{
		// Get the component parameters
		$componentParameters      = $this->getComponentParameters();

		// Is scheduling enabled?
		// WARNING: DO NOT USE $componentParameters HERE! THIS IS A PLUGIN PARAMETER NOT A COMPONENT PARAMETER
		$scheduling  = $this->params->get('scheduling', 1);

		if (!$scheduling)
		{
			return false;
		}

		// Find the next execution time (midnight GMT of the next day after the last time we ran the scheduling)
		$lastRunUnix = $componentParameters->get('plg_akeebasubs_asexpirationcontrol_timestamp', 0);
		$dateInfo    = getdate($lastRunUnix);
		$nextRunUnix = mktime(0, 0, 0, $dateInfo['mon'], $dateInfo['mday'], $dateInfo['year']);
		$nextRunUnix += 24 * 3600;

		// Get the current time
		$now = time();

		// have we reached the next execution time?
		return ($now >= $nextRunUnix);
	}

	/**
	 * Saves the timestamp of this plugin's last run
	 */
	private function setLastRunTimestamp()
	{
		$lastRun = time();
		$params  = $this->getComponentParameters();
		$params->set('plg_akeebasubs_asexpirationcontrol_timestamp', $lastRun);

		$db   = Container::getInstance('com_akeebasubs')->db;
		$data = $params->toString();

		$query = $db->getQuery(true)
		            ->update($db->qn('#__extensions'))
		            ->set($db->qn('params') . ' = ' . $db->q($data))
		            ->where($db->qn('element') . ' = ' . $db->q('com_akeebasubs'))
		            ->where($db->qn('type') . ' = ' . $db->q('component'));
		$db->setQuery($query);
		$db->execute();
	}
}
