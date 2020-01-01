<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Controller;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Controller\DataController;

class Level extends DataController
{
	/**
	 * Since I have an "id" filter its state is set after editing an item, causing browse issues.
	 */
	protected function onBeforeBrowse()
	{
		$this->getModel()->blacklistFilters(['created_on', 'created_by']);
		$this->getModel()->setState('id', []);
	}
}
