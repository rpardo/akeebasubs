<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Model\DataModel;

/**
 * Model for the invoices issued
 *
 * @property  int		$akeebasubs_subscription_id
 * @property  string	$extension
 * @property  int		$invoice_no
 * @property  string	$display_number
 * @property  string	$invoice_date
 * @property  string	$html
 * @property  string	$atxt
 * @property  string	$btxt
 * @property  string	$filename
 * @property  string	$sent_on
 * @property  int     	$enabled      		Publish status of this record
 * @property  int     	$created_by   		ID of the user who created this record
 * @property  string  	$created_on   		Date/time stamp of record creation
 * @property  int     	$modified_by  		ID of the user who modified this record
 * @property  string  	$modified_on  		Date/time stamp of record modification
 * @property  int     	$locked_by    		ID of the user who locked this record
 * @property  string  	$locked_on    		Date/time stamp of record locking
 *
 * Filters:
 *
 * @method  $this  akeebasubs_subscription_id()     akeebasubs_subscription_id(int $v)
 * @method  $this  extension()                      extension(string $v)
 * @method  $this  invoice_no()                     invoice_no(int $v)
 * @method  $this  display_number()                 display_number(string $v)
 * @method  $this  invoice_date()                   invoice_date(string $v)
 * @method  $this  html()                           html(string $v)
 * @method  $this  atxt()                           atxt(string $v)
 * @method  $this  btxt()                           btxt(string $v)
 * @method  $this  filename()                       filename(string $v)
 * @method  $this  sent_on()                        sent_on(string $v)
 * @method  $this  enabled()                        enabled(bool $v)
 * @method  $this  created_on()                     created_on(string $v)
 * @method  $this  created_by()                     created_by(int $v)
 * @method  $this  modified_on()                    modified_on(string $v)
 * @method  $this  modified_by()                    modified_by(int $v)
 * @method  $this  locked_on()                      locked_on(string $v)
 * @method  $this  locked_by()                      locked_by(int $v)
 * @method  $this  subids()							subids(array $v)
 *
 * @property-read  Subscriptions  		$subscription	The subscription of this invoice
 * @property-read  CreditNotes		    $creditNote		The credit note issued against this invoice
 */
class Invoices extends DataModel
{
	use Mixin\Assertions;

	/**
	 * Public constructor. We override it to set up behaviours and relations
	 *
	 * @param   Container  $container
	 * @param   array      $config
	 */
	public function __construct(Container $container, array $config = array())
	{
		// We have a non-standard PK field
		$config['idFieldName'] = 'akeebasubs_subscription_id';

		parent::__construct($container, $config);

		// Add the Filters behaviour
		$this->addBehaviour('Filters');
		// Some filters we will have to handle pragmatically so we need to exclude them from the behaviour
		$this->blacklistFilters([
			'akeebasubs_subscription_id',
			'invoice_date',
			'sent_date',
		]);

		// Set up relations
		$this->hasOne('subscription', 'Subscriptions', 'akeebasubs_subscription_id', 'akeebasubs_subscription_id');
		$this->hasOne('creditNote', 'CreditNotes', 'akeebasubs_subscription_id', 'akeebasubs_invoice_id');

		// Eager load the relations. This allows us to get rid of ugly JOINs.
		$this->with(['subscription']);
	}

	/**
	 * Set the default ordering
	 *
	 * @param   \JDatabaseQuery  $query
	 *
	 * @return  void
	 */
	protected function onBeforeBuildQuery(\JDatabaseQuery &$query)
	{
		// Set the default ordering by ID, descending
		if (is_null($this->getState('filter_order', null, 'cmd')) && is_null($this->getState('filter_order_Dir', null, 'cmd')))
		{
			$this->setState('filter_order', $this->getIdFieldName());
			$this->setState('filter_order_Dir', 'DESC');
		}
	}

