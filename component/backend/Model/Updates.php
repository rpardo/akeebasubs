<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Update\Update;

class Updates extends Update
{
	/**
	 * Public constructor. Initialises the protected members as well.
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$config['update_component'] = 'pkg_akeebasubs';
		$config['update_sitename']  = 'Akeeba Subscriptions';
		$config['update_site']      = 'https://raw.githubusercontent.com/akeeba/akeebasubs/main/update/pkg_akeebasubs_updates.xml';

		if (defined('AKEEBASUBS_VERSION') && !in_array(substr(AKEEBASUBS_VERSION, 0, 3), ['dev', 'rev']))
		{
			$config['update_version'] = AKEEBASUBS_VERSION;
		}

		parent::__construct($config);
	}

}
