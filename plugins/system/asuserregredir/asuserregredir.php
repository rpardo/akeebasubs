<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Container\Container;

defined('_JEXEC') or die();

class plgSystemAsuserregredir extends JPlugin
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

	public function onAfterRoute()
	{
		if (!$this->enabled)
		{
			return;
		}

		// Only run in the front-end
		$container = Container::getInstance('com_akeebasubs');

		if (!$container->platform->isFrontend())
		{
			return;
		}

		$input  = JFactory::getApplication()->input;
		$option = $input->getCmd('option');
		$view   = $input->getCmd('view');

		// Only run on user registration task
		if (($option != 'com_users') || ($view != 'registration'))
		{
			return;
		}

		$default_url = JRoute::_('index.php?option=com_akeebasubs');

		$url     = $this->params->get('url', $default_url);
		$message = $this->params->get('message', '');

		$url     = trim($url);
		$message = trim($message);

		if (empty($url))
		{
			$url = $default_url;
		}

		$container->platform->redirect($url, 303, $message);
	}
}
