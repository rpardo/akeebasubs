<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model;

use FOF30\Model\DataModel\Collection as DataCollection;

defined('_JEXEC') or die;

/**
 * This model extends the back-end model, pulling it into the frontend without duplicating the code
 *
 * @property-read DataCollection $upgrades   Levels which are my upgrades
 * @property-read DataCollection $downgrades Levels which are my downgrades
 * @property-read bool $allowRecurring Does this subscription level allow recurring purchases?
 */
class Levels extends \Akeeba\Subscriptions\Admin\Model\Levels
{
	/**
	 * Static map of each subscription level and its upgrades' IDs.
	 *
	 * @var   array|null
	 * @since 7.0.0
	 */
	static $relatedMap;

	/**
	 * Static map of each subscription level and its downgrades' IDs.
	 *
	 * @var   array|null
	 * @since 7.0.0
	 */
	static $downgradeMap;

	/**
	 * A list of levels which are upgrades to me
	 *
	 * @var   DataCollection
	 * @since 7.0.0
	 */
	private $upgrades;

	/**
	 * A list of levels which are downgrades to me
	 *
	 * @var   DataCollection
	 * @since 7.0.0
	 */
	private $downgrades;

	/**
	 * Overridden magic getter to add new functionality
	 *
	 * @param string $name
	 *
	 * @return Levels|DataCollection|mixed
	 *
	 * @since  7.0.0
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'upgrades':
				return $this->getUpgradeLevels();

				break;

			case 'downgrades':
				return $this->getDowngradeLevels();

				break;

			case 'allowRecurring':
				return $this->getAllowRecurring();

				break;
		}

		if ($name == 'upgrades')
		{
			return $this->getUpgradeLevels();
		}

		if ($name == 'downgrades')
		{
			return $this->getDowngradeLevels();
		}

		return parent::__get($name);
	}

	/**
	 * When resetting the model also reset the upgrades and downgrades
	 *
	 * @param bool $useDefaults
	 * @param bool $resetRelations
	 *
	 * @since 7.0.0
	 */
	protected function onAfterReset($useDefaults = true, $resetRelations = false)
	{
		if (!$resetRelations)
		{
			return;
		}

		$this->upgrades   = null;
		$this->downgrades = null;
	}

	/**
	 * Reset the information for determining upgrades and downgrades every time we save the model (since we might have
	 * changed the related_levels).
	 *
	 * We use onAfterSave instead of onAfterUpdate because new subscription levels may introduce new related levels,
	 * therefore modifying the downgrades between levels.
	 *
	 * @since 7.0.0
	 */
	protected function onAfterSave()
	{
		// Reset the common data shared between all model instances
		static::$relatedMap   = null;
		static::$downgradeMap = null;

		// Reset our "virtual relations" for upgrade and downgrade levels
		$this->upgrades   = null;
		$this->downgrades = null;
	}

	/**
	 * Get a data collection with all levels which are upgrades to me
	 *
	 * @return DataCollection
	 *
	 * @since  7.0.0
	 */
	protected function getUpgradeLevels(): DataCollection
	{
		if (is_null($this->upgrades))
		{
			$this->upgrades = empty($this->upsell)
				? (new DataCollection())
				: $this->tmpInstance()->id($this->upsell)->get(true);
		}

		return $this->upgrades;
	}

	/**
	 * Get a data collection will all levels which are downgrades to me
	 *
	 * @return DataCollection
	 *
	 * @since  7.0.0
	 */
	protected function getDowngradeLevels(): DataCollection
	{
		if (is_null($this->downgrades))
		{
			$downgradeIDs  = [];
			$allDowngrades = $this->getDowngradeMap();

			if (array_key_exists($this->getId(), $allDowngrades))
			{
				$downgradeIDs = array_unique($allDowngrades[$this->getId()]);
			}

			$this->downgrades = empty($downgradeIDs)
				? (new DataCollection())
				: $this->tmpInstance()->id($downgradeIDs)->get(true);
		}

		return $this->downgrades;
	}

	/**
	 * Return the static map of subscription level IDs to their upgrades' IDs
	 *
	 * @return array
	 *
	 * @since  7.0.0
	 */
	protected function getRelatedMap(): array
	{
		if (!is_null(static::$relatedMap))
		{
			return static::$relatedMap;
		}

		static::$relatedMap = $this->tmpInstance()
			->get(true)
			->lists('related_levels', 'akeebasubs_level_id');

		return static::$relatedMap;
	}

	/**
	 * Return the static map of subscription level IDs to their downgrades' IDs
	 *
	 * @return array
	 *
	 * @since  7.0.0
	 */
	protected function getDowngradeMap(): array
	{
		if (!is_null(static::$downgradeMap))
		{
			return static::$downgradeMap;
		}

		static::$downgradeMap = [];

		// Convert the upsell map into a downgrade map
		foreach ($this->getRelatedMap() as $lowerID => $upperIDs)
		{
			if (empty($upperIDs))
			{
				continue;
			}

			foreach ($upperIDs as $upperID)
			{
				if (!array_key_exists($upperID, static::$downgradeMap))
				{
					static::$downgradeMap[$upperID] = [];
				}

				static::$downgradeMap[$upperID][] = $lowerID;
			}
		}

		return static::$downgradeMap;
	}

	/**
	 * Does this subscription level allow recurring purchases *at all*?
	 *
	 * This only tells you if the subscription level is set up to possibly allow recurring subscriptions to be purchased
	 * at all. Whether this is possible for the current user is something that can only be determined by the Recurring
	 * validation provider when they are trying to sign up. The intent of this method and the allow_recurring virtual
	 * property is to quickly filter out subscription levels which do not allow recurring subscriptions to be purchased.
	 *
	 * @return bool
	 *
	 * @since 7.0.0
	 */
	protected function getAllowRecurring()
	{
		if ($this->upsell == 'none')
		{
			return false;
		}

		if (empty($this->paddle_plan_id) || empty($this->paddle_plan_secret))
		{
			return false;
		}
		return true;
	}
}
