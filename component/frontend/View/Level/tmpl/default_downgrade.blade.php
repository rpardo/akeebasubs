<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

?>

<div class="akeeba-panel--orange">
	<header class="akeeba-block-header">
		<h3>
			<span class="akion-android-warning"></span>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_DOWNGRADEWARNING_HEAD')
		</h3>
	</header>

	<p>
		@sprintf('COM_AKEEBASUBS_LEVEL_LBL_DOWNGRADEWARNING_INFO_' . $this->validation->price->expiration, $this->item->title)
	</p>

	<ul>
		@foreach ($this->warnSubscriptions as $subscription)
		<li>
			@sprintf(
				'COM_AKEEBASUBS_LEVEL_LBL_BLOCKEDBYRECURRING_SUBINFO',
				$subscription->getId(),
				$subscription->level->title,
				\Akeeba\Subscriptions\Admin\Helper\Format::date($subscription->publish_up),
				\Akeeba\Subscriptions\Admin\Helper\Format::date($subscription->publish_down)
			)
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