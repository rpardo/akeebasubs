<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */

$modeOptions = [
	'rules' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_RULES'),
	'fixed' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FIXED'),
	'flexi' => JText::_('COM_AKEEBASUBS_RELATIONS_MODE_FLEXI'),
];

$typeOptions = [
	'value'   => JText::_('COM_AKEEBASUBS_COUPON_TYPE_VALUE'),
	'percent' => JText::_('COM_AKEEBASUBS_COUPON_TYPE_PERCENT'),
];

$expirationOptions = [
	'replace' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_REPLACE'),
	'after'   => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_AFTER'),
	'overlap' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_EXPIRATION_OVERLAP'),
];

$uomOptions = [
	'd' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_D'),
	'w' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_W'),
	'm' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_M'),
	'y' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_UOM_Y'),
];

$timecalculationOptions = [
	'current' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMECALCULATION_CURRENT'),
	'future'  => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMECALCULATION_FUTURE'),
];

$roundingOptions = [
	'floor' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_FLOOR'),
	'ceil'  => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_CEIL'),
	'round' => JText::_('COM_AKEEBASUBS_RELATIONS_FIELD_FLEX_TIMEROUNDING_ROUND'),
];

$js = <<< JS
window.addEventListener('DOMContentLoaded', function(event) {
	akeebasubs_relations_mode_onChange();
});

function akeebasubs_relations_mode_onChange()
{
    var elMode = document.getElementById('mode');
	var elFixed = document.getElementById('akeebasubs-relations-fixed');
	var elFlexi = document.getElementById('akeebasubs-relations-flexi');
	var displayType = 'block';

    var mode = document.getElementById('mode').value;
    elFixed.style.display = 'none';
    elFlexi.style.display = 'none';

	if (mode === 'fixed')
	{
	    elFixed.style.display = displayType;
	}

	if (mode === 'flexi')
	{
	    elFlexi.style.display = displayType;
	}
}


JS;

$this->addJavascriptInline($js);

