<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\States;
use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

/**
 * @var  FOF30\View\DataView\Html $this
 * @var  States                   $row
 * @var  States                   $model
 */

$model = $this->getModel();
?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-table-header')
{{-- ### HEADER ROW ### --}}
<tr>
    {{-- Row select --}}
    <th width="20"></th>
    {{-- ID --}}
    <th width="60"></th>
    {{-- Country --}}
    <th>
        @selectfilter('country', \Akeeba\Subscriptions\Admin\Helper\Select::getCountriesForHeader())
    </th>
    {{-- Label --}}
    <th>
        @searchfilter('label')
    </th>
    {{-- Enabled --}}
    <th width="60">
        @selectfilter('enabled', \FOF30\Utils\SelectOptions::getOptions('published'), 'JENABLED')
    </th>
</tr>
<tr>
    {{-- Row select --}}
    <th>
        @jhtml('FEFHelper.browse.checkall')
    </th>
    {{-- ID --}}
    <th>
        @sortgrid('akeebasubs_state_id', 'JGLOBAL_NUM')
    </th>
    {{-- Country --}}
    <th>
        @sortgrid('country')
    </th>
    {{-- Label --}}
    <th>
        @sortgrid('label')
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
        {{-- Country --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=States&task=edit&id=[ITEM:ID]', $row))">
                {{{ BrowseView::getOptionName($row->country, \Akeeba\Subscriptions\Admin\Helper\Select::getCountries()) }}}
            </a>
        </td>
        {{-- Label --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=States&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->label }}}
            </a>
        </td>
        {{-- Enabled --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i)
        </td>
    </tr>
@endforeach
@stop
