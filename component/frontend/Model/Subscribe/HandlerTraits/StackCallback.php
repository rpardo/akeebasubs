<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits;


use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;

trait StackCallback
{
	/**
	 * Returns an $updates array which will modify the subscription in a way that stacks the current callback data into
	 * the subscription's parameters.
	 *
	 * @param   Subscriptions  $subscription  The subscription record
	 * @param   array          $requestData   The callback request data
	 *
	 * @return  array
	 *
	 * @since   7.0.0
	 */
	protected function getStackCallbackUpdate(Subscriptions $subscription, array $requestData): array
	{
		/** @var Container $container */
		$container     = $this->container;
		$log_callbacks = $container->params->get('log_callbacks', 0);

		// Only proceed if I have to log callbacks inside the subscription records.
		if (!$log_callbacks)
		{
			return [];
		}

		$params = $subscription->params;

		if (!isset($params['callbacks']))
		{
			$params['callbacks'] = [];
		}

		return [
			'params' => array_merge([
				'akeebasubs_when' => gmdate('Y-m-d H:i:s'),
			], $requestData),
		];
	}
}