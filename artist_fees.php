<?php 

require 'includes/config.php';

if (!isset($_SESSION['username'])) {
	header('Location: ' . URL . 'login.php');
	exit;
}

if (isset($_POST['update'])) {
	$artist_fees_id = clean_input($_POST['artist_fees_id']);
	$fees = clean_input($_POST['fees']);

	$data = array(
		'fees' => $fees
	);
	if ($db->update('artist_fees', $data, ['id' => $artist_fees_id])) {
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Artist fees updated successfully.
		</div>';

		header('location: ' . URL . 'artist_fees.php');
		exit;
	}
}

if (isset($_POST['submit'])) {
	$fees = clean_input($_POST['fees']);
	$data = array(
		'fees' => $fees
	);

	if ($db->insert('artist_fees', $data)) {
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Fees saved successfully.
		</div>';

		header('location: ' . URL . 'artist_fees.php');
		exit;
	}
}

if (isset($_GET['delete'])) {
	$id = clean_input($_GET['delete']);

	if ($db->delete('artist_fees', ['id' => $id])) {
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Artist fees deleted successfully.
		</div>';

		header('location: ' . URL . 'artist_fees.php');
		exit;
	}
}

$artists = $db->multiple_row("SELECT * FROM artists");
$artist_fees = $db->multiple_row("SELECT * FROM artist_fees ORDER BY id DESC");
$nav_active = 'artist_fees';

require 'includes/header.php';
require 'includes/navigation.php';
require 'includes/sidebar.php';

?>

<div class="container">
	<h2>Add artist fee</h2>
	<div class="row">
		<div class="col-md-4">
			<?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
			<?php unset($_SESSION['message']); ?>
			<form name="add_fees" action="artist_fees.php" method="POST">

				<div class="form-group">
					<label for="fees">Fees:</label>
					<input type="number" name="fees" class="form-control">
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
			<h2>Artist fees:</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Fees</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($artist_fees) > 0): ?>
						<?php foreach($artist_fees as $af): ?>
							<tr>
								<td data-fees="<?= $af['fees'] ?>" class=""><?= eu_currency($af['fees']) ?></td>
								<td>
									<a onclick="return confirm('Are you sure?');" href="<?= URL ?>artist_fees.php?delete=<?= $af['id'] ?>"><i class="fas fa-trash-alt delete"></a></i>&nbsp;
									<i class="fas fa-edit update cursor_pointer" data-id="<?= $af['id'] ?>">
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
					<tr>
						<td colspan="3"><i>No fees saved yet!</i></td>
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
        <h4 class="modal-title">Update artist fees:</h4>
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

	$.each($('td.eu_currency'), function(a, b) {
		var value = parseInt($(this).text());
		$(this).text(eu_format.format(value));
	});

	$('form[name="add_fees"]').submit(function(e) {
		$('.alert').remove();
		var fees = ($('input[name="fees"]').val()).trim();

		if (fees == '') {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> Enter artist fees.
			</div>`);
			$('form[name="add_fees"]').prepend(message);
			$(message).fadeIn();
		}
	});

	$('.update').click(function() {
		var artist_fees_id = $(this).attr('data-id');
		var tds = $(this).closest('tr').find('td');
		var fees = $(tds[0]).attr('data-fees');
		var form_html_el = $(`<form name="update_fees" action="artist_fees.php" method="POST">
				<input type="hidden" name="artist_fees_id" value="${artist_fees_id}">

				<div class="form-group">
					<label for="fees">Artist fees:</label>
					<input type="number" name="fees" id="fees" class="form-control" value="${fees}">
				</div>

				<div class="form-group">
					<input type="submit" name="update" value="Update" class="btn btn-primary btn-sm">
				</div>
			</form>`);
		var modal_body = $("#update_form_modal").find('.modal-body');
		$(form_html_el).submit(function(e) {
			$('.alert').remove();

			var fees = ($(form_html_el).find('input[name="fees"]').val()).trim();

			if (fees == '') {
				e.preventDefault();
				var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> Enter artist fees.
				</div>`);
				$(form_html_el).prepend(message);
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