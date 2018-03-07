<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\Levels;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

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
    <th></th>
    {{-- Row select --}}
    <th></th>
    {{-- ID --}}
    <th></th>
    {{-- Image --}}
    <th></th>
    {{-- Title --}}
    <th>
        <input type="text" name="title" placeholder="@lang('COM_AKEEBASUBS_LEVELS_FIELD_TITLE')"
               id="filter_title" onchange="document.adminForm.submit()"
               value="{{{ $this->getModel()->getState('title') }}}"
               title="@lang('COM_AKEEBASUBS_LEVELS_FIELD_TITLE')"/>
    </th>
    {{-- Level Group --}}
    @modelfilter('akeebasubs_levelgroup_id', 'title', 'LevelGroups')
    <th></th>
    {{-- Duration --}}
    <th></th>
    {{-- Recurring --}}
    <th></th>
    {{-- Price --}}
    <th></th>
    {{-- TODO Access --}}
    <th></th>
    {{-- TODO Enabled --}}
    <th></th>
</tr>
{{-- ### HEADER ROW ### --}}
<tr>
    {{-- Drag'n'drop reordering --}}
    <th width="20px">
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
    <th width="32">
        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
    </th>
    {{-- ID --}}
    <th width="20px">
        @lang('JGLOBAL_NUM')
    </th>
    {{-- Image --}}
    <th width="32px">
        &nbsp;
    </th>
    {{-- Title --}}
    <th>
        @sortgrid('title')
    </th>
    {{-- Level Group --}}
    <th width="8%">
        @sortgrid('akeebasubs_levelgroup_id', 'COM_AKEEBASUBS_LEVELS_FIELD_LEVELGROUP')
    </th>
    {{-- Duration --}}
    <th>
        @fieldtitle('duration')
    </th>
    {{-- Recurring --}}
    <th>
        @fieldtitle('recurring')
    </th>
    {{-- Price --}}
    <th>
        @fieldtitle('price')
    </th>
    {{-- Access --}}
    <th width="8%">
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
            <?php echo \JHtml::_('grid.id', ++$i, $row->id); ?>
        </td>
        {{-- ID --}}
        <td>
            {{{ $row->id }}}
        </td>
        {{-- Image --}}
        <td>
            @jhtml('image', $this->getContainer()->platform->URIroot() . '/' . $row->image, null, ['width' => '16'])
        </td>
        {{-- TODO Title --}}
        <td></td>
        {{-- TODO Level Group --}}
        <td></td>
        {{-- TODO Duration --}}
        <td></td>
        {{-- TODO Recurring --}}
        <td></td>
        {{-- TODO Price --}}
        <td></td>
        {{-- TODO Access --}}
        <td></td>
        {{-- TODO Enabled --}}
        <td></td>
    </tr>
@endforeach
@stop
