<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Message\Html $this */

?>
<div class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('Subscription #%s &mdash; %s', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			The subscription has expired
		</h3>
	</header>

	<p class="akeeba-block--warning">
		Your payment was successful but your subscription has expired.
	</p>

	<?php echo $this->message ?>
</div>