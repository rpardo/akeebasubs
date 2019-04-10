<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Message\Html $this */

$js = <<< JS

function akeebasubsToggleHelp(id)
{
	var elContainer = document.getElementById(id);
	if (elContainer === null) return;
	elContainer.style.display = (elContainer.style.display == 'none') ? 'block' : 'none';
}
JS;

?>
@inlineJs($js)
@include('site:com_akeebasubs/Level/paddlejs')

<div class="akeeba-panel--red">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('COM_AKEEBASUBS_MESSAGE_HEAD_COMMON', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HEAD_LABEL')
		</h3>
	</header>

	<p class="akeeba-block--info">
		@sprintf('COM_AKEEBASUBS_MESSAGE_NEW_TOP_DETAIL', $this->subscription->level->title, $this->subscription->juser->username, $this->subscription->juser->email, \Akeeba\Subscriptions\Admin\Helper\Format::date($this->subscription->created_on, \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC2')))
	</p>

	<a class="akeeba-btn--primary--big"
	   href="javascript:Paddle.Checkout.open({override: '{{ $this->subscription->payment_url }}', successCallback: 'akeebasubsCheckoutComplete', closeCallback: 'akeebasubsCheckoutClosed', eventCallback: 'akeebasubsCheckoutEvent'});">
		<span class="akion-card"></span>
		@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_COMPLETEPAYMENT')
	</a>

	<a class="akeeba-btn--ghost--small"
	   href="@route('index.php?option=com_akeebasubs&view=Subscribe&task=cancel_unpaid&id=' . $this->subscription->getId())">
		<span class="akion-android-cancel"></span>
		@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_CANCEL_UNPAID')
	</a>

	<h4>
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_HEAD_WECANHELP')
	</h4>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('changecountry')">
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_CHANGECOUNTRY_HEAD')
		</a>
	</h5>
	<div id="changecountry" style="display: none;">
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_CHANGECOUNTRY_BODY_P1')
		</p>
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_CHANGECOUNTRY_BODY_P2')
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('entervat')">
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_ENTERVAT_HEAD')
		</a>
	</h5>
	<div id="entervat" style="display: none;">
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_ENTERVAT_BODY_P1')
		</p>
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_ENTERVAT_BODY_P2')
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('paymethod')">
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_PAYMETHOD_HEAD')
		</a>
	</h5>
	<div id="paymethod" style="display: none;">
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_PAYMETHOD_BODY_P1')
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('coupon')">
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_COUPON_HEAD')
		</a>
	</h5>
	<div id="coupon" style="display: none;">
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_COUPON_BODY_P1')
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('payissue')">
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_PAYISSUE_HEAD')
		</a>
	</h5>
	<div id="payissue" style="display: none;">
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HELP_PAYISSUE_BODY_P1')
		</p>
	</div>

	<hr/>

	<p class="akeeba-help-text">
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_MSG_WIRETRANFSER')
	</p>
</div>
