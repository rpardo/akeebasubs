<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

use FOF30\Container\Container;
use FOF30\Layout\LayoutHelper;
use FOF30\Model\DataModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * A helper class for drop-down selection boxes
 */
abstract class Select
{
	/**
	 * Maps the two letter codes to country names (in English)
	 *
	 * @var  array
	 */
	public static $countries = [
		''   => '----',
		'AD' => 'Andorra',
		'AE' => 'United Arab Emirates',
		'AF' => 'Afghanistan',
		'AG' => 'Antigua and Barbuda',
		'AI' => 'Anguilla',
		'AL' => 'Albania',
		'AM' => 'Armenia',
		'AO' => 'Angola',
		'AQ' => 'Antarctica',
		'AR' => 'Argentina',
		'AS' => 'American Samoa',
		'AT' => 'Austria',
		'AU' => 'Australia',
		'AW' => 'Aruba',
		'AX' => 'Aland Islands',
		'AZ' => 'Azerbaijan',
		'BA' => 'Bosnia and Herzegovina',
		'BB' => 'Barbados',
		'BD' => 'Bangladesh',
		'BE' => 'Belgium',
		'BF' => 'Burkina Faso',
		'BG' => 'Bulgaria',
		'BH' => 'Bahrain',
		'BI' => 'Burundi',
		'BJ' => 'Benin',
		'BL' => 'Saint Barthélemy',
		'BM' => 'Bermuda',
		'BN' => 'Brunei Darussalam',
		'BO' => 'Bolivia, Plurinational State of',
		'BQ' => 'Bonaire, Saint Eustatius and Saba',
		'BR' => 'Brazil',
		'BS' => 'Bahamas',
		'BT' => 'Bhutan',
		'BV' => 'Bouvet Island',
		'BW' => 'Botswana',
		'BY' => 'Belarus',
		'BZ' => 'Belize',
		'CA' => 'Canada',
		'CC' => 'Cocos (Keeling) Islands',
		'CD' => 'Congo, the Democratic Republic of the',
		'CF' => 'Central African Republic',
		'CG' => 'Congo',
		'CH' => 'Switzerland',
		'CI' => 'Cote d\'Ivoire',
		'CK' => 'Cook Islands',
		'CL' => 'Chile',
		'CM' => 'Cameroon',
		'CN' => 'China',
		'CO' => 'Colombia',
		'CR' => 'Costa Rica',
		'CU' => 'Cuba',
		'CV' => 'Cape Verde',
		'CW' => 'Curaçao',
		'CX' => 'Christmas Island',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DE' => 'Germany',
		'DJ' => 'Djibouti',
		'DK' => 'Denmark',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'DZ' => 'Algeria',
		'EC' => 'Ecuador',
		'EE' => 'Estonia',
		'EG' => 'Egypt',
		'EH' => 'Western Sahara',
		'ER' => 'Eritrea',
		'ES' => 'Spain',
		'ET' => 'Ethiopia',
		'FI' => 'Finland',
		'FJ' => 'Fiji',
		'FK' => 'Falkland Islands (Malvinas)',
		'FM' => 'Micronesia, Federated States of',
		'FO' => 'Faroe Islands',
		'FR' => 'France',
		'GA' => 'Gabon',
		'GB' => 'United Kingdom',
		'GD' => 'Grenada',
		'GE' => 'Georgia',
		'GF' => 'French Guiana',
		'GG' => 'Guernsey',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GL' => 'Greenland',
		'GM' => 'Gambia',
		'GN' => 'Guinea',
		'GP' => 'Guadeloupe',
		'GQ' => 'Equatorial Guinea',
		'GR' => 'Greece',
		'GS' => 'South Georgia and the South Sandwich Islands',
		'GT' => 'Guatemala',
		'GU' => 'Guam',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HK' => 'Hong Kong',
		'HM' => 'Heard Island and McDonald Islands',
		'HN' => 'Honduras',
		'HR' => 'Croatia',
		'HT' => 'Haiti',
		'HU' => 'Hungary',
		'ID' => 'Indonesia',
		'IE' => 'Ireland',
		'IL' => 'Israel',
		'IM' => 'Isle of Man',
		'IN' => 'India',
		'IO' => 'British Indian Ocean Territory',
		'IQ' => 'Iraq',
		'IR' => 'Iran, Islamic Republic of',
		'IS' => 'Iceland',
		'IT' => 'Italy',
		'JE' => 'Jersey',
		'JM' => 'Jamaica',
		'JO' => 'Jordan',
		'JP' => 'Japan',
		'KE' => 'Kenya',
		'KG' => 'Kyrgyzstan',
		'KH' => 'Cambodia',
		'KI' => 'Kiribati',
		'KM' => 'Comoros',
		'KN' => 'Saint Kitts and Nevis',
		'KP' => 'Korea, Democratic People\'s Republic of',
		'KR' => 'Korea, Republic of',
		'KW' => 'Kuwait',
		'KY' => 'Cayman Islands',
		'KZ' => 'Kazakhstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LB' => 'Lebanon',
		'LC' => 'Saint Lucia',
		'LI' => 'Liechtenstein',
		'LK' => 'Sri Lanka',
		'LR' => 'Liberia',
		'LS' => 'Lesotho',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'LV' => 'Latvia',
		'LY' => 'Libyan Arab Jamahiriya',
		'MA' => 'Morocco',
		'MC' => 'Monaco',
		'MD' => 'Moldova, Republic of',
		'ME' => 'Montenegro',
		'MF' => 'Saint Martin (French part)',
		'MG' => 'Madagascar',
		'MH' => 'Marshall Islands',
		'MK' => 'Northern Macedonia',
		'ML' => 'Mali',
		'MM' => 'Myanmar',
		'MN' => 'Mongolia',
		'MO' => 'Macao',
		'MP' => 'Northern Mariana Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MS' => 'Montserrat',
		'MT' => 'Malta',
		'MU' => 'Mauritius',
		'MV' => 'Maldives',
		'MW' => 'Malawi',
		'MX' => 'Mexico',
		'MY' => 'Malaysia',
		'MZ' => 'Mozambique',
		'NA' => 'Namibia',
		'NC' => 'New Caledonia',
		'NE' => 'Niger',
		'NF' => 'Norfolk Island',
		'NG' => 'Nigeria',
		'NI' => 'Nicaragua',
		'NL' => 'Netherlands',
		'NO' => 'Norway',
		'NP' => 'Nepal',
		'NR' => 'Nauru',
		'NU' => 'Niue',
		'NZ' => 'New Zealand',
		'OM' => 'Oman',
		'PA' => 'Panama',
		'PE' => 'Peru',
		'PF' => 'French Polynesia',
		'PG' => 'Papua New Guinea',
		'PH' => 'Philippines',
		'PK' => 'Pakistan',
		'PL' => 'Poland',
		'PM' => 'Saint Pierre and Miquelon',
		'PN' => 'Pitcairn',
		'PR' => 'Puerto Rico',
		'PS' => 'Palestinian Territory, Occupied',
		'PT' => 'Portugal',
		'PW' => 'Palau',
		'PY' => 'Paraguay',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RS' => 'Serbia',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'SA' => 'Saudi Arabia',
		'SB' => 'Solomon Islands',
		'SC' => 'Seychelles',
		'SD' => 'Sudan',
		'SE' => 'Sweden',
		'SG' => 'Singapore',
		'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
		'SI' => 'Slovenia',
		'SJ' => 'Svalbard and Jan Mayen',
		'SK' => 'Slovakia',
		'SL' => 'Sierra Leone',
		'SM' => 'San Marino',
		'SN' => 'Senegal',
		'SO' => 'Somalia',
		'SR' => 'Suriname',
		'SS' => 'South Sudan',
		'ST' => 'Sao Tome and Principe',
		'SV' => 'El Salvador',
		'SX' => 'Sint Maarten',
		'SY' => 'Syrian Arab Republic',
		'SZ' => 'Swaziland',
		'TC' => 'Turks and Caicos Islands',
		'TD' => 'Chad',
		'TF' => 'French Southern Territories',
		'TG' => 'Togo',
		'TH' => 'Thailand',
		'TJ' => 'Tajikistan',
		'TK' => 'Tokelau',
		'TL' => 'Timor-Leste',
		'TM' => 'Turkmenistan',
		'TN' => 'Tunisia',
		'TO' => 'Tonga',
		'TR' => 'Turkey',
		'TT' => 'Trinidad and Tobago',
		'TV' => 'Tuvalu',
		'TW' => 'Taiwan',
		'TZ' => 'Tanzania, United Republic of',
		'UA' => 'Ukraine',
		'UG' => 'Uganda',
		'UM' => 'United States Minor Outlying Islands',
		'US' => 'United States',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VA' => 'Holy See (Vatican City State)',
		'VC' => 'Saint Vincent and the Grenadines',
		'VE' => 'Venezuela, Bolivarian Republic of',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',
		'VN' => 'Viet Nam',
		'VU' => 'Vanuatu',
		'WF' => 'Wallis and Futuna',
		'WS' => 'Samoa',
		'YE' => 'Yemen',
		'YT' => 'Mayotte',
		'ZA' => 'South Africa',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	];

