<?php
/**
 * Akeeba Subscriptions – New subscription
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your new [LEVEL] subscription at [SITENAME]
@stop
@section('topic')
    Thank you for your subscription!
@stop
@section('message')
    <p>
        Your [LEVEL] subscription is now activated and will remain active until [PUBLISH_DOWN].
    </p>
    <p>
        <strong>Important note</strong>: You may have to log out of our site and then log back in to have access to the
        software and services you paid for.
    </p>

    <h3>Invoice / receipt</h3>
    <p>
        You have already received a separate email from our reseller, Paddle, with your receipt / invoice. If you have
        not received it yet please check your spam folder. Alternatively, <a href="[SUB:RECEIPT_URL]">click here</a> to
        retrieve a copy.
    </p>

@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeebabackup.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you purchased a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop