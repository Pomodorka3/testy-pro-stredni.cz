<?php

require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user();

//--------------------- SELECT SCHOOL ----------------------

if ($_POST['load'] == 'city') {
	$cities = "SELECT city_id, city_name FROM city";

	if (isset($_POST['citySearch'])) {
		$_POST['citySearch'] = trim($_POST['citySearch']);
		$cities .= " WHERE city_name COLLATE utf8_general_ci LIKE '%".$_POST['citySearch']."%'";
	}

	echo $cities .= " ORDER BY city_name ASC LIMIT 6;";
	$cities_query = mysqli_query($connect, $cities);

	if (mysqli_num_rows($cities_query) > 0) {
		echo "<option value=''>Vyberte město</option>";
		while ($cities_row = mysqli_fetch_array($cities_query)) {
			echo $district_echo = sprintf("<option value='%s'></option>",
			$cities_row['city_name']);
		}
	} else {
		echo "<option value=''>Nejsou dostupná žádná města</option>";
	}
}

if (!empty($_POST['cityName'])) {
	$district = sprintf("SELECT d.district_id, d.district_name FROM district d, city c WHERE d.city_id = c.city_id AND c.city_name = '%s'",
	mysqli_real_escape_string($connect, $_POST['cityName']));

	if (isset($_POST['districtSearch'])) {
		$_POST['districtSearch'] = trim($_POST['districtSearch']);
		$district .= " AND d.district_name COLLATE utf8_general_ci LIKE '%".$_POST['districtSearch']."%'";
	}

	$district .= " ORDER BY d.district_name ASC LIMIT 6";
	$district_query = mysqli_query($connect, $district);

	if (mysqli_num_rows($district_query) > 0) {
		echo "<option value=''>Vyberte čtvrť</option>";
		while ($district_row = mysqli_fetch_array($district_query)) {
			echo $district_echo = sprintf("<option value='%s'></option>",
			$district_row['district_name']);
		}
	} else {
		echo "<option value=''>Nejsou dostupné žádné čtvrtě v tomto městě</option>";
	}
}

if (!empty($_POST['districtName'])) {
	$school = sprintf("SELECT s.school_id, s.school_name FROM school s, district d WHERE d.district_id = s.district_id AND d.district_name = '%s'",
	mysqli_real_escape_string($connect, $_POST['districtName']));

	if (isset($_POST['schoolSearch'])) {
		$_POST['schoolSearch'] = trim($_POST['schoolSearch']);
		$school .= " AND s.school_name COLLATE utf8_general_ci LIKE '%".$_POST['schoolSearch']."%'";
	}
	
	$school .= " ORDER BY s.school_name ASC LIMIT 6";
	echo $school;
	$school_query = mysqli_query($connect, $school);

	if (mysqli_num_rows($school_query) > 0) {
		echo "<option value=''>Vyberte školu</option>";
		while ($school_row = mysqli_fetch_array($school_query)) {
			echo $school_echo = sprintf("<option value='%s'></option>",
			$school_row['school_name']);
		}
	} else {
		echo "<option value=''>Nejsou dostupné školy v této čtvrti</option>";
	}
}

//--------------------- ADD NEW SCHOOL ----------------------

