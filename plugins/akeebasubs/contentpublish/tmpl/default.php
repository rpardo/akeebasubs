<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();
?>
<div class="akeeba-container--50-50">
    <div class="akeeba-panel--green">
        <div class="akeeba-form-group">
            <label for="params_contentpublish_publishcore" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHCORE_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_publishcore]', $params['contentpublish_publishcore']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHCORE_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_publishk2" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHK2_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_publishk2]', $params['contentpublish_publishk2']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHK2_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_publishsobipro" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHSOBIPRO_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_publishsobipro]', $params['contentpublish_publishsobipro']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHSOBIPRO_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_publishzoo" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHZOO_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_publishzoo]', $params['contentpublish_publishzoo']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_PUBLISHZOO_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_addgroups" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_ADDGROUPS_TITLE'); ?>
            </label>
			<?php echo JHtml::_('select.genericlist', $zooApps, 'params[contentpublish_addgroups][]', [
				'multiple' => 'multiple', 'size' => 8, 'class' => 'input-large',
			], 'value', 'text', $params['contentpublish_addgroups']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_ADDGROUPS_DESCRIPTION') ?>
            </p>
        </div>
    </div>

    <div class="akeeba-panel--red akeebasubs-panel-force-top-margin">
        <div class="akeeba-form-group">
            <label for="params_contentpublish_unpublishcore" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHCORE_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_unpublishcore]', $params['contentpublish_unpublishcore']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHCORE_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_unpublishk2" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHK2_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_unpublishk2]', $params['contentpublish_unpublishk2']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHK2_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_unpublishsobipro" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHSOBIPRO_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_unpublishsobipro]', $params['contentpublish_unpublishsobipro']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHSOBIPRO_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_unpublishzoo" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHZOO_TITLE'); ?>
            </label>
			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'params[contentpublish_unpublishzoo]', $params['contentpublish_unpublishzoo']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_UNPUBLISHZOO_DESCRIPTION') ?>
            </p>
        </div>
        <div class="akeeba-form-group">
            <label for="params_contentpublish_removegroups" class="control-label">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_REMOVEGROUPS_TITLE'); ?>
            </label>
			<?php echo JHtml::_('select.genericlist', $zooApps, 'params[contentpublish_removegroups][]', [
				'multiple' => 'multiple', 'size' => 8, 'class' => 'input-large',
			], 'value', 'text', $params['contentpublish_removegroups']) ?>
            <p class="akeeba-help-text">
				<?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_REMOVEGROUPS_DESCRIPTION') ?>
            </p>
        </div>
    </div>
</div>

<div class="akeeba-block--info">
    <p><?php echo JText::_('PLG_AKEEBASUBS_CONTENTPUBLISH_USAGENOTE'); ?></p>
</div>
