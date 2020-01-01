<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/**
 * Our main element class
 */
class JFormFieldHeader extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'header';

	function fetchElement($name, $value, &$node, $control_name)
	{
		return '<hr/>';
	}
	
	function getInput()
	{
		return '';
	}
}
