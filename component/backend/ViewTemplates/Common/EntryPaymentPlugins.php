<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 *
 * Payment plugins selection field. Select zero or more payment plugins.
 * Use it $this->loadAnyTemplate('admin:com_akeebasubs/Common/EntryPaymentPlugins', $params)
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

$default = isset($default) ? $default : '';
$value   = $item->getFieldValue($field, $default);

echo \Akeeba\Subscriptions\Admin\Helper\Select::paymentmethods(
	$field . '[]',
	$value,
	array(
		'id' => $field,
		'multiple' => 'multiple',
		'always_dropdown' => 1,
		'default_option' => 1
	)
) ?>
