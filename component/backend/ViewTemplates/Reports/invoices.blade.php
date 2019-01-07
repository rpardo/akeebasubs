<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Date\Date;

defined('_JEXEC') or die;

/** @var \Akeeba\Subscriptions\Admin\View\Reports\Html $this */

$date = new Date($this->params['year'] . '-' . $this->params['month'] . '-01');

?>

@if($this->input->getCmd('tmpl', 'html') != 'component')
    @include('admin:com_akeebasubs/Reports/invoices_controls', array('params' => $this->params))
@else
    @inlineJs("\n\nwindow.print();\n\n");
@endif

<h1>
	@sprintf('COM_AKEEBASUBS_REPORTS_INVOICES_TITLE', $date->format('F Y'))
</h1>

<table width="100%" cellspacing="0" cellpadding="0" style="border-top: thin solid #c0c0c0">
    <thead>
    <tr style="background-color: black; color: white">
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_NUMBER')</th>
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_ISSUED')</th>
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_PAYMENT')</th>
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_VATNR')</th>
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_NET')</th>
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_TAXPERCENT')</th>
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_TAXAMOUNT')</th>
        <th>@lang('COM_AKEEBASUBS_REPORTS_INVOICES_COL_PAYABLE')</th>
    </tr>
    </thead>
    <tbody>
	<?php
	$m = 1;
	$i = 0;

	$totalAmount = 0;
	$totalTax = 0;
	$totalNet = 0;
	$totalInvoicesShown = 0;

	foreach ($this->records as $r):
	$m = 1 - $m;
	$i++;
	$color = $m ? '#f0f0f0' : 'white';
	$tdStyle = 'style="border-bottom: thin solid black;  border-left: thin solid #c0c0c0;"';
	$tdStyleLast = 'style="border-bottom: thin solid black;  border-left: thin solid #c0c0c0; border-right: thin solid #c0c0c0"';

	$who = $r->isbusiness ? $r->businessname : $r->name;
	$occupation = $r->isbusiness ? $r->occupation : '';

	if ($occupation)
	{
		$occupation = "<span style=\"color: green; font-size: small;\">$occupation</span><br/>";
	}

	$address = $r->address1;
	$address .= $r->address2 ? ', ' . $r->address2 : '';

	$vatnumber = '';

	if ($r->isbusiness && ($r->tax_amount == 0))
	{
		$vatnumber = $r->vatnumber;
	}

	$totalAmount += $r->gross_amount;
	$totalTax += $r->tax_amount;
	$totalNet += $r->net_amount;
	$totalInvoicesShown++;

	$r->net_amount = sprintf('%.02f', $r->net_amount);
	$r->tax_amount = sprintf('%.02f', $r->tax_amount);
	$r->gross_amount = sprintf('%.02f', $r->gross_amount);
	$r->tax_percent = sprintf('%.02f', $r->tax_percent);
	?>
    <tr style="background-color: {{ $color }};">
        <td {{ $tdStyle }} width="100">
            # <span style="font-weight: bold;">{{{ $r->number }}}</span><br/>
            <span style="padding-left: 1em">{{{ $r->invoice_date }}}</span>
        </td>
        <td {{ $tdStyle }}>
            <span style="font-weight: bold;">{{{ $who }}}</span><br/>
			{{ $occupation }}
            <span style="color: #333; font-size: small;">
					{{{ $address }}} &bull; {{{ $r->zip }}} &bull;
					{{{ $r->city }}} &bull; {{{ $r->country }}}
				</span><br/>
        </td>
        <td {{ $tdStyle }} width="220">
					<span style="color: #333; font-size: small;">
						{{{ $r->processor }}}<br/>
						{{{ $r->processor_key }}}
					</span>
        </td>
        <td {{ $tdStyle }} width="120">
			{{{ $vatnumber }}}
        </td>
        <td {{ $tdStyle }} align="right" width="80">
			{{{ $r->net_amount }}} &euro;&nbsp;
        </td>
        <td {{ $tdStyle }} align="right" width="80">
			{{{ $r->tax_percent }}}%&nbsp;
        </td>
        <td {{ $tdStyle }} align="right" width="80">
			{{{ $r->tax_amount }}} &euro;&nbsp;
        </td>
        <td {{ $tdStyle }} align="right" width="80">
			{{{ $r->gross_amount }}} &euro;&nbsp;
        </td>
    </tr>
	<?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr style="background-color: navy; color: white; font-weight: bold;">
        <td colspan="4">
			@sprintf('COM_AKEEBASUBS_REPORTS_INVOICES_LBL_GRANDTOTAL', $totalInvoicesShown)
        </td>
        <td align="right">
			{{ $totalNet }} &euro;
        </td>
        <td></td>
        <td align="right">
			{{ $totalTax }} &euro;
        </td>
        <td align="right">
			{{ $totalAmount }} &euro;
        </td>
    </tr>
    </tfoot>
</table>
