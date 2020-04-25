<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];

if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id) || user_in_group("Support", $user_id) || user_in_group("Validator", $user_id)) {
	$_SESSION['error_message'] = "Už jste členem jedné ze skupin!";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."school_info");
	exit();
}

if (isset($_POST['biography'])) {
	$biography = htmlspecialchars(trim($_POST['biography']));

	$socialNetworks = sprintf("SELECT instagram, facebook FROM users WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$socialNetworks_query = mysqli_query($connect, $socialNetworks);
	$socialNetworks_row = mysqli_fetch_array($socialNetworks_query);
	if (empty($socialNetworks_row['instagram']) && empty($socialNetworks_row['facebook'])) {
		$_SESSION['error_message'] = "Musíte si nastavit alespoň 1 sociální síť v nastavení svého účtu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_info");
		exit();
	}

	/*
	$already_validator = sprintf("SELECT user_id FROM users_groups WHERE user_id = '%d' AND group_id = 2;", mysqli_real_escape_string($connect, $user_id));
	$already_validator_query = mysqli_query($connect, $already_validator);
	if (mysqli_num_rows($already_validator_query) != 0) {
		$_SESSION['error_message'] = "You are already a validator!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_info");
		exit();
	}*/

	$already_sent = sprintf("SELECT id FROM validators_requests WHERE request_from = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$already_sent_query = mysqli_query($connect, $already_sent);

	if (mysqli_num_rows($already_sent_query) != 0) {
		$_SESSION['error_message'] = "Už jste podal žádost o přidání do této skupiny!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_info");
		exit();
	}

	$sent_request = sprintf("INSERT INTO validators_requests (request_from, request_biography, request_date, request_school) VALUES ('%d', '%s', '%s', '%d');",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $biography),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$sent_request_query = mysqli_query($connect, $sent_request);

	if ($sent_request_query) {
		$_SESSION['success_message'] = "Vaše žádost o přidání do skupiny Validátorů byla úspěšně odesláná!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_info");
		exit();
	}
} else {
	handle_error("Nebylo získáno dostatečně informací!", "validators_apply");
}