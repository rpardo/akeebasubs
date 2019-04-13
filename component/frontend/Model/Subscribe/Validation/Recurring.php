<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Site\Model\Coupons;
use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Date\Date;
use FOF30\Utils\Collection;
use FOF30\Utils\Ip;
use Joomla\CMS\Http\HttpFactory;

defined('_JEXEC') or die;

class Recurring extends Base
{
	protected function getValidationResult(): array
	{
		// Get the subscription level
		/** @var Levels $level */
		$level = $this->container->factory->model('Levels')->tmpInstance();
		$level->find($this->state->id);

		$ret = [
			// Paddle subscription plan ID
			'recurringId'               => null,
			// IDs of the subscriptions blocking upsell to a recurring level
			'blocking_subscription_ids' => null,
			// Initial period price, merchant currency (always net)
			'initial_price'             => 0.00,
			// Recurring price, user currency (net or gross, based on component parameters)
			'recurring_price'           => 0.00,
			// Recurring frequency, integer
			'recurring_frequency'       => 0,
			// Recurring type: day, week, month, year
			'recurring_type'            => 'day',
			// Trial days -- adjusted for upgrade subscriptions where necessary
			'trial_days'                => 0,
		];

		$recurringId = $level->paddle_plan_id;

		// I can only upsell if there is a plan to upsell to
		if (empty($recurringId))
		{
			return $ret;
		}

		// I cannot upsell if the feature is disabled
		if ($level->upsell == 'never')
		{
			return $ret;
		}

		// If I have a special access coupon I can upsell to the user
		$couponValidation = $this->factory->getValidator('Coupon')->execute();
		/** @var Coupons $coupon */
		$coupon = $couponValidation['coupon'];

		if ($couponValidation['couponFound'] && $couponValidation['valid'] && $coupon->recurring_access)
		{
			$ret = array_merge($ret, $this->getRecurringPricing($recurringId, true));

			return $ret;
		}

		/**
		 * Guest users have a very simple logic.
		 *
		 * If we are allowed to always upsell I show them the upsell.
		 *
		 * If we are only allowed to upsell on renewal, the guest user cannot possibly purchase a renewal so we cannot
		 * upsell to them
		 */
		if ($this->jUser->guest)
		{
			if ($level->upsell == 'always')
			{
				$ret = array_merge($ret, $this->getRecurringPricing($recurringId, false));
			}

			return $ret;
		}

		// Get the user's subscriptions on this level
		/** @var Subscriptions $subsModel */
		$subsModel = $this->container->factory->model('Subscriptions')->tmpInstance();
		$subsModel
			->user_id($this->jUser->id)
			->level($level->getId())
			->paystate(['C', 'P']);
		$allSubs = $subsModel->get(true);

		$allSubs = $allSubs->filter(function (Subscriptions $item) {
			return $item->enabled != 0;
		});

		$activeRecurringSubs = $allSubs->filter(function (Subscriptions $item) {
			if (!empty($item->update_url) || !empty($item->cancel_url))
			{
				return true;
			}

			return false;
		});

		/**
		 * If the user already has recurring subscriptions on the same level I cannot upsell; they already have a
		 * recurring subscription!
		 */
		if ($activeRecurringSubs->count())
		{
			// Add blocking subscriptions
			$ret['blocking_subscription_ids'] = $activeRecurringSubs->map(function (Subscriptions $subscription, $key) {
				return $subscription->getId();
			});

			return $ret;
		}

		// I have a user who has not bought a recurring subscription. If I'm allowed to always upsell to them I am done.
		if ($level->upsell == 'always')
		{
			$ret = array_merge($ret, $this->getRecurringPricing($recurringId, false));

			return $ret;
		}

		// User with no recurring subscriptions and I can only upsell on upgrade. Is this an early subscription upgrade?
		if ($allSubs->count() == 0)
		{
			return $ret;
		}

		$ret = array_merge($ret, $this->getRecurringPricing($recurringId, false));

		return $ret;
	}

