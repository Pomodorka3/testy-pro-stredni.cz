<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];
$new_name = htmlspecialchars(trim(ucfirst($_POST['edit_name'])));
$new_lastname = htmlspecialchars(trim(ucfirst($_POST['edit_lastname'])));
$instagram = htmlspecialchars(trim($_POST['edit_instagram']));
$facebook = htmlspecialchars(trim($_POST['edit_facebook']));

$mysqli_user_update = sprintf("UPDATE users SET first_name = '%s', last_name = '%s', instagram = '%s', facebook = '%s' WHERE user_id = '%d';", mysqli_real_escape_string($connect, $new_name),
	mysqli_real_escape_string($connect, $new_lastname),
	mysqli_real_escape_string($connect, $instagram),
	mysqli_real_escape_string($connect, $facebook),
	mysqli_real_escape_string($connect, $user_id));
$mysqli_user_update_query = mysqli_query($connect, $mysqli_user_update);

	if ($mysqli_user_update_query) {
		$_SESSION['success_message'] = "Váš profil byl úspěšně nastaven!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile");	
	} else {
		handle_error("Něco se stalo špatně při obnovení nastavení Vašeho profilu.");
	}
?>