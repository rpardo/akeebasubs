<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */
?>

@repeatable('upsell_level', $upsell)
    <?php ob_start() ?>
        <span id="akeebasubs-sum-upsell-{{ $upsell['level_id'] }}">
            @if ($this->cparams->currencypos == 'before')
                <span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
            @endif
            <span class="akeebasubs-level-price">
                {{ ($this->validation->price->discount < 0.01) ? $upsell['price'] : $upsell['price_diff'] }}
            </span>
            @if ($this->cparams->currencypos == 'after')
                <span class="akeebasubs-level-price-currency">{{{ $this->cparams->currencysymbol }}}</span>
            @endif
        </span>{{ ($this->cparams->isTaxAllowed && ($this->validation->price->discount < 0.01)) ? '*' : '' }}
    <?php $price = ob_get_clean() ?>

    @if ($this->validation->price->discount < 0.01)
        @sprintf('COM_AKEEBASUBS_LEVEL_LBL_RELATED_UPSELL_' . (empty($upsell['info_url']) ? 'NOLINK' : 'WITHLINK'), $upsell['info_url'], $upsell['title'], $price)
    @else
        @sprintf('COM_AKEEBASUBS_LEVEL_LBL_RELATED_UPSELL_RELATIVE_' . (empty($upsell['info_url']) ? 'NOLINK' : 'WITHLINK'), $upsell['info_url'], $upsell['title'], $price)
    @endif

    <a href="@route('index.php?option=com_akeebasubs&view=Level&slug=' . $upsell['slug'])" class="akeeba-btn--default--small">
        <span class="akion-arrow-graph-up-right"></span>
        @sprintf('COM_AKEEBASUBS_LEVEL_BTN_RELATED_UPSELL', $upsell['title'])
    </a>

    @if ($upsell['info_url'])
        <a href="{{ $upsell['info_url'] }}" class="akeeba-btn--ghost--small">
            <span class="akion-information-circled"></span>
            @lang("COM_AKEEBASUBS_LEVEL_BTN_RELATED_INFO")
        </a>
    @endif

    <?php
    $allowTax = $this->cparams->isTaxAllowed ? 'true' : 'false';
    $target = "akeebasubs-sum-upsell-{$upsell['level_id']}";
    $js = <<< JS
window.jQuery(document).ready(function ($){
	akeebasubsLocalisePrice('{$upsell['product_id']}', $allowTax, '{$target}', null, null, null, null)
});

JS;
      ?>
    @if ($upsell['canLocalise'] && ($this->validation->price->discount < 0.01))
        @inlineJs($js)
    @endif

@endrepeatable

<div class="akeeba-panel--teal">
    <h4>
        @lang('COM_AKEEBASUBS_LEVEL_LBL_RELATED_UPSELL_HEAD')
    </h4>

    @foreach ($this->upsellLevels as $upsell)
        <p>
            @yieldRepeatable('upsell_level', $upsell)
        </p>
    @endforeach

</div>
