<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Admin\Helper;

use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use FOF30\Container\Container;
use Akeeba\Subscriptions\Admin\Helper\Message;

class MessageTest extends \PHPUnit\Framework\TestCase
{
	/** @var   Container  The container of the component */
	public static $container = null;

	/**
	 * Set up the static objects before the class is created
	 */
	public static function setUpBeforeClass()
	{
		if (is_null(static::$container))
		{
			static::$container = Container::getInstance('com_akeebasubs', [
				'platformClass' => 'Akeeba\\Subscriptions\\Tests\\Stubs\\CustomPlatform'
			]);
		}

		// Reset the component configuration
		static::$container->params->setParams([
			'hidecountries' => '',
			'reqcoupon' => 0,
			'currency' => 'USD',
			'currencysymbol' => '$',
			'invoice_altcurrency' => 'EUR',
		]);
		static::$container->params->save();

		// Prime the component parameters
		static::$container->params->get('currency');

		// Add some subscription data
		/** @var Subscriptions $sub */
		$sub = static::$container->factory->model('Subscriptions');
		$sub->findOrFail(2);
		$sub->save([
			'notes' => 'Notes',
			'tax_amount' => '9.00',
			'gross_amount' => '99.00',
			'tax_percent' => '10',
			'ip' => '8.8.8.8',
			'prediscount_amount' => '100',
			'discount_amount' => '10',
		]);
	}

	/**
	 * @dataProvider  getTestMessageCode()
	 *
	 * @param   string  $text
	 * @param   bool    $businessInfoAware
	 * @param   string  $expected
	 */
	public function testMessageCode($text, $businessInfoAware, $expected)
	{
		/** @var Subscriptions $sub */
		$sub = static::$container->factory->model('Subscriptions');
		$sub->findOrFail(2);

		$extras = array(
			'foo'		=> 'bar',
			'baz'		=> 'bat',
			'chicken'	=> 'kot'
		);

		$actual = Message::processSubscriptionTags($text, $sub, $extras, $businessInfoAware);

		$this->assertEquals($expected, $actual, $text . ' yields wrong result');
	}

