<?php
/**
 * Akeeba Subscriptions – 7 days after expiration email template (AKEEBABACKUP, with upsell to WordPress)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/asexpirationnotify_after')
@section('announcement')
    <h3>Also using WordPress? We've got your back!</h3>
    @include('any:com_akeebasubs/Templates/blurb_upsell_backup_wordpress')
    <p>
        Like what you see? You can use the coupon code <strong>WELCOMEBACK</strong> to get a special 30%  discount when
        you subscribe to Akeeba Backup for WordPress!
    </p>
@stop
