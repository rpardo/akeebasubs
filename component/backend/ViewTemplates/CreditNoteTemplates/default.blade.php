<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * @var  FOF30\View\DataView\Html $this
 * @var  CreditNoteTemplates         $row
 * @var  CreditNoteTemplates         $model
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Model\CreditNoteTemplates;

$model = $this->getModel();

$businessOptions = [
	'-1' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_ISBUSINESS_INDIFFERENT'),
	'0' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_ISBUSINESS_PERSONAL'),
	'1' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_ISBUSINESS_BUSINESS'),
];

$noinvoiceOptions = [
	'0' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_NOINVOICE_ALLOW'),
	'1' => JText::_('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_NOINVOICE_PREVENT'),
];
?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-table-header')
    <tr>
        <th width="20">
            @jhtml('FEFHelper.browse.orderfield', 'ordering')
        </th>
        <th width="20">
            @sortgrid('akeebasubs_invoicetemplate_id', 'JGLOBAL_NUM')
        </th>
        <th width="20">
            @lang('JGLOBAL_NUM')
        </th>
        <th>
            @lang('COM_AKEEBASUBS_INVOICETEMPLATES_FIELD_TITLE')
        </th>
        <th width="8%">
            @sortgrid('enabled', 'JENABLED')
        </th>
    </tr>
@stop

@section('browse-table-body-withrecords')
    {{-- Table body shown when records are present. --}}
	<?php $i = 0; ?>
    @foreach($this->items as $row)
        <tr>
            <td width="20">
                @jhtml('FEFHelper.browse.order', 'ordering', $row->ordering)
            </td>
            <td>
                @jhtml('FEFHelper.browse.id', ++$i, $row->getId())
            </td>
            <td>
                {{{ $row->akeebasubs_invoicetemplate_id }}}
            </td>
            <td>
                <a href="@route(\FOF30\Utils\FEFHelper\BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=CreditNoteTemplate&id=[ITEM:ID]', $row))">
                    @unless(is_null($row->invoiceTemplate))
                    {{{ $row->invoiceTemplate->title }}}
                    @else
                    &mdash;&mdash;&mdash;
                    @endunless
                </a>
            </td>
            <td>
                @jhtml('FEFHelper.browse.published', $row->enabled, $i)
            </td>
        </tr>
    @endforeach
@stop
