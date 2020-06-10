<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\View\Subscriptions;

use Akeeba\Subscriptions\Site\Model\MySubs;
use Akeeba\Subscriptions\Site\Model\Subscriptions;

defined('_JEXEC') or die;

class Html extends \FOF30\View\DataView\Html
{
	public $returnURL = '';

	public $displayInformation = [];

	protected function onBeforeBrowse()
	{
		// Eager loading of relations
		/** @var Subscriptions $model */
		$model = $this->getModel();
		$model->with(['level']);

		parent::onBeforeBrowse();

		// Assemble the information we need to display subscriptions in the frontend
		if (empty($this->items))
		{
			return;
		}

		/** @var MySubs $mySubsModel */
		$mySubsModel = $this->container->factory->model('MySubs', [
			'items' => $this->items,
			'user'  => $this->container->platform->getUser(),
		]);

		$this->displayInformation = $mySubsModel->getDisplayData();
	}
}
