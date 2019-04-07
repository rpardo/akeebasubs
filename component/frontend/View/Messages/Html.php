<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\View\Messages;

use Akeeba\Subscriptions\Admin\Helper\Message;
use Akeeba\Subscriptions\Site\Model\Subscriptions;

defined('_JEXEC') or die;

class Html extends \FOF30\View\DataView\Html
{
	/**
	 * The subscription we are dealing with, set by the Controller.
	 *
	 * @var  Subscriptions
	 */
	public $subscription = null;

	/**
	 * HTML for the post-subscription message returned by akeebasubs plugins. This is displayed after the Go Back link.
	 *
	 * @var  string
	 */
	public $pluginHTML = '';

	/**
	 * The message to display, from the subscription level.
	 *
	 * @var  string
	 */
	public $message = '';

	public function onBeforeRead($tpl = null)
	{
		switch ($this->getLayout())
		{
			case 'thankyou':
			default:
				$this->prepareView('onOrderMessage','ordertext');
				break;

			case 'cancel':
				$this->prepareView('onCancelMessage','canceltext');
				break;

			case 'pending':
				$this->prepareView('onCancelMessage','pendingtext');
				break;

			case 'abandoned':
				$this->prepareView('onCancelMessage','abandonedtext');
				break;
		}
	}

	/**
	 * Prepare the view parameters
	 *
	 * @param   string  $event         Plugin event to call
	 * @param   string  $messageField  Which field should I use to get the message's template
	 *
	 * @throws \Exception
	 */
	protected function prepareView($event = 'onOrderMessage', $messageField = 'ordertext')
	{
		parent::onBeforeRead();

		$app = \JFactory::getApplication();

		// Get and process the message from the subscription level
		switch ($messageField)
		{
			case 'ordertext':
				$message = $this->item->$messageField;
				break;

			default:
				$message = $this->container->params->get($messageField, '');
				break;
		}

		$message = Message::processLanguage($message);
		$message = Message::processSubscriptionTags($message, $this->subscription);
		$this->message = \JHTML::_('content.prepare', $message);

		// Get additional message HTML from the plugins
		$pluginHtml = '';

		$this->container->platform->importPlugin('akeebasubs');
		$jResponse = $this->container->platform->runPlugins($event, array($this->subscription));

		if (is_array($jResponse) && !empty($jResponse))
		{
			foreach ($jResponse as $pluginResponse)
			{
				if (!empty($pluginResponse))
				{
					$pluginHtml .= $pluginResponse;
				}
			}
		}

		$this->pluginHTML = $pluginHtml;

		// Makes sure SiteGround's SuperCache doesn't cache the subscription page
		$app->setHeader('X-Cache-Control', 'False', true);
	}
}
