<?php
/**
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license GNU General Public License version 3, or later
 */

/** @var \FOF30\View\DataView\Html $this */

defined('_JEXEC') or die;

?>

<div class="akeeba-grid">
    <a class="akeeba-action--grey"
	   href="@route('index.php?option=com_akeebasubs&view=Reports&task=renewals')" >
        <span class="akion-refresh"></span>
        @lang('COM_AKEEBASUBS_REPORTS_USER_RENEWAL')
    </a>
    <a class="akeeba-action--red"
	   href="@route('index.php?option=com_akeebasubs&view=Reports&task=missinginvoice')" >
        <span class="akion-android-warning"></span>
        @lang('COM_AKEEBASUBS_REPORTS_MISSINGINVOICE')
    </a>

</div>

<div class="akeeba-grid">
	<a href="@route('index.php?option=com_akeebasubs&view=Reports&task=invoices')" class="akeeba-action--teal">
		<span class="akion-ios-list"></span>
		@lang('COM_AKEEBASUBS_REPORTS_INVOICES')
	</a>

	<a href="@route('index.php?option=com_akeebasubs&view=Reports&task=vies')" class="akeeba-action--teal">
		<span class="akion-briefcase"></span>
		@lang('COM_AKEEBASUBS_REPORTS_VIES')
	</a>

	<a href="@route('index.php?option=com_akeebasubs&view=Reports&task=vatmoss')" class="akeeba-action--teal">
		<span class="akion-android-list"></span>
		@lang('COM_AKEEBASUBS_REPORTS_VATMOSS')
	</a>

	<a href="@route('index.php?option=com_akeebasubs&view=Reports&task=thirdcountry')" class="akeeba-action--teal">
		<span class="akion-ios-world"></span>
		@lang('COM_AKEEBASUBS_REPORTS_THIRDCOUNTRY_TITLE')
	</a>
</div>
