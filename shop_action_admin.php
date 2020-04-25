<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Administrator", "Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

if ($_GET['action'] == 'remove' && isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];

    $select = sprintf("SELECT item_name, item_createdby_userid FROM shop WHERE item_id = '%d';",
    mysqli_real_escape_string($connect, $remove_id));
    $select_query = mysqli_query($connect, $select);
    $select_row = mysqli_fetch_array($select_query);

    $remove = sprintf("UPDATE shop SET visible = 0 WHERE item_id = '%d';",
    mysqli_real_escape_string($connect, $remove_id));
    $remove_query = mysqli_query($connect, $remove);
    
    if ($remove_query) {
        //Create seller notification
		$message_send_seller = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_row['item_createdby_userid']),
		mysqli_real_escape_string($connect, "Váš test <span class='font-weight-bold'>".$select_row['item_name']."</span> byl odstraněn z našeho obchodu uživatelem <a class='font-weight-bold text-primary' href='profile_show?profile_id=".$user_id."'>".$_SESSION['username']."</a>, a to z důvodu porušení pravidel! V případě dotazů můžete vytvořit <a href='tickets' clas='text-primary'><u>nový tiket</u></a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_seller_query = mysqli_query($connect, $message_send_seller);
        //Add to shop_remove_log an event
        $remove_log = sprintf("INSERT INTO shop_remove_log (removed_item, removed_by, removed_time) VALUES ('%d', '%d', '%s');",
        mysqli_real_escape_string($connect, $remove_id),
        mysqli_real_escape_string($connect, $user_id),
        mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
        $remove_log_query = mysqli_query($connect, $remove_log);
        //Add +1 to removed items to table "users"
        $removed_items = sprintf("UPDATE users SET removed_items = removed_items + 1 WHERE user_id = '%d';",
        mysqli_real_escape_string($connect, $user_id));
        $removed_items_query = mysqli_query($connect, $removed_items);
        $_SESSION['success_message'] = "Vybraný test byl <span class='font-weight-bold'>úspěšně</span> odstraněn!";
        $_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
        header("Location: " . SITE_ROOT ."shop");
        exit();
    } else {
        $_SESSION['error_message'] = "Něco se stalo špatně při odstraňování tohoto testu!";
        $_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
        header("Location: " . SITE_ROOT ."shop");
        exit();
    }
} elseif ($_GET['action'] == 'restore' && isset($_GET['restore_id'])) {
    $restore_id = $_GET['restore_id'];

    $select = sprintf("SELECT item_name, item_createdby_userid FROM shop WHERE item_id = '%d';",
    mysqli_real_escape_string($connect, $restore_id));
    $select_query = mysqli_query($connect, $select);
    $select_row = mysqli_fetch_array($select_query);

    $restore = sprintf("UPDATE shop SET visible = 1 WHERE item_id = '%d';",
    mysqli_real_escape_string($connect, $restore_id));
    $restore_query = mysqli_query($connect, $restore);

    if ($restore_query) {
        //Create seller notification
		$message_send_seller = sprintf("INSERT INTO messages (message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $select_row['item_createdby_userid']),
		mysqli_real_escape_string($connect, "Váš test <span class='font-weight-bold'>".$select_row['item_name']."</span> byl vrácen do našeho obchodu uživatelem <a class='font-weight-bold text-primary' href='profile_show?profile_id=".$user_id."'>".$_SESSION['username']."</a>!"),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_seller_query = mysqli_query($connect, $message_send_seller);
        //Remove event from shop_remove_log
        $remove_log = sprintf("DELETE FROM shop_remove_log WHERE removed_item = '%d';",
        mysqli_real_escape_string($connect, $restore_id));
        $remove_log_query = mysqli_query($connect, $remove_log);
        $_SESSION['success_message'] = "Vybraný test byl <span class='font-weight-bold'>úspěšně</span> obnoven!";
        $_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
        header("Location: " . SITE_ROOT ."shop_removed");
        exit();
    } else {
        $_SESSION['error_message'] = "Něco se stalo špatně při obnovování tohoto testu!";
        $_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
        header("Location: " . SITE_ROOT ."shop_removed");
        exit();
    }
} else {
    handle_error("Nebylo získáno dostatečně parametrů.", "shop_action_admin");
}