<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

	function generateRndString($code_length = 10){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $code_length; $i++) {
		    $randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

if (($_GET['action'] == 'generateCode') && ($_POST['code_type'] == 'balance') && isset($_POST['code_value']) && isset($_POST['code_expiration'])) {

	$code_type = $_POST['code_type'];
	$code_value = $_POST['code_value'];
	if ($_POST['code_expiration'] == '5d') {
		$code_expiration = date('Y-m-d H:i:s', strtotime('+ 5 days'));
	} elseif ($_POST['code_expiration'] == '30d') {
		$code_expiration = date('Y-m-d H:i:s', strtotime('+ 30 days'));
	}

	$generated_code = generateRndString();

	//Generate new random code, if there is same code
	$if_exists = sprintf("SELECT code_id FROM codes WHERE code = '%s';",
	mysqli_real_escape_string($connect, $generated_code));
	$if_exists_query = mysqli_query($connect, $if_exists);
	while (mysqli_num_rows($if_exists_query) != 0) {
		$generated_code = generateRndString();
		$if_exists = sprintf("SELECT code_id FROM codes WHERE code = '%s';",
		mysqli_real_escape_string($connect, $generated_code));
		$if_exists_query = mysqli_query($connect, $if_exists);
	}
		
	$insert_code = sprintf("INSERT INTO codes (code_type, code, code_value, code_generatedby, code_created, code_expiration) VALUES ('%s', '%s', '%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, $code_type),
	mysqli_real_escape_string($connect, $generated_code),
	mysqli_real_escape_string($connect, $code_value),
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $code_expiration));
	$insert_code_query = mysqli_query($connect, $insert_code);

	if ($insert_code_query) {
		$_SESSION['success_message'] = "Nový kód byl úspěšně vygenerován!<br>Kód: <span class='font-weight-bold'>".$generated_code."</span><br>Typ kódu: <span class='font-weight-bold'>".$code_type."</span><br>Datum expirace: <span class='font-weight-bold'>".$code_expiration."</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."codes");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při generování nového kódu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."codes");
		exit();
	}
} elseif (($_GET['action'] == 'generateCode') && ($_POST['code_type'] == 'vip') && isset($_POST['code_expiration']) && isset($_POST['code_value'])) {
	$code_type = $_POST['code_type'];
	$code_value = $_POST['vip_value'];
	if ($_POST['code_expiration'] == '5d') {
		$code_expiration = date('Y-m-d H:i:s', strtotime('+ 5 days'));
	} elseif ($_POST['code_expiration'] == '30d') {
		$code_expiration = date('Y-m-d H:i:s', strtotime('+ 30 days'));
	}

	$generated_code = generateRndString();

	$if_exists = sprintf("SELECT code_id FROM codes WHERE code = '%s';",
	mysqli_real_escape_string($connect, $generated_code));
	$if_exists_query = mysqli_query($connect, $if_exists);
	while (mysqli_num_rows($if_exists_query) != 0) {
		$generated_code = generateRndString();
		$if_exists = sprintf("SELECT code_id FROM codes WHERE code = '%s';",
		mysqli_real_escape_string($connect, $generated_code));
		$if_exists_query = mysqli_query($connect, $if_exists);
	}
		
	$insert_code = sprintf("INSERT INTO codes (code_type, code, code_value, code_generatedby, code_created, code_expiration) VALUES ('%s', '%s', '%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, $code_type),
	mysqli_real_escape_string($connect, $generated_code),
	mysqli_real_escape_string($connect, $code_value),
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $code_expiration));
	$insert_code_query = mysqli_query($connect, $insert_code);
	if ($insert_code_query) {
		$_SESSION['success_message'] = "Nový kód byl úspěšně vygenerován!<br>Kód: <span class='font-weight-bold'>".$generated_code."</span><br>Typ kódu: <span class='font-weight-bold'>".$code_type."</span><br>Datum expirace: <span class='font-weight-bold'>".$code_expiration."</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."codes");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při generování nového kódu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."codes");
		exit();
	}
} elseif (($_GET['action'] == 'removeCode') && isset($_GET['code_id'])) {
	$code_id = $_GET['code_id'];

	$if_exists = sprintf("SELECT code_id FROM codes WHERE code_id = '%d';",
	mysqli_real_escape_string($connect, $code_id));
	$if_exists_query = mysqli_query($connect, $if_exists);
	if (mysqli_num_rows($if_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento kód neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."codes");
		exit();
	}

	$remove_code = sprintf("DELETE FROM codes WHERE code_id = '%d';",
	mysqli_real_escape_string($connect, $code_id));
	$remove_code_query = mysqli_query($connect, $remove_code);
	if ($remove_code_query) {
		$_SESSION['success_message'] = "Kód byl <span class='font-weight-bold'>úspěšně</span> odstraněn!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."codes");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odstraňování kódu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."codes");
		exit();
	}
} else {
	handle_error("Nebylo přijato dostatečně parametrů!", "admin_codes_action");
}