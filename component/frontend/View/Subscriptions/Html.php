<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\View\Subscriptions;

use Akeeba\Subscriptions\Site\Model\Invoices;
use Akeeba\Subscriptions\Site\Model\Levels;
use Akeeba\Subscriptions\Site\Model\MySubs;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use FOF30\Factory\Exception\ModelNotFound;

defined('_JEXEC') or die;

class Html extends \FOF30\View\DataView\Html
{
	public $returnURL = '';

	public $invoices = [];

	public $sortTable = [];

	public $renewalsSorting = [
		// Each sub-array is subscription level ID => array of subscription level IDs.
		'renewals'   => [],
		'upgrades'   => [],
		'downgrades' => [],
	];

	private $recurringSubsPerLevel = [];

	public $displayInformation = [];

	protected function onBeforeBrowse()
	{
		// Eager loading of relations
		/** @var Subscriptions $model */
		$model = $this->getModel();
		$model->with(['level', 'invoice']);

		parent::onBeforeBrowse();

		// Get the information on active recurring subscriptions per subscription level
		$this->initRecurringPerLevel();

		// Get subscription and subscription level IDs, sort subscriptions based on their status
		$this->sortSubscriptions();

		// Sort the renewals by type: renewals, upgrades, downgrades
		$this->sortRenewals();

		// Assemble the information we need to display subscriptions in the frontend
		/**
		 * Level object
		 * Status: new, pending, waiting, active, expired, canceled
		 * Latest enabled subscription
		 * Recurring:
		 *      Is it recurring?
		 *      Update URL
		 *      Cancel URL
		 *      Can I upgrade to recurring?
		 *          The level needs recurring info and upsell set to always or renewal
		 *          We also need to have NO upgrades or downgrades already purchased
		 *          IMPORTANT!! shows button ONLY IF "show renew" below is also true!
		 * Upgrade/Downgrade status
		 *      Status: n/a, upgraded, downgraded
		 *      Replacement subscription: subscription object
		 * Button options
		 *      Show "Purchase again"?
		 *          If it's forever: no!
		 *          If it's fixed date AND level.fixed_date != sub.publish_down for any waiting or active sub:
		 *              If upgrade.status != n/a: no!
		 *              Otherwise: yes!
		 *          If overall.status is not expired or canceled:
		 *              no!
		 *          If it's fixed date:
		 *              If upgrade.status != n/a: no!
		 *              Otherwise, yes, you can
		 *
		 *      Show "Renew"?
		 *          If upgrade.status != n/a:
		 *              no!
		 *          If overall.status is pending, waiting, active:
		 *              no!
		 *          If it's forever or fixed_date:
		 *              no! (the fixed_date exceptions are handled as "purchase again" above)
		 *          Otherwise: yes!
		 *
		 *      Show Upgrade button (foreach upgrade level "levelup")?
		 * Billing history: All subscriptions per level ordered chronologically by expiration date with metadata:
		 *      Sub object
		 *      Is it new, active, waiting, pending, expired or canceled
		 *      Legacy invoices
		 */

		if (empty($this->items))
		{
			return;
		}

		/** @var MySubs $mySubsModel */
		$mySubsModel = $this->container->factory->model('MySubs', [
			'items' => $this->items,
			'user'  => $this->container->platform->getUser(),
		])->tmpInstance();

		$this->displayInformation = $mySubsModel->getDisplayData();
	}

