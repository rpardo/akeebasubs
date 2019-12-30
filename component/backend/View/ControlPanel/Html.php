<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\View\ControlPanel;

use Akeeba\Subscriptions\Admin\Model\ControlPanel;
use JComponentHelper;
use JFactory;
use JText;

defined('_JEXEC') or die;

class Html extends \FOF30\View\DataView\Html
{
	public $hasGeoIPPlugin;
	public $akeebaCommonDatePHP;
	public $akeebaCommonDateObsolescence;

	protected function onBeforeMain($tpl = null)
	{
		/** @var ControlPanel $model */
		$model = $this->getModel();

		$this->hasGeoIPPlugin = $model->hasGeoIPPlugin();

		$this->akeebaCommonDatePHP = $this->container->platform->getDate('2015-08-14 00:00:00', 'GMT')->format(JText::_('DATE_FORMAT_LC1'));
		$this->akeebaCommonDateObsolescence = $this->container->platform->getDate('2016-05-14 00:00:00', 'GMT')->format(JText::_('DATE_FORMAT_LC1'));
	}
}
