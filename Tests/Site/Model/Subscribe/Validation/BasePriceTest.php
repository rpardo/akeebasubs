<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Site\Model\Subscriptions;
use Akeeba\Subscriptions\Tests\Stubs\ValidatorTestCase;

/**
 * Test the BasePrice validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\BasePrice
 */
class BasePriceTest extends ValidatorTestCase
{
	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'BasePrice';

		// Create the base objects
		parent::setUpBeforeClass();

		// Modify subscription #3 (user5, active sub for level 3) to have publish_up/down dates within a year
		/** @var Subscriptions $sub */
		$sub = self::$container->factory->model('Subscriptions')->tmpInstance();
		$sub->findOrFail(3);
		$sub->publish_up = self::$container->platform->getDate('@' . (time() - 180 * 24 * 3600));
		$sub->publish_down = self::$container->platform->getDate('@' . (time() + 180 * 24 * 3600));
		$sub->created_on = $sub->publish_up;
		$sub->save();
	}

	public function getTestData()
	{
		return [
			'Invalid level ID' => [
				'loggedIn'        => 'guest',
				'state'           => [
					'id' => 99999999
				],
				'expected'        => [
					'levelNet'    => 0.0,
					'isRecurring' => false
				],
				'message'         => 'Invalid level ID'
			],
			'Guest user, single product' => [
				'loggedIn'        => 'guest',
				'state'           => [
					'id' => 1
				],
				'expected'        => [
					'levelNet'    => 50.0,
					'isRecurring' => false
				],
				'message'         => 'Guest user'
			],
			'Guest user, bundle product' => [
				'loggedIn'        => 'guest',
				'state'           => [
					'id' => 3
				],
				'expected'        => [
					'levelNet'    => 75.0,
					'isRecurring' => false
				],
				'message'         => 'Guest user'
			],
			'User without subscription' => [
				'loggedIn'        => 'user4',
				'state'           => [
					'id' => 1
				],
				'expected'        => [
					'levelNet'    => 50.0,
					'isRecurring' => false
				],
				'message'         => 'User without subscription'
			],
			'User with expired subscription' => [
				'loggedIn'        => 'user1',
				'state'           => [
					'id' => 1
				],
				'expected'        => [
					'levelNet'    => 50.0,
					'isRecurring' => false
				],
				'message'         => 'User with expired subscription'
			],
			'User with active subscription' => [
				'loggedIn'        => 'user5',
				'state'           => [
					'id' => 1
				],
				'expected'        => [
					'levelNet'    => 50.0,
					'isRecurring' => false
				],
				'message'         => 'User with active subscription'
			],
			'Free subscription' => [
				'loggedIn'        => 'guest',
				'state'           => [
					'id' => 6
				],
				'expected'        => [
					'levelNet'    => 0.0,
					'isRecurring' => false
				],
				'message'         => 'Free subscription'
			]
		];
	}

	/**
	 * Test the validator
	 *
	 * @dataProvider getTestData
	 */
	public function testGetValidationResult($loggedIn, $state, $expected, $message)
	{
		self::$jUser = self::$users[ $loggedIn ];
		static::$factory->reset();

		parent::testGetValidationResult($state, $expected, $message);
	}
}
