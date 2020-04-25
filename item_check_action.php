<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Validator", "Support", "Administrator", "Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_GET['confirm_id'])) {
	$confirm_id = $_GET['confirm_id'];
	$seller_id = $_GET['seller_id'];

	$select = sprintf("SELECT item_name FROM shop WHERE item_id = '%d';",
	mysqli_real_escape_string($connect, $confirm_id));
	$select_query = mysqli_query($connect, $select);
	$select_row = mysqli_fetch_array($select_query);

	$check_confirm = sprintf("UPDATE shop SET checked = 1, confirmed_date = '%s', confirmed_by = '%d' WHERE item_id = '%d';",
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $confirm_id));
	$check_confirm_query = mysqli_query($connect, $check_confirm);

	$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, 0),
	mysqli_real_escape_string($connect, $seller_id),
	mysqli_real_escape_string($connect, "Vaše žádost o přidání testu <span class='font-weight-bold'>".$select_row['item_name']."</span> byla úspěšně schválena uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>." ),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$add_confirmed_items = sprintf("UPDATE users SET confirmed_items = confirmed_items + 1 WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));

	if ($check_confirm_query) {
		$message_send_query = mysqli_query($connect, $message_send);
		$add_confirmed_items_query = mysqli_query($connect, $add_confirmed_items);
		$_SESSION['success_message'] = "Žádost byla úspěšně <span class='font-weight-bold'>schválena</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		if (user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			header("Location: " . SITE_ROOT ."item_check_all");
		} else {
			header("Location: " . SITE_ROOT ."item_check");
		}
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při schvalování žádosti!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		if (user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			header("Location: " . SITE_ROOT ."item_check_all");
		} else {
			header("Location: " . SITE_ROOT ."item_check");
		}
		exit();
	}
} elseif (isset($_GET['decline_id']) && isset($_POST['decline_reason'])) {
	$decline_id = $_GET['decline_id'];
	$seller_id = $_GET['seller_id'];
	$decline_reason = htmlspecialchars(trim($_POST['decline_reason']));

	$userInfo = sprintf("SELECT email FROM users WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $seller_id));
	$userInfo_query = mysqli_query($connect, $userInfo);
	$userEmail = mysqli_fetch_row($userInfo_query)[0];

	$select = sprintf("SELECT item_name, item_createdby_userid FROM shop WHERE item_id = '%d';",
	mysqli_real_escape_string($connect, $decline_id));
	$select_query = mysqli_query($connect, $select);
	$select_row = mysqli_fetch_array($select_query);

	if ($decline_reason == "first_name"){
		$reason = "Na obrázku je uvedeno něčí jméno, toto je bohužel proti zásadam GDPR. Jméno můžete zakrýt papírkem, nebo smazat pomocí editoru";
	} elseif ($decline_reason == "image") {
		$reason = "Špatná příloha";
	} elseif ($decline_reason == "content") {
		$reason = "Špatný obsah";
	} elseif ($decline_reason == "image+blackpoint") {
		$reason = "Špatná příloha. <span class='font-weight-bold text-danger'>Byl Vám připsán trestný bod!</span>";
		$blackPoint_add = sprintf("INSERT INTO black_points (bp_userid, bp_givenby, bp_description, bp_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $select_row['item_createdby_userid']),
		mysqli_real_escape_string($connect, $user_id),
		'Item check',
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	} elseif ($decline_reason == "content+blackpoint") {
		$reason = "Špatný obsah. <span class='font-weight-bold text-danger'>Byl Vám připsán trestný bod!</span>";
		$blackPoint_add = sprintf("INSERT INTO black_points (bp_userid, bp_givenby, bp_description, bp_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $select_row['item_createdby_userid']),
		mysqli_real_escape_string($connect, $user_id),
		'Item check',
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	} else {
		$reason = $decline_reason;
	}
	mail($userEmail, 'Test byl zamítnut', "Dobrý den,\nVaše žádost o přidání testu ".$select_row['item_name']." byla zamítnuta.\nDůvod: ".$reason.".\n\rOpravte chybu a zkuste nahrát test znovu.\n\r\n\rS pozdravem, tým Testy-pro-střední.cz");
	$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, 0),
	mysqli_real_escape_string($connect, $seller_id),
	mysqli_real_escape_string($connect, "Vaše žádost o přidání testu <span class='font-weight-bold'>".$select_row['item_name']."</span> byla zamítnuta uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>. Důvod: ".$reason."." ),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$remove_item = sprintf("DELETE FROM shop WHERE item_id = '%d';", mysqli_real_escape_string($connect, $decline_id));
	$remove_item_query = mysqli_query($connect, $remove_item);
	$remove_image = sprintf("DELETE FROM images WHERE shop_id = '%d';", mysqli_real_escape_string($connect, $decline_id));
	$remove_image_query = mysqli_query($connect, $remove_image);
	$add_declined_items = sprintf("UPDATE users SET declined_items = declined_items + 1 WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));

	if ($remove_item_query) {

		if (!empty($blackPoint_add)) {
			$blackPoint_add_query = mysqli_query($connect, $blackPoint_add);
		}
		$message_send_query = mysqli_query($connect, $message_send);
		$add_declined_items_query = mysqli_query($connect, $add_declined_items);
		$_SESSION['success_message'] = "Žádost byla úspěšně <span class='font-weight-bold'>odmítnuta</span>!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		if (user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			header("Location: " . SITE_ROOT ."item_check_all");
		} else {
			header("Location: " . SITE_ROOT ."item_check");
		}
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odmítnutí žádosti!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		if (user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			header("Location: " . SITE_ROOT ."item_check_all");
		} else {
			header("Location: " . SITE_ROOT ."item_check");
		}
		exit();
	}
} else {
	handle_error("Nebylo přijato žádné id.", "item_check");
}