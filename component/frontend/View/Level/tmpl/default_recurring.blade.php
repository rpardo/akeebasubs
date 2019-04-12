<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

if (empty($this->upsellPlanId)) return;

$allowTax = $this->cparams->isTaxAllowed ? 'true' : 'false';

$js = <<< JS
window.jQuery(document).ready(function($) {
	akeebasubsLocaliseRecurring({$this->upsellPlanId}, $allowTax)
});

JS;

$langStrings = [
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_ONE_DAY',
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_DAY',
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_ONE_WEEK',
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_WEEK',
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_ONE_MONTH',
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_MONTH',
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_ONE_YEAR',
		'COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_PERIOD_YEAR',
];
array_walk($langStrings, [\Joomla\CMS\Language\Text::class, 'script']);

?>
@inlineJs($js)

<div class="akeeba-panel--success" id="akeebasubs-optin-recurring-container" style="display: none">
	<header class="akeeba-block-header">
		<h3>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_HEAD')
		</h3>
	</header>

	<div class="akeeba-form-group--checkbox--pull-right">
		<label>
			<input type="checkbox" name="use_recurring" id="use_recurring">
			@lang('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_CHECKBOX')
			@if($this->cparams->isTaxAllowed)
				*
			@endif
		</label>
		<p class="akeeba-help-text">
			@lang('COM_AKEEBASUBS_LEVEL_LBL_OPTIN_RECURRING_HELP')
		</p>

	</div>
</div>