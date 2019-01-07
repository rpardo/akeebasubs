<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\Relations;
use FOF30\Utils\FEFHelper\Html as FEFHtml;
use FOF30\Utils\FEFHelper\BrowseView;use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

/**
 * @var  FOF30\View\DataView\Html $this
 * @var  Relations  $row
 * @var  Relations  $model
 */
$model = $this->getModel();

// Options for the Mode column
$modeOptions = [
	'rules' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_RULES'),
	'fixed' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FIXED'),
	'flexi' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FLEXI'),
];

?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-table-header')
{{-- ### FILTER ROW ### --}}
<tr>
    {{-- Drag'n'drop reordering --}}
    <th width="20px"></th>
    {{-- Row select --}}
    <th width="32"></th>
    {{-- source_level_id --}}
    <th>
        {{ BrowseView::modelFilter('source_level_id', 'title', 'Levels')  }}
    </th>
    {{-- target_level_id --}}
    <th>
        {{ BrowseView::modelFilter('target_level_id', 'title', 'Levels')  }}
    </th>
    {{-- mode --}}
    <th>
        {{ \FOF30\Utils\FEFHelper\BrowseView::selectFilter('mode', $modeOptions) }}
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
    {{-- source_level_id --}}
    <th>
        @sortgrid('source_level_id')
    </th>
    {{-- target_level_id --}}
    <th>
        @sortgrid('target_level_id')
    </th>
    {{-- mode --}}
    <th>
        @sortgrid('mode')
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

        {{-- source_level_id --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=Relations&task=edit&id=[ITEM:ID]', $row))">
                {{{  BrowseView::modelOptionName($row->source_level_id, 'Levels') }}}
            </a>

        </td>
        {{-- target_level_id --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=Relations&task=edit&id=[ITEM:ID]', $row))">
                {{{  BrowseView::modelOptionName($row->target_level_id, 'Levels') }}}
            </a>
        </td>
        {{-- mode --}}
        <td>
            {{ BrowseView::getOptionName($row->access, $modeOptions) }}
        </td>
        {{-- Enabled --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i)
        </td>
    </tr>
@endforeach
@stop
