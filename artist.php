<?php 

require 'includes/config.php';

if (!isset($_SESSION['username'])) {
	header('Location: ' . URL . 'login.php');
	exit;
}

if (isset($_POST['update'])) {
	$artist_id = clean_input($_POST['artist_id']);
	$dj_name = clean_input($_POST['dj_name']);
	$backline = clean_input($_POST['backline']);
	$mobile = clean_input($_POST['mobile']);
	$email = clean_input($_POST['email']);
	$surename = clean_input($_POST['surename']);
	$name = clean_input($_POST['name']);
	$street = clean_input($_POST['street']);
	$zip_code = clean_input($_POST['zip_code']);
	$city = clean_input($_POST['city']);
	$country = clean_input($_POST['country']);
	$vat_id = clean_input($_POST['vat_id']);
	$facebook = clean_input($_POST['facebook']);
	$soundcloud = clean_input($_POST['soundcloud']);
	$instagram = clean_input($_POST['instagram']);
	// $previous_event = $db->single_row("SELECT * FROM events WHERE name = '$name'");

	if (false) { // count($previous_event) > 0)
		$_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> Event with same name already exist.
		</div>';

		header('location: ' . URL);
		exit;
	} else {
		$data = array(
			'dj_name' => $dj_name,
			'backline' => $backline,
			'mobile' => $mobile,
			'email' => $email,
			'surename' => $surename,
			'name' => $name,
			'street' => $street,
			'zip_code' => $zip_code,
			'city' => $city,
			'country' => $country,
			'vat_id' => $vat_id,
			'facebook' => $facebook,
			'soundcloud' => $soundcloud,
			'instagram' => $instagram
		);

		if ($db->update('artists', $data, ['id' => $artist_id])) {
			$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Artist updated successfully.
			</div>';

			header('location: ' . URL . 'artist.php');
			exit;
		}
	}
}

if (isset($_POST['submit'])) {
	$dj_name = clean_input($_POST['dj_name']);
	$backline = clean_input($_POST['backline']);
	$mobile = clean_input($_POST['mobile']);
	$email = clean_input($_POST['email']);
	$surename = clean_input($_POST['surename']);
	$name = clean_input($_POST['name']);
	$street = clean_input($_POST['street']);
	$zip_code = clean_input($_POST['zip_code']);
	$city = clean_input($_POST['city']);
	$country = clean_input($_POST['country']);
	$vat_id = clean_input($_POST['vat_id']);
	$facebook = clean_input($_POST['facebook']);
	$soundcloud = clean_input($_POST['soundcloud']);
	$instagram = clean_input($_POST['instagram']);

	// $previous_artist = $db->single_row("");

	if (false) { // count($previous_event) > 0
		$_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Error!</strong> Artist with same {variable} already exist.
		</div>';

		header('location: ' . URL . 'artist.php');
		exit;
	} else {
		$data = array(
			'dj_name' => $dj_name,
			'backline' => $backline,
			'mobile' => $mobile,
			'email' => $email,
			'surename' => $surename,
			'name' => $name,
			'street' => $street,
			'zip_code' => $zip_code,
			'city' => $city,
			'country' => $country,
			'vat_id' => $vat_id,
			'facebook' => $facebook,
			'soundcloud' => $soundcloud,
			'instagram' => $instagram
		);
		if ($db->insert('artists', $data)) {
			$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Success!</strong> Artist added successfully.
			</div>';

			header('location: ' . URL . 'artist.php');
			exit;
		}
	}
}

if (isset($_GET['delete'])) {
	$id = clean_input($_GET['delete']);

	if ($db->delete('artists', ['id' => $id])) {
		$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Success!</strong> Artist deleted successfully.
		</div>';

		header('location: ' . URL . 'artist.php');
		exit;
	}
}

$artists = $db->multiple_row("SELECT * FROM artists ORDER BY id DESC");
$nav_active = 'artists';

require 'includes/header.php';
require 'includes/navigation.php';
require 'includes/sidebar.php';

?>

