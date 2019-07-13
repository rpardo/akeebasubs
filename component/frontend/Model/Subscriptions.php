<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model;

use Akeeba\Subscriptions\Admin\Model\Levels;

defined('_JEXEC') or die;

/**
 * This model extends the back-end model, pulling it into the frontend without duplicating the code
 *
 * @property-read  \Akeeba\Subscriptions\Site\Model\Levels $level        The subscription level.
 * @property-read  bool                                    $recurring    Is this a recurring subscription?
 * @property-read  string                                  $status       Subscription status
 */
class Subscriptions extends \Akeeba\Subscriptions\Admin\Model\Subscriptions
{
	public function __get($name)
	{
		switch ($name)
		{
			case 'recurring':
				return $this->isRecurring();

				break;

			case 'status':
				return $this->getStatus();

				break;
		}

		return parent::__get($name);
	}

	/**
	 * Is this a recurring payment subscription?
	 *
	 * @return bool
	 *
	 * @since  7.0.0
	 */
	public function isRecurring()
	{
		return !empty($this->cancel_url) && !empty($this->update_url);
	}

	/**
	 * Returns the status of the subscription record. The possible values are:
	 * new          Not yet paid
	 * pending      Paid, but currently being processed
	 * canceled     Canceled payment
	 * active       Currently active subscription
	 * waiting      Purchased renewal, upgrade, or downgrade which will become active in the future
	 * expired      Expired subscription
	 *
	 * @return string One of 'new', 'pending', 'canceled', 'active', 'waiting' or 'expired'.
	 *
	 * @since 7.0.0
	 */
	public function getStatus()
	{
		$payState = $this->getFieldValue('state');

		if ($payState == 'N')
		{
			return 'new';
		}

		if ($payState == 'P')
		{
			return 'pending';
		}

		if ($payState == 'X')
		{
			return 'canceled';
		}

		if ($this->enabled)
		{
			return 'active';
		}

		if ($this->container->platform->getDate($this->publish_up)->getTimestamp() >= time())
		{
			return 'waiting';
		}

		return 'expired';
	}
}
