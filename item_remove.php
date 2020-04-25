<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

if (isset($_GET['remove_id'])) {
	$remove_id = $_GET['remove_id'];
	$user_id = $_SESSION['user_id'];

	$item_owner = sprintf("SELECT item_id FROM shop WHERE item_createdby_userid = '%d' AND item_id = '%d' AND visible = 1;",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $remove_id));
	$item_owner_query = mysqli_query($connect, $item_owner);

	if (mysqli_num_rows($item_owner_query) == 0) {
		handle_error("Nejste majitel tohoto testu!");
	}
	$remove_shop = sprintf("UPDATE shop SET visible = 0 WHERE item_createdby_userid = '%d' AND item_id = '%d';",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $remove_id));
	$remove_shop_query = mysqli_query($connect, $remove_shop);

	if ($remove_shop_query) {
		$_SESSION['success_message'] = "Vybraný test byl <span class='font-weight-bold'>úspěšně</span> odstraněn z našeho obchodu!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."selling_items");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k neočekávané chybě při odstraňování testu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."selling_items");
		exit();
	}
} else {
	handle_error("Nebylo zadáno žádné id!", "remove_id");
}