	/**
	 * Returns a list of all countries including the empty option (no country)
	 *
	 * @param   bool  $includeEmpty  Should I include the empty option?
	 *
	 * @return  array
	 */
	public static function getCountries(bool $includeEmpty = true): array
	{
		if ($includeEmpty)
		{
			return self::$countries;
		}

		$countries = self::$countries;

		unset($countries['']);

		return $countries;
	}

	/**
	 * Translate a two letter country code into the country name (in English). If the country is unknown the country
	 * code itself is returned.
	 *
	 * @param   string  $cCode  The country code
	 *
	 * @return  string  The name of the country or, of it's not known, the country code itself.
	 */
	public static function decodeCountry(string $cCode): string
	{
		if (array_key_exists($cCode, self::$countries))
		{
			return self::$countries[$cCode];
		}

		return $cCode;
	}

	/**
	 * Translate a two letter country code into the country name (in English). If the country is unknown three em-dashes
	 * are returned. This is different to decode country which returns the country code in this case.
	 *
	 * @param   string  $cCode  The country code
	 *
	 * @return  string  The name of the country or, of it's not known, the country code itself.
	 */
	public static function formatCountry(string $cCode = ''): string
	{
		$name = self::decodeCountry($cCode);

		if ($name == $cCode)
		{
			$name = '&mdash;';
		}

		return $name;
	}

