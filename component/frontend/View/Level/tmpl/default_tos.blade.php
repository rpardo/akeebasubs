<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

$apply_validation = isset($this->apply_validation) ? ($this->apply_validation == 'true') : true;
$tosUrl           = $this->getContainer()->params->get('tos_url', 'tos.html');
$privacyUrl       = $this->getContainer()->params->get('privacy_url', 'privacy.html');
?>
<div id="akeebasubs-panel-account" class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_TOSHEADER')
		</h3>
	</header>

	<div class="akeeba-form-group--checkbox--pull-right{{ $this->validation->validation->tos ? '' : '--error' }}">
		<label>
			<input type="checkbox" name="accept_terms">
			@sprintf('COM_AKEEBASUBS_LEVEL_LBL_TOS', $tosUrl, $privacyUrl)
		</label>
		<p class="akeeba-help-text">
			@lang('COM_AKEEBASUBS_LEVEL_LBL_TOS_HELP')
		</p>
	</div>
</div>