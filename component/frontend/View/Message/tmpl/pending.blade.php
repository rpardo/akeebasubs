<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
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
                Payment in dispute
            @else
                Payment awaiting clearance
            @endif
        </h3>
    </header>

    <p class="akeeba-block--warning">
        Your subscription is currently <strong>not</strong> active.
    </p>

    <h4>
        Why am I seeing this page?
    </h4>

    @if ($type == 'dispute')
        <p>
            You have filed a payment dispute. Until the dispute case is resolved your subscription will remain inactive, displayed as Pending. Your payment has <em>not</em> been refunded yet.
        </p>
        <p>
            If you have not filed a payment dispute; you did so in error; or need further information: please <a href="mailto:help@paddle.com">contact Paddle's Success Team</a>. Paddle is our reseller and handles all billing questions on our behalf.
        </p>
    @elseif ($type == 'risk')
        <p>
            Your payment has been flagged for manual review. You have not been charged any money yet.
        </p>
        <p>
            Typically, this process takes a few hours to a few days. You will be notified by email about the progress of the payment review.
        </p>
        <p>
            If it has been more than 7 business days or need further information please <a href="mailto:help@paddle.com">contact Paddle's Success Team</a>. Paddle is our reseller and handles all billing questions on our behalf.
        </p>
    @else
        <p>
            Your payment requires manual processing. This is necessary, for example, when you pay by wire transfer: someone needs to check that the transfer took place and manually mark the payment as successfully completed.
        </p>
        <p>
            This manual process is undertaken by Paddle, our reseller. It typically takes a few business days.
        </p>
        <p>
            If it has been more than 7 business days or need further information please <a href="mailto:help@paddle.com">contact Paddle's Success Team</a>. Paddle is our reseller and handles all billing questions on our behalf.
        </p>
    @endif

    <p>
        For faster and most accurate help with your billing enquiries we would like to kindly ask you to reply directly to the email you received from Paddle; it contains all the information they need to help you.
    </p>
</div>

