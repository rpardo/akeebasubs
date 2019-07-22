<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Controller;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Controller\DataController;
use FOF30\Controller\Exception\ItemNotFound;
use FOF30\View\Exception\AccessForbidden;

class CreditNote extends DataController
{
	public function __construct(Container $container, array $config = array())
	{
		parent::__construct($container, $config);

		$this->cacheableTasks = [];
	}

	public function onBeforeRead()
	{
		// Load the model
		/** @var \Akeeba\Subscriptions\Admin\Model\CreditNotes $model */
		$model = $this->getModel();

		// If there is no record loaded, try loading a record based on the id passed in the input object
		if (!$model->getId())
		{
			$ids = $this->getIDsFromRequest($model, true);

			if ($model->getId() != reset($ids))
			{
				$key = strtoupper($this->container->componentName . '_ERR_' . $model->getName() . '_NOTFOUND');
				throw new ItemNotFound(\JText::_($key), 404);
			}
		}

		// Check that this is the item's owner or an administrator
		$user = $this->container->platform->getUser();
		$sub  = $model->subscription;

		if (!$this->checkACL('core.manage') && ($sub->user_id != $user->id))
		{
			throw new AccessForbidden;
		}
	}
}
