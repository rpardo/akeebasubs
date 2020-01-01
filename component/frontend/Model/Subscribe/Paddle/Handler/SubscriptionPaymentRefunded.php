<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Site\Model\Subscribe\Paddle\Handler;

use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\FixSubscriptionDate;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\RecurringSubscriptions;
use Akeeba\Subscriptions\Site\Model\Subscribe\HandlerTraits\StackCallback;
use Akeeba\Subscriptions\Site\Model\Subscribe\SubscriptionCallbackHandlerInterface;
use Akeeba\Subscriptions\Site\Model\Subscriptions;
use Exception;
use FOF30\Container\Container;
use FOF30\Date\Date;

/**
 * Handle a recurring payment refund event.
 *
 * @see         https://paddle.com/docs/subscriptions-event-reference/#payment_refunded
 *
 * @since       7.0.0
 */
class SubscriptionPaymentRefunded extends PaymentRefunded
{
	/**
	 * This event is identical to the payment_refunded event. In fact, if a refund is issued for the initial payment a
	 * payment_refunded event is sent as well. All we need to do is have the same code run in both cases.
	 */
}