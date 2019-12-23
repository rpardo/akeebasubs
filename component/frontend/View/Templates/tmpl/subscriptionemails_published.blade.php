<?php
/**
 * Akeeba Subscriptions – A renewal got published
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your renewed [LEVEL] subscription at [SITENAME] has been activated
@stop
@section('topic')
    Congratulations! Your [LEVEL] subscription renewal on our site has been activated.
@stop
@section('message')
    <p>
        You had purchased a renewal for your [LEVEL] subscription on [SUB:CREATED_ON]. The renewal is now active and will remain active until [PUBLISH_DOWN].
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>
    </p>
    <h3>Why am I receiving this email?</h3>
    <p>
        You had purchased a renewal to your [LEVEL] subscription on [SUB:CREATED_ON], before your previous subscription expired. The renewal you bought is a new subscription which was remained paid but remained inactive until your previous subscription expired. This way you did not lose any subscription time or money by renewing early. This email confirms that the previous subscription expired and the renewal subscription is now activated. All good!
    </p>
@stop
@section('email_reason')
    You are receiving this procedural email message because you have a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop