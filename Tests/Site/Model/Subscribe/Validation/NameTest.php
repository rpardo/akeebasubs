<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Tests\Stubs\ValidatorTestCase;

/**
 * Test the Name validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\Name
 */
class NameTest extends ValidatorTestCase
{
	public static function setUpBeforeClass()
	{
		self::$validatorType = 'Name';

		parent::setUpBeforeClass();
	}

	public function getTestData()
	{
		return [
			'Empty names are NOT allowed' => [
				'state' => [
					'name' => ''
				],
				'expected' => false,
				'message' => 'Empty names are NOT allowed'
			],
			/**
			 * Reason for change: Paddle is dealing with the legally required invoicing information. We don't really
			 * need the client's full name, just something to call them when they communicate with us. They could use
			 * their real name or a handle which could conceivably be a single word.
			 */
			'Single word names are allowed now' => [
				'state' => [
					'name' => 'Foobar'
				],
				'expected' => true,
				'message' => 'Single word names are allowed now'
			],
			'Two word names are allowed' => [
				'state' => [
					'name' => 'Foo bar'
				],
				'expected' => true,
				'message' => 'Two word names are allowed'
			],
			'Three word names are allowed' => [
				'state' => [
					'name' => 'Foo bar baz'
				],
				'expected' => true,
				'message' => 'Three word names are allowed'
			],
			'Single letter names with two parts are allowed' => [
				'state' => [
					'name' => 'a b'
				],
				'expected' => true,
				'message' => 'Single letter names with two parts are allowed'
			],
		];
	}

	/**
	 * Test the validator
	 *
	 * @dataProvider getTestData
	 */
	public function testGetValidationResult($state, $expected, $message)
	{
		parent::testGetValidationResult($state, $expected, $message);
	}


}