	private function getRecurringPricing(string $recurringId, bool $hasSpecialCoupon): array
	{
		// Initialize the return value
		$ret = [
			// Paddle subscription plan ID
			'recurringId'         => null,
			// Initial period price
			'initial_price'       => 0.00,
			// Recurring price
			'recurring_price'     => 0.00,
			// Recurring frequency, integer
			'recurring_frequency' => 0,
			// Recurring type: day, week, month, year
			'recurring_type'      => 'day',
			// Trial days -- adjusted for upgrade subscriptions where necessary
			'trial_days'          => 0,
		];

		// Make a pricing request to Paddle
		$ip        = Ip::getIp();
		$urlParams = [
			'vendor_id'        => $this->container->params->get('vendor_id', ''),
			'vendor_auth_code' => $this->container->params->get('vendor_auth_code', ''),
			'product_ids'      => $recurringId,
			'customer_ip'      => $ip,
		];

		$url      = 'https://checkout.paddle.com/api/2.0/prices?' . http_build_query($urlParams);
		$http     = HttpFactory::getHttp();

		try
		{
			$response = $http->get($url, [], 10);
		}
		catch (\Exception $e)
		{
			return $ret;
		}

		// Did the request fail?
		if ($response->code != 200)
		{
			return $ret;
		}

		// Did we get an empty body?
		$body = $response->body;

		if (empty($body))
		{
			return $ret;
		}

		// Is the returned price information not a JSON object?
		$priceInfo = @json_decode($body, false);

		if (empty($priceInfo))
		{
			return $ret;
		}

		// Is the returned price information unsuccessful?
		if (!isset($priceInfo->success) || !$priceInfo->success || !isset($priceInfo->response) || !isset($priceInfo->response->products) || !is_array($priceInfo->response->products) || empty($priceInfo->response->products))
		{
			return $ret;
		}

		// Try to locate our product
		$found = false;

		foreach ($priceInfo->response->products as $product)
		{
			if ($product->product_id == $recurringId)
			{
				$found = true;

				break;
			}
		}

		// Is the product not found?
		if (!$found)
		{
			return $ret;
		}

		// Is the product non-recurring?
		if (!isset($product->subscription))
		{
			return $ret;
		}

		// Return the information
		$hasTax                     = $this->container->params->get('showEstimatedTax', 1);
		$ret['recurringId']         = $recurringId;
		$ret['initial_price']       = $hasTax ? $product->price->gross : $product->price->net;
		$ret['recurring_price']     = $hasTax ? $product->subscription->price->gross : $product->subscription->price->net;
		$ret['recurring_frequency'] = $product->subscription->frequency;
		$ret['recurring_type']      = $product->subscription->interval;
		$ret['trial_days']          = $product->subscription->trial_days;

		// Recalculate trial_days and initial_price

		/**
		 * A. Special access user (with coupon)
		 *   trial_days: 0
		 *   initial_price: 0
		 */
		if ($hasSpecialCoupon)
		{
			$ret['trial_days']      = 0;
			$ret['initial_price'] = 0;

			return $ret;
		}

		/** @var Subscriptions $subsModel */
		$subsModel = $this->container->factory->model('Subscriptions')->tmpInstance();
		$subsModel
			->user_id($this->jUser->id)
			->level($this->state->id)
			->paystate(['C'])
			->enabled(1);
		$allSubs = $subsModel->get(true);

		/**
		 * B. User WITH an active subscription on this level:
		 *   trial_days: remaining subscription time, rounded down -- minus one day
		 *   initial_price: 0
		 */
		if (!empty($allSubs))
		{
			$maxExpiration = $allSubs->reduce(function (int $carry, Subscriptions $sub) {
				$jDate = new Date($sub->publish_down);
				$toInt = $jDate->getTimestamp();

				if ($toInt > $carry)
				{
					$carry = $toInt;
				}

				return $toInt;
			}, 0);

			$now  = time();
			$days = floor(floatval($now - $maxExpiration) / 86400.00) - 1;

			$ret['trial_days']    = max(0, $days);
			$ret['initial_price'] = 0;
		}

		/**
		 * C. User WITHOUT an active subscription on this level:
		 *   trial_days: level duration
		 *   initial_price: post-discount price (use the price validator)
		 */
		/** @var Levels $level */
		$level = $this->container->factory->model('Levels')->tmpInstance();
		$level->find($this->state->id);

		$priceValidation      = $this->factory->getValidator('Price')->execute();
		$ret['trial_days']    = $level->duration;
		$ret['initial_price'] = $priceValidation['gross'];

		return $ret;
	}
}