<div class="container">
	<h2>Add artist:</h2>
	<div class="row">
		<div class="col-md-10">
			<?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
			<?php unset($_SESSION['message']); ?>
		</div>
	</div>
	<form name="add_artist" action="" method="POST">
		<div class="row">
			<div class="col-md-9 form_message_container">
				
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="dj_name">DJ name:</label>
					<input type="text" name="dj_name" class="form-control">
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="backline">backline:</label>
					<input type="text" name="backline" class="form-control">
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="mobile">mobile:</label>
					<input type="text" name="mobile" class="form-control">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="email">email:</label>
					<input type="text" name="email" class="form-control">
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="surename">surename:</label>
					<input type="text" name="surename" class="form-control">
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="name">name:</label>
					<input type="text" name="name" class="form-control">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-9">
				<div class="form-group">
					<label for="street">Street:</label>
					<input type="text" name="street" class="form-control">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="zip_code">zip code:</label>
					<input type="text" name="zip_code" class="form-control">
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="city">city:</label>
					<input type="text" name="city" class="form-control">
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="country">country:</label>
					<input type="text" name="country" class="form-control">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="vat_id">vat id:</label>
					<input type="text" name="vat_id" class="form-control">
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="facebook">facebook:</label>
					<input type="text" name="facebook" class="form-control">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="soundcloud">soundcloud:</label>
					<input type="text" name="soundcloud" class="form-control">
				</div>
			</div>

			<div class="col-md-5">
				<div class="form-group">
					<label for="instagram">instagram:</label>
					<input type="text" name="instagram" class="form-control">
				</div>
			</div>
		</div>

		

		<div class="form-group">
			<input type="submit" name="submit" value="Submit" class="btn btn-primary btn-sm">
		</div>
	</form>

	

	<div class="row">
		<div class="col-md-10">
			<hr>
			<h2>Artists:</h2>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Dj name</th>
						<th>Backline</th>
						<th>Mobile</th>
						<th>Email</th>
						<th>City</th>
						<th>Vat id</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php if (count($artists) > 0): ?>
						<?php foreach($artists as $artist): ?>
							<tr>
								<td><?= $artist['dj_name'] ?></td>
								<td><?= $artist['backline'] ?></td>
								<td><?= $artist['mobile'] ?></td>
								<td><?= $artist['email'] ?></td>
								<td><?= $artist['city'] ?></td>
								<td><?= $artist['vat_id'] ?></td>
								<td>
									<i class="fas fa-eye view cursor_pointer" data-id="<?= $artist['id'] ?>"></i>&nbsp;
									<a onclick="return confirm('Are you sure?');" href="<?= URL ?>artist.php?delete=<?= $artist['id'] ?>"><i class="fas fa-trash-alt delete"></a></i>&nbsp;
									<i class="fas fa-edit update cursor_pointer" data-id="<?= $artist['id'] ?>" style="color: #ffc107!important;"></i>
									<a href="https://<?= $artist['facebook'] ?>" target="_blank"><i class="fab fa-facebook-f cursor_pointer"></i></a>
									<a href="https://<?= $artist['soundcloud'] ?>" target="_blank"><i class="fab fa-soundcloud cursor_pointer"></i></a>
									<a href="https://<?= $artist['instagram'] ?>" target="_blank"><i class="fab fa-instagram cursor_pointer"></i></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
					<tr>
						<td colspan="9"><i>No artist saved yet!</i></td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- The Modal -->
<div class="modal" id="view_artist_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Artist info:</h4>
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

