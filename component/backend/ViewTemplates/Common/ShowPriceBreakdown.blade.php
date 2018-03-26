<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 *
 * User information display field
 * Use it $this->loadAnyTemplate('admin:com_akeebasubs/Common/ShowPriceBreakdown', $params)
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
@if($item->net_amount > 0)
    @if ($item->discount_amount > 0)
        <span class="akeebasubs-subscription-netamount">{{ \Akeeba\Subscriptions\Admin\Helper\Format::formatPrice($item->prediscount_amount) }}</span>
        <span class="akeebasubs-subscription-discountamount">{{ \Akeeba\Subscriptions\Admin\Helper\Format::formatPrice(-1.0 * $item->discount_amount) }}</span>
    @else
        <span class="akeebasubs-subscription-netamount">{{ \Akeeba\Subscriptions\Admin\Helper\Format::formatPrice($item->net_amount) }}</span>
    @endif
    <span class="akeebasubs-subscription-taxamount">{{ \Akeeba\Subscriptions\Admin\Helper\Format::formatPrice($item->tax_amount) }}</span>
    <span class="akeebasubs-subscription-grossamount">{{ \Akeeba\Subscriptions\Admin\Helper\Format::formatPrice($item->gross_amount) }}</span>
@endif
