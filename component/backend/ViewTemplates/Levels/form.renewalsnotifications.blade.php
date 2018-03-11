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
    <label for="recurring">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_RECURRING')
    </label>
    @jhtml('FEFHelper.select.booleanswitch', 'recurring', $model->recurring)
</div>

<div class="akeeba-form-group">
    <label for="only_once">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_ONLY_ONCE')
    </label>
    @jhtml('FEFHelper.select.booleanswitch', 'only_once', $model->only_once)
</div>

<div class="akeeba-form-group">
    <label for="renew_url">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_RENEW_URL')
    </label>
    <input type="text" name="renew_url" id="renew_url" value="{{{ $model->renew_url }}}">
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_RENEW_URL_DESC')
    </p>
</div>

<div class="akeeba-form-group">
    <label for="content_url">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_CONTENT_URL')
    </label>
    <input type="text" name="content_url" id="content_url" value="{{{ $model->content_url }}}">
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_CONTENT_URL_DESC')
    </p>
</div>

<div class="akeeba-form-group">
    <label for="notify1">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_NOTIFY1')
    </label>
    <input type="number" name="notify1" id="notify1" value="{{{ $model->notify1 }}}">
</div>

<div class="akeeba-form-group">
    <label for="notify2">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_NOTIFY2')
    </label>
    <input type="number" name="notify2" id="notify2" value="{{{ $model->notify2 }}}">
</div>

<div class="akeeba-form-group">
    <label for="notifyafter">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_NOTIFYAFTER')
    </label>
    <input type="number" name="notifyafter" id="notifyafter" value="{{{ $model->notifyafter }}}">
</div>
