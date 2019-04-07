<?php
/**
 *  @package   AkeebaSubs
 *  @copyright Copyright (c)2010-$toda.year Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits;


use FOF30\Container\Container;

/**
 * Used to verify the authenticity of a Paddle webhook callback
 *
 * @since  7.0.0
 */
trait VerifyAuthenticity
{
	/**
	 * Verifies the authenticity of the callback data. WARNING! Only pass the POST data. The GET and REQUEST data will
	 * include the Joomla-specific request parameters which must NOT be part of the verification process.
	 *
	 * @param   array  $data  The POST data of the request
	 *
	 * @return  bool
	 *
	 * @see     https://paddle.com/docs/reference-verifying-webhooks/#!
	 * @since   7.0.0
	 */
	protected function verifyCallbackData(array $data): bool
	{
		/** @var Container $container */
		$container  = $this->container;
		$do_verify  = $container->params->get('verify_callbacks', 1);
		$public_key = $container->params->get('public_key', '');

		// If verification is disabled I check that p_signature matches the 'secret' parameter, normally used for CRON.
		if (!$do_verify)
		{
			$signature = isset($data['p_signature']) ? $data['p_signature'] : null;
			$secret    = $container->params->get('secret', '');

			return $signature === $secret;
		}

		// Get the p_signature parameter & base64 decode it.
		$signature = base64_decode($data['p_signature']);

		// Get the fields sent in the request, and remove the p_signature parameter
		unset($data['p_signature']);

		// ksort() and serialize the fields
		ksort($data);

		foreach ($data as $k => $v)
		{
			if (!in_array(gettype($v), ['object', 'array']))
			{
				$data[$k] = "$v";
			}
		}

		$data = serialize($data);

		// Verify the signature
		$verification = openssl_verify($data, $signature, $public_key, OPENSSL_ALGO_SHA1);

		return $verification == 1;
	}
}