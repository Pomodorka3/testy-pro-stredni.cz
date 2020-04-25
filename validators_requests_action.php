<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Administrator", "Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_GET['confirm_id'])) {
	$confirm_id = $_GET['confirm_id'];

	$select_request = sprintf("SELECT request_from FROM validators_requests WHERE request_id = '%d';",
	mysqli_real_escape_string($connect, $confirm_id));
	$select_request_query = mysqli_query($connect, $select_request);
	if (mysqli_num_rows($select_request_query) == 0) {
		handle_error("Tato žádost neexistuje!", "validators_requests_action");
	}
	$select_request_row = mysqli_fetch_row($select_request_query);

	$set_checked = sprintf("UPDATE validators_requests SET checked = 1, checked_by = '%d' WHERE request_id = '%d';",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $confirm_id));
	$set_checked_query = mysqli_query($connect, $set_checked);
	$add_validator = sprintf("INSERT INTO users_groups (user_id, group_id, event_date, set_by, set_method) VALUES ('%d', '2', '%s', '%d', 'On request');",
	mysqli_real_escape_string($connect, $select_request_row[0]),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $user_id));
	$add_validator_query = mysqli_query($connect, $add_validator);

	if ($add_validator_query) {

		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_request_row[0]),
		mysqli_real_escape_string($connect, "<span class='text-secondary font-weight-bold'>Gratulace! Od teď jste nový Validátor ve vaší škole.</span> Vaše žádost byla schválena uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>." ),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <b>úspěšně</b> přidán do skupiny Validátorů!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."validators_requests");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při potvrzování žádosti!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."validators_requests");
		exit();
	}
} elseif (isset($_GET['decline_id']) && isset($_POST['decline_reason'])) {
	$decline_id = $_GET['decline_id'];
	$decline_reason = htmlspecialchars(trim($_POST['decline_reason']));

	$select_request = sprintf("SELECT request_from FROM validators_requests WHERE request_id = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$select_request_query = mysqli_query($connect, $select_request);
	if (mysqli_num_rows($select_request_query) == 0) {
		handle_error("Tato žádost neexistuje!", "validators_requests_action");
	}
	$select_request_row = mysqli_fetch_row($select_request_query);

	$delete_request = sprintf("DELETE FROM validators_requests WHERE request_id = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$delete_request_query = mysqli_query($connect, $delete_request);

	if ($delete_request_query) {
		if ($decline_reason == "requirements") {
			$reason = "Nevyhovujete požadavkům";
		} elseif ($decline_reason == "activity") {
			$reason = "Vaše aktivita není dostatečná";
		} else {
			$reason = $decline_reason;
		}
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_request_row[0]),
		mysqli_real_escape_string($connect, "Vaše žádost o vstup do skupiny validátorů byla <span class='text-danger font-weight-bold'>odmítnuta</span> uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>. Důvod: ".$reason."." ),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tato žádost byla <b>úspěšne</b> odmítnuta!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."validators_requests");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odmítání žádosti!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."validators_requests");
		exit();
	}
} else {
	handle_error("Nebylo přijato dostatečně parametrů!", "validators_requests_action");
}