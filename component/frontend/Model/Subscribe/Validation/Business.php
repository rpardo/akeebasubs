<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

class Business extends Base
{
	/**
	 * Validate the business fields: business name, activity, VAT number
	 *
	 * @return  array
	 */
	protected function getValidationResult()
	{
		$ret = [
			'businessname'  => false,
			'occupation'    => false,
		];

		// Get some state data
		$businessName     = trim($this->state->businessname);
		$businessActivity = trim($this->state->occupation);
		$isBusiness       = (bool) $this->state->isbusiness;

		// If this is not a business registration we have to return.
		if (!$isBusiness)
		{
			$ret['businessname'] = true;
			$ret['occupation']   = true;

			return $ret;
		}

		// Otherwise make sure the business name and activity are not empty
		$ret['businessname'] = !empty($businessName);
		$ret['occupation']   = !empty($businessActivity);

		return $ret;
	}
}
