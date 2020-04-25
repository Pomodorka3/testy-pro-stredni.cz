<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

authorize_user(array("Administrator", "Main administrator"));
update_activity();

//(DONE) Добавить сообщения для всех пользователей (включая продавца) после подтверждения жалобы.

$user_id = $_SESSION['user_id'];

if (isset($_GET['confirm_id'])) {
	$confirm_id = $_GET['confirm_id'];
	$buyer_id = $_GET['buyer_id'];

	$select = sprintf("SELECT seller_id, buyer_id, price, seller_multiplier, seller_ref_multiplier FROM buy_events WHERE item_id = '%d';",
	mysqli_real_escape_string($connect, $confirm_id));
	$select_query = mysqli_query($connect, $select);
	$select_query_second = mysqli_query($connect, $select);
	$total_minus_money = 0;
	if ($select_query) {
		$select_row_second = mysqli_fetch_array($select_query_second);
		while ($select_row = mysqli_fetch_array($select_query)) {
			//Restore full price to buyer
			$restore_money = sprintf("UPDATE users SET balance = balance + %d, bought_items = bought_items - 1 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $select_row['price']),
			mysqli_real_escape_string($connect, $select_row['buyer_id']));
			$restore_money_query = mysqli_query($connect, $restore_money);

			//Get referral money back
			$restore_referral = sprintf("UPDATE referrals SET referrals_money = referrals_money - %s * %d WHERE referrals_userid = '%d';",
			mysqli_real_escape_string($connect, $select_row['seller_ref_multiplier']),
			mysqli_real_escape_string($connect, $select_row['price']),
			mysqli_real_escape_string($connect, $select_row['buyer_id']));
			$restore_referral_query = mysqli_query($connect, $restore_referral);

			//Remove money from seller
			$remove_money = sprintf("UPDATE users SET balance = balance - %d WHERE user_id = '%d';",
			$select_row['price'] * $select_row['seller_multiplier'],
			mysqli_real_escape_string($connect, $select_row['seller_id']));
			$remove_money_query = mysqli_query($connect, $remove_money);
			$total_minus_money += ($select_row['price'] * $select_row['seller_multiplier']);

			//Update shop_earn statistics
			$updade_statistics = sprintf("DELETE FROM shop_earn WHERE shopearn_itemid = '%d';",
			mysqli_real_escape_string($connect, $confirm_id));
			$update_statistics_query = mysqli_query($connect, $updade_statistics);

			//Create buyer notification
			$message_send_buyer = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $select_row['buyer_id']),
			mysqli_real_escape_string($connect, "Test, který jste si koupil/a, byl odstraněn z našeho obchodu, z důvodu porušení pravidel. Plná cena tohoto předmětu Vám byla vrácena na účet!"),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
			$message_send_buyer_query = mysqli_query($connect, $message_send_buyer);
		}
		//Create seller notification
		$message_send_seller = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
			mysqli_real_escape_string($connect, 0),
			mysqli_real_escape_string($connect, $select_row_second['seller_id']),
			mysqli_real_escape_string($connect, "Test, který jste vystavil/a na prodej byl odstraněn z důvodu porušení pravidel. Peníze, které jste získal/a z prodeje tohoto testu Vám budou odečteny z účtu! (Celkem odečteno ".$total_minus_money." Kč)"),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_seller_query = mysqli_query($connect, $message_send_seller);

		$remove_bought_item = sprintf("DELETE FROM buy_events WHERE item_id = '%d';", mysqli_real_escape_string($connect, $confirm_id));
		$remove_bought_item_query = mysqli_query($connect, $remove_bought_item);

		$remove_item = sprintf("DELETE FROM shop WHERE item_id = '%d';",
		mysqli_real_escape_string($connect, $confirm_id));
		$remove_item_query = mysqli_query($connect, $remove_item);

		$remove_report = sprintf("DELETE FROM report WHERE report_item = '%d';",
		mysqli_real_escape_string($connect, $confirm_id));
		$remove_report_query = mysqli_query($connect, $remove_report);

		$add_confirmed_reports = sprintf("UPDATE users SET confirmed_reports = confirmed_reports + 1 WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $user_id));
		$add_confirmed_reports_query = mysqli_query($connect, $add_confirmed_reports);

		$_SESSION['success_message'] = "Reklamace byla úspěšně <span class='font-weight-bold'>schválena</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."report_view");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při schvalování reklamace!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."report_view");
		exit();
	}
} elseif (isset($_GET['decline_id'])) {
	$decline_id = $_GET['decline_id'];

	$select_report = sprintf("SELECT r.report_from, s.item_name FROM report r, shop s WHERE r.report_item = '%d' AND r.report_item = s.item_id;",
	mysqli_real_escape_string($connect, $decline_id));
	$select_report_query = mysqli_query($connect, $select_report);
	$select_report_row = mysqli_fetch_array($select_report_query);

	$remove_report = sprintf("DELETE FROM report WHERE report_item = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$remove_report_query = mysqli_query($connect, $remove_report);

	if ($remove_report_query) {
		$message_send = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_report_row['report_from']),
		mysqli_real_escape_string($connect, "Vaše reklamace testu ".$select_report_row['item_name']." byla odmítnuta uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'>".$_SESSION['username']."</a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);

		$add_declined_reports = sprintf("UPDATE users SET declined_reports = declined_reports + 1 WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $user_id));
		$add_declined_reports_query = mysqli_query($connect, $add_declined_reports);

		$_SESSION['success_message'] = "Reklamace byla úspěšně <span class='font-weight-bold'>odmítnuta</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."report_view");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odmítnutí reklamace!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."report_view");
		exit();
	}
} else {
	handle_error("Nebylo zadáno id!", "report_action");
}