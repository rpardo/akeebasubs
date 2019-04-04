<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \FOF30\View\DataView\Html $this */

$vendor = $this->getContainer()->params->get('vendor_id');
$setupJS = <<< JS
window.jQuery('document').ready(function(){
	Paddle.Setup({
		vendor: $vendor
	});
});
JS;

$this->addJavascriptFile('https://cdn.paddle.com/paddle/paddle.js');
$this->addJavascriptInline($setupJS);
