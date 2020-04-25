<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Support", "Administrator", "Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_GET['accept_id'])) {
	$accept_id = $_GET['accept_id'];
	$select_request = sprintf("SELECT sc.user_id, sc.change_school_id_to, s.school_name FROM school_change sc, school s WHERE s.school_id = sc.change_school_id_to AND sc.id = '%d';",
	mysqli_real_escape_string($connect, $accept_id));
	$select_request_query = mysqli_query($connect, $select_request);
	$select_request_row = mysqli_fetch_array($select_request_query);

	if ($select_request_query) {
		$user_update = sprintf("UPDATE users SET school_id = '%d', school_setdate = '%s' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $select_request_row['change_school_id_to']),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $select_request_row['user_id']));
		$user_update_query = mysqli_query($connect, $user_update);
		$change_remove = sprintf("DELETE FROM school_change WHERE id = '%d';",
		mysqli_real_escape_string($connect, $accept_id));
		$change_remove_query = mysqli_query($connect, $change_remove);

		if ($user_update_query && $change_remove_query) {
			$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $select_request_row['user_id']),
			mysqli_real_escape_string($connect, "Vaše žádost o změnu školy na ".$select_request_row['school_name']." byla <span class='text-success font-weight-bold'>schválena</span> uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>." ),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
			$message_send_query = mysqli_query($connect, $message_send);
			$_SESSION['success_message'] = "Změna školy byla ůspěšně <span class='font-weight-bold'>schválena</span>!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."school_check");
			exit();
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při schvalování změny školy!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."school_check");
			exit();
		}
	} else {
		handle_error("Došlo k chybě při výběru informace.");
	}
} elseif (isset($_GET['decline_id']) && isset($_POST['decline_reason'])) {
	$decline_id = $_GET['decline_id'];
	$decline_reason = htmlspecialchars(trim($_POST['decline_reason']));

	$select_request = sprintf("SELECT sc.user_id, s.school_name FROM school_change sc, school s WHERE s.school_id = sc.change_school_id_to AND sc.id = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$select_request_query = mysqli_query($connect, $select_request);
	$select_request_row = mysqli_fetch_array($select_request_query);

	$remove = sprintf("DELETE FROM school_change WHERE id = '%d';",
		mysqli_real_escape_string($connect, $decline_id));
	$change_remove_query = mysqli_query($connect, $remove);

	if ($change_remove_query) {
		if ($decline_reason == "time") {
			$reason = "Školu lze změnit nejdříve po uplynutí 2 týdnů od chvíle původního výběru";
		} else {
			$reason = $decline_reason;
		}
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_request_row['user_id']),
		mysqli_real_escape_string($connect, "Vaše žádost o změnu školy na ".$select_request_row['school_name']." byla <span class='text-danger font-weight-bold'>odmítnuta</span> uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>. Důvod: ".$reason."." ),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Změna školy byla ůspěšně <span class='font-weight-bold'>odmítnuta</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_check");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odmítání změny školy!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_check");
		exit();
	}
} elseif ($_GET['action'] == 'removeSchool' && isset($_GET['school_id'])) {
	authorize_user(array("Main administrator"));
	$remove_id = $_GET['school_id'];

	$if_exists = sprintf("SELECT school_name FROM school WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $remove_id));
	$if_exists_query = mysqli_query($connect, $if_exists);
	$if_exists_row = mysqli_fetch_row($if_exists_query);
	if (mysqli_num_rows($if_exists_query) == 0) {
		$_SESSION['error_message'] = "Tato škola neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."schools");
		exit();
	}

	$remove_school = sprintf("DELETE FROM school WHERE school_id = '%d';",
	mysqli_real_escape_string($connect, $remove_id));
	$remove_school_query = mysqli_query($connect, $remove_school);
	if ($remove_school_query) {
		$users_in_school = sprintf("SELECT user_id FROM users WHERE school_id = '%d';",
		mysqli_real_escape_string($connect, $remove_id));
		$users_in_school_query = mysqli_query($connect, $users_in_school);
		while ($row = mysqli_fetch_row($users_in_school_query)) {
			//Set school_id = 0 to all users
			$set_school = sprintf("UPDATE users SET school_id = 0 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $row[0]));
			$set_school_query = mysqli_query($connect, $set_school);
			//Create notification for all users
			$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $row[0]),
			mysqli_real_escape_string($connect, "Škola ".$if_exists_row[0]." byla <span class='text-danger font-weight-bold'>smazána</span> uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>." ),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
			$message_send_query = mysqli_query($connect, $message_send);
		}
		$_SESSION['success_message'] = "Vybraná škola byla <span class='font-weight-bold'>odstraněna</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."schools");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odstraňování školy!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."schools");
		exit();
	}
} else {
	handle_error("Nebylo uvedeno id žádosti!", "school_check_action");
}