	/**
	 * Build the SELECT query for returning records. Overridden to apply custom filters.
	 *
	 * @param   \JDatabaseQuery  $query           The query being built
	 * @param   bool             $overrideLimits  Should I be overriding the limit state (limitstart & limit)?
	 *
	 * @return  void
	 */
	public function onAfterBuildQuery(\JDatabaseQuery $query, $overrideLimits = false)
	{
		$db = $this->getDbo();

		$id = $this->getState('akeebasubs_subscription_id', null, 'raw');

		if (is_array($id))
		{
			if (isset($id['method']) && ($id['method'] == 'exact'))
			{
				$id = (int) $id['value'];
			}
			else
			{
				$id = 0;
			}
		}
		else
		{
			$id = (int) $id;
		}

		$subIDs = $this->getState('subids', null, 'array');
		$subIDs = empty($subIDs) ? [] : $subIDs;

		// Search by user
		$user = $this->getState('user', null, 'string');

		if (!empty($user))
		{
			// First get the Joomla! users fulfilling the criteria
			/** @var JoomlaUsers $users */
			$users = $this->container->factory->model('JoomlaUsers')->tmpInstance();
			$userIDs = $users->search($user)->with([])->get(true)->modelKeys();
			$filteredIDs = [-1];

			if (!empty($userIDs))
			{
				// Now get the subscriptions IDs for these users
				/** @var Subscriptions $subs */
				$subs = $this->container->factory->model('Subscriptions')->tmpInstance();
				$subs->setState('user_id', $userIDs);
				$subs->with([]);

				$filteredIDs = $subs->get(true)->modelKeys();
				$filteredIDs = empty($filteredIDs) ? [-1] : $filteredIDs;
			}

			if (!empty($subIDs))
			{
				$subIDs = array_intersect($subIDs, $filteredIDs);
			}
			else
			{
				$subIDs = $filteredIDs;
			}

			unset($subs);
		}

		// Search by business information
		$business = $this->getState('business', null, 'string');

		if (!empty($business))
		{
			$search = '%' . $business . '%';

			/** @var Subscriptions $subs */
			$subs = $this->container->factory->model('Subscriptions')->tmpInstance();
			$subs->whereHas('user', function(\JDatabaseQuery $q) use($search) {
				$q->where(
					$q->qn('businessname') . ' LIKE ' . $q->q($search)
				);
			});

			$subs->with([]);
			$filteredIDs = $subs->get(true)->modelKeys();
			$filteredIDs = empty($filteredIDs) ? [-1] : $filteredIDs;

			if (!empty($subIDs))
			{
				$subIDs = array_intersect($subIDs, $filteredIDs);
			}
			else
			{
				$subIDs = $filteredIDs;
			}

			unset($subs);
		}

		// Search by a list of subscription IDs
		if (is_numeric($id) && ($id > 0))
		{
			$query->where(
				$db->qn('akeebasubs_subscription_id') . ' = ' . $db->q((int)$id)
			);
		}
		elseif (!empty($subIDs))
		{
			$subIDs = array_unique($subIDs);
			$ids = array();

			foreach ($subIDs as $id)
			{
				$id = (int)$id;

				if ($id == 0)
				{
					continue;
				}

				$ids[] = $db->q($id);
			}

			if (!empty($ids))
			{
				$query->where(
					$db->qn('akeebasubs_subscription_id') . ' IN (' .
					implode(',', $ids) . ')'
				);
			}
		}

		// Search by invoice number (raw or formatted)
		$invoice_number = $this->getState('invoice_number', null, 'string');

		if ( !empty($invoice_number))
		{
			// Unified invoice / display number search
			$query->where(
				'((' .
				$db->qn('invoice_no') . ' = ' . $db->q((int)$invoice_number)
				. ') OR (' .
				$db->qn('display_number') . ' LIKE ' . $db->q('%' . $invoice_number . '%')
				. '))'
			);
		}

		// Prepare for date filtering
		$dateRegEx = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';

		// Filter by invoice issue date
		$invoice_date = $this->getState('invoice_date', null, 'string');
		$invoice_date_before = $this->getState('invoice_date_before', null, 'string');
		$invoice_date_after = $this->getState('invoice_date_after', null, 'string');

		if ( !empty($invoice_date) && preg_match($dateRegEx, $invoice_date))
		{
			$jFrom = $this->container->platform->getDate($invoice_date);
			$jFrom->setTime(0, 0, 0);
			$jTo = clone $jFrom;
			$jTo->setTime(23, 59, 59);

			$query->where(
				$db->qn('invoice_date') . ' BETWEEN ' . $db->q($jFrom->toSql()) .
				' AND ' . $db->q($jTo->toSql())
			);
		}
		elseif ( !empty($invoice_date_before) || !empty($invoice_date_after))
		{
			if ( !empty($invoice_date_before) && preg_match($dateRegEx, $invoice_date_before))
			{
				$date = $this->container->platform->getDate($invoice_date_before);
				$query->where($db->qn('invoice_date') . ' <= ' . $db->q($date->toSql()));
			}
			if ( !empty($invoice_date_after) && preg_match($dateRegEx, $invoice_date_after))
			{
				$date = $this->container->platform->getDate($invoice_date_after);
				$query->where($db->qn('invoice_date') . ' >= ' . $db->q($date->toSql()));
			}
		}

		// Filter by invoice email sent date
		$sent_on = $this->getState('sent_on', null, 'string');
		$sent_on_before = $this->getState('sent_on_before', null, 'string');
		$sent_on_after = $this->getState('sent_on_after', null, 'string');

		if ( !empty($sent_on) && preg_match($dateRegEx, $sent_on))
		{
			$jFrom = $this->container->platform->getDate($sent_on);
			$jFrom->setTime(0, 0, 0);
			$jTo = clone $jFrom;
			$jTo->setTime(23, 59, 59);

			$query->where(
				$db->qn('sent_on') . ' BETWEEN ' . $db->q($jFrom->toSql()) .
				' AND ' . $db->q($jTo->toSql())
			);
		}
		elseif ( !empty($sent_on_before) || !empty($sent_on_after))
		{
			if ( !empty($sent_on_before) && preg_match($dateRegEx, $sent_on_before))
			{
				$date = $this->container->platform->getDate($sent_on_before);
				$query->where($db->qn('sent_on') . ' <= ' . $db->q($date->toSql()));
			}
			if ( !empty($sent_on_after) && preg_match($dateRegEx, $sent_on_after))
			{
				$date = $this->container->platform->getDate($sent_on_after);
				$query->where($db->qn('sent_on') . ' >= ' . $db->q($date->toSql()));
			}
		}
	}

