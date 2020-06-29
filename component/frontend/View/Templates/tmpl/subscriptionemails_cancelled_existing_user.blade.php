<?php
/**
 * Akeeba Subscriptions – Subscription cancellation (per client's request)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your subscription #[SUB:ID] at [SITENAME] has been canceled per your request
@stop
@section('topic')
    Your subscription #[SUB:ID] has been canceled per your request.
@stop
@section('message')
    <p>
        You contacted us earlier expressing your wish to cancel your subscription #[SUB:ID] [LEVEL] earlier than its
        scheduled expiration date. This email confirms that we have cancelled your subscription per your request. We
        are sorry to see you go.
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