<?php
/**
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Image;
use Akeeba\Subscriptions\Admin\Helper\Message;
?>

<div class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_YOURORDER')
		</h3>
	</header>
	<p>
		<span class="akeeba-label--grey">{{{$this->item->title}}}</span>
	</p>
	<div>
		@jhtml('content.prepare', Message::processLanguage($this->item->description))
	</div>
</div>
