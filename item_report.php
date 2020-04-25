<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

authorize_user();
update_activity();

if (isset($_GET['report_id'])) {
	$report_id = $_GET['report_id'];
	$user_id = $_SESSION['user_id'];

	$if_bought = sprintf("SELECT item_id FROM buy_events WHERE item_id = '%d' AND buyer_id = '%d';",
	mysqli_real_escape_string($connect, $report_id),
	mysqli_real_escape_string($connect, $user_id));
	$if_bought_query = mysqli_query($connect, $if_bought);
	if (mysqli_num_rows($if_bought_query) == 0) {
		$_SESSION['error_message'] = "Nemůžete reklamovat test, který jste nekoupil!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."bought_items");
		exit();	
	}

	$already_reported = sprintf("SELECT report_id, report_from FROM report WHERE report_item = '%d';",
	mysqli_real_escape_string($connect, $report_id));
	$already_reported_query = mysqli_query($connect, $already_reported);
	$already_reported_row = mysqli_fetch_array($already_reported_query);

	if (mysqli_num_rows($already_reported_query) == 0) {
		$select = sprintf("SELECT item_createdby_userid, item_description FROM shop WHERE item_id = '%d';",
		mysqli_real_escape_string($connect, $report_id));
		$select_query = mysqli_query($connect, $select);
		$select_row = mysqli_fetch_array($select_query);

		if ($select_row['item_createdby_userid'] == $user_id) {
			$_SESSION['error_message'] = "Nemůžete reklamovat svůj test!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."bought_items");
			exit();	
		}

		$report = sprintf("INSERT INTO report (report_from, report_on, report_item, report_date, report_description) VALUES ('%d', '%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, $select_row['item_createdby_userid']),
		mysqli_real_escape_string($connect, $report_id),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $select_row['item_description']));
		$report_query = mysqli_query($connect, $report);

		if ($report_query) {
			$_SESSION['success_message'] = "Tento test byl <span class='font-weight-bold'>úspěšně</span> odeslán na reklamaci!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."bought_items");
			exit();
		} else {
			handle_error("Došlo k chybě při reklamaci vašeho testu.", "item_report");
		}
	} else {
		if ($user_id == $already_reported_row['report_from']) {
			$_SESSION['error_message'] = "Tento test už byl Vámi odeslán na reklamaci.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."bought_items");
			exit();
		}
		$_SESSION['error_message'] = "Někdo už odeslal tento test na reklamaci! Pokud reklamace bude schválena, všichni dostanou své peníze zpátky.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."bought_items");
		exit();
	}
} else {
	handle_error("Nebylo zadáno id.","item_report");
}