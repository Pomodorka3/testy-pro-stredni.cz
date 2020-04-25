<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Administrator", "Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_GET['block_id'])) {
	$block_id = $_GET['block_id'];

	if ($user_id == $block_id) {
		$_SESSION['error_message'] = "Nemůžete zabanovat sám sebe!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	//При попытке заблокировать пользователя с атрибутом user_id=1, пользователь заблокирует сам себя.
	//If user tries to block user with user_id=1. System will automatically block him.
	if ($block_id == 1) {
		$block_id = $user_id;
	}

	$if_banned = sprintf("SELECT banned_by FROM banned_users WHERE banned_id = '%d' AND ban_active = 1;",
	mysqli_real_escape_string($connect, $block_id));
	$if_banned_query = mysqli_query($connect, $if_banned);
	$if_banned_row = mysqli_fetch_row($if_banned_query);

	if (mysqli_num_rows($if_banned_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je zabanován!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$ban = sprintf("INSERT INTO banned_users (banned_id, banned_by, ban_time, ban_description) VALUES ('%d', '%d', '%s', '%s');", mysqli_real_escape_string($connect, $block_id),
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	"Manually");
	$ban_query = mysqli_query($connect, $ban);

	if ($ban_query) {
		//Remove all selling items from user
		$sellingItems_remove = sprintf("UPDATE shop SET visible = 0 WHERE item_createdby_userid = '%d';",
		mysqli_real_escape_string($connect, $block_id));
		$sellingItems_remove_query = mysqli_query($connect, $sellingItems_remove);
		//Remove user from group
		$removeGroup = sprintf("DELETE FROM users_groups WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $block_id));
		$removeGroup_query = mysqli_query($connect, $removeGroup);
		//Create notification for user
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $block_id),
		mysqli_real_escape_string($connect, "Váš účet byl zabanován a také odstraněn ze všech skupin."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> zabanován!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při banování uživatele!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (isset($_GET['unblock_id'])) {
	$unblock_id = $_GET['unblock_id'];

	$if_banned = sprintf("SELECT banned_by FROM banned_users WHERE banned_id = '%d' AND ban_active = 1;",
	mysqli_real_escape_string($connect, $unblock_id));
	$if_banned_query = mysqli_query($connect, $if_banned);
	$if_banned_row = mysqli_fetch_row($if_banned_query);

	if (mysqli_num_rows($if_banned_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel není zabanován!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	//Снимать бан может только тот, кто его выдал.
	/*if ($if_banned_row[0] != $user_id) {
		$_SESSION['error_message'] = "You haven't banned this user!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}*/

	$unban = sprintf("UPDATE banned_users SET ban_active = 0 WHERE banned_id = '%d';",
		mysqli_real_escape_string($connect, $unblock_id));
	$unban_query = mysqli_query($connect, $unban);

	if ($unban_query) {
		//Insert an unban into unbanned_users log
		$unbannedUser = sprintf("INSERT INTO unbanned_users(unbanned_id, unbanned_by, unban_time) VALUES ('%d', '%d', '%s');",
		mysqli_real_escape_string($connect, $unblock_id),
		mysqli_real_escape_string($connect, $user_id),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$unbannedUser_query = mysqli_query($connect, $unbannedUser);
		//Create notification for unbanned user
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $unblock_id),
		mysqli_real_escape_string($connect, "Váš účet byl odblokován. Pokud si myslíte, že došlo k chybě, můžete vytvořit <a href='tickets' class='font-weight-bold text-primary'>nový tiket</a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> odblokován";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při odblokování uživatele!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (($_GET['action'] == 'balance') && isset($_GET['user_id']) && isset($_POST['new_balance'])) {
	authorize_user(array("Main administrator"));
	$get_user_id = $_GET['user_id'];
	$new_balance = $_POST['new_balance'];

	if (!isset($_POST['reason'])) {
		$reason = '';
	} elseif (!empty($_POST['reason'])) {
		$reason = ' Reason: '.trim($_POST['reason']).'';
	}

	if ($new_balance < 0) {
		$_SESSION['error_message'] = "Zadali jste špatné číslo!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_exists = "SELECT user_id FROM users WHERE user_id = '".$get_user_id."'";
	$user_exists_query = mysqli_query($connect, $user_exists);
	if (mysqli_num_rows($user_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$update_balance = "UPDATE users SET balance = '".$new_balance."' WHERE user_id = '".$get_user_id."'";
	$update_balance_query = mysqli_query($connect, $update_balance);
	if ($update_balance_query) {
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $get_user_id),
		mysqli_real_escape_string($connect, "Vaše konto bylo nastaveno na <span class='font-weight-bold'>".$new_balance."</span> Kč uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>. Důvod: ".$reason.""),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Konto uživatele bylo <span class='font-weight-bold'>úspěšně</span> změněno!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při změně konta!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (($_POST['action'] == 'setAdmin') && isset($_GET['user_id']) && isset($_POST['admin_set_pwd'])) {

	if ($_POST['admin_set_pwd'] != ADMIN_SET_PWD) {
		$_SESSION['error_message'] = "Bylo zadáno špatné admin heslo!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$get_user_id = $_GET['user_id'];

	$user_exists = "SELECT user_id FROM users WHERE user_id = '".$get_user_id."'";
	$user_exists_query = mysqli_query($connect, $user_exists);
	if (mysqli_num_rows($user_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_admin = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '1'";
	$user_admin_query = mysqli_query($connect, $user_admin);
	if (mysqli_num_rows($user_admin_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Administrátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_validator = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '2'";
	$user_validator_query = mysqli_query($connect, $user_validator);
	if (mysqli_num_rows($user_validator_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Validátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_support = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '3'";
	$user_support_query = mysqli_query($connect, $user_support);
	if (mysqli_num_rows($user_support_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Supportem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$set_admin = sprintf("INSERT INTO users_groups (user_id, group_id, event_date, set_by, set_method) VALUES ('%d', '1', '%s', '%d', 'Manually');",
	mysqli_real_escape_string($connect, $get_user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $user_id));
	$set_admin_query = mysqli_query($connect, $set_admin);
	if ($set_admin_query) {
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $get_user_id),
		mysqli_real_escape_string($connect, "<span class='text-secondary font-weight-bold'>Gratulace, od teď jste novým Administrátorem našeho projektu!</span> Obraťte se na administrátora, který Vás přidal do skupiny pro admin heslo pomocí zprávy ('Odeslat zprávu' na horní liště). Byl jste přidán uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> přidán do skupiny Administrátorů!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při přidávání uživatele do skupiny!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (($_POST['action'] == 'removeAdmin') && isset($_GET['user_id']) && isset($_POST['admin_set_pwd'])) {

	if ($_GET['user_id'] == $user_id) {
		$_SESSION['error_message'] = "Tuto funkci nelze aplikovat na sebe!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	if ($_POST['admin_set_pwd'] != ADMIN_SET_PWD) {
		$_SESSION['error_message'] = "Bylo zadáno špatné admin heslo!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$get_user_id = $_GET['user_id'];

	$user_exists = "SELECT user_id FROM users WHERE user_id = '".$get_user_id."'";
	$user_exists_query = mysqli_query($connect, $user_exists);
	if (mysqli_num_rows($user_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_admin = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '1'";
	$user_admin_query = mysqli_query($connect, $user_admin);
	if (mysqli_num_rows($user_admin_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel není Administrátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$remove_admin = sprintf("DELETE FROM users_groups WHERE group_id = '1' AND user_id = '%d';",
	mysqli_real_escape_string($connect, $get_user_id));
	$remove_admin_query = mysqli_query($connect, $remove_admin);
	if ($remove_admin_query) {
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $get_user_id),
		mysqli_real_escape_string($connect, "Tento uživatel byl <span class='text-danger font-weight-bold'>odstraněn</span> ze skupiny Administrátorů uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> odstraněn ze skupiny Administrátorů!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při odstraňování uživatele ze skupiny!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (($_POST['action'] == 'setSupport') && isset($_GET['user_id']) && isset($_POST['admin_set_pwd'])) {

	if ($_POST['admin_set_pwd'] != ADMIN_SET_PWD) {
		$_SESSION['error_message'] = "Bylo zadáno špatné admin heslo!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$get_user_id = $_GET['user_id'];

	$user_exists = "SELECT user_id FROM users WHERE user_id = '".$get_user_id."'";
	$user_exists_query = mysqli_query($connect, $user_exists);
	if (mysqli_num_rows($user_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_admin = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '1'";
	$user_admin_query = mysqli_query($connect, $user_admin);
	if (mysqli_num_rows($user_admin_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Administrátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_validator = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '2'";
	$user_validator_query = mysqli_query($connect, $user_validator);
	if (mysqli_num_rows($user_validator_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Validátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_support = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '3'";
	$user_support_query = mysqli_query($connect, $user_support);
	if (mysqli_num_rows($user_support_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Supportem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$set_support = sprintf("INSERT INTO users_groups (user_id, group_id, event_date, set_by, set_method) VALUES ('%d', '3', '%s', '%d', 'Manually');",
	mysqli_real_escape_string($connect, $get_user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $user_id));
	$set_support_query = mysqli_query($connect, $set_support);
	if ($set_support_query) {
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $get_user_id),
		mysqli_real_escape_string($connect, "<span class='text-secondary font-weight-bold'>Gratulace, od teď jste novým Supportem našeho projektu!</span> Přidal Vás do skupiny uživatel <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> přidán do skupiny Supportů!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při přidávání uživatele do skupiny!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (($_POST['action'] == 'removeSupport') && isset($_GET['user_id']) && isset($_POST['admin_set_pwd'])) {

	if ($_POST['admin_set_pwd'] != ADMIN_SET_PWD) {
		$_SESSION['error_message'] = "Bylo zadáno špatné admin heslo!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$get_user_id = $_GET['user_id'];

	$user_exists = "SELECT user_id FROM users WHERE user_id = '".$get_user_id."'";
	$user_exists_query = mysqli_query($connect, $user_exists);
	if (mysqli_num_rows($user_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_support = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '3'";
	$user_support_query = mysqli_query($connect, $user_support);
	if (mysqli_num_rows($user_support_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel není Supportem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$remove_support = sprintf("DELETE FROM users_groups WHERE group_id = '3' AND user_id = '%d';",
	mysqli_real_escape_string($connect, $get_user_id));
	$remove_support_query = mysqli_query($connect, $remove_support);
	if ($remove_support_query) {
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $get_user_id),
		mysqli_real_escape_string($connect, "Tento uživatel byl <span class='text-danger font-weight-bold'>odstraněn</span> ze skupiny Supportů uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> odstraněn ze skupiny Supportů!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při odstraňování uživatele ze skupiny!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (($_POST['action'] == 'setValidator') && isset($_GET['user_id']) && isset($_POST['admin_set_pwd'])) {

	if ($_POST['admin_set_pwd'] != ADMIN_SET_PWD) {
		$_SESSION['error_message'] = "Bylo zadáno špatné admin heslo!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$get_user_id = $_GET['user_id'];

	$user_exists = "SELECT user_id FROM users WHERE user_id = '".$get_user_id."'";
	$user_exists_query = mysqli_query($connect, $user_exists);
	if (mysqli_num_rows($user_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_admin = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '1'";
	$user_admin_query = mysqli_query($connect, $user_admin);
	if (mysqli_num_rows($user_admin_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Administrátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_validator = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '2'";
	$user_validator_query = mysqli_query($connect, $user_validator);
	if (mysqli_num_rows($user_validator_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Validátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_support = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '3'";
	$user_support_query = mysqli_query($connect, $user_support);
	if (mysqli_num_rows($user_support_query) != 0) {
		$_SESSION['error_message'] = "Tento uživatel už je Supportem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$set_support = sprintf("INSERT INTO users_groups (user_id, group_id, event_date, set_by, set_method) VALUES ('%d', '2', '%s', '%d', 'Manually');",
	mysqli_real_escape_string($connect, $get_user_id),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $user_id));
	$set_support_query = mysqli_query($connect, $set_support);
	if ($set_support_query) {
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $get_user_id),
		mysqli_real_escape_string($connect, "<span class='text-secondary font-weight-bold'>Gratulace, od teď jste novým Validátorem našeho projektu!</span> Přidal Vás do skupiny uživatel <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> přidán do skupiny Validátorů!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při přidávání uživatele do skupiny!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} elseif (($_POST['action'] == 'removeValidator') && isset($_GET['user_id']) && isset($_POST['admin_set_pwd'])) {

	if ($_POST['admin_set_pwd'] != ADMIN_SET_PWD) {
		$_SESSION['error_message'] = "Bylo zadáno špatné admin heslo!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$get_user_id = $_GET['user_id'];

	$user_exists = "SELECT user_id FROM users WHERE user_id = '".$get_user_id."'";
	$user_exists_query = mysqli_query($connect, $user_exists);
	if (mysqli_num_rows($user_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$user_support = "SELECT user_id FROM users_groups WHERE user_id = '".$get_user_id."' AND group_id = '2'";
	$user_support_query = mysqli_query($connect, $user_support);
	if (mysqli_num_rows($user_support_query) == 0) {
		$_SESSION['error_message'] = "Tento uživatel není Validátorem!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}

	$remove_support = sprintf("DELETE FROM users_groups WHERE group_id = '2' AND user_id = '%d';",
	mysqli_real_escape_string($connect, $get_user_id));
	$remove_support_query = mysqli_query($connect, $remove_support);
	if ($remove_support_query) {
		$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
		mysqli_real_escape_string($connect, 0),
		mysqli_real_escape_string($connect, $get_user_id),
		mysqli_real_escape_string($connect, "Tento uživatel byl <span class='text-danger font-weight-bold'>odstraněn</span> ze skupiny Validátorů uživatelem <a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_id."'><u>".$_SESSION['username']."</u></a>."),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
		$message_send_query = mysqli_query($connect, $message_send);
		$_SESSION['success_message'] = "Tento uživatel byl <span class='font-weight-bold'>úspěšně</span> odstraněn ze skupiny Validátorů!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při odstraňování uživatele ze skupiny!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."admin_panel");
		exit();
	}
} else {
	handle_error("Nebylo získáno id!", "admin_action_user");
}