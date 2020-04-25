<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];

if ($_GET['action'] == 'lotteryJoin') {
	$if_joined = sprintf("SELECT lottery_userid FROM lottery WHERE lottery_userid = '%d' AND MONTH(lottery_joined) = MONTH(CURRENT_DATE());",
	mysqli_real_escape_string($connect, $user_id));
	$if_joined_query = mysqli_query($connect, $if_joined);

	if (mysqli_num_rows($if_joined_query) != 0) {
		$_SESSION['error_message'] = "Už se účastníte loterie!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	$count_referrals = sprintf("SELECT COUNT(r.referrals_userid) FROM referrals r, users u WHERE r.referrals_userby = '%d' AND u.user_id = r.referrals_userid AND MONTH(u.register_date) = MONTH(CURRENT_DATE());",
	mysqli_real_escape_string($connect, $user_id));
	$count_referrals_query = mysqli_query($connect, $count_referrals);
	$count_referrals_row = mysqli_fetch_row($count_referrals_query);

	if ($count_referrals_row[0] < LOTTERY_INVITED) {
		$_SESSION['error_message'] = "Za tento měsíc musíte pozvat nejméně 10 nových uživatelů!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	$count_boughtItems = sprintf("SELECT COUNT(*) FROM buy_events WHERE buyer_id = '%d' AND MONTH(buy_time) = MONTH(CURRENT_DATE());",
	mysqli_real_escape_string($connect, $user_id));
	$count_boughtItems_query = mysqli_query($connect, $count_boughtItems);
	$count_boughtItems_row = mysqli_fetch_row($count_boughtItems_query);

	if ($count_boughtItems_row[0] < LOTTERY_BOUGHT) {
		$_SESSION['error_message'] = "Za tento měsíc musíte koupit alespoň 10 testů!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	$count_addedItems = sprintf("SELECT COUNT(*) FROM buy_events WHERE buyer_id = '%d' AND MONTH(buy_time) = MONTH(CURRENT_DATE());",
	mysqli_real_escape_string($connect, $user_id));
	$count_addedItems_query = mysqli_query($connect, $count_addedItems);
	$count_addedItems_row = mysqli_fetch_row($count_addedItems_query);

	if ($count_addedItems_row[0] < LOTTERY_ADDED) {
		$_SESSION['error_message'] = "Za tento měsíc musíte přidat minimálně 10 testů!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	$lottery_add = sprintf("INSERT INTO lottery (lottery_userid, lottery_joined) VALUES ('%d', '%s');",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$lottery_add_query = mysqli_query($connect, $lottery_add);
	$_SESSION['success_message'] = "Váš účet byl úspěšně přihlášen do loterie!";
	$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."profile");
	exit();
} elseif ($_GET['action'] == 'lotteryDraw') {
	authorize_user(array("Administrator", "Main administrator"));

	$winners = "SELECT COUNT(*) FROM lottery WHERE MONTH(CURRENT_DATE()) = MONTH (lottery_joined) AND lottery_place = 1 OR lottery_place = 2 OR lottery_place = 3;";
	$winners_query = mysqli_query($connect, $winners);

	if (mysqli_fetch_row($winners_query)[0] != 0) {
		$_SESSION['error_message'] = "V tomto měsíci už bylo provedeno slosování!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	$joinedUsers = 'SELECT COUNT(*) FROM lottery WHERE MONTH(lottery_joined) = MONTH(CURRENT_DATE());';
    $joinedUsers_query = mysqli_query($connect, $joinedUsers);
	$joinedUsers = mysqli_fetch_row($joinedUsers_query)[0];
	
	if ($joinedUsers < LOTTERY_MINIMUM_USERS) {
		$_SESSION['error_message'] = "Pro losování musí být do loterie zaregistrováno nejméně ".LOTTERY_MINIMUM_USERS." účastníků!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	$monthEnd = strtotime('- 3 days', strtotime('last day of this month'));
	if (time() < $monthEnd) {
		$_SESSION['error_message'] = "Losování můžete provést nejdříve 3 dny před koncem tohoto měsíce.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."profile");
		exit();
	}

	//Random select winners
	$firstWinner = rand(1, $joinedUsers);
	$secondWinner = rand(1, $joinedUsers);
	while ($secondWinner == $firstWinner) {
		$secondWinner = rand(1, $joinedUsers);
	}
	$thirdWinner = rand(1, $joinedUsers);
	while (($thirdWinner == $secondWinner) || ($thirdWinner == $firstWinner)) {
		$thirdWinner = rand(1, $joinedUsers);
	}

	$lotteryList = 'SELECT lottery_userid, lottery_joined FROM lottery WHERE MONTH(lottery_joined) = MONTH(CURRENT_DATE())';
	$lotteryList_query = mysqli_query($connect, $lotteryList);
	$i = 0;
	while ($lotteryList_row = mysqli_fetch_array($lotteryList_query)) {
		$i++;
		$row[$i] = $lotteryList_row;	
	}

	//Add user's win to lottery table ('lottery_place' column)
	$firstUser_place = sprintf("UPDATE lottery SET lottery_place = 1 WHERE lottery_userid = '%d';",
	mysqli_real_escape_string($connect, $row[$firstWinner]['lottery_userid']));
	$firstUser_place_query = mysqli_query($connect, $firstUser_place);
	if ($firstUser_place_query) {
		//Increase first winner's balance
		$firstUser_balance = sprintf("UPDATE users SET balance =  balance + '%d' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, FIRST_PLACE_PRIZE),
		mysqli_real_escape_string($connect, $row[$firstWinner]['lottery_userid']));
		$firstUser_balance_query = mysqli_query($connect, $firstUser_balance);
		//Insert event into log (transactions table)
		$transactions = sprintf("INSERT INTO transactions(t_from, t_sum, t_description, t_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $row[$firstWinner]['lottery_userid']),
		mysqli_real_escape_string($connect, FIRST_PLACE_PRIZE),
		mysqli_real_escape_string($connect, "Lottery reward"),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$transactions_query = mysqli_query($connect, $transactions);
		//Create first winner's notification
		$firstUser_notification = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $row[$firstWinner]['lottery_userid']),
		mysqli_real_escape_string($connect, "<span class='text-secondary'>Gratulujeme k výhře v naší loterii!</span> Umístil/-a jste se na 1. místě - Vaše výhra je ".FIRST_PLACE_PRIZE." Kč"),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$firstUser_notification_query = mysqli_query($connect, $firstUser_notification);
	}

	//Add user's win to lottery table ('lottery_place' column)
	$secondUser_place = sprintf("UPDATE lottery SET lottery_place = 2 WHERE lottery_userid = '%d';",
	mysqli_real_escape_string($connect, $row[$secondWinner]['lottery_userid']));
	$secondUser_place_query = mysqli_query($connect, $secondUser_place);
	if ($secondUser_place_query) {
		//Increase second winner's balance
		$secondUser_balance = sprintf("UPDATE users SET balance =  balance + '%d' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, SECOND_PLACE_PRIZE),
		mysqli_real_escape_string($connect, $row[$secondWinner]['lottery_userid']));
		$secondUser_balance_query = mysqli_query($connect, $secondUser_balance);
		//Insert event into log (transactions table)
		$transactions = sprintf("INSERT INTO transactions(t_from, t_sum, t_description, t_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $row[$secondWinner]['lottery_userid']),
		mysqli_real_escape_string($connect, SECOND_PLACE_PRIZE),
		mysqli_real_escape_string($connect, "Lottery reward"),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$transactions_query = mysqli_query($connect, $transactions);
		//Create second winner's notification
		$secondUser_notification = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $row[$secondWinner]['lottery_userid']),
		mysqli_real_escape_string($connect, "<span class='text-secondary'>Gratulujeme k výhře v naší loterii!</span> Umístil/-a jste se na 2. místě - Vaše výhra je ".SECOND_PLACE_PRIZE." Kč"),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$secondUser_notification_query = mysqli_query($connect, $secondUser_notification);
	}

	//Add user's win to lottery table ('lottery_place' column)
	$thirdUser_place = sprintf("UPDATE lottery SET lottery_place = 3 WHERE lottery_userid = '%d';",
	mysqli_real_escape_string($connect, $row[$thirdWinner]['lottery_userid']));
	$thirdUser_place_query = mysqli_query($connect, $thirdUser_place);
	if ($thirdUser_place_query) {
		//Increase third winner's balance
		$thirdUser_balance = sprintf("UPDATE users SET balance =  balance + '%d' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, THIRD_PLACE_PRIZE),
		mysqli_real_escape_string($connect, $row[$thirdWinner]['lottery_userid']));
		$thirdUser_balance_query = mysqli_query($connect, $thirdUser_balance);
		//Insert event into log (transactions table)
		$transactions = sprintf("INSERT INTO transactions(t_from, t_sum, t_description, t_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, $row[$thirdWinner]['lottery_userid']),
		mysqli_real_escape_string($connect, THIRD_PLACE_PRIZE),
		mysqli_real_escape_string($connect, "Lottery reward"),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$transactions_query = mysqli_query($connect, $transactions);
	//Create third winner's notification
		$thirdUser_notification = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $row[$thirdWinner]['lottery_userid']),
		mysqli_real_escape_string($connect, "<span class='text-secondary'>Gratulujeme k výhře v naší loterii!</span> Umístil/-a jste se na 3. místě - Vaše výhra je ".THIRD_PLACE_PRIZE." Kč"),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$thirdUser_notification_query = mysqli_query($connect, $thirdUser_notification);
	}

	$_SESSION['success_message'] = "Losování bylo úspěšně provedeno!";
	$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."profile");
	exit();
} else {
	handle_error("Nebylo získáno dostatečně parametrů!", "lottery_action");
}