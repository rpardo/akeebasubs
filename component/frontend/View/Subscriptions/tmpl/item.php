<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Subscriptions\Html $this */

Use Akeeba\Subscriptions\Admin\Helper\Format;
use Akeeba\Subscriptions\Admin\Helper\Validator;

$this->getContainer()->platform->importPlugin('akeebasubs');

$app        = JFactory::getApplication();
$jPublishUp = $this->getContainer()->platform->getDate($this->item->publish_up);
$goBackURL  = JRoute::_('index.php?option=com_akeebasubs&view=subscriptions');

if ($this->returnURL)
{
	$goBackURL  = $this->returnURL;
}
?>

<div id="akeebasubs">

<table class="akeeba-table--striped">
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_COMMON_ID')?></td>
		<td class="subscription-info">
			<strong><?php echo sprintf('%05u', $this->item->akeebasubs_subscription_id)?></strong>
		</td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_USER')?></td>
		<td class="subscription-info">
			<strong><?php echo $this->container->platform->getUser($this->item->user_id)->username?></strong>
			(<em><?php echo $this->container->platform->getUser($this->item->user_id)->name?></em>)
		</td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_LEVEL')?></td>
		<td class="subscription-info">
			<?php echo $this->item->level->title ?>
		</td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISH_UP')?></td>
		<td class="subscription-info">
			<?php echo Format::date($this->item->publish_up) ?>
		</td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISH_DOWN')?></td>
		<td class="subscription-info">
			<?php echo Format::date($this->item->publish_down) ?>
		</td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_ENABLED')?></td>
		<td class="subscription-info">
			<?php if($this->item->enabled):?>
				<span class="akion-checkmark" title="<?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_ENABLED_ACTIVE') ?>"></span>
			<?php elseif($jPublishUp->toUnix() >= time()):?>
				<span class="akion-android-time" title="<?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_ENABLED_PENDING') ?>"></span>
			<?php else:?>
				<span class="akion-close" title="<?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_ENABLED_INACTIVE') ?>"></span>
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_STATE')?></td>
		<td class="subscription-info"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_'.$this->item->getFieldValue('state', 'N'))?></td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTION_AMOUNT_PAID')?></td>
		<td class="subscription-info">
			<?php if($this->container->params->get('currencypos','before') == 'before'): ?>
			<?php echo $this->container->params->get('currencysymbol','€')?>
			<?php endif; ?>
			<?php echo sprintf('%2.02F',$this->item->gross_amount)?>
			<?php if($this->container->params->get('currencypos','before') == 'after'): ?>
			<?php echo $this->container->params->get('currencysymbol','€')?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td class="subscription-label"><?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTION_SUBSCRIBED_ON')?></td>
		<td class="subscription-info">
			<?php echo Format::date($this->item->created_on) ?>
		</td>
	</tr>
</table>

<div class="akeebasubs-goback">
	<p>
		<a class="akeeba-btn--primary--big" href="<?php echo $goBackURL; ?>">
			<span class="akion-chevron-left"></span>
			<?php echo JText::_('COM_AKEEBASUBS_SUBSCRIPTIONS_TITLE')?>
		</a>
	</p>
</div>

	<?php // Subscription ID, so it's available for JS stuff ?>
	<input type="hidden" id="akeebasubs_subscription_id" value="<?php echo $this->item->akeebasubs_subscription_id?>"/>
	<input type="hidden" id="akeebasubs_level_id" value="<?php echo $this->item->akeebasubs_level_id?>"/>
</div>
