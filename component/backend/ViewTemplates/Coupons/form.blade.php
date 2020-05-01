<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

$typeOptions = [
	'value'       => JText::_('COM_AKEEBASUBS_COUPON_TYPE_VALUE'),
	'percent'     => JText::_('COM_AKEEBASUBS_COUPON_TYPE_PERCENT'),
	'lastpercent' => JText::_('COM_AKEEBASUBS_COUPON_TYPE_LASTPERCENT'),
];

/** @var \Akeeba\Subscriptions\Admin\Model\Coupons $item */
$item = $this->getItem();

?>
@if (version_compare(JVERSION, '3.999.999', 'le'))
    @jhtml('behavior.tooltip')
@endif

@jhtml('formbehavior.chosen', 'select')

@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
    <div class="akeeba-container--50-50">

        <div class="akeeba-panel--teal" id="akeebasubs-coupons-basic">
            <header class="akeeba-block-header">
                <h3>@lang('COM_AKEEBASUBS_COUPON_BASIC_TITLE')</h3>
            </header>

            <div class="akeeba-form-group">
                <label for="title">
                    @fieldtitle('title')
                </label>
                <input type="text" name="title" id=title" value="{{{ $item->title }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="coupon">
                    @fieldtitle('coupon')
                </label>
                <input type="text" name="coupon" id=coupon" value="{{{ $item->coupon }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="type">
                    @fieldtitle('type')
                </label>
                @jhtml('FEFHelper.select.genericlist', $typeOptions, 'type', ['list.select' => $item->type])
            </div>

            <div class="akeeba-form-group">
                <label for="value">
                    @fieldtitle('value')
                </label>
                <input type="number" step="0.01" name="value" id=value" value="{{{ $item->value }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="recurring_access">
                    @fieldtitle('recurring_access')
                </label>
                @jhtml('FEFHelper.select.booleanswitch', 'recurring_access', $item->recurring_access)
            </div>

            <div class="akeeba-form-group">
                <label for="enabled">
                    @lang('JPUBLISHED')
                </label>
                @jhtml('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)
            </div>

            <div class="akeeba-form-group">
                <label for="hits">
                    @lang('COM_AKEEBASUBS_COMMON_HITS')
                </label>
                <input type="number" step="1" name="hits" id=hits" value="{{{ $item->hits }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="notes">
                    @lang('COM_AKEEBASUBS_COMMON_NOTES')
                </label>
                <textarea name="notes" id="notes" cols="40" rows="5">{{ $item->notes }}</textarea>
            </div>

        </div>

        <div class="akeeba-panel--info akeebasubs-panel-force-top-margin" id="akeebasubs-coupons-finetuning">
            <header class="akeeba-block-header">
                <h3>@lang('COM_AKEEBASUBS_COUPON_FINETUNING_TITLE')</h3>
            </header>

            <div class="akeeba-form-group">
                <label for="publish_up">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTION_PUBLISH_UP')
                </label>
                @jhtml('calendar', $item->publish_up, 'publish_up', 'publish_up')
            </div>

            <div class="akeeba-form-group">
                <label for="publish_down">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTION_PUBLISH_DOWN')
                </label>
                @jhtml('calendar', $item->publish_down, 'publish_down', 'publish_down')
            </div>

            <div class="akeeba-form-group">
                <label for="user">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTION_USER')
                </label>
                @include('admin:com_akeebasubs/Common/EntryUser', ['field' => 'user', 'item' => $item, 'required' => true])
            </div>

            <div class="akeeba-form-group">
                <label for="email">
                    @lang('JGLOBAL_EMAIL')
                </label>
                <input type="email" name="email" id=email" value="{{{ $item->email }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="usergroups">
                    @fieldtitle('usergroups')
                </label>
                @jhtml('FEFHelper.select.genericlist', \FOF30\Utils\SelectOptions::getOptions('usergroups'), 'type', ['list.select' => $item->type, 'list.attr' => ['multiple' => 'multiple']])
            </div>

            <div class="akeeba-form-group">
                <label for="subscriptions">
                    @fieldtitle('subscriptions')
                </label>
				<?php echo BrowseView::modelSelect('subscriptions[]', 'Levels', $item->subscriptions, [
					'fof.autosubmit' => false, 'translate' => false, 'list.attr' => ['multiple' => 'multiple']
				]) ?>
            </div>

            <div class="akeeba-form-group">
                <label for="hitslimit">
                    @fieldtitle('hitslimit')
                </label>
                <input type="number" step="1" min="0" name="hitslimit" id=hitslimit" value="{{{ $item->hitslimit }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="userhits">
                    @fieldtitle('userhitslimit')
                </label>
                <input type="number" step="1" min="0" name="userhits" id=userhits" value="{{{ $item->userhits }}}"/>
            </div>

        </div>

    </div>
@stop
