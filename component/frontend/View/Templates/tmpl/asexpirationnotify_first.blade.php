<?php
/**
 * Akeeba Subscriptions – 30 days before expiration email template
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your [LEVEL] subscription at [SITENAME] is expiring soon
@stop
@section('topic')
    Your [LEVEL] subscription on our site is expiring soon!
@stop
@section('message')
    <p>
        Your [LEVEL] subscription on [SITENAME] will expire on [PUBLISH_DOWN].
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>
    </p>
@stop
@section('announcement')
    <h3>Renew early and get 40% off!</h3>
    <p>
        If you <a href="[RENEWALURL]">renew today</a> or at any time <strong>before your subscription's expiration at [PUBLISH_DOWN]</strong> you will get an <strong>automatic 40% discount</strong> off the listed price of the subscription if you renew for another full year.
    </p>
    <p>
        Alternatively, you can subscribe to a recurring (automatically billed) 3-month subscription which includes the renewal discount. The minimum time commitment in this case is just 3 months.
    </p>
    <p>
        In either case <strong>you do not lose any subscription time</strong>. Your renewal will start counting from [PUBLISH_DOWN] onwards.
    </p>
@stop
@section('announcement_note')
    The two necessary conditions for receiving the discount or being eligible for the recurring 3-month subscription are:<br/>
    a. You are purchasing a [LEVEL] subscription. If you buy a subscription to a different product or a bundle this offer is void.<br/>
    b. You must complete the checkout (payment) for your [LEVEL] renewal purchase before [PUBLISH_DOWN].<br/>
    Both conditions must be fulfilled to receive the discount. You do not need to use a coupon code or special link.<br/>
    You need to log into our site with your username ([USERNAME]) to take advantage of this special offer.<br/>
    <em>You will not lose any subscription time by renewing early.</em> Your renewal will start counting after [PUBLISH_DOWN].<br/>
    The offer is limited time, non-transferable and cannot be exchanged for money, subscription time, services or anything else whatsoever.<br/>
    The offer is only valid when applied automatically during the self-service checkout on our site. It can not be applied retroactively or for manual payments (bank transfer) conducted with the help of our staff.<br/>
    If you believe you should be receiving this special offer during renewal but you are not please contact us <em>before</em> making a payment.<br/>
    Please note that the expiration date and time is expressed in the GMT timezone unless noted otherwise. If you'd like to use a different timezone please edit your user profile on our site.
@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeeba.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you have an active [LEVEL] subscription on <em>[SITENAME]</em>.
@stop