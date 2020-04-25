<?php

require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user();

if (!empty($_POST['city_name'])) {
	$district = sprintf("SELECT d.district_id, d.district_name FROM district d, city c WHERE d.city_id = c.city_id AND c.city_name = '%s' ORDER BY d.district_name ASC;",
	mysqli_real_escape_string($connect, $_POST['city_name']));
	$district_query = mysqli_query($connect, $district);

	$district_rows = mysqli_num_rows($district_query);

	if ($district_rows > 0) {
		echo "<option value=''>Vyberte čtvrť</option>";
		while ($district_row = mysqli_fetch_array($district_query)) {
			echo $district_echo = sprintf("<option value='%d'>%s</option>",
			$district_row['district_id'],
			$district_row['district_name']);
		}
	} else {
		echo "<option value=''>Nejsou dostupné žádné čtvrtě v tomto městě</option>";
	}
} elseif (isset($_POST['school_name']) && isset($_POST['district_id'])) {
	$school = "SELECT school_id, school_name FROM school WHERE district_id = ".$_POST['district_id']." AND visible = 1";
	$school .= " AND school_name COLLATE utf8_general_ci LIKE '%".$_POST['school_name']."%'";
	$school .= " ORDER BY school_name ASC LIMIT 6";
	$school_query = mysqli_query($connect, $school);

	$school_rows = mysqli_num_rows($school_query);

	if ($school_rows > 0) {
		echo "<option value=''>Vyberte školu</option>";
		while ($school_row = mysqli_fetch_array($school_query)) {
			echo $school_echo = sprintf("<option value='%s'></option>",
			$school_row['school_name']);
		}
	} else {
		echo "<option value=''>Nejsou dostupné žádné školy v této čtvrti</option>";
	}
} elseif (!empty($_POST['district_id'])) {
	$school = sprintf("SELECT school_id, school_name FROM school WHERE district_id = '%d' AND visible = 1 ORDER BY school_name ASC LIMIT 6;",
	mysqli_real_escape_string($connect, $_POST['district_id']));
	$school_query = mysqli_query($connect, $school);

	$school_rows = mysqli_num_rows($school_query);

	if ($school_rows > 0) {
		echo "<option value=''>Vyberte školu</option>";
		while ($school_row = mysqli_fetch_array($school_query)) {
			echo $school_echo = sprintf("<option value='%s'></option>",
			$school_row['school_name']);
		}
	} else {
		echo "<option value=''>Nejsou dostupné žádné školy v této čtvrti</option>";
	}
} 