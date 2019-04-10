<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use \Akeeba\Subscriptions\Admin\Helper\Image;
use \Akeeba\Subscriptions\Admin\Helper\Message;

/** @var \Akeeba\Subscriptions\Site\View\Levels\Html $this */

// Load and initialise Paddle's JavaScript
echo $this->loadAnyTemplate('site:com_akeebasubs/Level/paddlejs')
?>

<div id="akeebasubs" class="levels awesome">

<?php echo $this->getContainer()->template->loadPosition('akeebasubscriptionslistheader')?>

<?php $max = count($this->items); ?>

<div class="akeebasubs-awesome">
	<div class="columns columns-<?php echo $max?>">
		<?php $i = 0; foreach($this->items as $level): $i++?>
		<?php
			$priceInfo   = $this->getLevelPriceInformation($level);
			$paddleClass = '';
			$paddleExtra = '';

			if ($level->paddle_product_id && ($priceInfo->levelPrice >= 0.01))
			{
				$paddleClass = 'paddle-net';
				$paddleExtra = 'data-product="' . $level->paddle_product_id;
			}
			?>
		<div class="akeebasubs-awesome-column akeebasubs-level-<?php echo $level->akeebasubs_level_id ?>">
			<div class="column-<?php echo $i == 1 ? 'first' : ($i == $max ? 'last' : 'middle')?>">
				<div class="akeebasubs-awesome-header">
					<div class="akeebasubs-awesome-level">
						<a href="<?php echo \JRoute::_('index.php?option=com_akeebasubs&view=Level&layout=default&format=html&slug='. $level->slug)?>" class="akeebasubs-awesome-level-link">
							<?php echo $this->escape($level->title)?>
						</a>
					</div>
					<div class="akeebasubs-awesome-price <?= $paddleClass ?>" <?= $paddleExtra ?>>
						<?php if($this->renderAsFree && ($priceInfo->levelPrice < 0.01)):?>
						<?php echo JText::_('COM_AKEEBASUBS_LEVEL_LBL_FREE') ?>
						<?php else: ?>
						<?php if($this->container->params->get('currencypos','before') == 'before'): ?><span class="akeebasubs-awesome-price-currency"><?php echo $this->container->params->get('currencysymbol','€')?></span><?php endif; ?><span class="akeebasubs-awesome-price-integer"><?php echo $priceInfo->priceInteger ?><?php if((int)$priceInfo->priceFractional > 0): ?></span><span class="akeebasubs-awesome-price-separator">.</span><span class="akeebasubs-awesome-price-decimal"><?php echo $priceInfo->priceFractional ?></span><?php endif; ?><?php if($this->container->params->get('currencypos','before') == 'after'): ?><span class="akeebasubs-awesome-price-currency"><?php echo $this->container->params->get('currencysymbol','€')?></span><?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="akeebasubs-awesome-body">
					<div class="akeebasubs-awesome-image">
						<img src="<?php echo Image::getURL($level->image)?>" />
					</div>
					<div class="akeebasubs-awesome-description">
						<?php echo JHTML::_('content.prepare', Message::processLanguage($level->description) );?>
					</div>
				</div>
				<div class="akeebasubs-awesome-footer">
					<td class="akeebasubs-awesome-subscribe">
						<button
							class="btn btn-inverse btn-default"
							onclick="window.location='<?php echo \JRoute::_('index.php?option=com_akeebasubs&view=level&slug='.$level->slug.'&format=html&layout=default')?>'">
							<?php echo JText::_('COM_AKEEBASUBS_LEVELS_SUBSCRIBE')?>
						</button>
					</td>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
		<div class="level-clear"></div>
	</div>
</div>

<?php echo $this->getContainer()->template->loadPosition('akeebasubscriptionslistfooter')?>
</div>
