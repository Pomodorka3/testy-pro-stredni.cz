<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

//(DONE) Сделать так, чтобы тот кто добавил пост, получал проценты от покупок его товара.

session_start();
authorize_user();
update_activity();

if (!isset($_GET['item_id'])) {
	handle_error("Nebylo přijato dostatečně parametrů!", "Nebyl zadán parametr Item_id! (item_buy)");
}
$item_id = $_GET['item_id'];
$balance = $_SESSION['balance'];
$balance_real = $_SESSION['balance_real'];
$balance_fake = $_SESSION['balance_fake'];
$user_id = $_SESSION['user_id'];

$item_select = sprintf("SELECT item_price, item_createdby_userid FROM shop WHERE item_id = '%d' AND visible = 1 AND checked = 1;",
mysqli_real_escape_string($connect, $item_id));
$item_select_query = mysqli_query($connect, $item_select);
if (mysqli_num_rows($item_select_query) == 0) {
	handle_error("Tento test neexistuje, nebo ještě nebyl schválen!", "This item has row 'visible' or row 'checked' set to 0 (item_buy)");
	exit();
}
$item_select_row = mysqli_fetch_array($item_select_query);

//Get seller's sell_multiplier
$seller_multiplier = sprintf("SELECT sell_multiplier FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $item_select_row['item_createdby_userid']));
$seller_multiplier_query = mysqli_query($connect, $seller_multiplier);
$seller_multiplier_row = mysqli_fetch_row($seller_multiplier_query);

$school_test = sprintf("SELECT u.user_id FROM users u, shop s WHERE u.school_id = s.school_id AND s.item_id = '%d' AND u.user_id = '%d';",
mysqli_real_escape_string($connect, $item_id),
mysqli_real_escape_string($connect, $user_id));
$school_test_query = mysqli_query($connect, $school_test);

if (mysqli_num_rows($school_test_query) == 0) {
	handle_error("Nemáte přístup k tomuto testu z jiné školy!", "item_buy");
	exit();
}

$check_exists = sprintf("SELECT item_id FROM buy_events WHERE buyer_id = '%d' AND item_id = '%d';",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $item_id));
$check_exists_query = mysqli_query($connect, $check_exists);
if (mysqli_num_rows($check_exists_query) !== 0) {
	$_SESSION['error_message'] = "Tento test už máte koupený.";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header('Location: '. SITE_ROOT. 'shop');
	exit();
}

if ($balance_real + $balance_fake - $item_select_row['item_price'] < 0) {
	$_SESSION['error_message'] = "Nemáte dostatek peněz.";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header('Location: '. SITE_ROOT. 'shop');
	exit();
}

//Selfbuy check
$selfbuy = sprintf("SELECT item_id FROM shop WHERE item_createdby_userid = '%d' AND item_id = '%d';",
mysqli_real_escape_string($connect, $user_id),
mysqli_real_escape_string($connect, $item_id));
$selfbuy_query = mysqli_query($connect, $selfbuy);
if (mysqli_num_rows($selfbuy_query) != 0) {
	$_SESSION['error_message'] = "Nemůžete koupit svůj test!";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header('Location: '. SITE_ROOT. 'shop');
	exit();
}

