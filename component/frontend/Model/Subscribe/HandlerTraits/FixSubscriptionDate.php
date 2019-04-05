<?php
/**
 *  @package   AkeebaSubs
 *  @copyright Copyright (c)2010-$toda.year Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits;


use Akeeba\Subscriptions\Admin\Model\Levels;
use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use Exception;
use FOF30\Date\Date;

/**
 * Helps with handling start and end dates of subscriptions
 *
 * @since   7.0.0
 */
trait FixSubscriptionDate
{
	/**
	 * Fixes the starting and end dates when a payment is accepted after the subscription's start date. This works
	 * around the case where someone pays by e-Check on January 1st and the check is cleared on January 5th. He'd
	 * lose those 4 days without this trick. Or, worse, if it was a one-day pass the user would have paid us and we'd
	 * never given him a subscription!
	 *
	 * @param   Subscriptions  $subscription  The subscription record
	 * @param   array          $updates       Updates to be applied to $subscription
	 *
	 * @return  array  Updates to be applied to $subscription (may be modified compared to input)
	 *
	 * @since   7.0.0
	 */
	public function fixSubscriptionDates(Subscriptions $subscription, array $updates): array
	{
		/**
		 * Take into account the params->fixdates data to determine when the new subscription should start and/or expire
		 * the old subscription.
		 */
		$subcustom = (!empty($updates['params']) ? $updates['params'] : $subscription->params);

		if (is_string($subcustom))
		{
			$subcustom = json_decode($subcustom, true);
		}
		elseif (is_object($subcustom))
		{
			$subcustom = (array) $subcustom;
		}

		$oldsub     = null;
		$expiration = 'overlap';
		$allsubs    = array();
		$noContact  = array();

		if (isset($subcustom['fixdates']))
		{
			$oldsub     = isset($subcustom['fixdates']['oldsub']) ? $subcustom['fixdates']['oldsub'] : null;
			$expiration = isset($subcustom['fixdates']['expiration']) ? $subcustom['fixdates']['expiration'] : 'overlap';
			$allsubs    = isset($subcustom['fixdates']['allsubs']) ? $subcustom['fixdates']['allsubs'] : array();
			$noContact  = isset($subcustom['fixdates']['nocontact']) ? $subcustom['fixdates']['nocontact'] : array();

			unset($subcustom['fixdates']);
		}

		// Mark all subscriptions being renewed by this subscription as "no contact" (contact_flag is set to 3)
		if (!empty($noContact))
		{
			foreach ($noContact as $subId)
			{
				/** @var Subscriptions $row */
				$row = $subscription->getContainer()->factory->model('Subscriptions')->tmpInstance();

				try
				{
					$row->findOrFail($subId)->save(['contact_flag' => 3]);
				}
				catch (Exception $e)
				{
					// Failure *is* an option.
				}
			}
		}

		if (is_numeric($oldsub))
		{
			/** @var Subscriptions $sub */
			$sub = $subscription
				->getClone()
				->savestate(0)
				->setIgnoreRequest(true)
				->reset(true, true)
				->load($oldsub, true);

			if ($sub->akeebasubs_subscription_id == $oldsub)
			{
				$oldsub = $sub;
			}
			else
			{
				$oldsub     = null;
				$expiration = 'overlap';
			}
		}
		else
		{
			$oldsub     = null;
			$expiration = 'overlap';
		}

		/**
		 * Fix the starting date if the payment was accepted after the subscription's start date. This works around the
		 * case where someone pays by e-Check on January 1st and the check is cleared on January 5th. He'd lose those 4
		 * days without this trick. Or, worse, if it was a one-day pass the user would have paid us and we'd never given
		 * him a subscription!
		 */
		$regex = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';

		if (!preg_match($regex, $subscription->publish_up))
		{
			$subscription->publish_up = '2001-01-01';
		}

		if (!preg_match($regex, $subscription->publish_down))
		{
			$subscription->publish_down = '2038-01-01';
		}

		$jNow   = new Date();
		$jStart = new Date($subscription->publish_up);
		$jEnd   = new Date($subscription->publish_down);
		$now    = $jNow->toUnix();
		$start  = $jStart->toUnix();
		$end    = $jEnd->toUnix();

		if (is_null($oldsub))
		{
			$oldSubExpirationTimestamp = $now;
		}
		else
		{
			if (!preg_match($regex, $oldsub->publish_down))
			{
				$oldSubExpirationTimestamp = $now;
			}
			else
			{
				$jOldSubExpiration         = new Date($oldsub->publish_down);
				$oldSubExpirationTimestamp = $jOldSubExpiration->toUnix();
			}
		}

		if ($start <= $now)
		{
			if ($end >= 2145916800)
			{
				// End date after 2038-01-01; forever subscription
				$start = $now;
			}
			else
			{
				// Get the subscription level and determine if this is a Fixed Expiration subscription.
				$nullDate = $this->container->db->getNullDate();

				/** @var Levels $level */
				if ($subscription->level instanceof Levels)
				{
					$level = $subscription->level;
				}
				else
				{
					/** @var Levels $level */
					$level = $this->container->factory->model('Levels')->tmpInstance();
					$level->find($subscription->akeebasubs_level_id);
				}

				$fixed_date = $level->fixed_date;

				if (!is_null($fixed_date) && !($fixed_date == $nullDate))
				{
					// Is the fixed date in the future?
					$jFixedDate = new Date($fixed_date);

					if ($now > $jFixedDate->toUnix())
					{
						// If the fixed date is in the past handle it as a regular subscription
						$fixed_date = null;
					}

					if (!empty($fixed_date))
					{
						$start = $now;
						$end   = $jFixedDate->toUnix();
					}
				}

				if (is_null($fixed_date) || ($fixed_date == $nullDate))
				{
					// Regular subscription
					$duration = $end - $start;

					// Assume expiration != after => start date = now
					$start = $now;

					// But if expiration = after => start date = end date of old sub
					if ($expiration == 'after')
					{
						// Make sure the activation date is never in the past
						$start = max($now, $oldSubExpirationTimestamp);
					}

					$end = $start + $duration;
				}
			}

			$jStart = new Date($start);
			$jEnd   = new Date($end);
		}

		// Expiration = replace => expire old subscription
		if ($expiration == 'replace')
		{
			// Disable the primary subscription used to determine the subscription date
			$newdata = array(
				'publish_down' => $jNow->toSql(),
				'enabled'      => 0,
				'contact_flag' => 3,
				'notes'        => $oldsub->notes . "\n\n" . "SYSTEM MESSAGE: This subscription was upgraded and replaced with " . $oldsub->getId() . "\n"
			);

			$oldsub->save($newdata);

			// Disable all old subscriptions
			if (!empty($allsubs))
			{
				foreach ($allsubs as $sub_id)
				{
					/** @var Subscriptions $subToBeDisabled */
					$subToBeDisabled = $subscription->getClone()->savestate(false)->reset(true, true);
					$subToBeDisabled->find($sub_id);

					if ($subToBeDisabled->akeebasubs_subscription_id == $oldsub->akeebasubs_subscription_id)
					{
						// Don't try to disable the same subscription twice
						continue;
					}

					$data = $subToBeDisabled->getData();

					$newdata = array_merge($data, array(
						'publish_down' => $jNow->toSql(),
						'enabled'      => 0,
						'contact_flag' => 3,
						'notes'        => $oldsub->notes . "\n\n" . "SYSTEM MESSAGE: This subscription was upgraded and replaced with " . $subToBeDisabled->getId() . "\n"
					));

					$subToBeDisabled->save($newdata);
				}
			}
		}

		$updates['publish_up']   = $jStart->toSql();
		$updates['publish_down'] = $jEnd->toSql();
		$updates['enabled']      = 1;
		$updates['params']       = $subcustom;

		return $updates;
	}
}