	public function getTestMessageCode()
	{
		global $akeebasubsTestConfig;

		return [
			// text, businessInfoAware, expected
			// Akeeba Subs merge codes
			['[SITENAME]', true, $akeebasubsTestConfig['site_name']],
			['[SITEURL]', true, $akeebasubsTestConfig['site_url']],
			['[FULLNAME]', true, 'User One'],
			['[FIRSTNAME]', true, 'User'],
			['[LASTNAME]', true, 'One'],
			['[USERNAME]', true, 'user1'],
			['[USEREMAIL]', true, 'user1@test.web'],
			['[LEVEL]', true, 'CONTACTUS'],
			['[SLUG]', true, 'contactus'],
			['[RENEWALURL]', true, $akeebasubsTestConfig['site_url'] . 'index.php?option=com_akeebasubs&view=Level&slug=contactus&layout=default'],
			['[RENEWALURL:]', true, $akeebasubsTestConfig['site_url'] . 'index.php?option=com_akeebasubs&view=Level&slug=contactus&layout=default'],
			['[ENABLED]', true, 'COM_AKEEBASUBS_SUBSCRIPTION_COMMON_DISABLED'],
			['[PAYSTATE]', true, 'COM_AKEEBASUBS_SUBSCRIPTION_STATE_C'],
			['[PUBLISH_UP]', true, 'Wednesday, 30 April 2014 00:00 GMT'],
			['[PUBLISH_UP_EU]', true, '30/04/2014 00:00:00 GMT'],
			['[PUBLISH_UP_USA]', true, '04/30/2014 12:00:00 am GMT'],
			['[PUBLISH_UP_JAPAN]', true, '2014/04/30 00:00:00 GMT'],
			['[PUBLISH_DOWN]', true, 'Wednesday, 29 April 2015 00:00 GMT'],
			['[PUBLISH_DOWN_EU]', true, '29/04/2015 00:00:00 GMT'],
			['[PUBLISH_DOWN_USA]', true, '04/29/2015 12:00:00 am GMT'],
			['[PUBLISH_DOWN_JAPAN]', true, '2015/04/29 00:00:00 GMT'],
			['[MYSUBSURL]', true, $akeebasubsTestConfig['site_url'] . 'index.php?option=com_akeebasubs&view=Subscriptions'],
			['[URL]', true, $akeebasubsTestConfig['site_url'] . 'index.php?option=com_akeebasubs&view=Subscriptions'],
			['[CURRENCY]', true, 'USD'],
			['[$]', true, '$'],
			['[DLID]', true, ''],
			['[COUPONCODE]', true, 'TENHITS'],
			// Legacy keys
			['[NAME]', true, 'User'],
			['[STATE]', true, 'COM_AKEEBASUBS_SUBSCRIPTION_STATE_C'],
			['[FROM]', true, 'Wednesday, 30 April 2014 00:00 GMT'],
			['[TO]', true, 'Wednesday, 29 April 2015 00:00 GMT'],
			// Subscription merge codes (automatic)
			['[SUB:ID]', true, 2],
			['[SUB:USER_ID]', true, 1000],
			['[SUB:AKEEBASUBS_LEVEL_ID]', true, 2],
			['[SUB:PUBLISH_UP]', true, '2014-04-30 00:00:00'],
			['[SUB:PUBLISH_DOWN]', true, '2015-04-29 00:00:00'],
			['[SUB:NOTES]', true, 'Notes'],
			['[SUB:ENABLED]', true, 0],
			['[SUB:PROCESSOR]', true, 'none'],
			['[SUB:PROCESSOR_KEY]', true, '20140430000001'],
			['[SUB:STATE]', true, 'C'],
			['[SUB:NET_AMOUNT]', true, '90.00'],
			['[SUB:TAX_AMOUNT]', true, '9.00'],
			['[SUB:GROSS_AMOUNT]', true, '99.00'],
			['[SUB:TAX_PERCENT]', true, '10'],
			['[SUB:CREATED_ON]', true, '2014-04-30 00:00:00'],
			['[SUB:IP]', true, '8.8.8.8'],
			['[SUB:AKEEBASUBS_COUPON_ID]', true, '15'],
			['[SUB:AKEEBASUBS_UPGRADE_ID]', true, ''],
			['[SUB:AKEEBASUBS_INVOICE_ID]', true, ''],
			['[SUB:PREDISCOUNT_AMOUNT]', true, '100.00'],
			['[SUB:DISCOUNT_AMOUNT]', true, '10.00'],
			['[SUB:CONTACT_FLAG]', true, '0'],
			['[SUB:FIRST_CONTACT]', true, '0000-00-00 00:00:00'],
			['[SUB:SECOND_CONTACT]', true, '0000-00-00 00:00:00'],
			['[SUB:AFTER_CONTACT]', true, '0000-00-00 00:00:00'],
			// SUBCUSTOM:
			['[SUBCUSTOM:LOL]', true, 'wut'],
			['[SUBCUSTOM:FOO]', true, '123'],
			// LEVEL:
			['[LEVEL:ID]', true, '2'],
			['[LEVEL:TITLE]', true, 'CONTACTUS'],
			['[LEVEL:SLUG]', true, 'contactus'],
			['[LEVEL:IMAGE]', true, 'images/levels/product-releasesystem.svg'],
			['[LEVEL:DESCRIPTION]', true, '<p>One YEAR access to Contact Us downloads and support. Unlimited sites / domains.</p>'],
			['[LEVEL:DURATION]', true, '365'],
			['[LEVEL:PRICE]', true, '50'],
			['[LEVEL:ORDERTEXT]', true, '<h3>Thank you for your purchase of Contact Us!</h3>
<p>Your subscription will be active until Wednesday, 29 April 2015 00:00 GMT.</p>
<p>This is some further text explaining what benefits you get from this subscription and the next steps you can follow.</p>'],
			['[LEVEL:ONLY_ONCE]', true, '0'],
			['[LEVEL:RECURRING]', true, '0'],
			['[LEVEL:FOREVER]', true, '0'],
			['[LEVEL:ACCESS]', true, '1'],
			['[LEVEL:FIXED_DATE]', true, '0000-00-00 00:00:00'],
			['[LEVEL:RENEW_URL]', true, ''],
			['[LEVEL:CONTENT_URL]', true, ''],
			['[LEVEL:ENABLED]', true, '1'],
			['[LEVEL:ORDERING]', true, '0'],
			['[LEVEL:CREATED_ON]', true, '2019-03-20 16:19:43'],
			['[LEVEL:CREATED_BY]', true, '70'],
			['[LEVEL:NOTIFY1]', true, '30'],
			['[LEVEL:NOTIFY2]', true, '15'],
			['[LEVEL:NOTIFYAFTER]', true, '0'],
		];
	}
}
