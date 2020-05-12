<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Joomla\CMS\Date\Date;

/** @var \Akeeba\Subscriptions\Site\View\Message\Html $this */

$jThen = new Date($this->subscription->created_on);
$timeAgo = time() - $jThen->toUnix();
?>
<div class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('COM_AKEEBASUBS_MESSAGE_HEAD_COMMON', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HEAD_LABEL')
		</h3>
	</header>

	@if ($timeAgo <= 1200)
	<div class="akeeba-block--warning large">
		<h2>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_DONTPAYAGAIN')
		</h2>
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_WAITAFEWMINUTES')
		</p>
	</div>
	@else
	@include('site:com_akeebasubs/Level/paddlejs')
	<div class="akeeba-block--warning large">
		<h2>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_PROBABLYFAILED')
		</h2>
		<p>
			<a class="akeeba-btn--green--big"
			   href="javascript:Paddle.Checkout.open({override: '{{ $this->subscription->payment_url }}', successCallback: 'akeebasubsCheckoutComplete', closeCallback: 'akeebasubsCheckoutClosed', eventCallback: 'akeebasubsCheckoutEvent'});">
				<span class="akion-card"></span>
				@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_RETRYPAYMENT')
			</a>

			<a class="akeeba-btn--red--small"
			   href="@route('index.php?option=com_akeebasubs&view=Subscribe&task=cancel_unpaid&id=' . $this->subscription->getId())">
				<span class="akion-android-cancel"></span>
				@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_STARTOVER')
			</a>
		</p>
	</div>
	@endif

	<p>
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_INFOTEXT1')
	</p>

	<p>
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_INFOTEXT2')
	</p>

	<p>
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_INFOTEXT3')
	</p>

	<p>
		@sprintf('COM_AKEEBASUBS_MESSAGE_NEW_LBL_CANCELTEXT', \Joomla\CMS\Router\Route::_('index.php?option=com_akeebasubs&view=Subscribe&task=cancel_unpaid&id=' . $this->subscription->getId()))
	</p>

	<hr/>

	<p>
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_FOOTNOTE1')
	</p>

</div>