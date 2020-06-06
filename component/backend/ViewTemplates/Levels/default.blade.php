<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\Levels;
use FOF30\Utils\FEFHelper\Html as FEFHtml;
use FOF30\Utils\FEFHelper\BrowseView;use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */
/** @var  Levels  $row */
$model = $this->getModel();

?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-table-header')
{{-- ### FILTER ROW ### --}}
<tr>
    {{-- Drag'n'drop reordering --}}
    <th width="20px"></th>
    {{-- Row select --}}
    <th width="32"></th>
    {{-- ID --}}
    <th width="20px"></th>
    {{-- Image --}}
    <th width="32px"></th>
    {{-- Title --}}
    <th>
        @searchfilter('title')
    </th>
    {{-- Duration --}}
    <th></th>
    {{-- Recurring --}}
    <th></th>
    {{-- Price --}}
    <th></th>
    {{-- Access --}}
    <th width="8%">
        {{ BrowseView::accessFilter('access', 'JFIELD_ACCESS_LABEL') }}
    </th>
    {{-- Enabled --}}
    <th width="8%">
        {{ BrowseView::publishedFilter('enabled', 'JENABLED') }}
    </th>
</tr>
{{-- ### HEADER ROW ### --}}
<tr>
    {{-- Drag'n'drop reordering --}}
    <th>
        @jhtml('FEFHelper.browse.orderfield', 'ordering')
    </th>
    {{-- Row select --}}
    <th>
        @jhtml('FEFHelper.browse.checkall')
    </th>
    {{-- ID --}}
    <th>
        @sortgrid('akeebasubs_level_id', 'JGLOBAL_NUM')
    </th>
    {{-- Image --}}
    <th>
        <span class="akion-camera hasTooltip" title="@jhtml('tooltipText', JText::_('COM_AKEEBASUBS_LEVEL_FIELD_IMAGE'))"></span>
    </th>
    {{-- Title --}}
    <th>
        @sortgrid('title')
    </th>
    {{-- Duration --}}
    <th>
        @sortgrid('duration')
    </th>
    {{-- Recurring --}}
    <th>
        @fieldtitle('recurring')
    </th>
    {{-- Price --}}
    <th>
        @sortgrid('price')
    </th>
    {{-- Access --}}
    <th>
        @sortgrid('access', 'JFIELD_ACCESS_LABEL')
    </th>
    {{-- Enabled --}}
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
        {{-- Drag'n'drop reordering --}}
        <td>
            @jhtml('FEFHelper.browse.order', 'ordering', $row->ordering)
        </td>
        {{-- Row select --}}
        <td>
            @jhtml('FEFHelper.browse.id', ++$i, $row->getId())
        </td>
        {{-- ID --}}
        <td>
            {{{ $row->getId() }}}
        </td>
        {{-- Image --}}
        <td>
            @jhtml('image', $this->getContainer()->platform->URIroot() . '/' . $row->image, null, ['width' => '16'])
        </td>
        {{-- Title --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=Levels&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->title }}}
            </a>
        </td>
        {{-- Duration --}}
        <td>
            {{{ $row->duration }}}
        </td>
        {{-- Recurring --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->upsell != 'never', $i, '', false)
        </td>
        <td>
            @include('admin:com_akeebasubs/Common/ShowPrice', array('item' => $row, 'field' => 'price'))
        </td>
        {{-- Access --}}
        <td>
            {{ BrowseView::getOptionName($row->access, SelectOptions::getOptions('access')) }}
        </td>
        {{-- Enabled --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i)
        </td>
    </tr>
@endforeach
@stop
