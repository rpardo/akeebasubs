<?php
/**
 * Akeeba Ltd site - Master email template
 *
 * This is the Blade rendition of the email template you can find in akeeba/internal under the
 * content_templates/email_template folder. It is used to generate the HTML email templates we use on our site.
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
{{-- Email subject --}}
@section('subject')
@stop
{{-- Additional head data, e.g. https://developers.google.com/gmail/markup/reference/go-to-action --}}
@section('head')
@stop
{{-- Main topic of this email --}}
@section('topic')
@stop
{{-- Main body of the email --}}
@section('message')
@stop
{{-- Special announcement/offer in red box --}}
@section('announcement')
@stop
{{-- Note after the announcement (only shown when 'announcement' is set) --}}
@section('announcement_note')
@stop
{{-- Visit link after the announcement (only shown when 'announcement' is set) --}}
@section('announcement_visitlink')
@stop
{{-- The reason we are sending this email --}}
@section('email_reason')
    You are receiving this procedural email message because you have a user account at <em>[SITENAME]</em>.
@stop
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('subject')</title>
    <style type="text/css">
        :root {
            color-scheme: light dark;
        }

        /*Reset styles*/
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            color: #514f4f;
        }

        .akemail-outer-wrapper {
            background: #40b5b8;
            background-image: url("/images/email-template/background_banner.png");
            background-position: fixed;
        }

        .akemail-outer-wrapper table {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }

        .akemail-wrapper {
            width: 100%;
            height: 100%;
            padding: 1em 0;
        }

        .akemail-container {
            background-color: #fff;
            color: #514f4f;
            text-align: left;
            box-shadow: black 0 0 8px;
        }

        .akemail-logo {
            background-color: #efefef;
            text-align: right;
            padding: 1em 1em;
            min-height: 40px;
        }

        .akemail-logo a {
            border: none;
            text-decoration: none;
        }

        .akemail-logo a img {
            max-height: 50px;
            max-width: 200px;
        }

        .akemail-hello {
            font-size: 13pt;
            color: #38b5b8;
            padding: 0.5em 1em;
        }

        .akemail-main-topic {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            color: #e43535;
            padding: 0.5em 1em;
        }

        .akemail-message {
            padding: 0.5em 1em;
        }

        .akemail-message a {
            color: #38b5b8;
            text-decoration: underline;
        }

        .akemail-product-buttons {
            text-align: center;
        }

        .akemail-message h4 {
            color: #38b5b8;
            font-size: 13pt;
            border-bottom: thin solid #38b5b8;
        }

        a.akemail-button {
            display: inline-block;
            text-align: center;
            padding: 20px 55px;
            color: #70a230;
            border: 5px solid #40b5b8;
            margin: 5px;
            box-sizing: border-box;
            background-color: #eaeaea;
            text-decoration: none;
            font-weight: bold;
        }

        a.akemail-button:hover {
            background-color: #d9d9d9;
        }

        a.akemail-button img {
            display: block;
            width: 50px;
            margin: 0 auto;
        }

        .akemail-announcement-box {
            background: #e43535;
            padding: 0 1em;
            margin: 0 2em;
            color: #fff;
            border-radius: 0.25em;
            border: thin solid #952b2b;
        }

        .akemail-announcement-box h3 {
            font-weight: bold;
            font-size: 13pt;
            font-style: italic;
            padding: 0;
            margin: 1em 0 0.5em;
            border-bottom: thin solid #efefef;
        }

        .akemail-announcement-note {
            color: #7a7a7a;
            font-size: 10pt;
            line-height: 1.1em;
            margin: 1em 3em 0;
        }

        .akemail-announcement-visitlink {
            margin: 1em 1em 0;
        }

        .akemail-announcement-visitlink a {
            color: #38b5b8;
            text-decoration: underline;
        }

        .akemail-outro {
            color: #514f4f;
            padding: 0 1em 1em;
        }

        .akemail-footer {
            display: block;
            text-align: right;
            background: #514f4f;
            color: #fff;
            font-size: 10pt;
            padding: 1px 2em;
        }

        .akemail-footer-sitelink a {
            float: left;
            color: #ffffff;
            text-decoration: none;
            border: none;
        }

        .akemail-footer-follow img {
            border: none;
            text-decoration: none;
            max-width: 100%;
        }

        .akemail-legalinfo {
            text-align: center;
            width: 100%;
            max-width: 960px;
            color: #efefef;
            font-size: 9pt;
            font-weight: normal;
            background-color: rgba(0, 0, 0, 0.2);
            margin: 2em auto;
            padding: 0.5em 0;
        }

        .akemail-legalinfo a {
            color: white;
            font-weight: bold;
            text-decoration: underline;
            border: none;
        }

        .akemail-legalinfo p {
            margin: 0.3em 1em 0;
        }

        {{ '@' . 'media' }} (prefers-color-scheme: dark) {
            .akemail-outer-wrapper {
                background-color: #274b4e;
            }

            .akemail-container {
                background-color: #383636;
                color: #efefef;
            }

            .akemail-logo {
                background-color: #242222;
            }

            .akemail-main-topic {
                color: #f73535;
            }

            .akemail-announcement-box {
                background: #712121;
                color: #efefef;
            }

            .akemail-announcement-box h3 {
                border-bottom: thin solid #f73535;
            }

            .akemail-announcement-note {
                color: #adadad;
            }

            .akemail-outro {
                color: #3ea1a4;
            }

            .akemail-footer {
                background-color: #242222;
            }
        }

    </style>
    @yield('head')
