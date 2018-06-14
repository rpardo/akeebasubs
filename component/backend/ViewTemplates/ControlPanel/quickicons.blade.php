<?php
/**
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;
?>

<a href="index.php?option=com_akeebasubs&view=Level" class="akeeba-btn--dark--small">
    <span class="akion-ios-book"></span>
    <span>@lang('COM_AKEEBASUBS_DASHBOARD_ADD_LEVEL')</span>
</a>

@if ($this->container->platform->getUser()->authorise('com_akeebasubs.pii', 'com_akeebasubs'))
<a href="index.php?option=com_akeebasubs&view=Subscription" class="akeeba-btn--dark--small">
    <span class="akion-bookmark"></span>
    <span>@lang('COM_AKEEBASUBS_DASHBOARD_ADD_SUBSCRIPTION')</span>
</a>
@endif

<a href="index.php?option=com_akeebasubs&view=Coupon" class="akeeba-btn--dark--small">
    <span class="akion-ios-pricetag"></span>
    <span>@lang('COM_AKEEBASUBS_DASHBOARD_ADD_COUPON')</span>
</a>
