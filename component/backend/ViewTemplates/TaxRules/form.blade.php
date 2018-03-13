<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Helper\Select;use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */

?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
	<div class="akeeba-form-group">
		<label for="akeebasubs_level_id">
			@lang('COM_AKEEBASUBS_TAXRULES_LEVEL')
		</label>
		{{ BrowseView::modelSelect('akeebasubs_level_id', 'Levels', $this->getItem()->akeebasubs_level_id, ['fof.autosubmit' => false, 'none' => '&mdash;&mdash;&mdash;', 'translate' => false]) }}
	</div>

	<div class="akeeba-form-group">
		<label for="country">
			@lang('COM_AKEEBASUBS_TAXRULES_COUNTRY')
		</label>
		{{ BrowseView::genericSelect('country', Select::getCountries(), $this->getItem()->country, ['fof.autosubmit' => false, 'none' => '&mdash;&mdash;&mdash;', 'translate' => false]) }}
	</div>

	<div class="akeeba-form-group">
		<label for="state">
			@lang('COM_AKEEBASUBS_TAXRULES_STATE')
		</label>
		{{ BrowseView::genericSelect('state', Select::getStates(), $this->getItem()->state, ['fof.autosubmit' => false, 'none' => '&mdash;&mdash;&mdash;', 'translate' => false]) }}
	</div>

	<div class="akeeba-form-group">
		<label for="city">
			@lang('COM_AKEEBASUBS_TAXRULES_CITY')
		</label>
		<input type="text" name="city" id="city" value="{{{ $this->getItem()->city }}}" />
	</div>

	<div class="akeeba-form-group">
		<label for="vies">
			@lang('COM_AKEEBASUBS_TAXRULES_VIES')
		</label>
		@jhtml('FEFHelper.select.booleanswitch', 'vies', $this->getItem()->vies)
	</div>

	<div class="akeeba-form-group">
		<label for="taxrate">
			@lang('COM_AKEEBASUBS_TAXRULES_TAXRATE')
		</label>
		<div class="akeeba-input-group">
			<span>%</span>
			<input type="number" min="0" max="100" step="0.01" name="taxrate" id="taxrate" value="{{{ $this->getItem()->taxrate }}}" />
		</div>
	</div>


	<div class="akeeba-form-group">
		<label for="enabled">
			@lang('JPUBLISHED')
		</label>
		@jhtml('FEFHelper.select.booleanswitch', 'enabled', $this->getItem()->enabled)
	</div>
@stop
