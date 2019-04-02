<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

class PersonalInformation extends Base
{
	/**
	 * Get the validation results for all personal information.
	 *
	 * Uses:
	 * 		Name
	 * 		Email
	 * 		Country
	 * 		State
	 * 		Business
	 * 		Coupon
	 *
	 * @return  bool
	 */
	protected function getValidationResult()
	{
		$state = $this->state;

		// 1. Basic checks
		$ret = array(
			'name'          => !empty($state->name),
			'email'         => !empty($state->email),
			'email2'        => !empty($state->email2) && ($state->email == $state->email2),
			'country'       => !empty($state->country),
		);

		$ret['rawDataForDebug'] = (array)$state;

		// Name validation
		$ret['name'] = $this->factory->getValidator('Name')->execute();

		// Email validation
		$ret['email'] = $this->factory->getValidator('Email')->execute();

		// 2. Country validation
		$ret['country'] = $this->factory->getValidator('Country')->execute();

		// 3. Coupon validation
		$couponValidation = $this->factory->getValidator('Coupon')->execute();
		$ret['coupon']    = $couponValidation['valid'];

		return $ret;
	}
}
