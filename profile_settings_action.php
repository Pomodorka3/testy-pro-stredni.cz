<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];

if ($_GET['action'] == 'generalInfo' && isset($_POST['edit_name']) && isset($_POST['edit_lastname'])) {
	$new_name = htmlspecialchars(trim(ucfirst($_POST['edit_name'])));
	$new_lastname = htmlspecialchars(trim(ucfirst($_POST['edit_lastname'])));

	$user_update = sprintf("UPDATE users SET first_name = '%s', last_name = '%s' WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $new_name),
	mysqli_real_escape_string($connect, $new_lastname),
	mysqli_real_escape_string($connect, $user_id));
	$user_update_query = mysqli_query($connect, $user_update);
	//Direct image upload w/o exif orientation correction
	/*
		$uploadDir = "profile_pictures/";
		$allowedTypes = array('jpg', 'jpeg', 'png');

		$statusMsg = $errorMsg = $insert_values = $errorUpload = $errorUploadType = '';
		$file_name = $_FILES['edit_profile_image']['name'];
		$file_tmp_name = $_FILES['edit_profile_image']['tmp_name'];

		if (file_exists($_FILES['edit_profile_image']['tmp_name']) || is_uploaded_file($_FILES['edit_profile_image']['tmp_name'])) {
			//File upload path
			$date = date('dmy_His');
			$fileName = basename($file_name);
			$uploadFilePath = $uploadDir . $fileName;

			//Check file type
			$fileType = pathinfo($uploadFilePath, PATHINFO_EXTENSION);
			if (in_array($fileType, $allowedTypes)) {
				//Upload file on server
				if (move_uploaded_file($file_tmp_name, $uploadDir.$username."_".$fileName)) {
					$insert_values .= $uploadDir.$username."_".mysqli_real_escape_string($connect, $fileName);
				} else {
					$errorUpload .= $file_name.', ';
				}
			} else {
				$errorUploadType .= $file_name.', ';
			}
			$insert_values = trim($insert_values,',');
			$update_image = sprintf("UPDATE users SET image_path = '%s' WHERE user_id = '%d'",
			$insert_values,
			mysqli_real_escape_string($connect, $user_id));
			$update_image_query = mysqli_query($connect, $update_image);

			if ($update_image_query) {
				$_SESSION['image_path'] = $insert_values;
			}
		}
		*/

	if ($user_update_query) {
		$_SESSION['success_message'] = "Vaše nastavení bylo <span class='font-weight-bold'>úspěšně</span> obnoveno!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile_settings");
	} else {
		handle_error("Došlo k chybě při obnovení vašeho nastavení.", "profile_settings_action (generalInfo)");
	}
} elseif ($_GET['action'] == 'socialSet' && isset($_POST['edit_instagram']) && isset($_POST['edit_facebook'])) {
	$instagram = htmlspecialchars(trim($_POST['edit_instagram']));
	$facebook = htmlspecialchars(trim($_POST['edit_facebook']));

	$user_update = sprintf("UPDATE users SET instagram = '%s', facebook = '%s' WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $instagram),
	mysqli_real_escape_string($connect, $facebook),
	mysqli_real_escape_string($connect, $user_id));
	$user_update_query = mysqli_query($connect, $user_update);

	if ($user_update_query) {
		$_SESSION['success_message'] = "Vaše nastavení bylo <span class='font-weight-bold'>úspěšně</span> obnoveno!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile_settings");
	} else {
		handle_error("Došlo k chybě při obnovení vašeho nastavení.", "profile_settings_action (socialSet)");
	}
} elseif ($_GET['action'] == 'paymentSet' && isset($_POST['bank_number'])) {
	$bankNumber = htmlspecialchars(trim($_POST['bank_number']));

	$user_update = sprintf("UPDATE users SET bank_number = '%s' WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $bankNumber),
	mysqli_real_escape_string($connect, $user_id));
	$user_update_query = mysqli_query($connect, $user_update);

	if ($user_update_query) {
		$_SESSION['success_message'] = "Vaše nastavení bylo <span class='font-weight-bold'>úspěšně</span> obnoveno!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile_settings");
	} else {
		handle_error("Došlo k chybě při obnovení vašeho nastavení.", "profile_settings_action (paymentSet)");
	}
} elseif ($_GET['action'] == 'pwdChange' && isset($_POST['new_password']) && isset($_POST['repeat_password'])) {
	$newPwd = crypt(trim($_POST['new_password']), $_SESSION['username']);
	$repeatPwd = crypt(trim($_POST['repeat_password']), $_SESSION['username']);

	if (strcmp($newPwd, $repeatPwd) != 0) {
		$_SESSION['error_message'] = "Hesla si neodpovídají!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile_settings");
		exit();
	}

	$user_update = sprintf("UPDATE users SET password = '%s' WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $newPwd),
	mysqli_real_escape_string($connect, $user_id));
	$user_update_query = mysqli_query($connect, $user_update);

	if ($user_update_query) {
		$_SESSION['success_message'] = "Vaše heslo bylo <span class='font-weight-bold'>úspěšně</span> změněno!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile_settings");
	} else {
		handle_error("Došlo k chybě při obnovení vašeho nastavení.", "profile_settings_action (pwdChange)");
	}
} elseif ($_GET['action'] == 'refferalCode' && isset($_POST['refferal_code'])) {
	$refferalCode = htmlspecialchars(trim($_POST['refferal_code']));

	$if_exists = sprintf("SELECT ref_code FROM users WHERE ref_code = '%s';",
	mysqli_real_escape_string($connect, $refferalCode));
	$if_exists_query = mysqli_query($connect, $if_exists);
	if (mysqli_num_rows($if_exists_query) != 0) {
		$_SESSION['error_message'] = "Tento referální kód již existuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile_settings");
		exit();
	}

	$user_update = sprintf("UPDATE users SET ref_code = '%s' WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $refferalCode),
	mysqli_real_escape_string($connect, $user_id));
	$user_update_query = mysqli_query($connect, $user_update);
	if ($user_update_query) {
		$_SESSION['success_message'] = "Váš referální kód byl <span class='font-weight-bold'>úspěšně</span> nastaven! Od této chvíle budete získávat určitá procenta z každého nákupu uživatele, který se zaregistruje s Vaším referálním kódem.";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: ". SITE_ROOT ."profile_settings?settingsPage=codes");
	} else {
		handle_error("Došlo k chybě při obnovení vašeho nastavení.", "profile_settings_action (refferalCode)");
	}
} else {
	handle_error("Nebylo získáno dostatečně parametrů.", "profile_settings_action");
}