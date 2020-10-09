<?php 

require 'includes/config.php';

if (isset($_GET['booking'])) {
	$booking_code_md5 = clean_input($_GET['booking']);

	$booking = $db->single_row("SELECT * FROM bookings WHERE code_md5 = '$booking_code_md5'");
	$event = $db->single_row("SELECT * FROM events WHERE id = $booking[event_id]");
	$artist = $db->single_row("SELECT * FROM artists WHERE id = $booking[artist_id]");
	$floor = $db->single_row("SELECT * FROM floors WHERE id = $booking[floor_id]");
	$slot = $db->single_row("SELECT * FROM slots WHERE id = $booking[slot_id]");
	$artist_fees = $db->single_row("SELECT * FROM artist_fees WHERE id = $booking[artist_fees_id]");

	/* ZERO TAX */
	$z_tax = round($artist_fees['fees'] - ($artist_fees['fees'] / 1.07), 2);
	$amount_z_tax = $artist_fees['fees'] - $z_tax;
	$first_radio_text = "Payout: ". eu_currency($amount_z_tax) ." (due to the lack of input tax deduction, the club pays taxes of ". eu_currency($z_tax) ." directly to the tax office). Payment by bank transfer. Please enter your bank details below.";

	/* SEVEN TAX */
	$z_tax = round($artist_fees['fees'] - ($artist_fees['fees'] / 1.07), 2);
	$amount_z_tax = $artist_fees['fees'] - $z_tax;
	$second_radio_text = "Payout: ". eu_currency($artist_fees['fees']) ." (". eu_currency($amount_z_tax) ." net plus 7% vallue added taxes in the amount of ". eu_currency($z_tax) ."). Payment cash on night.";

	if (count($booking) == 0) {
		$booking_msg = '<div class="alert alert-danger alert-dismissible mt-4">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> No booking found.
		</div>';
	} else if ($booking['booking_active'] == 0) {
		header('Location: ' . URL . 'unavailable.php?booking=' . $booking_code_md5);
		exit;
	} else if ($booking['status_id'] == 2) {
		header('Location: ' . URL . 'declined.php?booking=' . $booking_code_md5);
		exit;
	} else if ($booking['status_id'] == 3) {
		header('Location: ' . URL . 'booking.php?booking=' . $booking_code_md5);
		exit;
	}
}

if (isset($_POST['decline_booking'])) {
	$booking_code_md5 = clean_input($_POST['booking_code_md5']);

	$booking = $db->single_row("SELECT * FROM bookings WHERE code_md5 = '$booking_code_md5'");
	$event = $db->single_row("SELECT * FROM events WHERE id = $booking[event_id]");
	$artist = $db->single_row("SELECT * FROM artists WHERE id = $booking[artist_id]");
	$floor = $db->single_row("SELECT * FROM floors WHERE id = $booking[floor_id]");
	$slot = $db->single_row("SELECT * FROM slots WHERE id = $booking[slot_id]");
	$artist_fees = $db->single_row("SELECT * FROM artist_fees WHERE id = $booking[artist_fees_id]");
	
	$data = Array(
		'status_id' => 2
	);

	if ($db->update('bookings', $data, ['code_md5' => $booking_code_md5])) {
		$from = 'archive@gcbi.de';
		$to = (isset($artist['email'])) ? $artist['email']: '';
		$subject = 'Geheimclub Booking Interface | Booking cancellation ' . $artist['dj_name'];
		$body = 'Hi '. ucfirst($artist['surename']) .',<br>
			<br>
			you\'ve declined the requested booking.
			<br><br>
			All the best and kind regards,
			<br>
			Geheimclub Booking Team
			';
		send_email($from, $to, $subject, $body);

		$_SESSION['booking_msg'] = '<div class="alert alert-success alert-dismissible mt-4">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Booking declined successfully.
		</div>';
		header('location: ' . URL . 'request.php');
		exit;
	}
}

