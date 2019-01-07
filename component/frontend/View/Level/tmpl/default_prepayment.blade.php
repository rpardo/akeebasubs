<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Level\Html $this */
/** @var array $field */

$this->getContainer()->platform->importPlugin('akeebasubs');
$jResponse = $this->getContainer()->platform->runPlugins('onSubscriptionFormPrepaymentRender', [
				$this->userparams,
				array_merge($this->cache, ['subscriptionlevel' => $this->item->akeebasubs_level_id])
			]);

if (!is_array($jResponse) || empty($jResponse)) return;
?>
@repeatable('customField', $field)
<?php
$field['label'] = (trim($field['label']) == '*') ? '' : $field['label'];
$field['isValid']  = array_key_exists('isValid', $field) ? $field['isValid'] : true;
$customField_class = '';

if ($this->apply_validation == 'true')
{
	$customField_class = $field['isValid'] ? '' : '--error';
}
?>
<div class="akeeba-form-group{{{$customField_class}}}">
	<label for="{{{$field['id']}}}">
		{{$field['label']}}
	</label>

	{{$field['elementHTML']}}
</div>
@endRepeatable

@foreach($jResponse as $customFields)
	@if (is_array($customFields) && !empty($customFields))
		@foreach($customFields as $field)
			@yieldRepeatable('customField', $field)
		@endforeach
	@endif
@endforeach
