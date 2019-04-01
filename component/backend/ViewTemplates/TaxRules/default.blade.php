<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\TaxRules;
use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

/**
 * @var  FOF30\View\DataView\Html $this
 * @var  TaxRules                 $row
 * @var  TaxRules                 $model
 */

$model = $this->getModel();
?>

@extends('admin:com_akeebasubs/Common/browse')

@section('browse-page-top')
    <a href="index.php?option=com_akeebasubs&view=TaxConfigs" class="akeeba-btn--primary">
        <span class="akion-wand"></span>
		@lang('COM_AKEEBASUBS_TITLE_TAXCONFIGS')
    </a>
@stop

@section('browse-filters')
    <div class="akeeba-filter-element akeeba-form-group">
        {{ BrowseView::modelFilter('akeebasubs_level_id', 'title', 'Levels', 'COM_AKEEBASUBS_TAXRULES_LEVEL')  }}
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @selectfilter('country', \Akeeba\Subscriptions\Admin\Helper\Select::getCountriesForHeader(), 'COM_AKEEBASUBS_TAXRULES_COUNTRY')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('city', null, 'COM_AKEEBASUBS_TAXRULES_CITY')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @selectfilter('vies', SelectOptions::getOptions('boolean'), 'COM_AKEEBASUBS_TAXRULES_VIES')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        {{ BrowseView::publishedFilter('enabled', 'JENABLED') }}
    </div>
@stop

@section('browse-table-header')
{{-- ### HEADER ROW ### --}}
<tr>
    {{-- Row select --}}
    <th>
        @jhtml('FEFHelper.browse.checkall')
    </th>
    {{-- ID --}}
    <th>
        @sortgrid('akeebasubs_taxrule_id', 'JGLOBAL_NUM')
    </th>
    {{-- Level --}}
    <th>
        @sortgrid('akeebasubs_level_id', 'COM_AKEEBASUBS_TAXRULES_LEVEL')
    </th>
    {{-- Country --}}
    <th>
        @sortgrid('country', 'COM_AKEEBASUBS_TAXRULES_COUNTRY')
    </th>
    {{-- City --}}
    <th>
        @sortgrid('city', 'COM_AKEEBASUBS_TAXRULES_CITY')
    </th>
    {{-- Vies --}}
    <th>
        @sortgrid('vies', 'COM_AKEEBASUBS_TAXRULES_VIES')
    </th>
    {{-- Taxrate --}}
    <th>
        @sortgrid('taxrate', 'COM_AKEEBASUBS_TAXRULES_TAXRATE')
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
        {{-- Level --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=TaxRules&task=edit&id=[ITEM:ID]', $row))">
                {{{  BrowseView::modelOptionName($row->akeebasubs_level_id, 'Levels') }}}
            </a>
        </td>
        {{-- Country --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=TaxRules&task=edit&id=[ITEM:ID]', $row))">
                {{{ BrowseView::getOptionName($row->country, \Akeeba\Subscriptions\Admin\Helper\Select::getCountries()) }}}
            </a>
        </td>
        {{-- City --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=TaxRules&task=edit&id=[ITEM:ID]', $row))">
                {{{ $row->city }}}
            </a>
        </td>
        {{-- Vies --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=TaxRules&task=edit&id=[ITEM:ID]', $row))">
                @jhtml('FEFHelper.browse.published', $row->vies, $i, '', false)
            </a>
        </td>
        {{-- Taxrate --}}
        <td>
            <a href="@route(BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=TaxRules&task=edit&id=[ITEM:ID]', $row))">
                {{{ sprintf("%0.2f %%", $row->taxrate) }}}
            </a>
        </td>
        {{-- Enabled --}}
        <td>
            @jhtml('FEFHelper.browse.published', $row->enabled, $i)
        </td>
    </tr>
@endforeach
@stop
