<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];

if (($_GET['action'] == 'activateCode') && isset($_POST['code'])) {

	$code = trim($_POST['code']);

	$select_code = sprintf("SELECT code_type, code_value, code_expiration, code_used FROM codes WHERE code = '%s';",
	mysqli_real_escape_string($connect, $code));
	$select_code_query = mysqli_query($connect, $select_code);
	if (mysqli_num_rows($select_code_query) == 0) {
		$_SESSION['error_message'] = "Tento kód neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile_settings");
		exit();
	}

	$select_code_row = mysqli_fetch_array($select_code_query);

	if ($select_code_row['code_used'] != 0) {
		$_SESSION['error_message'] = "Tento kód už byl aktivován!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile_settings");
		exit();
	}

	if ($select_code_row['code_expiration'] < date('Y-m-d H:i:s')) {
		$_SESSION['error_message'] = "Tento kód již vypršel!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile_settings");
		exit();
	}

	if (($select_code_row['code_type'] == 'vip') && ($select_code_row['code_value'] != 0)) {
		$already_status = sprintf("SELECT status_userid FROM statuses WHERE status_userid = '%d';",
		mysqli_real_escape_string($connect, $user_id));
		$already_status_query = mysqli_query($connect, $already_status);
		if (mysqli_num_rows($already_status_query) != 0) {
			$_SESSION['error_message'] = "Už máte status!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile_settings");
			exit();
		}

		$status_expiration = date('Y-m-d H:i:s', strtotime('+ '.$select_code_row['code_value'].' days'));
		$status_expiration_formatted = date('d.m.Y H:i', strtotime('+ '.$select_code_row['code_value'].' days'));
		$add_status = sprintf("INSERT INTO statuses (status_userid, status , status_expiration) VALUES ('%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, $select_code_row['code_type']),
		mysqli_real_escape_string($connect, $status_expiration));
		$add_status_query = mysqli_query($connect, $add_status);
		if ($add_status_query) {
			//Sets, that the code is activated.
			$code_used = sprintf("UPDATE codes SET code_used = 1, code_activatedby = '%d', code_activated = '%s' WHERE code = '%s';",
			mysqli_real_escape_string($connect, $user_id),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
			mysqli_real_escape_string($connect, $code));
			$code_used_query = mysqli_query($connect, $code_used);
			$_SESSION['status'] = '<span class="font-weight-bold text-warning">VIP</span>';
			$_SESSION['notifications_count'] += 1;
			//Update user's sell multiplier
			$sell_multiplier = sprintf("UPDATE users SET sell_multiplier = 0.8 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $user_id));
			$sell_multiplier_query = mysqli_query($connect, $sell_multiplier);
			//Create notification
			$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $user_id),
			mysqli_real_escape_string($connect, "<span class='font-weight-bold'>KÓDY: </span>Váš ".$select_code_row['code_type']." status byl úspěšně aktivován! Datum expirace: <span class='font-weight-bold'>".$status_expiration_formatted."</span>"),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
			$message_send_query = mysqli_query($connect, $message_send);
			$_SESSION['success_message'] = "Váš ".$select_code_row['code_type']." status byl úspěšně aktivován!<br>Datum expirace: <span class='font-weight-bold'>".$status_expiration_formatted."</span>";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při aktivaci tohoto kódu!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		}
	} elseif (($select_code_row['code_type'] == 'balance') && ($select_code_row['code_value'] != 0)) {
		$update_user = sprintf("UPDATE users SET balance = balance + %d WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $select_code_row['code_value']),
		mysqli_real_escape_string($connect, $user_id));
		$update_user_query = mysqli_query($connect, $update_user);
		if ($update_user_query) {
			//Sets, that the code is activated.
			$code_used = sprintf("UPDATE codes SET code_used = 1, code_activatedby = '%d', code_activated = '%s' WHERE code = '%s';",
			mysqli_real_escape_string($connect, $user_id),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
			mysqli_real_escape_string($connect, $code));
			$code_used_query = mysqli_query($connect, $code_used);
			$_SESSION['balance'] += $select_code_row['code_value'];
			$_SESSION['notifications_count'] += 1;
			//Create notification
			$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $user_id),
			mysqli_real_escape_string($connect, "<span class='font-weight-bold'>KÓDY: </span><span class='font-weight-bold'>".$select_code_row['code_value']."</span> Kč bylo úspěšně připsáno na vaše konto!"),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
			$message_send_query = mysqli_query($connect, $message_send);
			$_SESSION['success_message'] = "<span class='font-weight-bold'>".$select_code_row['code_value']."</span> Kč bylo úspěšně připsáno na vaše konto!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při aktivaci tohoto kódu!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		}
	}
} else {
	handle_error("Nebylo získáno dostatečně parametrů!", "user_codes_action");
}