<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
check_bank_number();
update_activity();

$user_id = $_SESSION['user_id'];

$referrals = sprintf("SELECT ref_money_given FROM users WHERE ref_by = '%d' AND bought_items > 0;",
mysqli_real_escape_string($connect, $user_id));
$referrals_query = mysqli_query($connect, $referrals);
$available_withdraw = 0;
while ($row = mysqli_fetch_array($referrals_query)) {
	$available_withdraw += $row['ref_money_given'];
}

if ($available_withdraw <= 0) {
	$_SESSION['error_message'] = "Zatím nemáte žádné peníze dostupné pro výběr z pozvaných uživatelů.";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."referrals.php");
	exit();
}

$if_bought = sprintf("SELECT bought_items FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $user_id));
$if_bought_query = mysqli_query($connect, $if_bought);
$row = mysqli_fetch_row($if_bought_query);

if ($row[0] == 0) {
	$_SESSION['error_message'] = "Musíte koupit alespoň 1 test v našem obchodě abyste mohl požádat o výběr!";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."referrals.php");
	exit();
}

$create_withdraw = sprintf("INSERT INTO withdraw (withdraw_from, withdraw_sum, withdraw_description, withdraw_date) VALUES ('%d', '%d', '%s', '%s');",
mysqli_real_escape_string($connect, $user_id),
mysqli_real_escape_string($connect, $available_withdraw),
mysqli_real_escape_string($connect, 'Výběr z pozvaných lidí.'),
mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
$create_withdraw_query = mysqli_query($connect, $create_withdraw);

if ($create_withdraw_query) {
	$remove_money_given = sprintf("UPDATE users SET ref_money_given = 0 WHERE ref_by = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$remove_money_given_query = mysqli_query($connect, $remove_money_given);
	$_SESSION['success_message'] = "Vaše žádost byla odeslána na zpracování našim Administrátorům. Žádost bude zpracována nejpozději do 24 hodin.";
	$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."referrals.php");
	exit();
} else {
	handle_error("Něco se stalo špatně při vytváření žádosti o výběr.", "withdraw_referral");
}