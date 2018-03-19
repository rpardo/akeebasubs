<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

$businessOptions = [
	'-1' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_ISBUSINESS_INDIFFERENT'),
	'0' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_ISBUSINESS_PERSONAL'),
	'1' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_ISBUSINESS_BUSINESS'),
];

$noinvoiceOptions = [
	'0' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_NOINVOICE_ALLOW'),
	'1' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_NOINVOICE_PREVENT'),
];

/** @var \Akeeba\Subscriptions\Admin\Model\InvoiceTemplates $item */
$item = $this->getItem();

?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')

    <div class="akeeba-form-group">
        <label for="title">
            @fieldtitle('title')
        </label>
        <input type="text" name="title" id=title" value="{{{ $item->title }}}"/>
    </div>

    <div class="akeeba-form-group">
        <label for="enabled">
            @lang('JPUBLISHED')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)
    </div>

    <div class="akeeba-form-group">
        <label for="akeebasubs_level_id">
            @lang('COM_AKEEBASUBS_SUBSCRIPTION_LEVEL')
        </label>
        @jhtml('formbehavior.chosen', 'select.akeebasubsChosen')
	    <?php echo BrowseView::modelSelect('levels[]', 'Levels', $item->levels, ['fof.autosubmit' => false, 'translate' => false, 'multiple' => 'multiple', 'size' => 5, 'class' => 'akeebasubsChosen']) ?>
    </div>

    <div class="akeeba-form-group">
        <label for="globalformat">
            @fieldtitle('globalformat')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'globalformat', $item->globalformat)
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_GLOBALFORMAT_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="localformat">
            @fieldtitle('format')
        </label>
        <input type="text" name="localformat" id="localformat" value="{{{ $item->format }}}" />
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_FORMAT_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="globalnumbering">
            @fieldtitle('globalnumbering')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'globalnumbering', $item->globalnumbering)
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_GLOBALNUMBERING_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="numberreset">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_NUMBER_RESET')
        </label>
        <input type="number" step="1" min="0" name="numberreset" id="numberreset" value="{{{ $item->numberreset }}}" />
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_NUMBER_RESET_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="isbusiness">
            @fieldtitle('isbusiness')
        </label>
        @jhtml('FEFHelper.select.genericlist', $businessOptions, 'isbusiness', ['list.select' => $item->isbusiness])
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_ISBUSINESS_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="country">
            @fieldtitle('country')
        </label>
        @jhtml('FEFHelper.select.genericlist', \Akeeba\Subscriptions\Admin\Helper\Select::getCountries(), 'country', ['list.select' => $item->country])
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_COUNTRY_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="noinvoice">
            @fieldtitle('noinvoice')
        </label>
        @jhtml('FEFHelper.select.genericlist', $noinvoiceOptions, 'noinvoice', ['list.select' => $item->noinvoice])
    </div>

    <div class="akeeba-form-group">
        <label for="template">
            @fieldtitle('template')
        </label>
        <div class="akeeba-noreset">
            @jhtml('FEFHelper.edit.editor', 'template', $this->getItem()->template)
        </div>
    </div>

@stop
