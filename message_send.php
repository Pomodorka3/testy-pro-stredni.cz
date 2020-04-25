<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Support", "Administrator", "Main administrator"));
update_activity();

if (isset($_POST['username']) && isset($_POST['message_send_content'])) {
	$message_to = $_POST['username'];
	$message_content = htmlspecialchars(trim($_POST['message_send_content']));
	$user_id = $_SESSION['user_id'];

	//Self-send check
	if ($message_to == $_SESSION['username']) {
		$_SESSION['error_message'] = "Nelze odeslat zprávu sám sobě!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	//Проверить на наличие пользователя!
	$user_search = sprintf("SELECT user_id FROM users WHERE username = '%s';",
	mysqli_real_escape_string($connect, $message_to));
	$user_search_query = mysqli_query($connect, $user_search);
	$user_search_row = mysqli_fetch_array($user_search_query);
	$message_to_userid = $user_search_row['user_id'];

	if (mysqli_num_rows($user_search_query) == 0) {
		$_SESSION['error_message'] = "Špatně zadané jméno uživatele!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT . "profile");
		exit();
	}

	//Anti-spam with same message system
	$message_exists = sprintf("SELECT message_content FROM messages WHERE message_to = '%d';",
	mysqli_real_escape_string($connect, $message_to));
	$message_exists_query = mysqli_query($connect, $message_exists);
	$message_exists_row = mysqli_fetch_array($message_exists_query);

	if ($message_content == $message_exists_row['message_content']) {
		$_SESSION['error_message'] = "Tento uživatel už má zprávu se stejným obsahem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT . "profile");
		exit();
	}

	$message_reply = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $message_to_userid),
	mysqli_real_escape_string($connect, $message_content),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$message_reply_query = mysqli_query($connect, $message_reply);

	if ($message_reply_query) {
		$_SESSION['success_message'] = "Zpráva byla úspěšně <span class='font-weight-bold'>odeslána</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odesílání zprávy!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}
} else {
	handle_error("Nebyl uveden příjemce zprávy nebo její obsah!", "message_send");
}
