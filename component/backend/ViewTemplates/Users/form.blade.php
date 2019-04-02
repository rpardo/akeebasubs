<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * @var  FOF30\View\DataView\Html               $this
 * @var \Akeeba\Subscriptions\Admin\Model\Users $item
 * @var \Akeeba\Subscriptions\Admin\Model\Users $model
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$item  = $this->getItem();
$model = $this->getModel();
?>

@jhtml('behavior.tooltip')
@jhtml('behavior.modal')

@extends('admin:com_akeebasubs/Common/edit')

@section('edit-page-top')
    @if ($item->user_id)
        <div class="akeeba-panel--info">
            <a class="akeeba-btn--dark" href="@route('index.php?option=com_users&task=user.edit&id=' . $item->user_id)">
                <span class="akion-edit"></span>
                @lang('COM_AKEEBASUBS_USER_EDITTHISINJUSERMANAGER')
            </a>
        </div>
    @endif
@stop

@section('edit-form-body')
    <div class="akeeba-container--50-50">

        <div class="akeeba-panel--teal" id="akeebasubs-user-basic">
            <header class="akeeba-block-header">
                <h3>@lang('COM_AKEEBASUBS_USER_BASIC_TITLE')</h3>
            </header>

            <div class="akeeba-form-group">
                <label for="user_id">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTION_USER')
                </label>
                @include('admin:com_akeebasubs/Common/EntryUser', ['field' => 'user_id', 'item' => $item])
            </div>

            <div class="akeeba-form-group">
                <label for="country">
                    @fieldtitle('country')
                </label>
                {{ \FOF30\Utils\FEFHelper\BrowseView::genericSelect('country', \Akeeba\Subscriptions\Admin\Helper\Select::getCountries(), $item->country, ['fof.autosubmit' => false, 'none' => null, 'translate' => false]) }}
            </div>

        </div>

        <div class="akeeba-panel--info akeebasubs-panel-force-top-margin" id="akeebasubs-user-business">
            <header class="akeeba-block-header">
                <h3>@lang('COM_AKEEBASUBS_USER_BASIC_BUSINESS')</h3>
            </header>

            <div class="akeeba-form-group">
                <label for="notes">
                    @lang('COM_AKEEBASUBS_USER_NOTES_TITLE')
                </label>
                <textarea type="text" name="notes" id="notes" rows="5" cols="50" >{{{ $item->notes }}}</textarea>
            </div>
        </div>

    </div>
@stop
