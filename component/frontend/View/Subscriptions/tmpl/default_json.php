<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Subscriptions\Json $this */

$result = [];

/** @var \Akeeba\Subscriptions\Site\Model\Subscriptions $item */
foreach ($this->items as $item)
{
	// Convert the record to an array
	$array = $item->toArray();

	// Convert price fields to currency representation (floating point with exactly two decimal places)
	$convertFields = [
		'net_amount',
		'tax_amount',
		'gross_amount',
		'tax_percent',
		'prediscount_amount',
		'discount_amount',
	];

	foreach ($convertFields as $f)
	{
		$v         = $array[$f] ?? 0.00;
		$array[$f] = sprintf('%0.2f', floatval($v));
	}

	$array['_userInfo'] = [
		'name' => $item->juser->name,
		'username' => $item->juser->username,
		'v' => $item->juser->username,
	];

	$array['_levelInfo'] = [
		'title' => $item->level->title,
		'slug'  => $item->level->slug,
		'image' => $item->level->image,
	];

	$array['_couponInfo'] = is_null($item->coupon) ? [] : [
		'coupon' => $item->coupon->coupon,
		'title'  => $item->coupon->title,
		'type'   => $item->coupon->type,
		'value'  => $item->coupon->value,
	];

	$array['_upgradeTitle'] = is_null($item->upgrade) ? '' : $item->upgrade->title;

	// Add the converted record to the result set
	$result[] = $array;
}

// Output the JSON
echo json_encode($result, JSON_PRETTY_PRINT);