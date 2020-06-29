<?php
/**
 * Akeeba Subscriptions – Subscription cancellation (subscription upgraded / converted per user's request)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your subscription #[SUB:ID] at [SITENAME] has been canceled due to subscription conversion.
@stop
@section('topic')
    Your subscription #[SUB:ID] has been canceled because it has been converted to a different subscription type.
@stop
@section('message')
    <p>
        You are receiving this email because your subscription #[SUB:ID] has been absorbed by a new subscription of a
        different type that you purchased on our site.
    </p>
    <p>
        In some rare cases you might be receiving this email when you asked us to make a change in your subscriptions
        such as changing the subscription type, merging subscriptions into a bundle or handling double payments. If this
        is the case you can safely ignore this email message.
    </p>
@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeeba.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you had a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop