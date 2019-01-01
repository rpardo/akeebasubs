<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 *
 * User information display field
 * Use it $this->loadAnyTemplate('admin:com_akeebasubs/Common/ShowPaymentStatus', $params)
 *
 * $params is an array defining the following keys (they are expanded into local scope vars automatically):
 *
 * @var \FOF30\Model\DataModel   $item  The current row
 * @var string                   $field The name of the field in the current row containing the value
 * @var string                   $processorField
 * @var string                   $processorKeyField
 * @var string                   $uaField
 * @var string                   $mobileField
 * @var string                   $class
 *
 * Variables made automatically available to us by FOF:
 *
 * @var \FOF30\View\DataView\Raw $this
 */

use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die;

// Get field parameters
$defaultParams = [
	'processorField'    => 'processor',
	'processorKeyField' => 'processor_key',
	'uaField'           => 'ua',
	'mobileField'       => 'mobile',
	'class'             => '',
];

foreach ($defaultParams as $paramName => $paramValue)
{
	if (!isset(${$paramName}))
	{
		${$paramName} = $paramValue;
	}
}

unset($defaultParams, $paramName, $paramValue);

// Initialization
$stateValue   = $item->getFieldValue($field);
$stateLower   = strtolower($stateValue);
$stateLabel   = htmlspecialchars(JText::_('COM_AKEEBASUBS_SUBSCRIPTION_STATE_' . $stateValue));
$processor    = htmlspecialchars($item->{$processorField});
$processorKey = htmlspecialchars($item->{$processorKeyField});
$mobile       = $item->{$mobileField};
$ua           = $item->{$uaField};
$labelClass   = $mobile ? 'green' : 'grey';
$iconClass    = $mobile ? 'akion-android-phone-portrait' : 'akion-android-desktop';

?>
<span class="akeebasubs-payment akeebasubs-payment-{{ $stateLower }} hasTip"
      title="{{{ $stateLabel }}}::{{{ $processor }}} &bull; {{ $processorKey }}">
</span>

<span class="akeebasubs-subscription-processor">
	{{{ $processor }}}
</span>
@if(!empty($ua))
    <span class="akeebasubs-subscription-ua hasTip" title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_UA')::{{{ $ua }}}">
        <span class="akeeba-label--{{{ $labelClass }}}"><span class="{{{ $iconClass }}}"></span></span>
</span>

@endif
