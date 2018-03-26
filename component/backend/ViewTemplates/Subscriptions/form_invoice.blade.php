<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\Invoices;

defined('_JEXEC') or die();

/** @var  \Akeeba\Subscriptions\Admin\Model\Subscriptions $model */

\JHtml::_('behavior.modal');

$invoice = $model->invoice;
$needsInvoice = is_null($invoice) || !($invoice instanceof Invoices) || empty($invoice->display_number);
$returnURL = 'index.php?option=com_akeebasubs&view=Subscriptions&task=edit&id=' . $model->akeebasubs_subscription_id;
?>

@if ($needsInvoice)
    <a class="akeeba-btn--large---primary"
       href="@route('index.php?option=com_akeebasubs&view=Invoices&task=generateForSubscription&id=' . (int) $model->akeebasubs_subscription_id . '&returnurl=' . base64_encode($returnURL))">
        <span class="akion-refresh"></span>
        @lang('COM_AKEEBASUBS_INVOICES_ACTION_REGENERATE')
    </a>
@else
    <h5>
        <a href="@route('index.php?option=com_akeebasubs&view=Invoices&akeebasubs_subscription_id=' . (int) $model->akeebasubs_subscription_id)"
           target="_blank">
            #{{{ $invoice->display_number }}}
        </a>
    </h5>

    <a class="akeeba-btn--grey modal"
       href="@route('index.php?option=com_akeebasubs&view=Invoices&task=read&id=' . (int) $model->akeebasubs_subscription_id . '&tmpl=component')"
       rel="{handler: 'iframe', size: {x: 800, y: 500}}">
        <span class="akion-document-text"></span>
		@lang('COM_AKEEBASUBS_INVOICES_ACTION_PREVIEW')
    </a>

    <a class="akeeba-btn--teal"
            href="@route('index.php?option=com_akeebasubs&view=Invoices&task=download&id=' . (int) $model->akeebasubs_subscription_id)">
        <span class="akion-android-download"></span>
		@lang('COM_AKEEBASUBS_INVOICES_ACTION_DOWNLOAD')
    </a>

@endif
