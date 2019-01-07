<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Site\Model\APICoupons;
use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');

/**
 * @var \FOF30\View\DataView\Form $this
 * @var  APICoupons               $row
 * @var  APICoupons               $model
 */

$model = $this->getModel();
?>
@extends('admin:com_akeebasubs/Common/browse')

@section('browse-page-top')
    <div class="akeeba-block--info">
        @lang('COM_AKEEBASUBS_APICOUPONS_INFO')
    </div>
@stop

@section('browse-filters')
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('title', 'title', 'COM_AKEEBASUBS_COUPONS_FTITLE')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        {{ BrowseView::publishedFilter('enabled', 'JENABLED') }}
    </div>
@stop

@section('browse-table-header')
    {{-- ### HEADER ROW ### --}}
    <tr>
        <th width="20px">
            @jhtml('FEFHelper.browse.checkall')
        </th>
        <th width="20px">
            @sortgrid('akeebasubs_apicoupon_id', 'JGLOBAL_NUM')
        </th>
        <th>
            @lang('COM_AKEEBASUBS_COUPONS_FTITLE')
        </th>
        <th width="15%">
            @sortgrid('key')
        </th>
        <th width="15%">
            @lang('COM_AKEEBASUBS_APICOUPONS_FIELD_PWD')
        </th>
        <th>
            @fieldtitle('limits')
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
                {{{ $row->getId() }}}
            </td>
            <td>
                <a href="@route(\FOF30\Utils\FEFHelper\BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=APICoupons&task=edit&id=[ITEM:ID]', $row))">
                    {{{ $row->title }}}
                </a>
            </td>
            <td>
                <a href="@route(\FOF30\Utils\FEFHelper\BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=APICoupons&task=edit&id=[ITEM:ID]', $row))">
                    <strong>{{{ $row->key }}}</strong>
                </a>
            </td>
            <td>
                {{{ $row->password }}}
            </td>
            <td>
                @if ($row->subscriptions)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_LEVELS')
                    </span>
                @endif
                @if ($row->creation_limit)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_HITS')
                    </span>
                @endif
                @if ($row->value_limit)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_VALUE')
                    </span>
                @endif
                <br/>
                <em>
					<?php $usage = $row->getApiLimits($row->key, $row->password) ?>
                    @lang('COM_AKEEBASUBS_COUPONS_USAGE'): {{{ $usage['current'] }}} / {{{ $usage['limit'] }}}
                </em>
            </td>
            <td>
                @jhtml('FEFHelper.browse.published', $row->enabled, $i, '', false)
            </td>
        </tr>
    @endforeach
@stop
