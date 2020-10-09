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
		$artist_payout_msg = "Payout: ". display_currency($amount_z_tax) ." via bank transfer.<br>According to §19 UStG, no sales tax is shown";
	} else if ($booking['tax_id'] == 3) {
		$z_tax = round($artist_fees['fees'] - ($artist_fees['fees'] / 1.07), 2);
		$amount_z_tax = $artist_fees['fees'] - $z_tax;
		$artist_payout_msg = "Payout: ". display_currency($artist_fees['fees']) ." cash on night. (". display_currency($amount_z_tax) ." net fee plus taxes of ". display_currency($z_tax) .")";
	} else {
		$artist_payout_msg = "";
	}

}

require 'includes/header.php';
// require 'includes/navigation.php';
require 'includes/sidebar.php';

?>

<div class="container mt-4">
	<div class="row">
		<div class="col-md-6">
			<?= (isset($booking_msg)) ? $booking_msg: false; ?>
		</div>
	</div>
	<?php if (isset($_GET['booking'])): ?>
	<h2>Booking overview:</h2>
	<div class="row">
		<div class="col-md-8">
			<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  At this moment ther're no booking details available.
			</div>
		</div>
	</div>
	<?php else: ?>
	<div class="row">
		<div class="col-md-8">
			<?= (isset($_SESSION['booking_msg'])) ? $_SESSION['booking_msg']: false; ?>
			<?php unset($_SESSION['booking_msg']); ?>
		</div>
	</div>
	<?php endif; ?>

	

	<div class="row">
		<div class="col-md-6">
			
		</div>
	</div>
</div>



<script>
$(document).ready(function() {

});
</script>
<?php require 'includes/footer.php'; ?>