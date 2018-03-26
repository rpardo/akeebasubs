<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Select;

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal">

    <div class="akeeba-form-group">
        <label for="novatcalc">
            @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_NOVATCALC')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'novatcalc', 0)
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_NOVATCALC_INFO')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="akeebasubs_level_id">
			@lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_LEVEL')
        </label>
	    <?php echo Select::levels('akeebasubs_level_id', 0, array(
		    'include_all' => 1
	    )); ?>
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_LEVEL_INFO')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="country">
			@lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_COUNTRY')
        </label>
	    <?php echo Select::countries(); ?>
        <p class="akeeba-help-text">
		    @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_COUNTRY_INFO')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="taxrate">
			@lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_TAXRATE')
        </label>
        <div class="akeeba-input-group">
            <span>%</span>
            <input type="text" name="taxrate" id="taxrate" class="input-small" value="0"/>
        </div>

        <p class="akeeba-help-text">
		    @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_TAXRATE_INFO')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="viesreg">
			@lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_VIESREG')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'viesreg', 0)
        <p class="akeeba-help-text">
		    @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_VIESREG_INFO')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="showvat">
			@lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_SHOWVAT')
        </label>
        @jhtml('FEFHelper.select.booleanswitch', 'showvat', 0)
        <p class="akeeba-help-text">
		    @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_SHOWVAT_INFO')
        </p>
    </div>

    <div class="akeeba-form-group--pull-right">
        <div class="akeeba-form-group--actions">
            <button class="akeeba-btn--green">
                <span class="akion-checkmark"></span>
                @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_SUBMIT')
            </button>
            <a href="@route('index.php?option=com_akeebasubs&view=TaxRules')"
               class="akeeba-btn--red--small">
                <span class="akion-close"></span>
                @lang('COM_AKEEBASUBS_TAXCONFIGS_LBL_CANCEL')
            </a>
        </div>
    </div>


    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" value="com_akeebasubs"/>
        <input type="hidden" name="view" value="TaxConfig"/>
        <input type="hidden" id="task" name="task" value="apply"/>
        <input type="hidden" name="@token()" value="1"/>
    </div>

</form>
