<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var  \Akeeba\Subscriptions\Admin\Model\Levels  $level */
defined('_JEXEC') or die();
?>
<div class="akeeba-form-group">
    <label for="params_atscredits_credits">
		<?php echo JText::_('PLG_AKEEBASUBS_ATSCREDITS_CREDITS_TITLE'); ?>
    </label>
    <input type="text" name="params[atscredits_credits]" id="params_atscredits_credits"
           value="<?php echo isset($level->params['atscredits_credits']) ? $level->params['atscredits_credits'] : 0 ?>"
           class="input-small"
    />
    <p class="akeeba-help-text">
        <?php echo JText::_('PLG_AKEEBASUBS_ATSCREDITS_CREDITS_DESC') ?>
    </p>
</div>

<div class="akeeba-block--info">
	<p><?php echo JText::_('PLG_AKEEBASUBS_ATSCREDITS_USAGENOTE'); ?></p>
</div>
