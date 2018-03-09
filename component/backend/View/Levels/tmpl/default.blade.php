<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
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
        <input type="text" name="title" placeholder="@lang('COM_AKEEBASUBS_LEVELS_FIELD_TITLE')"
               id="filter_title" onchange="document.adminForm.submit()"
               value="{{{ $this->getModel()->getState('title') }}}"
               title="@lang('COM_AKEEBASUBS_LEVELS_FIELD_TITLE')"/>
    </th>
    {{-- Level Group --}}
    <th width="8%">
        {{ BrowseView::modelFilter('akeebasubs_levelgroup_id', 'title', 'LevelGroups', 'COM_AKEEBASUBS_LEVELS_FIELD_LEVELGROUP')  }}
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
        <a href="#"
           onclick="Joomla.tableOrdering('ordering','asc','');return false;"
           class="hasPopover"
           title=""
           data-content="@lang('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN')"
           data-placement="top"
           data-original-title="Ordering"
        >
            <span class="icon-menu-2"></span>
        </a>
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
    {{-- Level Group --}}
    <th>
        @sortgrid('akeebasubs_levelgroup_id', 'COM_AKEEBASUBS_LEVELS_FIELD_LEVELGROUP')
    </th>
    {{-- Duration --}}
    <th>
        @sortgrid('duration')
    </th>
    {{-- Recurring --}}
    <th>
        @sortgrid('recurring')
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
            <?php echo FEFHtml::dragDropReordering($this, 'ordering', $row->ordering)?>
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
        {{-- Level Group --}}
        <td>
            {{{  BrowseView::modelOptionName($row->akeebasubs_levelgroup_id, 'LevelGroups') }}}
        </td>
        {{-- Duration --}}
        <td>
            {{{ $row->duration }}}
        </td>
        {{-- Recurring --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i, '', false)
        </td>
        <td>
            @include('admin:com_akeebasubs/Common/LevelPrice', array('item' => $row, 'value' => $row->price))
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
