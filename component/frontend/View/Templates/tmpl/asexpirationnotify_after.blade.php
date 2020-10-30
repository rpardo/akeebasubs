<?php
/**
 * Akeeba Subscriptions – 7 days after expiration email template (all levels)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    We miss you!
@stop
@section('topic')
    It's been a while, and we miss you.
@stop
@section('message')
    <p>
        We noticed that your [LEVEL] subscription at [SITENAME] has expired quite a while ago, on
        [PUBLISH_DOWN]. This is why we're offering you a special 30% discount if you <a
                href="[RENEWALURL:WELCOMEBACK]">resubscribe to [LEVEL]</a>. Just use the special link in
        this email to receive your discount. If you don't see the link, please copy and paste the
        following URL to your browser's address bar:<br />
        [RENEWALURL:WELCOMEBACK]
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>.
    </p>
@stop

@section('announcement_note')
    This email is only sent once per expired subscription. If you are really not interested in resubscribing
    there are no hard feelings. We promise this is the last email you are receiving from us regarding your expired
    subscription.<br/>
    You need to log into our site with your username ([USERNAME]) and enter the coupon code to take advantage of this special offer.<br/>
    Clicking on the renewal link in this email automatically applies the coupon code.<br/>
    The offer is limited time, non-transferable and cannot be exchanged for money, subscription time, services or anything else whatsoever.<br/>
    The offer is only valid when you use the coupon code during the self-service checkout on our site. It can not be applied retroactively or for manual payments (bank transfer) conducted with the help of our staff.<br/>
    If the special offer does not seem to work on the checkout page of our site please contact us <em>before</em> making a payment.
@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeeba.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you had a subscription, now expired, on <em>[SITENAME]</em>.
@stop