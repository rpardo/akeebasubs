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
    <label for="signupfee">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_SIGNUPFEE')
    </label>
    @include('admin:com_akeebasubs/Common/EntryPrice', ['field' => 'signupfee', 'item' => $item])
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
    <label for="payment_plugins">
        @lang('COM_AKEEBASUBS_LEVEL_FIELD_PAYMENT_PLUGINS')
    </label>
    @include('admin:com_akeebasubs/Common/EntryPaymentPlugins', ['field' => 'payment_plugins', 'item' => $item])
</div>

<div class="akeeba-form-group">
    <label for="akeebasubs_levelgroup_id">
        @lang('COM_AKEEBASUBS_LEVELS_FIELD_LEVELGROUP')
    </label>
    <?php echo \FOF30\Utils\FEFHelper\BrowseView::modelSelect('akeebasubs_levelgroup_id', 'LevelGroups',
		$item->akeebasubs_levelgroup_id, [
			'translate'      => false,
			'apply_access'   => false,
			'key_field'      => "akeebasubs_levelgroup_id",
			'value_field'    => "title",
			'none'           => "COM_AKEEBASUBS_SELECT_LEVELGROUP",
			'fof.autosubmit' => false
		]) ?>
</div>
