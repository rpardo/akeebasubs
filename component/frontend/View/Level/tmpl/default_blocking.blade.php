<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

?>

<div class="akeeba-panel--red">
	<header class="akeeba-block-header">
		<h3>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_HEAD')
		</h3>
	</header>

	@if(($this->blockingSubscriptions->count() == 1) && $this->blockingSubscriptions->first()->akeebasubs_level_id == $this->item->akeebasubs_level_id)
		<p>
			@sprintf('COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_SAMELEVEL_INFO', $this->item->title)
		</p>
		<p>
			@sprintf('COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_SAMELEVEL_WHATTODO', $this->item->title)
		</p>
	@else
		<p>
			@sprintf('COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_RELATEDLEVEL_INFO', $this->item->title)
		</p>
		<p>
			@sprintf('COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_RELATEDLEVEL_WHATTODO', $this->item->title)
		</p>
	@endif

	<ul>
		@foreach ($this->blockingSubscriptions as $subscription)
		<li>
			@sprintf(
				'COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_SUBINFO',
				$subscription->getId(),
				$subscription->level->title,
				\Akeeba\Subscriptions\Admin\Helper\Format::date($subscription->publish_up),
				\Akeeba\Subscriptions\Admin\Helper\Format::date($subscription->publish_down)
			)

			<a class="akeeba-btn--red--small"
			   onclick="Paddle.Checkout.open({override: '{{ $subscription->cancel_url }}'});">
				<span class="akion-android-cancel"></span>
				@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_CANCEL_RECURRING')
			</a>
		</li>
		@endforeach
	</ul>

	<hr/>

	<p class="akeeba-help-text">
		@lang('COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_GOTOMYSUBS')
		<a href="@route('index.php?option=com_akeebasubs&view=Subscriptions&layout=default')">
			@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_TITLE')
		</a>
	</p>
</div>