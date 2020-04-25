<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

echo $amount = $_POST['deposit-sum'] * 100;
echo $user_id = $_SESSION['user_id'];
echo $email = $_SESSION['email'];
echo $order_id = mysqli_fetch_row(mysqli_query($connect, "SELECT id FROM deposits ORDER BY id DESC LIMIT 1;"))[0] + 1;

if ((isset($amount) && $amount!=0) && isset($user_id) && isset($email)) {
	$insert_request = sprintf("INSERT INTO deposits(id, user_id, amount, time_requested) VALUES ('%d', '%d', '%d', '%s')",
	mysqli_real_escape_string($connect, $order_id),
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $amount),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$insert_request_query = mysqli_query($connect, $insert_request);

	if ($insert_request_query) {
		header("Location: https://www.pays.cz/paymentorder?Merchant=74867&Shop=52236&Amount=".$amount."&Currency=CZK&MerchantOrderNumber=".$order_id."&Email=".$email);
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při vytváření platebního příkazu. Prosím nahlaste tuto skutečnost administrátorům prostřednictvím <a href='tickets'><u>tiketů</u></a>.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile");
		exit();
	}
}

?>