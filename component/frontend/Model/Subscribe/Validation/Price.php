<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

class Price extends Base
{
	/**
	 * Return the pricing information.
	 *
	 * Uses
	 *        BasePrice
	 *        CouponDiscount
	 *        BestAutomaticDiscount
	 *        PersonalInformation
	 *
	 * @return  array
	 */
	protected function getValidationResult()
	{
		$basePriceStructure = $this->factory->getValidator('BasePrice')->execute();
		$netPrice           = $basePriceStructure['levelNet'];

		$couponStructure = $this->factory->getValidator('CouponDiscount')->execute();
		$couponDiscount  = $couponStructure['value'];

		// Automatic discount (upgrade rules, subscription level relations) validation
		$discountStructure = $this->factory->getValidator('BestAutomaticDiscount')->execute();
		$autoDiscount      = $discountStructure['discount'] ?? 0.00;

		// Should I use the coupon code or the automatic discount?
		$useCoupon = false;
		$useAuto   = true;
		$couponid  = $couponStructure['coupon_id'] ?? 0;
		$upgradeid = $discountStructure['upgrade_id'] ?? 0;

		if ($couponStructure['valid'])
		{
			if ($autoDiscount < $couponDiscount)
			{
				$useAuto   = false;
				$useCoupon = true;
				$upgradeid = 0;
			}
			else
			{
				$couponid = 0;
			}
		}

		$discount = $useCoupon ? $couponDiscount : $autoDiscount;

		if ($discount < 0.001)
		{
			$useCoupon = false;
			$useAuto   = false;
		}

		// Note: do not reset the oldsup and expiration fields. Subscription level relations must not be bound
		// to the discount.

		// Calculate the base price minimising rounding errors
		$basePrice = 0.01 * (100 * $netPrice - 100 * $discount);

		if ($basePrice < 0.01)
		{
			$basePrice = 0;
		}

		// Calculate the gross amount minimising rounding errors
		$grossAmount = $basePrice;

		$result = [
			'net'        => sprintf('%1.02F', round($netPrice, 2)),
			'realnet'    => sprintf('%1.02F', round($basePriceStructure['levelNet'], 2)),
			'discount'   => sprintf('%1.02F', round($discount, 2)),
			'taxrate'    => sprintf('%1.02F', 0.00),
			'tax'        => sprintf('%1.02F', 0.00),
			'gross'      => sprintf('%1.02F', round($grossAmount, 2)),
			'usecoupon'  => $useCoupon ? 1 : 0,
			'useauto'    => $useAuto ? 1 : 0,
			'couponid'   => $couponid,
			'upgradeid'  => $upgradeid,
			'oldsub'     => $discountStructure['oldsub'],
			'allsubs'    => $discountStructure['allsubs'],
			'expiration' => $discountStructure['expiration'],
		];

		return $result;
	}
}
