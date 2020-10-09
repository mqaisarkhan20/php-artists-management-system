<?php 

require 'includes/config.php';

if (!isset($_SESSION['username'])) {
	header('Location: ' . URL . 'login.php');
	exit;
}

if (isset($_POST['submit'])) {
	$username = clean_input($_POST['username']);
	$old_password = clean_input($_POST['old_password']);
	$new_password = clean_input($_POST['new_password']);
	$confirm_password = clean_input($_POST['confirm_password']);

	$admin = $db->single_row("SELECT * FROM users WHERE id = 1");

	if (isset($admin['username'])) {
		if (password_verify($old_password, $admin['password'])) {
		  $password = password_hash($new_password, PASSWORD_BCRYPT, array('cost' => 14));

		  $data = Array(
		  	'username' => $username,
		  	'password' => $password
		  );

		  if ($db->update('users', $data, ['id' => 1])) {
		  	$_SESSION['message'] = '<div class="alert alert-success alert-dismissible">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Error!</strong> Username/password updated successfully.
				</div>';

			  header('Location: ' . URL . 'setting.php');
			  exit;
		  }
		} else {
		  $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> Incorrect old password.
			</div>';

		  header('Location: ' . URL . 'setting.php');
		  exit;
		}
	}
}

$admin = $db->single_row("SELECT * FROM users WHERE id = 1");
$nav_active = 'setting';

require 'includes/header.php';
require 'includes/navigation.php';
require 'includes/sidebar.php';

?>

<div class="container">
	<h2>Admin settings:</h2>
	<form name="admin_setting" action="" method="POST">
		<div class="row">
			<div class="col-md-4 col-sm-6">
				<?= (isset($_SESSION['message'])) ? $_SESSION['message']: false; ?>
				<?php unset($_SESSION['message']); ?>

				<div class="message-box">
					
				</div>

				<div class="form-group">
					<label for="">Username:</label>
					<input type="text" name="username" class="form-control" value="<?= $admin['username'] ?>">
				</div>

				<div class="form-group">
					<label for="">Old password:</label>
					<input type="password" name="old_password" class="form-control">
				</div>

				<div class="form-group">
					<label for="">New password:</label>
					<input type="password" name="new_password" class="form-control">
				</div>

				<div class="form-group">
					<label for="">Confirm password:</label>
					<input type="password" name="confirm_password" class="form-control">
				</div>

				<div class="form-group">
					<input type="submit" name="submit" value="Submit" class="btn btn-primary btn-sm">
				</div>
			</div>
		</div>
	</form>
	

</div>

<script>
$(document).ready(function() {

	$('form[name="admin_setting"]').submit(function(e) {
		$('.alert').remove();
		
		var username = ($('input[name="username"]').val()).trim();
		var old_password = ($('input[name="old_password"]').val()).trim();
		var new_password = ($('input[name="new_password"]').val()).trim();
		var confirm_password = ($('input[name="confirm_password"]').val()).trim();

		if (username == '' || old_password == '' || new_password == '' || confirm_password == '') {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> All fields are required.
			</div>`);
			$('.message-box').html(message);
			$(message).fadeIn();
		} else if (old_password == new_password) {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> New password must be different.
			</div>`);
			$('.message-box').html(message);
			$(message).fadeIn();
		} else if (new_password != confirm_password) {
			e.preventDefault();
			var message = $(`<div class="alert alert-danger alert-dismissible" style="display: none;">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Error!</strong> Password not same.
			</div>`);
			$('.message-box').html(message);
			$(message).fadeIn();
		}
	});

});
</script>
<?php require 'includes/footer.php'; ?>