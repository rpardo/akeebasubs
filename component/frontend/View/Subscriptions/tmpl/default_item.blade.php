<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Date\Date;

defined('_JEXEC') or die();

/**
 * @var \Akeeba\Subscriptions\Site\View\Subscriptions\Html $this
 * @var int                                                $subId
 * @var \Akeeba\Subscriptions\Site\Model\Subscriptions     $sub
 */
$sub        = $this->items[$subId];
$subPublish = new Date($sub->publish_up);
$now        = new Date();
$imageSize  = $this->container->params->get('summaryimages', 1);

/**
 * A subscription can be renewed if:
 * - We are allowed to show renewals
 * - The level is enabled
 * - The level is not recurring
 * - The level is not an only-once level
 * - There is no update_url and cancel_url (it's NOT an automatically recurring subscription)
 * - No other subscription on the same level is automatically recurring
 * - There is no other active, new or pending subscription on the same level (that would mean it's already renewed)
 * - Its payment status is Complete
 *
 * If, however, the level has a renew_url we will always display a renew button with that URL. This is used for public
 * beta subscriptions, leading users to make a purchase once the beta period is about to or has already expired.
 */
$hasRenew =
	($this->container->params->get('showrenew', 1) == 1)
	&& $sub->level->enabled
	&& !$sub->level->recurring
	&& !$sub->level->only_once
	&& empty($sub->update_url)
	&& empty($sub->cancel_url)
	&& !$this->hasOtherRecurringInLevel($sub)
	&& !$this->hasOtherActiveNewOrPendingInLevel($sub)
	&& ($sub->getFieldValue('state') == 'C');

$hasRenew             = $hasRenew || !empty($sub->level->renew_url);
$isRecurring          = !empty($sub->update_url) && !empty($sub->cancel_url);
$isRecurringSuspended = !$isRecurring && isset($sub->params['subscription_id']);

/**
 * A subscription can show the update and cancel URLs if:
 * - It is active
 * - It has both update_url and cancel_url
 * - No other subscription on the same level is recurring
 */
$hasRecurringButtons = $sub->enabled && !empty($sub->update_url) && !empty($sub->cancel_url) && !$this->hasOtherRecurringInLevel($sub);

if ($hasRecurringButtons)
{
	$hasRenew = false;
}

/**
 * A subscription can show the receipt URL if:
 * - It has a receipt_url
 */
$hasReceipt = !empty($sub->receipt_url);

/**
 * A subscription can show the invoice URL if:
 * - It has an akeebasubs_invoice_id
 * - It does not have a receipt URL
 * - The akeebasubs_invoice_id is a known invoice record that can be loaded
 * - The akeebasubs_invoice_id is of the 'akeebasubs' type (some older invoices use ccInvoices or other 3PD extensions)
 */
$hasLegacyInvoice = !empty($sub->akeebasubs_invoice_id) && empty($sub->receipt_url)
	&& array_key_exists($sub->akeebasubs_subscription_id, $this->invoices)
	&& ($this->invoices[$sub->akeebasubs_subscription_id]->extension == 'akeebasubs');

/**
 * A subscription can show the Pay Now link if:
 * - Its status is New
 * - It has a payment_url
 */
$hasPayNow = ($sub->getFieldValue('state') == 'N') && !empty($sub->payment_url);

$hasButtons = $hasRenew || $hasRecurringButtons || $hasReceipt || $hasLegacyInvoice || $hasPayNow;
?>

