<?php
/**
 * Akeeba Subscriptions – Subscription cancellation (full refund)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your subscription #[SUB:ID] at [SITENAME] has been refunded
@stop
@section('topic')
    Your subscription #[SUB:ID] has been refunded.
@stop
@section('message')
    <p>
        Your subscription #[SUB:ID] to [LEVEL] has been refunded.
    </p>
    <p>
        A refund request has been placed with our reseller, Paddle. You should see the refund shortly, typically within
        2 to 10 business days.
    </p>
    <p>
        If you had not requested a refund and you're not sure why you are receiving this email please
        <a href="https://www.akeeba.com/contact-us.html">contact us</a>.Kindly remember to include subscription
        reference #[SUB:ID] in your communication with us. Do not reply to this email directly; it is sent from an
        unmonitored email address.
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
    You are receiving this procedural email message because you had a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop