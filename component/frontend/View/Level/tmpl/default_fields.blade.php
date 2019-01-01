<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Select;

$akeebasubs_subscription_level = isset($this->item) ? $this->item->akeebasubs_level_id : null;
$apply_validation              = isset($this->apply_validation) ? ($this->apply_validation == 'true') : true;
$field_data = [
        'name'         => $this->getFieldValue('name'),
        'email'        => $this->getFieldValue('email'),
        'email2'       => $this->getFieldValue('email2'),
        'address1'     => $this->getFieldValue('address1'),
        'address2'     => $this->getFieldValue('address2'),
        'city'         => $this->getFieldValue('city'),
        'state'        => $this->getFieldValue('state'),
        'zip'          => $this->getFieldValue('zip'),
        'country'      => $this->getFieldValue('country', ['XX']),
        'businessname' => $this->getFieldValue('businessname'),
        'occupation'   => $this->getFieldValue('occupation'),
        'vatnumber'    => $this->getFieldValue('vatnumber'),
];

$group_classes                 = [
	'username'     => '',
	'password'     => '',
	'password2'    => '',
	'name'         => $this->validation->validation->name ? '' : '--error',
	'email'        => $this->validation->validation->email ? '' : '--error',
	'email2'       => $this->validation->validation->email2 ? '' : '--error',
	'address1'     => $this->validation->validation->address1 ? '' : '--error',
	'city'         => $this->validation->validation->city ? '' : '--error',
	'state'        => $this->validation->validation->state ? '' : '--error',
	'zip'          => $this->validation->validation->zip ? '' : '--error',
	'country'      => $this->validation->validation->country ? '' : '--error',
	'businessname' => $this->validation->validation->businessname ? '' : '--error',
	'occupation'   => !empty($field_data['occupation']) ? '' : '--error',
	'vatnumber'    => $this->validation->validation->vatnumber ? '' : '--warning',
];

if ($this->container->platform->getUser()->guest)
{
	$group_classes['username']  = (!$apply_validation || $this->validation->validation->username) ? '--success' : '--error';
	$group_classes['password']  = !$this->cache['password'] ? '--error' : '';
	$group_classes['password2'] =
		(!$this->cache['password2'] || ($this->cache['password2'] != $this->cache['password'])) ? '--error' :
			'';
}

$isBusiness = $this->getFieldValue('isbusiness');

$returnURI = JUri::getInstance();
$returnURI->setVar('reset', 1);
?>
@js('media://com_akeebasubs/js/signup.js')
@js('media://com_akeebasubs/js/autosubmit.js')