if (isset($_POST['confirm_booking'])) {
	$booking_code_md5 = clean_input($_POST['booking_code_md5']);

	$booking = $db->single_row("SELECT * FROM bookings WHERE code_md5 = '$booking_code_md5'");
	$event = $db->single_row("SELECT * FROM events WHERE id = $booking[event_id]");
	$artist = $db->single_row("SELECT * FROM artists WHERE id = $booking[artist_id]");
	$floor = $db->single_row("SELECT * FROM floors WHERE id = $booking[floor_id]");
	$slot = $db->single_row("SELECT * FROM slots WHERE id = $booking[slot_id]");
	$artist_fees = $db->single_row("SELECT * FROM artist_fees WHERE id = $booking[artist_fees_id]");

	$tax_id = clean_input($_POST['id']);

	$data = Array(
		'tax_id' => $tax_id,
		'status_id' => 3
	);

	if (isset($_POST['account_holder']) && isset($_POST['iban']) && isset($_POST['bic'])) {
		$artist_data = array(
			'account_holder' => clean_input($_POST['account_holder']),
			'iban' => clean_input($_POST['iban']),
			'bic' => clean_input($_POST['bic'])
		);

		$db->update('artists', $artist_data, ['id' => $booking['artist_id']]);
	}
	

	if ($db->update('bookings', $data, ['code_md5' => $booking_code_md5])) {
		$last_invoice = $db->single_row("SELECT * FROM invoices ORDER BY id DESC");
		$new_invoice_internal_id = (isset($last_invoice['internal_id']) && $last_invoice['internal_id'] > 0) ? ($last_invoice['internal_id'] + 1) : 413;

		$data2 = Array(
			'booking_id' => $booking['id'],
			'internal_id' => $new_invoice_internal_id
		);

		if ($db->insert('invoices', $data2)) {
			$from = 'archive@gcbi.de';
			$to = (isset($artist['email'])) ? $artist['email']: '';
			$subject = 'Geheimclub Booking Interface | Booking confirmation ' . $artist['dj_name'];
			$body = 'Hi '. ucfirst($artist['surename']) .',<br>
				<br>
				your booking has been confirmed. For details and please follow this link. The GCBI has just created your invoice automaticly, please just add your own internal invoice id.
				'. URL .'booking.php?booking='. $booking_code_md5 .'
				<br><br>
				If you\'ve added your own invoice ID, you can print out your invoice by following this link:
				'. URL . 'invoice.php?booking='. $booking_code_md5 .'
				<br>
				All the best and kind regards,
				<br>
				Geheimclub Booking Team
				';
			send_email($from, $to, $subject, $body);
			$_SESSION['booking_msg'] = '<div class="alert alert-success alert-dismissible mt-4">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Booking confirmed successfully.
			</div>';
			header('location: ' . URL . 'request.php');
			exit;
		}
	}
}


if (isset($_POST['update'])) {
	$slot_id = clean_input($_POST['slot_id']);
	$start_time = clean_input($_POST['start_time']);
	$end_time = clean_input($_POST['end_time']);

	// $previous_event = $db->single_row("SELECT * FROM events WHERE name = '$name'");

	if (false) { // count($previous_event) > 0
		$_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> Event with same name already exist.
		</div>';

		header('location: ' . URL);
		exit;
	} else {
		$data = array(
			'start_time' => $start_time,
			'end_time' => $end_time
		);
		if ($db->update('slots', $data, ['id' => $slot_id])) {
			$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Slot updated successfully.
			</div>';

			header('location: ' . URL . 'slot.php');
			exit;
		}
	}
}

if (isset($_POST['submit'])) {
	$start_time = clean_input($_POST['start_time']);
	$end_time = clean_input($_POST['end_time']);

	// $previous_event = $db->single_row("SELECT * FROM events WHERE name = '$name'");

	if (false) { // count($previous_event) > 0
		$_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> Event with same name already exist.
		</div>';

		header('location: ' . URL);
		exit;
	} else {
		$data = array(
			'start_time' => $start_time,
			'end_time' => $end_time
		);
		if ($db->insert('slots', $data)) {
			$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Slot saved successfully.
			</div>';

			header('location: ' . URL . 'slot.php');
			exit;
		}
	}
}

if (isset($_GET['delete'])) {
	$id = clean_input($_GET['delete']);

	if ($db->delete('slots', ['id' => $id])) {
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Slot deleted successfully.
		</div>';

		header('location: ' . URL . 'slot.php');
		exit;
	}
}

