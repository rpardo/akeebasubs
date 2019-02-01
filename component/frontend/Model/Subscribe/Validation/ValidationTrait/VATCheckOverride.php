<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation\ValidationTrait;

use Akeeba\Subscriptions\Admin\Helper\EUVATInfo;
use Akeeba\Subscriptions\Site\Model\Users;

defined('_JEXEC') or die;

trait VATCheckOverride
{
	/**
	 * Returns a result telling us if the VAT number is VIES-registered, taking into account the VAT validity check
	 * override feature in the Akeeba Subscriptions Users page.
	 *
	 * The following will take place if the "Is VIES Registered?" option is set to:
	 * - 0 "No, check again": We go through the VIES SOAP service by means of EUVATInfo::isVIESValidVATNumber
	 * - 1 "Yes, check again": We go through the VIES SOAP service by means of EUVATInfo::isVIESValidVATNumber
	 * - 2 "Yes, do NOT check again": We return true, we do NOT check with the SOAP service
	 *
	 * @param   string  $country    Country code
	 * @param   string  $vatNumber  VAT Number
	 *
	 * @return bool
	 */
	public function isVIESRegisteredRespectingOverrides(string $country, string $vatNumber): bool
	{
		// For this to make sense I must be logged in
		if (!$this->jUser->guest)
		{
			// I must have my viesregistered flag set to 2 and my VAT number must match the saved record.
			/** @var Users $subsUsersModel */
			$subsUsersModel = $this->container->factory->model('Users')->tmpInstance();

			$userparams = $subsUsersModel
				->getMergedData($this->jUser->id);

			if (($userparams->viesregistered == 2) && ($userparams->vatnumber == $vatNumber))
			{
				return true;
			}
		}

		// If I'm here I need to check the VAT number against the VIES web service
		return EUVATInfo::isVIESValidVATNumber($country, $vatNumber);
	}
}