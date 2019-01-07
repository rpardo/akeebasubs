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

		if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_akeebasubs/version.php'))
		{
			define('AKEEBASUBS_VERSION', 'dev');
			define('AKEEBASUBS_DATE', date('Y-m-d'));
		}

		// Renderer options (0=none, 1=frontend, 2=backend, 3=both)
		$useFEF   = $this->container->params->get('load_fef', 3);
		$fefReset = $this->container->params->get('fef_reset', 3);

		$this->container->renderer->setOption('load_fef', in_array($useFEF, [2,3]));
		$this->container->renderer->setOption('fef_reset', in_array($fefReset, [2,3]));
		$this->container->renderer->setOption('linkbar_style', 'classic');

		// Load common CSS and JavaScript
		\JHtml::_('jquery.framework');
		$this->container->template->addCSS('media://com_akeebasubs/css/backend.css', $this->container->mediaVersion);
		$this->container->template->addJS('media://com_akeebasubs/js/backend.js', false, false, $this->container->mediaVersion);
	}
}
