<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * @var  FOF30\View\DataView\Html $this
 * @var  InvoiceTemplates         $row
 * @var  InvoiceTemplates         $model
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Model\InvoiceTemplates;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;

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

@section('browse-filters')
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('title')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @selectfilter('isbusiness', $businessOptions)
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @selectfilter('country', \Akeeba\Subscriptions\Admin\Helper\Select::getCountriesForHeader())
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        {{ \FOF30\Utils\FEFHelper\BrowseView::publishedFilter('enabled', 'JENABLED') }}
    </div>
@stop

@section('browse-table-header')
    <tr>
        <th width="20">
            @jhtml('FEFHelper.browse.orderfield', 'ordering')
        </th>
        <th width="20">
            @sortgrid('akeebasubs_subscription_id', 'JGLOBAL_NUM')
        </th>
        <th width="20">
            @lang('JGLOBAL_NUM')
        </th>
        <th>
            @sortgrid('title')
        </th>
        <th width="15%">
            @fieldtitle('levels')
        </th>
        <th width="8%">
            @sortgrid('isbusiness')
        </th>
        <th width="15%">
            @sortgrid('country')
        </th>
        <th width="10%">
            @sortgrid('noinvoice')
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
                {{{ sprintf('%05d', $row->getId()) }}}
            </td>
            <td>
                <a href="@route(\FOF30\Utils\FEFHelper\BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=InvoiceTemplate&id=[ITEM:ID]', $row))">
                    {{{ $row->title }}}
                </a>
            </td>
            <td>
                {{ \Akeeba\Subscriptions\Admin\Helper\Format::formatInvTempLevels($row->levels) }}
            </td>
            <td>
                {{{ \FOF30\Utils\FEFHelper\BrowseView::getOptionName($row->isbusiness, $businessOptions) }}}
            </td>
            <td>
                {{{ \FOF30\Utils\FEFHelper\BrowseView::getOptionName($row->country, \Akeeba\Subscriptions\Admin\Helper\Select::getCountries()) }}}
            </td>
            <td>
                {{{ \FOF30\Utils\FEFHelper\BrowseView::getOptionName($row->noinvoice, $noinvoiceOptions) }}}
            </td>
            <td>
                @jhtml('FEFHelper.browse.published', $row->enabled, $i)
            </td>
        </tr>
    @endforeach
@stop
