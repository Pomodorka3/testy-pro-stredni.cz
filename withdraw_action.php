<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
check_bank_number();
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_POST['amount'])) {
	$balance = $_SESSION['balance'];
	$withdraw_amount = $_POST['amount'];
	//Check user's last withdraw date
	$last_withdraw = sprintf("SELECT last_withdraw FROM users WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$last_withdraw_query = mysqli_query($connect, $last_withdraw);
	$last_withdraw_row = mysqli_fetch_row($last_withdraw_query);

	if ($last_withdraw_row[0] != '0000-00-00 00:00:00') {
		$daysDifference = date('j', time() - strtotime($last_withdraw_row[0]));

		if ($daysDifference < 15) {
			$_SESSION['error_message'] = "Vybírat je možné každých 14 dní! Váš poslední schválený výběr byl ".$last_withdraw_row[0];
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."withdraw");
			exit();
		}
	}

	if ($withdraw_amount < MIN_WITHDRAW_SUM) {
		$_SESSION['error_message'] = "Nejmenší částka pro výběr je 50 Kč!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."withdraw");
		exit();
	}

	if ($balance < $withdraw_amount) {
		$_SESSION['error_message'] = "Na Vašem kontě není dostatek peněz!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."withdraw");
		exit();
	}

	$withdraw_request = sprintf("INSERT INTO withdraw (withdraw_from, withdraw_sum, withdraw_description, withdraw_date) VALUES ('%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $withdraw_amount - ($withdraw_amount * WITHDRAW_MULTIPLIER)),
	mysqli_real_escape_string($connect, "Standardní výběr z konta"),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$withdraw_request_query = mysqli_query($connect, $withdraw_request);

	if ($withdraw_request) {
		$balance_update = sprintf("UPDATE users SET balance = balance - %d WHERE user_id = %d;",mysqli_real_escape_string($connect, $withdraw_amount),
			mysqli_real_escape_string($connect, $user_id));
		$balance_update_query = mysqli_query($connect, $balance_update);
		$statistics_update = sprintf("UPDATE statistics SET withdraw_earn = withdraw_earn + %d;",mysqli_real_escape_string($connect, WITHDRAW_MULTIPLIER * $withdraw_amount));
		$statistics_update_query = mysqli_query($connect, $statistics_update);
		$_SESSION['balance'] -= $withdraw_amount;
		$_SESSION['success_message'] = "Vaše žádost o výběr peněz byla <span class='font-weight-bold'>úspěšně</span> odeslána!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."withdraw");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odesílání žádosti o výběr peněz!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."withdraw");
		exit();
	}
} elseif (isset($_GET['cancel_id'])) {
	$balance = $_SESSION['balance'];
	$cancel_id = $_GET['cancel_id'];

	$select_request = sprintf("SELECT withdraw_sum FROM withdraw WHERE withdraw_id = '%d';",
	mysqli_real_escape_string($connect, $cancel_id));
	$select_request_query = mysqli_query($connect, $select_request);
	$select_request_row = mysqli_fetch_row($select_request_query);

	$delete_request = sprintf("DELETE FROM withdraw WHERE withdraw_id = '%d';",
	mysqli_real_escape_string($connect, $cancel_id));
	$delete_request_query = mysqli_query($connect, $delete_request);

	if ($delete_request_query) {
		$money_restore = sprintf("UPDATE users SET balance = balance + %d WHERE user_id = %d;",
		mysqli_real_escape_string($connect, $select_request_row[0]),
		mysqli_real_escape_string($connect, $user_id));
		$money_restore_query = mysqli_query($connect, $money_restore);
		$_SESSION['balance'] += $select_request_row[0];
		$_SESSION['success_message'] = "Vybraná žádost o výběr peněz byla <span class='font-weight-bold'>úspěšně</span> zrušena!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."withdraw");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při rušení žádosti!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."withdraw");
		exit();
	}
} elseif ($_GET['for'] == 'itemCheck' || user_in_group("Validator", $user_id) || user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
	$availableConfirmedMoney = sprintf("SELECT SUM(be.price) FROM shop s, buy_events be WHERE s.confirmed_by = '%d' AND s.item_id = be.item_id AND be.confirmedby_withdrawed = 0 AND MONTH(be.buy_time) = MONTH(CURRENT_DATE());",
	mysqli_real_escape_string($connect, $user_id));
	$availableConfirmedMoney_query = mysqli_query($connect, $availableConfirmedMoney);

	if (mysqli_num_rows($availableConfirmedMoney_query) != 0) {
		$availableConfirmedMoney = mysqli_fetch_row($availableConfirmedMoney_query)[0] * VALIDATORS_MULTIPLIER;
		if ($availableConfirmedMoney == 0) {
			$_SESSION['error_message'] = "Nemáte dostatek peněz z kontrolování testů pro uskutečnění výběru.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		}
		$transaction = sprintf("INSERT INTO transactions (t_from, t_sum, t_description, t_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, $availableConfirmedMoney),
		mysqli_real_escape_string($connect, 'Checked items reward'),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$transaction_query = mysqli_query($connect, $transaction);
		if ($transaction_query) {
			$updateConfirmedMoney = sprintf("UPDATE buy_events be INNER JOIN shop s ON s.item_id = be.item_id SET be.confirmedby_withdrawed = 1 WHERE s.confirmed_by = '%d' AND be.confirmedby_withdrawed = 0;",
			mysqli_real_escape_string($connect, $user_id));
			$updateConfirmedMoney_query = mysqli_query($connect, $updateConfirmedMoney);
			//Add sum to users_balance
			$usersBalance = sprintf("UPDATE users SET balance = balance + '%d' WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $availableConfirmedMoney),
			mysqli_real_escape_string($connect, $user_id));
			$usersBalance_query = mysqli_query($connect, $usersBalance);
			$_SESSION['balance'] += $availableConfirmedMoney;
			$_SESSION['success_message'] = "Peníze z potvrzených testů byly <span class='font-weight-bold'>úspěšně</span> převedeny na Vaše konto.";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		} else {
			$_SESSION['error_message'] = "Došlo k chybě při převodu peněz na Vaše konto.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."profile");
			exit();
		}
		$_SESSION['error_message'] = "Nemáte žádné peníze z kontrolování testů.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}
} else {
	handle_error("Nebyla zadaná částka!", "withdraw_action");
}