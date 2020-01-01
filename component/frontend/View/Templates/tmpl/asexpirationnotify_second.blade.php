<?php
/**
 * Akeeba Subscriptions – 15 days before expiration email template
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/asexpirationnotify_first')
@section('subject')
    Your [LEVEL] subscription at [SITENAME] is expiring very soon
@stop
@section('topic')
    Your [LEVEL] subscription on our site is expiring very soon!
@stop