<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Main administrator"));
update_activity();

if (isset($_GET['accept_id'])) {
	$accept_id = $_GET['accept_id'];
	$select = sprintf("SELECT * FROM withdraw WHERE withdraw_id = '%d';",
	mysqli_real_escape_string($connect, $accept_id));
	$select_query = mysqli_query($connect, $select);
	$select_row = mysqli_fetch_array($select_query);

	if ($select_query) {
		//Update withdraw's status
		$withdraw_status = sprintf("UPDATE withdraw SET withdraw_status = 1 WHERE withdraw_id = '%d';",
		mysqli_real_escape_string($connect, $select_row['withdraw_id']));
		$withdraw_status_query = mysqli_query($connect, $withdraw_status);
		//Update user's last withdraw date
		$withdraw_lastDate  = sprintf("UPDATE users SET last_withdraw = '%s' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $select_row['withdraw_from']));
		$withdraw_lastDate_query = mysqli_query($connect, $withdraw_lastDate);
		$message_send = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $select_row['withdraw_from']),
			mysqli_real_escape_string($connect, "Vaše žádost o výběr ".$select_row['withdraw_sum']." Kč byla schválena uživatelem <a class='text-primary font-weight-bold' href='".SITE_ROOT."profile_show?profile_id=".$_SESSION['user_id']."'><u>".$_SESSION['username']."</u></a>. Peníze budete mít na účtě do 3 pracovních dní."),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);

		if ($withdraw_status_query && $message_send_query) {
			$_SESSION['success_message'] = "Žádost o výběr byla <span class='font-weight-bold'>úspěšně</span> schválena!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."withdraw_requests");
			exit();
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při schvalování žádosti!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."withdraw_requests");
			exit();
		}
	} else {
		handle_error("Došlo k chybě při výběru informace.", "withdraw_requests_action");
	}
} elseif (isset($_GET['decline_id'])) {
	$decline_id = $_GET['decline_id'];
	$select = sprintf("SELECT withdraw_id, withdraw_from, withdraw_sum FROM withdraw WHERE withdraw_id = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$select_query = mysqli_query($connect, $select);
	$select_row = mysqli_fetch_array($select_query);

	if ($select_query) {
		$return_money = sprintf("UPDATE users SET balance = balance + '%d' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $select_row['withdraw_sum']),
		mysqli_real_escape_string($connect, $select_row['withdraw_from']));
		$return_money_query = mysqli_query($connect, $return_money);
		$withdraw_status = sprintf("UPDATE withdraw SET withdraw_status = 2 WHERE withdraw_id = '%d';",
		mysqli_real_escape_string($connect, $select_row['withdraw_id']));
		$withdraw_status_query = mysqli_query($connect, $withdraw_status);
		$message_send = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $select_row['withdraw_from']),
			mysqli_real_escape_string($connect, "Vaše žádost o výběr ".$select_row['withdraw_sum']." Kč byla odmítnuta uživatelem <a class='text-primary font-weight-bold' href='".SITE_ROOT."profile_show?profile_id=".$_SESSION['user_id']."'><u>".$_SESSION['username']."</u></a>. V případě dotazu můžete vytvořit <a class='font-weight-bold text-primary' href='tickets'>nový tiket</a>."),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);

		if ($withdraw_status_query && $message_send_query) {
			$_SESSION['success_message'] = "Žádost o výběr byla <span class='font-weight-bold'>úspěšně</span> odmítnuta!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."withdraw_requests");
			exit();
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při odmítnutí žádosti!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."withdraw_requests");
			exit();
		}
	}
} else {
	handle_error("Nebylo zadáno ID!", "withdraw_requests_action");
}