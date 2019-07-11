<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Admin\Model\Coupons;
use Akeeba\Subscriptions\Admin\Model\Levels;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use DateInterval;
use FOF30\Model\DataModel\Collection as DataCollection;
use FOF30\Utils\Collection;
use FOF30\Utils\Ip;
use Joomla\CMS\Http\HttpFactory;

defined('_JEXEC') or die;

class Recurring extends Base
{
	/**
	 * A callback to use instead of the HTTP request to Paddle during unit testing
	 *
	 * @var     null|callable
	 * @since   7.0.0
	 */
	public static $callbackForUnitTests = null;

	protected function getValidationResult(): array
	{
		// Return data
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

		// Get the subscription level
		/** @var Levels $level */
		$level = $this->container->factory->model('Levels')->tmpInstance();
		$level->find($this->state->id);

		$recurringId = $level->paddle_plan_id ?? 0;

		// This is a forever subscription. YOU CANNOT CHARGE A RECURRING FEE FOR AN ONE-TIME-PAYMENT SUSBCRIPTION, BRUH!
		if ($level->forever)
		{
			return $ret;
		}

		// This level has a fixed expiration date. YOU CANNOT RECURSIVELY CHARGE FOR A FIXED DATE EXPIRATION, BRUH!
		if (!is_null($level->fixed_date) && !empty($level->fixed_date) && ($level->fixed_date != $this->container->db->getNullDate()))
		{
			return $ret;
		}

		// No recurring information? No recurring subscription available for you.
		if (empty($recurringId))
		{
			return $ret;
		}

		// Feature explicitly disabled? No recurring subscription available for you.
		if ($level->upsell == 'never')
		{
			return $ret;
		}

		// Check for blocking subscriptions on this level
		$blockingSubs = $this->getBlockingSubscriptionIDs($level->getId());

		if (!empty($blockingSubs))
		{
			return array_merge($ret, [
				'blocking_subscription_ids' => $blockingSubs,
			]);
		}

		// Get recurring access coupon information
		$hasCoupon = $this->hasRecurringCoupon();

		// Get recurring pricing and information from Paddle
		$recurringInfo = $this->getRecurringInformationFromPaddle($recurringId);

		// If we can't get pricing info or if it's not recurring: bye-bye!
		if (
			(($recurringInfo['recurringId'] ?? 0) != $recurringId)
			|| (($recurringInfo['recurring_frequency'] ?? 0) <= 0)
			|| (($recurringInfo['recurring_price'] ?? 0.00) <= 0.009)
		)
		{
			return $ret;
		}

		// Get active + renewal subs on this level, therefore determining if we're trying to purchase a renewal.
		$currentSubs = $this->getActiveAndRenewalSubs($level->getId());
		$isRenewal   = $currentSubs->count() > 0;

		// Should I include tax in the recurring price?
		$includeTax = $this->container->params->get('showEstimatedTax', 1);

		/**
		 * Get the maximum trial period
		 *
		 * By default it's the subscription level's duration.
		 *
		 * However, if we have currently active subscriptions and / or purchased renewals then the max trial period is
		 * the number of days from now to the last subscription's expiration date. Subscriptions are sorted by
		 * expiration date ascending, so I need to only check the last record in the collection :)
		 */
		$maxTrialPeriod = $level->duration;

		if ($isRenewal)
		{
			/**
			 * The recurring subscriptions starts up to one day BEFORE the last active / renewal subscription expires.
			 * This is necessary since the payment takes places sometime within the 24 hours of the renewal anniversary
			 * but we are NOT guaranteed when exactly. It is possible that the current subscription expires at 6 am but
			 * the recurring payment only happens at 8 pm, leaving the user with 14 hours of no subscription which would
			 * leave them very confused indeed. By "stealing" a day we make sure that Paddle will have charged the
			 * recurring fee before the last regular subscription expires, therefore ensuring a continuous subscription
			 * and uninterrupted service for the customer.
			 */
			/** @var Subscriptions $lastSub */
			$lastSub        = $currentSubs->pop();
			$jNow           = $this->container->platform->getDate()->setTime(0, 0, 0, 0);
			$jThen          = $this->container->platform->getDate($lastSub->publish_down)->setTime(0, 0, 0, 0);
			$oneDay         = new DateInterval('P1D');
			$maxTrialPeriod = intval($jThen->sub($oneDay)->diff($jNow)->format('%a'));

			if ($maxTrialPeriod < 0)
			{
				$maxTrialPeriod = intval($jThen->diff($jNow)->format('%a'));
			}

			/**
			 * Defend against an impossibility: the maximum expiration date of the active / renewal subscriptions on
			 * this level are in the past, making the result above negative. In this case I revert to the level's
			 * duration.
			 */
			if ($maxTrialPeriod < 0)
			{
				$maxTrialPeriod = $level->duration;
			}
		}

		/**
		 * First case: Always recurring
		 *
		 * New subscriptions consist of a trial period equal to the level's duration with an initial price equal to the
		 * (possibly discounted) subscription level price. Then you get recurring payments.
		 *
		 * If you use a recurring access coupon you get 0 trial period, 0 initial price and recurring payments right
		 * away.
		 *
		 * If you are purchasing a renewal you get a trial period to get to the publish_down date of your last active
		 * or renewal subscription, 0 initial price and recurring payments right away. Coupons don't matter for renewals
		 */
		if ($level->upsell == 'always')
		{
			$priceValidation = $this->factory->getValidator('Price')->execute();
			$trialPeriod     = $maxTrialPeriod;
			$isDiscounted    = $priceValidation['discount'] > 0.001;
			$initialPrice    = $priceValidation['gross'];

			if ($isRenewal)
			{
				$isDiscounted = false;
				$initialPrice = 0;
			}
			elseif ($hasCoupon)
			{
				$isDiscounted = false;
				$initialPrice = 0;
				$trialPeriod  = 0;
			}

			return [
				// Paddle subscription plan ID
				'recurringId'               => $recurringId,
				// IDs of the subscriptions blocking upsell to a recurring level
				'blocking_subscription_ids' => null,
				// Initial period price, merchant currency (always net)
				'initial_price'             => $initialPrice,
				// Recurring price, user currency (net or gross, based on component parameters)
				'recurring_price'           => (!$isDiscounted && $includeTax) ? $recurringInfo['recurring_price'] : $recurringInfo['recurring_price_net'],
				// Recurring frequency, integer
				'recurring_frequency'       => $recurringInfo['recurring_frequency'],
				// Recurring type: day, week, month, year
				'recurring_type'            => $recurringInfo['recurring_type'],
				// Trial days -- adjusted for upgrade subscriptions where necessary
				'trial_days'                => $trialPeriod,
			];
		}

		/**
		 * Upsell = renewal
		 *
		 * For a new subscription you get no recurring payments _unless_ you have a coupon code. If you have a coupon
		 * code you get 0 trial period, 0 initial price and recurring payments right away.
		 *
		 * If you are purchasing a renewal you get a trial period to get to the publish_down date of your last active
		 * or renewal subscription, 0 initial price and recurring payments right away. Coupons don't matter for renewals
		 */
		if (!$isRenewal && !$hasCoupon)
		{
			return $ret;
		}

		// New user with coupon: no trial period
		if (!$isRenewal && $hasCoupon)
		{
			$maxTrialPeriod = 0;
		}

		return [
			// Paddle subscription plan ID
			'recurringId'               => $recurringId,
			// IDs of the subscriptions blocking upsell to a recurring level
			'blocking_subscription_ids' => null,
			// Initial period price, merchant currency (always net)
			'initial_price'             => 0,
			// Recurring price, user currency (net or gross, based on component parameters)
			'recurring_price'           => $includeTax ? $recurringInfo['recurring_price'] : $recurringInfo['recurring_price_net'],
			// Recurring frequency, integer
			'recurring_frequency'       => $recurringInfo['recurring_frequency'],
			// Recurring type: day, week, month, year
			'recurring_type'            => $recurringInfo['recurring_type'],
			// Trial days -- adjusted for upgrade subscriptions where necessary
			'trial_days'                => $maxTrialPeriod,
		];
	}

