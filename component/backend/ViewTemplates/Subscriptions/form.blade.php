<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\FEFHelper\BrowseView;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */

$stateOptions = [
	'N' => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_N'),
	'P' => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_P'),
	'C' => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_C'),
	'X' => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_X'),
];

$cancellationReasonOptions = [
		'refund'   => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CANCELLATION_REASON_REFUND'),
		'risk'     => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CANCELLATION_REASON_RISK'),
		'past_due' => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CANCELLATION_REASON_PAST_DUE'),
		'user'     => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CANCELLATION_REASON_USER'),
		'upgrade'  => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CANCELLATION_REASON_UPGRADE'),
		'tos'      => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CANCELLATION_REASON_TOS'),
		'other'    => Text::_('COM_AKEEBASUBS_SUBSCRIPTION_CANCELLATION_REASON_OTHER')
];

$discountOptions = [
	'none'    => Text::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_NONE'),
	'coupon'  => Text::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_COUPON'),
	'upgrade' => Text::_('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_UPGRADE'),
];


/** @var \Akeeba\Subscriptions\Admin\Model\Subscriptions $item */
$item = $this->getItem();
$user = JFactory::getUser($item->user_id ?? 0);
?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
<div class="akeeba-container--50-50">

	<div class="akeeba-panel--teal" id="akeebasubs-subscription-information">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_SUBSCRIPTION_LBL_SUB')</h3>
		</header>

		<div class="akeeba-form-group">
			<label for="akeebasubs_level_id">
				@lang('COM_AKEEBASUBS_SUBSCRIPTION_LEVEL')
			</label>
			<?php echo BrowseView::modelSelect('akeebasubs_level_id', 'Levels', $item->akeebasubs_level_id, ['fof.autosubmit' => false, 'translate' => false]) ?>
		</div>

		<div class="akeeba-form-group">
			<label for="user_id">
				@lang('COM_AKEEBASUBS_SUBSCRIPTION_USER')
			</label>
			@include('admin:com_akeebasubs/Common/EntryUser', ['field' => 'user_id', 'item' => $item, 'required' => true])
		</div>

		<div class="akeeba-form-group">
			<label for="enabled">
				@lang('JPUBLISHED')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)
		</div>

		<div class="akeeba-form-group">
			<label for="_noemail">
				@lang('COM_AKEEBASUBS_SUBSCRIPTION_NOEMAIL')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', '_noemail', $item->_noemail)
		</div>

		<div class="akeeba-form-group">
			<label for="publish_up">
				@lang('COM_AKEEBASUBS_SUBSCRIPTION_PUBLISH_UP')
			</label>
			@jhtml('calendar', $item->publish_up, 'publish_up', 'publish_up', '%Y-%m-%d %H:%M:%S', ['showtime' => 1])
		</div>

		<div class="akeeba-form-group">
			<label for="publish_down">
				@lang('COM_AKEEBASUBS_SUBSCRIPTION_PUBLISH_DOWN')
			</label>
			@jhtml('calendar', $item->publish_down, 'publish_down', 'publish_down', '%Y-%m-%d %H:%M:%S', ['showtime' => 1])
		</div>

		<div class="akeeba-form-group">
			<label for="notes">
				@lang('COM_AKEEBASUBS_SUBSCRIPTION_NOTES')
			</label>
			<textarea name="notes" id="notes" cols="40" rows="5">{{ $item->notes }}</textarea>
		</div>

	</div>

	<div class="akeeba-panel--green akeebasubs-panel-force-top-margin" id="akeebasubs-upgrades-discount">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_SUBSCRIPTION_LBL_PAYMENT')</h3>
		</header>

		<div class="akeeba-form-group">
			<label for="processor">
				@fieldtitle('processor')
			</label>
			<input type="text" name="processor" id="processor" value="{{{ $item->processor }}}" />
		</div>

		<div class="akeeba-form-group">
			<label for="processor_key">
				@fieldtitle('processor_key')
			</label>
			<input type="text" name="processor_key" id="processor_key" value="{{{ $item->processor_key }}}" />
		</div>

		<div class="akeeba-form-group">
			<label for="payment_state">
				@fieldtitle('state')
			</label>
			@jhtml('FEFHelper.select.genericlist', $stateOptions, 'payment_state', ['list.select' => $item->getFieldValue('state')])
		</div>

		<div class="akeeba-form-group">
			<label for="cancellation_reason">
				@fieldtitle('cancellation_reason')
			</label>
			@jhtml('FEFHelper.select.genericlist', $cancellationReasonOptions, 'cancellation_reason', ['list.select' => $item->getFieldValue('cancellation_reason')])
		</div>

		<div class="akeeba-form-group">
			<label for="prediscount_amount">
				@fieldtitle('prediscount_amount')
			</label>
			@include('admin:com_akeebasubs/Common/EntryPrice', ['item' => $item, 'field' => 'prediscount_amount'])
		</div>

		<div class="akeeba-form-group">
			<label for="discount_amount">
				@fieldtitle('discount_amount')
			</label>
			@include('admin:com_akeebasubs/Common/EntryPrice', ['item' => $item, 'field' => 'discount_amount'])
		</div>

		<div class="akeeba-form-group">
			<label for="net_amount">
				@fieldtitle('net_amount')
			</label>
			@include('admin:com_akeebasubs/Common/EntryPrice', ['item' => $item, 'field' => 'net_amount'])
		</div>

		<div class="akeeba-form-group">
			<label for="tax_percent">
				@fieldtitle('tax_percent')
			</label>
			<div class="akeeba-input-group">
				<span>%</span>
				<input type="number" min="0" max="200" step="0.01" name="tax_percent" id="tax_percent" value="{{{ (float) $item->tax_percent }}}" />
			</div>
		</div>

		<div class="akeeba-form-group">
			<label for="tax_amount">
				@fieldtitle('tax_amount')
			</label>
			@include('admin:com_akeebasubs/Common/EntryPrice', ['item' => $item, 'field' => 'tax_amount'])
		</div>

		<div class="akeeba-form-group">
			<label for="gross_amount">
				@fieldtitle('gross_amount')
			</label>
			@include('admin:com_akeebasubs/Common/EntryPrice', ['item' => $item, 'field' => 'gross_amount'])
		</div>

		<div class="akeeba-form-group">
			<label for="fee_amount">
				@fieldtitle('fee_amount')
			</label>
			@include('admin:com_akeebasubs/Common/EntryPrice', ['item' => $item, 'field' => 'fee_amount'])
		</div>

		<div class="akeeba-form-group">
			<label for="created_on">
				@fieldtitle('created_on')
			</label>
			@jhtml('calendar', $item->created_on, 'created_on', 'created_on')
		</div>

	</div>

