<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\None;


use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use Akeeba\Subscriptions\Site\Model\Subscribe\CallbackInterface;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\FixSubscriptionDateTrait;
use FOF30\Container\Container;
use Joomla\CMS\Language\Text;
use RuntimeException;

class CallbackHandler implements CallbackInterface
{
	use FixSubscriptionDateTrait;

	/**
	 * The component's Container
	 *
	 * @var    Container
	 * @since  7.0.0
	 */
	private $container;

	/**
	 * Constructor
	 *
	 * @param Container $container The component container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Handle a webhook callback from the payment service provider
	 *
	 * @param   string  $requestMethod  The HTTP method, e.g. 'POST' or 'GET'
	 * @param   array   $requestData    The request data minus component, option, view, task
	 *
	 * @return  string|null  Text to include in the callback response page
	 *
	 * @throws  RuntimeException  In case an error occurs. The exception code will be used as the HTTP status.
	 */
	public function handleCallback(string $requestMethod, array $requestData): ?string
	{
		if (!isset($requestData['passthrough']))
		{
			throw new RuntimeException(Text::_('COM_AKEEBASUBS_CALLBACK_ERR_NOSUCHSUBSCRIPTION'), 404);
		}

		$id = (int)$requestData['passthrough'];

		if (empty($id))
		{
			throw new RuntimeException(Text::_('COM_AKEEBASUBS_CALLBACK_ERR_NOSUCHSUBSCRIPTION'), 404);
		}

		/** @var Subscriptions $subscription */
		$subscription = $this->container->factory->model('Subscriptions')->tmpInstance();

		$subscription->find($id);

		if (empty($subscription->akeebasubs_subscription_id) || ($subscription->akeebasubs_subscription_id != $id))
		{
			throw new RuntimeException(Text::_('COM_AKEEBASUBS_CALLBACK_ERR_NOSUCHSUBSCRIPTION'), 404);
		}

		$updates = array(
			'akeebasubs_subscription_id' => $id,
			'processor_key'              => md5(microtime(false)),
			'state'                      => 'C',
			'enabled'                    => 1,
		);

		$subscription->save($this->fixSubscriptionDates($subscription, $updates));

		// Run the onAKAfterPaymentCallback events
		$this->container->platform->importPlugin('akeebasubs');
		$this->container->platform->runPlugins('onAKAfterPaymentCallback', array(
			$subscription
		));


		// This callback is a tricky one; it will redirect you to the thank you page ;)
		$slug = $subscription->level->slug;

		$url = 'index.php?option=com_akeebasubs&view=Message&slug=' . $slug . '&layout=order&subid=' . $subscription->akeebasubs_subscription_id;

		try
		{
			$this->container->platform->redirect($url);
		}
		catch (\Exception $e)
		{
			return 'The payment succeeded but Joomla crashed during redirection :(';
		}

		// Everything is fine, no matter what
		return null;
	}
}