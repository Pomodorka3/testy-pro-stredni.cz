<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_POST['city_add']) && isset($_POST['district_add']) && isset($_POST['school_add'])) {
	$city_add = htmlspecialchars(trim($_POST['city_add']));
	$district_add = htmlspecialchars(trim($_POST['district_add']));
	$school_add = htmlspecialchars(trim($_POST['school_add']));
	$district_add = $_POST['district_add'];

	if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id) || user_in_group("Support", $user_id)) {
		
	} else {
		$first_time_check = sprintf("SELECT school_added FROM users WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $user_id));
		$first_time_check_query = mysqli_query($connect, $first_time_check);
		$first_time_check_row = mysqli_fetch_array($first_time_check_query);
		if ($first_time_check_row['school_added'] == 1) {
			$_SESSION['error_message'] = "Můžete přidat pouze 1 školu.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		}
	}

	$school_exists = sprintf("SELECT school_name FROM school WHERE school_name = '%s';",
	mysqli_real_escape_string($connect, $school_add));
	$school_exists_query = mysqli_query($connect, $school_exists);
	if (mysqli_num_rows($school_exists_query) == 0) {

		$check_city = sprintf("SELECT city_id FROM city WHERE city_name = '%s';",
		mysqli_real_escape_string($connect, $city_add));
		$check_city_query = mysqli_query($connect, $check_city);
		if (mysqli_num_rows($check_city_query) == 0) {
			$create_city = sprintf("INSERT INTO city (city_name) VALUES ('%s');",
			mysqli_real_escape_string($connect, $city_add));
			$create_city_query = mysqli_query($connect, $create_city);
			$city_id = mysqli_insert_id($connect);
		} else {
			//get id of existing city
			$city_id = mysqli_fetch_row($check_city_query)[0];
		}

		$check_district = sprintf("SELECT district_id FROM district WHERE district_name = '%s';",
		mysqli_real_escape_string($connect, $district_add));
		$check_district_query = mysqli_query($connect, $check_district);
		if (mysqli_num_rows($check_district_query) == 0) {
			$create_district = sprintf("INSERT INTO district (district_name, city_id) VALUES ('%s', '%d');",
			mysqli_real_escape_string($connect, $district_add),
			mysqli_real_escape_string($connect, $city_id));
			$create_district_query = mysqli_query($connect, $create_district);
			$district_id = mysqli_insert_id($connect);
		} else {
			//get id of existing district
			$district_id = mysqli_fetch_row($check_district_query)[0];
		}

		$create_school = sprintf("INSERT INTO school (school_name, district_id, added_by, checked_by, school_created) VALUES ('%s', '%d', '%d', '%d', '%s');",
		mysqli_real_escape_string($connect, $school_add),
		mysqli_real_escape_string($connect, $district_id),
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$create_school_query = mysqli_query($connect, $create_school);
		$school_id = mysqli_insert_id($connect);

		if ($create_school_query) {
			$school_added = sprintf("UPDATE users SET school_added = 1, school_id = '%d' WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $school_id),
			mysqli_real_escape_string($connect, $user_id));
			$school_added_query = mysqli_query($connect, $school_added);
			$_SESSION['school_id'] = $school_id;
			$_SESSION['success_message'] = "Škola byla úspěšně přidána a automaticky nastavena na vašem profilu!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		} else {
			handle_error("Došlo k chybě při přidávání nové školy!");
		}
	} else {
		$_SESSION['error_message'] = "Tato škola už je v naší databázi.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_change");
		exit();
	}
} else {
	handle_error("Zadal jste špatný název školy!");
}