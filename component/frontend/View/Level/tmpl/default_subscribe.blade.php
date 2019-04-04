<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

use Akeeba\Subscriptions\Admin\Helper\Image;
use Akeeba\Subscriptions\Admin\Helper\Message;

use Akeeba\Subscriptions\Admin\Helper\Select;

$paymentMethodsCount = count(Select::paymentmethods('paymentmethod', '', ['id'              => 'paymentmethod',
																		  'level_id'        => $this->item->akeebasubs_level_id,
																		  'return_raw_list' => 1]));
$hidePaymentMethod   =
	($paymentMethodsCount <= 1) || ($this->validation->price->gross < 0.01);

?>

{{-- PAYMENT METHODS --}}
<div id="paymentmethod-container" style="display: {{$hidePaymentMethod ? 'none' : 'inherit'}}">
	<div class="akeeba-form-group">
		<label for="paymentmethod">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_METHOD')
		</label>

		<div id="paymentlist-container">
			<?php
			/** @var \Akeeba\Subscriptions\Site\Model\PaymentMethods $paymentMethods */
			$paymentMethods = $this->getContainer()->factory->model('PaymentMethods')->tmpInstance();
			$defaultPayment = $this->validation->validation->rawDataForDebug['paymentmethod'];

			if (empty($defaultPayment))
			{
				$defaultPayment = $paymentMethods->getLastPaymentPlugin($this->container->platform->getUser()->id);
			}

			echo Select::paymentmethods(
					'paymentmethod',
					$defaultPayment,
					array(
							'id'       => 'paymentmethod',
							'level_id' => $this->item->akeebasubs_level_id,
					)
			) ?>
		</div>
	</div>
</div>

{{-- SUBSCRIBE BUTTON --}}
<div class="akeeba-form-group--pull-right">
	<button id="subscribenow" class="akeeba-btn--block akeeba-btn--teal akeebasubs-btn-big" type="submit">
		@lang('COM_AKEEBASUBS_LEVEL_BUTTON_SUBSCRIBE')
	</button>
	<div class="akeeba-help-text akeebasubs-level-footer">
		<p>
			@sprintf('COM_AKEEBASUBS_LEVEL_FOOTERTEXT', \Joomla\CMS\Factory::getConfig()->get('sitename'))
		</p>
		<p class="akeebasubs-payment-methods">
			<span class="akpayment-icon-paypal" title="PayPal" class="hasTooltip"></span>
			<span class="akpayment-icon-visa" title="VISA" class="hasTooltip"></span>
			<span class="akpayment-icon-mastercard" title="MasterCard" class="hasTooltip"></span>
			<span class="akpayment-icon-amex" title="American Express" class="hasTooltip"></span>
			<span class="akpayment-icon-bank" title="Wire Transfer, Bank Deposit, SEPA" class="hasTooltip"></span>
			<span class="akpayment-icon-apple" title="Apple Pay" class="hasTooltip"></span>
		</p>
	</div>
</div>
