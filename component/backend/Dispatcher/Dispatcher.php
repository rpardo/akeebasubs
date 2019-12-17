<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Dispatcher;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Dispatcher\Mixin\ViewAliases;

class Dispatcher extends \FOF30\Dispatcher\Dispatcher
{
	use ViewAliases {
		onBeforeDispatch as onBeforeDispatchViewAliases;
	}

	/** @var   string  The name of the default view, in case none is specified */
	public $defaultView = 'ControlPanel';

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->viewNameAliases = [
			'cpanel'             => 'ControlPanel',
		];
	}

	public function onBeforeDispatch()
	{
		$this->onBeforeDispatchViewAliases();

		@include_once(JPATH_ADMINISTRATOR . '/components/com_akeebasubs/version.php');

		if (!defined('AKEEBASUBS_VERSION'))
		{
			define('AKEEBASUBS_VERSION', 'dev');
			define('AKEEBASUBS_DATE', date('Y-m-d'));
		}

		// Renderer options (0=none, 1=frontend, 2=backend, 3=both)
		$useFEF   = in_array($this->container->params->get('load_fef', 3), [2, 3]);
		$fefReset = $useFEF && in_array($this->container->params->get('fef_reset', 3), [2, 3]);

		if (!$useFEF)
		{
			$this->container->rendererClass = '\\FOF30\\Render\\Joomla3';
		}

		$darkMode  = $this->container->params->get('dark_mode_backend', -1);
		$customCss = 'media://com_akeebasubs/css/backend.css';

		$this->container->renderer->setOptions([
			'load_fef'      => $useFEF,
			'fef_reset'     => $fefReset,
			'fef_dark'      => $useFEF ? $darkMode : 0,
			// Render submenus as drop-down navigation bars powered by Bootstrap
			'linkbar_style' => 'classic',
		]);

		// Load common CSS and JavaScript
		\JHtml::_('jquery.framework');
		$this->container->template->addCSS('media://com_akeebasubs/css/backend.css', $this->container->mediaVersion);

		if ($useFEF && ($darkMode != 0))
		{
			$this->container->template->addCSS('media://com_akeebasubs/css/backend_dark.css', $this->container->mediaVersion);
		}

		$this->container->template->addJS('media://com_akeebasubs/js/backend.js', false, false, $this->container->mediaVersion);
	}
}
