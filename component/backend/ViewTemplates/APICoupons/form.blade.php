<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

$typeOptions = [
	'value'       => JText::_('COM_AKEEBASUBS_COUPON_TYPE_VALUE'),
	'percent'     => JText::_('COM_AKEEBASUBS_COUPON_TYPE_PERCENT'),
	'lastpercent' => JText::_('COM_AKEEBASUBS_COUPON_TYPE_LASTPERCENT'),
];

/** @var \Akeeba\Subscriptions\Admin\Model\APICoupons $item */
$item = $this->getItem();

$js = <<< JS

jQuery(document).ready(function()
{
	function akeebasubsOnUsageLimitsChange()
	{
		var value = jQuery('#usage_limits').val();

		if (value == 1)
		{
			jQuery("#creation_limit").show();
			jQuery("#subscription_limit").hide().val("0");
			jQuery("#value_limit").hide().val("0");
		}
		else if (value == 2)
		{
			jQuery("#creation_limit").hide().val("0");
			jQuery("#subscription_limit").show();
			jQuery("#value_limit").hide().val("0");
		}
		else
		{
			jQuery("#creation_limit").hide().val("0");
			jQuery("#subscription_limit").hide().val("0");
			jQuery("#value_limit").show();
		}
	}

	jQuery("#usage_limits").change(akeebasubsOnUsageLimitsChange);

	akeebasubsOnUsageLimitsChange();
})

JS;

$createURL = JUri::root() . 'index.php?option=com_akeebasubs&view=APICoupons&task=create&key=' .
	urlencode($item->key) . '&pwd=' . urlencode($item->password) .
	'&format=json';

$limitsURL = JUri::root() . 'index.php?option=com_akeebasubs&view=APICoupons&task=getlimits&key=' .
	urlencode($item->key) . '&pwd=' . urlencode($item->password) .
	'&format=json';

?>
@jhtml('behavior.tooltip')
@jhtml('formbehavior.chosen', 'select')
@inlineJs($js)

@extends('admin:com_akeebasubs/Common/edit')

@section('edit-page-top')
    @if ($this->item->akeebasubs_apicoupon_id > 0)
        <div class="akeeba-block--info">
            <div>@sprintf('COM_AKEEBASUBS_APICOUPONS_INFO_URL', $createURL)</div>
            <div>@sprintf('COM_AKEEBASUBS_APICOUPONS_LIMITS_URL', $limitsURL)</div>
        </div>
    @endif
@stop

@section('edit-form-body')
    <div class="akeeba-container--50-50">

        <div class="akeeba-panel--teal" id="akeebasubs-apicoupons-items">
            <header class="akeeba-block-header">
                <h3>@lang('COM_AKEEBASUBS_COUPON_BASIC_TITLE')</h3>
            </header>

            <div class="akeeba-form-group">
                <label for="title">
                    @lang('COM_AKEEBASUBS_COUPON_BASIC_TITLE')
                </label>
                <input type="text" name="title" id=title" value="{{{ $item->title }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="key">
                    @fieldtitle('key')
                </label>
                <input type="text" name="key" id=key" value="{{{ $item->key }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="password">
                    @fieldtitle('pwd')
                </label>
                <input type="text" name="password" id=password" value="{{{ $item->password }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="type">
                    @lang('COM_AKEEBASUBS_COUPON_FIELD_TYPE')
                </label>
                @jhtml('FEFHelper.select.genericlist', $typeOptions, 'type', ['list.select' => $item->type])
            </div>

            <div class="akeeba-form-group">
                <label for="value">
                    @lang('COM_AKEEBASUBS_COUPON_FIELD_VALUE')
                </label>
                <input type="number" step="0.01" name="value" id=value" value="{{{ $item->value }}}"/>
            </div>

            <div class="akeeba-form-group">
                <label for="enabled">
                    @lang('JPUBLISHED')
                </label>
                @jhtml('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)
            </div>

        </div>

        <div class="akeeba-panel--orange akeebasubs-panel-force-top-margin" id="akeebasubs-apicoupons-limits">
            <header class="akeeba-block-header">
                <h3>@lang('COM_AKEEBASUBS_COUPONS_LIMITS')</h3>
            </header>

            <div class="akeeba-form-group">
                <label for="subscriptions">
                    @lang('COM_AKEEBASUBS_COUPON_FIELD_SUBSCRIPTIONS')
                </label>
				<?php echo BrowseView::modelSelect('subscriptions', 'Levels', $item->subscriptions, [
					'fof.autosubmit' => false, 'translate' => false, 'list.attr' => ['multiple' => 'multiple']
				]) ?>
            </div>

            <div class="akeeba-form-group">
                <label for="usage_limits">
                    @fieldtitle('usage_limits')
                </label>
				<?php $selected = $item->creation_limit ? 1 : ($item->subscription_limit ? 2 : 3); ?>
                {{ \Akeeba\Subscriptions\Admin\Helper\Select::apicouponLimits('usage_limits', $selected) }}
                <input type="text" style="width: 50px; display:none" id="creation_limit" name="creation_limit"
                       value="{{{ $item->creation_limit }}}"/>
                <input type="text" style="width: 50px; display:none" id="subscription_limit"
                       name="subscription_limit"
                       value="{{{ $item->subscription_limit }}}"/>
                <input type="text" style="width: 50px; display:none" id="value_limit" name="value_limit"
                       value="{{{ $item->value_limit }}}"/>
            </div>

        </div>

    </div>
@stop