$is_referred = sprintf("SELECT referrals_userby FROM referrals WHERE referrals_userid = '%d';",
mysqli_real_escape_string($connect, $user_id));
$is_referred_query = mysqli_query($connect, $is_referred);
$is_referred_row = mysqli_fetch_row($is_referred_query);
//Get referred by user's referral multiplier
$getMultiplier = sprintf("SELECT ref_multiplier FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $is_referred_row[0]));
$getMultiplier_query = mysqli_query($connect, $getMultiplier);
$referral_Multiplier = mysqli_fetch_row($getMultiplier_query)[0];
//-----------------------------------------
if ($balance_fake > 0) {
	$item_buy = sprintf("INSERT INTO buy_events (item_id, buyer_id, seller_id, price, seller_multiplier, buy_time, seller_ref_multiplier, fake_money_used) VALUES ('%d', '%d', '%d', '%d', '%f', '%s', '%f', '%d');",
		mysqli_real_escape_string($connect, $item_id),
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $item_select_row['item_price']),
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $balance_fake));
	$item_buy_query = mysqli_query($connect, $item_buy);

	if ($item_buy_query) {
		
		if ($balance_fake - $item_select_row['item_price'] < 0) {
			$real_money = $item_select_row['item_price'] - $balance_fake;

			//Deducting fake money from product buyer
			$minus_money_fake = sprintf("UPDATE users SET balance_fake = 0 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $user_id));
			$minus_money_fake_query = mysqli_query($connect, $minus_money_fake);
			$_SESSION['balance_fake'] = 0;

			//Deducting real money from product buyer
			$minus_money = sprintf("UPDATE users SET balance = '%d' WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $balance_real - $real_money),
			mysqli_real_escape_string($connect, $user_id));
			$minus_money_query = mysqli_query($connect, $minus_money);
		} else {
			//Deducting fake money from product buyer
			$minus_money_fake = sprintf("UPDATE users SET balance_fake = '%d' WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $balance_fake - $item_select_row['item_price']),
			mysqli_real_escape_string($connect, $user_id));
			$minus_money_fake_query = mysqli_query($connect, $minus_money_fake);
			$_SESSION['balance_fake'] = $balance_fake - $item_select_row['item_price'];
		}

		//Update shop_earn statistics
		$statistics_upd = sprintf("INSERT INTO shop_earn (shopearn_itemid, shopearn_value, shopearn_date) VALUES ('%d', '%d', '%s');",
		$item_id,
		$item_select_row['item_price'],
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$statistics_upd_query = mysqli_query($connect, $statistics_upd);

		$_SESSION['balance'] -= $item_select_row['item_price'];

		$_SESSION['success_message'] = "Děkujeme Vám za nákup!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";

	} else {
		handle_error("Došlo k neočekávané chybě!", "item_buy");
		exit();
	}
} else {
	$item_buy = sprintf("INSERT INTO buy_events (item_id, buyer_id, seller_id, price, seller_multiplier, buy_time, seller_ref_multiplier, fake_money_used) VALUES ('%d', '%d', '%d', '%d', '%f', '%s', '%f', '%d');",
		mysqli_real_escape_string($connect, $item_id),
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, $item_select_row['item_createdby_userid']),
		mysqli_real_escape_string($connect, $item_select_row['item_price']),
		mysqli_real_escape_string($connect, $seller_multiplier_row[0]),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $referral_Multiplier),
		mysqli_real_escape_string($connect, 0));
	$item_buy_query = mysqli_query($connect, $item_buy);

	if ($item_buy_query) {
		//Deducting money from product buyer
		$minus_money = sprintf("UPDATE users SET balance = '%d' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $balance_real - $item_select_row['item_price']),
		mysqli_real_escape_string($connect, $user_id));
		$minus_money_query = mysqli_query($connect, $minus_money);

		//Seller recieves his sell_multiplier of total price
		$percent_money = sprintf("UPDATE users SET balance = balance + %s * %d WHERE user_id = '%d';",
		$seller_multiplier_row[0],
		mysqli_real_escape_string($connect, $item_select_row['item_price']),
		mysqli_real_escape_string($connect, $item_select_row['item_createdby_userid']));
		$percent_money_query = mysqli_query($connect, $percent_money);

		//Referred by user recieves 3% of total price + update shop_earn statistics
		if (!is_null($is_referred_row[0])) {
			
			$referred = sprintf("UPDATE referrals SET referrals_money = referrals_money + %s * %d WHERE referrals_userid = '%d';",
			$referral_Multiplier,
			mysqli_real_escape_string($connect, $item_select_row['item_price']),
			mysqli_real_escape_string($connect, $user_id));
			$referred_query = mysqli_query($connect, $referred);

			$statistics_upd = sprintf("INSERT INTO shop_earn (shopearn_itemid, shopearn_value, shopearn_referralmoney, shopearn_date) VALUES ('%d', '%f', '%f', '%s');",
			$item_id,
			$item_select_row['item_price'] * (1 - $seller_multiplier_row[0] - $referral_Multiplier - VALIDATORS_MULTIPLIER),
			$item_select_row['item_price'] * $referral_Multiplier,
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
			$statistics_upd_query = mysqli_query($connect, $statistics_upd);
		} else {
			$statistics_upd = sprintf("INSERT INTO shop_earn (shopearn_itemid, shopearn_value, shopearn_date) VALUES ('%d', '%d', '%s');",
			$item_id,
			$item_select_row['item_price'] * (1 - $seller_multiplier_row[0] - VALIDATORS_MULTIPLIER),
			mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
			$statistics_upd_query = mysqli_query($connect, $statistics_upd);
		}

		//Validated by user recieves VALIDATORS_MULTIPLIER (1.5%) from price
		
		

		//Updating items statistics
		/*$statistics_upd = sprintf("UPDATE statistics SET shop_earn = shop_earn + %s * %d;",
		SYSTEM_MULTIPLIER,
		mysqli_real_escape_string($connect, $item_select_row['item_price']));
		$statistics_upd_query = mysqli_query($connect, $statistics_upd);*/
		$bought_times = sprintf("UPDATE shop SET bought_times = bought_times + 1 WHERE item_id = '%d';",
		mysqli_real_escape_string($connect, $item_id));
		$bought_times_query = mysqli_query($connect, $bought_times);

		$bought_items_counter = sprintf("UPDATE users SET bought_items = bought_items + 1 WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $user_id));
		$bought_items_counter_query = mysqli_query($connect, $bought_items_counter);

		$_SESSION['balance'] -= $item_select_row['item_price'];

		$_SESSION['success_message'] = "Děkujeme Vám za nákup!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";

	} else {
		handle_error("Došlo k neočekávané chybě!", "item_buy");
		exit();
	}
}

header('Location: '. SITE_ROOT. 'bought_items');