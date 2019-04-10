<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>

<div class="akeeba-panel--green">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('COM_AKEEBASUBS_MESSAGE_HEAD_COMMON', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			@lang('COM_AKEEBASUBS_MESSAGE_COMPLETE_HEAD_LABEL')
		</h3>
	</header>

	<p class="akeeba-block--success">
		@sprintf('COM_AKEEBASUBS_MESSAGE_WAITING_TOP_DETAIL', \Akeeba\Subscriptions\Admin\Helper\Format::date($this->subscription->publish_up, \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC2')))
	</p>

	<?php echo $this->message ?>
</div>