</div>

<div class="akeeba-container--50-50">

	<div class="akeeba-panel--red akeebasubs-panel-force-top-margin" id="akeebasubs-paddle">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_SUBSCRIPTION_LBL_PADDLE')</h3>
		</header>

		@if (isset($item->params['checkout_id']))
			<p>
				@sprintf('COM_AKEEBASUBS_SUBSCRIPTION_LBL_PADDLE_CHECKOUT_ID', $item->params['checkout_id'])
			</p>
		@endif

		@if (isset($item->params['subscription_id']))
			<p>
				@sprintf('COM_AKEEBASUBS_SUBSCRIPTION_LBL_PADDLE_SUBSCRIPTION_ID', $item->params['subscription_id'])
			</p>
		@endif

		@if ($item->update_url)
			<p>
				<a href="{{ $item->update_url }}">@lang('COM_AKEEBASUBS_SUBSCRIPTION_LBL_PADDLE_UPDATE_URL')</a>
			</p>
		@endif

		@if ($item->cancel_url)
			<p>
				<a href="{{ $item->cancel_url }}">@lang('COM_AKEEBASUBS_SUBSCRIPTION_LBL_PADDLE_CANCEL_URL')</a>
			</p>
		@endif

		<?php $country = is_null($item->juser) ? null : $item->juser->getProfileField('akeebasubs.country') ?>
		@if (!empty($country))
			<p>
				<span class="akeeba-label--teal">
					{{ $country }}
				</span>
				{{ \Akeeba\Subscriptions\Admin\Helper\Select::countryToEmoji($country) }}
				{{ \Akeeba\Subscriptions\Admin\Helper\Select::formatCountry($country) }}
			</p>
		@endif
	</div>

</div>

<div class="akeeba-container--50-50">

	<div class="akeeba-panel--info" id="akeebasubs-ipinfo">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_SUBSCRIPTION_LBL_IP')</h3>
		</header>

		<div class="akeeba-form-group">
			<label for="ip">
				@fieldtitle('ip')
			</label>
			<input type="text" name="ip" id="ip" value="{{{ $item->ip }}}" />
		</div>

	</div>

	<div class="akeeba-panel--info akeebasubs-panel-force-top-margin" id="akeebasubs-uainfo">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_SUBSCRIPTION_LBL_UAINFO')</h3>
		</header>

		<div class="akeeba-form-group">
			<label for="ua">
				@fieldtitle('ua')
			</label>
			<input type="text" name="ua" id="ua" value="{{{ $item->ua }}}" />
		</div>

		<div class="akeeba-form-group">
			<label for="mobile">
				@fieldtitle('mobile')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', 'mobile', $item->mobile)
		</div>

	</div>

</div>
@stop
