<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Controller\Mixin;

use FOF30\Container\Container;

defined('_JEXEC') or die;

trait PersonalInformation
{
	/**
	 * Runs before executing a task in the controller
	 *
	 * @param   string  $task  The task to execute
	 *
	 * @return  bool
	 */
	public function onBeforeExecute($task)
	{
		/** @var Container $container */
		$container = $this->container;

		// Only apply the check in the backend. In the frontend I have different kinds of access control.
		if (!$container->platform->isBackend())
		{
			return true;
		}

		/** @var \JUser $user */
		$user = $container->platform->getUser();

		if (!$user->authorise('akeebasubs.pii', 'com_akeebasubs'))
		{
			throw new \RuntimeException(\JText::_('COM_AKEEBASUBS_COMMON_NO_ACL_PII_FOR_YOU'), 403);
		}

		return true;
	}
}