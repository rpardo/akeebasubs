<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\Levels;

class BasePrice extends Base
{
	/**
	 * Get the base price including any price modifiers set by the plugins
	 *
	 * @return  array    levelNet, isRecurring
	 */
	protected function getValidationResult()
	{
		$ret = [
			'levelNet'    => 0.0,
			'isRecurring' => false,
		];

		// Get the subscription level and its base price
		/** @var Levels $level */
		$level = $this->container->factory->model('Levels')->tmpInstance();
		$level->find($this->state->id);

		// Get the default price value
		$ret['isRecurring'] = (bool) $level->recurring;
		$ret['levelNet']    = (float) $level->price;

		return $ret;
	}

}
