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
 * Test the UpgradeDiscount validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\UpgradeExpiredDiscount
 */
class UpgradeExpiredDiscountTest extends ValidatorWithSubsTestCase
{
	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'UpgradeExpiredDiscount';

		// Create the base objects
		parent::setUpBeforeClass();
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
			'DATACOMPLIANCE renewal, first six months, not expired' => [
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
					'upgrade_id' => 0,
					'value'      => 0.0,
					'combine'    => false
				],
				'message'  => 'DATACOMPLIANCE renewal, first six months, not expired'
			],
			'DATACOMPLIANCE renewal, last six months, not expired' => [
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
					'upgrade_id' => 0,
					'value'      => 0.0,
					'combine'    => false
				],
				'message'  => 'DATACOMPLIANCE renewal, last six months, not expired'
			],
			'CONTACTUS renewal, last six months, different price for lastpercent (not applied; not expired)' => [
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
					'upgrade_id' => 0,
					'value'      => 0.0,
					'combine'    => false
				],
				'message'  => 'CONTACTUS renewal, last six months, different price for lastpercent (not applied; not expired)'
			],
			'LEVEL4 and FREE to DATACOMPLIANCE, combined to 40% (not applied; not expired)' => [
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
					'upgrade_id' => 0,
					'value'      => 0.00,
					'combine'    => false
				],
				'message'  => 'LEVEL4 and FREE to DATACOMPLIANCE, combined to 40% (not applied; not expired)'
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
			'Combine active and expired discounts, keep expired' => [
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
					'upgrade_id' => 9,
					'value'      => 5.00,
					'combine'    => true
				],
				'message'  => 'Combine active and expired discounts, keep expired'
			],

			'One expired rule, one active rule, no combine, pick expired' => [
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
					'upgrade_id' => 13,
					'value'      => 5.00,
					'combine'    => false
				],
				'message'  => 'One expired rule, one active rule, no combine, pick expired'
			]
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
