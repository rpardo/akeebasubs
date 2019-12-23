<?php
/**
 * Akeeba Subscriptions – Early renewal
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your [LEVEL] subscription renewal at [SITENAME]
@stop
@section('topic')
    Thank you for your loyalty!
@stop
@section('message')
    <p>
        We confirm that we have successfully received the payment for your [LEVEL] subscription renewal.
    </p>
    <p>
        The renewed subscription will start counting when your previous [LEVEL] subscription was set to expire, namely on [PUBLISH_UP]. It will be active until [PUBLISH_DOWN]. This way you did not lose any subscription time by renewing early <em>and</em> you go to pay less. Awesome!
    </p>
    <p>
        We will send you an email around [PUBLISH_UP] to remind you that your renewal becomes active.
    </p>
    <p>
        Once again, thank you for being with us! It is very much appreciated.
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>
    </p>

    <h3>Invoice / receipt</h3>
    <p>
        You have already received a separate email from our reseller, Paddle, with your receipt / invoice. If you have
        not received it yet please check your spam folder. Alternatively, <a href="[SUB:RECEIPT_URL]">click here</a> to
        retrieve a copy.
    </p>
@stop
@section('email_reason')
    You are receiving this procedural email message because you bought a subscription on <em>[SITENAME]</em>.
@stop