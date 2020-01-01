<?php
/**
 * Akeeba Subscriptions – Miscellaneous change
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your subscription #[SUB:ID] has been modified.
@stop
@section('topic')
    Your subscription #[SUB:ID] has been modified.
@stop
@section('message')
    <p>
        Its new details are:
    </p>
    <dl>
        <dt>Subscription type</dt>
        <dd>[LEVEL]</dd>

        <dt>Subscription status</dt>
        <dd>[ENABLED]</dd>

        <dt>Payment status</dt>
        <dd>[PAYSTATE]</dd>

        <dt>Valid From</dt>
        <dd>[PUBLISH_UP]</dd>

        <dt>Valid To</dt>
        <dd>[PUBLISH_DOWN]</dd>
    </dl>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>
    </p>
@stop
@section('email_reason')
    You are receiving this procedural email message because you have a subscription on <em>[SITENAME]</em>.
@stop