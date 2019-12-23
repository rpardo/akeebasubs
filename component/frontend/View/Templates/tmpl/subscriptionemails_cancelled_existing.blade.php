<?php
/**
 * Akeeba Subscriptions – Subscription cancellation (miscellanous reason)
 *
 * This is fired when the cancellation reason is 'other'. That really only happens when I made a manual change and I
 * should be careful not to let that happen by always setting an appropriate reason.
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your [LEVEL] subscription at [SITENAME] was cancelled
@stop
@section('topic')
    Your [LEVEL] subscription has been cancelled.
@stop
@section('message')
    <p>
        Your subscription to [LEVEL] has been canceled. You no longer have access to the downloads and support that came
        with it.
    </p>
    <p>
        If you received an email from our reseller, Paddle, about a problem with your payment please contact them by
        replying to their email. They will let you know what the problem was. In most cases you can retry subscribing
        using a different means of payment, for example a different credit card or a PayPal account.
    </p>
    <p>
        If you had requested a refund, a subscription change, asked our staff to handle a payment issue or were
        otherwise notified about the cancellation of your subscription by our staff you can ignore this automatic email.
        If you have an open ticket regarding an issue that requires a subscription change or cancellation please wait
        about half an hour; you will receive a reply to your ticket explaining the actions taken.
    </p>
    <p>
        If you are not sure what is going on please <a href="https://www.akeebabackup.com/contact-us.html">contact
            us</a>. Kindly remember to include subscription reference #[SUB:ID] in your communication with us. Do not
        reply to this email directly; it is sent from an unmonitored email address.
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>.
    </p>
@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeebabackup.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you had a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop