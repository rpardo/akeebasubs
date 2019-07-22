<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// no direct access
use Akeeba\Subscriptions\Admin\Helper\Format;
use FOF30\Date\Date;

defined('_JEXEC') or die;

if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
{
	throw new RuntimeException('FOF 3.0 is not installed', 500);
}

// Get the Akeeba Subscriptions container. Also includes the autoloader.
$container = FOF30\Container\Container::getInstance('com_akeebasubs');

// Load the language files
$lang = JFactory::getLanguage();
$lang->load('mod_aksexpires', JPATH_SITE, 'en-GB', true);
$lang->load('mod_aksexpires', JPATH_SITE, null, true);
$lang->load('com_akeebasubs', JPATH_SITE, 'en-GB', true);
$lang->load('com_akeebasubs', JPATH_SITE, null, true);

if (JFactory::getUser()->guest)
{
	echo '&nbsp;';
}
else
{
	/** @var \Akeeba\Subscriptions\Site\Model\Subscriptions $subsModel */
	$subsModel = $container->factory->model('Subscriptions')->tmpInstance();
	$list = $subsModel
		->user_id(JFactory::getUser()->id)
		->enabled(1)
		->get(true);

	if (!$list->count())
	{
		echo "&nbsp;";

		return;
	}

	$expires = 0;
	$regex   = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';

	/** @var \Akeeba\Subscriptions\Site\Model\Subscriptions $s */
	foreach ($list as $s)
	{
		if (!preg_match($regex, $s->publish_down))
		{
			$s->publish_down = '2037-01-01';
		}

		$ed = new Date($s->publish_down);
		$ex = $ed->toUnix();

		if ($ex > $expires)
		{
			$expires = $ex;
		}
	}

	$ed = new Date($expires);
	echo JText::sprintf('MOD_AKSEXPIRES_EXPIRESON', Format::date($ed, JText::_('DATE_FORMAT_LC1') , ' Z', true));
}
