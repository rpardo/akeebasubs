<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscribe\Validation\Recurring;
use Akeeba\Subscriptions\Tests\Stubs\ValidatorWithSubsTestCase;
use DateInterval;
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
		$jLastYear = clone($jNow);
		$jLastYear->sub(new DateInterval('P365D'));

		// - 181 days ==> expires in 184 days
		$jLastHalfYear = clone($jNow);
		$jLastHalfYear->sub(new DateInterval('P181D'));

		// NOW ==> 365 remaining, 0 used

		// + 184 days ==> expires in 549 and change days
		$jNextHalfYear = clone($jNow);
		$jNextHalfYear->add(new DateInterval('P184D'));

		// + 365 days ==> expires in 730 days
		$jNextYear = clone($jNow);
		$jNextYear->add(new DateInterval('P365D'));

		$testCases = [
			//<editor-fold desc="Renewal = never">
			'Non-recurring level, guest'                                                                                     => [
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
			'Non-recurring level, no sub'                                                                                    => [
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
			'Non-recurring level, with sub'                                                                                  => [
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
			'Non-recurring level, with sub and coupon code'                                                                  => [
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
			'Always recurring level, guest'                                                                                  => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, guest, coupon'                                                                          => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, no sub'                                                                                 => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, no sub, coupon'                                                                         => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,

				],
			],
			'Always recurring level, with sub'                                                                               => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 183, // 365 days - 181 already consumed - 1 day "buffer"
					'blocking_subscription_ids' => null,
				],
			],
			'Always recurring level, with sub, coupon'                                                                       => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 183, // 365 days - 181 already consumed - 1 day "buffer"
					'blocking_subscription_ids' => null,
				],
			],
			'Always recurring level, with renewal'                                                                           => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 549, // Last sub expiring in 549 days and change, rounded down
					'blocking_subscription_ids' => null,
				],
			],
			'Always recurring level, with renewal and coupon'                                                                => [
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
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 549, // Last sub expiring in 549 days and change, rounded down
					'blocking_subscription_ids' => null,
				],
			],
			/**/
			//</editor-fold>

			//<editor-fold desc="Recurring only on renewal (level 2)">
			// Guest, no coupon => NOT recurring
			'Conditional recurring, guest, no coupon'                                                                        => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.0,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, guest, coupon
			'Conditional recurring, guest, coupon'                                                                           => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, no sub => NOT recurring
			'Conditional recurring, logged in, no sub'                                                                       => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.0,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, no sub, coupon => recursion, 0 trial, 0 initial price
			'Conditional recurring, logged in, no sub, coupon'                                                               => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, expired sub => NOT recurring
			'Conditional recurring, logged in, expired sub'                                                                  => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => (clone $jLastYear)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'    => 0,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.0,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, expired sub, coupon => recursion, 0 trial, 0 initial price
			'Conditional recurring, logged in, expired sub, coupon'                                                          => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => (clone $jLastYear)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'    => 0,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, sub expiring ten minutes from now => recursion, 0 trial, 0 initial price
			'Conditional recurring, logged in, sub expiring ten minutes from now'                                            => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => (clone $jLastYear)->add(new DateInterval('PT10M'))->toSql(),
						'enabled'    => 1,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 0,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, one sub 6 months ago => recursion, 183 trial, 0 initial price
			'Conditional recurring, logged in, one sub 6 months ago '                                                        => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 1,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 183,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, one sub 6 months ago, coupon => recursion, 184 trial, 0 initial price
			'Conditional recurring, logged in, one sub 6 months ago, coupon'                                                 => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 1,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 183,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, two subs 6 months ago + 6 months into the future => recursion, 365 + 184 trial, 0 initial price
			'Conditional recurring, logged in, two subs 6 months ago + 6 months into the future'                             => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 1,
					],
					[
						'level'      => 1,
						'publish_up' => $jNextHalfYear->toSql(),
						'enabled'    => 1,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 184 + 365,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, two subs 6 months ago + 6 months into the future, coupon => recursion, 365 + 184 trial, 0 initial price
			'Conditional recurring, logged in, two subs 6 months ago + 6 months into the future, coupon '                    => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 1,
					],
					[
						'level'      => 1,
						'publish_up' => $jNextHalfYear->toSql(),
						'enabled'    => 1,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 184 + 365,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, two subs 375 days ago (expired) + 10 days ago => recursion, 355 trial, 0 initial price
			'Conditional recurring, logged in, two subs 375 days ago (expired) + 10 days ago'                                => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => (clone $jLastYear)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'    => 0,
					],
					[
						'level'      => 1,
						'publish_up' => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'    => 1,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 355,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, two subs 375 days ago (expired) + 10 days ago, coupon => recursion, 355 trial, 0 initial price
			'Conditional recurring, logged in, two subs 375 days ago (expired) + 10 days ago, coupon'                        => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => (clone $jLastYear)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'    => 0,
					],
					[
						'level'      => 1,
						'publish_up' => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'    => 1,
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 355,
					'blocking_subscription_ids' => null,
				],
			],
			// Conditional recurring, logged in, already has a recurring subscription 6 months ago => no recursion, blocking sub (S1)
			'Conditional recurring, logged in, already has a recurring subscription 6 months ago'                            => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 1,
						'update_url' => 'foobar',
						'cancel_url' => 'foobar',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => ['S1'],
				],
			],
			// Conditional recurring, logged in, already has a recurring subscription 6 months ago, coupon does not affect it => no recursion, blocking sub (S1)
			'Conditional recurring, logged in, already has a recurring subscription 6 months ago, coupon does not affect it' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 1,
						'update_url' => 'foobar',
						'cancel_url' => 'foobar',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => ['S1'],
				],
			],
			// Conditional recurring, logged in, regular sub 6 months ago, recurring 6 months into the future => no recursion, blocking sub (S2)
			'Conditional recurring, logged in, regular sub 6 months ago, recurring 6 months into the future'                 => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 1,
					],
					[
						'level'      => 1,
						'publish_up' => $jNextHalfYear->toSql(),
						'enabled'    => 0,
						'update_url' => 'foobar',
						'cancel_url' => 'foobar',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => ['S2'],
				],
			],
			// Conditional recurring, logged in, canceled recurring 6 months ago => no recursion, NO blocking sub
			'Conditional recurring, logged in, canceled recurring 6 months ago'                                              => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled'    => 0,
						'state'      => 'X',
						'update_url' => 'foobar',
						'cancel_url' => 'foobar',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
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
			// Conditional recurring, logged in, canceled recurring 6 months ago, active regular 3 months ago => recursion, 184 + 90 trial, 0 initial price
			'Conditional recurring, logged in, canceled recurring 6 months ago, active regular 3 months ago'                 => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => $jLastHalfYear->toSql(),
						'publish_down' => (clone $jLastHalfYear)->add(new DateInterval('P90D'))->toSql(),
						'enabled'      => 0,
						'state'        => 'X',
						'update_url'   => 'foobar',
						'cancel_url'   => 'foobar',
					],
					[
						'level'      => 1,
						'publish_up' => (clone $jLastHalfYear)->add(new DateInterval('P90D'))->toSql(),
						'enabled'    => 1,
						'state'      => 'C',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renewal',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 0.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 274,
					'blocking_subscription_ids' => null,
				],
			],
			//</editor-fold>

			//<editor-fold desc="Special cases">
			// Purchasing bundle, always recurring, already recurringly subscribed to single product level => blocked sub
			'Purchasing bundle, always recurring, already recurringly subscribed to single product level' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
						'update_url'   => 'foobar',
						'cancel_url'   => 'foobar',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => '',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => [
						'S1'
					],
				],
			],
			'Purchasing bundle, always recurring, already recurringly subscribed to single product level, coupon is ignored' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
						'update_url'   => 'foobar',
						'cancel_url'   => 'foobar',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => 'RECURRING',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => [
						'S1'
					],
				],
			],
			// Purchasing bundle, always recurring, already subscribed to single product level => discount
			'Purchasing bundle, always recurring, already subscribed to single product level' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => '',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556729',
					'initial_price'             => 37.50,
					'recurring_price'           => 11.25,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365, // Since it's a level upgrade user pays for 1 year, then recurring
					'blocking_subscription_ids' => null,
				],
			],
			// Purchasing bundle, always recurring, already subscribed to single product level, has coupon => coupon IGNORED
			'Purchasing bundle, always recurring, already subscribed to single product level, has coupon' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => 'RECURRING',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556729',
					'initial_price'             => 37.50,
					'recurring_price'           => 11.25,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365, // Since it's a level upgrade user pays for 1 year, then recurring
					'blocking_subscription_ids' => null,
				],
			],

			// Purchasing bundle, renew recurring, already recurringly subscribed to single product level => blocked sub
			'Purchasing bundle, renew recurring, already recurringly subscribed to single product level' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
						'update_url'   => 'foobar',
						'cancel_url'   => 'foobar',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => '',
					'_upsell' => 'renew',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => [
						'S1'
					],
				],
			],
			'Purchasing bundle, renew recurring, already recurringly subscribed to single product level, coupon is ignored' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
						'update_url'   => 'foobar',
						'cancel_url'   => 'foobar',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renew',
				],
				'expected' => [
					'recurringId'               => null,
					'initial_price'             => 0.00,
					'recurring_price'           => 0.00,
					'recurring_frequency'       => 0,
					'recurring_type'            => 'day',
					'trial_days'                => 0,
					'blocking_subscription_ids' => [
						'S1'
					],
				],
			],
			'Purchasing bundle, renew recurring, already subscribed to single product level, not recurring' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => '',
					'_upsell' => 'renew',
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
			'Purchasing bundle, renew recurring, already subscribed to single product level, coupon allows recurring' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 1,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P10D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '3',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renew',
				],
				'expected' => [
					'recurringId'               => '556729',
					'initial_price'             => 37.50,
					'recurring_price'           => 11.25,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365, // Since it's a level upgrade user pays for 1 year, then recurring
					'blocking_subscription_ids' => null,
				],
			],

			/**
			 * Here is something I will not test:
			 *
			 * Two subscriptions on level 1 (current + renewal), upgrading to level 3. Level 3 is set to always
			 * recurring.
			 *
			 * The reasonable expectation is that the client pays nothing and his upgraded subscription starts
			 * immediately, then gets recurring payments after a year (or however the prorated start of the subscription
			 * is).
			 *
			 * Reality: I cannot make the subscription level relations give the correct discount and calculate the
			 * correct expiration date without screwing up the most common cases. In these rare cases I'd rather have
			 * the client contact me so I can manually upgrade their subscription. Then they can "purchase" a recurring
			 * renewal which simply activates recurring payments after their manually upgraded subscription expires,
			 * using the previous rules for "always".
			 *
			 * There are limits to my insanity!
			 */

			/**
			 * Downgrade with a subscription level relation, without a coupon.
			 *
			 * User purchased a recurring level 3 subscription 60 days ago. He canceled it, therefore it now appears as
			 * a non-recurring subscription from 60 days ago, expiring in 29 days from now.
			 *
			 * He now tries to buy a subscription on level 1. There's a subscription relation rule for downgrades which
			 * gives zero discount and is of the "after" type. The intent is that the purchased downgrade will get
			 * activated when the current Level 3 subscription expires.
			 *
			 * Therefore I need to have a recurring subscription with a trial period of 29 + 365 days and initial
			 * payment equal to level 1's full price.
			 *
			 * NOTE: Assisted downgrades require a subscription relation with mode "rules" and expiration "after".
			 */
			'Downgrade with a subscription level relation, without a coupon.' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 3,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P60D'))->toSql(),
						'publish_down'   => (clone $jNow)->add(new DateInterval('P29D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 50.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365 + 28,
					'blocking_subscription_ids' => null,
				],
			],
			'Downgrade with a subscription level relation, with a coupon.' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 3,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P60D'))->toSql(),
						'publish_down'   => (clone $jNow)->add(new DateInterval('P29D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'always',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 50.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365 + 28,
					'blocking_subscription_ids' => null,
				],
			],
			'Downgrade with a subscription level relation, target level has "renew" upsell, without a coupon.' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 3,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P60D'))->toSql(),
						'publish_down'   => (clone $jNow)->add(new DateInterval('P29D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => '',
					'_upsell' => 'renew',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 50.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365 + 28,
					'blocking_subscription_ids' => null,
				],
			],
			'Downgrade with a subscription level relation, target level has "renew" upsell, with a coupon.' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'        => 3,
						'publish_up'   => (clone $jNow)->sub(new DateInterval('P60D'))->toSql(),
						'publish_down'   => (clone $jNow)->add(new DateInterval('P29D'))->toSql(),
						'enabled'      => 1,
						'state'        => 'C',
					],
				],
				'state'    => [
					'id'      => '1',
					'coupon'  => 'RECURRING',
					'_upsell' => 'renew',
				],
				'expected' => [
					'recurringId'               => '556090',
					'initial_price'             => 50.00,
					'recurring_price'           => 11.16,
					'recurring_frequency'       => 3,
					'recurring_type'            => 'month',
					'trial_days'                => 365 + 28,
					'blocking_subscription_ids' => null,
				],
			],

			// Forever subscription => always NOT recurring
			// Fixed expiration date subscription => always NOT recurring
			// Always recurring, new sub, discount coupon  => recurring sub with a discounted initial price, no tax included
			// Always recurring, new sub, discount + recurring access coupon => recurring sub with no initial price, tax included

			// TODO When a recurring sub is canceled mark it as NON-recurring so it doesn't screw up calculations.
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
				$response->body = '{"success":true,"response":{"customer_country":"GR","products":[{"product_id":556090,"product_title":"TEST DataCompliance","currency":"EUR","vendor_set_prices_included_tax":false,"price":{"gross":11.16,"net":9.0,"tax":2.16},"list_price":{"gross":11.16,"net":9.0,"tax":2.16},"subscription":{"trial_days":0,"interval":"month","frequency":3,"price":{"gross":11.16,"net":9.0,"tax":2.16},"list_price":{"gross":11.16,"net":9.0,"tax":2.16}}}]}}
