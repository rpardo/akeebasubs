<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Helper\Select;
use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */

?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
	<div class="akeeba-form-group">
		<label for="country">
			@fieldtitle('country')
		</label>
		{{ BrowseView::genericSelect('country', Select::getCountries(), $this->getItem()->country, ['fof.autosubmit' => false, 'none' => '&mdash;&mdash;&mdash;', 'translate' => false]) }}
	</div>

	<div class="akeeba-form-group">
		<label for="label">
			@fieldtitle('label')
		</label>
		<input type="text" name="label" id="label" value="{{{ $this->getItem()->label }}}" />
	</div>

	<div class="akeeba-form-group">
		<label for="state">
			@fieldtitle('state')
		</label>
		<input type="text" name="state" id="state" value="{{{ $this->getItem()->state }}}" />
	</div>

	<div class="akeeba-form-group">
		<label for="enabled">
			@lang('JPUBLISHED')
		</label>
		@jhtml('FEFHelper.select.booleanswitch', 'enabled', $this->getItem()->enabled)
	</div>
@stop