	/**
	 * Do we have a special coupon which allows access to recurring subscriptions?
	 *
	 * @return  bool
	 *
	 * @since   7.0.0
	 */
	protected function hasRecurringCoupon(): bool
	{
		$hasSpecialCoupon = false;
		$couponValidation = $this->factory->getValidator('Coupon')->execute();
		/** @var Coupons $coupon */
		$coupon = $couponValidation['coupon'];

		if ($couponValidation['couponFound'] && $couponValidation['valid'] && $coupon->recurring_access)
		{
			$hasSpecialCoupon = true;
		}

		return $hasSpecialCoupon;
	}

	/**
	 * Returns the subscription IDs for paid, enabled, recurring subscriptions of the current user in the given level.
	 * These subscriptions "block" recurring purchases, hence their name.
	 *
	 * @param int $level_id Subscription level ID
	 *
	 * @return  int[]  The IDs of the blocking subscription levels
	 *
	 * @since   7.0.0
	 */
	protected function getBlockingSubscriptionIDs(int $level_id): array
	{
		// Guests do not have any subscriptions.
		if ($this->jUser->guest)
		{
			return [];
		}

		/** @var Subscriptions $subsModel */
		$subsModel = $this->container->factory->model('Subscriptions')->tmpInstance();

		/**
		 * Important notes:
		 *
		 * DO NOT set ->enabled(1) because a user may have already bought early a recurring subscription renewal. In
		 * this case the recurring subscription is paid (state 'C') but not yet active (enabled '0'), yet it should be
		 * blocking the purchase or a renewal.
		 */
		return $subsModel
			// Filter for paid and enabled subs for given user and level
			->user_id($this->jUser->id)
			->level($level_id)
			->paystate(['C'])
			// Get all items
			->get(true)
			// Filter for recurring subscriptions (since there's an OR element I can't efficiently do that in the query)
			->filter(function (Subscriptions $item) {
				if (!empty($item->update_url) || !empty($item->cancel_url))
				{
					return true;
				}

				return false;
			})
			// Return just the level IDs
			->lists('akeebasubs_subscription_id');
	}

