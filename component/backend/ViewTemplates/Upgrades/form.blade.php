<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */

$typeOptions = [
	'value'       => JText::_('COM_AKEEBASUBS_UPGRADE_TYPE_VALUE'),
	'percent'     => JText::_('COM_AKEEBASUBS_UPGRADE_TYPE_PERCENT'),
	'lastpercent' => JText::_('COM_AKEEBASUBS_UPGRADE_TYPE_LASTPERCENT'),
]

?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
<div class="akeeba-container--50-50">

	<div class="akeeba-panel--teal" id="akeebasubs-upgrades-basic">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_UPGRADE_BASIC_TITLE')</h3>
		</header>

		<div class="akeeba-form-group">
			<label for="title">
				@fieldtitle('title')
			</label>
			<input type="text" name="title" id="title" value="{{{ $this->getItem()->title }}}" />
		</div>

		<div class="akeeba-form-group">
			<label for="enabled">
				@lang('JPUBLISHED')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', 'enabled', $this->getItem()->enabled)
		</div>

	</div>

	<div class="akeeba-panel--info akeebasubs-panel-force-top-margin" id="akeebasubs-upgrades-discount">
		<header class="akeeba-block-header">
			<h3>@lang('COM_AKEEBASUBS_UPGRADE_DISCOUNT_TITLE')</h3>
		</header>

		<div class="akeeba-form-group">
			<label for="from_id">
				@fieldtitle('from_id')
			</label>
			<?php echo BrowseView::modelSelect('from_id', 'Levels', $this->getItem()->from_id, ['fof.autosubmit' => false, 'none' => BrowseView::fieldLabelKey('from_id'), 'translate' => false]) ?>
		</div>

		<div class="akeeba-form-group">
			<label for="to_id">
				@fieldtitle('to_id')
			</label>
			<?php echo BrowseView::modelSelect('to_id', 'Levels', $this->getItem()->to_id, ['fof.autosubmit' => false, 'none' => BrowseView::fieldLabelKey('to_id'), 'translate' => false]) ?>
		</div>

		<div class="akeeba-form-group">
			<label for="min_presence">
				@fieldtitle('min_presence')
			</label>
			<input type="number" step="1" name="min_presence" id="min_presence" value="{{{ $this->getItem()->min_presence }}}" />
		</div>

		<div class="akeeba-form-group">
			<label for="max_presence">
				@fieldtitle('max_presence')
			</label>
			<input type="number" step="1" name="max_presence" id="max_presence" value="{{{ $this->getItem()->max_presence }}}" />
		</div>

		<div class="akeeba-form-group">
			<label for="type">
				@fieldtitle('type')
			</label>
			@jhtml('FEFHelper.select.genericlist', $typeOptions, 'type', ['id' => 'type', 'list.select' => $this->getItem()->type])
		</div>

		<div class="akeeba-form-group">
			<label for="value">
				@fieldtitle('value')
			</label>
			<input type="number" step="0.01" name="value" id="value" value="{{{ $this->getItem()->value }}}" />
		</div>

		<div class="akeeba-form-group">
			<label for="combine">
				@fieldtitle('combine')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', 'combine', $this->getItem()->combine)
		</div>

		<div class="akeeba-form-group">
			<label for="expired">
				@fieldtitle('expired')
			</label>
			@jhtml('FEFHelper.select.booleanswitch', 'expired', $this->getItem()->expired)
			<p class="akeeba-help-text">
				@lang('COM_AKEEBASUBS_UPGRADES_FIELD_EXPIRED_DESC')
			</p>
		</div>

	</div>

</div>
@stop
