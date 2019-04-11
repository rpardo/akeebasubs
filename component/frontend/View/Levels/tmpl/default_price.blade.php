<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/**
 * Renders the price cell of the subscription level
 *
 * @var \Akeeba\Subscriptions\Site\View\Levels\Html $this
 * @var \Akeeba\Subscriptions\Site\Model\Levels     $level
 */

$priceInfo        = $this->getLevelPriceInformation($level);
$currencyPosition = $this->container->params->get('currencypos', 'before');
$symbol           = $this->container->params->get('currencysymbol', '€');
// No tax displayed for free products
$isTaxAllowed     = $this->isTaxAllowed && !($priceInfo->levelPrice < 0.01);
$allowTax         = $isTaxAllowed ? 'true' : 'false';
$grossTargetId    = 'akeebasubs-price-' . $level->getId();
$taxTargetId      = 'akeebasubs-tax-value-' . $level->getId();
$netTargetId      = 'akeebasubs-net-value-' . $level->getId();
$taxContainerId   = 'akeebasubs-taxinfo-rows-' . $level->getId();
$countryTarget    = 'akeebasubs-detected-country';

$js = <<< JS
window.jQuery(document).ready(function ($){
	akeebasubsLocalisePrice('$level->paddle_product_id', $allowTax, '$grossTargetId', '$taxTargetId', '$netTargetId', '$taxContainerId', '$countryTarget')
});
JS;

?>
@unless(!$this->localisePrice || ($priceInfo->levelPrice < 0.01))
@inlineJs($js)
@endunless
<div class="akeebasubs-awesome-price">
@if ($this->renderAsFree && $priceInfo->levelPrice < 0.01)
	@lang('COM_AKEEBASUBS_LEVEL_LBL_FREE')
@else
	<span class="akeebasubs-awesome-price-integer" id="{{ $netTargetId }}">
		@if ($currencyPosition == 'before')
			{{ $symbol }}
		@endif
		{{ $priceInfo->formattedPrice }}
		@if ($currencyPosition == 'after')
			{{ $symbol }}
		@endif
	</span>
@endif
</div>
@unless ($priceInfo->levelPrice < 0.01 || !$isTaxAllowed)
<div id="{{ $taxContainerId }}" style="display: none;">
	<div class="akeebasubs-awesome-taxnotice">
		<span class="hasTooltip" title="@lang('COM_AKEEBASUBS_LEVEL_LBL_ESTIMATED_TAX_TOOLTIP')">
			@lang('COM_AKEEBASUBS_LEVEL_LBL_ESTIMATED_TAX') <span id="{{ $taxTargetId }}"></span>
		</span>
		<sup>&dagger;</sup>
	</div>
	<div class="akeebasubs-awesome-prediscount">
		<span class="hasTooltip" title="@lang('COM_AKEEBASUBS_LEVEL_LBL_ESTIMATED_GROSS_TOOLTIP')">
			@lang('COM_AKEEBASUBS_LEVEL_LBL_ESTIMATED_GROSS') <span id="{{ $grossTargetId }}"></span>
		</span>
		<sup>&dagger;</sup>
	</div>
</div>
@endunless
