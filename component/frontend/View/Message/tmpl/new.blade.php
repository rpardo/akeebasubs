<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Message\Html $this */

?>
<div class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('COM_AKEEBASUBS_MESSAGE_HEAD_COMMON', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_HEAD_LABEL')
		</h3>
	</header>

	<div class="akeeba-block--warning large">
		<h2>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_DONTPAYAGAIN')
		</h2>
		<p>
			@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_WAITAFEWMINUTES')
		</p>

	</div>

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
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_CANCELTEXT')
	</p>

	<hr/>

	<p>
		@lang('COM_AKEEBASUBS_MESSAGE_NEW_LBL_FOOTNOTE1')
	</p>

</div>
