<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\Upgrades;
use FOF30\Utils\FEFHelper\Html as FEFHtml;
use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */
/** @var  Upgrades  $row */
$model = $this->getModel();

// Options for the Mode column
$modeOptions = [
	'rules' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_RULES'),
	'fixed' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FIXED'),
	'flexi' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FLEXI'),
];

?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-filters')
	<div class="akeeba-filter-element akeeba-form-group">
		@searchfilter('title')
	</div>
	<div class="akeeba-filter-element akeeba-form-group">
		{{ BrowseView::modelFilter('from_id', 'title', 'Levels')  }}
	</div>
	<div class="akeeba-filter-element akeeba-form-group">
		{{ BrowseView::modelFilter('to_id', 'title', 'Levels')  }}
	</div>
	<div class="akeeba-filter-element akeeba-form-group">
		@selectfilter('combine', SelectOptions::getOptions('boolean'))
	</div>
	<div class="akeeba-filter-element akeeba-form-group">
		@selectfilter('expired', SelectOptions::getOptions('boolean'))
	</div>
	<div class="akeeba-filter-element akeeba-form-group">
		{{ BrowseView::publishedFilter('enabled', 'JENABLED') }}
	</div>
@stop

@section('browse-table-header')
{{-- ### HEADER ROW ### --}}
<tr>
    {{-- Drag'n'drop reordering --}}
    <th width="20px">
        @jhtml('FEFHelper.browse.orderfield', 'ordering')
    </th>
    {{-- Row select --}}
    <th width="20px">
        @jhtml('FEFHelper.browse.checkall')
    </th>
	{{-- ID --}}
	<th width="20px">
		@sortgrid('akeebasubs_upgrade_id', 'JGLOBAL_NUM')
	</th>
	{{-- Title --}}
	<th>
		@sortgrid('title')
	</th>
	{{-- from_id --}}
    <th>
        @sortgrid('from_id')
    </th>
    {{-- to_id --}}
    <th>
        @sortgrid('to_id')
    </th>
	{{-- min_presence --}}
	<th>
		@sortgrid('min_presence')
	</th>
	{{-- max_presence --}}
	<th>
		@sortgrid('max_presence')
	</th>
	{{-- value --}}
	<th>
		@sortgrid('value')
	</th>
	{{-- combine --}}
	<th>
		@sortgrid('combine')
	</th>
	{{-- expired --}}
	<th>
		@sortgrid('expired')
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
		{{-- Title --}}
		<td>
			<a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=Upgrades&task=edit&id=[ITEM:ID]', $row))">
				{{{ $row->title }}}
			</a>
		</td>
        {{-- from_id --}}
        <td>
			{{{  BrowseView::modelOptionName($row->from_id, 'Levels') }}}
        </td>
        {{-- to_id --}}
        <td>
			{{{  BrowseView::modelOptionName($row->to_id, 'Levels') }}}
        </td>
		{{-- min_presence --}}
		<td>
			{{{ $row->min_presence }}}
		</td>
		{{-- max_presence --}}
		<td>
			{{{ $row->max_presence }}}
		</td>
		{{-- Value --}}
		<td>
			@include('admin:com_akeebasubs/Common/ShowDiscount', ['item' => $row, 'field' => 'value'])
		</td>
		{{-- combine --}}
		<td>
			@jhtml('FEFHelper.browse.published', $row->combine, $i, '', false)
		</td>
		{{-- expired --}}
		<td>
			@jhtml('FEFHelper.browse.published', $row->expired, $i, '', false)
		</td>
        {{-- Enabled --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i)
        </td>
    </tr>
@endforeach
@stop
