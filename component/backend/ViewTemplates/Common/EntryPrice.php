<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/*
 * Price entry field, showing the currency next to it.
 * Use it $this->loadAnyTemplate('admin:com_akeebasubs/Common/EntryPrice', $params)
 *
 * $params is an array defining the following keys (they are expanded into local scope vars automatically):
 *
 * @var string                 $field   The price field's name, e.g. "price"
 * @var \FOF30\Model\DataModel $item    The item we're editing
 * @var mixed                  $default The default value for the item
 *
 * Variables made automatically available to us by FOF:
 *
 * @var \FOF30\View\DataView\DataViewInterface $this
 */

defined('_JEXEC') or die();

// Parameters from the component's configuration
$currencyPosition = $this->container->params->get('currencypos', 'before');
$currencySymbol   = $this->container->params->get('currencysymbol', '€');

$default = isset($default) ? $default : '';
$value   = $item->getFieldValue($field, $default);
?>
<div class="akeeba-input-group">
	<?php if ($currencyPosition == 'before'): ?>
    <span>
        <?php echo $currencySymbol ?>
    </span>
	<?php endif; ?>
	<input type="number" size="15" step="any" min="0" id="<?php echo $field ?>" name="<?php echo $field ?>" value="<?php echo $value ?>"
           style="float: none"/>
	<?php if ($currencyPosition == 'after'): ?>
    <span>
        <?php echo $currencySymbol ?>
    </span>
	<?php endif; ?>
</div>
