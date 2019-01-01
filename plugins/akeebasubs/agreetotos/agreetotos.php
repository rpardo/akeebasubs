<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Container\Container;

defined('_JEXEC') or die();

/**
 * This plugin renders an Agree to Terms of Service field
 */
class plgAkeebasubsAgreetotos extends JPlugin
{
	function onSubscriptionFormPrepaymentRender($userparams, $cache)
	{
		JHtml::_('bootstrap.popover');

		// Load the language
		$lang = JFactory::getLanguage();
		$lang->load('plg_akeebasubs_agreetotos', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('plg_akeebasubs_agreetotos', JPATH_ADMINISTRATOR, null, true);

		// Init the fields array which will be returned
		$fields = array();

		// ----- CONFIRM BEING INFORMED FIELD -----
		// Setup the checkbox parameters
		$url      = $this->params->get('tosurl', '');
		$urlField = JText::_('PLG_AKEEBASUBS_AGREETOTOS_TOS_LABEL');

		if (!empty($url))
		{
			$text     = JText::_('PLG_AKEEBASUBS_AGREETOTOS_TOS_LABEL');
			$urlField = '<a href="javascript:return false;" onclick="window.open(\'' . $url . '\',\'toswindow\',\'width=640,height=480,resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,menubar=no\');">' . $text . '</a>';
		}

		$labelText = JText::sprintf('PLG_AKEEBASUBS_AGREETOTOS_AGREE_LABEL', $urlField);
		$labelText2 = JText::sprintf('PLG_AKEEBASUBS_AGREETOTOS_AGREE_LABEL', JText::_('PLG_AKEEBASUBS_AGREETOTOS_TOS_LABEL'));
		$extraText = JText::sprintf('PLG_AKEEBASUBS_AGREETOTOS_TOS_INFO_LABEL', JText::_('PLG_AKEEBASUBS_AGREETOTOS_TOS_LABEL'));
		$html      = <<<HTML
<label class="checkbox">
	<input type="checkbox" name="custom[agreetotos]" id="agreetotos" />
	<span class="akion-information-circled hasPopover" title="$labelText2" data-content="$extraText"></span>
	$labelText
</label>
HTML;

		// Setup the field
		$field = array(
			'id'          => 'agreetotos',
			'label'       => '* ',
			'elementHTML' => $html,
			'isValid'     => false,
		);
		// Add the field to the return output
		$fields[] = $field;

		// ----- ADD THE JAVASCRIPT -----
		$javascript = <<<JS

(function($) {
	$(document).ready(function(){
		// Tell Akeeba Subscriptions how to fetch the extra field data
		addToValidationFetchQueue(plg_akeebasubs_agreetotos_fetch);
		// Tell Akeeba Subscriptions how to validate the extra field data
		addToValidationQueue(plg_akeebasubs_agreetotos_validate);
		
		// Immediate validation of the field
		if (akeebasubs_apply_validation)
		{
			$('#agreetotos').change(function(e){
				if($('#agreetotos').is(':checked')) {
					$('#agreetotos').parents('div.form-group').removeClass('has-error');
				} else {
					$('#agreetotos').parents('div.form-group').addClass('has-error');
				}
			});
		}
	});
})(akeeba.jQuery);

function plg_akeebasubs_agreetotos_fetch()
{
	var result = {};

	(function($) {
		result.agreetotos = $('#agreetotos').is(':checked') ? 1 : 0;
	})(akeeba.jQuery);

	return result;
}

function plg_akeebasubs_agreetotos_validate(response)
{
    var thisIsValid = true;

	(function($) {
		$('#agreetotos').parents('div.form-group').removeClass('has-error');

		if (!akeebasubs_apply_validation)
		{
			thisIsValid = true;
			return;
		}
		
		if (!response.custom_validation.agreetotos) {
			$('#agreetotos').parents('div.form-group').addClass('has-error');
			thisIsValid = false;
		}
	})(akeeba.jQuery);

	return thisIsValid;
}

JS;
		$container  = Container::getInstance('com_akeebasubs');
		$container->template->addJSInline($javascript);

		// ----- RETURN THE FIELDS -----
		return $fields;
	}

	function onValidate($data)
	{
		$response = array(
			'isValid'           => true,
			'custom_validation' => array(),
		);

		$custom = $data->custom;

		if (!array_key_exists('agreetotos', $custom))
		{
			$custom['agreetotos'] = 0;
		}

		$custom['agreetotos']                        = $this->isTruthism($custom['agreetotos']) ? 1 : 0;
		$response['custom_validation']['agreetotos'] = $custom['agreetotos'];
		$response['custom_validation']['agreetotos'] = ($custom['agreetotos'] != 0) ? 1 : 0;
		$response['valid']                           = $response['custom_validation']['agreetotos'] ? true : false;

		return $response;
	}

	private function isTruthism($value)
	{
		if ($value === 1)
		{
			return true;
		}

		if (in_array($value, ['on', 'checked', 'true', '1', 'yes', 1, true], true))
		{
			return true;
		}

		return false;
	}
}
