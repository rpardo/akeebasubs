<?php
/**
 * @package   ats
 * @copyright Copyright (c)2011-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Exception;

defined('_JEXEC') or die;

use RuntimeException;

/**
 * Generic RuntimeException for the CRON jobs
 *
 * @since  7.1.2
 */
class CronException extends RuntimeException
{
}