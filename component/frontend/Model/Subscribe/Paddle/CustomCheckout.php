<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle;

use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Container\Container;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use RuntimeException;

/**
 * Implements Paddle's custom checkout logic
 *
 * @see
 *
 * @since   7.0.0
 */
class CustomCheckout
{
	/**
	 * Component's container
	 *
	 * @var   Container
	 * @since 7.0.0
	 */
	private $container;

	/**
	 * CustomCheckout constructor.
	 *
	 * @param   Container  $container  The component container
	 *
	 * @since   7.0.0
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Get the custom checkout URL
	 *
	 * @param   Subscriptions  $sub
	 *
	 * @return  string
	 *
	 * @since   7.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getCheckoutUrl(Subscriptions $sub): string
	{
		$user   = Factory::getUser($sub->user_id);
		/** @var Levels $level */
		$level = $this->container->factory->model('Levels')->tmpInstance();
		$level->findOrFail($sub->akeebasubs_level_id);

		if (empty($level->paddle_product_id))
		{
			throw new RuntimeException(sprintf('There is no Paddle product associated with %s', $level->title));
		}

		$fields = [
			'vendor_id'         => $this->container->params->get('vendor_id'),
			'vendor_auth_code'  => $this->container->params->get('vendor_auth_code'),
			'product_id'        => $level->paddle_product_id,
			'prices'            => [
				$this->container->params->get('currency') . ':' . sprintf('%0.2f', $sub->net_amount),
			],
			'discountable'      => 0,
			//'image_url'         => Image::getURL($level->image),
			'quantity_variable' => 0,
			'quantity'          => 1,
			'marketing_consent' => 0,
			'customer_email'    => $user->email,
			'passthrough'       => $sub->getId(),
		];

		// Add country from the user's profile
		$country = $this->getCountry($user);

		if (!empty($country) && ($country != 'XX'))
		{
			$fields['customer_country'] = $country;
		}

		// Here we make the request to the Paddle API
		$url = 'https://vendors.paddle.com/api/2.0/product/generate_pay_link';
		$ch  = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		$response = curl_exec($ch);

		$curlErrNo = curl_errno($ch);
		$curlErrMsg = curl_error($ch);

		if ($curlErrNo)
		{
			throw new RuntimeException(sprintf('cURL error %u: %s', $curlErrNo, $curlErrMsg));
		}

		// And handle the response...
		$data = json_decode($response);

		if (empty($data) || !is_object($data) || !isset($data->success))
		{
			throw new RuntimeException('JSON decoding error');
		}

		if ($data->success)
		{
			return $data->response->url;
		}

		throw new RuntimeException($data->error->message);
	}

	private function getCountry(User $user): ?string
	{
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select([
				$db->qn('profile_value')
			])->from($db->qn('#__user_profiles'))
			->where($db->qn('user_id') . ' = ' . $db->q($user->id))
			->where($db->qn('profile_key') . ' = ' . $db->q('akeebasubs.country'));

		try
		{
			return $db->setQuery($query)->loadResult();
		}
		catch (\Exception $e)
		{
			return null;
		}
	}
}