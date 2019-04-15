<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */
$allowTax = $this->cparams->isTaxAllowed ? 'true' : 'false';
$js = <<< JS
window.jQuery(document).ready(function ($){
	akeebasubsLocalisePrice('{$this->item->paddle_product_id}', $allowTax, 'akeebasubs-sum-price-amount', 'akeebasubs-sum-tax-field', 'akeebasubs-sum-net-field', 'akeebasubs-sum-localised-tax-container', 'akeebasubs-detected-country')
});

JS;

?>
@if ($this->cparams->localisePrice && !($this->validation->price->discount > 0.009))
	@inlineJs($js)
@endif

<div id="akeebasubs-column-price" class="akeeba-panel--teal">
	<header class="akeeba-block-header">
		<h3>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_ORDERSUMMARY')
		</h3>
	</header>
	{{-- DISCOUNT BREAK DOWN --}}
	@if (($this->validation->price->discount > 0.009))
		<div id="akeebasubs-sum-original-container" class="akeeba-container--50-50">
			<div id="akeebasubs-original-label">
				@lang('COM_AKEEBASUBS_LEVEL_SUM_ORIGINALLY')
			</div>

			<div id="akeebasubs-sum-original-field">
				<del>
					@if ($this->cparams->currencypos == 'before')
						<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
					@endif
					<span class="akeebasubs-level-price" id="akeebasubs-sum-original">{{ $this->validation->price->net }}</span>
					@if ($this->cparams->currencypos == 'after')
						<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
					@endif
				</del>
			</div>
		</div>

		<div id="akeebasubs-sum-discount-container" class="akeeba-container--50-50">
			<div id="akeebasubs-discount-label">
				@lang('COM_AKEEBASUBS_LEVEL_SUM_DISCOUNT')
			</div>

			<div id="akeebasubs-sum-discount-field">
				@if ($this->cparams->currencypos == 'before')
					<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
				@endif
				- <span class="akeebasubs-level-price" id="akeebasubs-sum-discount">{{ $this->validation->price->discount }}</span>
				@if ($this->cparams->currencypos == 'after')
					<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
				@endif
			</div>
		</div>
	@elseif ($this->cparams->isTaxAllowed)
		<div id="akeebasubs-sum-localised-tax-container">
			<div id="akeebasubs-sum-net-container" class="akeeba-container--50-50">
				<div id="akeebasubs-net-label">
					@lang('COM_AKEEBASUBS_LEVEL_SUM_NET')
				</div>

				<div id="akeebasubs-sum-net-field">
					@if ($this->cparams->currencypos == 'before')
						<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
					@endif
					<span class="akeebasubs-level-price" id="akeebasubs-sum-net">{{ $this->validation->price->net }}</span>
					@if ($this->cparams->currencypos == 'after')
						<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
					@endif
				</div>
			</div>
			<div id="akeebasubs-sum-tax-container" class="akeeba-container--50-50">
				<div id="akeebasubs-tax-label">
					@lang('COM_AKEEBASUBS_LEVEL_SUM_TAX') *
				</div>

				<div id="akeebasubs-sum-tax-field">
					@if ($this->cparams->currencypos == 'before')
						<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
					@endif
					0.00
					@if ($this->cparams->currencypos == 'after')
						<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
					@endif
				</div>
			</div>
		</div>
	@endif

	{{-- TOTAL CHARGE --}}
	<div id="akeebasubs-sum-label">
		@lang('COM_AKEEBASUBS_LEVEL_SUM_TOTAL')
		@if (($this->validation->price->discount < 0.01) && $this->cparams->isTaxAllowed)
			*
		@endif
	</div>

	<div id="akeebasubs-sum-price">
		<span class="akeeba-label--green" id="akeebasubs-sum-price-amount">
			@if ($this->cparams->currencypos == 'before')
				<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
			@endif
			<span class="akeebasubs-level-price" id="akeebasubs-sum-total">{{{ $this->validation->price->gross }}}</span>
			@if ($this->cparams->currencypos == 'after')
				<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
			@endif
		</span>
	</div>

	{{-- NO TAX INCLUDED WARNING --}}
	@if (!$this->cparams->isTaxAllowed || ($this->validation->price->discount >= 0.009))
		<p id="akeebasubs-panel-yourorder-info" class="akeeba-help-text">
			<span class="akion-information-circled"></span>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_PRICE_AND_TAX')
		</p>
	@endif

	{{-- COUPON CODE--}}
	@if (($this->validation->price->net > 0))
		<h4>
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_COUPON')
		</h4>
		<div id="akeebasubs-coupon-code-outer-container">
			<div class="akeeba-input-group" id="akeebasubs-coupon-code-container">
				<input type="text" name="coupon" id="coupon"
					   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_COUPON')"
					   value="{{{$this->cache['coupon']}}}" />
				<span class="akeeba-input-group-btn">
					<button class="akeeba-btn--dark" type="button" onclick="validateForm()">
						@lang('COM_AKEEBASUBS_LEVEL_FIELD_COUPON_VALIDATE')
					</button>
				</span>
			</div>
		</div>
	@endif
</div>
