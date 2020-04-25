<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
update_activity();

$OrderId = $_GET['PaymentOrderID'];
$Merchant = $_GET['MerchantOrderNumber'];
$PaymentStatus = $_GET['PaymentOrderStatusID']; //3 - success; 2 - not passed
$Currency = $_GET['CurrencyID'];
$Amount = $_GET['Amount'];
$CurrencyBaseUnits = $_GET['CurrencyBaseUnits'];
$StatusDescription = $_GET['PaymentOrderStatusDescription'];
$RecievedHash = $_GET['hash'];
$DataToHash = $OrderId.$Merchant.$PaymentStatus.$Currency.$Amount.$CurrencyBaseUnits;

$Hash = hash_hmac("md5", $DataToHash, PAYMENT_KEY);

$Amount /= 100; //Converts Haléře into CZK

if ($RecievedHash == $Hash) {
    $getDepositStatus_query = mysqli_query($connect, sprintf("SELECT status FROM deposits WHERE id='%d';", mysqli_real_escape_string($connect, $Merchant)));
    $getDepositStatus = mysqli_fetch_row($getDepositStatus_query)[0];

    if (mysqli_num_rows($getDepositStatus_query) > 0) {
        if ($getDepositStatus == 0) {
            if ($PaymentStatus == 3) {
                //Update deposit status in database
                $statusUpdate = sprintf("UPDATE deposits SET status = 1, time_completed = '%s', description = '%s' WHERE id = '%d';",
                mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
                mysqli_real_escape_string($connect, $StatusDescription),
                mysqli_real_escape_string($connect, $Merchant));
                $statusUpdate_query = mysqli_query($connect, $statusUpdate);
                //Get payer's user_id
                $user_id = mysqli_fetch_row(mysqli_query($connect, sprintf("SELECT user_id FROM deposits WHERE id = '%d';", mysqli_real_escape_string($connect, $Merchant))))[0];
                //Add money to user's balance
                $user_balance = sprintf("UPDATE users SET balance = balance + '%d' WHERE user_id = '%d';",
                mysqli_real_escape_string($connect, $Amount),
                mysqli_real_escape_string($connect, $user_id));
                $user_balance_query = mysqli_query($connect, $user_balance);
                //Create notification
                $message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
                mysqli_real_escape_string($connect, 0),
                mysqli_real_escape_string($connect, $user_id),
                mysqli_real_escape_string($connect, "<span class='font-weight-bold'>VKLAD: </span>Na Váše konto bylo připsáno ".$Amount.".00 Kč. Děkujeme Vám za využívání našich služeb!"),
                mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
                $message_send_query = mysqli_query($connect, $message_send);
                //Change user session's hash to log him out
                $session_hash = md5(rand(0,1000));
                $hash_change = sprintf("UPDATE users SET session_hash = '%s' WHERE user_id = '%d';",
                mysqli_real_escape_string($connect, $session_hash),
                mysqli_real_escape_string($connect, $user_id));
                $hash_change_query = mysqli_query($connect, $hash_change);
            } else {
                //Update deposit status in database
                $statusUpdate = sprintf("UPDATE deposits SET status = 2, time_completed = '%d', description = '%s' WHERE id = '%d';",
                mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
                mysqli_real_escape_string($connect, $StatusDescription),
                mysqli_real_escape_string($connect, $Merchant));
                $statusUpdate_query = mysqli_query($connect, $statusUpdate);
                //Get payer's user_id
                $user_id = mysqli_fetch_row(mysqli_query($connect, sprintf("SELECT user_id FROM deposits WHERE id = '%d';", mysqli_real_escape_string($connect, $Merchant))))[0];
                //Create notification
                $message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
                mysqli_real_escape_string($connect, 0),
                mysqli_real_escape_string($connect, $user_id),
                mysqli_real_escape_string($connect, "<span class='font-weight-bold'>VKLAD: </span>Došlo k chybě při platbě. Buď transakce byla Vámi stornována nebo ji neschválila banka či jiný partner. V případě dotazů vytvořte <a href='tickets' class='text-primary'><u>tiket</u></a>"),
                mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
                $message_send_query = mysqli_query($connect, $message_send);
            }
            http_response_code(202);
        } else {
            header('CustomError: This-payment-was-completed');
            http_response_code(202);
        }
    } else {
        echo "asdas";
        header('CustomError: This-payment-doesn\'t-exist-in-database');
        http_response_code(202);
    }
} else {
    header('CustomError: Hash-isn\'t-valid');
    http_response_code(202);
}

?>