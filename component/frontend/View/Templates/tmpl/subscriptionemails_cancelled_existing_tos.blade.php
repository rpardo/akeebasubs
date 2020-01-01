<?php
/**
 * Akeeba Subscriptions – Subscription cancellation (client violated the ToS)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your subscription #[SUB:ID] at [SITENAME] has been terminated
@stop
@section('topic')
    Your subscription #[SUB:ID] has been terminated without a refund.
@stop
@section('message')
    <p>
        You were earlier notified about a violation of our Terms of Service. This ultimately resulted in your
        subscription #[SUB:ID] [LEVEL] being terminated without a refund.
    </p>
@stop
@section('email_reason')
    You are receiving this procedural email message because you had a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop