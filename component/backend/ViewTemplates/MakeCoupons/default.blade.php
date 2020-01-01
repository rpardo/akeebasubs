<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use \Akeeba\Subscriptions\Admin\Helper\Select;

/** @var \Akeeba\Subscriptions\Admin\View\MakeCoupons\Html $this */
/** @var \Akeeba\Subscriptions\Admin\Model\MakeCoupons $model */

$item          = $this->getModel();
$subscriptions = $item->getState('subscriptions', '');

if (is_array($subscriptions))
{
	$subscriptions = implode(',', $subscriptions);
}
?>

<?php if ($this->coupons): ?>
<div class="akeeba-panel--green">
	<header class="akeeba-block-header">
		<h3>@lang('COM_AKEEBASUBS_MAKECOUPONS_COUPONS_LABEL')</h3>
	</header>

	<table class="akeeba-table--striped--hover" width="100%">
		<?php foreach ($this->coupons as $coupon): ?>
		<tr>
			<td>
				<?php echo $coupon ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal">


	<div class="akeeba-panel--info">
		<header class="akeeba-block-header">
			<h3><?php echo JText::_('COM_AKEEBASUBS_MAKECOUPONS_GENERATE_LABEL') ?></h3>
		</header>

		<div class="akeeba-form-group">
			<label for="title">
				@lang('COM_AKEEBASUBS_COUPON_FIELD_TITLE')
			</label>

			<input type="text" id="title" name="title" class="input-medium"
				   value="{{{ $item->getState('title', '') }}}"/>
		</div>

		<div class="akeeba-form-group">
			<label for="prefix">
				@lang('COM_AKEEBASUBS_MAKECOUPONS_PREFIX_LABEL')
			</label>

			<input type="text" id="prefix" name="prefix" class="input-medium"
				   value="{{{ $item->getState('prefix', '') }}}"/>
		</div>

		<div class="akeeba-form-group">
			<label for="quantity">
				@lang('COM_AKEEBASUBS_MAKECOUPONS_QUANTITY_LABEL')
			</label>

			<input type="text" id="quantity" name="quantity" class="input-small"
				   value="{{{ $item->getState('quantity', 5) }}}"/>
		</div>

		<div class="akeeba-form-group">
			<label for="type">
				@lang('COM_AKEEBASUBS_COUPON_FIELD_TYPE')
			</label>

			{{ Select::coupontypes('type', $item->getState('type', 'percent')) }}
		</div>

		<div class="akeeba-form-group">
			<label for="value">
				@lang('COM_AKEEBASUBS_COUPON_FIELD_VALUE')
			</label>

			<input type="text" id="value" name="value" class="input-small"
				   value="{{{ $item->getState('value', 100) }}}"/>
		</div>

		<div class="akeeba-form-group">
			<label for="subscriptions">
				@lang('COM_AKEEBASUBS_COUPON_FIELD_SUBSCRIPTIONS')
			</label>

			{{ Select::levels('subscriptions[]', empty($subscriptions) ? '-1' : explode(',', $subscriptions), array(
				'multiple' => 'multiple',
				'size'     => 3
			)) }}
		</div>

		<div class="akeeba-form-group">
			<label for="userhits">
				@lang('COM_AKEEBASUBS_COUPON_FIELD_USERHITSLIMIT')
			</label>

			<input type="text" size="5" id="userhits" name="userhits"
				   value="{{{ $item->getState('userhits', 1) }}}"/>
		</div>

		<div class="akeeba-form-group">
			<label for="hits">
				@lang('COM_AKEEBASUBS_COUPON_FIELD_HITSLIMIT')
			</label>

			<input type="text" size="5" id="hits" name="hits"
				   value="{{{ $item->getState('hits', 0) }}}"/>
		</div>

		<div class="akeeba-form-group">
			<label for="expiration">
				@lang('COM_AKEEBASUBS_COUPON_PUBLISH_DOWN')
			</label>

			@jhtml('calendar', $item->getState('expiration', ''), 'expiration', 'expiration')
		</div>

		<div class="akeeba-form-group--actions">
			<button class="akeeba-btn--large">
				<span class="akion-ios-cog"></span>
				@lang('COM_AKEEBASUBS_MAKECOUPONS_RUN_LABEL')
			</button>
			<a href="index.php?option=com_akeebasubs&view=coupons" class="akeeba-btn--red">
				<span class="akion-chevron-left"></span>
				@lang('COM_AKEEBASUBS_MAKECOUPONS_BACK_LABEL')
			</a>
		</div>

	</div>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" value="com_akeebasubs"/>
		<input type="hidden" name="view" value="MakeCoupons"/>
		<input type="hidden" id="task" name="task" value="generate"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</div>

</form>
