<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\View\Message;

use Akeeba\Subscriptions\Admin\Helper\Message;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\View\DataView\Html as BaseView;

defined('_JEXEC') or die;

class Html extends BaseView
{
	/**
	 * The subscription we are dealing with, set by the Controller.
	 *
	 * @var  Subscriptions
	 */
	public $subscription = null;

	/**
	 * The message to display, from the subscription level.
	 *
	 * @var  string
	 */
	public $message = '';

	public function onBeforeShow($tpl = null)
	{
		$message = $this->subscription->level->ordertext;
		$message = Message::processLanguage($message);
		$message = Message::processSubscriptionTags($message, $this->subscription);
		$this->message = \JHTML::_('content.prepare', $message);
	}
}
