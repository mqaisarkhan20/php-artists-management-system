<?php 

require 'includes/config.php';

if (!isset($_SESSION['username'])) {
	header('Location: ' . URL . 'login.php');
	exit;
}

if (isset($_POST['update'])) {
	$event_id = clean_input($_POST['event_id']);
	$name = clean_input($_POST['name']);
	$date = clean_input($_POST['date']);
	$start_time = clean_input($_POST['start_time']);
	$end_time = clean_input($_POST['end_time']);

	$previous_event = $db->single_row("SELECT * FROM events WHERE name = '$name'");

	if (false) { // count($previous_event) > 0
		$_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> Event with same name already exist.
		</div>';

		header('location: ' . URL);
		exit;
	} else {
		$data = array(
			'name' => ucfirst($name),
			'date' => $date,
			'start_time' => $start_time,
			'end_time' => $end_time
		);
		if ($db->update('events', $data, ['id' => $event_id])) {
			$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Event updated successfully.
			</div>';

			header('location: ' . URL);
			exit;
		}
	}
}

if (isset($_POST['submit'])) {
	$name = clean_input($_POST['name']);
	$date = clean_input($_POST['date']);
	$start_time = clean_input($_POST['start_time']);
	$end_time = clean_input($_POST['end_time']);

	$previous_event = $db->single_row("SELECT * FROM events WHERE name = '$name'");

	if (count($previous_event) > 0) {
		$_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> Event with same name already exist.
		</div>';

		header('location: ' . URL);
		exit;
	} else {
		$data = array(
			'name' => ucfirst($name),
			'date' => $date,
			'start_time' => $start_time,
			'end_time' => $end_time
		);
		if ($db->insert('events', $data)) {
			$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Event saved successfully.
			</div>';

			header('location: ' . URL);
			exit;
		}
	}
}

if (isset($_GET['delete'])) {
	$id = clean_input($_GET['delete']);

	if ($db->delete('events', ['id' => $id])) {
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Event deleted successfully.
		</div>';

		header('location: ' . URL);
		exit;
	}
}

$events = $db->multiple_row("SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC");
$nav_active = 'events';

require 'includes/header.php';
require 'includes/navigation.php';
require 'includes/sidebar.php';

?>

<div class="container">
	<h2>Add event</h2>
	<div class="row">
		<div class="col-md-4">
			<?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
			<?php unset($_SESSION['message']); ?>
			<form name="add_event" action="" method="POST">
				<div class="form-group">
					<label for="name">Event name:</label>
					<input type="text" name="name" class="form-control">
				</div>

				<div class="form-group">
					<label for="date">Date:</label>
					<input type="date" name="date" class="form-control">
				</div>

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
			<h2>Events:</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Name</th>
						<th>Date</th>
						<th>Start time</th>
						<th>End time</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($events)): ?>
						<?php foreach($events as $event): ?>
							<tr>
								<td><?= $event['name'] ?></td>
								<td><?= dotted_date($event['date']); ?></td>
								<td><?= $event['start_time'] ?></td>
								<td><?= $event['end_time'] ?></td>
								<td>
									<a onclick="return confirm('Are you sure?');" href="<?= URL ?>?delete=<?= $event['id'] ?>"><i class="fas fa-trash-alt delete"></a></i>&nbsp;
									<i class="fas fa-edit update cursor_pointer" data-id="<?= $event['id'] ?>">
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
					<tr>
						<td colspan="5"><i>No event saved yet!</i></td>
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
        <h4 class="modal-title">Update event:</h4>
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

	$('form[name="add_event"]').submit(function(e) {
		$('.alert').remove();
		
		var name = ($('input[name="name"]').val()).trim();
		var date = ($('input[name="date"]').val()).trim();
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

		if (name == '' || date == '' || start_time == '' || end_time == '') {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> All fields are required.
			</div>`);
			$('form[name="add_event"]').prepend(message);
			$(message).fadeIn();
		} else if (false) { // timeto < timefrom
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> End time should be greater then start time.
			</div>`);
			$('form[name="add_event"]').prepend(message);
			$(message).fadeIn();
		}
	});

	$('.update').click(function() {
		var event_id = $(this).attr('data-id');
		var tds = $(this).closest('tr').find('td');
		var event_name = $(tds[0]).html();
		var date = $(tds[1]).html();
		var start_time = $(tds[2]).html();
		var end_time = $(tds[3]).html();
		var form_html_el = $(`<form name="update_event" action="" method="POST">
				<input type="hidden" name="event_id" value="${event_id}">

				<div class="form-group">
					<label for="name">Event name:</label>
					<input type="text" name="name" class="form-control" value="${event_name}">
				</div>

				<div class="form-group">
					<label for="date">Date:</label>
					<input type="date" name="date" class="form-control" value="${date}">
				</div>

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

			var name = ($(form_html_el).find('input[name="name"]').val()).trim();
			var date = ($(form_html_el).find('input[name="date"]').val()).trim();
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

			if (name == '' || date == '' || start_time == '' || end_time == '') {
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