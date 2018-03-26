<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\View\Invoices;

use Akeeba\Subscriptions\Admin\Model\Invoices;

defined('_JEXEC') or die;

class Html extends \FOF30\View\DataView\Html
{
	public function onBeforeRead($tpl = null)
	{
		$this->setPreRender(false);
		$this->setPostRender(false);
		parent::onBeforeRead($tpl);
	}
}
