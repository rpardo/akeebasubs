<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Subscriptions\Site\View\Message\Html $this */

$params = $this->subscription->params;

if (isset($params['dispute']) && $params['dispute'])
{
    $type = 'dispute';
}
elseif (isset($params['risk_case_id']))
{
    $type = 'risk';
}
else
{
    $type = 'delay';
}

?>

<div class="akeeba-panel--orange">
    <header class="akeeba-block-header">
        <h3>
            @sprintf('Subscription #%s &mdash; %s', $this->subscription->getId(), $this->subscription->level->title)
            &mdash;
            @if ($type == 'dispute')
                @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HEAD_LABEL_DISPUTE')
            @else
                @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HEAD_LABEL')
            @endif
        </h3>
    </header>

    <p class="akeeba-block--warning">
        @lang('COM_AKEEBASUBS_MESSAGE_PENDING_TOP_DETAIL')
    </p>

    <h4>
        @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HEAD_WHY')
    </h4>

    @if ($type == 'dispute')

        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_DISPUTE_P1')
        </p>
        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_DISPUTE_P2')
        </p>

    @elseif ($type == 'risk')
        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_RISK_P1')
        </p>
        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_RISK_P2')
        </p>
        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_RISK_P3')
        </p>
    @else
        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_PENDING_P1')
        </p>
        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_PENDING_P2')
        </p>
        <p>
            @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_PENDING_P3')
        </p>
    @endif

    <p>
        @lang('COM_AKEEBASUBS_MESSAGE_PENDING_HELP_FOOTER')
    </p>
</div>

