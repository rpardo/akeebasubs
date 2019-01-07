<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
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
		<label for="username">
			@fieldtitle('username')
		</label>
		<input type="text" name="username" id="username" value="{{{ $this->getItem()->username }}}" />
		<p class="akeeba-help-text">
			@lang('COM_AKEEBASUBS_BLOCKRULES_FIELD_USERNAME_HELP')
		</p>
	</div>

	<div class="akeeba-form-group">
		<label for="name">
			@fieldtitle('name')
		</label>
		<input type="text" name="name" id="name" value="{{{ $this->getItem()->name }}}" />
		<p class="akeeba-help-text">
			@lang('COM_AKEEBASUBS_BLOCKRULES_FIELD_NAME_HELP')
		</p>
	</div>

	<div class="akeeba-form-group">
		<label for="email">
			@fieldtitle('email')
		</label>
		<input type="text" name="email" id="email" value="{{{ $this->getItem()->email }}}" />
		<p class="akeeba-help-text">
			@lang('COM_AKEEBASUBS_BLOCKRULES_FIELD_EMAIL_HELP')
		</p>
	</div>

	<div class="akeeba-form-group">
		<label for="iprange">
			@fieldtitle('iprange')
		</label>
		<input type="text" name="iprange" id="iprange" value="{{{ $this->getItem()->iprange }}}" />
		<p class="akeeba-help-text">
			@lang('COM_AKEEBASUBS_BLOCKRULES_FIELD_IPRANGE_HELP')
		</p>
	</div>

	<div class="akeeba-form-group">
		<label for="enabled">
			@lang('JPUBLISHED')
		</label>
		@jhtml('FEFHelper.select.booleanswitch', 'enabled', $this->getItem()->enabled)
	</div>
@stop
