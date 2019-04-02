<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Tests\Admin\Helper;

use FOF30\Container\Container;
use Akeeba\Subscriptions\Admin\Helper\EUVATInfo;

class EUVATInfoTest extends \PHPUnit\Framework\TestCase
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

		// Prime the component parameters
		static::$container->params->get('currency');
	}

	/**
	 * This test is unnecessarily thorough, covering every EU country. This is on purpose. I've had so much trouble with
	 * the EU service not returning data for specific countries over periods of time that I need a way to test *their
	 * SOAP service*. The only reasonable way to do that is with this complicated test.
	 *
	 * @dataProvider Akeeba\Subscriptions\Tests\Admin\Helper\EUVATInfoTest::getTestLiveVIESValidationDataSHORT
	 */
	public function testLiveVIESValidation(string $country, string $vat, bool $expected, int $repetitions = 1, bool $clearCache = true)
	{
		for ($currentRun = 1; $currentRun <= $repetitions; $currentRun++)
		{
			if ($clearCache)
			{
				// Reset the current cache...
				$reflectionClass = new \ReflectionClass('Akeeba\Subscriptions\Admin\Helper\EUVATInfo');
				$refCache = $reflectionClass->getProperty('cache');
				$refCache->setAccessible(true);
				$refCache->setValue([]);

				// ...and the session cache
				$session = \JFactory::getSession();
				$session->set('vat_validation_cache_data', null, 'com_akeebasubs');
			}

			// Run the VIES check
			$actual = EUVATInfo::isVIESValidVATNumber($country, $vat);

			// Assert results
			$message = $expected ? "Could not validate " : "False positive validation of ";

			$this->assertEquals($expected, $actual, "$message VAT number $country $vat (run: #$currentRun)");
		}
	}

	public static function getTestLiveVIESValidationDataSHORT()
	{
		return [
			'Greece, invalid VAT number' => [
				'GR', '070298898', false
			],
			'Greece, valid VAT number' => [
				'GR', '999645865', true
			],
		];
	}

	public static function getTestLiveVIESValidationData()
	{
		return [
			'Greece, invalid VAT number' => [
				'GR', '070298898', false
			],
			'Greece, valid VAT number' => [
				'GR', '999645865', true
			],

			'Belgium, invalid VAT number' => [
				'BE', '0822461812', false
			],
			'Belgium, valid VAT number' => [
				'BE', '0867150108', true
			],

			'Bulgaria, invalid VAT number' => [
				'BG', '121469482', false
			],
			'Bulgaria, valid VAT number' => [
				'BG', '203755351', true
			],

			'Czechia, invalid VAT number' => [
				'CZ', '01467247', false
			],
			'Czechia, valid VAT number, 8 digits' => [
				'CZ', '61989592', true
			],
			'Czechia, valid VAT number, 10 digits' => [
				'CZ', '7009235552', true
			],

			'Denmark, invalid VAT number' => [
				'DK', '33560974', false
			],
			'Denmark, valid VAT number' => [
				'DK', '30060946', true
			],

			'Germany, invalid VAT number' => [
				'DE', '207749330', false
			],
			'Germany, valid VAT number' => [
				'DE', '813885934', true
			],

			'Estonia, valid VAT number' => [
				'EE', '100993596', true
			],

			'Spain, invalid VAT number' => [
				'ES', 'B24646879', false
			],
			'Spain, valid VAT number' => [
				'ES', 'Q2803011B', true
			],

			'France, invalid VAT number' => [
				'FR', '52494844996', false
			],
			'France, valid VAT number' => [
				'FR', '79421717190', true
			],

			'Croatia, invalid VAT number' => [
				'HR', '34371862090', false
			],
			'Croatia, valid VAT number' => [
				'HR', '82943151895', true
			],

			'Ireland, invalid VAT number' => [
				'IE', '7642871C', false
			],
			'Ireland, valid VAT number' => [
				'IE', '4000113V', true
			],

			'Italy, invalid VAT number' => [
				'IT', '01423070182', false
			],
			'Italy, valid VAT number' => [
				'IT', '07154361005', true
			],

			'Cyprus, invalid VAT number' => [
				'CY', '90006056E', false
			],
			'Cyprus, valid VAT number' => [
				'CY', '10307966C', true
			],

			'Latvia, valid VAT number' => [
				'LV', '54103058571', true
			],

			'Lithuania, valid VAT number' => [
				'LT', '119508113', true
			],

			'Luxembourg, valid VAT number' => [
				'LU', '20935325', true
			],

			'Hungary, invalid VAT number' => [
				'HU', '18071834', false
			],
			'Hungary, valid VAT number' => [
				'HU', '15308816', true
			],

			'Malta, valid VAT number' => [
				'MT', '23284704', true
			],

			'Netherlands, invalid VAT number' => [
				'NL', '82439485B01', false
			],
			'Netherlands, valid VAT number' => [
				'NL', '804194774B01', true
			],

			'Austria, invalid VAT number' => [
				'AT', '65845277', false
			],
			'Austria, valid VAT number' => [
				'AT', 'U42657308', true
			],

			'Poland, invalid VAT number' => [
				'PL', '8571769502', false
			],
			'Poland, valid VAT number' => [
				'PL', '5060034353', true
			],

			'Portugal, invalid VAT number' => [
				'PT', '134681045', false
			],
			'Portugal, valid VAT number' => [
				'PT', '501345361', true
			],

			'Romania, invalid VAT' => [
				'RO', '31066862', false
			],
			'Romania, valid VAT number (7 characters)' => [
				'RO', '6646761', true
			],
			'Romania, valid VAT number' => [
				'RO', '38753723', true
			],

			'Slovenia, invalid VAT number' => [
				'SI', '84600128', false
			],
			'Slovenia, valid VAT number' => [
				'SI', '98280171', true
			],

			'Slovakia, valid VAT number' => [
				'SK', '2022212159', true
			],

			'Finland, invalid VAT number' => [
				'FI', '19372541', false
			],
			'Finland, valid VAT number' => [
				'FI', '09881828', true
			],

			'Sweden, invalid VAT number' => [
				'SE', '920317213601', false
			],
			'Sweden, valid VAT number' => [
				'SE', '969657623101', true
			],

			'United Kingdom, invalid VAT number' => [
				'GB', '847742101', false
			],
			'United Kingdom, valid VAT number' => [
				'GB', '111979171', true
			],

			'Isle of Man, valid VAT number' => [
				'IM', '003152930', true
			],
		];
	}
}