	/**
	 * Gets the recurring information (price, frequency, type) for a recurring plan from Paddle using their pricing API.
	 *
	 * @param string $recurringId The Paddle recurring plan ID
	 *
	 * @return  array  The recurring plan information
	 *
	 * @since   7.0.0
	 */
	protected function getRecurringInformationFromPaddle(string $recurringId): array
	{
		// Initialize the return value
		$ret = [
			// Paddle subscription plan ID
			'recurringId'         => null,
			// Recurring price (possibly with tax)
			'recurring_price'     => 0.00,
			// Recurring price (always without tax)
			'recurring_price_net' => 0.00,
			// Recurring frequency, integer
			'recurring_frequency' => 0,
			// Recurring type: day, week, month, year
			'recurring_type'      => 'day',
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
		$response = null;

		if (!is_null(self::$callbackForUnitTests) && is_callable(self::$callbackForUnitTests))
		{
			$response = call_user_func(self::$callbackForUnitTests, $urlParams);
		}

		try
		{
			if (is_null($response))
			{
				$response = $http->get($url, [], 10);
			}
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
		$ret['recurring_price']     = $hasTax ? $product->subscription->price->gross : $product->subscription->price->net;
		$ret['recurring_price_net'] = $product->subscription->price->net;
		$ret['recurring_frequency'] = $product->subscription->frequency;
		$ret['recurring_type']      = $product->subscription->interval;

		return $ret;
	}

	/**
	 * Get all active and future renewal subscriptions on a given subscription level sorted by expiration date ascending
	 *
	 * @param int $level_id The susbcription level ID
	 *
	 * @return  Collection  All subscriptions matching our criteria
	 *
	 * @since   7.0.0
	 */
	protected function getActiveAndRenewalSubs(int $level_id): DataCollection
	{
		// Guests do not have any subscriptions.
		if ($this->jUser->guest)
		{
			return new DataCollection();
		}

		/** @var Subscriptions $subsModel */
		$subsModel = $this->container->factory->model('Subscriptions')->tmpInstance();

		return $subsModel
			// Get all paid subscriptions expiring in the future
			->user_id($this->jUser->id)
			->level($this->state->id)
			->paystate(['C'])
			->expires_from($this->container->platform->getDate()->toSql())
			// Get all items
			->get(true)
			// Order by expiration date, ascending
			->sortBy(function (Subscriptions $sub) {
				return $this->container->platform->getDate($sub->publish_down)->getTimestamp();
			}, SORT_NUMERIC);

	}
}