<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Subscriptions\Html $this */
?>
@include('site:com_akeebasubs/Level/paddlejs')

<div id="akeebasubs" class="subscriptions">
	<h2 class="pageTitle">
		@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_TITLE')
	</h2>

	@include('site:com_akeebasubs/Subscriptions/tz_warning')

	@unless(count($this->displayInformation))
		<p>
			@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_NO_SUBSCRIPTIONS')
		</p>
		<p>
			<a href="@route('index.php?option=com_akeebasubs&view=Levels')"
			   class="akeeba-btn--big">
				<span class="akion-ios-cart"></span>
				@lang('COM_AKEEBASUBS_LEVELS_SUBSCRIBE')
			</a>
		</p>
	@else
		@each('site:com_akeebasubs/Subscriptions/default_level', $this->displayInformation, 'levelInfo', 'raw|Error retrieving subscription information')
	@endif
</div>


