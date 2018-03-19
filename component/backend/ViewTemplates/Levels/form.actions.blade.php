<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var  \Akeeba\Subscriptions\Admin\Model\Levels  $item */

$item->getContainer()->platform->importPlugin('akeebasubs');

$params = $item->params;

$jResponse = $item->getContainer()->platform->runPlugins('onSubscriptionLevelFormRender', array($item));
$tabCounter = 0;

if (is_array($jResponse) && !empty($jResponse)):
	?>
	<div class="akeeba-tabs" id="akeebasubs-level-tabs-actions">
		@foreach($jResponse as $customGroup)
		<label for="akeebasubs-level-tabs-action{{ ++$tabCounter }}">{{{ $customGroup->title }}}</label>
		<section id="akeebasubs-level-tabs-action{{ $tabCounter }}" class="{{ ($tabCounter == 1) ? 'active' : '' }}">
			{{ $customGroup->html }}
		</section>
		@endforeach
	</div>
<?php endif; ?>
