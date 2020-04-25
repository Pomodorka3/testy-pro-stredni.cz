<?php

require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user();

$user_id = $_SESSION['user_id'];

$teacher = "SELECT DISTINCT s.teacher FROM shop s, users u WHERE u.user_id = $user_id AND s.visible = 1 AND s.checked = 1 AND u.school_id = s.school_id";

if (!empty($_POST['teacher_input'])) {
	$teacher .= " AND s.teacher COLLATE utf8_general_ci LIKE '%".$_POST['teacher_input']."%'";
}

//Order by and limit number of results
$teacher .= ' ORDER BY s.teacher ASC LIMIT 6';
$teacher_query = mysqli_query($connect, $teacher);

while ($teacher_row = mysqli_fetch_row($teacher_query)) {
	if ($teacher_row[0] != '') {
		echo "<option value='$teacher_row[0]'>$teacher_row[0]</option>";
	}
}