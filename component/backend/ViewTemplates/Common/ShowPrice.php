<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Price display field
 * Use it $this->loadAnyTemplate('admin:com_akeebasubs/Common/ShowPrice', $params)
 *
 * $params is an array defining the following keys (they are expanded into local scope vars automatically):
 *
 * @var \FOF30\Model\DataModel $item  The current row
 * @var string                 $field The name of the field in the current row containing the value
 * @var string                 $class
 *
 * Variables made automatically available to us by FOF:
 *
 * @var \FOF30\View\DataView\DataViewInterface $this
 */

defined('_JEXEC') or die;

// Parameters from the component's configuration
$currencyPos    = $this->getContainer()->params->get('currencypos', 'before');
$currencySymbol = $this->getContainer()->params->get('currencysymbol', '€');

// Get field parameters
$value = $item->getFieldValue($field);
$class = isset($class) ? $class = "class=\"$class\" " : '';

// Start the HTML output
$html = "<span $class>";

// First line: regular price
if ($currencyPos == 'before')
{
	$html .= $currencySymbol;
}

$html .= ' ' . sprintf('%02.02f', (float) $value) . ' ';

if ($currencyPos == 'after')
{
	$html .= $currencySymbol;
}

// End the HTML output
$html .= '</span>';

echo $html;
