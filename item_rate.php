<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

if (isset($_GET['like_id'])){
	$like_id = $_GET['like_id'];
	$user_id = $_SESSION['user_id'];

	$owner_check = sprintf("SELECT rated FROM buy_events WHERE buyer_id = '%d' AND item_id = '%d';",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $like_id));
	$owner_check_query = mysqli_query($connect, $owner_check);
	$owner_check_query_row = mysqli_fetch_array($owner_check_query);
	if (mysqli_num_rows($owner_check_query) == 0) {
		handle_error("Nemůžete ohodnotit tento test, jelikož jste ho zatím nekoupil!");
	}
	if ($owner_check_query_row['rated'] == 1) {
		$_SESSION['error_message'] = "Tento test jste již ohodnotil!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header('Location: '. SITE_ROOT. 'bought_items');
		exit();
	} else {
		$like = sprintf("UPDATE shop SET likes = likes + 1 WHERE item_id = '%d';", mysqli_real_escape_string($connect, $like_id));
		$like_query = mysqli_query($connect, $like);
		$rated = sprintf("UPDATE buy_events SET rated = 1 WHERE item_id = '%d';", mysqli_real_escape_string($connect, $like_id));
		$rated_query = mysqli_query($connect, $rated);
		if ($like_query && $rated_query) {
			$_SESSION['success_message'] = "Děkujeme Vám za hodnocení testu!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header('Location: '. SITE_ROOT. 'bought_items');
			exit();
		} else {
			handle_error("Něco se stalo špatně při hodnocení testu!");
		}
	}
} elseif (isset($_GET['dislike_id'])) {
	$dislike_id = $_GET['dislike_id'];
	$user_id = $_SESSION['user_id'];

	$owner_check = sprintf("SELECT rated FROM buy_events WHERE buyer_id = '%d' AND item_id = '%d';",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $dislike_id));
	$owner_check_query = mysqli_query($connect, $owner_check);
	$owner_check_query_row = mysqli_fetch_array($owner_check_query);
	if (mysqli_num_rows($owner_check_query) == 0) {
		handle_error("You haven't bought this item!");
	}
	if ($owner_check_query_row['rated'] == 1) {
		$_SESSION['error_message'] = "Tento test jste již ohodnotil!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header('Location: '. SITE_ROOT. 'bought_items');
		exit();
	} else {
		$like = sprintf("UPDATE shop SET dislikes = dislikes + 1 WHERE item_id = '%d';", mysqli_real_escape_string($connect, $dislike_id));
		$like_query = mysqli_query($connect, $like);
		$rated = sprintf("UPDATE buy_events SET rated = 1 WHERE item_id = '%d';", mysqli_real_escape_string($connect, $dislike_id));
		$rated_query = mysqli_query($connect, $rated);
		if ($like_query && $rated_query) {
			$_SESSION['success_message'] = "Děkujeme Vám za hodnocení testu!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header('Location: '. SITE_ROOT. 'bought_items');
			exit();
		} else {
			handle_error("Něco se stalo špatně při hodnocení testu!");
		}
	}
} else {
	handle_error("Musíte vybrat test, který chcete ohodnotit!");
}