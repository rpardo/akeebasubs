<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */
/** @var  \Akeeba\Subscriptions\Site\Model\Levels  $model */

?>
<div class="akeeba-form-group">
    <label for="orderurl">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_ORDERURL')
    </label>
    <input type="text" name="orderurl" id="orderurl" value="{{{ $model->orderurl }}}">
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_ORDERURL_DESC')
    </p>
</div>

<div class="akeeba-form-group">
    <label for="ordertext">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_ORDERTEXT')
    </label>
    <div class="akeeba-noreset">
        @jhtml('FEFHelper.edit.editor', 'ordertext', $model->ordertext)
    </div>
</div>

<div class="akeeba-form-group">
    <label for="cancelurl">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_CANCELURL')
    </label>
    <input type="text" name="cancelurl" id="cancelurl" value="{{{ $model->cancelurl }}}">
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_CANCELURL_DESC')
    </p>
</div>

<div class="akeeba-form-group">
    <label for="canceltext">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_CANCELTEXT')
    </label>
    <div class="akeeba-noreset">
        @jhtml('FEFHelper.edit.editor', 'canceltext', $model->canceltext)
    </div>
</div>
