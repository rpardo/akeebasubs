<?php defined('_JEXEC') or die();
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var plgAkeebasubsAcymailing $this */
/** @var \Akeeba\Subscriptions\Site\Model\Levels $level */

$params = $level->params;
?>

<div class="akeeba-form-group">
    <label for="params_reseller_company_url">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_COMPANYURL_LABEL'); ?>
    </label>
    <input type="text" name="params[reseller_company_url]" id="params_reseller_company_url"
           value="<?php echo isset($params['reseller_company_url']) ? $params['reseller_company_url'] : ''; ?>"/>
    <p class="akeeba-help-text">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_COMPANYURL_DESC') ?>
    </p>
</div>
<div class="akeeba-form-group">
    <label for="params_reseller_api_key">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_APIKEY_LABEL'); ?>
    </label>
    <input type="text" name="params[reseller_api_key]" id="params_reseller_api_key"
           value="<?php echo isset($params['reseller_api_key']) ? $params['reseller_api_key'] : ''; ?>"/>
    <p class="akeeba-help-text">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_APIKEY_DESCR') ?>
    </p>
</div>
<div class="akeeba-form-group">
    <label for="params_reseller_api_pwd">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_APIPWD_LABEL'); ?>
    </label>
    <input type="password" name="params[reseller_api_pwd]" id="params_reseller_api_pwd"
           value="<?php echo isset($params['reseller_api_pwd']) ? $params['reseller_api_pwd'] : ''; ?>"/>
    <p class="akeeba-help-text">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_APIPWD_DESCR') ?>
    </p>
</div>
<div class="akeeba-form-group">
    <label for="params_reseller_notify_emails">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_NOTIFYEMAILS_LABEL'); ?>
    </label>
    <input type="text" name="params[reseller_notify_emails]"
           id="params_reseller_notify_emails"
           value="<?php echo isset($params['reseller_notify_emails']) ? $params['reseller_notify_emails'] : ''; ?>"/>
    <p class="akeeba-help-text">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_NOTIFYEMAILS_DESC') ?>
    </p>
</div>
<div class="akeeba-form-group">
    <label for="params_reseller_frontend_label">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_FRONTEND_LABEL_LABEL'); ?>
    </label>
    <input type="text" name="params[reseller_frontend_label]"
           id="params_reseller_frontend_label"
           value="<?php echo isset($params['reseller_frontend_label']) ? htmlentities($params['reseller_frontend_label']) : ''; ?>"/>
    <p class="akeeba-help-text">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_FRONTEND_LABEL_DESC') ?>
    </p>
</div>
<div class="akeeba-form-group">
    <label for="params_reseller_frontend_format">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_FRONTEND_FORMAT_LABEL'); ?>
    </label>
    <input type="text" name="params[reseller_frontend_format]"
           id="params_reseller_frontend_format"
           value="<?php echo isset($params['reseller_frontend_format']) ? htmlentities($params['reseller_frontend_format']) : ''; ?>"/>
    <p class="akeeba-help-text">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_FRONTEND_FORMAT_DESC') ?>
    </p>
</div>
<div class="akeeba-form-group">
    <label for="params_reseller_coupon_link">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_COUPON_LINK_LABEL'); ?>
    </label>
    <input type="text" name="params[reseller_coupon_link]" id="params_reseller_coupon_link"
           value="<?php echo isset($params['reseller_coupon_link']) ? $params['reseller_coupon_link'] : ''; ?>"/>
    <p class="akeeba-help-text">
		<?php echo JText::_('PLG_AKEEBASUBS_RESELLER_COUPON_LINK_DESC') ?>
    </p>
</div>
