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
<div class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('Subscription #%s &mdash; %s', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			@lang('COM_AKEEBASUBS_MESSAGE_EXPIRED_HEAD_LABEL')
		</h3>
	</header>

	<p class="akeeba-block--warning">
		@sprintf('COM_AKEEBASUBS_MESSAGE_EXPIRED_TOP_DETAIL', \Akeeba\Subscriptions\Admin\Helper\Format::date($this->subscription->publish_down, \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC2')))
	</p>

	<?php echo $this->message ?>
</div>