<?php 

require 'includes/config.php';

if (!isset($_SESSION['username'])) {
	header('Content-Type: application/json');
	echo json_encode(['error' => 'not logged in']);
	exit;
}

if (isset($_GET['get_artist'])) {
	$id = clean_input($_GET['id']);

	$artist = $db->single_row("SELECT * FROM artists WHERE id = $id");
	$artist_object = new stdClass; 
	if (count($artist) > 0) {
		foreach ($artist as $key => $value) {
			$artist_object->{$key} = $value;
		}
		
		header('Content-Type: application/json');
		echo json_encode($artist_object);
	}
}