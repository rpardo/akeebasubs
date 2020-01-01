<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Tests\Stubs\ValidatorWithSubsTestCase;
use FOF30\Date\Date;

/**
 * Test the BestUpgradeDiscount validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\BestUpgradeDiscount
 */
class BestUpgradeDiscountTest extends ValidatorWithSubsTestCase
{
	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'BestUpgradeDiscount';

		// Create the base objects
		parent::setUpBeforeClass();
	}

	public function getTestData()
	{
		$jNow = new Date();

		$jLastYear = clone $jNow;
		$jLastYear->sub(new \DateInterval('P1Y1D'));

		$jTwoYearsAgo = clone $jNow;
		$jTwoYearsAgo->sub(new \DateInterval('P2Y'));

		$jLastHalfYear = clone($jNow);
		$jLastHalfYear->sub(new \DateInterval('P181D'));

		$j370DaysAgo = clone($jNow);
		$j370DaysAgo->sub(new \DateInterval('P370D'));

		return [
			'No upgrade' => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => null,
					'value'      => 0.0,
				    'combine'    => false
				],
				'message'  => 'No upgrade'
			],
			'DATACOMPLIANCE renewal – caught by expired subscription rule' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 1,
					    'publish_up' => $jLastYear->toSql(),
					    'enabled' => 0,
					]
				],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => 5,
					'value'      => 10.0
					,
					'combine'    => false
				],
				'message'  => 'DATACOMPLIANCE renewal – caught by expired subscription rule'
			],
			'DATACOMPLIANCE renewal, first six months' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 1,
					    'publish_up' => $jNow->toSql(),
					]
				],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => 1,
					'value'      => 20.0,
					'combine'    => false
				],
				'message'  => 'DATACOMPLIANCE renewal, first six months'
			],
			'DATACOMPLIANCE renewal, last six months' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 1,
					    'publish_up' => $jLastHalfYear->toSql(),
					]
				],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => 1,
					'value'      => 20.0,
					'combine'    => false
				],
				'message'  => 'DATACOMPLIANCE renewal, last six months'
			],
			'CONTACTUS renewal, last six months, different price for lastpercent' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 2,
					    'publish_up' => $jLastHalfYear->toSql(),
						'net_amount' => 40,
					]
				],
				'state'    => [
					'id' => '2',
				],
				'expected' => [
					'upgrade_id' => 2,
					'value'      => 16.0,
					'combine'    => false
				],
				'message'  => 'CONTACTUS renewal, last six months, different price for lastpercent'
			],
			'DATACOMPLIANCE to LEVEL4, fixed price' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 1,
					    'publish_up' => $jLastYear->toSql(),
					]
				],
				'state'    => [
					'id' => '4',
				],
				'expected' => [
					'upgrade_id' => 6,
					'value'      => 5.00,
					'combine'    => false
				],
				'message'  => 'DATACOMPLIANCE to BUNDLE, fixed price'
			],
			'DATACOMPLIANCE renewal, no subscription (rule not applied)' => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => 0,
					'value'      => 0.00,
					'combine'    => false
				],
				'message'  => 'DATACOMPLIANCE to BUNDLE, no subscription (rule not applied)'
			],
			'LEVEL4 to DATACOMPLIANCE, 10% (precondition for combined test)' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 4,
						'publish_up' => $jLastHalfYear->toSql(),
					]
				],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => 7,
					'value'      => 5.00,
					'combine'    => true
				],
				'message'  => 'LEVEL4 to DATACOMPLIANCE, 10% (precondition for combined test)'
			],
			'FREE to DATACOMPLIANCE, 30% (precondition for combined test)' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 6,
						'publish_up' => $jLastHalfYear->toSql(),
					]
				],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => 4,
					'value'      => 15.00,
					'combine'    => true
				],
				'message'  => 'FREE to DATACOMPLIANCE, 30% (precondition for combined test)'
			],
			'LEVEL4 and FREE to DATACOMPLIANCE, combined to 40% (only the last rule is reported as active)' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 4,
						'publish_up' => $jLastHalfYear->toSql(),
					],
					[
						'level' => 6,
						'publish_up' => $jLastHalfYear->toSql(),
					]
				],
				'state'    => [
					'id' => '1',
				],
				'expected' => [
					'upgrade_id' => 7,
					'value'      => 20.00,
					'combine'    => true
				],
				'message'  => 'LEVEL4 and FREE to DATACOMPLIANCE, combined to 40% (only the last rule is reported as active)'
			],
			'Combine two expired subscription discounts – only validated by UpgradeExpiredDiscount' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 2,
						'publish_up' => $jLastYear->toSql(),
					    'enabled' => 0
					],
					[
						'level' => 5,
						'publish_up' => $jLastYear->toSql(),
					    'enabled' => 0
					],
				],
				'state'    => [
					'id' => '5',
				],
				'expected' => [
					'upgrade_id' => 9,
					'value'      => 10.00,
					'combine'    => true
				],
				'message'  => 'Combine two expired subscription discounts – only validated by UpgradeExpiredDiscount'
			],

			// Combine active and expired discounts
			'Combine active and expired discounts' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 4,
						'publish_up' => $jLastHalfYear->toSql(),
					    'enabled' => 1
					],
					[
						'level' => 5,
						'publish_up' => $jLastYear->toSql(),
					    'enabled' => 0
					],
				],
				'state'    => [
					'id' => '5',
				],
				'expected' => [
					'upgrade_id' => 10,
					'value'      => 10.00,
					'combine'    => true
				],
				'message'  => 'Combine active and expired discounts'
			],

			// Unpublished rule
			'Unpublished rule' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 6,
						'publish_up' => $jLastYear->toSql(),
					],
				],
				'state'    => [
					'id' => '5',
				],
				'expected' => [
					'upgrade_id' => 0,
					'value'      => 0.00,
					'combine'    => false
				],
				'message'  => 'Unpublished rule'
			],

			// One expired rule, one active rule, no combine, pick the best one
			'One expired rule, one active rule, no combine, pick the best one' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 1,
						'publish_up' => $jLastHalfYear->toSql(),
						'enabled' => 1
					],
					[
						'level' => 1,
						'publish_up' => $jLastYear->toSql(),
						'enabled' => 0
					],
				],
				'state'    => [
					'id' => '5',
				],
				'expected' => [
					'upgrade_id' => 12,
					'value'      => 15.00,
					'combine'    => false
				],
				'message'  => 'One expired rule, one active rule, no combine, pick the best one'
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
}
