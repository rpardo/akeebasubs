<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

?>

<div id="akeebasubs-panel-yourorder" class="akeeba-panel--info akeeba-container--33-66">
	<div class="akeebasubs-subscription-level">
		<div class="akeebasubs-subscription-level-inner-container">
			<h4>
				@lang('COM_AKEEBASUBS_LEVEL_LBL_YOURORDER')
			</h4>

			<img src="{{ \Joomla\CMS\Uri\Uri::base() }}{{ $this->item->image }}"
				 class="akeebasubs-subscription-level-image hasTooltip"
				 width="64px"
				 title="{{{ $this->item->title }}}" />

			<h5>
				{{{$this->item->title}}}
			</h5>
		</div>
	</div>
	<div class="akeebasubs-subscription-description">
		@jhtml('content.prepare', Akeeba\Subscriptions\Admin\Helper\Message::processLanguage($this->item->description))
	</div>
</div>