$slots = $db->multiple_row("SELECT * FROM slots ORDER BY id DESC");
$nav_active = 'slots';

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
	<h2>Booking request details:</h2>
	<div class="row">
		<div class="col-md-8">
			<p><strong>Event: </strong><?= dotted_date($event['date']) ?>, <?= $event['name'] ?>, <?= $event['start_time'] . ' - ' . $event['end_time'] ?></p>
			<p><strong>Artist: </strong><?= $artist['dj_name'] ?> (<?= $artist['backline'] ?>)</p>
			<p><strong>Floor: </strong><?= $floor['name'] ?></p>
			<p><strong>Slot: </strong><?= $slot['start_time'] . ' - ' . $slot['end_time'] ?></p>
			<p><strong>Artist fee all included, taxes as well: </strong><?= eu_currency($artist_fees['fees']) ?></p>
			<p><strong>Please select your value added tax:</strong></p>

			<form action="" method="POST" name="confirm_booking_form">
				
				<input type="hidden" name="booking_code_md5" value="<?= (isset($booking_code_md5)) ? $booking_code_md5: false; ?>">

				<div class="form-group">
					<div class="custom-control custom-radio mt-3">
					  <input type="radio" id="customRadio1" name="id" value="2" class="custom-control-input">
					  <label class="custom-control-label" for="customRadio1">0% value added tax (according to §19 UStG)</label>
					</div>
					<p class="text-success payout_msg" style="display: none;"><?= $first_radio_text; ?></p>
					<div id="new_input_els_cntnr">

					</div>
				</div>

				<div class="form-group">
					<div class="custom-control custom-radio mt-3">
					  <input type="radio" id="customRadio2" name="id" value="3" class="custom-control-input">
					  <label class="custom-control-label" for="customRadio2">7% </label>
				    value added tax					</div>
					<p class="text-success payout_msg" style="display: none;"><?= $second_radio_text; ?></p>
				</div>

				<div class="form-group">
					<div class="custom-control custom-radio mt-3">
					  <input type="radio" id="customRadio3" name="id" value="4" class="custom-control-input">
					  <label class="custom-control-label" for="customRadio3">19% value added tax</label>
					</div>
					<p class="text-danger payout_msg" style="display: none;">Sorry, we do not accept invoices with value added taxes of 19%. The booking cannot be confirmed.</p>
				</div>

				<div class="form-group">
					<strong>Terms and conditions:</strong>
					<p>If you're acting like a nazi, homophobic, sexist, racist, stigmatizer, violent  or as another asshole on facebook, twitter, instagram or in real live, the booking will be cancelled without any additional notice. Love and respect each other. Be techno.</p>
				</div>

				<div class="form-group">
					<div class="custom-control custom-checkbox">
					  <input type="checkbox" class="custom-control-input" id="agree_to_tac" name="agree_to_tac">
					  <label class="custom-control-label" for="agree_to_tac">I agree to terms and conditions.</label>
					</div>
				</div>

				<div class="form-group">
					<input type="submit" name="confirm_booking" value="Confirm" class="btn btn-primary btn-sm" disabled>
					<input type="submit" name="decline_booking" value="Decline" class="btn btn-danger btn-sm">
				</div>
			</form>
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
			<?php if (false): ?>
			<hr>
			<h2>Slots:</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Duration</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($slots)): ?>
						<?php foreach($slots as $slot): ?>
							<tr>
								<td><?= $slot['start_time'] . ' - ' . $slot['end_time'] ?></td>
								<td>
									<a onclick="return confirm('Are you sure?');" href="<?= URL ?>slot.php?delete=<?= $slot['id'] ?>"><i class="fas fa-trash-alt delete"></a></i>&nbsp;
									<i class="fas fa-edit update cursor_pointer" data-id="<?= $slot['id'] ?>">
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
					<tr>
						<td colspan="5"><i>No slot saved yet!</i></td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<?php endif; ?>
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

	$.each($('.eu_currency'), function(a, b) {
		var value = parseInt($(this).text());
		$(this).text(eu_format.format(value));
	});

	$('input[type=radio][name=id]').change(function() {
		$('.payout_msg').hide();
		$(this).parent().next().fadeIn()
	});

	$("input[name='id']").click(function() {
		var tax_input_checked = $('input[name="id"]').is(':checked');
		var tac_checked = $('input[name="agree_to_tac"]').is(':checked');
		if (tax_input_checked && tac_checked) {
			$('input[name="confirm_booking"]').prop("disabled", false);
		} else {
			$('input[name="confirm_booking"]').prop("disabled", true);
		}

		var tax_id = $('input[name="id"]:checked').val();
		if (tax_id == 2) {
			var new_input_els = `<div class="form-group">
					<label>Account holder:</label>
					<input type="text" class="form-control" name="account_holder">
				</div>

				<div class="form-group">
					<label>IBAN:</label>
					<input type="text" class="form-control" name="iban">
				</div>

				<div class="form-group">
					<label>BIC:</label>
					<input type="text" class="form-control" name="bic">
				</div>`;

			$('#new_input_els_cntnr').html(new_input_els)
		} else if (tax_id == 4) {
			$('input[name="confirm_booking"]').prop("disabled", true);
		} else {
			$('#new_input_els_cntnr').html('');
		}
	});

	$('input[name="agree_to_tac"]').click(function() {
		var tax_input_checked = $('input[name="id"]').is(':checked');
		var tac_checked = $('input[name="agree_to_tac"]').is(':checked');
		var tax_id = $('input[name="id"]:checked').val();

		if (tax_input_checked && tac_checked && tax_id != 4) {
			$('input[name="confirm_booking"]').prop("disabled", false);
		} else {
			$('input[name="confirm_booking"]').prop("disabled", true);
		}
	});

	$('form[name="confirm_booking_form"]').submit(function(e) {
		$('.alert').remove();
		var input_length = $('input[name="account_holder"]').length;

		if (input_length > 0) {
			var account_holder = ($('input[name="account_holder"]').val()).trim();
			var iban = ($('input[name="iban"]').val()).trim();
			var bic = ($('input[name="bic"]').val()).trim();

			if (account_holder == '' || iban == '' || bic == '') {
				e.preventDefault();
				var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> Your bank details are necessary for the payment by bank transfer.
				</div>`);
				$('form[name="confirm_booking_form"]').prepend(message);
				$(message).fadeIn();
			}
		}
	});
});
</script>
<?php require 'includes/footer.php'; ?>