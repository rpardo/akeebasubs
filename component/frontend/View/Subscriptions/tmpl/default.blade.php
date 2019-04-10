<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Subscriptions\Html $this */
?>
@include('site:com_akeebasubs/Level/paddlejs')

<div id="akeebasubs" class="subscriptions">
	<h2 class="pageTitle">
		@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_TITLE')
	</h2>

	@include('site:com_akeebasubs/Subscriptions/tz_warning')

	@if(empty($this->subIDs))
		<p>
			@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_NO_SUBSCRIPTIONS')
		</p>
		<p>
			<a href="@route('index.php?option=com_akeebasubs&view=Levels')"
			   class="akeeba-btn--big">
				<span class="akion-ios-cart"></span>
				@lang('COM_AKEEBASUBS_LEVELS_SUBSCRIBE')
			</a>
		</p>
	@endif

	{{-- UNPAID SUBSCRIPTIONS (NEW) --}}
	@if(count($this->sortTable['new']))
		<div class="akeeba-panel--red">
			<header class="akeeba-block-header">
				<h3 class="hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_NEW_HELP')">
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_NEW')
					<span class="akion-information-circled pull-right"></span>
				</h3>
			</header>

			@foreach($this->sortTable['new'] as $id)
				@include('site:com_akeebasubs/Subscriptions/default_item', [ 'subId' => $id ])
			@endforeach

		</div>
	@endif

	{{-- PENDING PAYMENTS --}}
	@if(count($this->sortTable['pending']))
		<div class="akeeba-panel--orange">
			<header class="akeeba-block-header">
				<h3 class="hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_PENDING_HELP')">
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_PENDING')
					<span class="akion-information-circled pull-right"></span>
				</h3>
			</header>

			@foreach($this->sortTable['pending'] as $id)
				@include('site:com_akeebasubs/Subscriptions/default_item', [ 'subId' => $id ])
			@endforeach

		</div>
	@endif

	{{-- ACTIVE --}}
	@if(count($this->sortTable['active']))
		<div class="akeeba-panel--green">
			<header class="akeeba-block-header">
				<h3 class="hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_ACTIVE_HELP')">
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_ACTIVE')
					<span class="akion-information-circled pull-right"></span>
				</h3>
			</header>

			@foreach($this->sortTable['active'] as $id)
				@include('site:com_akeebasubs/Subscriptions/default_item', [ 'subId' => $id ])
			@endforeach

		</div>
	@endif

	{{-- RENEWALS --}}
	@if(count($this->sortTable['waiting']))
		<div class="akeeba-panel--teal">
			<header class="akeeba-block-header">
				<h3 class="hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_WAITING_HELP')">
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_WAITING')
					<span class="akion-information-circled pull-right"></span>
				</h3>
			</header>

			@foreach($this->sortTable['waiting'] as $id)
				@include('site:com_akeebasubs/Subscriptions/default_item', [ 'subId' => $id ])
			@endforeach

		</div>
	@endif

	{{-- EXPIRED --}}
	@if(count($this->sortTable['expired']))
		<div class="akeeba-panel--info">
			<header class="akeeba-block-header">
				<h3 class="hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_EXPIRED_HELP')">
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_EXPIRED')
					<span class="akion-information-circled pull-right"></span>
				</h3>
			</header>

			@foreach($this->sortTable['expired'] as $id)
				@include('site:com_akeebasubs/Subscriptions/default_item', [ 'subId' => $id ])
			@endforeach

		</div>
	@endif

	{{-- CANCELED --}}
	@if(count($this->sortTable['canceled']))
		<div class="akeeba-panel--grey">
			<header class="akeeba-block-header">
				<h3 class="hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_CANCELLED_HELP')">
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_CANCELLED')
					<span class="akion-information-circled pull-right"></span>
				</h3>
			</header>

			@foreach($this->sortTable['canceled'] as $id)
				@include('site:com_akeebasubs/Subscriptions/default_item', [ 'subId' => $id ])
			@endforeach
		</div>
	@endif

</div>


