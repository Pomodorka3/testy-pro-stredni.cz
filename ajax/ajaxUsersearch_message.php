<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user(array("Main administrator"));

//$output = '';
$search = "SELECT username FROM users";
if (!empty($_POST['username'])) {
	$search .= " WHERE username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%' LIMIT 5"; 
	$search_query = mysqli_query($connect, $search);

	if (mysqli_num_rows($search_query) == 0) {
		echo "Nebyl nalezen žádný uživatel";
	} else {
		//echo $output = '<datalist>';
		while ($row = mysqli_fetch_row($search_query)) {
			echo "<option value='".$row[0]."'></option>";
		}
	}
}