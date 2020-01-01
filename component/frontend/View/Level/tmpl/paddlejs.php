<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Language\Text;

/** @var \FOF30\View\DataView\Html $this */

$vendor = $this->getContainer()->params->get('vendor_id');
$setupJS = <<< JS
window.jQuery('document').ready(function($){
    if (typeof Paddle === 'undefined' || Paddle === null)
    {
        var elJsElementsBlocked = $('#akeebasubs_js_elements_blocked');
        
        if (elJsElementsBlocked)
        {
            elJsElementsBlocked.show();
        }
        
        return;
    }
    
	Paddle.Setup({
		vendor: $vendor
	});
});
JS;

$this->addJavascriptFile('https://cdn.paddle.com/paddle/paddle.js');
$this->addJavascriptInline($setupJS);
?>
<div id="akeebasubs_js_elements_blocked" style="display: none;">
	<div class="akeeba-panel--red">
		<header class="akeeba-block-header">
			<h1>
				<span class="akion-heart-broken"></span>
				<?= Text::_('COM_AKEEBASUBS_LEVEL_LBL_BROKENJS_HEAD') ?>
			</h1>
		</header>
		<h2>
			<?= Text::_('COM_AKEEBASUBS_LEVEL_LBL_BROKENJS_SUBHEAD') ?>
		</h2>
		<hr/>
		<div class="akeeba-block--info">
			<h3>
				<?= Text::_('COM_AKEEBASUBS_LEVEL_LBL_BROKENJS_WHATTODO') ?>
			</h3>
		</div>
		<hr/>
		<p>
			<?= Text::_('COM_AKEEBASUBS_LEVEL_LBL_BROKENJS_WHY_CDN') ?>
		</p>
		<p>
			<?= Text::_('COM_AKEEBASUBS_LEVEL_LBL_BROKENJS_WHY_CHECKOUT') ?>
		<p>
			<?= Text::_('COM_AKEEBASUBS_LEVEL_LBL_BROKENJS_WELIKEPRIVACY') ?>
		</p>
	</div>
</div>
