<?php
/*
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Controller;

use Akeeba\Subscriptions\Admin\Controller\SubscriptionStatistics as BackendSubscriptionStatistics;
use FOF30\View\Exception\AccessForbidden;

class SubscriptionStatistics extends BackendSubscriptionStatistics
{
	protected function onBeforeBrowse()
	{
		$user = $this->container->platform->getUser();

		if ($user->guest || !$user->authorise('core.admin', 'com_akeebasubs'))
		{
			throw new AccessForbidden;
		}

		parent::onBeforeBrowse();
	}

}