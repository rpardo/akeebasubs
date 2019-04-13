<?php
/**
 *  @package   AkeebaSubs
 *  @copyright Copyright (c)2010-$toda.year Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits;


use Akeeba\Subscriptions\Admin\Model\Subscriptions;
use FOF30\Date\Date;

/**
 * Handle the updates of recurring subscriptions
 *
 * @since   7.0.0
 */
trait RecurringSubscriptions
{
	/**
	 * Handles a recurring subscription's payment
	 *
	 * @param   Subscriptions $subscription The currently active subscription
	 * @param   array         $updates      Updates to the currently active subscription
	 *
	 * @return  array
	 *
	 * @throws  \Exception
	 * @since   7.0.0
	 */
	protected function handleRecurringSubscription(Subscriptions $subscription, array $updates): array
	{
		$jNow = new Date();

		// Create a new record for the old subscription
		$oldData                               = $subscription->getData();
		$oldData['akeebasubs_subscription_id'] = 0;
		$oldData['publish_down']               = $jNow->toSql();
		$oldData['enabled']                    = 0;
		$oldData['contact_flag']               = 3;
		$oldData['notes']                      = "Automatically renewed subscription on " . $jNow->toSql();

		/**
		 * Update the existing subscription with a fake processor key, appending "_TEMP" to the existing one. This will
		 * eliminate any interference from saving a new record below.
		 */
		$subscription->_dontNotify(true);
		$subscription->bind([
			'processor_key' => $subscription->processor_key . '_TEMP'
		]);
		$subscription->_dontNotify(false);

		// Save the record for the old subscription
		$oldSubscription = $subscription->tmpInstance();
		$oldSubscription->reset();
		$oldSubscription->_dontNotify(true);
		$oldSubscription->bind($oldData)->save();
		$oldSubscription->_dontNotify(false);

		// Remove obsolete invoice information
		$updates['akeebasubs_invoice_id'] = 0;

		// Legacy: we no longer have integrated invoicing and can do away with reassigning invoices.
		// $this->reassignInvoices($subscription, $oldSubscription);

		return $updates;
	}

	/**
	 * If there's an invoice for the currently active (old) subscription we need to reassign it to the new ID of the
	 * old subscription (since the existing subscription ID is actually reused for the recurring installment). This
	 * will allow a new invoice to be issued for the new installment despite it having an old ID.
	 *
	 * @param   Subscriptions  $subscription
	 * @param   Subscriptions  $oldSubscription
	 *
	 * @return  void
	 *
	 * @deprecated 7.0.0
	 */
	private function reassignInvoices(Subscriptions $subscription, Subscriptions $oldSubscription): void
	{
		$db    = $subscription->getDbo();
		$query = $db->getQuery(true)
			->update($db->qn('#__akeebasubs_invoices'))
			->set($db->qn('akeebasubs_subscription_id') . '=' . $db->q($oldSubscription->getId()))
			->where($db->qn('akeebasubs_subscription_id') . '=' . $db->q($subscription->getId()));
		$db->setQuery($query);
		$db->execute();
	}

}