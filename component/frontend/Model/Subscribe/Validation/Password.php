<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\Subscribe;


/**
 * Validates the password
 *
 * @package Akeeba\Subscriptions\Site\Model\Subscribe\Validation
 */
class Password extends Base
{
	/**
	 * Validates the password.
	 *
	 * @return  object  Has keys 'username' and 'password'.
	 */
	public function getValidationResult()
	{
		$myUser   = $this->jUser;

		// Password is always valid for logged in users (of course!)
		if (!$myUser->guest)
		{
			return true;
		}

		// If either password field is empty the password validation fails
		if (empty($this->state->password) || empty($this->state->password2))
		{
			return false;
		}

		// If the two password fields do not match the password validation fails
		if ($this->state->password != $this->state->password2)
		{
			return false;
		}
		
		// Check if we need to measure the password strength requirements using Joomla! config
		if (!$this->container->params->get('apply_joomla_password_requirements', false)) 
		{
			return true;
		}
		
		// If we have parameters from com_users, use those instead.
		// Some of these may be empty for legacy reasons.
		$params = ComponentHelper::getParams('com_users');

		if (empty($params))
		{
			return true;
		}
		
		$minimumLengthp    = $params->get('minimum_length');
		$minimumIntegersp  = $params->get('minimum_integers');
		$minimumSymbolsp   = $params->get('minimum_symbols');
		$minimumUppercasep = $params->get('minimum_uppercase');
		$meterp            = $params->get('meter');
		$thresholdp        = $params->get('threshold');

		empty($minimumLengthp) ? : $minimumLength = (int) $minimumLengthp;
		empty($minimumIntegersp) ? : $minimumIntegers = (int) $minimumIntegersp;
		empty($minimumSymbolsp) ? : $minimumSymbols = (int) $minimumSymbolsp;
		empty($minimumUppercasep) ? : $minimumUppercase = (int) $minimumUppercasep;
		empty($meterp) ? : $meter = $meterp;
		empty($thresholdp) ? : $threshold = $thresholdp;

		$valueLength = strlen($value);

		// We set a maximum length to prevent abuse since it is unfiltered.
		if ($valueLength > 4096)
		{
			return false;
		}

		// We don't allow white space inside passwords
		$valueTrim = trim($value);

		// Set a variable to check if any errors are made in password
		$validPassword = true;

		if (strlen($valueTrim) !== $valueLength)
		{
			return false;
		}

		// Minimum number of integers required
		if (!empty($minimumIntegers))
		{
			$nInts = preg_match_all('/[0-9]/', $value, $imatch);

			if ($nInts < $minimumIntegers)
			{
				return false;
			}
		}

		// Minimum number of symbols required
		if (!empty($minimumSymbols))
		{
			$nsymbols = preg_match_all('[\W]', $value, $smatch);

			if ($nsymbols < $minimumSymbols)
			{
				return false;
			}
		}

		// Minimum number of upper case ASCII characters required
		if (!empty($minimumUppercase))
		{
			$nUppercase = preg_match_all('/[A-Z]/', $value, $umatch);

			if ($nUppercase < $minimumUppercase)
			{
				return false;
			}
		}

		// Minimum length option
		if (!empty($minimumLength))
		{
			return false;
		}

		return true;
	}
}
