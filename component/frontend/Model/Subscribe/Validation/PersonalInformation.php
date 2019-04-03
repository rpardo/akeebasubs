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
		$ret = [
			'name'   => !empty($state->name),
			'email'  => !empty($state->email),
			'email2' => !empty($state->email2) && ($state->email == $state->email2),
		];

		$ret['rawDataForDebug'] = (array) $state;

		// Name validation
		$ret['name'] = $this->factory->getValidator('Name')->execute();

		// Email validation
		$ret['email'] = $this->factory->getValidator('Email')->execute();

		// Coupon validation
		$couponValidation = $this->factory->getValidator('Coupon')->execute();
		$ret['coupon']    = $couponValidation['valid'];

		// ToS validation
		$tosValidation = $this->factory->getValidator('ToS')->execute();
		$ret['tos']    = $tosValidation;

		return $ret;
	}
}
