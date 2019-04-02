<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Site\Model\Subscribe\StateData;
use FOF30\Container\Container;

defined('_JEXEC') or die();

/**
 * A plugin which creates two extra fields for conformance to EU directives regarding consumer protection and VAT.
 */
class plgAkeebasubsAgreetoeu extends JPlugin
{
	function onSubscriptionFormPrepaymentRender($userparams, $cache)
	{
		$level_id = $cache['id'];

		// Load the language
		$lang = JFactory::getLanguage();
		$lang->load('plg_akeebasubs_agreetoeu', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('plg_akeebasubs_agreetoeu', JPATH_ADMINISTRATOR, null, true);

		// Init the fields array which will be returned
		$fields = array();

		// ----- CONFIRM BEING INFORMED FIELD -----
		$hasInformed = 0;

		if (!$this->isExcluded('informed', $level_id))
		{
			$hasInformed = 1;
			// Setup the combobox parameters
			$labelText = JText::_('PLG_AKEEBASUBS_AGREETOEU_CONFIRM_INFORMED_LABEL');
			$extraText = JText::_('PLG_AKEEBASUBS_AGREETOEU_CONFIRM_INFORMED_DESC');
			$html = <<<HTML
	<label class="checkbox">
		<input type="checkbox" name="custom[confirm_informed]" id="confirm_informed" />
		<span class="akion-information-circled hasPopover" title="$labelText" data-content="$extraText"></span>
		$labelText
	</label>
HTML;

			// Setup the field
			$field = array(
				'id'           => 'confirm_informed',
				'label'        => '* ',
				'elementHTML'  => $html,
				'isValid'      => false
			);
			// Add the field to the return output
			$fields[] = $field;
		}

		// ----- CONFIRM RIGHT TO WITHDRAWAL FIELD -----
		$hasWithdrawal = 0;

		if (!$this->isExcluded('withdrawal', $level_id))
		{
			$hasWithdrawal = 1;

			// Setup the combobox parameters
			$labelText = JText::_('PLG_AKEEBASUBS_AGREETOEU_CONFIRM_WITHDRAWAL_LABEL');
			$extraText = JText::_('PLG_AKEEBASUBS_AGREETOEU_CONFIRM_WITHDRAWAL_DESC');
			$html = <<<HTML
<label class="checkbox">
	<input type="checkbox" name="custom[confirm_withdrawal]" id="confirm_withdrawal" />
	<span class="akion-information-circled hasPopover" title="$labelText" data-content="$extraText"></span>
	$labelText
</label>
HTML;

			// Setup the field
			$field = array(
				'id'           => 'confirm_withdrawal',
				'label'        => '* ',
				'elementHTML'  => $html,
				'isValid'      => false
			);

			// Add the field to the return output
			$fields[] = $field;
		}

		// ----- EU DATA PROTECTION POLICY (GDPR COMPLIANCE) -----
		$hasEUDataInteger = 0;

		$eudataURL        = $this->params->get('eudataurl', '');
		$eudataURL        = trim($eudataURL);
		$hasEUData        = !empty($eudataURL);
//		$hasEUDataInteger = $hasEUData ? 1 : 0;

		if (!$this->isExcluded('eudata', $level_id) && $hasEUData)
		{
			$hasEUDataInteger = 1;
			// Setup the combobox parameters
			$labelText = JText::sprintf('PLG_AKEEBASUBS_AGREETOEU_CONFIRM_EUDATA_LABEL', $eudataURL);
			$extraText = JText::_('PLG_AKEEBASUBS_AGREETOEU_CONFIRM_EUDATA_DESC');
			$html = <<<HTML
<label class="checkbox">
	<input type="checkbox" name="custom[confirm_eudata]" id="confirm_eudata" />
	<span class="akion-information-circled hasPopover" title="$labelText" data-content="$extraText"></span>
	$labelText
</label>
HTML;

			// Setup the field
			$field = array(
				'id'           => 'confirm_eudata',
				'label'        => '* ',
				'elementHTML'  => $html,
				'isValid'      => false
			);

			// Add the field to the return output
			$fields[] = $field;
		}

		// ----- ADD THE JAVASCRIPT -----
		$javascript = <<<JS

(function($) {
	$(document).ready(function(){
		// Tell Akeeba Subscriptions how to fetch the extra field data
		addToValidationFetchQueue(plg_akeebasubs_agreetoeu_fetch);
		// Tell Akeeba Subscriptions how to validate the extra field data
		addToValidationQueue(plg_akeebasubs_agreetoeu_validate);
		
		// Immediate validation of the field
		if (akeebasubs_apply_validation)
		{
			if ($hasInformed)
			{
				$('#confirm_informed').change(function(e){
					if($('#confirm_informed').is(':checked')) {
						$('#confirm_informed').parents('div.form-group').removeClass('has-error');
					} else {
						$('#confirm_informed').parents('div.form-group').addClass('has-error');
					}
				});
			}

			if ($hasWithdrawal)
			{
				$('#confirm_withdrawal').change(function(e){
					if($('#confirm_withdrawal').is(':checked')) {
						$('#confirm_withdrawal').parents('div.form-group').removeClass('has-error');
					} else {
						$('#confirm_withdrawal').parents('div.form-group').addClass('has-error');
					}
				});
			}
			
			if ($hasEUDataInteger)
			{
				$('#confirm_eudata').change(function(e){
					if($('#confirm_eudata').is(':checked')) {
						$('#confirm_eudata').parents('div.form-group').removeClass('has-error');
					} else {
						$('#confirm_eudata').parents('div.form-group').addClass('has-error');
					}
				});
			}
		}
	});
})(akeeba.jQuery);

function plg_akeebasubs_agreetoeu_fetch()
{
	var result = {};

	(function($) {
		result.confirm_informed = $('#confirm_informed').is(':checked') ? 1 : 0;
		result.confirm_withdrawal = $('#confirm_withdrawal').is(':checked') ? 1 : 0;
		
		if ($hasEUDataInteger)
		{
			result.confirm_eudata = $('#confirm_eudata').is(':checked') ? 1 : 0;
		}
	})(akeeba.jQuery);

	return result;
}

function plg_akeebasubs_agreetoeu_validate(response)
{
    var thisIsValid = true;

	(function($) {
		$('#confirm_informed').parents('div.form-group').removeClass('has-error');
		$('#confirm_withdrawal').parents('div.form-group').removeClass('has-error');
		
		if ($hasEUDataInteger) {
			$('#confirm_eudata').parents('div.form-group').removeClass('has-error');
		}

		if (!akeebasubs_apply_validation)
		{
			thisIsValid = true;
			return;
		}
		
		if (!response.custom_validation.confirm_informed) {
			$('#confirm_informed').parents('div.form-group').addClass('has-error');
			thisIsValid = false;
		}

		if (!response.custom_validation.confirm_withdrawal) {
			$('#confirm_withdrawal').parents('div.form-group').addClass('has-error');
			thisIsValid = false;
		}
		
		if ($hasEUDataInteger) {
			if (!response.custom_validation.confirm_eudata) {
				$('#confirm_eudata').parents('div.form-group').addClass('has-error');
				thisIsValid = false;
			}
		}
		
	})(akeeba.jQuery);

	return thisIsValid;
}

JS;
		$container = Container::getInstance('com_akeebasubs');
		$container->template->addJSInline($javascript);

		// ----- RETURN THE FIELDS -----
		return $fields;
	}

	/**
	 * @param   StateData $data
	 *
	 * @return  array
	 */
	function onValidate($data)
	{
		$level_id  = $data->id;
		$eudataURL = $this->params->get('eudataurl', '');
		$eudataURL = trim($eudataURL);
		$hasEUData = !empty($eudataURL);

		$response = array(
			'valid'             => true,
			'isValid'           => true,
			'custom_validation' => array(),
		);

		$custom = $data->custom;

		// If we don't have a URL we don't show the field, therefore we force it validated to go on
//		if (!$hasEUData)
//		{
//			$custom['confirm_eudata'] = 2;
//		}

		foreach (['informed', 'withdrawal', 'eudata'] as $fieldName)
		{
			if ($this->isExcluded($fieldName, $level_id))
			{
				continue;
			}

			if (!array_key_exists('confirm_' . $fieldName, $custom))
			{
				$custom['confirm_' . $fieldName] = 0;
			}

			$custom['confirm_' . $fieldName]                        = $this->isTruthism($custom['confirm_' . $fieldName]) ? 1 : 0;
			// If we don't have a URL we don't show the field, therefore we force it validated to go on
			if ($fieldName == 'eudata' && !$hasEUData) {
				$custom[ 'confirm_' . $fieldName ] = 2;
			}
			$response['custom_validation']['confirm_' . $fieldName] = $custom['confirm_' . $fieldName];
			$response['valid']                                      = $response['valid'] && ($response['custom_validation']['confirm_' . $fieldName] != 0);
		}

		return $response;
	}

	private function isTruthism($value)
	{
		if ($value === 1) return true;

		if (in_array($value, ['on', 'checked', 'true', '1', 'yes', 1, true], true))
		{
			return true;
		}

		return false;
	}

	private function isExcluded($type, $levelID)
	{
		$paramName = 'excluded_levels_' . $type;
		$excludedLevels = $this->params->get($paramName, '');

		if (empty($excludedLevels))
		{
			return false;
		}

		return in_array($levelID, $excludedLevels);
	}
}
