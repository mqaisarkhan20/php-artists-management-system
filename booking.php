<?php 

require 'includes/config.php';

if (isset($_POST['save_invoice_number'])) {
	$booking_code_md5 = clean_input($_POST['booking_code_md5']);
	$external_id = clean_input($_POST['external_id']);

	$previous_record = $db->single_row("SELECT * FROM invoices WHERE external_id = $external_id");
	if (false) { // isset($previous_record['external_id'])
		$_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> Invoice with same external id already exist.
		</div>';

		header('location: ' . URL . 'booking.php?booking=' . $booking_code_md5);
		exit;
	} else {
		$booking = $db->single_row("SELECT * FROM bookings WHERE code_md5 = '$booking_code_md5'");
		$data = Array(
			'external_id' => $external_id
		);

		if ($db->update('invoices', $data, ['booking_id' => $booking['id']])) {
			$_SESSION['iiid_msg'] = '<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Invoice external id updated successfully.
			</div>';

			header('location: ' . URL . 'booking.php?booking=' . $booking_code_md5);
			exit;
		}
	}
}

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
		$artist_payout_msg = "Payout: ". eu_currency($amount_z_tax) ." (tax in the amount of ". eu_currency($z_tax) ." are paid by the club). Payment by bank transfer.";
	} else if ($booking['tax_id'] == 3) {
		$z_tax = round($artist_fees['fees'] - ($artist_fees['fees'] / 1.07), 2);
		$amount_z_tax = $artist_fees['fees'] - $z_tax;
		$artist_payout_msg = "". eu_currency($artist_fees['fees']) ." (". eu_currency($amount_z_tax) ." net plus 7% value added tax in the amount of ". eu_currency($z_tax) ."). Payment cash on night.";
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
			<?= (isset($_SESSION['iiid_msg'])) ? $_SESSION['iiid_msg']: false; ?>
			<?php unset($_SESSION['iiid_msg']); ?>
			<p><strong>Event:</strong> <?= dotted_date($event['date']) .", $event[name], $event[start_time] - $event[end_time]," ?></p>
			<p><strong>Artist:</strong> <?= "$artist[dj_name], $artist[backline]"; ?></p>
			<p><strong>Floor:</strong> <?= $floor['name']; ?></p>
			<p><strong>Slot:</strong> <?= "$slot[start_time] - $slot[end_time]"; ?></p>
			<p><strong>Artist fee all included, incl. taxes:</strong> <?= eu_currency($artist_fees['fees']) ?></p>
			<p><strong>Artist’s Payout:</strong></p>
			<p class="text-success"><?= $artist_payout_msg; ?></p>
			<form action="" method="POST" name="external_invoice_number">
				<?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
				<?php unset($_SESSION['message']); ?>
				<input type="hidden" name="booking_code_md5" value="<?= (isset($booking_code_md5)) ? $booking_code_md5: false; ?>">

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="">External invoice number:</label>
							<input type="text" name="external_id" class="form-control" value="<?= (isset($invoice['external_id'])) ? $invoice['external_id']: false; ?>">
						</div>
					</div>
				</div>

				<input type="submit" name="save_invoice_number" value="Update" class="btn btn-primary btn-sm">
			</form>

			<hr>

			<a id="invoice_link" href="<?= URL.'invoice.php?booking='.$booking_code_md5 ?>">View invoice</a>
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

	let eu_format = new Intl.NumberFormat('de-DE', {
	  style: 'currency',
	  currency: 'EUR',
	  minimumFractionDigits: 2
	});

	$.each($('span.eu_currency'), function(a, b) {
		var value = parseInt($(this).text());
		$(this).text(eu_format.format(value));
	});

	$('form[name="external_invoice_number"]').submit(function(e) {
		$('.alert').remove();
		var internal_id = ($('input[name="internal_id"]').val()).trim();

		if (internal_id == '') {
			e.preventDefault();
				var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> Please external invoice number.
				</div>`);
				$('form[name="external_invoice_number"]').prepend(message);
				$(message).fadeIn();
		}
	});
});
</script>
<?php require 'includes/footer.php'; ?>