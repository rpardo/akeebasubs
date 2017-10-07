<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2017 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\View\Subscriptions;

use Akeeba\Subscriptions\Site\Model\Invoices;
use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use JUri;

defined('_JEXEC') or die;

class Xml extends Html
{
	public function onBeforeBrowse()
	{
		/** @var Subscriptions $model */
		$model = $this->getModel();
		$model->with(['juser', 'user', 'level']);

		parent::onBeforeBrowse();

		$this->setLayout('browse.xml');
	}

	public function onAfterBrowse()
	{
		\JFactory::getApplication()->logout();
	}
}