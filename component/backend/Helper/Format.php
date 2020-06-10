<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

use DateTimeZone;
use FOF30\Container\Container;
use FOF30\Date\Date;
use FOF30\Model\DataModel;
use Joomla\CMS\Factory;
use JText;

defined('_JEXEC') or die;

/**
 * A helper class for formatting data for display
 */
abstract class Format
{
	/**
	 * Format a date for display.
	 *
	 * The $tzAware parameter defines whether the formatted date will be timezone-aware. If set to false the formatted
	 * date will be rendered in the UTC timezone. If set to true the code will automatically try to use the logged in
	 * user's timezone or, if none is set, the site's default timezone (Server Timezone). If set to a positive integer
	 * the same thing will happen but for the specified user ID instead of the currently logged in user.
	 *
	 * @param   string    $date     The date to format
	 * @param   string    $format   The format string, default is whatever you specified in the component options
	 * @param   bool|int  $tzAware  Should the format be timezone aware? See notes above.
	 *
	 * @return string
	 */
	public static function date($date, $format = null, $tzAware = true)
	{
		$utcTimeZone = new DateTimeZone('UTC');
		$jDate       = new Date($date, $utcTimeZone);

		// Which timezone should I use?
		$tz = null;

		if ($tzAware !== false)
		{
			$userId    = is_bool($tzAware) ? null : (int) $tzAware;

			try
			{
				$tzDefault = Factory::getApplication()->get('offset');
			}
			catch (\Exception $e)
			{
				$tzDefault = 'GMT';
			}

			$user      = Factory::getUser($userId);
			$tz        = $user->getParam('timezone', $tzDefault);
		}

		if (!empty($tz))
		{
			try
			{
				$userTimeZone = new DateTimeZone($tz);

				$jDate->setTimezone($userTimeZone);
			}
			catch(\Exception $e)
			{
				// Nothing. Fall back to UTC.
			}
		}


		if (empty($format))
		{
			$format = self::getContainer()->params->get('dateformat', 'Y-m-d H:i T');
			$format = str_replace('%', '', $format);
		}

		return $jDate->format($format, true);
	}

	/**
	 * Check if the given string is a valid date
	 *
	 * @param   string  $date  Date as string
	 *
	 * @return  bool|Date  False on failure, JDate if successful
	 */
	public static function checkDateFormat($date)
	{
		$regex = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';

		if (!preg_match($regex, $date))
		{
			return false;
		}

		return new Date($date);
	}

	/**
	 * Returns the human readable subscription level title based on the numeric subscription level ID given in $id
	 *
	 * @param   int  $id  The subscription level ID
	 *
	 * @return  string  The subscription level title, or three em-dashes if it's unknown
	 */
	public static function formatLevel($id)
	{
		static $levels;

		if (empty($levels))
		{
			/** @var DataModel $levelsModel */
			$levelsModel = Container::getInstance('com_akeebasubs')->factory
				->model('Levels')->tmpInstance();

			$rawlevels = $levelsModel
				->filter_order('ordering')
				->filter_order_Dir('ASC')
				->get(true);

			$levels = array();

			if (!empty($rawlevels))
			{
				foreach ($rawlevels as $rawlevel)
				{
					$levels[ $rawlevel->akeebasubs_level_id ] = $rawlevel->title;
				}
			}
		}

		if (array_key_exists($id, $levels))
		{
			return $levels[ $id ];
		}
		else
		{
			return '&mdash;&mdash;&mdash;';
		}
	}

	/**
	 * Format a list of subscription levels, as used in invoice templates
	 *
	 * @param   string|array  $ids  An array or a comma-separated list of IDs
	 *
	 * @return  string
	 */
	public static function formatInvTempLevels($ids)
	{
		if (empty($ids))
		{
			return JText::_('COM_AKEEBASUBS_COMMON_LEVEL_ALL');
		}
		if (empty($ids))
		{
			return JText::_('COM_AKEEBASUBS_COMMON_LEVEL_NONE');
		}

		if (!is_array($ids))
		{
			$ids = explode(',', $ids);
		}

		static $levels;

		if (empty($levels))
		{
			$levelsList = Container::getInstance('com_akeebasubs')->factory->model('Levels')->tmpInstance()->get(true);

			if (!empty($levelsList))
			{
				foreach ($levelsList as $level)
				{
					$levels[ $level->akeebasubs_level_id ] = $level->title;
				}
			}

			$levels[ - 1 ] = JText::_('COM_AKEEBASUBS_COMMON_LEVEL_NONE');
			$levels[0]     = JText::_('COM_AKEEBASUBS_COMMON_LEVEL_ALL');
		}

		$ret = array();

		foreach ($ids as $id)
		{
			if (array_key_exists($id, $levels))
			{
				$ret[] = $levels[ $id ];
			}
			else
			{
				$ret[] = '&mdash;';
			}
		}

		return implode(', ', $ret);
	}

	/**
	 * Format a value as money
	 *
	 * @param   float   $value  The money value to format
	 *
	 * @return  string  The HTML of the formatted price
	 */
	public static function formatPrice($value)
	{
		static $currencyPosition = null;
		static $currencySymbol = null;

		if (is_null($currencyPosition))
		{
			$currencyPosition = self::getContainer()->params->get('currencypos', 'before');
			$currencySymbol = self::getContainer()->params->get('currencysymbol', '€');
		}

		$html = '';
		if ($currencyPosition == 'before')
		{
			$html .= $currencySymbol . ' ';
		}

		$html .= sprintf('%2.2f', (float) $value);

		if ($currencyPosition != 'before')
		{
			$html .= ' ' . $currencySymbol;
		}

		return $html;
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
