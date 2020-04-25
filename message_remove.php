<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

if (isset($_GET['message_id'])) {
	$message_id = $_GET['message_id'];
	$user_id = $_SESSION['user_id'];

	if ($_GET['message_id'] == 'all') {
		$check_messages = sprintf("SELECT message_id FROM messages WHERE message_to = '%d';",
		mysqli_real_escape_string($connect, $user_id));
		$check_messages_query = mysqli_query($connect, $check_messages);

		if (mysqli_num_rows($check_messages_query) == 0) {
			$_SESSION['error_message'] = "Nemáte žádné upozornění na odstranění.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		}

		$remove_message = sprintf("UPDATE messages SET message_removed = 1 WHERE message_to = '%d';",
		mysqli_real_escape_string($connect, $user_id));
		$remove_message_query = mysqli_query($connect, $remove_message);

		if ($remove_message_query) {
			$_SESSION['notifications_count'] = 0;
			$_SESSION['success_message'] = "Veškeré upozornění byla <span class='font-weight-bold'>úspěšně</span> odstraněna!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		} else {
			$_SESSION['error_message'] = "Došlo k chybě při odstraňování všech upozornění!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		}
	}

	$message_owner = sprintf("SELECT message_id FROM messages WHERE message_id = '%d' AND message_to = '%d';",
	mysqli_real_escape_string($connect, $message_id),
	mysqli_real_escape_string($connect, $user_id));
	$message_owner_query = mysqli_query($connect, $message_owner);

	if (mysqli_num_rows($message_owner_query) == 0) {
		handle_error("Můžete odstraňovat pouze svoje upozornění!");
	}

	$remove_message = sprintf("UPDATE messages SET message_removed = 1 WHERE message_id = '%d' AND message_to = '%d';",
	mysqli_real_escape_string($connect, $message_id),
	mysqli_real_escape_string($connect, $user_id));
	$remove_message_query = mysqli_query($connect, $remove_message);

	if ($remove_message_query) {
		$_SESSION['notifications_count'] -= 1;
		$_SESSION['success_message'] = "Vybrané upozornění bylo <span class='font-weight-bold'>úspěšně</span> odstraněno!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při odstraňování upozornění!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}
} else {
	handle_error("Nebylo získáno žádné id!", "message_remove");
}