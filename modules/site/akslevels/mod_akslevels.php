<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// no direct access
defined('_JEXEC') or die;

if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	throw new RuntimeException('FOF 3.0 is not installed', 500);
}

// Load the language files
$lang = JFactory::getLanguage();
$lang->load('mod_akslevels', JPATH_SITE, 'en-GB', true);
$lang->load('mod_akslevels', JPATH_SITE, null, true);
$lang->load('com_akeebasubs', JPATH_SITE, 'en-GB', true);
$lang->load('com_akeebasubs', JPATH_SITE, null, true);

$layout = $params->get('layout', 'awesome');
$ids    = $params->get('ids', array());

$config = [
	'tempInstance' => true,
	'input'        => [
		'option'           => 'com_akeebasubs',
		'view'             => 'levels',
		'layout'           => $layout,
		'savestate'        => 0,
		'limit'            => 0,
		'limitstart'       => 0,
		'no_clear'         => true,
		'only_once'        => true,
		'task'             => 'browse',
		'filter_order'     => 'ordering',
		'filter_order_Dir' => 'ASC',
		'enabled'          => 1,
		'caching'          => false,
		'shownotices'	   => false,
	]

];

if (!empty($ids))
{
	$config['input']['id'] = $ids;
}

$container = FOF30\Container\Container::getInstance('com_akeebasubs', $config);

$container->dispatcher->dispatch();
