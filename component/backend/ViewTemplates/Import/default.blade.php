<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

$this->loadHelper('select');
$this->addJavascriptInline(<<< JS

akeeba.jQuery(document).ready(function(){
	akeeba.jQuery("#csvdelimiters").change(function(){
		if(akeeba.jQuery(this).val() == -99){
			akeeba.jQuery("#field_delimiter").show();
			akeeba.jQuery("#field_enclosure").show();
		}
		else{
			akeeba.jQuery("#field_delimiter").hide();
			akeeba.jQuery("#field_enclosure").hide();
		}
	})
});

JS

);
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal"
      enctype="multipart/form-data">

    <div class="akeeba-block--info">
        <p>
            @lang('COM_AKEEBASUBS_IMPORT_INFO')
        </p>
    </div>

    <div class="akeeba-block--warning">
        <p>
            @lang('COM_AKEEBASUBS_IMPORT_WARNING')
        </p>
    </div>

    <h3>
        @lang('COM_AKEEBASUBS_IMPORT_DETAILS')
    </h3>

    <div class="akeeba-form-group">
        <label for="csvdelimiters">@lang('COM_AKEEBASUBS_IMPORT_DELIMITERS')</label>
	    {{ \Akeeba\Subscriptions\Admin\Helper\Select::csvdelimiters('csvdelimiters', 1, ['class' => 'minwidth']) }}
        <p class="akeeba-help-text">
		    @lang('COM_AKEEBASUBS_IMPORT_DELIMITERS_DESC')
        </p>
    </div>

    <div class="akeeba-form-group" id="field_delimiter" style="display:none">
        <label for="field_delimiter">
            @lang('COM_AKEEBASUBS_IMPORT_FIELD_DELIMITERS')
        </label>
        <input type="text" name="field_delimiter" id="field_delimiter" value="">
        <p class="akeeba-help-text">
            @lang('COM_AKEEBASUBS_IMPORT_FIELD_DELIMITERS_DESC')
        </p>
    </div>

    <div class="akeeba-form-group" id="field_enclosure" style="display:none">
        <label for="field_enclosure">
            @lang('COM_AKEEBASUBS_IMPORT_FIELD_ENCLOSURE')
        </label>
        <input type="text" name="field_enclosure" id="field_enclosure" value="">
        <p class="akeeba-help-text">
		    @lang('COM_AKEEBASUBS_IMPORT_FIELD_ENCLOSURE_DESC')
        </p>
    </div>

    <div class="akeeba-form-group">
        <label for="csvfile">
            @lang('COM_AKEEBASUBS_IMPORT_FILE')
        </label>
        <input type="file" name="csvfile"/>
        <p class="akeeba-help-text">
		    @lang('COM_AKEEBASUBS_IMPORT_FILE_DESC')
        </p>
    </div>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" value="com_akeebasubs"/>
        <input type="hidden" name="view" value="import"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
    </div>
</form>
