<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */
/** @var  \Akeeba\Subscriptions\Site\Model\Levels  $model */

?>
<div class="akeeba-form-group">
    <label for="ordertext">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_ORDERTEXT')
    </label>
    <div class="akeeba-nofef">
        @jhtml('FEFHelper.edit.editor', 'ordertext', $item->ordertext)
    </div>
</div>

<div class="akeeba-form-group">
    <label for="canceltext">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_CANCELTEXT')
    </label>
    <div class="akeeba-nofef">
        @jhtml('FEFHelper.edit.editor', 'canceltext', $item->canceltext)
    </div>
</div>
