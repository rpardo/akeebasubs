<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Admin\Model\CreditNoteTemplates $item */
$item = $this->getItem();

?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
    <div class="akeeba-form-group">
        <label for="akeebasubs_level_id">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_TITLE')
        </label>
		<?php echo BrowseView::modelSelect('akeebasubs_invoicetemplate_id', 'InvoiceTemplates', $item->akeebasubs_invoicetemplate_id, ['fof.autosubmit' => false, 'translate' => false]) ?>
    </div>

    <div class="akeeba-form-group">
        <label for="enabled">
            @lang('JPUBLISHED')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)
    </div>

    <div class="akeeba-form-group">
        <label for="globalformat">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_GLOBALFORMAT')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'globalformat', $item->globalformat)
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_GLOBALFORMAT_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="localformat">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_FORMAT')
        </label>
        <input type="text" name="localformat" id="localformat" value="{{{ $item->format }}}" />
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_FORMAT_HELP')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="globalnumbering">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_GLOBALNUMBERING')
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
        <label for="template">
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_TEMPLATE')
        </label>
        <div class="akeeba-nofef">
            @jhtml('FEFHelper.edit.editor', 'template', $this->getItem()->template)
        </div>
    </div>

@stop
