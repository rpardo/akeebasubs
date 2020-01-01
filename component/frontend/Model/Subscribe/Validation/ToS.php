<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\JoomlaUsers;

class ToS extends Base
{
	/**
	 * Validate the Email field
	 *
	 * @return  bool
	 */
	protected function getValidationResult()
	{
		$tos = trim($this->state->accept_terms);

		if (!$tos)
		{
			return false;
		}

		return true;
	}

}
