<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Select;

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

$script = <<<JS

akeebasubs_level_id = {$this->item->akeebasubs_level_id};

JS;
$this->addJavascriptInline($script);

?>

<div id="akeebasubs">

	{{-- "Do Not Track" warning --}}
	@include('site:com_akeebasubs/Level/default_donottrack')

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
				<a href="http://enable-javascript.com" class="akeeba-btn--primary" target="_blank">
					<span class="akion-information-circled"></span>
					@lang('COM_AKEEBASUBS_LEVEL_ERR_NOJS_MOREINFO')
				</a>
			</p>
		</div>
	</noscript>

	<form
		action="@route('index.php?option=com_akeebasubs&view=Subscribe&layout=default&slug=' . $this->input->getString('slug', ''))"
		method="post"
		id="signupForm" class="akeeba-form--horizontal">
		<input type="hidden" name="@token()" value="1"/>

		<div class="akeeba-container--50-50">
			{{-- ACCOUNT COLUMN --}}
			<div id="akeebasubs-panel-account" class="akeeba-panel--info">
				<header class="akeeba-block-header">
					<h3>
						@lang('COM_AKEEBASUBS_LEVEL_LBL_ACCOUNTHEADER')
					</h3>
				</header>
				@include('site:com_akeebasubs/Level/default_fields')
			</div>

			{{-- ORDER COLUMN --}}
			<div id="akeebasubs-panel-order" class="akeeba-panel--primary">
				<header class="akeeba-block-header">
					<h3>
						@lang('COM_AKEEBASUBS_LEVEL_LBL_YOURORDER')
					</h3>
				</header>
				@include('site:com_akeebasubs/Level/default_summary')
			</div>
		</div>
	</form>

	{{-- Module position 'akeebasubscriptionsfooter' --}}
	@modules('akeebasubscriptionsfooter')
</div>

<?php
$aks_msg_error_overall = JText::_('COM_AKEEBASUBS_LEVEL_ERR_JSVALIDATIONOVERALL', true);
$script                = <<<JS

akeebasubs_apply_validation = {$this->apply_validation};

akeeba.jQuery(document).ready(function(){
	validatePassword();
	validateName();
	validateEmail();
	validateAddress();
	validateBusiness();
	validateForm();
});

function onSignupFormSubmit()
{
	if (akeebasubs_valid_form == false) {
		alert('$aks_msg_error_overall');
	}

	return akeebasubs_valid_form;
}

JS;
$this->addJavascriptInline($script);
