<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\Subscriptions\Site\Model\Subscriptions;

defined('_JEXEC') or die();

/**
 * @var \Akeeba\Subscriptions\Site\View\Subscriptions\Html $this
 * @var array                                              $levelInfo
 */

$imageSize  = $this->container->params->get('summaryimages', 1);

/** @var \Akeeba\Subscriptions\Site\Model\Levels $level */
$level = $levelInfo['level'];
/** @var Subscriptions $lastSub */
$lastSub = $levelInfo['latest'];
// Do I have action buttons at the bottom?
$hasButtons = array_reduce($levelInfo['buttons'], function ($carry, $hasButton) {
	return $hasButton || $carry;
}, false);
/** @var Subscriptions|null $relatedSub */
$relatedSub = $levelInfo['related']['related_sub'];
/** @var \FOF30\Model\DataModel\Collection $allSubs */
$allSubs = $levelInfo['transactions'];

/** @var Subscriptions|null $unpaidSub */
$unpaidSub = (clone $allSubs)->filter(function (Subscriptions $sub) {
	return $sub->status == 'new';
})->first();

/** @var Subscriptions|null $pendingSub */
$pendingSub = (clone $allSubs)->filter(function (Subscriptions $sub) {
	return $sub->status == 'pending';
})->first();

$statusToColor = function(string $status): string {
	switch ($status)
	{
		case 'new':
			return 'red';
			break;

		case 'pending':
			return 'orange';
			break;

		default:
		case 'active':
		    return 'green';
			break;

		case 'waiting':
			return 'teal';
			break;

		case 'expired':
			return 'grey';
			break;

		case 'canceled':
			return 'info';
			break;
	}
};

?>

<div class="akeeba-panel--{{ $statusToColor($levelInfo['status']) }}">
    {{-- Header for the Level --}}
    <header class="akeeba-block-header mysubs-level-header">
        {{-- LEVEL IMAGE AND TITLE --}}
        <h3>
            @if ($imageSize && !empty($level->image))
                <img src="{{ \Joomla\CMS\Uri\Uri::base() }}{{ $level->image }}"
                     class="akeebasubs-subscription-level-image hasTooltip"
                     width="{{ (int)$imageSize }}px"
                     title="{{{ $level->title }}}" />
            @endif

            {{{ $level->title }}}
        </h3>

        {{-- SUBSCRIPTION STATUS AND EXPIRATION --}}
        <div class="akeebasubs-subscription-info">
            <span class="hasTooltip"
                  title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_' . $levelInfo['status'] . '_HELP')"
            >
                <strong>
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_' . $levelInfo['status'])
                </strong>
            </span>
            <span>
                @if (in_array($levelInfo['status'], ['canceled', 'expired']))
                    @sprintf(
                    'COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISHDATES_EXPIRED',
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($lastSub->publish_down)
                    )
                @elseif ($levelInfo['status'] == 'waiting')
                    @sprintf(
                    'COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISHDATES_RENEWAL',
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($lastSub->publish_up),
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($lastSub->publish_down)
                    )
                @elseif ($levelInfo['status'] == 'active')
                    @sprintf(
                    'COM_AKEEBASUBS_SUBSCRIPTIONS_PUBLISHDATES_ACTIVE',
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($lastSub->publish_down)
                    )
                @endif
            </span>
        </div>
    </header>

    {{-- Main Contents for the Level --}}
    <div>
        {{-- SUBSCRIPTION HAS BEEN DOWNGRADED --}}
        @if ($levelInfo['related']['status'] == 'downgrade')
            <p class="akeeba-block--info">
                @sprintf(
                    'COM_AKEEBASUBS_SUBSCRIPTIONS_ALREADYDOWNGRADED',
                    $relatedSub->getId(),
                    $relatedSub->level->title,
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($relatedSub->publish_up),
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($relatedSub->publish_down)
                )
            </p>
        {{--  SUBSCRIPTION HAS BEEN UPGRADED --}}
        @elseif ($levelInfo['related']['status'] == 'upgrade')
            <p class="akeeba-block--info">
                @sprintf(
                    'COM_AKEEBASUBS_SUBSCRIPTIONS_ALREADYUPGRADED',
                    $relatedSub->getId(),
                    $relatedSub->level->title,
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($relatedSub->publish_up),
                    \Akeeba\Subscriptions\Admin\Helper\Format::date($relatedSub->publish_down)
                )
            </p>
        {{-- PENDING PAYMENT --}}
        @elseif (!is_null($pendingSub))
            <div class="akeeba-block--warning">
                <h5>
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_PENDING_PAYMENT')
                </h5>
                <a href="@route('index.php?option=com_akeebasubs&view=Message&subid=' . $pendingSub->getId())"
                   class="akeeba-btn--grey">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_MORE_INFO')
                </a>
            </div>
        {{-- NOT YET PAID --}}
        @elseif (!is_null($unpaidSub))
            <div class="akeeba-block--warning">
                <h5>
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_UNPAID')
                </h5>
                <a href="@route('index.php?option=com_akeebasubs&view=Message&subid=' . $unpaidSub->getId())"
                   class="akeeba-btn--grey">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_MORE_INFO')
                </a>
            </div>
        @endif

        {{-- RECURRING SUBSCRIPTION MANAGEMENT --}}
        @if ($levelInfo['recurring']['is_recurring'])
        <div class="akeeba-block--success">
            <p>
                @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_RECURRING_INFO')
            </p>
            <p>
                <button class="akeeba-btn--teal"
                        onclick="Paddle.Checkout.open({override: '{{ $levelInfo['recurring']['update_url'] }}'});">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_BTN_UPDATE')
                </button>
                <button class="akeeba-btn--red--small"
                        onclick="Paddle.Checkout.open({override: '{{ $levelInfo['recurring']['cancel_url'] }}'});">
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_ACTION_CANCEL_RECURRING')
                </button>
            </p>
        </div>
        @endif

        {{-- TODO BILLING HISTORY --}}
        <h4>
            @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_HEAD_BILLINGHISTORY')
        </h4>
        @foreach ($allSubs as $sub)
        <?php /** @var Subscriptions $sub */ ?>
            <div class="akeeba-panel--info akeebasubs-subscription-container">
                <header class="akeebasubs-subscription-header akeeba-block-header">
                </header>
            </div>
        <!--
        <li>
            {{-- Subscription ID --}}
            #{{ $sub->getId() }}
            {{-- Status --}}
            @if ($sub->status == 'canceled')
                <span class="akeeba-label--{{ $statusToColor($sub->status) }} hasTooltip"
                      title="@lang('COM_AKEEBASUBS_SUBSCRIPTION_DETAILED_CANCELLATION_REASON_' . $sub->cancellation_reason)"
                >
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_' . $sub->status)
                </span>
            @else
                <span class="akeeba-label--{{ $statusToColor($sub->status) }} hasTooltip"
                      title="@lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_' . $sub->status . '_HELP')"
                >
                    @lang('COM_AKEEBASUBS_SUBSCRIPTIONS_AREAHEADING_' . $sub->status)
                </span>
            @endif
            {{-- Created on --}}
            {{ \Akeeba\Subscriptions\Admin\Helper\Format::date($lastSub->created_on) }}
            {{ $lastSub->processor}}
            {{ $lastSub->processor_key}}
        </li>
        -->
        @endforeach

        {{-- TODO BUTTONS --}}
        @if ($hasButtons)
        <h4>
            Further actions
        </h4>
        @endif

    </div>
</div>
