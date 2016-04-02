<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

defined('_JEXEC') or die();

use Akeeba\Subscriptions\Admin\Helper\Select;

$akeebasubs_subscription_level = isset($this->item) ? $this->item->akeebasubs_level_id : null;
$apply_validation              = isset($this->apply_validation) ? ($this->apply_validation == 'true') : true;
$field_data                    = [
	'name'         => !empty($this->userparams->name) ? $this->userparams->name : $this->cache['name'],
	'email'        => !empty($this->userparams->email) ? $this->userparams->email : $this->cache['email'],
	'email2'       => !empty($this->userparams->email2) ? $this->userparams->email2 : $this->cache['email2'],
	'address1'     => !empty($this->userparams->address1) ? $this->userparams->address1 : $this->cache['address1'],
	'address2'     => !empty($this->userparams->address2) ? $this->userparams->address2 : $this->cache['address2'],
	'city'         => !empty($this->userparams->city) ? $this->userparams->city : $this->cache['city'],
	'state'        => !empty($this->userparams->state) ? $this->userparams->state : $this->cache['state'],
	'zip'          => !empty($this->userparams->zip) ? $this->userparams->zip : $this->cache['zip'],
	'country'      => !empty($this->userparams->country) && ($this->userparams->country != 'XX') ?
		$this->userparams->country : $this->cache['country'],
	'businessname' => !empty($this->userparams->businessname) ? $this->userparams->businessname :
		$this->cache['businessname'],
	'occupation'   => !empty($this->userparams->occupation) ? $this->userparams->occupation :
		$this->cache['occupation'],
	'vatnumber'    => !empty($this->userparams->vatnumber) ? $this->userparams->vatnumber : $this->cache['vatnumber'],
];
$group_classes                 = [
	'username'     => '',
	'password'     => '',
	'password2'    => '',
	'name'         => $this->validation->validation->name ? '' : 'error has-error',
	'email'        => $this->validation->validation->email ? '' : 'error has-error',
	'email2'       => $this->validation->validation->email2 ? '' : 'error has-error',
	'address1'     => $this->validation->validation->address1 ? '' : 'error has-error',
	'city'         => $this->validation->validation->city ? '' : 'error has-error',
	'state'        => $this->validation->validation->state ? '' : 'error has-error',
	'zip'          => $this->validation->validation->zip ? '' : 'error has-error',
	'country'      => $this->validation->validation->country ? '' : 'error has-error',
	'businessname' => $this->validation->validation->businessname ? '' : 'error has-error',
	'occupation'   => !empty($field_data['occupation']) ? '' : 'error has-error',
	'vatnumber'    => $this->validation->validation->vatnumber ? '' : 'warning has-warning',
];

if (JFactory::getUser()->guest)
{
	$group_classes['username']  = ($this->cache['username']) ?
		(($this->validation->validation->username) ? 'success has-success' : 'error has-error') : '';
	$group_classes['password']  = !$this->cache['password'] ? 'error has-error' : '';
	$group_classes['password2'] =
		(!$this->cache['password2'] || ($this->cache['password2'] != $this->cache['password'])) ? 'error has-error' :
			'';
}
?>
@js('media://com_akeebasubs/js/signup.js')
@js('media://com_akeebasubs/js/autosubmit.js')

