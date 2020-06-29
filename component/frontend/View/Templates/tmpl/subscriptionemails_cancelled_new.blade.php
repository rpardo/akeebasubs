<?php
/**
 * Akeeba Subscriptions – Payment cancellation before subscription activation
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Payment failure for the [LEVEL] subscription at [SITENAME]
@stop
@section('topic')
    Apologies. Your payment could not be processed.
@stop
@section('message')
    <p>
        Our reseller, Paddle, was ultimately unable to process your payment for the [LEVEL] subscription you tried to
        purchase on our site.
    </p>
    <p>
        You should have received an email from our reseller, Paddle, about the issue. If you want to learn more about
        the reason of your payment failure please reply to that email.
    </p>
    <p>
        You can always retry subscribing using the same username and email address. You may want
        to select a different means of payment, for example a different credit card. We apologise for the
        inconvenience.
    </p>
    <p>
        If you are not sure what is going on please <a href="https://www.akeeba.com/contact-us.html">contact
            us</a>. Kindly remember to include subscription reference #[SUB:ID] in your communication with us. Do not
        reply to this email directly; it is sent from an unmonitored email address.
    </p>
@stop
@section('email_reason')
    You are receiving this procedural email message because you tried to purchase a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop