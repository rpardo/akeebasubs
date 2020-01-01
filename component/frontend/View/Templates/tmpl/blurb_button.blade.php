<?php
/**
 * Akeeba Subscriptions – Product button
 *
 * Variables:
 * 		img_url
 * 		title
 * 		target_url
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
<a href="{{ $target_url }}" class="akemail-button">
	<img src="{{ $img_url }}" alt="{{ $title }}" />
	{{ $title }}
</a>