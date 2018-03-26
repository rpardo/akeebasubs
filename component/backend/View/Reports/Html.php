<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\View\Reports;

// Protect from unauthorized access
defined('_JEXEC') or die();

class Html extends \FOF30\View\DataView\Html
{
	public function onBeforeRenewals()
	{
		$this->onBeforeBrowse();
	}

	public function onBeforeMissinginvoice()
	{
		$this->onBeforeBrowse();
	}
}
