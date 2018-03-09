<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var \FOF30\View\DataView\DataViewInterface $this */
/** @var string $class */
/** @var float|string $value */
/** @var \FOF30\Model\DataModel $item */

$currencyPos    = $this->getContainer()->params->get('currencypos', 'before');
$currencySymbol = $this->getContainer()->params->get('currencysymbol', '€');

// Get field parameters
$class = '';

if (isset($class))
{
	$class = "class=\"$class\" ";
}

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

// Second line: sign-up fee
if (property_exists($item, 'signupfee') && ($item->signupfee >= 0.01))
{
	$html .= '<br /><span class="small">( ';
	$html .= JText::_('COM_AKEEBASUBS_LEVEL_FIELD_SIGNUPFEE_LIST');

	if ($currencyPos == 'before')
	{
		$html .= ' ' . $currencySymbol;
	}

	$html .= sprintf('%02.02f', (float) $item->signupfee);

	if ($currencyPos == 'after')
	{
		$html .= $currencySymbol;
	}

	$html .= ' )</span>';
}

// End the HTML output
$html .= '</span>';

echo $html;
