<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Image;use Akeeba\Subscriptions\Admin\Helper\Message;

/** @var \Akeeba\Subscriptions\Site\View\Levels\Html $this */

$maxColumns = count($this->items);
$i          = 0;

?>
{{-- Load and initialise Paddle's JavaScript --}}
@include('site:com_akeebasubs/Level/paddlejs')
@js('media://com_akeebasubs/js/signup.js')

<div id="akeebasubs" class="levels awesome">

{{ $this->getContainer()->template->loadPosition('akeebasubscriptionslistheader') }}

<div class="akeebasubs-awesome">
	<div class="columns columns-{{ $maxColumns }}">

		@forelse ($this->items as $level)

			@include('site:com_akeebasubs/Levels/default_product', ['level' => $level, 'i' => ++$i, 'maxColumns' => $maxColumns])

		@empty

			No subscription levels have been set up yet.

		@endforelse

		<div class="level-clear"></div>
	</div>
</div>

{{ $this->getContainer()->template->loadPosition('akeebasubscriptionslistfooter') }}
</div>

<hr />
<p class="akeeba-help-text">
	@lang('COM_AKEEBASUBS_LEVEL_LBL_PRICEINFO_NODISCOUNT')
</p>

@if ($this->localisePrice)
<p class="akeeba-help-text">
	@sprintf('COM_AKEEBASUBS_LEVEL_LBL_PRICEINFO_LOCALISED', $this->container->params->get('currency', '€'), $this->container->params->get('currencysymbol', '€'))
</p>
@endif

@if ($this->isTaxAllowed)
<p class="akeeba-help-text">
	&dagger; @lang('COM_AKEEBASUBS_LEVEL_LBL_PRICEINFO_ESTIMATETAX')
</p>
@else
<p class="akeeba-help-text">
	@lang('COM_AKEEBASUBS_LEVEL_LBL_PRICE_AND_TAX')
</p>
@endif