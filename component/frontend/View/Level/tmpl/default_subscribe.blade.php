<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

$demoPayment = $this->container->params->get('demo_payment', 0);
$buttonClass = $demoPayment ? 'red' : 'teal';

?>

{{-- SUBSCRIBE BUTTON --}}
<div class="akeeba-form-group--pull-right">
	<button id="subscribenow" class="akeeba-btn--block akeeba-btn--{{ $buttonClass }} akeebasubs-btn-big" onclick="return akeebaSubscriptionsStartPayment()">
		@lang($demoPayment ? 'COM_AKEEBASUBS_LEVEL_BUTTON_SUBSCRIBE_DEMO' : 'COM_AKEEBASUBS_LEVEL_BUTTON_SUBSCRIBE')
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