<div class="akeeba-form--horizontal akeebasubs-signup-fields">

	<h3>@lang('COM_AKEEBASUBS_LEVEL_USERACCOUNT')</h3>

	@if ($this->container->platform->getUser()->guest)

	{{-- Login button --}}
	<div id="akeebasubs-level-login" class="akeeba-form-group--pull-right">
		<div class="akeeba-form-group--actions">
			<a href="@route('index.php?option=com_users&view=login&return=' . base64_encode($returnURI->toString())))"
			   class="akeeba-btn--primary" rel="nofollow,noindex">
				<span class="glyphicon glyphicon-log-in"></span>
				@lang('COM_AKEEBASUBS_LEVEL_BTN_LOGINIFALERADY')
			</a>
		</div>
	</div>

	{{-- Username --}}
	<div class="akeeba-form-group{{$group_classes['username']}}">
		<label for="username">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME')
		</label>

		<input type="text" name="username" id="username"
			   value="{{{$this->cache['username']}}}"/>
		<p id="username_invalid" class="akeeba-help-text"
		   <?php if (strpos($group_classes['username'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME_INVALID')
		</p>
	</div>

	{{-- Password --}}
	<div class="akeeba-form-group{{$group_classes['password']}}">
		<label for="password">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_PASSWORD')
		</label>

		<input type="password" name="password" id="password"
			   value="{{{$this->cache['password']}}}"/>
		<p id="password_invalid" class="akeeba-help-text"
		   style="<?php if (strpos($group_classes['password'], 'error') === false): ?>display:none<?php endif; ?>">
			@lang('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD_EMPTY')
		</p>
	</div>

	{{-- Password (repeat) --}}
	<div class="akeeba-form-group{{$group_classes['password2']}}">
		<label for="password2">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_PASSWORD2')
		</label>

		<input type="password" name="password2" id="password2"
			   value="{{{$this->cache['password2']}}}"/>
		<p id="password2_invalid" class="help-block"
		   style="<?php if (strpos($group_classes['password2'], 'error') === false): ?>display:none<?php endif; ?>">
			@lang('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD2')
		</p>
	</div>
	@endif

	@unless($this->container->platform->getUser()->guest)
	{{-- Username (STATIC DISPLAY) --}}
	<div class="akeeba-form-group">
		<label for="username">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME')
		</label>

		<input type="text" name="username" id="username" disabled="disabled"
			   value="{{{$this->userparams->username}}}"/>
	</div>
	@endunless

	{{-- Email --}}
	<div class="akeeba-form-group{{$group_classes['email']}}">
		<label for="email">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL')
		</label>

		<input type="text" name="email" id="email"
			   value="{{{$field_data['email']}}}"/>
		<p id="email_invalid" class="akeeba-help-text"
		   <?php if (strpos($group_classes['email'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_EMAIL')
		</p>
	</div>

	{{-- Email (repeat) --}}
	<div class="akeeba-form-group{{$group_classes['email2']}}">
		<label for="email2">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL2')
		</label>

		<input type="text" name="email2" id="email2"
			   value="{{{$field_data['email2']}}}"/>
		<p id="email2_invalid" class="akeeba-help-text"
		   <?php if (strpos($group_classes['email2'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_EMAIL2')
		</p>
	</div>

	<h3><?php echo JText::_('COM_AKEEBASUBS_LEVEL_COUNTRYINFO') ?></h3>

	<div class="akeeba-block--info">
		@lang('COM_AKEEBASUBS_LEVEL_COUNTRYINFO_HELP')
	</div>

	{{-- Country --}}
	<div class="akeeba-form-group{{$group_classes['country']}}">
		<label for="country">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_COUNTRY')
		</label>

		{{Select::countries($field_data['country'], 'country', array())}}
		<p id="country_empty" class="akeeba-help-text"
		   <?php if (strpos($group_classes['country'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED')
		</p>
	</div>

	{{-- State --}}
	<div class="akeeba-form-group{{$group_classes['state']}}" id="stateField">
		<label for="state">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_STATE')
		</label>

		<div id="akeebasubs-statescontainer">
			{{Select::states($field_data['state'], 'state', array('country' => $field_data['country']))}}
		</div>
		<p id="state_empty" class="akeeba-help-text"
		   <?php if (strpos($group_classes['city'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED')
		</p>
	</div>

	<h3><?php echo JText::_('COM_AKEEBASUBS_LEVEL_INVOICINGPREFS') ?></h3>

	<div class="akeeba-block--info">
		@lang('COM_AKEEBASUBS_LEVEL_INVOICINGPREFS_HELP')
	</div>

	{{-- Full name --}}
	<div class="akeeba-form-group{{{$group_classes['name']}}}">
		<label for="name">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_NAME')
		</label>

		<input type="text" name="name" id="name"
			   value="{{{$field_data['name']}}}"/>
		<p id="name_empty" class="akeeba-help-text"
		   <?php if (strpos($group_classes['name'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_NAME_INVALID')
		</p>
	</div>

	{{-- Purchasing as a company --}}
	<div class="akeeba-form-group">
		<label for="isbusiness">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_ISBUSINESS')
		</label>
	<?php echo JHtml::_('select.genericlist', [
			JHtml::_('select.option', '0', JText::_('JNO')),
			JHtml::_('select.option', '1', JText::_('JYES'))
	], 'isbusiness', [], 'value', 'text', $isBusiness, 'isbusiness'); ?>
	</div>

	<div id="businessfields">
		{{-- Business name --}}
		<div class="akeeba-form-group{{$group_classes['businessname']}}">
			<label for="businessname">
				@lang('COM_AKEEBASUBS_LEVEL_FIELD_BUSINESSNAME')
			</label>

            <input type="text" name="businessname" id="businessname"
                   value="{{{$field_data['businessname']}}}"/>
            <p id="businessname_empty" class="akeeba-help-text"
			   <?php if (strpos($group_classes['businessname'], 'error') === false): ?>style="display:none"<?php endif ?>>
                @lang('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED')
            </p>
		</div>

		{{-- Business activity --}}
		<div class="akeeba-form-group{{$group_classes['occupation']}}">
			<label for="occupation">
				@lang('COM_AKEEBASUBS_LEVEL_FIELD_OCCUPATION')
			</label>

            <input type="text" name="occupation" id="occupation"
                   value="{{{$field_data['occupation']}}}"/>
            <p id="occupation_empty" class="akeeba-help-text"
			   <?php if (strpos($group_classes['occupation'], 'error') === false): ?>style="display:none"<?php endif ?>>
                @lang('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED')
            </p>
		</div>

		{{-- VAT Number --}}
		<div class="akeeba-form-group{{$group_classes['vatnumber']}}" id="vatfields">
			<label for="vatnumber" id="vatlabel">
				@lang('COM_AKEEBASUBS_LEVEL_FIELD_VATNUMBER_ALTLABEL')
			</label>

            <div class="akeeba-input-group">
                <span id="vatcountry">EU</span>
                <input type="text" name="vatnumber" id="vatnumber"
                       value="<?php echo $this->escape($field_data['vatnumber']); ?>"/>
            </div>

            <p id="vat-status-invalid" class="akeeba-help-text"
			   <?php if (strpos($group_classes['vatnumber'], '--warning') === false): ?>style="display:none"<?php endif ?>>
                @lang('COM_AKEEBASUBS_LEVEL_VAT_INVALID')
            </p>
		</div>
	</div>

	{{-- Address --}}
	<div class="akeeba-form-group{{$group_classes['address1']}}">
		<label for="address1">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_ADDRESS1')
		</label>

        <input type="text" name="address1" id="address1"
               value="{{{$field_data['address1']}}}"/>
        <p id="address1_empty" class="akeeba-help-text"
		   <?php if (strpos($group_classes['address1'], 'error') === false): ?>style="display:none"<?php endif ?>>
            @lang('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED')
        </p>
	</div>

	{{-- Address (cont) --}}
	<div class="akeeba-form-group">
		<label for="address2">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_ADDRESS2')
		</label>

        <input type="text" name="address2" id="address2"
               value="{{{$field_data['address2']}}}"/>
	</div>

	{{-- City --}}
	<div class="akeeba-form-group{{$group_classes['city']}}">
		<label for="city">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_CITY')
		</label>

        <input type="text" name="city" id="city"
               value="{{{$field_data['city']}}}"/>
        <p id="city_empty" class="akeeba-help-text"
		   <?php if (strpos($group_classes['city'], 'error') === false): ?>style="display:none"<?php endif ?>>
            @lang('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED')
        </p>
	</div>

	{{-- Zip --}}
	<div class="akeeba-form-group{{$group_classes['zip']}}">
		<label for="zip">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_ZIP')
		</label>

        <input type="text" name="zip" id="zip"
               value="{{{$field_data['zip']}}}"/>
        <p id="zip_empty" class="akeeba-help-text"
		   <?php if (strpos($group_classes['zip'], 'error') === false): ?>style="display:none"<?php endif ?>>
            @lang('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED')
        </p>
	</div>

	{{-- Per-subscription custom fields --}}
	@unless(is_null($akeebasubs_subscription_level))
	@include('site:com_akeebasubs/Level/default_persubscription', [
		'akeebasubs_subscription_level' => $akeebasubs_subscription_level,
		'apply_validation'              => $apply_validation
	])
	@endunless
</div>

<?php
$aks_validate_url  = JUri::base() . 'index.php';
$aks_noneuvat      = $this->container->params->get('noneuvat', 0) ? 'true' : 'false';
$script            = <<< JS

var akeebasubs_validate_url = "$aks_validate_url";
var akeebasubs_valid_form = false;
var akeebasubs_noneuvat = $aks_noneuvat;

JS;
$this->addJavascriptInline($script);
