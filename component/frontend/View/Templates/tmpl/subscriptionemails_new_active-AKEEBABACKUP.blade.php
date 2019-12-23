<?php
/**
 * Akeeba Subscriptions – New subscription: AKEEBABACKUP
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/subscriptionemails_new_active')
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

    <h3>What is included in my subscription?</h3>
    <p>
        The [LEVEL] subscription gives you access to downloads and support for the following products:
    </p>
    <ul>
        <li>Akeeba Backup Professional for Joomla!</li>
        <li>Akeeba Kickstart</li>
        <li>Akeeba UNiTE</li>
        <li>Akeeba Remote CLI</li>
    </ul>
    <p>
        You can find the links to the downloads, video tutorials, documentation and support for each product below.
    </p>
    <p>
        If you run into any problems, do not hesitate to post a support request in our support ticket
        system. We're here to help!
    </p>

    <h3>Where to go from here?</h3>
    @include('any:com_akeebasubs/Templates/blurb_akeebabackup')
    @include('any:com_akeebasubs/Templates/blurb_backuptools')
@stop