	/**
	 * Converts an ISO country code to an emoji flag.
	 *
	 * This is stupidly easy. An emoji flag is the country code using Unicode Regional Indicator Symbol Letter glyphs
	 * instead of the regular ASCII characters. Thus US becomes \u1F1FA\u1F1F8 which is incidentally the emoji for the
	 * US flag :)
	 *
	 * On really old browsers (pre-2015) this still renders as the country code since the Regional Indicator Symbol
	 * Letter glyphs were added to Unicode in 2010. Now, if you have an even older browser -- what the heck, dude?!
	 *
	 * @param   string  $cCode
	 *
	 * @return string
	 *
	 * @since version
	 */
	public static function countryToEmoji(string $cCode = ''): string
	{
		$name = self::decodeCountry($cCode);

		if ($name == $cCode)
		{
			return '';
		}

		$cCode = strtoupper($cCode);

		// Uppercase letter to Unicode Regional Indicator Symbol Letter
		$letterToRISL = [
			'A' => "&#x1F1E6;",
			'B' => "&#x1F1E7;",
			'C' => "&#x1F1E8;",
			'D' => "&#x1F1E9;",
			'E' => "&#x1F1EA;",
			'F' => "&#x1F1EB;",
			'G' => "&#x1F1EC;",
			'H' => "&#x1F1ED;",
			'I' => "&#x1F1EE;",
			'J' => "&#x1F1EF;",
			'K' => "&#x1F1F0;",
			'L' => "&#x1F1F1;",
			'M' => "&#x1F1F2;",
			'N' => "&#x1F1F3;",
			'O' => "&#x1F1F4;",
			'P' => "&#x1F1F5;",
			'Q' => "&#x1F1F6;",
			'R' => "&#x1F1F7;",
			'S' => "&#x1F1F8;",
			'T' => "&#x1F1F9;",
			'U' => "&#x1F1FA;",
			'V' => "&#x1F1FB;",
			'W' => "&#x1F1FC;",
			'X' => "&#x1F1FD;",
			'Y' => "&#x1F1FE;",
			'Z' => "&#x1F1FF;",
		];

		return $letterToRISL[substr($cCode, 0, 1)] . $letterToRISL[substr($cCode, 1, 1)];
	}

