<?php 

require 'includes/config.php';

if (isset($_GET['booking'])) {
  $booking_code_md5 = clean_input($_GET['booking']);

  $booking = $db->single_row("SELECT * FROM bookings WHERE code_md5 = '$booking_code_md5'");
  if (!isset($booking['id'])) {
    die('<div class="alert alert-success alert-dismissible" style="background-color:rgb(248, 215, 218);border-bottom-color:rgb(245, 198, 203);border-bottom-left-radius:3.75px;border-bottom-right-radius:3.75px;border-bottom-style:solid;border-bottom-width:1px;border-image-outset:0px;border-image-repeat:stretch;border-image-slice:100%;border-image-source:none;border-image-width:1;border-left-color:rgb(245, 198, 203);border-left-style:solid;border-left-width:1px;border-right-color:rgb(245, 198, 203);border-right-style:solid;border-right-width:1px;border-top-color:rgb(245, 198, 203);border-top-left-radius:3.75px;border-top-right-radius:3.75px;border-top-style:solid;border-top-width:1px;box-sizing:border-box;color:rgb(114, 28, 36);display:block;font-family:Verdana, sans-serif;font-size:15px;font-weight:400;height:46.9px;line-height:22.5px;margin-bottom:15px;padding-bottom:11.25px;padding-left:18.75px;padding-right:18.75px;padding-top:11.25px;position:relative;text-align:left;text-size-adjust:100%;width:670.188px;-webkit-tap-highlight-color:rgba(0, 0, 0, 0);">
        <strong>Error!</strong> No booking found with this code.
      </div>');
  }
  $event = $db->single_row("SELECT * FROM events WHERE id = $booking[event_id]");
  $artist = $db->single_row("SELECT * FROM artists WHERE id = $booking[artist_id]");
  $floor = $db->single_row("SELECT * FROM floors WHERE id = $booking[floor_id]");
  $slot = $db->single_row("SELECT * FROM slots WHERE id = $booking[slot_id]");
  $artist_fees = $db->single_row("SELECT * FROM artist_fees WHERE id = $booking[artist_fees_id]");
  $invoice = $db->single_row("SELECT * FROM invoices WHERE booking_id = $booking[id]");

  if ($booking['tax_id'] == 2) {
    $z_tax = round($artist_fees['fees'] - ($artist_fees['fees'] / 1.07), 2);
    $amount_z_tax = $artist_fees['fees'] - $z_tax;
    $artist_payout_msg = "Payout: ". eu_currency($amount_z_tax) ." via bank transfer.<br>According to Â§19 UStG, no sales tax is shown";
  } else if ($booking['tax_id'] == 3) {
    $z_tax = round($artist_fees['fees'] - ($artist_fees['fees'] / 1.07), 2);
    $amount_z_tax = $artist_fees['fees'] - $z_tax;
    $artist_payout_msg = "Payout: ". eu_currency($artist_fees['fees']) ." cash on night. (". eu_currency($amount_z_tax) ." net fee plus taxes of ". eu_currency($z_tax) .")";
  } else {
    $artist_payout_msg = "";
  }

}


?>
<html>

<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta name=Generator content="Microsoft Word 15 (filtered)">
<style>
<!--
 /* Font Definitions */
 @font-face
  {font-family:"Cambria Math";
  panose-1:2 4 5 3 5 4 6 3 2 4;}
@font-face
  {font-family:"Segoe UI";
  panose-1:2 11 5 2 4 2 4 2 2 3;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
  {margin-top:0cm;
  margin-right:0cm;
  margin-bottom:8.0pt;
  margin-left:0cm;
  line-height:107%;
  font-size:11.0pt;
  font-family:"Calibri",sans-serif;}
.MsoChpDefault
  {font-family:"Calibri",sans-serif;}
.MsoPapDefault
  {margin-bottom:8.0pt;
  line-height:107%;}
@page WordSection1
  {size:595.3pt 841.9pt;
  margin:28.4pt 70.85pt 14.2pt 70.85pt;}
div.WordSection1
  {page:WordSection1;}
-->
table.invoice_table {
  font-size: 12.0pt;
  line-height: 115%;
  font-family: "Segoe UI",sans-serif;
  color: #212529;
  background: white;
}
div.WordSection1 {
	padding-left: 30px;
}
</style>

</head>

<body lang=DE>

<div class=WordSection1>
<p>&nbsp;</p>
<p class=MsoNormal style='line-height:normal;background:white'><b><span
style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;color:#212529'>Invoice
from:</span></b></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
color:#212529;background:white'><?= $artist['dj_name']; ?></span><span style='font-size:12.0pt;
font-family:"Segoe UI",sans-serif;color:#212529'><br>
<span style='background:white'><?= "$artist[surename] $artist[name]"; ?></span><br>
<span style='background:white'><?= $artist['street']; ?></span><br>
<span style='background:white'><?= "$artist[zip_code] $artist[city]"; ?></span><br>
<span style='background:white'><?= $artist['country']; ?></span><br>
<span style='background:white'>VAT: <?= $artist['vat_id']; ?></span><br>
<br>
<br>
</span></p>

<p class=MsoNormal style='line-height:normal;background:white'><b><span
style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;color:#212529'>Invoice
to:</span></b></p>

<p class=MsoNormal><span style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'>Geheimclub Magdeburg</span><span
style='font-size:12.0pt;line-height:107%;font-family:"Segoe UI",sans-serif;
color:#212529'><br>
<span style='background:white;line-height:115%;'>Enrico Ebert</span><br>
<span style='background:white;line-height:115%;'>Münchenhofstr. 37</span><br>
<span style='background:white;line-height:115%;'>39124 Magdeburg</span><br>
<br>
</span></p>

<p class=MsoNormal><b><span style='font-size:12.0pt;line-height:107%;
font-family:"Segoe UI",sans-serif;color:#212529'>Invoice details:</span></b></p>

<table width="250" class="invoice_table">
  <tbody>
    <tr>
      <td style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'>Invoice ID:</td>
      <td><?= $invoice['external_id'] ?></td>
    </tr>
    <tr>
      <td>abcd Invoice ID:</td>
      <td><?= $invoice['internal_id'] ?></td>
    </tr>
    <tr>
      <td>Invoice date:</td>
      <td><?= dotted_date($event['date']) ?></td>
    </tr>
  </tbody>
</table>

<!-- <p class=MsoNormal><span style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'>Invoice ID:                 <?= $invoice['external_id'] ?></span><span
style='font-size:12.0pt;line-height:107%;font-family:"Segoe UI",sans-serif;
color:#212529'><br>
<span style='background:white;line-height:115%;'>abcd Invoice ID:       <?= $invoice['internal_id'] ?></span><br>
<span style='background:white;line-height:115%;'>Invoice date:              <?= dotted_date($event['date']) ?><br>
</span></span></p> -->

<p class=MsoNormal><span style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'>&nbsp;</span></p>

<!-- <p class=MsoNormal><span style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'>(if 0%)</span></p> -->
<?php if ($booking['tax_id'] == 2): ?>

<table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;border:none'>
 <tr>
  <td width=359 valign=top style='width:269.1pt;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529;background:white'><?= "$event[name], ".dotted_date($event['date']).", $artist[dj_name]" ?></span></p>
  </td>
  <td width=151 valign=top style='width:4.0cm;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>Net total:</span></p>
  </td>
  <td width=94 valign=top style='width:70.6pt;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=right style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:right;line-height:normal'><span style='font-size:12.0pt;
  font-family:"Segoe UI",sans-serif;color:#212529'><?= eu_currency($amount_z_tax) ?></span></p>
  </td>
 </tr>
 <tr>
  <td width=359 valign=top style='width:269.1pt;border:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>&nbsp;</span></p>
  </td>
  <td width=151 valign=top style='width:4.0cm;border:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>Total:</span></p>
  </td>
  <td width=94 valign=top style='width:70.6pt;border:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=right style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:right;line-height:normal'><span style='font-size:12.0pt;
  font-family:"Segoe UI",sans-serif;color:#212529'><?= eu_currency($amount_z_tax) ?></span></p>
  </td>
 </tr>
</table>

<p class=MsoNormal><span style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529'><br>
<span style='background:white;line-height:115%;'>According to §19 UStG no value added tax is shown.<br>
The invoice date corresponds to the service date.<br>
Payment by bank transfer to:</span></span></p>
  <td width=359 valign=top style='width:269.1pt;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529;background:white'><b>Account holder:</b> <?= $artist['account_holder'] ?>, <b>IBAN:</b> <?= $artist['iban'] ?>, <b>BIC:</b> <?= $artist['bic'] ?></span></p>

<p class=MsoNormal><span style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'>&nbsp;</span></p>
<?php elseif ($booking['tax_id'] == 3): ?>

<!-- <p class=MsoNormal><span style='font-size:12.0pt;line-height:115%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'>(if 7%)</span></p> -->

<table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;border:none'>
 <tr>
  <td width=359 valign=top style='width:269.1pt;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529;background:white'><?= "$event[name], ".dotted_date($event['date']).", $artist[dj_name]" ?></span></p>
  </td>
  <td width=151 valign=top style='width:4.0cm;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>Net total:</span></p>
  </td>
  <td width=94 valign=top style='width:70.6pt;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=right style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:right;line-height:normal'><span style='font-size:12.0pt;
  font-family:"Segoe UI",sans-serif;color:#212529'><?= eu_currency($amount_z_tax) ?></span></p>
  </td>
 </tr>
 <tr>
  <td width=359 valign=top style='width:269.1pt;border:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>&nbsp;</span></p>
  </td>
  <td width=151 valign=top style='width:4.0cm;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>7% value added tax:</span></p>
  </td>
  <td width=94 valign=top style='width:70.6pt;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=right style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:right;line-height:normal'><span style='font-size:12.0pt;
  font-family:"Segoe UI",sans-serif;color:#212529'><?= eu_currency($z_tax) ?></span></p>
  </td>
 </tr>
 <tr>
  <td width=359 valign=top style='width:269.1pt;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>&nbsp;</span></p>
  </td>
  <td width=151 valign=top style='width:4.0cm;border:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='font-size:12.0pt;font-family:"Segoe UI",sans-serif;
  color:#212529'>Total:</span></p>
  </td>
  <td width=94 valign=top style='width:70.6pt;border:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=right style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:right;line-height:normal'><span style='font-size:12.0pt;
  font-family:"Segoe UI",sans-serif;color:#212529'><?= eu_currency($artist_fees['fees']) ?></span></p>
  </td>
 </tr>
</table>

<p class=MsoNormal><span style='font-size:12.0pt;line-height:107%;font-family:
"Segoe UI",sans-serif;color:#212529'><br>
<span style='background:white;line-height:115%;'>The invoice date corresponds to the service date.<br>
Payment cash on night.</span></span></p>

<p class=MsoNormal><span style='font-size:12.0pt;line-height:107%;font-family:
"Segoe UI",sans-serif;color:#212529;background:white'><br>
Payment received:</span></p>
<?php endif; ?>

</div>

</body>

</html>
