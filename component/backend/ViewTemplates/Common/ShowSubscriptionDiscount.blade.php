<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 *
 * User information display field
 * Use it $this->loadAnyTemplate('admin:com_akeebasubs/Common/ShowSubscriptionDiscount', $params)
 *
 * $params is an array defining the following keys (they are expanded into local scope vars automatically):
 *
 * @var Subscriptions            $item  The current row
 *
 * Variables made automatically available to us by FOF:
 *
 * @var \FOF30\View\DataView\Raw $this
 */

use Akeeba\Subscriptions\Admin\Model\Subscriptions;

defined('_JEXEC') or die;

?>
@if($item->akeebasubs_coupon_id)
    <span class="akeebasubs-subscription-discount-coupon" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_COUPON')">
	<span class="discount-icon"></span>
	    {{{ $item->coupon->title or '&mdash;&mdash;&mdash;' }}}
    </span>
@elseif($item->akeebasubs_upgrade_id)
    <span class="akeebasubs-subscription-discount-upgrade" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_UPGRADE')">
	<span class="discount-icon"></span>
	    {{{ $item->upgrade->title or '&mdash;&mdash;&mdash;' }}}
    </span>
@else
    <span class="akeebasubs-subscription-discount-none">
        @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_DISCOUNT_NONE')
    </span>
@endif