	/**
	 * Returns a drop-down selection box for countries. Some special attributes:
	 *
	 * show     An array of country codes to display. Takes precedence over hide.
	 * hide     An array of country codes to hide.
	 *
	 * @param   string  $selected  Selected country code
	 * @param   string  $id        Field name and ID
	 * @param   array   $attribs   Field attributes
	 *
	 * @return string
	 */
	public static function countries($selected = null, $id = 'country', $attribs = [])
	{
		// Get the raw list of countries
		$options   = [];
		$countries = self::$countries;
		asort($countries);
		// Parse show / hide options
		// -- Initialisation
		$show = [];
		$hide = [];
		// -- Parse the show attribute
		if (isset($attribs['show']))
		{
			$show = trim($attribs['show']);
			if (!empty($show))
			{
				$show = explode(',', $show);
			}
			else
			{
				$show = [];
			}
			unset($attribs['show']);
		}
		// -- Parse the hide attribute
		if (isset($attribs['hide']))
		{
			$hide = trim($attribs['hide']);
			if (!empty($hide))
			{
				$hide = explode(',', $hide);
			}
			else
			{
				$hide = [];
			}
			unset($attribs['hide']);
		}
		// -- If $show is not empty, filter the countries
		if (count($show))
		{
			$temp = [];
			foreach ($show as $key)
			{
				if (array_key_exists($key, $countries))
				{
					$temp[$key] = $countries[$key];
				}
			}
			asort($temp);
			$countries = $temp;
		}
		// -- If $show is empty but $hide is not, filter the countries
		elseif (count($hide))
		{
			$temp = [];
			foreach ($countries as $key => $v)
			{
				if (!in_array($key, $hide))
				{
					$temp[$key] = $v;
				}
			}
			asort($temp);
			$countries = $temp;
		}
		foreach ($countries as $code => $name)
		{
			$options[] = HTMLHelper::_('select.option', $code, $name);
		}

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	/**
	 * Generates a yes/no drop-down list.
	 *
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $selected  The key that is selected
	 *
	 * @return  string  HTML for the list
	 */
	public static function booleanlist($name, $attribs = null, $selected = null)
	{
		$options = [
			HTMLHelper::_('select.option', '', '---'),
			HTMLHelper::_('select.option', '0', Text::_('JNo')),
			HTMLHelper::_('select.option', '1', Text::_('JYes')),
		];

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Displays a list of the available user groups.
	 *
	 * @param   string  $name      The form field name.
	 * @param   string  $selected  The name of the selected section.
	 * @param   array   $attribs   Additional attributes to add to the select field.
	 *
	 * @return  string   The HTML for the list
	 */
	public static function usergroups($name = 'usergroups', $selected = '', $attribs = [])
	{
		return HTMLHelper::_('access.usergroup', $name, $selected, $attribs, false);
	}

	/**
	 * Generates a Published/Unpublished drop-down list.
	 *
	 * @param   string  $selected  The key that is selected (0 = unpublished / 1 = published)
	 * @param   string  $id        The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML for the list
	 */
	public static function published($selected = null, $id = 'enabled', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', null, '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECTSTATE') . ' -');
		$options[] = HTMLHelper::_('select.option', 0, Text::_('JUNPUBLISHED'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('JPUBLISHED'));

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	/**
	 * Generates a drop-down list for the available languages of a multi-language site.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML for the list
	 */
	public static function languages($selected = null, $id = 'language', $attribs = [])
	{
		$languages = \JLanguageHelper::getLanguages('lang_code');
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '*', Text::_('JALL_LANGUAGE'));

		if (!empty($languages))
		{
			foreach ($languages as $key => $lang)
			{
				$options[] = HTMLHelper::_('select.option', $key, $lang->title);
			}
		}

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	/**
	 * Generates a drop-down list for the available subscription payment states.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML for the list
	 */
	public static function paystates($selected = null, $id = 'state', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE') . ' -');

		$types = ['N', 'P', 'C', 'X'];

		foreach ($types as $type)
		{
			$options[] = HTMLHelper::_('select.option', $type, Text::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_' . $type));
		}

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	/**
	 * Generates a drop-down list for the available coupon types.
	 *
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   string  $selected  The key that is selected
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML for the list
	 */
	public static function coupontypes($name = 'type', $selected = 'value', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'value', Text::_('COM_AKEEBASUBS_COUPON_TYPE_VALUE'));
		$options[] = HTMLHelper::_('select.option', 'percent', Text::_('COM_AKEEBASUBS_COUPON_TYPE_PERCENT'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Generates a drop-down list for the available subscription levels. Alias of levels() with different ordering of
	 * parameters and include_clear set to true.
	 *
	 * @param   string  $selected  The key that is selected
	 * @param   string  $id        The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML for the list
	 */
	public static function subscriptionlevels($selected = null, $id = 'akeebasubs_level_id', $attribs = [])
	{
		$attribs['include_clear'] = true;

		return self::levels($id, $selected, $attribs);
	}

	/**
	 * Generates a drop-down list for the available subscription levels.
	 *
	 * Some interesting attributes:
	 *
	 * include_none     Include an option with value -1 titled "None"
	 * include_all      Include an option with value 0 titled "All"
	 * include_clear    Include an option with no value for clearing the selection
	 *
	 * By default none of these attributes is set
	 *
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   string  $selected  The key that is selected
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 *
	 * @return  string  HTML for the list
	 */
	public static function levels($name = 'level', $selected = '', $attribs = [])
	{
		/** @var DataModel $model */
		$model = Container::getInstance('com_akeebasubs')->factory
			->model('Levels')->tmpInstance();

		$list = $model->filter_order('ordering')->filter_order_Dir('ASC')->get(true);

		$options = [];

		$include_none  = false;
		$include_all   = false;
		$include_clear = false;

		if (array_key_exists('include_none', $attribs))
		{
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}

		if (array_key_exists('include_all', $attribs))
		{
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}

		if (array_key_exists('include_clear', $attribs))
		{
			$include_clear = $attribs['include_clear'];
			unset($attribs['include_clear']);
		}

		if ($include_none)
		{
			$options[] = HTMLHelper::_('select.option', '-1', Text::_('COM_AKEEBASUBS_COMMON_SELECTLEVEL_NONE'));
		}

		if ($include_all)
		{
			$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_AKEEBASUBS_COMMON_SELECTLEVEL_ALL'));
		}

		if ($include_clear || (!$include_none && !$include_all))
		{
			$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		}

		foreach ($list as $item)
		{
			$options[] = HTMLHelper::_('select.option', $item->akeebasubs_level_id, $item->title);
		}

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Returns the human readable subscription level title based on the numeric subscription level ID given in $id
	 *
	 * Alias of Format::formatLevel
	 *
	 * @param   int  $id  The subscription level ID
	 *
	 * @return  string  The subscription level title, or three em-dashes if it's unknown
	 */
	public static function formatLevel($id)
	{
		return Format::formatLevel($id);
	}

	/**
	 * Drop down list of discount modes
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function discountmodes($name = 'discountmode', $selected = '', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'none', Text::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_NONE'));
		$options[] = HTMLHelper::_('select.option', 'coupon', Text::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_COUPON'));
		$options[] = HTMLHelper::_('select.option', 'upgrade', Text::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_UPGRADE'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of upgrade types
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function upgradetypes($name = 'type', $selected = 'value', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'value', Text::_('COM_AKEEBASUBS_UPGRADE_TYPE_VALUE'));
		$options[] = HTMLHelper::_('select.option', 'percent', Text::_('COM_AKEEBASUBS_UPGRADE_TYPE_PERCENT'));
		$options[] = HTMLHelper::_('select.option', 'lastpercent', Text::_('COM_AKEEBASUBS_UPGRADE_TYPE_LASTPERCENT'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of custom field types
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function fieldtypes($name = 'type', $selected = 'text', $attribs = [])
	{
		$fieldTypes = self::getFieldTypes();

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		foreach ($fieldTypes as $type => $desc)
		{
			$options[] = HTMLHelper::_('select.option', $type, $desc);
		}

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of subscription level relation modes
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function relationmode($name = 'mode', $selected = 'rules', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'rules', Text::_('COM_AKEEBASUBS_RELATIONS_MODE_RULES'));
		$options[] = HTMLHelper::_('select.option', 'fixed', Text::_('COM_AKEEBASUBS_RELATIONS_MODE_FIXED'));
		$options[] = HTMLHelper::_('select.option', 'flexi', Text::_('COM_AKEEBASUBS_RELATIONS_MODE_FLEXI'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of subscription level relations' period units of measurement
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function flexiperioduoms($name = 'flex_uom', $selected = 'rules', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'd', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_D'));
		$options[] = HTMLHelper::_('select.option', 'w', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_W'));
		$options[] = HTMLHelper::_('select.option', 'm', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_M'));
		$options[] = HTMLHelper::_('select.option', 'y', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_Y'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of subscription level relations' flexible discount time calculation preference
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function flexitimecalc($name = 'flex_timecalculation', $selected = 'current', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'current', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMECALCULATION_CURRENT'));
		$options[] = HTMLHelper::_('select.option', 'future', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMECALCULATION_FUTURE'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of subscription level relations' flexible discount rounding preference
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function flexirounding($name = 'flex_rounding', $selected = 'round', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'floor', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_FLOOR'));
		$options[] = HTMLHelper::_('select.option', 'ceil', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_CEIL'));
		$options[] = HTMLHelper::_('select.option', 'round', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_ROUND'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of subscription level relations' subscription expiration preference
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function flexiexpiration($name = 'expiration', $selected = 'replace', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = HTMLHelper::_('select.option', 'replace', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_REPLACE'));
		$options[] = HTMLHelper::_('select.option', 'after', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_AFTER'));
		$options[] = HTMLHelper::_('select.option', 'overlap', Text::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_OVERLAP'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of CSV delimiter preference
	 *
	 * @param   string  $name      The field's name
	 * @param   int     $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function csvdelimiters($name = 'csvdelimiters', $selected = 1, $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', 'abc, def');
		$options[] = HTMLHelper::_('select.option', '2', 'abc; def');
		$options[] = HTMLHelper::_('select.option', '3', '"abc"; "def"');
		$options[] = HTMLHelper::_('select.option', '-99', Text::_('COM_AKEEBASUBS_IMPORT_DELIMITERS_CUSTOM'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of payment method types
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function paymentMethods($name = 'flex_uom', $selected = 'rules', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('COM_AKEEBASUBS_SUBSCRIPTION_PAYMENT_TYPE_FIELDTITLE') . ' -');
		$options[] = HTMLHelper::_('select.option', 'apple', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_PAYMENT_TYPE_APPLE'));
		$options[] = HTMLHelper::_('select.option', 'card', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_PAYMENT_TYPE_CARD'));
		$options[] = HTMLHelper::_('select.option', 'free', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_PAYMENT_TYPE_FREE'));
		$options[] = HTMLHelper::_('select.option', 'paypal', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_PAYMENT_TYPE_PAYPAL'));
		$options[] = HTMLHelper::_('select.option', 'wire', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_PAYMENT_TYPE_WIRE'));
		$options[] = HTMLHelper::_('select.option', 'unknown', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_PAYMENT_TYPE_UNKNOWN'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Displays a media (image) select field, rendered by Joomla itself
	 *
	 * @param   string  $name      The name of the field
	 * @param   string  $selected  Currently selected image
	 * @param   array   $attribs   Overrides to the layout parameters
	 *
	 * @return  string  The rendered Joomla layout
	 *
	 * @throws \Exception
	 * @since  7.1.1
	 */
	public static function mediaSelect(string $name, ?string $selected = '', array $attribs = []): string
	{
		$selected = $selected ?? '';

		$data = array_merge([
			'asset'         => 'com_akeebasubs',
			'authorField'   => null,
			'authorId'      => null,
			'class'         => '',
			'disabled'      => false,
			'folder'        => '',
			'id'            => '',
			'link'          => '',
			'name'          => $name,
			'preview'       => 'false',
			'previewHeight' => 200,
			'previewWidth'  => 200,
			'onchange'      => '',
			'readonly'      => false,
			'size'          => '',
			'value'         => $selected,
			'src'           => '',
		], $attribs);

		if ($data['asset'] === '')
		{
			$data['asset'] = Factory::getApplication()->input->get('option', 'com_akeebasubs');
		}

		$directory = $data['directory'] ?? '';

		if ($selected && file_exists(JPATH_ROOT . '/' . $selected))
		{
			$folder = explode('/', $selected);
			$folder = array_diff_assoc($folder, explode('/', ComponentHelper::getParams('com_media')->get('image_path', 'images')));
			array_pop($folder);
			$data['folder'] = implode('/', $folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . ComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $directory))
		{
			$data['folder'] = $directory;
		}
		else
		{
			$data['folder'] = '';
		}

		return LayoutHelper::render(self::getContainer(), 'joomla.form.field.media', $data);
	}

	/**
	 * Drop down list of discount modes
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function contactFlagOptions($name = 'contact_flag', $selected = '', $attribs = [])
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CONTACTFLAG_0'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CONTACTFLAG_1'));
		$options[] = HTMLHelper::_('select.option', '2', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CONTACTFLAG_2'));
		$options[] = HTMLHelper::_('select.option', '3', Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CONTACTFLAG_3'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Return a generic drop-down list
	 *
	 * @param   array   $list      An array of objects, arrays, or scalars.
	 * @param   string  $name      The value of the HTML name attribute.
	 * @param   mixed   $attribs   Additional HTML attributes for the <select> tag. This
	 *                             can be an array of attributes, or an array of options. Treated as options
	 *                             if it is the last argument passed. Valid options are:
	 *                             Format options, see {@see HTMLHelper::$formatOptions}.
	 *                             Selection options, see {@see HTMLHelperSelect::options()}.
	 *                             list.attr, string|array: Additional attributes for the select
	 *                             element.
	 *                             id, string: Value to use as the select element id attribute.
	 *                             Defaults to the same as the name.
	 *                             list.select, string|array: Identifies one or more option elements
	 *                             to be selected, based on the option key values.
	 * @param   mixed   $selected  The key that is selected (accepts an array or a string).
	 * @param   string  $idTag     Value of the field id or null by default
	 *
	 * @return  string  HTML for the select list
	 */
	protected static function genericlist($list, $name, $attribs = null, $selected = null, $idTag = null)
	{
		if (empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$temp = '';

			foreach ($attribs as $key => $value)
			{
				$temp .= ' ' . $key . '="' . $value . '"';
			}

			$attribs = $temp;
		}

		return HTMLHelper::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	/**
	 * Generates an HTML radio list.
	 *
	 * @param   array    $list      An array of objects
	 * @param   string   $name      The value of the HTML name attribute
	 * @param   string   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string   $selected  The name of the object variable for the option text
	 * @param   boolean  $idTag     Value of the field id or null by default
	 *
	 * @return  string  HTML for the select list
	 */
	protected static function genericradiolist($list, $name, $attribs = null, $selected = null, $idTag = null)
	{
		if (empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$temp = '';

			foreach ($attribs as $key => $value)
			{
				$temp .= $key . ' = "' . $value . '"';
			}

			$attribs = $temp;
		}

		return HTMLHelper::_('select.radiolist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	/**
	 * Returns the current Akeeba Subscriptions container object
	 *
	 * @return  Container
	 */
	protected static function getContainer()
	{
		static $container = null;

		if (is_null($container))
		{
			$container = Container::getInstance('com_akeebasubs');
		}

		return $container;
	}

}
