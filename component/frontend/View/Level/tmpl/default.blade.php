<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

?>
{{-- Include Paddle JavaScript --}}
@include('site:com_akeebasubs/Level/paddlejs')

@if ($this->blockingSubscriptions->count())
	@include('site:com_akeebasubs/Level/default_blocking')
	<?php return; ?>
@endif

<div id="akeebasubs">

	{{-- Module position 'akeebasubscriptionsheader' --}}
	@modules('akeebasubscriptionsheader')

	<div class="clearfix"></div>

	{{-- Warning when Javascript is disabled --}}
	<noscript>
		<div class="akeeba-block--warning">
			<h4>
				<span class="glyphicon glyphicon-alert"></span>
				@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_HEADER')
			</h4>
			<p>@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_BODY')</p>
			<p>
				<a href="https://www.enable-javascript.com" class="akeeba-btn--primary" target="_blank">
					<span class="akion-information-circled"></span>
					@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_MOREINFO')
				</a>
			</p>
		</div>
	</noscript>

	<form
		action="@route('index.php?option=com_akeebasubs&view=Subscribe&slug=' . $this->input->getString('slug', ''))"
		method="post"
		id="signupForm" class="akeeba-form--horizontal">
		<input type="hidden" name="@token()" value="1"/>

		{{-- PRODUCT SUMMARY --}}
		@include('site:com_akeebasubs/Level/default_product')

		{{-- UPSELL TO RELATED LEVELS --}}
		@if (!empty($this->upsellLevels))
			@include('site:com_akeebasubs/Level/default_related')
		@endif

		{{-- DOWNGRADE WARNING --}}
		@if ($this->warnSubscriptions->count())
			@include('site:com_akeebasubs/Level/default_downgrade')
		@endif

		{{-- MAIN FIELDS --}}
		@unless($this->validation->price->net < 0.01)
		<div id="akeebasubs-page-level" class="akeeba-container--66-33">
			<div id="akeebasubs-page-level-orderfields">
					{{-- USER ACCOUNT--}}
					@include('site:com_akeebasubs/Level/default_account')
				</div>
				<div id="akeebasubs-page-level-pricing">
					{{-- PRICING INFORMATION--}}
					@include('site:com_akeebasubs/Level/default_pricing')
				</div>
			</div>
		@else
			<div id="akeebasubs-page-level-orderfields">
				{{-- USER ACCOUNT--}}
				@include('site:com_akeebasubs/Level/default_account')
			</div>
		@endunless



		{{-- UPSELL TO RECURRING --}}
		@include('site:com_akeebasubs/Level/default_recurring')

		{{-- TOS ACCEPTANCE--}}
		@include('site:com_akeebasubs/Level/default_tos')

		{{-- SUBSCRIBE BUTTON --}}
		@include('site:com_akeebasubs/Level/default_subscribe')
	</form>

	<div class="clearfix"></div>

	{{-- Module position 'akeebasubscriptionsfooter' --}}
	@modules('akeebasubscriptionsfooter')

	<div class="clearfix"></div>
</div>

@if (($this->cparams->localisePrice && !($this->validation->price->discount > 0.009)) || !empty($this->upsellPlanId))
	<hr />
@endif

@if ($this->cparams->localisePrice && !($this->validation->price->discount > 0.009))
	<p class="akeeba-help-text">
		@sprintf('COM_AKEEBASUBS_LEVEL_LBL_PRICEINFO_LOCALISED_SUBSCRIBEPAGE', $this->container->params->get('currency', '€'), $this->container->params->get('currencysymbol', '€'))
	</p>

	@if ($this->cparams->isTaxAllowed)
		<p class="akeeba-help-text">
			* @lang('COM_AKEEBASUBS_LEVEL_LBL_PRICEINFO_ESTIMATETAX')
		</p>
	@endif
@endif

@unless(empty($this->validation->recurring['recurringId']))
	<p class="akeeba-help-text" id="akeebasubs-optin-recurring-info" style="display: none">
		@lang('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_INFO')
	</p>
@endunless