<!-- The Modal -->
<div class="modal" id="update_form_modal">
  <div class="modal-dialog modal-lg">
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
	function validateEmail(email) {
	  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  return re.test(String(email).toLowerCase());
	}
	function capitalizeFirstLetter(string) {
	  return string.charAt(0).toUpperCase() + string.slice(1);
	}
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

	$('form[name="add_artist"]').submit(function(e) {
		$('.alert').remove();
		
		var dj_name = ($('input[name="dj_name"]').val()).trim();
		var backline = ($('input[name="backline"]').val()).trim();
		var mobile = ($('input[name="mobile"]').val()).trim();
		var email = ($('input[name="email"]').val()).trim();
		var surename = ($('input[name="surename"]').val()).trim();
		var name = ($('input[name="name"]').val()).trim();
		var street = ($('input[name="street"]').val()).trim();
		var zip_code = ($('input[name="zip_code"]').val()).trim();
		var city = ($('input[name="city"]').val()).trim();
		var country = ($('input[name="country"]').val()).trim();
		var vat_id = ($('input[name="vat_id"]').val()).trim();
		var facebook = ($('input[name="facebook"]').val()).trim();
		var soundcloud = ($('input[name="soundcloud"]').val()).trim();
		var instagram = ($('input[name="instagram"]').val()).trim();

		if (dj_name == '' || backline == '' || mobile == '' || email == '' || surename == '' || name == '' || street == '' || zip_code == '' || city == '' || country == '' || vat_id == '' || facebook == '' || soundcloud == '' || instagram == '') {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> All fields are required.
			</div>`);
			$('.form_message_container').html(message);
			$(message).fadeIn();
		} else if (!validateEmail(email)) {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> Enter valid email address.
			</div>`);
			$('.form_message_container').html(message);
			$(message).fadeIn();
		}
	});

	$('.view').click(function() {
		var artist_id = $(this).attr('data-id');

		$.ajax({
			url: 'ajax.php',
			method: 'GET',
			data: {
				'get_artist': true,
				'id': artist_id
			},
			success: function(data) {
				var artist = data;
				if (typeof artist.id !== undefined) {
					$('#view_artist_modal').modal();
					var artist_information = `
					<div class="row">
						<div class="col-md-5">
							<ul class="list-group">
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Dj name:</strong>
							    <span class="">${capitalizeFirstLetter(artist.dj_name)}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Backline:</strong>
							    <span class="">${artist.backline}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Mobile:</strong>
							    <span class="">${artist.mobile}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Email:</strong>
							    <span class="">${artist.email}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Surename:</strong>
							    <span class="">${capitalizeFirstLetter(artist.surename)}</span>
							  </li>
							</ul>
						</div>
						<div class="col-md-5">
							<ul class="list-group">
								<li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Name:</strong>
							    <span class="">${capitalizeFirstLetter(artist.name)}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Zip code:</strong>
							    <span class="">${artist.zip_code}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>City:</strong>
							    <span class="">${capitalizeFirstLetter(artist.city)}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Country:</strong>
							    <span class="">${capitalizeFirstLetter(artist.country)}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							    <strong>Vat id:</strong>
							    <span class="">${artist.vat_id}</span>
							  </li>
							</ul>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-md-10">
							<ul class="list-group">
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							  	<strong>Street:</strong>
							    <span class="">${artist.street}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							  	<strong>Facebook url:</strong>
							    <span class="">${artist.facebook}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							  	<strong>Soundcloud url:</strong>
							    <span class="">${artist.soundcloud}</span>
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							  	<strong>Instagram url:</strong>
							    <span class="">${artist.instagram}</span>
							  </li>
							</ul>
						</div>
					</div>
					`;
					$('#view_artist_modal').find('.modal-body').html(artist_information);
					$('#view_artist_modal').modal();
				}
			},
			error: function(error) {
				console.log(error);
			}
		});
	})

	$('.update').click(function() {
		var artist_id = $(this).attr('data-id');
		$.ajax({
			url: 'ajax.php',
			method: 'GET',
			data: {
				'get_artist': true,
				'id': artist_id
			},
			success: function(data) {
				var artist = data;
				if (typeof artist.id !== undefined) {
							var form_html_el = $(`<form name="update_artist" action="artist.php" method="POST">
				<input type="hidden" name="artist_id" value="${artist.id}">
				<div class="row">
					<div class="col-md-12 form_message_container">
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="dj_name">DJ name:</label>
							<input type="text" name="dj_name" class="form-control" value="${artist.dj_name}">
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="backline">backline:</label>
							<input type="text" name="backline" class="form-control" value="${artist.backline}">
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="mobile">mobile:</label>
							<input type="text" name="mobile" class="form-control" value="${artist.mobile}">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="email">email:</label>
							<input type="text" name="email" class="form-control" value="${artist.email}">
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="surename">surename:</label>
							<input type="text" name="surename" class="form-control" value="${artist.surename}">
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="name">name:</label>
							<input type="text" name="name" class="form-control" value="${artist.name}">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="street">Street:</label>
							<input type="text" name="street" class="form-control" value="${artist.street}">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="zip_code">zip code:</label>
							<input type="text" name="zip_code" class="form-control" value="${artist.zip_code}">
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="city">city:</label>
							<input type="text" name="city" class="form-control" value="${artist.city}">
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="country">country:</label>
							<input type="text" name="country" class="form-control" value="${artist.country}">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="vat_id">vat id:</label>
							<input type="text" name="vat_id" class="form-control" value="${artist.vat_id}">
						</div>
					</div>

					<div class="col-md-8">
						<div class="form-group">
							<label for="facebook">facebook:</label>
							<input type="text" name="facebook" class="form-control" value="${artist.facebook}">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-5">
						<div class="form-group">
							<label for="soundcloud">soundcloud:</label>
							<input type="text" name="soundcloud" class="form-control" value="${artist.soundcloud}">
						</div>
					</div>

					<div class="col-md-7">
						<div class="form-group">
							<label for="instagram">instagram:</label>
							<input type="text" name="instagram" class="form-control" value="${artist.instagram}">
						</div>
					</div>
				</div>

				

				<div class="form-group">
					<input type="submit" name="update" value="Update" class="btn btn-primary btn-sm">
				</div>
			</form>`);
		var modal_body = $("#update_form_modal").find('.modal-body');
		$(form_html_el).submit(function(e) {
			$('.alert').remove();

			var dj_name = ($(form_html_el).find('input[name="dj_name"]').val()).trim();
			var backline = ($(form_html_el).find('input[name="backline"]').val()).trim();
			var mobile = ($(form_html_el).find('input[name="mobile"]').val()).trim();
			var email = ($(form_html_el).find('input[name="email"]').val()).trim();
			var surename = ($(form_html_el).find('input[name="surename"]').val()).trim();
			var name = ($(form_html_el).find('input[name="name"]').val()).trim();
			var street = ($(form_html_el).find('input[name="street"]').val()).trim();
			var zip_code = ($(form_html_el).find('input[name="zip_code"]').val()).trim();
			var city = ($(form_html_el).find('input[name="city"]').val()).trim();
			var country = ($(form_html_el).find('input[name="country"]').val()).trim();
			var vat_id = ($(form_html_el).find('input[name="vat_id"]').val()).trim();
			var facebook = ($(form_html_el).find('input[name="facebook"]').val()).trim();
			var soundcloud = ($(form_html_el).find('input[name="soundcloud"]').val()).trim();
			var instagram = ($(form_html_el).find('input[name="instagram"]').val()).trim();

			if (dj_name == '' || backline == '' || mobile == '' || email == '' || surename == '' || name == '' || street == '' || zip_code == '' || city == '' || country == '' || vat_id == '' || facebook == '' || soundcloud == '' || instagram == '') {
				e.preventDefault();
				var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> All fields are required.
				</div>`);
				$('.form_message_container').prepend(message);
				$(message).fadeIn();
			} else if (!validateEmail(email)) {
				e.preventDefault();
				var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> Please enter correct email.
				</div>`);
				$('.form_message_container').prepend(message);
				$(message).fadeIn();
			}
		});
		$(modal_body).html(form_html_el); 
		// console.log(.find('input[name="name"]'));

		$("#update_form_modal").modal();
				}
			},
			error: function(error) {
				console.log(error);
			}
		});
	});

});
</script>
<?php require 'includes/footer.php'; ?>