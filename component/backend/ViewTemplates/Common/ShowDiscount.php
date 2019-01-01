<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/*
 * Discount display field
 * Use it $this->loadAnyTemplate('admin:com_akeebasubs/Common/ShowDiscount', $params)
 *
 * $params is an array defining the following keys (they are expanded into local scope vars automatically):
 *
 * @var DataModel $item             The current row
 * @var string    $field            The name of the field in the current row containing the discount value
 * @var string    $typeField        The name of the field in the current row containing the discount type
 * @var string    $class            An additional CSS class to apply, defaults to $field
 * @var string    $classValue       CSS class for "value" type fields
 * @var string    $classPercent     CSS class for "percent" type fields
 * @var string    $classLastPercent CSS class for "lastpercent" type fields
 *
 * Variables made automatically available to us by FOF:
 *
 * @var DataViewInterface $this
 */

use FOF30\Model\DataModel;
use FOF30\View\DataView\DataViewInterface;

defined('_JEXEC') or die;

// Initialise
$typeField        = isset($typeField) ? $typeField : 'type';
$class            = isset($class) ? $class : $field;
$id               = isset($id) ? $id : $field;
$classValue       = isset($classValue) ? $classValue : 'akeebasubs-coupon-discount-value';
$classPercent     = isset($classPercent) ? $classPercent : 'akeebasubs-coupon-discount-percent';
$classLastPercent = isset($classLastPercent) ? $classLastPercent : 'akeebasubs-coupon-discount-lastpercent';

$type           = $item->{$typeField};
$value          = $item->{$field};
$extraClass     = ($type == 'value') ? $classValue : $classPercent;
$extraClass     .= ($type == 'lastpercent') ? ' ' . $classLastPercent : '';
$currencyPos    = $this->getContainer()->params->get('currencypos', 'before');
$currencySymbol = $this->getContainer()->params->get('currencysymbol', '€');

?>
<span class="<?php echo $class . ' ' . $extraClass ?>">
<?php
// Case 1: Value discount
if ($type == 'value')
{
	if ($currencyPos == 'before')
	{
		echo $currencySymbol . ' ';
	}

	echo sprintf('%02.02f', (float) $value);

	if ($currencyPos == 'after')
	{
		echo ' ' . $currencySymbol;
	}
}
else
{
	echo sprintf('%2.2f', (float) $value) . ' %';
}
?>
</span>
