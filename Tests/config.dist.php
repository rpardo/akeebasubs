<?php
/**
 * @package    AkeebaSubs
 * @subpackage Tests
 * @copyright  Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    GNU General Public License version 3, or later
 */

/*
 * Configuration for unit tests
 *
 * YOU ARE SUPPOSED TO USE A DEDICATED SITE FOR THESE TESTS, SINCE WE'LL COMPLETELY OVERWRITE EXISTING
 * DATABASE DATA WITH WHAT IS NEEDED FOR THE TEST!
 */

$akeebasubsTestConfig = [
	'site_root' => '/var/www/guineapig',
	'site_name' => 'Akeeba Subscriptions Unit Tests',
	'site_url'  => 'http://localhost/guineapig/',
	// Only used for determining the recurring price
	'products'  => [
		'single_product_a' => [
			'paddle_product_id'  => '12345',
			'paddle_secret'      => 'abcdef',
			'paddle_plan_id'     => '12345',
			'paddle_plan_secret' => 'abcdef',
		],
		'single_product_b' => [
			'paddle_product_id'  => '12345',
			'paddle_secret'      => 'abcdef',
			'paddle_plan_id'     => '12345',
			'paddle_plan_secret' => 'abcdef',
		],
		'bundle'           => [
			'paddle_product_id'  => '12345',
			'paddle_secret'      => 'abcdef',
			'paddle_plan_id'     => '12345',
			'paddle_plan_secret' => 'abcdef',
		],
	],
];
