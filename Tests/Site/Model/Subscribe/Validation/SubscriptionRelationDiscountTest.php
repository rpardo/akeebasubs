<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Admin\Model\Relations;
use Akeeba\Subscriptions\Tests\Stubs\ValidatorWithSubsTestCase;
use FOF30\Date\Date;

/**
 * Test the SubscriptionRelationDiscount validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\SubscriptionRelationDiscount
 */
class SubscriptionRelationDiscountTest extends ValidatorWithSubsTestCase
{
	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'SubscriptionRelationDiscount';

		// Create the base objects
		parent::setUpBeforeClass();
	}

	public function getTestData()
	{
		$jNow = new Date();

		$jLastYear = clone $jNow;
		$jLastYear->sub(new \DateInterval('P1Y1D'));

		$j13MonthsAgo = clone $jNow;
		$j13MonthsAgo->sub(new \DateInterval('P1Y1M'));

		$jNextYear = clone $jNow;
		$jNextYear->add(new \DateInterval('P1Y1D'));

		$jLastHalfYear = clone($jNow);
		$jLastHalfYear->sub(new \DateInterval('P181D'));

		$jLastMonth = clone($jNow);
		$jLastMonth->sub(new \DateInterval('P31D'));

		$jThreeMonthsAgo = clone($jNow);
		$jThreeMonthsAgo->sub(new \DateInterval('P92D'));

		$jElevenMonthsAgo = clone($jNow);
		$jElevenMonthsAgo->sub(new \DateInterval('P335D'));

		$j370DaysAgo = clone($jNow);
		$j370DaysAgo->sub(new \DateInterval('P370D'));

		return [
			'Not logged in, no relation' => [
				'loggedIn' => 'guest',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'discount' => 0.0,
					'relation' => null,
					'oldsub'   => null,
					'allsubs'  => [],
				],
				'message'  => 'Not logged in, no relation'
			],
			'No relation' => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'discount' => 0.0,
					'relation' => null,
					'oldsub'   => null,
					'allsubs'  => [],
				],
				'message'  => 'No relation'
			],
			'Relation with upgrade rules, replace' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 2,
						'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id' => '2',
				],
				'expected' => [
					'discount' => 40.0,
					'relation' => 5,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1'],
				],
				'message'  => 'Relation with upgrade rules, replace'
			],
			'Relation with upgrade rules, extend' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 4,
						'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id' => '5',
				],
				'expected' => [
					'discount' => 5.0,
					'relation' => 6,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1'],
				],
				'message'  => 'Relation with upgrade rules, extend'
			],
			'Relation with upgrade rules, overlap' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id' => '5',
				],
				'expected' => [
					'discount' => 15.0,
					'relation' => 7,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1'],
				],
				'message'  => 'Relation with upgrade rules, overlap'
			],
			'Relation with fixed discount, value' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 6,
						'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id' => '5',
				],
				'expected' => [
					'discount' => 19.66,
					'relation' => 8,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1'],
				],
				// FREEWITHSIGNUP to LEVEL1
				'message'  => 'Relation with fixed discount, value'
			],
			'Relation with fixed discount, percent' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 4,
						'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id' => '4',
				],
				'expected' => [
					'discount' => 2.50,
					'relation' => 9,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1'],
				],
				'message'  => 'Relation with fixed discount, percent'
			],
			'Relation with flexible discount, value, round down – high threshold' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jNow->toSql()
					],
					[
						'level'      => 1,
						'publish_up' => $jNextYear->toSql(),
					    'enabled'    => 0
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 37.5, // High threshold
					'relation' => 1,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1', 'S2'],
				],
				// FREE to LEVEL1
				'message'  => 'Relation with flexible discount, value, round down – high threshold'
			],
			'Relation with flexible discount, value, round down – during flexible period' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastHalfYear->toSql()
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 18,
					'relation' => 1,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1'],
				],
				// FREE to LEVEL1
				'message'  => 'Relation with flexible discount, value, round down – during flexible period'
			],
			'Relation with flexible discount, value, round down – low threshold' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jElevenMonthsAgo->toSql()
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 5, // low threshold
					'relation' => 1,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1'],
				],
				// FREE to LEVEL1
				'message'  => 'Relation with flexible discount, value, round down – low threshold'
			],
			'Relation with flexible discount, include renewals, value, round down – hitting high threshold' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jElevenMonthsAgo->toSql(),
					    'enabled'    => 1,
					],
					[
						'level'      => 1,
						'publish_up' => $jNextYear->toSql(),
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 37.5, // low threshold
					'relation' => 1,
					'oldsub'   => 'S1',
					'allsubs'  => ['S1','S2'],
				],
				'message'  => 'Relation with flexible discount, include renewals, value, round down – hitting high threshold'
			],
			'Combined flexi relation, high threshold for both' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jLastMonth->toSql(),
					    'enabled'    => 1,
					],
					[
						'level'      => 2,
						'publish_up' => $jLastMonth->toSql(),
						'enabled'    => 1,
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 75,
					'relation' => 2,
					'oldsub'   => 'S2',
					'allsubs'  => ['S1','S2'],
				],
				'message'  => 'Combined flexi relation, high threshold for both'
			],
			'Combined flexi relation, low threshold for both' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jElevenMonthsAgo->toSql(),
					    'enabled'    => 1,
					],
					[
						'level'      => 2,
						'publish_up' => $jElevenMonthsAgo->toSql(),
						'enabled'    => 1,
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 10, // low threshold
					'relation' => 2,
					'oldsub'   => 'S2',
					'allsubs'  => ['S1','S2'],
				],
				'message'  => 'Combined flexi relation, low threshold for both'
			],
			'Combined flexi relation, in the middle for both' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jThreeMonthsAgo->toSql(),
					    'enabled'    => 1,
					],
					[
						'level'      => 2,
						'publish_up' => $jThreeMonthsAgo->toSql(),
						'enabled'    => 1,
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 54, // low threshold
					'relation' => 2,
					'oldsub'   => 'S2',
					'allsubs'  => ['S1','S2'],
				],
				'message'  => 'Combined flexi relation, in the middle for both'
			],
			'Combined flexi relation, middle and low' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jThreeMonthsAgo->toSql(),
					    'enabled'    => 1,
					],
					[
						'level'      => 2,
						'publish_up' => $jElevenMonthsAgo->toSql(),
						'enabled'    => 1,
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 32,
					'relation' => 2,
					'oldsub'   => 'S2',
					'allsubs'  => ['S1','S2'],
				],
				'message'  => 'Combined flexi relation, middle and low'
			],
			'Combined flexi relation, middle and high' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level'      => 1,
						'publish_up' => $jNow->toSql(),
					    'enabled'    => 1,
					],
					[
						'level'      => 2,
						'publish_up' => $jElevenMonthsAgo->toSql(),
						'enabled'    => 1,
					]
				],
				'state'    => [
					'id' => '3',
				],
				'expected' => [
					'discount' => 42.5,
					'relation' => 2,
					'oldsub'   => 'S2',
					'allsubs'  => ['S1','S2'],
				],
				'message'  => 'Combined flexi relation, middle and high'
			],
		];
	}

	/**
	 * Test the validator
	 *
	 * @dataProvider getTestData
	 */
	public function testGetValidationResult($loggedIn, $subs, $state, $expected, $message)
	{
		$this->createSubscriptions($subs);

		self::$jUser = self::$users[ $loggedIn ];
		self::$factory->reset();

		parent::testGetValidationResult($state, $expected, $message);
	}

	/**
	 * Perform the assertion(s) required for this test
	 *
	 * @param   mixed  $expected Expected value
	 * @param   mixed  $actual   Actual validator result
	 * @param   string $message  Message to show on failure
	 *
	 * @return  void
	 */
	public function performAssertion($expected, $actual, $message)
	{
		$expected['oldsub'] = self::translateSubToId($expected['oldsub']);
		$expected['allsubs'] = self::translateSubToId($expected['allsubs']);

		if ($actual['relation'] instanceof Relations)
		{
			$actual['relation'] = $actual['relation']->akeebasubs_relation_id;
		}

		parent::performAssertion($expected, $actual, $message);
	}


}
