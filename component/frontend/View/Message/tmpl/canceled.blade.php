<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Message\Html $this */

?>

<div class="akeeba-panel--grey">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('COM_AKEEBASUBS_MESSAGE_HEAD_COMMON', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			@lang('COM_AKEEBASUBS_MESSAGE_CANCELED_HEAD_LABEL')
		</h3>
	</header>

	<p class="akeeba-block--failure">
		@lang('COM_AKEEBASUBS_SUBSCRIPTION_DETAILED_CANCELLATION_REASON_' . $this->subscription->cancellation_reason)
	</p>

	<p>
		@lang('COM_AKEEBASUBS_MESSAGE_CANCELED_TOP_DETAIL')
	</p>

</div>