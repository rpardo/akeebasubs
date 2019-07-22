<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Tests\Stubs\ValidatorTestCase;

/**
 * Test the PersonalInformation validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\PersonalInformation
 */
class PersonalInformationTest extends ValidatorTestCase
{
	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'PersonalInformation';

		// Create the base objects
		parent::setUpBeforeClass();
	}

	public function getTestData()
	{
		return [
			'All information empty, guest' => [
				'componentParams' => [
				],
				'loggedIn'        => 'guest',
				'state'           => [
					'name'         => '',
					'email'        => '',
					'email2'       => '',
					'coupon'       => '',
					'accept_terms' => 0,
				],
				'expected'        => [
					'name'   => false,
					'email'  => false,
					'email2' => false,
					'coupon' => false,
					'tos'    => false,
				],
				'message'         => 'All information empty, guest',
			],
			/**
			 * We no longer pre-fill the personal information of logged in users if they submit no data.
			 */
			'All information empty, user1' => [
				'componentParams' => [
				],
				'loggedIn'        => 'user1',
				'state'           => [
					'name'         => '',
					'email'        => '',
					'email2'       => '',
					'coupon'       => '',
					'accept_terms' => 0,
				],
				'expected'        => [
					'name'   => false,
					'email'  => false,
					'email2' => false,
					'coupon' => false,
					'tos'    => false,
				],
				'message'         => 'All information empty, user1',
			],
			'Everything valid'             => [
				'componentParams' => [
				],
				'loggedIn'        => 'guest',
				'state'           => [
					'name'         => 'Foo Bar',
					'email'        => 'foobar@example.com',
					'email2'       => 'foobar@example.com',
					'coupon'       => 'VALIDALL',
					'accept_terms' => 1,
				],
				'expected'        => [
					'name'   => true,
					'email'  => true,
					'email2' => true,
					'coupon' => true,
					'tos'    => true,
				],
				'message'         => 'Everything valid',
			],
			'Valid except name'             => [
				'componentParams' => [
				],
				'loggedIn'        => 'guest',
				'state'           => [
					'name'         => '',
					'email'        => 'foobar@example.com',
					'email2'       => 'foobar@example.com',
					'coupon'       => 'VALIDALL',
					'accept_terms' => 1,
				],
				'expected'        => [
					'name'   => false,
					'email'  => true,
					'email2' => true,
					'coupon' => true,
					'tos'    => true,
				],
				'message'         => 'Valid except name',
			],
			'Valid except email'             => [
				'componentParams' => [
				],
				'loggedIn'        => 'guest',
				'state'           => [
					'name'         => 'Foo Bar',
					'email'        => '',
					'email2'       => 'barghfargh@example.net',
					'coupon'       => 'VALIDALL',
					'accept_terms' => 1,
				],
				'expected'        => [
					'name'   => true,
					'email'  => false,
					'email2' => false,
					'coupon' => true,
					'tos'    => true,
				],
				'message'         => 'Valid except email',
			],
			'Valid except mismatching email'             => [
				'componentParams' => [
				],
				'loggedIn'        => 'guest',
				'state'           => [
					'name'         => 'Foo Bar',
					'email'        => 'foobar@example.com',
					'email2'       => 'barghfargh@example.net',
					'coupon'       => 'VALIDALL',
					'accept_terms' => 1,
				],
				'expected'        => [
					'name'   => true,
					'email'  => true,
					'email2' => false,
					'coupon' => true,
					'tos'    => true,
				],
				'message'         => 'Valid except mismatching email',
			],
			'Valid except coupon'             => [
				'componentParams' => [
				],
				'loggedIn'        => 'guest',
				'state'           => [
					'name'         => 'Foo Bar',
					'email'        => 'foobar@example.com',
					'email2'       => 'foobar@example.com',
					'coupon'       => 'IDONOTEXIST',
					'accept_terms' => 1,
				],
				'expected'        => [
					'name'   => true,
					'email'  => true,
					'email2' => true,
					'coupon' => false,
					'tos'    => true,
				],
				'message'         => 'Valid except coupon',
			],
			'Valid except ToS'             => [
				'componentParams' => [
				],
				'loggedIn'        => 'guest',
				'state'           => [
					'name'         => 'Foo Bar',
					'email'        => 'foobar@example.com',
					'email2'       => 'foobar@example.com',
					'coupon'       => 'VALIDALL',
					'accept_terms' => 0,
				],
				'expected'        => [
					'name'   => true,
					'email'  => true,
					'email2' => true,
					'coupon' => true,
					'tos'    => false,
				],
				'message'         => 'Valid except ToS',
			]

		];
	}

	/**
	 * Test the validator
	 *
	 * @dataProvider getTestData
	 */
	public function testGetValidationResult($componentParams, $loggedIn, $state, $expected, $message)
	{
		foreach ($componentParams as $k => $v)
		{
			if (static::$container->params->get($k) != $v)
			{
				static::$container->params->set($k, $v);
				static::$container->params->save();
			}
		}

		self::$jUser = self::$users[$loggedIn];
		static::$factory->reset();

		parent::testGetValidationResult($state, $expected, $message);
	}

	public function performAssertion($expected, $actual, $message)
	{
		$expected = array_merge($expected, [
			'rawDataForDebug' => (array) self::$state,
		]);

		parent::performAssertion($expected, $actual, $message); // TODO: Change the autogenerated stub
	}


}
