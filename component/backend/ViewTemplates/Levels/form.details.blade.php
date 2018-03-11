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
    <label for="title">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_TITLE')
    </label>
    <input type="text" class="title" name="title" id="title" value="{{{ $model->title }}}">
</div>

<div class="akeeba-form-group">
    <label for="slug">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_SLUG')
    </label>
    <input type="text" name="slug" id="slug" value="{{{ $model->slug }}}">
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_SLUG_TIP')
    </p>
</div>

<div class="akeeba-form-group">
    <label for="access">
        @lang('JFIELD_ACCESS_LABEL')
    </label>
    @jhtml('FEFHelper.select.genericlist', \FOF30\Utils\SelectOptions::getOptions('access'), 'access', ['list.select' => $model->access])
</div>


<div class="akeeba-form-group">
    <label for="image">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_IMAGE')
    </label>
    <input type="text" name="image" id="image" value="{{{ $model->image }}}">
</div>


<div class="akeeba-form-group">
    <label for="enabled">
        @lang('JPUBLISHED')
    </label>
    @jhtml('FEFHelper.select.booleanswitch', 'enabled', $model->enabled)
</div>


<div class="akeeba-form-group">
    <label for="description">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_DESCRIPTION')
    </label>
    <div class="akeeba-noreset">
    @jhtml('FEFHelper.edit.editor', 'description', $model->description)
    </div>
</div>
