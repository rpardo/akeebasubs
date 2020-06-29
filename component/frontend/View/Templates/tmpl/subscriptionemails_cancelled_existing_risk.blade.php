<?php
/**
 * Akeeba Subscriptions – Subscription cancellation (high risk transaction)
 *
 * We should never be sending this email. In these cases we go from N => X and we fire the cancelled_new email. If this
 * email is sent we have a problem with Paddle.
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Payment cancellation for the [LEVEL] subscription at [SITENAME]
@stop
@section('topic')
    Apologies. Your payment was declined.
@stop
@section('message')
    <p>
        Our reseller, Paddle, has flagged the transaction as unusual and declined to process it. Your subscription
        #[SUB:ID] to [LEVEL] is, therefore, unpaid and will remain inactive until this issue is rectified.
    </p>
    <p>
        You should have received an email from our reseller, Paddle, about the issue. If you want to learn more about
        the reason of your payment failure please reply to that email. If you cannot find that email please contact
        them at <a href="mailto:help@paddle.com">help@paddle.com</a> and tell them it's about the payment to Akeeba Ltd
        with passthrough reference [SUB:ID]. Do remember to include the email address you used to subscribe if it is
        different than the one you are emailing them from. They will be able to help you.
    </p>
    <p>
        You can always retry subscribing. You may want to select a different means of payment, for example a different
        credit card. We apologise for the inconvenience.
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>.
    </p>
@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeeba.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you tried to purchase a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop