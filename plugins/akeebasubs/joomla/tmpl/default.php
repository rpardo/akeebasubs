<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();
/** @var plgAkeebasubsJoomla $this */
/** @var \Akeeba\Subscriptions\Site\Model\Levels $level */
?>
<div class="akeeba-container--50-50">
	<div class="akeeba-panel--green">
		<div class="akeeba-form-group">
			<label for="params_joomla_addgroups" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_JOOMLA_ADDGROUPS_TITLE'); ?>
			</label>
			<?php echo $this->getSelectField($level, 'add') ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_JOOMLA_ADDGROUPS_DESCRIPTION2') ?>
            </p>
		</div>
	</div>
	<div class="akeeba-panel--red akeebasubs-panel-force-top-margin">
		<div class="akeeba-form-group">
			<label for="params_joomla_removegroups" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_JOOMLA_REMOVEGROUPS_TITLE'); ?>
			</label>
			<?php echo $this->getSelectField($level, 'remove') ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_JOOMLA_REMOVEGROUPS_DESCRIPTION2') ?>
            </p>
		</div>
	</div>
</div>
<div class="akeeba-block--info">
	<p><?php echo JText::_('PLG_AKEEBASUBS_JOOMLA_USAGENOTE'); ?></p>
</div>