</head>
<body>
<!-- region Body -->
<div class="akemail-outer-wrapper">
    <table>
        <tr>
            <td width="10%"></td>
            <td>
                <div class="akemail-wrapper">
                    <div class="akemail-container">
                        <!-- Logo Header -->
                        <div class="akemail-logo">
                            <a href="[SITEURL]" title="Visit akeebabackup.com">
                                <picture>
                                    <source srcset="/images/email-template/akeeba-logo-white.png" media="(prefers-color-scheme: dark)">
                                    <source srcset="/images/email-template/akeeba-logo.png" media="(prefers-color-scheme: light)">
                                    <img src="/images/email-template/akeeba-logo.png" alt="Akeeba Ltd">
                                </picture>
                            </a>
                        </div>
                        <!-- Hello -->
                        <div class="akemail-hello">
                            <p>Hello [FIRSTNAME],</p>
                        </div>
                        <!-- Main-Topic -->
                        <div class="akemail-main-topic">
                            <p>@yield('topic')</p>
                        </div>
                        <!-- Message -->
                        <div class="akemail-message">
                            @yield('message')
                        </div>
                        <!-- Area Offer/Announcement -->
                        <div class="akemail-announcement">
                            @if (!empty($this->yieldContent('announcement')))
                                <div class="akemail-announcement-box">
                                    @yield('announcement')
                                </div>
                            @endif
                            @if (!empty($this->yieldContent('announcement_note')))
                                <p class="akemail-announcement-note">
                                    @yield('announcement_note')
                                </p>
                            @endif
                            @if (!empty($this->yieldContent('announcement_visitlink')))
                                <p class="akemail-announcement-visitlink">
                                    @yield('announcement_visitlink')
                                </p>
                            @endif
                        </div>
                        <!-- Bye, Bye -->
                        <div class="akemail-outro">
                            <p>
                                We wish you a great day,<br />
                                Akeeba Ltd
                            </p>
                        </div>
                        <!-- Footer -->
                        <div class="akemail-footer">
                            <p class="akemail-footer-sitelink">
                                <a href="[SITEURL]" title="Visit akeebabackup.com">
                                    www.akeebabackup.com
                                </a>
                            </p>
                            <p class="akemail-footer-follow">
                                Follow us:
                                <a href="https://www.facebook.com/akeebaltd/" target="_blank"
                                   title="Follow us on Facebook">
                                    <img src="/images/email-template/facebook.png" width="15" height="14" alt="Facebook logo">
                                </a>
                                <a href="https://twitter.com/akeebabackup" target="_blank"
                                   title="Follow us on Twitter">
                                    <img src="/images/email-template/twitter.png" width="15" height="12" alt="Twitter logo">
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- Legal info -->
                    <div class="akemail-legalinfo">
                        <p>
                            @yield('email_reason')
                        </p>
                        <p>
                            Please do not reply to this email, it's sent from an unmonitored email address.
                        </p>
                        <p>
                            If you want to stop receiving any email from us you can
                            <a href="[SITEURL]/data-options.html">delete your user account</a>
                            on our site. Please note that this process is <em>irreversible</em>.
                        </p>
                    </div>
                </div>
            </td>
            <td width="10%"></td>
        </tr>
    </table>
</div>

<!-- endregion -->
</body>
</html>