<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>

<div class="akeeba-panel--green">
	<header class="akeeba-block-header">
		<h3>
			@sprintf('Subscription #%s &mdash; %s', $this->subscription->getId(), $this->subscription->level->title)
			&mdash;
			The subscription is active
		</h3>
	</header>

	<p class="akeeba-block--success">
		Your payment is successful and your subscription is active.
	</p>

	<?php echo $this->message ?>
</div>