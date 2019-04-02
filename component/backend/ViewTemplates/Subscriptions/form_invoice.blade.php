<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Admin\Model\Invoices;

defined('_JEXEC') or die();

/** @var  \Akeeba\Subscriptions\Admin\Model\Subscriptions $model */

\JHtml::_('behavior.modal');

$invoice = $model->invoice;
$returnURL = 'index.php?option=com_akeebasubs&view=Subscriptions&task=edit&id=' . $model->akeebasubs_subscription_id;
?>

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
