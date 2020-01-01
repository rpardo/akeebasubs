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
$lang->load('mod_akmysubs', JPATH_SITE, 'en-GB', true);
$lang->load('mod_akmysubs', JPATH_SITE, null, true);
$lang->load('com_akeebasubs', JPATH_SITE, 'en-GB', true);
$lang->load('com_akeebasubs', JPATH_SITE, null, true);

if (JFactory::getUser()->guest) return;
?>
<div id="mod-akmysubs-<?php echo $module->id ?>" class="mod-akmysubs">
	<?php
		FOF30\Container\Container::getInstance('com_akeebasubs', [
			'tempInstance' => true,
			'input' => [
				'savestate'        => 0,
				'option'           => 'com_akeebasubs',
				'view'             => 'Subscriptions',
				'layout'           => 'default',
				'limit'            => 0,
				'limitstart'       => 0,
				'paystate'         => 'C',
				'user_id'          => JFactory::getUser()->id,
				'task'             => 'browse',
				'includereturnurl' => 1,
			]
		])->dispatcher->dispatch();
	?>
</div>
