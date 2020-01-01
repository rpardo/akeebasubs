<?php
/**
 * Akeeba Subscriptions – Problem Transactions Report email template
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */
?>
@extends('any:com_akeebasubs/Templates/template')
@section('subject')
	Latest transactions with problems
@stop
@section('topic')
	Report of transactions with problems
@stop
@section('message')
	<p>The Akeeba Subscriptions Collation script identified the following transactions with potential problems.</p>
	[PROBLEM_TRANSACTIONS]
@stop
@section('email_reason')
	You are receiving this procedural email message because you are an administrator at <em>[SITENAME]</em>.
@stop