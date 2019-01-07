<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Helper\Select;
use FOF30\Utils\FEFHelper\BrowseView;

defined('_JEXEC') or die();

/** @var  FOF30\View\DataView\Html  $this */

?>
@extends('admin:com_akeebasubs/Common/edit')

@section('edit-form-body')
	<div class="akeeba-form-group">
		<label for="key">
			@fieldtitle('key')
		</label>
		{{ BrowseView::genericSelect('key', \Akeeba\Subscriptions\Admin\Helper\Email::getEmailKeys(1), $this->getItem()->key, ['fof.autosubmit' => false, 'translate' => false]) }}
	</div>

	<div class="akeeba-form-group">
		<label for="language">
			@fieldtitle('language')
		</label>
		{{ BrowseView::genericSelect('language', \FOF30\Utils\SelectOptions::getOptions('languages', ['none' => 'COM_AKEEBASUBS_EMAILTEMPLATES_FIELD_LANGUAGE_ALL']), $this->getItem()->language, ['fof.autosubmit' => false, 'translate' => false]) }}
	</div>

	<div class="akeeba-form-group">
		<label for="subscription_level_id">
			@fieldtitle('subscription_level_id')
		</label>
		<?php echo BrowseView::modelSelect('subscription_level_id', 'Levels', $this->getItem()->subscription_level_id, ['fof.autosubmit' => false, 'none' => 'COM_AKEEBASUBS_EMAILTEMPLATES_FIELD_SUBSCRIPTION_LEVEL_ID_NONE', 'translate' => false]) ?>
	</div>

	<div class="akeeba-form-group">
		<label for="enabled">
			@lang('JPUBLISHED')
		</label>
		@jhtml('FEFHelper.select.booleanswitch', 'enabled', $this->getItem()->enabled)
	</div>

	<div class="akeeba-form-group">
		<label for="subject">
			@fieldtitle('subject')
		</label>
		<input type="text" name="subject" id="subject" value="{{{ $this->getItem()->subject }}}" />
	</div>

	<div class="akeeba-form-group">
		<label for="body">
			@fieldtitle('body')
		</label>
		<div class="akeeba-nofef">
			@jhtml('FEFHelper.edit.editor', 'body', $this->getItem()->body)
		</div>
	</div>
@stop
