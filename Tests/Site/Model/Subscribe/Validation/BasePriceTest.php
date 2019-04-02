<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

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
					'basePrice'   => 0.0, // Base price, including sign-up and surcharges
					'isRecurring' => false
				],
				'message'         => 'Invalid level ID'
			],
			'Guest user' => [
				'loggedIn'        => 'guest',
				'state'           => [
					'id' => 1
				],
				'expected'        => [
					'levelNet'    => 100.0,
					'basePrice'   => 100.0, // Base price, including sign-up and surcharges
					'isRecurring' => false
				],
				'message'         => 'Guest user'
			],
			'User without subscription' => [
				'loggedIn'        => 'forcedvat',
				'state'           => [
					'id' => 1
				],
				'expected'        => [
					'levelNet'    => 100.0,
					'basePrice'   => 100.0, // Base price, including sign-up and surcharges
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
					'levelNet'    => 100.0,
					'basePrice'   => 100.0, // Base price, including sign-up and surcharges
					'isRecurring' => false
				],
				'message'         => 'User with expired subscription'
			],
			'User with active subscription' => [
				'loggedIn'        => 'business',
				'state'           => [
					'id' => 1
				],
				'expected'        => [
					'levelNet'    => 100.0,
					'basePrice'   => 100.0, // Base price, including sign-up and surcharges
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
					'basePrice'   => 0.0, // Base price, including sign-up and surcharges
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
