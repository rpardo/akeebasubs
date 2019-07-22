<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */


namespace Akeeba\Subscriptions\Site\Model\Subscribe;

use FOF30\Container\Container;

/**
 * Interface for callback handlers
 *
 * @since  7.0.0
 */
interface CallbackInterface
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
	 * Handle a webhook callback from the payment service provider
	 *
	 * @param   string  $requestMethod  The HTTP method, e.g. 'POST' or 'GET'
	 * @param   array   $requestData    The request data minus component, option, view, task
	 *
	 * @return  string|null  Text to include in the callback response page
	 *
	 * @throws  \RuntimeException  In case an error occurs. The exception code will be used as the HTTP status.
	 *
	 * @since  7.0.0
	 */
	public function handleCallback(string $requestMethod, array $requestData): ?string;
}