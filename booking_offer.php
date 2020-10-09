<?php 

require 'includes/config.php';

if (!isset($_SESSION['username'])) {
	header('Location: ' . URL . 'login.php');
	exit;
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
	$event = clean_input($_POST['event']);
	$artist = clean_input($_POST['artist']);
	$floor = clean_input($_POST['floor']);
	$slot = clean_input($_POST['slot']);
	$artist_fees = clean_input($_POST['artist_fees']);

	$artist_info = $db->single_row("SELECT * FROM artists WHERE id = $artist");

	do {
		$code_md5 = md5(uniqid(rand(), true));
		$db_code_md5 = $db->single_row("SELECT * FROM bookings WHERE code_md5 = '$code_md5'");
	} while (count($db_code_md5) != 0);

	$data = array(
		'event_id' => $event,
		'artist_id' => $artist,
		'floor_id' => $floor,
		'slot_id' => $slot,
		'artist_fees_id' => $artist_fees,
		'tax_id' => 1,
		'status_id' => 1,
		'booking_active' => 1,
		'code_md5' => $code_md5
	);

	if ($db->insert('bookings', $data)) {
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Booking saved successfully.
		</div>';

		$from = 'archive@abcd.skf';
		$to = (isset($artist_info['email'])) ? $artist_info['email']: '';
		$subject = 'Sumanko Booking Interface | Booking offer ' . $artist_info['dj_name'];
		$body = 'Hi '. ucfirst($artist_info['surename']) .',<br>
			<br>
			A new booking offer just has arrived. For details please follow this link:
			http://abcd.skf/request.php?booking='. $code_md5 .'
			<br><br>
			All the best and kind regards,
			<br>
			Sumanko Booking Team
			';
		// send_email($from, $to, $subject, $body);

		header('location: ' . URL . 'booking_offer.php');
		exit;
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

$events = $db->multiple_row("SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC");
$artists = $db->multiple_row("SELECT * FROM artists ORDER BY dj_name ASC");
$floors = $db->multiple_row("SELECT * FROM floors ORDER BY id DESC");
$slots = $db->multiple_row("SELECT * FROM slots ORDER BY start_time ASC");
$artist_fees = $db->multiple_row("SELECT * FROM artist_fees ORDER BY fees");
$bookings = $db->multiple_row("SELECT bookings.id, events.name as event_name, artists.dj_name as artist_dj_name, floors.name as floor_name, slots.start_time as slot_start_time, slots.end_time as slot_end_time, artist_fees.fees as artist_fees, bookings.tax_id,bookings.status_id, bookings.booking_active, bookings.code_md5
FROM bookings
LEFT JOIN events ON bookings.event_id = events.id
LEFT JOIN artists ON bookings.artist_id = artists.id
LEFT JOIN floors ON bookings.floor_id = floors.id
LEFT JOIN slots ON bookings.slot_id = slots.id
LEFT JOIN artist_fees ON bookings.artist_fees_id = artist_fees.id
ORDER BY bookings.id DESC");
$nav_active = 'booking_offer';

if (count($events) == 0) {
	$no_events_msg = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> No event saved yet.
		</div>';
}
if (count($artists) == 0) {
	$no_artists_msg = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> No artist saved yet.
		</div>';
}
if (count($floors) == 0) {
	$no_floors_msg = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> No floor saved yet.
		</div>';
}
if (count($slots) == 0) {
	$no_slots_msg = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> No slot saved yet.
		</div>';
}
if (count($artist_fees) == 0) {
	$no_artist_fees_msg = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> No artist fees saved yet.
		</div>';
}

require 'includes/header.php';
require 'includes/navigation.php';
require 'includes/sidebar.php';

?>

<div class="container">
	<h2>Create booking</h2>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-warning alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Note:</strong> Email featured is disabled currently other wise email is sent to artist.If He accepts then status is shown as accepted and on rejection it is shown as rejected.
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			
			<?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
			<?php unset($_SESSION['message']); ?>
			<?= (isset($no_events_msg)) ? $no_events_msg: false; ?>
			<?= (isset($no_artists_msg)) ? $no_artists_msg: false; ?>
			<?= (isset($no_floors_msg)) ? $no_floors_msg: false; ?>
			<?= (isset($no_slots_msg)) ? $no_slots_msg: false; ?>
			<?= (isset($no_artist_fees_msg)) ? $no_artist_fees_msg: false; ?>
		</div>
	</div>
			<form name="save_booking" action="booking_offer.php" method="POST">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="event">Select event:</label>
							<select name="event" id="event" class="form-control">
								<?php if (count($events) > 0): ?>
									<option value="">Select event</option>
									<?php foreach ($events as $event): ?>
										<option value="<?= $event['id']; ?>"><?= ucfirst($event['name']) ?></option>
									<?php endforeach; ?> 
								<?php endif; ?>
							</select>
						</div>

						<div class="form-group">
							<label for="artist">Select artist:</label>
							<select name="artist" id="artist" class="form-control">
								<?php if (count($artists) > 0): ?>
									<option value="">Select artist</option>
									<?php foreach ($artists as $artist): ?>
										<option value="<?= $artist['id']; ?>"><?= ucfirst($artist['dj_name']) . " ($artist[backline])" ?></option>
									<?php endforeach; ?> 
								<?php endif; ?>
							</select>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="floor">Select floor:</label>
							<select name="floor" id="floor" class="form-control">
								<?php if (count($floors) > 0): ?>
									<option value="">Select floor</option>
									<?php foreach ($floors as $floor): ?>
										<option value="<?= $floor['id']; ?>"><?= ucfirst($floor['name']) ?></option>
									<?php endforeach; ?> 
								<?php endif; ?>
							</select>
						</div>

						<div class="form-group">
							<label for="slot">Select slot:</label>
							<select name="slot" id="slot" class="form-control">
								<?php if (count($slots) > 0): ?>
									<option value="">Select slot</option>
									<?php foreach ($slots as $slot): ?>
										<option value="<?= $slot['id']; ?>"><?= $slot['start_time'] . ' - ' . $slot['end_time'] ?></option>
									<?php endforeach; ?> 
								<?php endif; ?>
							</select>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="artist_fees">Select artist fees:</label>
							<select name="artist_fees" id="artist_fees" class="form-control">
								<?php if (count($artist_fees) > 0): ?>
									<option value="">Select fees</option>
									<?php foreach ($artist_fees as $fees): ?>
										<option value="<?= $fees['id']; ?>"><?= eu_currency($fees['fees']); ?></option>
									<?php endforeach; ?> 
								<?php endif; ?>
							</select>
						</div>
					</div>
				</div>
				

				<div class="form-group">
					<input type="submit" name="submit" value="Submit" class="btn btn-primary btn-sm">
				</div>
			</form>

	

	<div class="row">
		<div class="col-md-12">
			<hr>
			<h2>Bookings:</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Event</th>
						<th>Artist</th>
						<th>Floor</th>
						<th>Slot</th>
						<th>Fees</th>
						<th>Booking code</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($bookings)): ?>
						<?php foreach($bookings as $booking): ?>
							<tr>
								<td><?= $booking['event_name'] ?></td>
								<td><?= $booking['artist_dj_name'] ?></td>
								<td><?= $booking['floor_name'] ?></td>
								<td><?= $booking['slot_start_time'] . ' - ' . $booking['slot_end_time'] ?></td>
								<td><?= eu_currency($booking['artist_fees']) ?></td>
								<td><?= $booking['code_md5'] ?></td>
								<!-- <td>
									<a onclick="return confirm('Are you sure?');" href="<?= URL ?>booking.php?delete=<?= $booking['id'] ?>"><i class="fas fa-trash-alt delete"></a></i>&nbsp;
									<i class="fas fa-edit update cursor_pointer" data-id="<?= $booking['id'] ?>">
								</td> -->
								<td><?php
								if ($booking['status_id'] == 1) {
									echo '<span class="text-warning">Unattended</span>';
								} else if ($booking['status_id'] == 3) {
									echo '<span class="text-success">Acceped</span> <a href="'.URL.'invoice.php?booking='.$booking['code_md5'].'" target="_blank">Invoice</a>';
								} else if ($booking['status_id'] == 2) {
									echo '<span class="text-danger">Declined</span>';
								}

								?></td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
					<tr>
						<td colspan="7"><i>No booking saved yet!</i></td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- The Modal -->
<div class="modal" id="update_form_modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Update slot:</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

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

	$.each($('td.eu_currency, option.eu_currency'), function(a, b) {
		var value = parseInt($(this).text());
		$(this).text(eu_format.format(value));
	});

	$('form[name="save_booking"]').submit(function(e) {
		$('.alert').remove();
		
		var event = ($('select[name="event"]').val()).trim();
		var artist = ($('select[name="artist"]').val()).trim();
		var floor = ($('select[name="floor"]').val()).trim();
		var slot = ($('select[name="slot"]').val()).trim();
		var artist_fees = ($('select[name="artist_fees"]').val()).trim();

		if (event == '' || artist == '' || floor == '' || slot == '' || artist_fees == '') {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> All fields are required.
			</div>`);
			$('form[name="save_booking"]').prepend(message);
			$(message).fadeIn();
		}
	});

	$('.update').click(function() {
		var event_id = $(this).attr('data-id');
		var tds = $(this).closest('tr').find('td');
		var duration_string = $(tds[0]).html();
		var start_time = duration_string.split(' - ')[0];
		var end_time = duration_string.split(' - ')[1];
		var form_html_el = $(`<form name="update_event" action="slot.php" method="POST">
				<input type="hidden" name="slot_id" value="${event_id}">

				<div class="form-group">
					<label for="start_time">Start time:</label>
					<input type="time" name="start_time" class="form-control time" value="${start_time}">
				</div>

				<div class="form-group">
					<label for="end_time">End time:</label>
					<input type="time" name="end_time" class="form-control time" value="${end_time}">
				</div>

				<div class="form-group">
					<input type="submit" name="update" value="Update" class="btn btn-primary btn-sm">
				</div>
			</form>`);
		var modal_body = $("#update_form_modal").find('.modal-body');
		$(form_html_el).submit(function(e) {
			$('.alert').remove();

			var start_time = ($(form_html_el).find('input[name="start_time"]').val()).trim();
			var end_time = ($(form_html_el).find('input[name="end_time"]').val()).trim();

			// check if end time is greater then start time or not
			var timefrom = new Date();
			temp = start_time.split(":");
			timefrom.setHours((parseInt(temp[0]) - 1 + 24) % 24);
			timefrom.setMinutes(parseInt(temp[1]));

			var timeto = new Date();
			temp = end_time.split(":");
			timeto.setHours((parseInt(temp[0]) - 1 + 24) % 24);
			timeto.setMinutes(parseInt(temp[1]));

			if (start_time == '' || end_time == '') {
				e.preventDefault();
				var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> All fields are required.
				</div>`);
				$('form[name="update_event"]').prepend(message);
				$(message).fadeIn();
			} else if (timeto < timefrom) {
				e.preventDefault();
				var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> End time should be greater then start time.
				</div>`);
				$('form[name="update_event"]').prepend(message);
				$(message).fadeIn();
			}
		});
		$(modal_body).html(form_html_el); 
		// console.log(.find('input[name="name"]'));

		$("#update_form_modal").modal();
	});

});
</script>
<?php require 'includes/footer.php'; ?>