<div class="form form-horizontal akeebasubs-signup-fields">

	<fieldset>
		<?php if (JFactory::getUser()->guest): ?>
			<legend><?php echo JText::_('COM_AKEEBASUBS_LEVEL_NEWACCOUNT') ?></legend>

			{{-- Login --}}
			@if(JFactory::getUser()->guest)
			<div class="akeebasubs-level-login">
				<a href="@route('index.php?option=com_users&task=user.login&return=' . base64_encode(JUri::getInstance()->toString())))"
				   class="btn btn-default btn-inverse" rel="nofollow,noindex">
					<span class="glyphicon glyphicon-log-in"></span>
					@lang('COM_AKEEBASUBS_LEVEL_BTN_LOGINIFALERADY')
				</a>
			</div>
			@endif

			<div class="form-group <?php echo $group_classes['username'] ?>">
				<label for="username" class="control-label col-sm-4">
					* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME') ?>
				</label>

				<div class="col-sm-8">
					<input type="text" class="form-control" name="username" id="username"
						   value="<?php echo $this->escape($this->cache['username']) ?>"/>
					<p id="username_valid" class="help-block"
					   <?php if (strpos($group_classes['username'], 'success') == false): ?>style="display:none"<?php endif ?>>
						<?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME_VALID') ?>
					</p>
					<p id="username_invalid" class="help-block"
					   <?php if (strpos($group_classes['username'], 'error') === false): ?>style="display:none"<?php endif ?>>
						<?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME_INVALID') ?>
					</p>
				</div>
			</div>

			<div class="form-group <?php echo $group_classes['password'] ?>">
				<label for="password" class="control-label col-sm-4">
					* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_PASSWORD') ?>
				</label>

				<div class="col-sm-8">
					<input type="password" class="form-control" name="password" id="password"
						   value="<?php echo $this->escape($this->cache['password']) ?>"/>
					<p id="password_invalid" class="help-block"
					   style="<?php if (strpos($group_classes['password'], 'error') === false): ?>display:none<?php endif; ?>">
						<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD_EMPTY') ?>
					</p>
				</div>
			</div>

			<div class="form-group <?php echo $group_classes['password2'] ?>">
				<label for="password2" class="control-label col-sm-4">
					* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_PASSWORD2') ?>
				</label>

				<div class="col-sm-8">
					<input type="password" class="form-control" name="password2" id="password2"
						   value="<?php echo $this->escape($this->cache['password2']) ?>"/>
					<p id="password2_invalid" class="help-block"
					   style="<?php if (strpos($group_classes['password2'], 'error') === false): ?>display:none<?php endif; ?>">
						<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_PASSWORD2') ?>
					</p>
				</div>
			</div>

		<?php else: ?>

			<legend><?php echo JText::_('COM_AKEEBASUBS_LEVEL_EXISTINGACCOUNT') ?></legend>

			<div class="form-group">
				<label for="username" class="control-label col-sm-4">
					* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_USERNAME') ?>
				</label>

				<div class="col-sm-8">
					<input type="text" class="form-control" name="username" id="username" disabled="disabled"
						   value="<?php echo $this->escape($this->userparams->username) ?>"/>
				</div>
			</div>
			<br/>
		<?php endif; ?>

	</fieldset>

	<fieldset>

		<div class="form-group <?php echo $group_classes['name'] ?>">
			<label for="name" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_NAME') ?>
			</label>

			<div class="col-sm-8">
				<input type="text" class="form-control" name="name" id="name"
					   value="<?php echo $this->escape($field_data['name']); ?>"/>
				<p id="name_empty" class="help-block"
				   <?php if (strpos($group_classes['name'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_NAME_INVALID') ?>
				</p>
			</div>
		</div>

		<div class="form-group <?php echo $group_classes['email'] ?>">
			<label for="email" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL') ?>
			</label>

			<div class="col-sm-8">
				<input type="text" class="form-control" name="email" id="email"
					   value="<?php echo $this->escape($field_data['email']); ?>"/>
				<p id="email_invalid" class="help-block"
				   <?php if (strpos($group_classes['email'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_EMAIL') ?>
				</p>
			</div>
		</div>

		<div class="form-group <?php echo $group_classes['email2'] ?>">
			<label for="email2" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_EMAIL2') ?>
			</label>

			<div class="col-sm-8">
				<input type="text" class="form-control" name="email2" id="email2"
					   value="<?php echo $this->escape($field_data['email2']); ?>"/>
				<p id="email2_invalid" class="help-block"
				   <?php if (strpos($group_classes['email2'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_EMAIL2') ?>
				</p>
			</div>
		</div>

		<div class="form-group <?php echo $group_classes['address1'] ?>">
			<label for="address1" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_ADDRESS1') ?>
			</label>

			<div class="col-sm-8">
				<input type="text" class="form-control" name="address1" id="address1"
					   value="<?php echo $this->escape($field_data['address1']); ?>"/>
				<p id="address1_empty" class="help-block"
				   <?php if (strpos($group_classes['address1'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED') ?>
				</p>
			</div>
		</div>

		<div class="form-group">
			<label for="address2" class="control-label col-sm-4">
				<?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_ADDRESS2') ?>
			</label>

			<div class="col-sm-8">
				<input type="text" class="form-control" name="address2" id="address2"
					   value="<?php echo $this->escape($field_data['address2']); ?>"/>
			</div>
		</div>

		<div class="form-group <?php echo $group_classes['city'] ?>">
			<label for="city" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_CITY') ?>
			</label>

			<div class="col-sm-8">
				<input type="text" class="form-control" name="city" id="city"
					   value="<?php echo $this->escape($field_data['city']); ?>"/>
				<p id="city_empty" class="help-block"
				   <?php if (strpos($group_classes['city'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED') ?>
				</p>
			</div>
		</div>

		<div class="form-group <?php echo $group_classes['city'] ?>" id="stateField">
			<label for="state" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_STATE') ?>
			</label>

			<div class="col-sm-8">
				<?php echo Select::states($field_data['state'], 'state', array('class' => 'form-control advancedSelect')) ?>
				<p id="state_empty" class="help-block"
				   <?php if (strpos($group_classes['city'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED') ?>
				</p>
			</div>
		</div>

		<div class="form-group <?php echo $group_classes['zip'] ?>">
			<label for="zip" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_ZIP') ?>
			</label>

			<div class="col-sm-8">
				<input type="text" class="form-control" name="zip" id="zip"
					   value="<?php echo $this->escape($field_data['zip']); ?>"/>
				<p id="zip_empty" class="help-block"
				   <?php if (strpos($group_classes['zip'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED') ?>
				</p>
			</div>
		</div>

		<div class="form-group <?php echo $group_classes['country'] ?>">
			<label for="country" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_COUNTRY') ?>
			</label>

			<div class="col-sm-8">
				<?php echo Select::countries($field_data['country'], 'country', array('class' => 'form-control advancedSelect')) ?>
				<p id="country_empty" class="help-block"
				   <?php if (strpos($group_classes['country'], 'error') === false): ?>style="display:none"<?php endif ?>>
					<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED') ?>
				</p>
			</div>
		</div>

	</fieldset>
	<fieldset>

		<legend><?php echo JText::_('COM_AKEEBASUBS_LEVEL_INVOICINGPREFS') ?></legend>

		<?php
		$isBusiness = !empty($this->userparams->isbusiness) ? $this->userparams->isbusiness :
			(@array_key_exists('isbusiness', $this->cache) ? $this->cache['isbusiness'] : 0);
		$style      = '';
		?>

		<div class="form-group" style="<?php echo $style ?>">
			<label for="isbusiness" class="control-label col-sm-4">
				* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_ISBUSINESS') ?>
			</label>
			<span class="col-sm-2">
				<?php echo JHtml::_('select.genericlist', [
					JHtml::_('select.option', '0', JText::_('JNO')),
					JHtml::_('select.option', '1', JText::_('JYES'))
				], 'isbusiness', ['class' => 'form-control'], 'value', 'text', $isBusiness, 'isbusiness'); ?>
			</span>
		</div>

		<div id="businessfields">
			<div class="form-group <?php echo $group_classes['businessname'] ?>">
				<label for="businessname" class="control-label col-sm-4">
					* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_BUSINESSNAME') ?>
				</label>

				<div class="col-sm-8">
					<input type="text" class="form-control" name="businessname" id="businessname"
						   value="<?php echo $this->escape($field_data['businessname']); ?>"/>
					<p id="businessname_empty" class="help-block"
					   <?php if (strpos($group_classes['businessname'], 'error') === false): ?>style="display:none"<?php endif ?>>
						<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED') ?>
					</p>
				</div>
			</div>

			<div class="form-group <?php echo $group_classes['occupation'] ?>">
				<label for="occupation" class="control-label col-sm-4">
					* <?php echo JText::_('COM_AKEEBASUBS_LEVEL_FIELD_OCCUPATION') ?>
				</label>

				<div class="col-sm-8">
					<input type="text" class="form-control" name="occupation" id="occupation"
						   value="<?php echo $this->escape($field_data['occupation']); ?>"/>
					<p id="occupation_empty" class="help-block"
					   <?php if (strpos($group_classes['occupation'], 'error') === false): ?>style="display:none"<?php endif ?>>
						<?php echo JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED') ?>
					</p>
				</div>
			</div>

			<div class="form-group <?php echo $group_classes['vatnumber'] ?>" id="vatfields">
				<label for="vatnumber" class="control-label col-sm-4" id="vatlabel">
					* <?php echo $this->container->params->get('noneuvat', 0) ?
						JText::_('COM_AKEEBASUBS_LEVEL_FIELD_VATNUMBER_ALTLABEL') :
						JText::_('COM_AKEEBASUBS_LEVEL_FIELD_VATNUMBER') ?>
				</label>

				<div class="col-sm-8">
						<span class="input-group">
							<span class="input-group-addon" id="vatcountry">EU</span>
							<input type="text" name="vatnumber" id="vatnumber" class="form-control"
								   size="16"
								   value="<?php echo $this->escape($field_data['vatnumber']); ?>"/>
						</span>
						<p id="vat-status-invalid" class="help-block"
							  <?php if (strpos($group_classes['vatnumber'], 'warning') === false): ?>style="display:none"<?php endif ?>>
							<?php echo JText::_('COM_AKEEBASUBS_LEVEL_VAT_INVALID') ?>
						</p>
						<p id="vat-status-valid" class="help-block"
							  <?php if (strpos($group_classes['vatnumber'], 'success') === false): ?>style="display:none"<?php endif ?>>
							<?php echo JText::_('COM_AKEEBASUBS_LEVEL_VAT_VALID') ?>
						</p>
				</div>
			</div>

		</div>

		<?php
		// Render per-subscription fields, only when we have a valid subscription level!
		if (!is_null($akeebasubs_subscription_level)):
		$this->getContainer()->platform->importPlugin('akeebasubs');

		$jResponse = $this->getContainer()->platform->runPlugins(
			'onSubscriptionFormRenderPerSubFields',
			array(array_merge($this->cache, array('subscriptionlevel' => $akeebasubs_subscription_level)))
		);

		ob_start();

		if (is_array($jResponse) && !empty($jResponse))
		{
			foreach ($jResponse as $customFields):
				if (is_array($customFields) && !empty($customFields))
				{
					foreach ($customFields as $field):
						if ($apply_validation && array_key_exists('isValid', $field))
						{
							$customField_class = $field['isValid'] ?
								(array_key_exists('validLabel', $field) ? 'success has-success' : '') :
								'error has-error';
						}
						else
						{
							$customField_class = '';
						}
						?>
						<div class="form-group <?php echo $customField_class ?>">
							<label for="<?php echo $field['id'] ?>" class="control-label col-sm-4">
								<?php echo $field['label'] ?>
							</label>

							<div class="col-sm-8">
								<?php echo $field['elementHTML'] ?>

								<?php if (array_key_exists('validLabel', $field)): ?>
								<p id="<?php echo $field['id'] ?>_valid" class="help-block"
								   style="<?php if (!$field['isValid'] || !$apply_validation): ?>display:none<?php endif ?>">
									<?php echo $field['validLabel'] ?>
								</p>
								<?php endif; ?>

								<?php if (array_key_exists('invalidLabel', $field)): ?>
								<p id="<?php echo $field['id'] ?>_invalid" class="help-block"
								   style="<?php if ($field['isValid'] || !$apply_validation): ?>display:none<?php endif ?>">
									<?php echo $field['invalidLabel'] ?>
								</p>
								<?php endif; ?>
							</div>
						</div>

						<?php
					endforeach;
				}
			endforeach;
		}
		$subfieldsHTML = trim(@ob_get_clean());
		if (!empty($subfieldsHTML)): ?>
	</fieldset>
	<fieldset>

		<legend><?php echo JText::_('COM_AKEEBASUBS_LEVEL_PERSUBFIELDS') ?></legend>
		<?php echo $subfieldsHTML ?>
		<?php
		endif;
		endif;
		?>

	</fieldset>
</div>

<?php
$aks_validate_url  = JUri::base() . 'index.php';
$aks_noneuvat      = $this->container->params->get('noneuvat', 0) ? 'true' : 'false';
$script            = <<< JS

var akeebasubs_validate_url = "$aks_validate_url";
var akeebasubs_valid_form = false;
var akeebasubs_noneuvat = $aks_noneuvat;

JS;
JFactory::getDocument()->addScriptDeclaration($script);