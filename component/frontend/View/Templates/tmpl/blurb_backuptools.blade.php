<?php
/**
 * Akeeba Subscriptions – backup tools product blurb
 *
 * @package    akeeba/internal
 * @subpackage email_template
 * @copyright  Copyright (c) 2017-2019 Akeeba Ltd
 * @license    Proprietary
 */
?>
<h4>Advanced backup and restoration tools</h4>
<p>
    Your subscription includes access to the following optional, advanced tools which will help you back up and restore your sites:
</p>
<ul>
    <li><strong>Akeeba Kickstart</strong>. Restore your sites on any server.</li>
    <li><strong>Akeeba UNiTE</strong>. Unattended remote site backup and restoration.</li>
    <li><strong>Akeeba Remote CLI</strong>. Unattended remote site backup and administration, including downloading your backup archives.</li>
</ul>
@include('any:com_akeebasubs/Templates/blurb_buttons', [
	'download_url' => 'https://www.akeebabackup.com/download.html',
	'docs_url' => 'https://www.akeebabackup.com/documentation.html',
	'support_url' => 'https://www.akeebabackup.com/support/desktop-utilities/Tickets.html'
])
