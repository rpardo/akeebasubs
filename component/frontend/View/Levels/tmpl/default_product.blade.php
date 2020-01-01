<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use \Akeeba\Subscriptions\Admin\Helper\Image;
use \Akeeba\Subscriptions\Admin\Helper\Message;

/**
 * @var \Akeeba\Subscriptions\Site\View\Levels\Html $this
 * @var \Akeeba\Subscriptions\Site\Model\Levels     $level
 * @var int                                         $i
 * @var int                                         $maxColumns
 */
?>
<div class="akeebasubs-awesome-column akeebasubs-level-{{{ $level->getId() }}}">
	<div class="column-{{ ($i == 1) ? 'first' : ($i == $maxColumns ? 'last' : 'middle') }}">
		<div class="akeebasubs-awesome-header">
			<div class="akeebasubs-awesome-level">
				<a href="@route('index.php?option=com_akeebasubs&view=Level&layout=default&format=html&slug=' . $level->slug)"
				   class="akeebasubs-awesome-level-link">
					{{{ $level->title }}}
				</a>
			</div>
			@include('site:com_akeebasubs/Levels/default_price', ['level' => $level])
		</div>
		<div class="akeebasubs-awesome-body">
			<div class="akeebasubs-awesome-image">
				<img src="{{ Image::getURL($level->image) }}" alt="{{{ $level->title }}}" />
			</div>
			<div class="akeebasubs-awesome-description">
				@jhtml('content.prepare', Message::processLanguage($level->description))
			</div>
		</div>
		<div class="akeebasubs-awesome-footer">
			<div class="akeebasubs-awesome-subscribe">
				<button
						class="btn btn-inverse btn-default"
						onclick="window.location='@route('index.php?option=com_akeebasubs&view=level&slug=' . $level->slug . '&format=html&layout=default')'">
					@lang('COM_AKEEBASUBS_LEVELS_SUBSCRIBE')
				</button>
			</div>
		</div>
	</div>
</div>
