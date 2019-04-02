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
        'country'      => $this->getFieldValue('country', ['XX']),
];

$group_classes                 = [
	'username'     => '',
	'password'     => '',
	'password2'    => '',
	'name'         => $this->validation->validation->name ? '' : '--error',
	'email'        => $this->validation->validation->email ? '' : '--error',
	'email2'       => $this->validation->validation->email2 ? '' : '--error',
	'country'      => $this->validation->validation->country ? '' : '--error',
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

	@if ($this->container->platform->getUser()->guest)
	<h3>@lang('COM_AKEEBASUBS_LEVEL_USERACCOUNT')</h3>

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

	{{-- Full name --}}
	<div class="akeeba-form-group{{{$group_classes['name']}}}">
		<label for="name">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_NAME')
		</label>

		<input type="text" name="name" id="name"
			   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_NAME')"
			   value="{{{$field_data['name']}}}"/>

		<p id="name_empty" class="akeeba-help-text"
		   <?php if (strpos($group_classes['name'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_NAME_INVALID')
		</p>
	</div>

	{{-- Username --}}
	<div class="akeeba-form-group{{$group_classes['username']}}">
		<label for="username">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME')
		</label>

		<input type="text" name="username" id="username"
			   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME')"
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
		<label for="password2" aria-hidden="false" style="display: none;">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_PASSWORD2')
		</label>

		<div class="akeeba-form-group--radio">
			<input type="password" name="password" id="password"
				   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_PASSWORD')"
				   value="{{{$this->cache['password']}}}"/>
			<input type="password" name="password2" id="password2"
				   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_PASSWORD2')"
				   value="{{{$this->cache['password2']}}}"/>
		</div>

		<p id="password_invalid" class="akeeba-help-text"
		   style="<?php if (strpos($group_classes['password'], 'error') === false): ?>display:none<?php endif; ?>">
			@lang('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD_EMPTY')
		</p>
		<p id="password2_invalid" class="help-block"
		   style="<?php if (strpos($group_classes['password2'], 'error') === false): ?>display:none<?php endif; ?>">
			@lang('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD2')
		</p>
	</div>

	{{-- Email --}}
	<div class="akeeba-form-group{{$group_classes['email']}}">
		<label for="email">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL')
		</label>
		<label for="email2" aria-hidden="false" style="display: none;">
			@lang('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL2')
		</label>

		<div class="akeeba-form-group--radio">
			<input type="text" name="email" id="email"
				   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL')"
				   value="{{{$field_data['email']}}}"/>

			<input type="text" name="email2" id="email2"
				   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL2')"
				   value="{{{$field_data['email2']}}}"/>
		</div>

		<p id="email_invalid" class="akeeba-help-text"
		   <?php if (strpos($group_classes['email'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_EMAIL')
		</p>

		<p id="email2_invalid" class="akeeba-help-text"
		   <?php if (strpos($group_classes['email2'], 'error') === false): ?>style="display:none"<?php endif ?>>
			@lang('COM_AKEEBASUBS_LEVEL_ERR_EMAIL2')
		</p>
	</div>

	@endif

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
</div>

<?php
$aks_validate_url  = JUri::base() . 'index.php';
$script            = <<< JS

var akeebasubs_validate_url = "$aks_validate_url";
var akeebasubs_valid_form = false;

JS;
$this->addJavascriptInline($script);