	/**
	 * Returns a list of known invoicing extensions
	 *
	 * @param   integer $style 0 = raw sections list, 1 = list options, 2 = key/description array
	 *
	 * @return  string[]
	 */
	public function getExtensions($style = 0)
	{
		static $rawOptions = null;
		static $htmlOptions = null;
		static $shortlist = null;

		if (is_null($rawOptions))
		{
			$rawOptions = array();

			\JLoader::import('joomla.plugin.helper');
			\JPluginHelper::importPlugin('akeebasubs');
			\JPluginHelper::importPlugin('system');
			$app       = \JFactory::getApplication();
			$jResponse = $app->triggerEvent('onAKGetInvoicingOptions', array());

			if (is_array($jResponse) && !empty($jResponse))
			{
				foreach ($jResponse as $pResponse)
				{
					if ( !is_array($pResponse))
					{
						continue;
					}
					if (empty($pResponse))
					{
						continue;
					}

					$rawOptions[$pResponse['extension']] = $pResponse;
				}
			}
		}

		if ($style == 0)
		{
			return $rawOptions;
		}

		if (is_null($htmlOptions))
		{
			$htmlOptions = array();

			foreach ($rawOptions as $def)
			{
				$htmlOptions[]                = \JHtml::_('select.option', $def['extension'], $def['title']);
				$shortlist[$def['extension']] = $def['title'];
			}
		}

		if ($style == 1)
		{
			return $htmlOptions;
		}
		else
		{
			return $shortlist;
		}
	}

	protected function setHtmlAttribute($value)
	{
		return $this->container->crypto->encrypt($value);
	}

	protected function setAtxtAttribute($value)
	{
		return $this->container->crypto->encrypt($value);
	}

	protected function setBtxtAttribute($value)
	{
		return $this->container->crypto->encrypt($value);
	}

	protected function getHtmlAttribute($value)
	{
		return $this->container->crypto->decrypt($value);
	}

	protected function getAtxtAttribute($value)
	{
		return $this->container->crypto->decrypt($value);
	}

	protected function getBtxtAttribute($value)
	{
		return $this->container->crypto->decrypt($value);
	}
}
