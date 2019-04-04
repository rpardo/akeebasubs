<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

use FOF30\Container\Container;
use FOF30\Model\DataModel;
use JHtml;
use JLoader;
use JText;

defined('_JEXEC') or die;

/**
 * A helper class for drop-down selection boxes
 */
abstract class Select
{
	/**
	 * Returns a list of known invoicing extensions supported by plugins
	 *
	 * @return  array  extension => title
	 */
	public static function getInvoiceExtensions()
	{
		static $invoiceExtensions = null;

		if (is_null($invoiceExtensions))
		{
			$source = Container::getInstance('com_akeebasubs')->factory
				->model('Invoices')->tmpInstance()
				->getExtensions(0);
			$invoiceExtensions = array();

			if (!empty($source))
			{
				foreach ($source as $item)
				{
					$invoiceExtensions[ $item['extension'] ] = $item['title'];
				}
			}
		}

		return $invoiceExtensions;
	}

	/**
	 * Return a generic drop-down list
	 *
	 * @param   array   $list      An array of objects, arrays, or scalars.
	 * @param   string  $name      The value of the HTML name attribute.
	 * @param   mixed   $attribs   Additional HTML attributes for the <select> tag. This
	 *                             can be an array of attributes, or an array of options. Treated as options
	 *                             if it is the last argument passed. Valid options are:
	 *                             Format options, see {@see JHtml::$formatOptions}.
	 *                             Selection options, see {@see JHtmlSelect::options()}.
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

		return JHtml::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	/**
	 * Generates an HTML radio list.
	 *
	 * @param   array    $list       An array of objects
	 * @param   string   $name       The value of the HTML name attribute
	 * @param   string   $attribs    Additional HTML attributes for the <select> tag
	 * @param   string   $selected   The name of the object variable for the option text
	 * @param   boolean  $idTag      Value of the field id or null by default
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

		return JHtml::_('select.radiolist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
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
		$options = array(
			JHtml::_('select.option', '', '---'),
			JHtml::_('select.option', '0', JText::_('JNo')),
			JHtml::_('select.option', '1', JText::_('JYes'))
		);

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Displays a list of the available user groups.
	 *
	 * @param   string   $name      The form field name.
	 * @param   string   $selected  The name of the selected section.
	 * @param   array    $attribs   Additional attributes to add to the select field.
	 *
	 * @return  string   The HTML for the list
	 */
	public static function usergroups($name = 'usergroups', $selected = '', $attribs = array())
	{
		return JHtml::_('access.usergroup', $name, $selected, $attribs, false);
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
	public static function published($selected = null, $id = 'enabled', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', null, '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECTSTATE') . ' -');
		$options[] = JHtml::_('select.option', 0, JText::_('JUNPUBLISHED'));
		$options[] = JHtml::_('select.option', 1, JText::_('JPUBLISHED'));

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
	public static function languages($selected = null, $id = 'language', $attribs = array())
	{
		JLoader::import('joomla.language.helper');
		$languages = \JLanguageHelper::getLanguages('lang_code');
		$options   = array();
		$options[] = JHtml::_('select.option', '*', JText::_('JALL_LANGUAGE'));

		if (!empty($languages))
		{
			foreach ($languages as $key => $lang)
			{
				$options[] = JHtml::_('select.option', $key, $lang->title);
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
	public static function paystates($selected = null, $id = 'state', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE') . ' -');

		$types = array('N', 'P', 'C', 'X');

		foreach ($types as $type)
		{
			$options[] = JHtml::_('select.option', $type, JText::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_' . $type));
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
	public static function coupontypes($name = 'type', $selected = 'value', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = JHtml::_('select.option', 'value', JText::_('COM_AKEEBASUBS_COUPON_TYPE_VALUE'));
		$options[] = JHtml::_('select.option', 'percent', JText::_('COM_AKEEBASUBS_COUPON_TYPE_PERCENT'));

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
	public static function subscriptionlevels($selected = null, $id = 'akeebasubs_level_id', $attribs = array())
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
	public static function levels($name = 'level', $selected = '', $attribs = array())
	{
		/** @var DataModel $model */
		$model =  Container::getInstance('com_akeebasubs')->factory
			->model('Levels')->tmpInstance();

		$list = $model->filter_order('ordering')->filter_order_Dir('ASC')->get(true);

		$options = array();

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
			$options[] = JHtml::_('select.option', '-1', JText::_('COM_AKEEBASUBS_COMMON_SELECTLEVEL_NONE'));
		}

		if ($include_all)
		{
			$options[] = JHtml::_('select.option', '0', JText::_('COM_AKEEBASUBS_COMMON_SELECTLEVEL_ALL'));
		}

		if ($include_clear || (!$include_none && !$include_all))
		{
			$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		}

		foreach ($list as $item)
		{
			$options[] = JHtml::_('select.option', $item->akeebasubs_level_id, $item->title);
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
	public static function discountmodes($name = 'discountmode', $selected = '', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT') . ' -');
		$options[] = JHtml::_('select.option', 'none', JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_NONE'));
		$options[] = JHtml::_('select.option', 'coupon', JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_COUPON'));
		$options[] = JHtml::_('select.option', 'upgrade', JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_UPGRADE'));

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
	public static function upgradetypes($name = 'type', $selected = 'value', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = JHtml::_('select.option', 'value', JText::_('COM_AKEEBASUBS_UPGRADE_TYPE_VALUE'));
		$options[] = JHtml::_('select.option', 'percent', JText::_('COM_AKEEBASUBS_UPGRADE_TYPE_PERCENT'));
		$options[] = JHtml::_('select.option', 'lastpercent', JText::_('COM_AKEEBASUBS_UPGRADE_TYPE_LASTPERCENT'));

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
	public static function fieldtypes($name = 'type', $selected = 'text', $attribs = array())
	{
		$fieldTypes = self::getFieldTypes();

		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		foreach ($fieldTypes as $type => $desc)
		{
			$options[] = JHtml::_('select.option', $type, $desc);
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
	public static function relationmode($name = 'mode', $selected = 'rules', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = JHtml::_('select.option', 'rules', JText::_('COM_AKEEBASUBS_RELATIONS_MODE_RULES'));
		$options[] = JHtml::_('select.option', 'fixed', JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FIXED'));
		$options[] = JHtml::_('select.option', 'flexi', JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FLEXI'));

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
	public static function flexiperioduoms($name = 'flex_uom', $selected = 'rules', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = JHtml::_('select.option', 'd', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_D'));
		$options[] = JHtml::_('select.option', 'w', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_W'));
		$options[] = JHtml::_('select.option', 'm', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_M'));
		$options[] = JHtml::_('select.option', 'y', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_Y'));

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
	public static function flexitimecalc($name = 'flex_timecalculation', $selected = 'current', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = JHtml::_('select.option', 'current', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMECALCULATION_CURRENT'));
		$options[] = JHtml::_('select.option', 'future', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMECALCULATION_FUTURE'));

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
	public static function flexirounding($name = 'flex_rounding', $selected = 'round', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = JHtml::_('select.option', 'floor', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_FLOOR'));
		$options[] = JHtml::_('select.option', 'ceil', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_CEIL'));
		$options[] = JHtml::_('select.option', 'round', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_ROUND'));

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
	public static function flexiexpiration($name = 'expiration', $selected = 'replace', $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		$options[] = JHtml::_('select.option', 'replace', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_REPLACE'));
		$options[] = JHtml::_('select.option', 'after', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_AFTER'));
		$options[] = JHtml::_('select.option', 'overlap', JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_OVERLAP'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	/**
	 * Drop down list of invoice extensions
	 *
	 * @param   string  $name      The field's name
	 * @param   string  $selected  Pre-selected value
	 * @param   array   $attribs   Field attributes
	 *
	 * @return  string  The HTML of the drop-down
	 */
	public static function invoiceextensions($name = 'extension', $selected = '', $attribs = array())
	{
		/** @var \Akeeba\Subscriptions\Admin\Model\Invoices $model */
		$model = Container::getInstance('com_akeebasubs')->factory
			->model('Invoices')->tmpInstance();

		$options = $model->getExtensions(1);
		$option = JHtml::_('select.option', '', '- ' . JText::_('COM_AKEEBASUBS_COMMON_SELECT') . ' -');
		array_unshift($options, $option);

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
	public static function csvdelimiters($name = 'csvdelimiters', $selected = 1, $attribs = array())
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '1', 'abc, def');
		$options[] = JHtml::_('select.option', '2', 'abc; def');
		$options[] = JHtml::_('select.option', '3', '"abc"; "def"');
		$options[] = JHtml::_('select.option', '-99', JText::_('COM_AKEEBASUBS_IMPORT_DELIMITERS_CUSTOM'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
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
