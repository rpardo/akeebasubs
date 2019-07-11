<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscribe\Validation\Recurring;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use Akeeba\Subscriptions\Tests\Stubs\ValidatorTestCase;
use Akeeba\Subscriptions\Tests\Stubs\ValidatorWithSubsTestCase;
use FOF30\Date\Date;
use FOF30\Utils\Ip;
use Joomla\CMS\Http\Response;

/**
 * Test the Recurring validator
 *
 * @covers \Akeeba\Subscriptions\Site\Model\Subscribe\Validation\RecurringTest
 */
class RecurringTest extends ValidatorWithSubsTestCase
{
	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'Recurring';

		// Create the base objects
		parent::setUpBeforeClass();

		global $akeebasubsTestConfig;

		static::$container->params->setParams([
			'vendor_id'        => $akeebasubsTestConfig['vendor_id'],
			'vendor_auth_code' => $akeebasubsTestConfig['vendor_auth_code'],
		]);
		static::$container->params->save();

	}

	public function getTestData()
	{
		$jNow = new Date();

		// - 365 days ==> expires in 365 days
		$jLastYear = clone $jNow;
		$jLastYear->sub(new \DateInterval('P365D'));

		// - 181 days ==> expires in 184 days
		$jLastHalfYear = clone($jNow);
		$jLastHalfYear->sub(new \DateInterval('P181D'));

		// NOW ==> 365 remaining, 0 used

		// + 181 days ==> expires in 546 days
		$jNextHalfYear = clone($jLastHalfYear);
		$jNextHalfYear->add(new \DateInterval('P181D'));

		// + 365 days ==> expires in 730 days
		$jNextYear = clone $jNow;
		$jNextYear->add(new \DateInterval('P365D'));

		$testCases = [
			//<editor-fold desc="Renewal = never">
			'Non-recurring level, guest'                      => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'never',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			'Non-recurring level, no sub'                     => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'never',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			'Non-recurring level, with sub'                   => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 3,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'never',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			'Non-recurring level, with sub and coupon code'   => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 3,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURSION',
					'_upsell' => 'never',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			//</editor-fold>

			//<editor-fold desc="Always recurring level (1)">
			'Always recurring level, guest'                   => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 50.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, guest, coupon'           => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, no sub'                  => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 50.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, no sub, coupon'          => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, with sub'                => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 184, // 365 days - 181 already consumed
					'blocking_subscription_ids' => null,
				],
			],
			'Always recurring level, with sub, coupon'        => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 184, // 365 days - 181 already consumed
					'blocking_subscription_ids' => null,
				],
			],
			'Always recurring level, with renewal'            => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
					],
					[
						'level'      => 1,
						'publish_up' => $jNextHalfYear->toSql(),
						'enabled'    => 0,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 184 + 365, // First sub: 365 - 181 consumed; second sub: 365 days
					'blocking_subscription_ids' => null,
				],
			],
			'Always recurring level, with renewal and coupon' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
					],
					[
						'level'      => 1,
						'publish_up' => $jNextHalfYear->toSql(),
						'enabled'    => 0,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 9.00,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 184 + 365, // First sub: 365 - 181 consumed; second sub: 365 days
					'blocking_subscription_ids' => null,
				],
			],
			/**/
			//</editor-fold>

			//<editor-fold desc="Recurring only on renewal (level 2)">
			// Guest, no coupon => NOT recurring
			// Guest, coupon => recursion, 0 trial, 0 initial price
			// Logged in, no sub => NOT recurring
			// Logged in, no sub, coupon => recursion, 0 trial, 0 initial price
			// Logged in, expired sub => NOT recurring
			// Logged in, expired sub, coupon => recursion, 0 trial, 0 initial price
			// Logged in, one sub 6 months ago => recursion, 184 trial, 0 initial price
			// Logged in, one sub 6 months ago, coupon => recursion, 184 trial, 0 initial price
			// Logged in, two subs 6 months ago + 6 months into the future => recursion, 365 + 184 trial, 0 initial price
			// Logged in, two subs 6 months ago + 6 months into the future, coupon => recursion, 365 + 184 trial, 0 initial price
			// Logged in, two subs 375 days ago (expired) + 10 days ago => recursion, 355 trial, 0 initial price
			// Logged in, two subs 375 days ago (expired) + 10 days ago, coupon => recursion, 355 trial, 0 initial price
			// Logged in, already has a recurring subscription 6 months ago => no recursion, blocking sub (S1)
			// Logged in, regular sub 6 months ago, recurring 6 months into the future => no recursion, blocking sub (S2)
			// Logged in, canceled recurring 6 months ago => no recursion, NO blocking sub
			// Logged in, canceled recurring 6 months ago, active regular 6 months ago => recursion, 184 trial, 0 initial price
			//</editor-fold>

			//<editor-fold desc="Special cases">
			// Purchasing bundle, already recurringly subscribed to single product level => blocked sub
			// Purchasing bundle, already subscribed to single product level => discount
			//</editor-fold>

		];

		foreach ($testCases as $message => &$testCase)
		{
			$testCase = array_merge($testCase, [
				'message' => $message,
			]);
		}

		return $testCases;
	}

	/**
	 * Test the validator
	 *
	 * @dataProvider getTestData
	 */
	public function testGetValidationResult($loggedIn, $subs, $state, $expected, $message)
	{
		/**
		 * Fake the HTTP response from Paddle. This lets our tests run without making contact with the Paddle server
		 *
		 * @param array $urlParams The URL parameters Akeeba Subs sends to Paddle's pricing API
		 *
		 * @return  Response|null  A Joomla! HTTP response
		 *
		 * @since   7.0.0
		 */
		Recurring::$callbackForUnitTests = function ($urlParams) {
			$response       = new Response();
			$response->code = 200;

			if (in_array(556090, explode(',', $urlParams['product_ids'])))
			{
				$response->body = '{"success":true,"response":{"customer_country":"US","products":[{"product_id":556090,"product_title":"TEST DataCompliance","currency":"USD","vendor_set_prices_included_tax":false,"price":{"gross":9.0,"net":9.0,"tax":0.0},"list_price":{"gross":9.0,"net":9.0,"tax":0.0},"subscription":{"trial_days":0,"interval":"month","frequency":3,"price":{"gross":9.0,"net":9.0,"tax":0.0},"list_price":{"gross":9.0,"net":9.0,"tax":0.0}}}]}}
';

				return $response;
			}

			return null;
		};

		/**
		 * Change the recurring subscription access type for the subscription level we will be using for our tests.
		 *
		 * If none is specified we set it to "renewal".
		 */
		$upsell = 'renewal';

		if (isset($state['_upsell']))
		{
			$upsell = $state['_upsell'] ?? 'renewal';
			unset($state['_upsell']);
		}

		/** @var Levels $level */
		$level = static::$container->factory->model('Levels')->tmpInstance();
		$level->findOrFail($state['id'] ?? 1);

		if ($level->upsell != $upsell)
		{
			// Only save stuff to the database if there's a need to, speeding up the tests.
			$level->bind([
				'upsell' => $upsell,
			])->save();
		}

		/**
		 * Fake our IP address to pretend we are in the USA. Not really used, unless we remove the
		 * Recurring::$callbackForUnitTests callback above.
		 */
		Ip::setIp('72.229.28.185');

		$this->createSubscriptions($subs);

		self::$jUser = self::$users[$loggedIn];
		self::$factory->reset();

		parent::testGetValidationResult($state, $expected, $message);
	}
}
