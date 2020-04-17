<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Model\DataModel\Collection as DataCollection;

/**
 * Common utility code for all the plugins
 *
 * @package     Akeeba\Subscriptions\Admin\Helper
 *
 * @since       version
 */
final class Plugins
{
	protected static $allLevels;

	public static function getAllLevels(): DataCollection
	{
		if (!is_null(self::$allLevels))
		{
			return self::$allLevels;
		}

		$container       = Container::getInstance('com_akeebasubs');
		$levelsModel     = $container
			->factory
			->model('Levels')
			->tmpInstance();
		self::$allLevels = $levelsModel->get(true);

		return self::$allLevels;
	}
}