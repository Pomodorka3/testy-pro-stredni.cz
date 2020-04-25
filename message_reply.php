<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

if (isset($_GET['message_to']) && isset($_POST['message_content'])) {
	$message_to = $_GET['message_to'];
	$message_content = $_POST['message_content'];

	//Self-reply check
	if ($message_to == $_SESSION['user_id']) {
		$_SESSION['error_message'] = "Nemůžete odpovědět sám sobě!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	$only_reply = sprintf("SELECT message_id FROM messages WHERE message_from = '%d' AND message_to = '%d' AND message_removed = 0;",
	mysqli_real_escape_string($connect, $message_to),
	mysqli_real_escape_string($connect, $_SESSION['user_id']));
	$only_reply_query = mysqli_query($connect, $only_reply);

	if (mysqli_num_rows($only_reply_query) == 0) {
		handle_error("Můžete odpovědět pouze na přijaté zprávy!");
	}

	$message_reply = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, $_SESSION['user_id']),
	mysqli_real_escape_string($connect, $message_to),
	mysqli_real_escape_string($connect, $message_content),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$message_reply_query = mysqli_query($connect, $message_reply);

	if ($message_reply_query) {
		$_SESSION['success_message'] = "Odpověď byla <span class='font-weight-bold'>úspěšně</span> odeslána!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při odesílání odpovědi!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}
} else {
	handle_error("Nebylo získáno id, nebo obsah zprávy!", "message_reply");
}
