<?php
/**
 * Akeeba Subscriptions – Expired subscription
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
    Your [LEVEL] subscription at [SITENAME] has expired
@stop
@section('topic')
    Your [LEVEL] subscription on our site has expired.
@stop
@section('message')
    <p>
        Your [LEVEL] subscription on [SITENAME] expired on [PUBLISH_DOWN].
    </p>
    <p>We'd like to remind you that you have registered on our site using the username
        <strong>[USERNAME]</strong>
        and email address
        <strong>[USEREMAIL]</strong>
    </p>
@stop
@section('announcement')
    <h3>Renew now and get 30% off!</h3>
    <p>
        <a href="[RENEWALURL:WELCOMEBACK]">Renew today</a> using the coupon code <strong>WELCOMEBACK</strong> for a special 30%
        discount off the listed price.
    </p>
    <p>
        Additionally, you can subscribe to a recurring (automatically billed) 3-month subscription which includes the
        full renewal discount. The recurring portion of the subscription will be billed and activated after the
        one-year renewal expires.
    </p>
@stop
@section('announcement_note')
    You need to log into our site with your username ([USERNAME]) and enter the coupon code to take advantage of this special offer.<br/>
    Clicking on the renewal link in this email automatically applies the coupon code.<br/>
    The recurring (automatic billing) option is optional and additional to the regular, one year subscription renewal. If you choose this option you will be paying immediately for the first year of subscription. After the first year ends you will be automatically billed the recurring price and your subscription will be renewed for three months. This will keep going on every three months. You will receive a reminder email from our reseller, Paddle, 10 and 3 days prior to the automatic billing date. You can cancel your recurring subscription 24 hours prior to its billing date at the latest.<br/>
    The offer is limited time, non-transferable and cannot be exchanged for money, subscription time, services or anything else whatsoever.<br/>
    The offer is only valid when you use the coupon code during the self-service checkout on our site. It can not be applied retroactively or for manual payments (bank transfer) conducted with the help of our staff.<br/>
    If the special offer does not seem to work on the checkout page of our site please contact us <em>before</em> making a payment.
@stop
@section('announcement_visitlink')
    You can <a href="https://www.akeebabackup.com/my-subscriptions/subscriptions.html">review the status
        of all your subscriptions</a> any time on our site.
@stop
@section('email_reason')
    You are receiving this procedural email message because you had a [LEVEL] subscription on <em>[SITENAME]</em>.
@stop