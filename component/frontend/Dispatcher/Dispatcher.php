<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Dispatcher;

defined('_JEXEC') or die;

class Dispatcher extends \FOF30\Dispatcher\Dispatcher
{
	/** @var   string  The name of the default view, in case none is specified */
	public $defaultView = 'Levels';

	public function onBeforeDispatch()
	{
		@include_once(JPATH_ADMINISTRATOR . '/components/com_akeebasubs/version.php');

		if (!defined('AKEEBASUBS_VERSION'))
		{
			define('AKEEBASUBS_VERSION', 'dev');
			define('AKEEBASUBS_DATE', date('Y-m-d'));
		}

		// Renderer options (0=none, 1=frontend, 2=backend, 3=both)
		$useFEF   = $this->container->params->get('load_fef', 3);
		$fefReset = $this->container->params->get('fef_reset', 3);

		$this->container->renderer->setOption('load_fef', in_array($useFEF, [1,3]));
		$this->container->renderer->setOption('fef_reset', in_array($fefReset, [1,3]));
		$this->container->renderer->setOption('linkbar_style', 'classic');

		// Load common CSS JavaScript
		\JHtml::_('jquery.framework');
		$this->container->template->addCSS('media://com_akeebasubs/css/frontend.css', $this->container->mediaVersion);

		// Translate view names from Akeeba Subscriptions 1.x, 2.x, 3.x and 4.x
		$this->translateOldViewNames();
	}

	/**
	 * Translates the view name of an old version of Akeeba Subscriptions to the new names used in Akeeba Subscriptions
	 * 5.x and later.
	 */
	protected function translateOldViewNames()
	{
		// Map Akeeba Subscriptions 1.x-4.x view name to Akeeba Subscriptions 5.x+ view name
		$map = [
			'callbacks'     => 'Callbacks',
			'callback'      => 'Callbacks',
			'cron'          => 'Cron',
			'crons'         => 'Cron',
			'invoice'       => 'Invoices',
			'invoices'      => 'Invoices',
			'level'         => 'Level',
			'levels'        => 'Levels',
			'messages'      => 'Messages',
			'message'       => 'Messages',
			'subscribes'    => 'Subscribe',
			'subscribe'     => 'Subscribe',
			'subscription'  => 'Subscriptions',
			'subscriptions' => 'Subscriptions',
			'userinfos'     => 'UserInfo',
			'userinfo'      => 'UserInfo',
			'Userinfo'      => 'UserInfo',
			'validates'     => 'Validate',
			'validate'      => 'Validate',
		];

		$oldViewName = strtolower($this->view);

		if (isset($map[$oldViewName]))
		{
			$this->view = $map[$oldViewName];
		}
	}
}