<div class="akeeba-panel--info akeebasubs-subscription-container">
	<header class="akeebasubs-subscription-header akeeba-block-header">
		<h5>
			@if ($imageSize && !empty($sub->level->image))
				<img src="{{ \Joomla\CMS\Uri\Uri::base() }}{{ $sub->level->image }}"
					 class="akeebasubs-subscription-level-image hasTooltip"
					 style="vertical-align:middle"
					 width="{{ (int)$imageSize }}px"
					 title="{{{ $sub->level->title }}}" />
			@endif
			<span class=akeebasubs-subscription-level-title">
				{{{ $sub->level->title }}}
				@if ($isRecurring)
					<span class="akion-refresh hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_TIP_RECURRING_ACTIVE')"></span>
				@elseif ($isRecurringSuspended)
					<span class="akion-android-remove-circle hasTooltip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_TIP_RECURRING_CANCELED')"></span>
				@endif
			</span>

				<span class="akeebasubs-subscription-created pull-right">
				{{ \Akeeba\Subscriptions\Admin\Helper\Format::date($sub->created_on) }}
			</span>
		</h5>
	</header>

	<p class="akeebasubs-subscription-info">
		<span class="akeebasubs-subscription-subid">
			#{{ $sub->getId()}}
		</span>
		<span class="pull-right">
		@if ($sub->getFieldValue('state') == 'N')
				@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_UNPAID')
			@elseif ($sub->getFieldValue('state') == 'P')
				@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_PENDING_PAYMENT')
			@elseif ($sub->getFieldValue('state') == 'X')
				@lang('COM_AKEEBASUBS_SUBSCRIPTION_DETAILED_CANCELLATION_REASON_' . $sub->cancellation_reason)
			@elseif ($sub->enabled)
				@sprintf(
					'COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISHDATES_ACTIVE',
					\Akeeba\Subscriptions\Admin\Helper\Format::date($sub->publish_up),
					\Akeeba\Subscriptions\Admin\Helper\Format::date($sub->publish_down)
				)
			@elseif ($subPublish->getTimestamp() > $now->getTimestamp())
				@sprintf(
					'COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISHDATES_RENEWAL',
					\Akeeba\Subscriptions\Admin\Helper\Format::date($sub->publish_up),
					\Akeeba\Subscriptions\Admin\Helper\Format::date($sub->publish_down)
				)
			@else
				@sprintf(
					'COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISHDATES_EXPIRED',
					\Akeeba\Subscriptions\Admin\Helper\Format::date($sub->publish_up),
					\Akeeba\Subscriptions\Admin\Helper\Format::date($sub->publish_down)
				)
			@endif
		</span>
	</p>

	@if(($sub->getFieldValue('state') != 'P') && $hasButtons)
		<hr/>

		<div class="akeebasubs-subscription-buttons">
			@if ($hasPayNow)
				<a class="akeeba-btn--primary--big"
				   onclick="Paddle.Checkout.open({override: '{{ $sub->payment_url }}', successCallback: 'akeebasubsCheckoutComplete', closeCallback: 'akeebasubsCheckoutClosed', eventCallback: 'akeebasubsCheckoutEvent'});">
					<span class="akion-card"></span>
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_COMPLETEPAYMENT')
				</a>

				<a class="akeeba-btn--ghost--small"
				   href="@route('index.php?option=com_akeebasubs&view=Subscribe&task=cancel_unpaid&id=' . $subId)">
					<span class="akion-android-cancel"></span>
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_CANCEL_UNPAID')
				</a>
			@endif

			@if ($hasRecurringButtons)
				<a class="akeeba-btn--teal"
				   onclick="Paddle.Checkout.open({override: '{{ $sub->update_url }}'});">
					<span class="akion-gear-b"></span>
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_UPDATE')
				</a>

				<a class="akeeba-btn--red--small"
				   onclick="Paddle.Checkout.open({override: '{{ $sub->cancel_url }}'});">
					<span class="akion-android-cancel"></span>
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_CANCEL_RECURRING')
				</a>
			@endif

			@if ($hasRenew)
				@if ($sub->level->renew_url)
				<a class="akeeba-btn--green" href="{{ $sub->level->renew_url }}">
					<span class="akion-refresh"></span>
					@lang($sub->enabled ? 'COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_RENEW' : 'COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_REPURCHASE')
				</a>
				@else
				<a class="akeeba-btn--green" href="@route('index.php?option=com_akeebasubs&view=level&slug=' . $sub->level->slug)">
					<span class="akion-refresh"></span>
					@lang($sub->enabled ? 'COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_RENEW' : 'COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_REPURCHASE')
				</a>
				@endif
			@endif

			@if ($hasReceipt)
				<a href="{{ $sub->receipt_url }}" class="akeeba-btn--grey">
					<span class="akion-document-text"></span>
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_RECEIPT')
				</a>
			@endif

			@if ($hasLegacyInvoice)
				<a class="akeeba-btn--small--grey"
				   href="@route('index.php?option=com_akeebasubs&view=Invoice&task=read&id=' . $subId . '&tmpl=component')"
				   target="_blank"
				>
					<span class="akion-document-text"></span>
					@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_INVOICE')
				</a>
			@endif
		</div>
	@endif

</div>
