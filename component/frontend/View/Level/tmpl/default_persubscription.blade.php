<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var  int  $akeebasubs_subscription_level  Subscription level ID */
/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */

$this->getContainer()->platform->importPlugin('akeebasubs');
$jResponse = $this->getContainer()->platform->runPlugins(
		'onSubscriptionFormRenderPerSubFields',
		[array_merge($this->cache, ['subscriptionlevel' => $akeebasubs_subscription_level])]
);

$hasPerSubFields = false;

if (is_array($jResponse) && !empty($jResponse))
{
	foreach ($jResponse as $customFields)
	{
		if (is_array($customFields) && !empty($customFields))
		{
			$hasPerSubFields = true;
			break;
		}
	}
}
?>

@if($hasPerSubFields)
	<h3>@lang('COM_AKEEBASUBS_LEVEL_PERSUBFIELDS')</h3>

	@foreach ($jResponse as $customFields)
		@foreach ($customFields as $field)
			<?php
			$customField_class = '';
			if ($apply_validation && array_key_exists('isValid', $field))
			{
				$customField_class = (array_key_exists('validLabel', $field) ? '--success' : '');
				$customField_class = $field['isValid'] ? $customField_class : '--error';
			}
			?>
			<div class="akeeba-form-group{{$customField_class}}">
				<label for="{{$field['id']}}">
					{{$field['label']}}
				</label>

				{{$field['elementHTML']}}

				@if (array_key_exists('validLabel', $field))
					<p id="{{$field['id']}}_valid" class="akeeba-help-text"
					   style="<?php if (!$field['isValid'] || !$apply_validation): ?>display:none<?php endif ?>">
						{{$field['validLabel']}}
					</p>
				@endif

				@if (array_key_exists('invalidLabel', $field))
					<p id="{{$field['id']}}_invalid" class="akeeba-help-text"
					   style="<?php if ($field['isValid'] || !$apply_validation): ?>display:none<?php endif ?>">
						{{$field['invalidLabel']}}
					</p>
				@endif
			</div>
		@endforeach
	@endforeach
@endif
