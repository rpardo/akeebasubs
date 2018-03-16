<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Site\Model\Coupons;use FOF30\Utils\FEFHelper\BrowseView;
use FOF30\Utils\SelectOptions;

defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');

/**
 * @var \FOF30\View\DataView\Form $this
 * @var  Coupons                  $row
 * @var  Coupons                  $model
 */

$model = $this->getModel();
?>
@extends('admin:com_akeebasubs/Common/browse')

@section('browse-page-top')
    <div class="akeeba-panel--info">
        <a href="@route('index.php?option=com_akeebasubs&view=MakeCoupons')" class="akeeba-btn--green">
            <span class="akion-ios-cog"></span>
            @lang('COM_AKEEBASUBS_TITLE_MAKECOUPONS')
        </a>
    </div>
@stop

@section('browse-filters')
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('title')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @searchfilter('coupon')
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @jhtml('calendar', $model->publish_up, 'publish_up', 'publish_up', '%Y-%m-%d', ['placeholder' => JText::_('COM_AKEEBASUBS_COUPONS_PUBLISH_UP')])
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        @jhtml('calendar', $model->publish_down, 'publish_down', 'publish_down', '%Y-%m-%d', ['placeholder' => JText::_('COM_AKEEBASUBS_COUPONS_PUBLISH_DOWN')])
    </div>
    <div class="akeeba-filter-element akeeba-form-group">
        {{ BrowseView::publishedFilter('enabled', 'JENABLED') }}
    </div>
@stop

@section('browse-table-header')
    {{-- ### HEADER ROW ### --}}
    <tr>
        <th width="20px">
            @jhtml('FEFHelper.browse.orderfield', 'ordering')
        </th>
        <th width="20px">
            @jhtml('FEFHelper.browse.checkall')
        </th>
        <th width="20px">
            @sortgrid('akeebasubs_coupon_id', 'JGLOBAL_NUM')
        </th>
        <th>
            @sortgrid('title')
        </th>
        <th width="10%">
            @sortgrid('coupon')
        </th>
        <th width="8%">
            @sortgrid('value')
        </th>
        <th>
            @sortgrid('limits')
        </th>
        <th width="14%">
            @sortgrid('publish_up')
        </th>
        <th width="14%">
            @sortgrid('publish_down')
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
                @jhtml('FEFHelper.browse.order', 'ordering', $row->ordering)
            </td>
            <td>
                @jhtml('FEFHelper.browse.id', ++$i, $row->getId())
            </td>
            <td>
                {{{ $row->getId() }}}
            </td>
            <td>
                <a href="@route(\FOF30\Utils\FEFHelper\BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=Coupons&task=edit&id=[ITEM:ID]', $row))">
                    {{{ $row->title }}}
                </a>
            </td>
            <td>
                <a href="@route(\FOF30\Utils\FEFHelper\BrowseView::parseFieldTags('index.php?option=com_akeebasubs&view=Coupons&task=edit&id=[ITEM:ID]', $row))">
                    <strong>{{{ $row->coupon }}}</strong>
                </a>
            </td>
            <td>
                @include('admin:com_akeebasubs/Common/ShowDiscount', ['item' => $row, 'field' => 'value'])
            </td>
            <td>
                @if ($row->user)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_USERS')
                        ({{{ $this->getContainer()->platform->getUser($row->user)->username }}})
                    </span>
                @endif
                @if ($row->email)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_EMAIL')
                        ({{{ $row->email }}})
                    </span>
                @endif
                @if ($row->subscriptions)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_LEVELS')
                    </span>
                @endif
                @if ($row->hitslimit)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_HITS')
                    </span>
                @endif
                @if ($row->userhits)
                    <span class="akeeba-label">
                        @lang('COM_AKEEBASUBS_COUPONS_LIMITS_USERHITS')
                    </span>
                @endif
            </td>
            <td>
                {{ Akeeba\Subscriptions\Admin\Helper\Format::date($row->publish_up) }}
            </td>
            <td>
                {{ Akeeba\Subscriptions\Admin\Helper\Format::date($row->publish_down) }}
            </td>
            <td>
                @jhtml('FEFHelper.browse.published', $row->enabled, $i, '', false)
            </td>
        </tr>
    @endforeach
@stop
