<?php
/**
 * Akeeba Subscriptions – Subscription cancellation (recurring subscription is past due)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your [LEVEL] recurring subscription at [SITENAME] was cancelled
@stop
@section('topic')
    Your [LEVEL] recurring subscription has been cancelled.
@stop
@section('message')
    <p>
        Your recurring subscription to [LEVEL] has been canceled. You no longer have access to the downloads and support
        that came with it.
    </p>
    <p>
        The cancellation is the result of our reseller, Paddle Ltd, being unable to automatically charge you for the
        next installment of your subscription several times in a row.
    </p>
    <p>
        You have already received several emails from our reseller, Paddle, about the problem with your payment. If you
        need more information about it please contact them by replying to any of their emails.
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>.
    </p>
@stop
@section('announcement')
    <h3>Ready to re-subscribe?</h3>
    <p>
        We understand when life gets in the way. From lost credit cards and problems with banks to a client that hasn't
        paid you on time you might have had no choice than to let your subscription lapse.
    </p>
    <p>
        Whenever you're ready to come back please use <a href="[RENEWALURL:IWANTITALL]">special link</a> to resubscribe
        at the currently listed price for recurring subscriptions.
    </p>
@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeeba.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you had a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop