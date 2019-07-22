<?php
/**
 *  @package   AkeebaSubs
 *  @copyright Copyright (c)2010-$toda.year Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license   GNU General Public License version 3, or later
 */


namespace Akeeba\Subscriptions\Site\Model\Subscribe;

use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

/**
 * Interface for subscription-level callback handlers
 *
 * @since  7.0.0
 */
interface SubscriptionCallbackHandlerInterface
{
	/**
	 * Constructor
	 *
	 * @param   Container  $container  The component container
	 *
	 * @since  7.0.0
	 */
	public function __construct(Container $container);

	/**
	 * Handle a webhook callback from the payment service provider about a specific subscription
	 *
	 * @param   Subscriptions  $subscription  The subscription the webhook refers to
	 * @param   array          $requestData   The request data minus component, option, view, task
	 *
	 * @return  string|null  Text to include in the callback response page
	 *
	 * @throws  \RuntimeException  In case an error occurs. The exception code will be used as the HTTP status.
	 *
	 * @since  7.0.0
	 */
	public function handleCallback(Subscriptions $subscription, array $requestData): ?string;
}