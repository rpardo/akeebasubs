<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Select;

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

$layout = $this->input->getCmd('layout', 'default');
?>

<div id="akeebasubs">

	{{-- Module position 'akeebasubscriptionsheader' --}}
	@modules('akeebasubscriptionsheader')

	<div class="clearfix"></div>

	{{-- Warning when Javascript is disabled --}}
	<noscript>
		<div class="akeeba-block--warning">
			<h4>
				<span class="glyphicon glyphicon-alert"></span>
				@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_HEADER')
			</h4>
			<p>@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_BODY')</p>
			<p>
				<a href="https://www.enable-javascript.com" class="akeeba-btn--primary" target="_blank">
					<span class="akion-information-circled"></span>
					@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_MOREINFO')
				</a>
			</p>
		</div>
	</noscript>

	<form
		action="@route('index.php?option=com_akeebasubs&view=Subscribe&layout='.$layout.'&slug=' . $this->input->getString('slug', ''))"
		method="post"
		id="signupForm" class="akeeba-form--horizontal">
		<input type="hidden" name="@token()" value="1"/>

		{{-- PRODUCT SUMMARY --}}
		@include('site:com_akeebasubs/Level/default_product')

		{{-- USER ACCOUNT--}}
		@include('site:com_akeebasubs/Level/default_account')

		{{-- SUBSCRIBE BUTTON --}}
		@include('site:com_akeebasubs/Level/default_subscribe')
	</form>

	<div class="clearfix"></div>

	{{-- Module position 'akeebasubscriptionsfooter' --}}
	@modules('akeebasubscriptionsfooter')

	<div class="clearfix"></div>
</div>