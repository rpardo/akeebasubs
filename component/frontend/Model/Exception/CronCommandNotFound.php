<?php
/**
 * @package   ats
 * @copyright Copyright (c)2011-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Exception;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Language\Text;

/**
 * CRON job error: command not found
 *
 * @since  7.1.2
 */
class CronCommandNotFound extends CronException
{
	/**
	 * @inheritDoc
	 */
	public function __construct($message = "", $code = 0, Exception $previous = null)
	{
		if (empty($code))
		{
			$code = 501;
		}

		if (empty($message))
		{
			$message = Text::_('COM_AKEEBASUBS_CRON_ERR_COMMAND_NOT_FOUND');
		}

		parent::__construct($message, $code, $previous);
	}

}