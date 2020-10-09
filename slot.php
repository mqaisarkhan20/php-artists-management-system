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
require 'includes/navigation.php';
require 'includes/sidebar.php';

?>

<div class="container">
	<h2>Add Slot</h2>
	<div class="row">
		<div class="col-md-4">
			<?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
			<?php unset($_SESSION['message']); ?>
			<form name="add_slot" action="slot.php" method="POST">
				<div class="form-group">
					<label for="start_time">Start time:</label>
					<input type="time" name="start_time" class="form-control time">
				</div>

				<div class="form-group">
					<label for="end_time">End time:</label>
					<input type="time" name="end_time" class="form-control time">
				</div>

				<div class="form-group">
					<input type="submit" name="submit" value="Submit" class="btn btn-primary btn-sm">
				</div>
			</form>
		</div>
	</div>

	

	<div class="row">
		<div class="col-md-6">
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

	// $('.time').timepicker({
	//   timeFormat: 'HH:mm',
	//   interval: 60,
	//   // minTime: '10',
	//   // maxTime: '6:00pm',
	//   // defaultTime: '11',
	//   // startTime: '10:00',
	//   dynamic: false,
	//   dropdown: true,
	//   scrollbar: true
	// });

	$('form[name="add_slot"]').submit(function(e) {
		$('.alert').remove();
		
		var start_time = ($('input[name="start_time"]').val()).trim();
		var end_time = ($('input[name="end_time"]').val()).trim();

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
			$('form[name="add_slot"]').prepend(message);
			$(message).fadeIn();
		} else if (false) { // timeto < timefrom
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> End time should be greater then start time.
			</div>`);
			$('form[name="add_slot"]').prepend(message);
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
			} else if (false) { // timeto < timefrom
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