';

				return $response;
			}

			if (in_array(556729, explode(',', $urlParams['product_ids'])))
			{
				$response->body = '{"success":true,"response":{"customer_country":"GR","products":[{"product_id":556729,"product_title":"TEST Bundle","currency":"EUR","vendor_set_prices_included_tax":false,"price":{"gross":13.95,"net":11.25,"tax":2.70},"list_price":{"gross":13.95,"net":11.25,"tax":2.70},"subscription":{"trial_days":0,"interval":"month","frequency":3,"price":{"gross":13.95,"net":11.25,"tax":2.70},"list_price":{"gross":13.95,"net":11.25,"tax":2.70}}}]}}
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
		 * Fake our IP address to pretend we are in Greece. Not really used, unless we remove the
		 * Recurring::$callbackForUnitTests callback above.
		 */
		Ip::setIp('109.242.90.176');

		$this->createSubscriptions($subs);

		self::$jUser = self::$users[$loggedIn];
		self::$factory->reset();

		parent::testGetValidationResult($state, $expected, $message);
	}

	/**
	 * Perform the assertion(s) required for this test
	 *
	 * @param mixed  $expected Expected value
	 * @param mixed  $actual   Actual validator result
	 * @param string $message  Message to show on failure
	 *
	 * @return  void
	 */
	public function performAssertion($expected, $actual, $message)
	{
		if (!empty($expected['blocking_subscription_ids']))
		{
			$expected['blocking_subscription_ids'] = self::translateSubToId($expected['blocking_subscription_ids']);
		}

		parent::performAssertion($expected, $actual, $message);
	}

}
