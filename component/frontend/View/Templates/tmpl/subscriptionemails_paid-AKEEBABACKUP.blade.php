<?php
/**
 * Akeeba Subscriptions – Pending subscription got paid (AKEEBABACKUP)
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/subscriptionemails_new_active-AKEEBABACKUP')
@section('subject')
    Your [LEVEL] subscription at [SITENAME] is now paid
@stop
@section('topic')
    The payment for your [LEVEL] subscription on our site has just been cleared.
@stop