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

$vatOptions = [
	'0' => JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_USER_VIESREGISTERED_NO'),
	'1' => JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_USER_VIESREGISTERED_YES'),
	'2' => JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_USER_VIESREGISTERED_FORCEYES'),
]
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
                <label for="address1">
                    @fieldtitle('address1')
                </label>
                <input type="text" name="address1" id="address1" value="{{{ $item->address1 }}}" />
            </div>

            <div class="akeeba-form-group">
                <label for="address2">
                    @fieldtitle('address2')
                </label>
                <input type="text" name="address2" id="address2" value="{{{ $item->address2 }}}" />
            </div>

            <div class="akeeba-form-group">
                <label for="city">
                    @fieldtitle('city')
                </label>
                <input type="text" name="city" id="city" value="{{{ $item->city }}}" />
            </div>

            <div class="akeeba-form-group">
                <label for="zip">
                    @fieldtitle('zip')
                </label>
                <input type="text" name="zip" id="zip" value="{{{ $item->zip }}}" />
            </div>

            <div class="akeeba-form-group">
                <label for="state">
                    @fieldtitle('state')
                </label>
                {{ \FOF30\Utils\FEFHelper\BrowseView::genericSelect('state', \Akeeba\Subscriptions\Admin\Helper\Select::getStates(), $item->state, ['fof.autosubmit' => false, 'none' => null, 'translate' => false]) }}
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
                <label for="isbusiness">
                    @fieldtitle('isbusiness')
                </label>
                @jhtml('FEFHelper.select.booleanswitch', 'isbusiness', $item->isbusiness, ['id' => 'is_business'])
            </div>

            <div class="akeeba-form-group">
                <label for="businessname">
                    @fieldtitle('businessname')
                </label>
                <input type="text" name="businessname" id="businessname" value="{{{ $item->businessname }}}" />
            </div>

            <div class="akeeba-form-group">
                <label for="occupation">
                    @fieldtitle('occupation')
                </label>
                <input type="text" name="occupation" id="occupation" value="{{{ $item->occupation }}}" />
            </div>

            <div class="akeeba-form-group">
                <label for="vatnumber">
                    @fieldtitle('vatnumber')
                </label>
                <input type="text" name="vatnumber" id="vatnumber" value="{{{ $item->vatnumber }}}" />
            </div>

            <div class="akeeba-form-group">
                <label for="viesregistered">
                    @fieldtitle('viesregistered')
                </label>
                {{ \FOF30\Utils\FEFHelper\BrowseView::genericSelect('viesregistered', $vatOptions, $item->viesregistered, ['fof.autosubmit' => false, 'none' => null, 'translate' => false]) }}
            </div>

            <div class="akeeba-form-group">
                <label for="notes">
                    @lang('COM_AKEEBASUBS_USER_NOTES_TITLE')
                </label>
                <textarea type="text" name="notes" id="notes" rows="5" cols="50" >{{{ $item->notes }}}</textarea>
            </div>
        </div>

    </div>
@stop
