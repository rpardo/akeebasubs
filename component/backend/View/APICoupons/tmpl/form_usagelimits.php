<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$selected = $item->creation_limit ? 1 : ($item->subscription_limit ? 2 : 3);
echo \Akeeba\Subscriptions\Admin\Helper\Select::apicouponLimits('usage_limits', $selected) ?>

<input type="text" style="width: 50px; display:none" id="creation_limit" name="creation_limit"
	   value="<?php echo $this->escape($item->creation_limit) ?>"/>
<input type="text" style="width: 50px; display:none" id="subscription_limit"
	   name="subscription_limit"
	   value="<?php echo $this->escape($item->subscription_limit) ?>"/>
<input type="text" style="width: 50px; display:none" id="value_limit" name="value_limit"
	   value="<?php echo $this->escape($item->value_limit) ?>"/>
