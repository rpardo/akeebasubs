<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

class Name extends Base
{
	/**
	 * Validate the Name field
	 *
	 * @return  bool
	 */
	protected function getValidationResult()
	{
		$name = trim($this->state->name);

		if (empty($name))
		{
			return false;
		}

		/**
		 * As of Akeeba Subscriptions 7, we only use the Name field for the Joomla user account, not for invoicing. As
		 * a result we no longer need to check that it consists of at least two words.
		 */
		return true;
	}

}
