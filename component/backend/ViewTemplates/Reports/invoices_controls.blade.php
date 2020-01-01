<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

use \Akeeba\Subscriptions\Admin\Helper\Select;

JHtml::_('formbehavior.chosen', 'select.akeebasubsChosen');

/** @var \FOF30\View\View $this */

$optionsMonths = [
	'1'  => JText::_('JANUARY'),
	'2'  => JText::_('FEBRUARY'),
	'3'  => JText::_('MARCH'),
	'4'  => JText::_('APRIL'),
	'5'  => JText::_('MAY'),
	'6'  => JText::_('JUNE'),
	'7'  => JText::_('JULY'),
	'8'  => JText::_('AUGUST'),
	'9'  => JText::_('SEPTEMBER'),
	'10' => JText::_('OCTOBER'),
	'11' => JText::_('NOVEMBER'),
	'12' => JText::_('DECEMBER'),
];

$optionsYears = array();
$nextYear     = intval(gmdate('Y')) + 1;

for ($year = 2010; $year <= $nextYear; $year++)
{
	$optionsYears[] = JHtml::_('select.option', $year, $year);
}

$printUrl = JURI::getInstance();
$printUrl->setVar('tmpl', 'component');
$template_id = isset($params['template_id']) ? $params['template_id'] : null;
?>

<form action="index.php" method="get" name="invoiceControlsForm" class="akeeba-form--inline">
    <div class="akeeba-form-group">
        @jhtml('FEFHelper.select.genericlist', $optionsMonths, 'month', ['onchange' => 'document.forms.invoiceControlsForm.submit()', 'list.select' => $params['month']])
    </div>

    <div class="akeeba-form-group">
        @jhtml('FEFHelper.select.genericlist', $optionsYears, 'year', ['onchange' => 'document.forms.invoiceControlsForm.submit()', 'list.select' => $params['year']])
    </div>

    <div class="akeeba-form-group">
        <button class="akeeba-btn--primary">
            <span class="akion-search"></span>
            @lang('COM_AKEEBASUBS_REPORTS_INVOICES_BTN_LOAD')
        </button>
    </div>

    <div class="akeeba-form-group">
        <a href="@route($printUrl)" class="akeeba-btn--green" target="_blank">
            <span class="akion-printer"></span>
            @lang('COM_AKEEBASUBS_REPORTS_INVOICES_BTN_PRINT')
        </a>
    </div>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" value="com_akeebasubs">
        <input type="hidden" name="view" value="Reports">
        <input type="hidden" name="task" value="{{{ $this->input->getCmd('task', 'invoices') }}}">
    </div>
</form>
