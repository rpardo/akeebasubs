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
			'address1'      => !empty($state->address1),
			'country'       => !empty($state->country),
			'city'          => !empty($state->city),
			'zip'           => !empty($state->zip),
			'businessname'  => !empty($state->businessname),
			'occupation'    => !empty($state->occupation),
			'coupon'        => !empty($state->coupon),
		);

		$ret['rawDataForDebug'] = (array)$state;

		// Name validation
		$ret['name'] = $this->factory->getValidator('Name')->execute();

		// Email validation
		$ret['email'] = $this->factory->getValidator('Email')->execute();

		// 2. Country validation
		$ret['country'] = $this->factory->getValidator('Country')->execute();

		// 3. Business validation
		$businessValidation   = $this->factory->getValidator('Business')->execute();
		$ret['businessname']  = $businessValidation['businessname'];
		$ret['occupation']    = $businessValidation['occupation'];

		// 4. Coupon validation
		$couponValidation = $this->factory->getValidator('Coupon')->execute();
		$ret['coupon']    = $couponValidation['valid'];

		return $ret;
	}
}
