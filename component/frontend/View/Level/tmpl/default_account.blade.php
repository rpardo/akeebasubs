<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

defined('_JEXEC') or die();

use Joomla\CMS\User\UserHelper;

$akeebasubs_subscription_level = isset($this->item) ? $this->item->akeebasubs_level_id : null;
$apply_validation              = isset($this->apply_validation) ? ($this->apply_validation == 'true') : true;
$field_data = [
        'name'         => $this->getFieldValue('name'),
        'email'        => $this->getFieldValue('email'),
        'email2'       => $this->getFieldValue('email2'),
];

$hasErrors                 = [
	'username'     => false,
	'password'     => false,
	'password2'    => false,
	'name'         => !$this->validation->validation->name,
	'email'        => !$this->validation->validation->email,
	'email2'       => !$this->validation->validation->email2,
];

if ($this->container->platform->getUser()->guest)
{
	$hasErrors['username']  = ! (!$apply_validation || $this->validation->validation->username);
	$hasErrors['password']  = empty($this->cache['password']);
	$hasErrors['password2'] = empty($this->cache['password2']) || ($this->cache['password'] != $this->cache['password2']);
}

// If the email is wrong don't show an additional error for the "repeat email" field
if ($hasErrors['email'] && $hasErrors['email2'])
{
	$hasErrors['email2'] = false;
}

// If the password is wrong don't show an additional error for the "repeat password" field
if ($hasErrors['password'] && $hasErrors['password2'])
{
	$hasErrors['password2'] = false;
}

$returnURI = JUri::getInstance();
$returnURI->setVar('reset', 1);
?>
@js('media://com_akeebasubs/js/signup.js')

@if ($this->container->platform->getUser()->guest)
<div id="akeebasubs-panel-account" class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_LOGINHEADER')
		</h3>
	</header>

	<div>
		{{-- SOCIAL LOGIN --}}
		@if (class_exists('Akeeba\SocialLogin\Library\Helper\Integrations'))
			<?php
			$this->getContainer()->platform->importPlugin('sociallogin');
			$buttonDefinitions = $this->getContainer()->platform->runPlugins('onSocialLoginGetLoginButton', [null, null]);
			?>
			@unless(!count($buttonDefinitions))
			<div id="akeebasubs-level-login" class="akeeba-form-group--pull-right">
				<div class="akeeba-form-group--actions">
					@foreach ($buttonDefinitions as $button)
						<a class="akeeba-btn--grey akeeba-sociallogin-button akeeba-sociallogin-button-{{{ $button['slug'] }}} hasTooltip"
						id="{{{ 'akeeba-sociallogin-' . UserHelper::genRandomPassword(12) . '-' . UserHelper::genRandomPassword(8) }}}"
						href="{{ $button['link'] }}"
					    title="{{ $button['tooltip'] }}">
							@unless(empty($button['icon_class']))
							<span class="{{ $button['icon_class'] }}"></span>
							@else
							{{ $button['img'] }}
							@endunless
							{{ $button['label'] }}
						</a>
					@endforeach
				</div>
			</div>
			<div id="akeebasubs-level-login" class="akeeba-form-group--pull-right">
					<span class="akeeba-help-text">
						@lang('COM_AKEEBASUBS_LEVEL_LBL_SOCIALHINT')
					</span>
			</div>
			@endunless
		@endif

		{{-- Login button --}}
		<div id="akeebasubs-level-login" class="akeeba-form-group--pull-right">
			<div class="akeeba-form-group--actions">
				<a href="@route('index.php?option=com_users&view=login&return=' . base64_encode($returnURI->toString())))"
				   class="akeeba-btn--primary" rel="nofollow,noindex">
					<span class="glyphicon glyphicon-log-in"></span>
					@lang('COM_AKEEBASUBS_LEVEL_BTN_LOGINIFALERADY')
				</a>
				<span class="akeeba-help-text">
					@lang('COM_AKEEBASUBS_LEVEL_LBL_CREATEACCOUNTHINT')
				</span>
			</div>
		</div>
	</div>
</div>
<div id="akeebasubs-panel-account" class="akeeba-panel--info">
	<header class="akeeba-block-header">
		<h3>
			@lang('COM_AKEEBASUBS_LEVEL_LBL_ACCOUNTHEADER')
		</h3>
	</header>


	<div class="akeeba-form--horizontal akeebasubs-signup-fields">

		{{-- Full name --}}
		<div class="akeeba-form-group{{{$hasErrors['name'] ? '--error' : ''}}}">
			<label for="name">
				@lang('COM_AKEEBASUBS_LEVEL_FIELD_NAME')
			</label>

			<input type="text" name="name" id="name"
				   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_NAME')"
				   value="{{{$field_data['name']}}}"/>

			<p id="name_empty" class="akeeba-help-text"
			   {{ $hasErrors['name'] ? '' : 'style="display:none"' }}>
				@lang('COM_AKEEBASUBS_LEVEL_ERR_NAME_INVALID')
			</p>
		</div>

		{{-- Username --}}
		<div class="akeeba-form-group{{{$hasErrors['username'] ? '--error' : ''}}}">
			<label for="username">
				@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME')
			</label>

			<input type="text" name="username" id="username"
				   placeholder="@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME')"
				   value="{{{$this->cache['username']}}}"/>

			<p id="username_invalid" class="akeeba-help-text"
					{{ $hasErrors['username'] ? '' : 'style="display:none"' }}>
				@lang('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME_INVALID')
			</p>
		</div>

		{{-- Password --}}
		<div class="akeeba-form-group{{{($hasErrors['password'] || $hasErrors['password2']) ? '--error' : ''}}}">
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
				{{ $hasErrors['password'] ? '' : 'style="display:none"' }}>
				@lang('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD_EMPTY')
			</p>
			<p id="password2_invalid" class="akeeba-help-text"
				{{ $hasErrors['password2'] ? '' : 'style="display:none"' }}>
				@lang('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD2')
			</p>
		</div>

		{{-- Email --}}
		<div class="akeeba-form-group{{{($hasErrors['email'] || $hasErrors['email2']) ? '--error' : ''}}}">
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
				{{ $hasErrors['email'] ? '' : 'style="display:none"' }}>
				@lang('COM_AKEEBASUBS_LEVEL_ERR_EMAIL')
			</p>

			<p id="email2_invalid" class="akeeba-help-text"
				{{ $hasErrors['email2'] ? '' : 'style="display:none"' }}>
				@lang('COM_AKEEBASUBS_LEVEL_ERR_EMAIL2')
			</p>
		</div>
	</div>
</div>
@endif