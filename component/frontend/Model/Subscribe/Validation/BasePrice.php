<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Validation;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\Subscriptions;

class BasePrice extends Base
{
	/**
	 * Get the base price including any price modifiers set by the plugins
	 *
	 * @return  array    basePrice, net, isRecurring
	 */
	protected function getValidationResult()
	{
		$ret = [
			'levelNet'    => 0.0,
			'basePrice'   => 0.0, // Base price, including surcharges
			'isRecurring' => false
		];

		// Get the subscription level and its base price
		/** @var Levels $level */
		$level = $this->container->factory->model('Levels')->tmpInstance();
		$level->find($this->state->id);
		$ret['levelNet'] = (float)$level->price;

		// Get the default price value
		$basePrice = (float)$level->price;

		$ret['isRecurring'] = (bool) $level->recurring;

		// Net price modifiers (via plugins)
		$price_modifier = 0;

		$this->container->platform->importPlugin('akeebasubs');

		$priceValidationData = array_merge(
			(array)$this->state, array(
				'level'    => $level,
				'netprice' => $basePrice
			)
		);

		$jResponse = $this->container->platform->runPlugins('onValidateSubscriptionPrice', array(
				(object)$priceValidationData)
		);

		if (is_array($jResponse) && !empty($jResponse))
		{
			foreach ($jResponse as $pluginResponse)
			{
				if (empty($pluginResponse))
				{
					continue;
				}

				$price_modifier += $pluginResponse;
			}
		}

		$basePrice += $price_modifier;

		$ret['basePrice'] = $basePrice;

		return $ret;
	}

}
