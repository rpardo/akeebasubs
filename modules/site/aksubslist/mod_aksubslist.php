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
$lang->load('mod_aksubslist', JPATH_SITE, 'en-GB', true);
$lang->load('mod_aksubslist', JPATH_SITE, null, true);
$lang->load('com_akeebasubs', JPATH_SITE, 'en-GB', true);
$lang->load('com_akeebasubs', JPATH_SITE, null, true);

?>
<div id="mod-aksubslist-<?php echo $module->id ?>" class="mod-aksubslist">
	<?php if (JFactory::getUser()->guest): ?>
		<span class="akeebasubs-subscriptions-itemized-nosubs">
		<?php echo JText::_('COM_AKEEBASUBS_LEVELS_ITEMIZED_NOSUBS') ?>
	</span>
	<?php else:
		FOF30\Container\Container::getInstance('com_akeebasubs', [
			'tempInstance' => true,
			'input' => [
				'savestate'  => 0,
				'option'     => 'com_akeebasubs',
				'view'       => 'Subscriptions',
				'layout'     => 'itemized',
				'limit'      => 0,
				'limitstart' => 0,
				'paystate'   => 'C',
				'user_id'    => JFactory::getUser()->id,
				'task'       => 'browse'
			]
		])->dispatcher->dispatch();

	endif; ?>
</div>
