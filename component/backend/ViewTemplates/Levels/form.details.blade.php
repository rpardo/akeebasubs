<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
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
    <input type="text" class="title" name="title" id="title" value="{{{ $item->title }}}">
</div>

<div class="akeeba-form-group">
    <label for="slug">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_SLUG')
    </label>
    <input type="text" name="slug" id="slug" value="{{{ $item->slug }}}">
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_SLUG_TIP')
    </p>
</div>

<div class="akeeba-form-group">
    <label for="access">
        @lang('JFIELD_ACCESS_LABEL')
    </label>
    @jhtml('FEFHelper.select.genericlist', \FOF30\Utils\SelectOptions::getOptions('access'), 'access', ['list.select' => $item->access])
</div>


<div class="akeeba-form-group">
    <label for="image">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_IMAGE')
    </label>
    <input type="text" name="image" id="image" value="{{{ $item->image }}}">
</div>


<div class="akeeba-form-group">
    <label for="slug">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PRODUCT_URL')
    </label>
    <input type="text" name="product_url" id="product_url" value="{{{ $item->product_url }}}">
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PRODUCT_URL_HELP')
    </p>
</div>


<div class="akeeba-form-group">
    <label for="enabled">
        @lang('JPUBLISHED')
    </label>
    @jhtml('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)
</div>


<div class="akeeba-form-group">
    <label for="description">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_DESCRIPTION')
    </label>
    <div class="akeeba-nofef">
    @jhtml('FEFHelper.edit.editor', 'description', $item->description)
    </div>
</div>
