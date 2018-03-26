<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Site\Model\BlockRules;
use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

/**
 * @var  FOF30\View\DataView\Html $this
 * @var  BlockRules               $row
 * @var  BlockRules               $model
 */

$model = $this->getModel();
?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-filters')
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('username')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('name')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('email')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('iprange')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @selectfilter('enabled', \FOF30\Utils\SelectOptions::getOptions('published'), 'JENABLED')
    </div>
@stop

@section('browse-table-header')
<tr>
    <th width="20">
        @jhtml('FEFHelper.browse.checkall')
    </th>
    <th>
        @sortgrid('username')
    </th>
    <th>
        @sortgrid('name')
    </th>
    <th>
        @sortgrid('email')
    </th>
    <th>
        @sortgrid('iprange')
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
        <td>
            @jhtml('FEFHelper.browse.id', ++$i, $row->getId())
        </td>

        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=BlockRules&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->username }}}
            </a>
        </td>
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=BlockRules&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->name }}}
            </a>
        </td>
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=BlockRules&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->email }}}
            </a>
        </td>
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=BlockRules&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->iprange }}}
            </a>
        </td>

        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i)
        </td>
    </tr>
@endforeach
@stop
