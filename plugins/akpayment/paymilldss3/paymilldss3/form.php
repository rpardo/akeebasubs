<?php defined('_JEXEC') or die(); ?>

<div class="akeeba-panel--primary">
    <header class="akeeba-block-header">
        <h3><?php echo JText::_('PLG_AKPAYMENT_PAYMILLDSS3_FORM_HEADER') ?></h3>
    </header>

    <div id="payment-errors" class="akeeba-block--failure" style="display: none;"></div>

    <form id="payment-form"
          style="display: none;" method="post"
          action="<?php echo $data->url ?>"
          class="akeeba-form--horizontal">

        <div id="paymilldss3-credit-card-fields"></div>

        <div class="akeeba-form-group">
            <div class="akeeba-form-group--actions">
                <a href="#payment-form" id="payment-button" class="akeeba-btn--green--block">
					<?php echo JText::_('PLG_AKPAYMENT_PAYMILLDSS3_FORM_PAYBUTTON') ?>
                </a>
            </div>
        </div>

        <div class="akeeba-block--warning" id="paymill-warn-noreload" style="display: none;">
			<?php echo JText::_('PLG_AKPAYMENT_PAYMILLDSS3_WARN_NORELOAD') ?>
        </div>

        <div class="akeeba-hidden-fields-container">
            <input type="hidden" name="currency" id="paymilldss3_currency" value="<?php echo $data->currency ?>"/>
            <input type="hidden" name="amount" id="paymilldss3_amount" value="<?php echo $data->amount ?>"/>
            <input type="hidden" name="description" id="paymilldss3_description"
                   value="<?php echo $data->description ?>"/>
            <input type="hidden" name="token" id="paymilldss3_token"/>
        </div>
    </form>
</div>


<script type="text/javascript">
	var akeebasubs_paymill_clicked = false;

	(function ($)
	{
		$(document).ready(function ()
          {
              AkeebaSubs.PayMillDss3.initialize();
          });
	})(akeeba.jQuery);

</script>
