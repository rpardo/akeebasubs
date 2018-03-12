<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * @var  FOF30\View\DataView\Html $this
 * @var  LevelGroups              $model
 */

use \Akeeba\Subscriptions\Site\Model\LevelGroups;
use FOF30\Utils\FEFHelper\Html as FEFHtml;
use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

$model = $this->getModel();
?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-table-header')
{{-- ### FILTER ROW ### --}}
<tr>
    {{-- Row select --}}
    <th width="20px"></th>
    {{-- ID --}}
    <th width="20px"></th>
    {{-- Title --}}
    <th>
        @searchfilter('title')
    </th>
    {{-- Enabled --}}
    <th width="8%">
        {{ BrowseView::publishedFilter('enabled', 'JENABLED') }}
    </th>
</tr>
{{-- ### HEADER ROW ### --}}
<tr>
    {{-- Row select --}}
    <th>
        @jhtml('FEFHelper.browse.checkall')
    </th>
    {{-- ID --}}
    <th>
        @sortgrid('akeebasubs_level_id', 'JGLOBAL_NUM')
    </th>
    {{-- Title --}}
    <th>
        @sortgrid('title')
    </th>
    {{-- Enabled --}}
    <th>
        @sortgrid('enabled', 'JENABLED')
    </th>
</tr>
@stop

@section('browse-table-body-withrecords')
{{-- Table body shown when records are present. --}}
<?php $i = 0; ?>
@foreach($this->items as $row)
    <tr>
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
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=LevelGroups&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->title }}}
            </a>
        </td>
        {{-- Enabled --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i)
        </td>
    </tr>
@endforeach
@stop
