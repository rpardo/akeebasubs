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
    <label for="price">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PRICE')
    </label>
    @include('admin:com_akeebasubs/Common/EntryPrice', ['field' => 'price', 'item' => $item])
</div>

<div class="akeeba-form-group">
    <label for="duration">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_DURATION')
    </label>
    <input type="number" name="duration" id="duration" min="1" step="1" value="{{{ $item->getFieldValue('duration', 30) }}}" />
</div>

<div class="akeeba-form-group">
    <label for="fixed_date">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_FIXED_DATE')
    </label>
    @jhtml('calendar', $item->fixed_date, 'fixed_date', 'fixed_date')
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_FIXED_DATE_TIP')
    </p>
</div>

<div class="akeeba-form-group">
    <label for="forever">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_FOREVER')
    </label>
    @jhtml('FEFHelper.select.booleanswitch', 'forever', $item->forever)
</div>

<div class="akeeba-form-group">
    <label for="related_level_id">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_RELATED_LEVELS')
    </label>
    {{ \Akeeba\Subscriptions\Admin\Helper\Select::levels('related_level_id', $item->related_level_id, ['include_none' => true, 'multiple' => 1]) }}
    <p class="akeeba-help-text">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_RELATED_LEVELS_HELP')
    </p>
</div>

<h4>@lang('COM_AKEEBASUBS_LEVEL_HEADER_PADDLE')</h4>

<div class="akeeba-form-group">
    <label for="paddle_product_id">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PADDLE_PRODUCT_ID')
    </label>
    <input type="text" name="paddle_product_id" id="paddle_product_id" value="{{{ $item->getFieldValue('paddle_product_id', '') }}}" />
</div>

<div class="akeeba-form-group">
    <label for="paddle_secret">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PADDLE_SECRET')
    </label>
    <input type="text" name="paddle_secret" id="paddle_secret" value="{{{ $item->getFieldValue('paddle_secret', '') }}}" />
</div>

<div class="akeeba-form-group">
    <label >
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_UPSELL')
    </label>
    <div class="akeeba-toggle">
        <input id="upsell-never" type="radio" name="upsell" value="never"
                {{ ($item->getFieldValue('upsell', 'never') == 'never') ? 'checked' : '' }}
        />
        <label for="upsell-never" class="green">Never</label>

        <input id="upsell-renewal" type="radio" name="upsell" value="renewal"
                {{ ($item->getFieldValue('upsell', 'never') == 'renewal') ? 'checked' : '' }}
        />
        <label for="upsell-renewal" class="orange">Renewal</label>

        <input id="upsell-always" type="radio" name="upsell" value="always"
                {{ ($item->getFieldValue('upsell', 'never') == 'always') ? 'checked' : '' }}
        />
        <label for="upsell-always" class="red">Always</label>
    </div>
</div>

<div class="akeeba-form-group">
    <label for="paddle_plan_id">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PADDLE_PLAN_ID')
    </label>
    <input type="text" name="paddle_plan_id" id="paddle_plan_id" value="{{{ $item->getFieldValue('paddle_plan_id', '') }}}" />
</div>

<div class="akeeba-form-group">
    <label for="paddle_plan_secret">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PADDLE_PLAN_SECRET')
    </label>
    <input type="text" name="paddle_plan_secret" id="paddle_plan_secret" value="{{{ $item->getFieldValue('paddle_plan_secret', '') }}}" />
</div>
