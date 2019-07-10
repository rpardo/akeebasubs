<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Site\Model\Subscriptions;
use Akeeba\Subscriptions\Tests\Stubs\ValidatorTestCase;
use Akeeba\Subscriptions\Tests\Stubs\ValidatorWithSubsTestCase;
use FOF30\Date\Date;
use FOF30\Utils\Ip;

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
			'vendor_id' => $akeebasubsTestConfig['vendor_id'],
			'vendor_auth_code' => $akeebasubsTestConfig['vendor_auth_code'],
		]);
		static::$container->params->save();

	}

	public function getTestData()
	{
		$jNow = new Date();

		$jLastYear = clone $jNow;
		$jLastYear->sub(new \DateInterval('P1Y1D'));

		$jLastHalfYear = clone($jNow);
		$jLastHalfYear->sub(new \DateInterval('P181D'));

		$j370DaysAgo = clone($jNow);
		$j370DaysAgo->sub(new \DateInterval('P370D'));

		$testCases = [
			//<editor-fold desc="Non-recurring level (3)">
			'Non-recurring level, guest' => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'     => '3',
					'coupon' => '',
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
			'Non-recurring level, no sub' => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'     => '3',
					'coupon' => '',
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
			'Non-recurring level, with sub' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 3,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'     => '3',
					'coupon' => '',
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
			'Non-recurring level, with sub and coupon code' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 3,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'     => '3',
					'coupon' => 'RECURSION',
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
			'Always recurring level, guest' => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'     => '1',
					'coupon' => '',
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
			/**
			'Always recurring level, no sub' => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'     => '2',
					'coupon' => '',
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
			'Always recurring level, with sub' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 3,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'     => '2',
					'coupon' => '',
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
			'Always recurring level, with sub and coupon code' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 3,
						'publish_up' => $jLastHalfYear->toSql(),
					],
				],
				'state'    => [
					'id'     => '2',
					'coupon' => 'RECURSION',
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
			/**/
			//</editor-fold>

		];

		foreach ($testCases as $message => &$testCase)
		{
			$testCase = array_merge($testCase, [
				'message' => $message
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
		Ip::setIp('85.72.158.96');

		$this->createSubscriptions($subs);

		self::$jUser = self::$users[ $loggedIn ];
		self::$factory->reset();

		parent::testGetValidationResult($state, $expected, $message);
	}
}
