<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Site\Model\Subscribe\Validation;

use Akeeba\Subscriptions\Tests\Stubs\ValidatorTestCase;

/**
 * Test the CouponDiscount validator
 *
 * @covers Akeeba\Subscriptions\Site\Model\Subscribe\Validation\CouponDiscount
 */
class CouponDiscountTest extends ValidatorTestCase
{
	public static function setUpBeforeClass()
	{
		// Set the validator type
		self::$validatorType = 'CouponDiscount';

		// Create the base objects
		parent::setUpBeforeClass();
	}

	public function getTestData()
	{
		return [
			'No coupon: invalid'                                                                                      => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => '',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => false,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'No coupon: invalid',
			],
			'Non-existent coupon code (IAMNOTTHERE): invalid'                                                         => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'IAMNOTTHERE',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => false,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Non-existent coupon code (IAMNOTTHERE): invalid',
			],
			'Valid coupon code, all caps (VALIDALL): valid'                                                           => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'VALIDALL',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 25,
					'coupon_id'   => 3,
				],
				'message'  => 'Valid coupon code, all caps (VALIDALL): valid',
			],
			'Valid coupon code, all lowercase (validall): valid'                                                      => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'validall',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 25,
					'coupon_id'   => 3,
				],
				'message'  => 'Valid coupon code, all lowercase (validall): valid',
			],
			'Valid coupon code, mixed case (ValidALL): valid'                                                         => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'ValidALL',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 25,
					'coupon_id'   => 3,
				],
				'message'  => 'Valid coupon code, mixed case (ValidALL): valid',
			],
			'Valid coupon code, spaces before (VALIDALL): valid'                                                      => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => ' VALIDALL',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 25,
					'coupon_id'   => 3,
				],
				'message'  => 'Valid coupon code, spaces before (VALIDALL): valid',
			],
			'Valid coupon code, spaces after (VALIDALL): valid'                                                       => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'VALIDALL ',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 25,
					'coupon_id'   => 3,
				],
				'message'  => 'Valid coupon code, spaces after (VALIDALL): valid',
			],
			'Valid coupon code, newline after (VALIDALL): valid'                                                      => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => "VALIDALL\n",
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 25,
					'coupon_id'   => 3,
				],
				'message'  => 'Valid coupon code, newline after (VALIDALL): valid',
			],
			'Valid coupon code, spaces around (VALIDALL): valid'                                                      => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => ' VALIDALL ',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 25,
					'coupon_id'   => 3,
				],
				'message'  => 'Valid coupon code, spaces around (VALIDALL): valid',
			],
			'Valid coupon code, not yet active (NOTYETACTIVE): invalid'                                               => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'NOTYETACTIVE',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, not yet active (NOTYETACTIVE): invalid',
			],
			'Valid coupon code, expired (ALREADYEXPIRED): invalid'                                                    => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'ALREADYEXPIRED',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, expired (ALREADYEXPIRED): invalid',
			],
			'Valid coupon code, inside the date range (INSIDEDATERANGE): valid'                                       => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'INSIDEDATERANGE',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 10,
					'coupon_id'   => 6,
				],
				'message'  => 'Valid coupon code, inside the date range (INSIDEDATERANGE): valid',
			],
			'Valid coupon code, limited to this subscription level (FORLEVEL1): valid'                                => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORLEVEL1',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 15,
					'coupon_id'   => 7,
				],
				'message'  => 'Valid coupon code, limited to this subscription level (FORLEVEL1): valid',
			],
			'Valid coupon code, limited to other subscription level (FORLEVEL2): invalid'                             => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORLEVEL2',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, limited to other subscription level (FORLEVEL2): invalid',
			],
			'Valid coupon code, limited to our user (FORUSER1): valid'                                                => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORUSER1',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 10,
					'coupon_id'   => 9,
				],
				'message'  => 'Valid coupon code, limited to our user (FORUSER1): valid',
			],
			'Valid coupon code, limited to other user (FORUSER2): invalid'                                            => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORUSER2',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, limited to other user (FORUSER2): invalid',
			],
			'Valid coupon code, limited to our email address (FORUSER1EMAIL): valid'                                  => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORUSER1EMAIL',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 10,
					'coupon_id'   => 11,
				],
				'message'  => 'Valid coupon code, limited to our email address (FORUSER1EMAIL): valid',
			],
			'Valid coupon code, limited to other email address (FORUSER2EMAIL): invalid'                              => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORUSER2EMAIL',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, limited to other email address (FORUSER2EMAIL): invalid',
			],
			'Valid coupon code, limited to our user group (FORSUBSCRIBERS): valid'                                    => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORSUBSCRIBERS',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 20,
					'coupon_id'   => 13,
				],
				'message'  => 'Valid coupon code, limited to our user group (FORSUBSCRIBERS): valid',
			],
			'Valid coupon code, limited to other user group (FORSUPERUSERS): invalid'                                 => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'FORSUPERUSERS',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, limited to other user group (FORSUPERUSERS): invalid',
			],
			'Valid coupon code, hits limit not reached (TENHITS): valid'                                              => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'TENHITS',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 10,
					'coupon_id'   => 15,
				],
				'message'  => 'Valid coupon code, hits limit not reached (TENHITS): valid',
			],
			'Valid coupon code, hits limit already reached (ONEHIT): invalid'                                         => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'ONEHIT',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, hits limit already reached (ONEHIT): invalid',
			],
			'Valid coupon code, logged in, user hits limit not reached (TENUSERHITS): valid'                          => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'TENUSERHITS',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 10,
					'coupon_id'   => 17,
				],
				'message'  => 'Valid coupon code, logged in, user hits limit not reached (TENUSERHITS): valid',
			],
			'Valid coupon code, logged in, user hits limit already reached (ONEUSERHIT): invalid'                     => [
				'loggedIn' => 'user4',
				'state'    => [
					'id'     => '1',
					'email'  => 'user4@test.web',
					'coupon' => 'ONEUSERHIT',
				],
				'expected' => [
					'valid'       => false,
					'couponFound' => true,
					'value'       => 0,
					'coupon_id'   => null,
				],
				'message'  => 'Valid coupon code, logged in, user hits limit already reached (ONEUSERHIT): invalid',
			],
			'Valid coupon code, logged in, user hits limit already reached for a different user (TWOUSERHITS): valid' => [
				'loggedIn' => 'user2',
				'state'    => [
					'id'     => '1',
					'email'  => 'user1@test.web',
					'coupon' => 'ONEUSERHIT',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 10,
					'coupon_id'   => 18,
				],
				'message'  => 'Valid coupon code, logged in, user hits limit already reached for a different user (TWOUSERHITS): valid',
			],
			'Valid coupon code, guest, user hits limit (TENUSERHITS): valid'                                          => [
				'loggedIn' => 'guest',
				'state'    => [
					'id'     => '1',
					'email'  => 'newuser@test.web',
					'coupon' => 'TENUSERHITS',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 10,
					'coupon_id'   => 17,
				],
				'message'  => 'Valid coupon code, guest, user hits limit (TENUSERHITS): valid',
			],

			'Fixed value (12.34), guest user with signup fee' => [
				'loggedIn' => 'guest',
				'state'    => [
					'id'     => '1',
					'email'  => 'newuser@test.web',
					'coupon' => 'FIXED1234',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 12.34,
					'coupon_id'   => 19,
				],
				'message'  => 'Fixed value (12.34), guest user with signup fee',
			],


			'Last percent (50%), guest user'                                                                     => [
				'loggedIn' => 'guest',
				'state'    => [
					'id'     => '1',
					'email'  => 'newuser@test.web',
					'coupon' => 'LAST50',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 0, // Guest user has no last transaction
					'coupon_id'   => 20,
				],
				'message'  => 'Last percent (50%), guest user',
			],
			'Last percent (50%), logged in user'                                              => [
				'loggedIn' => 'user1',
				'state'    => [
					'id'     => '1',
					'email'  => 'newuser@test.web',
					'coupon' => 'LAST50',
				],
				'expected' => [
					'valid'       => true,
					'couponFound' => true,
					'value'       => 40,
					'coupon_id'   => 20,
				],
				'message'  => 'Last percent (50%), logged in user',
			],


		];
	}

	/**
	 * Test the validator
	 *
	 * @dataProvider getTestData
	 */
	public function testGetValidationResult($loggedIn, $state, $expected, $message)
	{
		self::$jUser = self::$users[$loggedIn];
		self::$factory->reset();

		parent::testGetValidationResult($state, $expected, $message);
	}
}
