<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

use Akeeba\Subscriptions\Admin\Helper\Image;
use Akeeba\Subscriptions\Admin\Helper\Message;

use Akeeba\Subscriptions\Admin\Helper\Select;

$requireCoupon       = $this->cparams->reqcoupon;

$paymentMethodsCount = count(Select::paymentmethods('paymentmethod', '', ['id'              => 'paymentmethod',
																		  'level_id'        => $this->item->akeebasubs_level_id,
																		  'return_raw_list' => 1]));
$hidePaymentMethod   =
	($paymentMethodsCount <= 1) || ($this->validation->price->gross < 0.01);

?>
{{-- SUBSCRIPTION LEVEL NAME --}}
<p>
	<span class="akeeba-label--grey">{{{$this->item->title}}}</span>
</p>
{{-- SUBSCRIPTION LEVEL DESCRIPTION --}}
<div>
	@jhtml('content.prepare', Message::processLanguage($this->item->description))
</div>

<hr />

{{-- PRICE INFORMATION SUMMARY AREA --}}
@unless($this->validation->price->net < 0.01)
<div id="akeebasubs-sum-original-container" class="akeeba-container--50-50" style="display: none">
	<div id="akeebasubs-original-label">
		@lang('COM_AKEEBASUBS_LEVEL_SUM_ORIGINALLY')
	</div>

	<div id="akeebasubs-sum-original-field">
		<del>
		@if ($this->cparams->currencypos == 'before')
			<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
		@endif
		<span class="akeebasubs-level-price" id="akeebasubs-sum-original">0.00</span>
		@if ($this->cparams->currencypos == 'after')
			<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
		@endif
		</del>
	</div>
</div>

<div id="akeebasubs-sum-discount-container" class="akeeba-container--50-50" style="display: none">
	<div id="akeebasubs-discount-label">
		@lang('COM_AKEEBASUBS_LEVEL_SUM_DISCOUNT')
	</div>

	<div id="akeebasubs-sum-discount-field">
		@if ($this->cparams->currencypos == 'before')
			<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
		@endif
		– <span class="akeebasubs-level-price" id="akeebasubs-sum-discount">0</span>
		@if ($this->cparams->currencypos == 'after')
			<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
		@endif
	</div>
</div>

<div id="akeebasubs-sum-container" class="akeeba-container--50-50">
	<div id="akeebasubs-sum-label">
		@lang('COM_AKEEBASUBS_LEVEL_SUM_TOTAL')
	</div>

	<div id="akeebasubs-sum-price">
	<span class="akeeba-label--green">
		@if ($this->cparams->currencypos == 'before')
			<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
		@endif
		<span class="akeebasubs-level-price" id="akeebasubs-sum-total">{{{ $this->validation->price->gross }}}</span>
		@if ($this->cparams->currencypos == 'after')
			<span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
		@endif
	</span>
	</div>
</div>

<div id="akeebasubs-vat-container" class="akeeba-container--50-50"
	 style="display:{{ ($this->validation->price->taxrate > 0) ? 'block' : 'none' }}">
	<div>{{-- Intentionally left blank to make the notice appear on the right --}}&nbsp;</div>
	<div id="akeebasubs-sum-vat-container">
		@lang('COM_AKEEBASUBS_LEVEL_SUM_VAT') <span id="akeebasubs-sum-vat-percent">{{{$this->validation->price->taxrate}}}</span>%
	</div>
</div>

<noscript>
	<div class="akeeba-block--failure">
		<h4>
			<span class="glyphicon glyphicon-alert"></span>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_HEADER')
		</h4>
		<p>
			@lang('COM_AKEEBASUBS_LEVEL_SUM_NOSCRIPT')
		</p>
	</div>
</noscript>

<hr />
@endunless

{{-- COUPON CODE--}}
@if ($requireCoupon || ($this->validation->price->net > 0))
	<h3>
		@lang('COM_AKEEBASUBS_LEVEL_FIELD_COUPON')
	</h3>

	<div class="akeeba-form-group">
		<label>&nbsp;</label>
		<div class="akeeba-input-group">
			<input type="text" name="coupon" id="coupon"
				   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_COUPON')"
				   value="{{{$this->cache['coupon']}}}"/>
			<span class="akeeba-input-group-btn">
				<button class="akeeba-btn--dark" type="button" onclick="validateBusiness()">
					@lang('COM_AKEEBASUBS_LEVEL_FIELD_COUPON_VALIDATE')
				</button>
			</span>
		</div>
	</div>

@endif

{{-- CUSTOM FIELDS --}}
<div>
	<h3>@lang('COM_AKEEBASUBS_LEVEL_SUBSCRIBE')</h3>

	@include('site:com_akeebasubs/Level/default_prepayment')
</div>

{{-- PAYMENT METHODS --}}
<div id="paymentmethod-container" style="display: {{$hidePaymentMethod ? 'none' : 'inherit'}}">
	<div class="akeeba-form-group">
		<label for="paymentmethod">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_METHOD')
		</label>

		<div id="paymentlist-container">
			<?php
            $country = $this->getFieldValue('country', ['XX']);

			/** @var \Akeeba\Subscriptions\Site\Model\PaymentMethods $paymentMethods */
			$paymentMethods = $this->getContainer()->factory->model('PaymentMethods')->tmpInstance();
			$defaultPayment = $this->validation->validation->rawDataForDebug['paymentmethod'];

			if (empty($defaultPayment))
			{
				$defaultPayment = $paymentMethods->getLastPaymentPlugin($this->container->platform->getUser()->id, $country);
			}

			echo Select::paymentmethods(
					'paymentmethod',
					$defaultPayment,
					array(
							'id'       => 'paymentmethod',
							'level_id' => $this->item->akeebasubs_level_id,
							'country'  => $country
					)
			) ?>
		</div>
	</div>
</div>

{{-- SUBSCRIBE BUTTON --}}
<div class="akeeba-form-group--pull-right">
	<button id="subscribenow" class="akeeba-btn--block akeeba-btn--teal akeebasubs-btn-big" type="submit">
		@lang('COM_AKEEBASUBS_LEVEL_BUTTON_SUBSCRIBE')
	</button>
</div>
