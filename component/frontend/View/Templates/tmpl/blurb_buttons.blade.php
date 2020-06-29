<?php
/**
 * Akeeba Subscriptions – Product buttons
 *
 * Variables:
 *        download_url  Download page URL
 *        video_url     Video Tutorials URL
 *        docs_url      Documentation URL
 *        support_url   Support URL
 *
 * If any URL is empty its button will not be rendered
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    Proprietary
 */

$download_url = isset($download_url) ? $download_url : '';
$video_url    = isset($video_url) ? $video_url : '';
$docs_url     = isset($docs_url) ? $docs_url : '';
$support_url  = isset($support_url) ? $support_url : '';

?>
<div class="akemail-product-buttons">
    @unless(empty($download_url))
        @include('any:com_akeebasubs/Templates/blurb_button', [
            'title' => 'Download',
            'img_url' => 'https://www.akeeba.com/images/email-template/download.png',
            'target_url' => $download_url
        ])
    @endunless
    @unless(empty($video_url))
        @include('any:com_akeebasubs/Templates/blurb_button', [
            'title' => 'Video Tutorials',
            'img_url' => 'https://www.akeeba.com/images/email-template/video.png',
            'target_url' => $video_url
        ])
    @endunless
    @unless(empty($docs_url))
        @include('any:com_akeebasubs/Templates/blurb_button', [
            'title' => 'Documentation',
            'img_url' => 'https://www.akeeba.com/images/email-template/book.png',
            'target_url' => $docs_url
        ])
    @endunless
    @unless(empty($support_url))
        @include('any:com_akeebasubs/Templates/blurb_button', [
            'title' => 'Support',
            'img_url' => 'https://www.akeeba.com/images/email-template/support.png',
            'target_url' => $support_url
        ])
    @endunless
</div>