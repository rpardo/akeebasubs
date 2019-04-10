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
			@sprintf('Subscription #%s &mdash; %s', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			The payment is not yet complete
		</h3>
	</header>

	<p class="akeeba-block--info">
		@sprintf('You started purchasing a %s subscription with the username <em>%s</em> and email address <em>%s</em> on %s but you didn\'t get the chance to finish paying for it. Would you like to retry the payment?', $this->subscription->level->title, $this->subscription->juser->username, $this->subscription->juser->email, \Akeeba\Subscriptions\Admin\Helper\Format::date($this->subscription->created_on, \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC2')))
	</p>


	<a class="akeeba-btn--primary--big"
	   href="javascript:Paddle.Checkout.open({override: '{{ $this->subscription->payment_url }}'});">
		<span class="akion-card"></span>
		@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_COMPLETEPAYMENT')
	</a>

	<a class="akeeba-btn--ghost--small"
	   href="@route('index.php?option=com_akeebasubs&view=Subscribe&task=cancel_unpaid&id=' . $this->subscription->getId())">
		<span class="akion-android-cancel"></span>
		@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_CANCEL_UNPAID')
	</a>

	<h4>
		Did you have an issue? Let us help you!
	</h4>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('changecountry')">
			Change the country
		</a>
	</h5>
	<div id="changecountry" style="display: none;">
		<p>
			On the payment popup look at its bottom right. There's a &ldquo;Log out&rdquo; link. Click on it. You can now re-enter your email address and select a different country.
		</p>
		<p>
			Pro tip: We will remember your country selection next time you buy something from us!
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('entervat')">
			Enter my VAT / tax ID
		</a>
	</h5>
	<div id="entervat" style="display: none;">
		<p>
			On the payment popup look at its bottom left. You can enter the tax ID there _without_ the country prefix. For example, enter 012345678 instead of EL012345678.
		</p>
		<p>
			If the VAT / tax ID is not accepted please check that the detected country is correct. Unsure? See above how to change your country.
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('paymethod')">
			Use a different payment method
		</a>
	</h5>
	<div id="paymethod" style="display: none;">
		<p>
			Please click on the &ldquo;Complete Payment&rdquo; button above to restart the payment process and choose a different payment method.
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('coupon')">
			Enter a coupon code
		</a>
	</h5>
	<div id="coupon" style="display: none;">
		<p>
			Please click on the &ldquo;I changed my mind&rdquo; button above to restart the subscription process. Before you click on &ldquo;Subscribe Now&rdquo; look further up the page, below the price. There's a &ldquo;Coupon Code&rdquo; link. Please click on it to enter your coupon code.
		</p>
	</div>

	<h5>
		<a href="javascript:akeebasubsToggleHelp('payissue')">
			Payment issue
		</a>
	</h5>
	<div id="payissue" style="display: none;">
		<p>
			We are sorry to hear that! If using a different payment method does not work for you please <a href="mailto:help@paddle.com">contact Paddle's Success Team</a>. Paddle is our reseller; they handle all billing enquiries on our behalf.
		</p>
	</div>


	<hr/>
	<p class="akeeba-help-text">
		Please note that if you paid by bank / wire transfer it might take a few days for the payment to clear. During this time your subscription will appear as not yet paid due to technical limitations. If this is the case please wait; don't try to pay for the subscription again. Thank you for your understanding!
	</p>
</div>
