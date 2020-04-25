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

	$select_request = sprintf("SELECT sa_userid, sa_city, sa_district, sa_school FROM school_add WHERE sa_id = '%d';",
	mysqli_real_escape_string($connect, $confirm_id));
	$select_request_query = mysqli_query($connect, $select_request);
	$select_request_row = mysqli_fetch_array($select_request_query);
	$sa_city = $select_request_row['sa_city'];
	$sa_district = $select_request_row['sa_district'];
	$sa_school = $select_request_row['sa_school'];

	//Get user's GDPR and email
	$userData = sprintf("SELECT email, gdpr_accepted FROM users WHERE user_id = '%d';", 
	mysqli_real_escape_string($connect, $select_request_row['sa_userid']));
	$userData_query = mysqli_query($connect, $userData);
	$userData_row = mysqli_fetch_array($userData_query);
	$userEmail = $userData_row['email'];
	$userGDPR = $userData_row['gdpr_accepted'];

	$check_city = sprintf("SELECT city_id FROM city WHERE city_name = '%s';",
	mysqli_real_escape_string($connect, $sa_city));
	$check_city_query = mysqli_query($connect, $check_city);
	if (mysqli_num_rows($check_city_query) == 0) {
		echo $create_city = sprintf("INSERT INTO city (city_name) VALUES ('%s');",
		mysqli_real_escape_string($connect, $sa_city));
		$create_city_query = mysqli_query($connect, $create_city);
		$sa_city = mysqli_insert_id($connect);
	} else {
		//get id of existing city
		$sa_city = mysqli_fetch_row($check_city_query)[0];
	}

	$check_district = sprintf("SELECT district_id FROM district WHERE district_name = '%s';",
	mysqli_real_escape_string($connect, $sa_district));
	$check_district_query = mysqli_query($connect, $check_district);
	if (mysqli_num_rows($check_district_query) == 0) {
		echo $create_district = sprintf("INSERT INTO district (district_name, city_id) VALUES ('%s', '%d');",
		mysqli_real_escape_string($connect, $sa_district),
		mysqli_real_escape_string($connect, $sa_city));
		$create_district_query = mysqli_query($connect, $create_district);
		$sa_district = mysqli_insert_id($connect);
	} else {
		//get id of existing district
		$sa_district = mysqli_fetch_row($check_district_query)[0];
	}

	$create_school = sprintf("INSERT INTO school (school_name, district_id, added_by, checked_by, school_created) VALUES ('%s', '%d', '%d', '%d', '%s');",
	mysqli_real_escape_string($connect, $sa_school),
	mysqli_real_escape_string($connect, $sa_district),
	mysqli_real_escape_string($connect, $select_request_row['sa_userid']),
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$create_school_query = mysqli_query($connect, $create_school);

	if ($create_school_query) {
		// $school_id = mysqli_insert_id($create_school_query);

		//Remove request
		$request_remove = sprintf("DELETE FROM school_add WHERE sa_id = '%d';",
		mysqli_real_escape_string($connect, $select_request_row['sa_id']));
		$request_remove_query = mysqli_query($connect, $request_remove);
		//Update column user createdby
		$confirmedby = sprintf("UPDATE school_add SET sa_confirmedby = '%s', sa_confirmed = '%s' WHERE sa_id = '%d';",
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $confirm_id));
		$confirmedby_query = mysqli_query($connect, $confirmedby);
		//Add user to this school
/* 		$insert_school = sprintf("UPDATE users SET school_id = '%d', school_setdate = '%s' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $school_id),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $select_request_row['sa_userid']));
		$insert_school_query = mysqli_query($connect, $insert_school); */
		//Create notification for school creator
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_request_row['sa_userid']),
		mysqli_real_escape_string($connect, "Vaše žádost o přidání školy ".$select_school_row['school_name']." byla <span class='text-success font-weight-bold'>schválena</span> uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>. Nyní si tuto školu můžete vybrat v nastavení." ),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		//If user has accepted GDPR -> send notification to email
		if ($userGDPR == 1) {
			mail($userEmail, 'Schválení přidané školy', "Děkujeme Vám za přidání školy do našeho systému.\nVaše škola byla schválena a přidána do našeho systému.\nOd teď můžete začít přidávat testy a sdílet náš web mezi spolužákami.\n\rTIP: Čím více testů přidáte hned po přidání školy do našeho systému, tím více si můžete na začatku vydělat. Jelikož konkurence bude malá. :)\n\r\n\rS pozdravem, tým Testy-pro-střední.cz");	
		}
		$_SESSION['success_message'] = "Žádost o přidání školy byla <span class='font-weight-bold'>schválena</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_add");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při schvalování žádosti o přidání školy!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_add");
		exit();
	}
} elseif (isset($_GET['decline_id']) && isset($_POST['decline_reason'])) {
	$decline_id = $_GET['decline_id'];
	$decline_reason = htmlspecialchars(trim($_POST['decline_reason']));

	$select_school = sprintf("SELECT sa_school, sa_userid FROM school_add WHERE sa_id = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$select_school_query = mysqli_query($connect, $select_school);
	$select_school_row = mysqli_fetch_array($select_school_query);

	$remove = sprintf("DELETE FROM school_add WHERE sa_id = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$remove_query = mysqli_query($connect, $remove);

	if ($remove_query) {
		$update_user = sprintf("UPDATE users SET school_added = 0 WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $select_school_row['sa_userid']));
		$update_user_query = mysqli_query($connect, $update_user);
		if ($decline_reason == "not_exists") {
			$reason = "Tato škola neexistuje";
		} elseif ($decline_reason == "already") {
			$reason = "Tato škola už je v naší databázi";
		} else {
			$reason = $decline_reason;
		}
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_school_row['sa_userid']),
		mysqli_real_escape_string($connect, "Vaše žádost o přidání školy ".$select_school_row['sa_school']." byla <span class='text-danger font-weight-bold'>odmítnuta</span> uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>. Důvod: ".$reason."." ),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Žádost o přidání školy byla <span class='font-weight-bold'>odmítnuta</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_add");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odmítání žádosti o přidání školy!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_add");
		exit();
	}
} else {
	handle_error("Nebylo uvedeno id žádosti!", "school_add_action");
}