	/**
	 * Initializes the active recurring subscriptions per subscription level.
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	private function initRecurringPerLevel(): void
	{
		if (empty($this->items))
		{
			return;
		}

		/** @var Subscriptions $sub */
		foreach ($this->items as $sub)
		{
			// I only care about enabled subscriptions. Expired / replaced / canceled / new / pending don't count.
			if (!$sub->enabled)
			{
				continue;
			}

			// Recurring subscriptions have an update and cancel URL
			if (empty($sub->cancel_url) || empty($sub->update_url))
			{
				continue;
			}

			if (!isset($this->recurringSubsPerLevel[$sub->akeebasubs_level_id]))
			{
				$this->recurringSubsPerLevel[$sub->akeebasubs_level_id] = [];
			}

			$this->recurringSubsPerLevel[$sub->akeebasubs_level_id][] = $sub->getId();
		}
	}

	/**
	 * Are there any *active* recurring subscriptions on the same level as $sub?
	 *
	 * @param Subscriptions $sub
	 *
	 * @return  bool
	 *
	 * @since   7.0.0
	 */
	public function hasOtherRecurringInLevel(Subscriptions $sub): bool
	{
		if (!isset($this->recurringSubsPerLevel[$sub->akeebasubs_level_id]))
		{
			return false;
		}

		if (empty($this->recurringSubsPerLevel[$sub->akeebasubs_level_id]))
		{
			return false;
		}

		foreach ($this->recurringSubsPerLevel[$sub->akeebasubs_level_id] as $id)
		{
			if ($id != $sub->getId())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Is there another active, new or pending subscription on the same level as $sub?
	 *
	 * @param Subscriptions $sub The subscription record to check
	 *
	 * @return  bool
	 */
	public function hasOtherActiveNewOrPendingInLevel(Subscriptions $sub): bool
	{
		$myId = $sub->getId();

		foreach (['new', 'active', 'waiting', 'pending'] as $area)
		{
			foreach ($this->sortTable[$area] as $subId)
			{
				/** @var Subscriptions $otherSub */
				$otherSub = $this->items[$subId];

				if ($otherSub->getId() == $myId)
				{
					continue;
				}

				if ($otherSub->akeebasubs_level_id != $sub->akeebasubs_level_id)
				{
					continue;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Sorts the user's subscriptions by their status
	 */
	private function sortSubscriptions(): void
	{
		$this->subIDs    = [];
		$this->sortTable = [
			'new'      => [],
			'active'   => [],
			'waiting'  => [],
			'pending'  => [],
			'expired'  => [],
			'canceled' => [],
		];

		$this->subIDs = $this->items->modelKeys();
		$this->items->each(function (Subscriptions $sub) {
			$status = $sub->status;

			// Filter out new subscriptions without a payment_url; we can't do anything about them :)
			if (($status == 'new') && empty($sub->payment_url))
			{
				return;
			}

			$this->sortTable[$status][] = $sub->getId();
		});


	}

	/**
	 * Go through the active subscriptions and find which renewal subscriptions are renewals, upgrades or downgrades
	 *
	 * @since 7.0.0
	 */
	private function sortRenewals()
	{
		(clone $this->items)
			// Get the active subscriptions
			->filter(function (Subscriptions $sub) {
				return $sub->status == 'active';
			})
			// Get the subscription's renewals, upgrades and downgrades
			->each(function (Subscriptions $activeSub) {
				$this->renewalsSorting['renewals'][$activeSub->getId()] =
					$this->getLastRenewalSubInLevels([$activeSub->akeebasubs_level_id]);

				$this->renewalsSorting['upgrades'][$activeSub->getId()] =
					$this->getLastRenewalSubInLevels(
						$activeSub->level->upgrades->modelKeys()
					);

				$this->renewalsSorting['downgrades'][$activeSub->getId()] =
					$this->getLastRenewalSubInLevels(
						$activeSub->level->downgrades->modelKeys()
					);
			});
	}

	/**
	 * Get a list of all renewal subscription IDs in the given subscription levels
	 *
	 * @param array $levels IDs of levels to look for
	 *
	 * @return  int
	 *
	 * @since   7.0.0
	 */
	private function getLastRenewalSubInLevels(array $levels): ?int
	{
		if (empty($levels))
		{
			return null;
		}

		$allSubs = [];

		foreach ($this->sortTable['waiting'] as $renewalSubId)
		{
			/** @var Subscriptions $renewalSub */
			$renewalSub = $this->items[$renewalSubId];

			if (!in_array($renewalSub->akeebasubs_level_id, $levels))
			{
				continue;
			}

			$allSubs[$renewalSubId] = $this->container->platform->getDate($renewalSub->publish_down)->getTimestamp();
		}

		if (empty($allSubs))
		{
			return null;
		}

		$lastTimestamp = 0;
		$subId         = null;

		foreach ($allSubs as $id => $timestamp)
		{
			if ($timestamp < $lastTimestamp)
			{
				continue;
			}

			$lastTimestamp = $timestamp;
			$subId         = $id;
		}

		return $subId;
	}
}
