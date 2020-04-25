<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];

if ($_GET['action'] == 'disable' && isset($_GET['page'])) {
	$page = $_GET['page'];

	$page_exists = sprintf("SELECT %s FROM tutorial WHERE user_id = %d;",
	mysqli_real_escape_string($connect, $page),
	mysqli_real_escape_string($connect, $user_id));
	$page_exists_query = mysqli_query($connect, $page_exists);
	if (mysqli_num_rows($page_exists_query) == 0) {
		handle_error("Tutoriál pro tuto stránku neexistuje!", "tutorial_action");
	} elseif (mysqli_fetch_row($page_exists_query)[0] == 0) {
		$_SESSION['error_message'] = "Tutoriál pro tuto stránku je už vypnutý!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: ".SITE_ROOT.$page);
		exit();
	}

	$disable = sprintf("UPDATE tutorial SET %s = 0 WHERE user_id = %d;",
	mysqli_real_escape_string($connect, $page),
	mysqli_real_escape_string($connect, $user_id));
	$disable_query = mysqli_query($connect, $disable);

	if ($disable_query) {
		$_SESSION['success_message'] = "Tutoriál pro tuto stránku byl vypnut!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ".SITE_ROOT.$page);
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při vypínaní tutoriálu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: ".SITE_ROOT.$page);
		exit();
	}
} elseif ($_GET['action'] == 'enable' && $_GET['page'] == 'all') {
	$enable = sprintf("UPDATE tutorial SET profile = 1, shop = 1, item_add = 1, school_change = 1, deposit = 1, withdraw = 1, referrals = 1, item_check = 1, selling_items = 1, bought_items = 1 WHERE user_id = %d;",
	mysqli_real_escape_string($connect, $user_id));
	$enable_query = mysqli_query($connect, $enable);

	if ($enable_query) {
		$_SESSION['success_message'] = "Veškerý tutoriál byl znovu zapnut!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ".SITE_ROOT."profile_settings");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při zapínaní tutoriálu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: ".SITE_ROOT."profile_settings");
		exit();
	}
} else {
	handle_error("Nebylo získáno dostatečně parametrů!", "tutorial_action");
}