?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
<div class="akeeba-container--50-50">

	<div class="akeeba-panel--teal" id="akeebasubs-relations-basic">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_RELATION_BASIC_TITLE')</h3>
		</header>

		<div class="akeeba-form-group">
			<label for="source_level_id">
				@fieldtitle('source_level_id')
			</label>
			<?php echo BrowseView::modelSelect('source_level_id', 'Levels', $this->getItem()->source_level_id, ['fof.autosubmit' => false, 'none' => BrowseView::fieldLabelKey('source_level_id'), 'translate' => false]) ?>
		</div>

		<div class="akeeba-form-group">
			<label for="target_level_id">
				@fieldtitle('target_level_id')
			</label>
			<?php echo BrowseView::modelSelect('target_level_id', 'Levels', $this->getItem()->target_level_id, ['fof.autosubmit' => false, 'none' => BrowseView::fieldLabelKey('target_level_id'), 'translate' => false]) ?>
		</div>

		<div class="akeeba-form-group">
			<label for="mode">
				@fieldtitle('mode')
			</label>
			@jhtml('FEFHelper.select.genericlist', $modeOptions, 'mode', ['id' => 'mode', 'list.select' => $this->getItem()->mode, 'list.attr' => ['onchange' => 'akeebasubs_relations_mode_onChange();']])
		</div>

		<div class="akeeba-form-group">
			<label for="type">
				@fieldtitle('type')
			</label>
			@jhtml('FEFHelper.select.genericlist', $typeOptions, 'type', ['id' => 'type', 'list.select' => $this->getItem()->type])
		</div>

		<div class="akeeba-form-group">
			<label for="expiration">
				@fieldtitle('expiration')
			</label>
			@jhtml('FEFHelper.select.genericlist', $expirationOptions, 'expiration', ['id' => 'expiration', 'list.select' => $this->getItem()->expiration])
		</div>

		<div class="akeeba-form-group">
			<label for="combine">
				@fieldtitle('combine')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', 'combine', $this->getItem()->combine)
		</div>

		<div class="akeeba-form-group">
			<label for="enabled">
				@lang('JPUBLISHED')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', 'enabled', $this->getItem()->enabled)
		</div>

	</div>

	{{-- Do not remove the outer DIV. It's necessary for putting the inner DIV always to the right --}}
	<div>
		<div class="akeeba-panel--info" id="akeebasubs-relations-fixed">
			<header class="akeeba-block-header">
				<h3>@lang('COM_AKEEBASUBS_RELATION_FIXED_TITLE')</h3>
			</header>

			<div class="akeeba-form-group">
				<label for="amount">
					@fieldtitle('amount')
				</label>
				<input type="number" step="0.01" name="amount" id="amount" value="{{{ $this->getItem()->amount }}}" />
			</div>

		</div>

		<div class="akeeba-panel--info akeebasubs-panel-force-top-margin" id="akeebasubs-relations-flexi">
			<header class="akeeba-block-header">
				<h3>@lang('COM_AKEEBASUBS_RELATION_FLEXI_TITLE')</h3>
			</header>

			<div class="akeeba-form-group">
				<label for="flex_amount">
					@lang('COM_AKEEBASUBS_RELATION_FLEXIBLE_TITLE')
				</label>
				<input type="number" step="0.01" name="flex_amount" id="flex_amount" value="{{{ $this->getItem()->flex_amount }}}" />
			</div>

			<div class="akeeba-form-group">
				<label for="flex_period">
					@lang('COM_AKEEBASUBS_RELATION_FLEXI_PRE')
				</label>
				<input type="number" step="1" name="flex_period" id="flex_period" value="{{{ $this->getItem()->flex_period }}}" />
			</div>

			<div class="akeeba-form-group">
				<label for="flex_uom">
					@lang('COM_AKEEBASUBS_RELATION_FLEXI_POST')
				</label>
				@jhtml('FEFHelper.select.genericlist', $uomOptions, 'flex_uom', ['id' => 'flex_uom', 'list.select' => $this->getItem()->flex_uom])
			</div>


			<div class="akeeba-form-group">
				<label for="low_amount">
					@lang('COM_AKEEBASUBS_RELATION_LOWTHRESHOLD_TITLE')
				</label>
				<div>
					<input type="number" step="0.01" name="low_amount" id="low_amount" value="{{{ $this->getItem()->low_amount }}}" />
					<span>@lang('COM_AKEEBASUBS_RELATION_LOW_PRE')</span>
				</div>
			</div>

			<div class="akeeba-form-group">
				<label for="low_threshold">
					&nbsp;
				</label>
				<div>
					<input type="number" step="0.01" name="low_threshold" id="low_threshold" value="{{{ $this->getItem()->low_threshold }}}" />
					<span>@lang('COM_AKEEBASUBS_RELATION_LOW_POST')</span>
				</div>
			</div>


			<div class="akeeba-form-group">
				<label for="high_amount">
					@lang('COM_AKEEBASUBS_RELATION_HIGHTHRESHOLD_TITLE')
				</label>
				<div>
					<input type="number" step="0.01" name="high_amount" id="high_amount" value="{{{ $this->getItem()->high_amount }}}" />
					<span>@lang('COM_AKEEBASUBS_RELATION_HIGH_PRE')</span>
				</div>
			</div>

			<div class="akeeba-form-group">
				<label for="high_threshold">
					&nbsp;
				</label>
				<div>
					<input type="number" step="0.01" name="high_threshold" id="high_threshold" value="{{{ $this->getItem()->high_threshold }}}" />
					<span>@lang('COM_AKEEBASUBS_RELATION_HIGH_POST')</span>
				</div>
			</div>


			<div class="akeeba-form-group">
				<label for="flex_timecalculation">
					@lang('COM_AKEEBASUBS_RELATION_FLEXI_POST')
				</label>
				@jhtml('FEFHelper.select.genericlist', $timecalculationOptions, 'flex_timecalculation', ['id' => 'flex_timecalculation', 'list.select' => $this->getItem()->flex_timecalculation])
			</div>

			<div class="akeeba-form-group">
				<label for="flex_rounding">
					@lang('COM_AKEEBASUBS_RELATION_FLEXI_POST')
				</label>
				@jhtml('FEFHelper.select.genericlist', $roundingOptions, 'flex_rounding', ['id' => 'flex_rounding', 'list.select' => $this->getItem()->flex_rounding])
			</div>

		</div>
	</div>

</div>
@stop