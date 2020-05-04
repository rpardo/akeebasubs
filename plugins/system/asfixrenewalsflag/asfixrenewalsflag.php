<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use FOF30\Container\Container;
use Akeeba\Subscriptions\Admin\Model\Levels;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use FOF30\Date\Date;

class plgSystemAsfixrenewalsflag extends JPlugin
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
	}

	/**
	 * Called when Joomla! is booting up and checks the subscriptions statuses.
	 * If a subscription is close to expiration, it sends out an email to the user.
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

		$this->onAkeebasubsCronTask('fixrenewalsflag');
	}

	public function onAkeebasubsCronTask($task, $options = array())
	{
		if (!$this->enabled)
		{
			return;
		}

		if ($task != 'fixrenewalsflag')
		{
			return;
		}

		$defaultOptions = array(
			'time_limit' => 2,
		);

		$options = array_merge($defaultOptions, $options);

		\JLog::add("Starting Fixing the Contact flag on renewals- Time limit {$options['time_limit']} seconds", \JLog::DEBUG, "akeebasubs.cron.fixrenewalsflag");

		// Get today's date
		$jNow = new Date();
		$now  = $jNow->toUnix();

		// Start the clock!
		$clockStart = microtime(true);

		// Update the last run info before doing the actual work
		$this->setLastRunTimestamp();

		$jTo   = new Date($now + (60 * 24 * 3600));

		/** @var Subscriptions $subsModel */
		$container = Container::getInstance('com_akeebasubs');
		$subsModel = $container->factory->model('Subscriptions')->tmpInstance();

		// Let's fetch all the expiring subs in the next 60 days, one query for each contact flag
		$next_expires0 = $subsModel->getClone()
			->contact_flag(0)
			->enabled(1)
			->expires_from($jNow->toSql())
			->expires_to($jTo->toSql())
			->get(true);

		$next_expires1 = $subsModel->getClone()
			->contact_flag(1)
			->enabled(1)
			->expires_from($jNow->toSql())
			->expires_to($jTo->toSql())
			->get(true);

		$next_expires2 = $subsModel->getClone()
			->contact_flag(2)
			->enabled(1)
			->expires_from($jNow->toSql())
			->expires_to($jTo->toSql())
			->get(true);

		// If there are no subscriptions, bail out
		$subs0count = is_object($next_expires0) ? $next_expires0->count() : 0;
		$subs1count = is_object($next_expires1) ? $next_expires1->count() : 0;
		$subs2count = is_object($next_expires2) ? $next_expires2->count() : 0;

		if (($subs0count + $subs1count + $subs2count) == 0)
		{
			return;
		}

		foreach (array($next_expires0, $next_expires1, $next_expires2) as $subs)
		{
			/** @var Subscriptions $sub */
			foreach ($subs as $sub)
			{
				// This should never happen, but better be safe than sorry
				if ($sub->contact_flag == 3)
				{
					continue;
				}

				// Given the user and the level, load similar subscriptions with start date after this subscription's expiry date
				$subsModel = $container->factory->model('Subscriptions')->tmpInstance();

				/**
				 * Renewal subscriptions won't be enabled at this point (since they have not reached the publish_up
				 * date yet) however their payment status MUST BE completed. If we do not look for the payment state
				 * a failed renewal would be considered as a "valid one".
				 */
				$subsModel
					->enabled(0)
					->paystate('C')
					->user_id($sub->user_id)
					->level($sub->akeebasubs_level_id)
					->publish_up($sub->publish_down);

				$renewals = $subsModel->get(true);

				// No renewals? There's nothing to do, then
				if (!$renewals->count())
				{
					continue;
				}

				\JLog::add("Fixing #" . $sub->akeebasubs_subscription_id . ', renewals found. Updating contact flag to 3', \JLog::INFO, "akeebasubs.cron.fixrenewalsflag");

				// The user has already renewed. Don't send him an email; just update the row
				$subsModel->getClone()
					->find($sub->akeebasubs_subscription_id)
					->save([
						'contact_flag' => 3
					]);

				// Timeout check -- Only if we did make a modification!
				$clockNow = microtime(true);
				$elapsed  = $clockNow - $clockStart;

				if (($options['time_limit'] > 0) && ($elapsed > $options['time_limit']))
				{
					return;
				}
			}
		}
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
		$lastRunUnix = $componentParameters->get('plg_akeebasubs_asfixrenewalsflag_timestamp', 0);
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
	private function setLastRunTimestamp($timestamp = null)
	{
		$lastRun = is_null($timestamp) ? time() : $timestamp;
		$params  = $this->getComponentParameters();
		$params->set('plg_akeebasubs_asfixrenewalsflag_timestamp', $lastRun);

		$db = Container::getInstance('com_akeebasubs')->db;

		$data  = $params->toString('JSON');
		$query = $db->getQuery(true)
		            ->update($db->qn('#__extensions'))
		            ->set($db->qn('params') . ' = ' . $db->q($data))
		            ->where($db->qn('element') . ' = ' . $db->q('com_akeebasubs'))
		            ->where($db->qn('type') . ' = ' . $db->q('component'));
		$db->setQuery($query);
		$db->execute();
	}
}
