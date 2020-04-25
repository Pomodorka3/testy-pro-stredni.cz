<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
check_bank_number();
update_activity();

$user_id = $_SESSION['user_id'];

$referrals = sprintf("SELECT r.referrals_money FROM referrals r, users u WHERE r.referrals_userby = '%d' AND r.referrals_userid = u.user_id AND u.bought_items > 0;",
mysqli_real_escape_string($connect, $user_id));
$referrals_query = mysqli_query($connect, $referrals);
$available_withdraw = 0;
while ($row = mysqli_fetch_array($referrals_query)) {
	$available_withdraw += $row['referrals_money'];
}

if ($available_withdraw <= 0) {
	$_SESSION['error_message'] = "Zatím nemáte žádné peníze dostupné pro výběr z pozvaných uživatelů.";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."referrals");
	exit();
}

$if_bought = sprintf("SELECT bought_items FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $user_id));
$if_bought_query = mysqli_query($connect, $if_bought);
$row = mysqli_fetch_row($if_bought_query);

if ($row[0] == 0) {
	$_SESSION['error_message'] = "Musíte koupit alespoň 1 test v našem obchodě abyste mohl požádat o výběr!";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."referrals");
	exit();
}

$transaction = sprintf("INSERT INTO transactions (t_from, t_sum, t_description, t_date) VALUES ('%d', '%d', '%s', '%s');",
mysqli_real_escape_string($connect, $user_id),
mysqli_real_escape_string($connect, $available_withdraw),
mysqli_real_escape_string($connect, 'Referrals reward'),
mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
$transaction_query = mysqli_query($connect, $transaction);

if ($transaction_query) {
	$remove_money_given = sprintf("UPDATE referrals SET referrals_money = 0 WHERE referrals_userby = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$remove_money_given_query = mysqli_query($connect, $remove_money_given);
	//Add withdrawed money to user's balance
	$moneyAdd = sprintf("UPDATE users SET balance = balance + $available_withdraw WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$_SESSION['balance'] += $available_withdraw;
	$moneyAdd_query = mysqli_query($connect, $moneyAdd);
	$_SESSION['success_message'] = "Peníze z pozvaných uživatelů byly <span class='font-weight-bold'>úspěšně</span> převedeny na Vaše konto.";
	$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."referrals");
	exit();
} else {
	handle_error("Došlo k chybě při vytváření žádosti o výběr.", "withdraw_referral");
}