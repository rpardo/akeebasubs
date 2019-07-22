<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Tests\Stubs\ValidatorWithSubsTestCase;
use FOF30\Date\Date;

/**
 * Test the Price validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\Price
 */
class PriceTest extends ValidatorWithSubsTestCase
{

	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'Price';

		// Create the base objects
		parent::setUpBeforeClass();
	}

	public function getTestData()
	{
		$jNow = new Date();

		return [

			'Guest, no discount' => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'           => '1',
					'coupon'       => '',
				],
				'expected' => [
					'net'        => 50.0,
					'realnet'    => 50.0,
					'discount'   => 0.0,
					'taxrate'    => 0.0,
					'tax'        => 0.0,
					'gross'      => 50.00,
					'usecoupon'  => 0,
					'useauto'    => 0,
					'couponid'   => 0,
					'upgradeid'  => 0,
					'oldsub'     => null,
					'allsubs'    => [],
					'expiration' => 'overlap',
				],
				'message'  => 'With sign-up, Guest, no discount, EU, business, VIES registered (no VAT)',
			],

			'Guest, coupon discount' => [
				'loggedIn' => 'guest',
				'subs'     => [],
				'state'    => [
					'id'           => '1',
					'coupon'       => 'VALIDALL',
				],
				'expected' => [
					'net'        => 50.0, // Prediscount net
					'realnet'    => 50.0, // Prediscount net
					'discount'   => 25.0, // Discount
					'taxrate'    => 0.0, // Tax rate in % points
					'tax'        => 0.0, // Tax amount = taxrate * (net - discount)
					'gross'      => 25.00, // net - discount
					'usecoupon'  => 1,
					'useauto'    => 0,
					'couponid'   => 3,
					'upgradeid'  => 0,
					'oldsub'     => null,
					'allsubs'    => [],
					'expiration' => 'overlap',
				],
				'message'  => 'Guest, coupon discount',
			],

			'Logged in, no discount' => [
				'loggedIn' => 'guineapig',
				'subs'     => [],
				'state'    => [
					'id'           => '1',
					'coupon'       => '',
				],
				'expected' => [
					'net'        => 50.0,
					'realnet'    => 50.0,
					'discount'   => 0.0,
					'taxrate'    => 0.0,
					'tax'        => 0.0,
					'gross'      => 50.0,
					'usecoupon'  => 0,
					'useauto'    => 0,
					'couponid'   => 0,
					'upgradeid'  => 0,
					'oldsub'     => null,
					'allsubs'    => [],
					'expiration' => 'overlap',
				],
				'message'  => 'Logged in, no discount'
			],

			'Upgrade rule' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 1,
						'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id'           => '1',
					'coupon'       => '',
				],
				'expected' => [
					'net'        => 50.0,
					'realnet'    => 50.0,
					'discount'   => 20.0,
					'taxrate'    => 0.0,
					'tax'        => 0.0,
					'gross'      => 30.0,
					'usecoupon'  => 0,
					'useauto'    => 1,
					'couponid'   => 0,
					'upgradeid'  => 1,
					'oldsub'     => null,
					'allsubs'    => [],
					'expiration' => 'overlap',
				],
				'message'  => 'Upgrade rule'
			],

			'Logged in, coupon + best upgrade, coupon wins' => [
				'loggedIn' => 'guineapig',
				'subs'     => [
					[
						'level' => 6,
					    'publish_up' => $jNow->toSql()
					]
				],
				'state'    => [
					'id'           => '2',
					'coupon'       => 'VALIDALL',
				],
				'expected' => [
					'net'        => 50.0,
					'realnet'    => 50.0,
					'discount'   => 25.0,
					'taxrate'    => 0.0,
					'tax'        => 0.0,
					'gross'      => 25.0,
					'usecoupon'  => 1,
					'useauto'    => 0,
					'couponid'   => 3,
					'upgradeid'  => 0,
					'oldsub'     => 'S1',
					'allsubs'    => ['S1'],
					'expiration' => 'replace',
				],
				'message'  => 'Logged in, coupon + best upgrade, coupon wins'
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
		$expected['oldsub']  = self::translateSubToId($expected['oldsub']);
		$expected['allsubs'] = self::translateSubToId($expected['allsubs']);

		parent::performAssertion($expected, $actual, $message);
	}
}
