<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';

	session_start();

	$email = trim($_POST['email']);
	$hash = $_POST['hash'];
	$username = htmlspecialchars(trim($_POST['username']));
	$password = crypt(trim($_POST['new_password']), $username);
	$password_check = trim($_POST['new_password']);
	$password_check_retype = trim($_POST['new_password_retype']);

	if (isset($email) && !empty($email) && isset($hash) && !empty($hash) && isset($password) && !empty($password) && isset($username) && !empty($username)) {
		//Сделать проверку на совпадение пароля на основе JavaScript
		if ($password_check != $password_check_retype) {
			$_SESSION['error_message'] = "Hesla si neodpovídají!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header('Location: '. SITE_ROOT. 'reset_password?hash='.$hash.'&email='.$email.'&username='.$username);
			exit();
		}

		$check_user = sprintf("SELECT username FROM users WHERE email = '%s' AND hash = '%s';",
		mysqli_real_escape_string($connect, $email),
		mysqli_real_escape_string($connect, $hash));
		$check_user_query = mysqli_query($connect, $check_user);

		if (mysqli_num_rows($check_user_query) == 1) {
			$new_password = sprintf("UPDATE users SET password = '%s' WHERE email = '%s' AND hash = '%s';",
			mysqli_real_escape_string($connect, $password),
			mysqli_real_escape_string($connect, $email),
			mysqli_real_escape_string($connect, $hash));
			$new_password_query = mysqli_query($connect, $new_password);

			if ($new_password_query) {
				$update_hash = sprintf("UPDATE users SET hash = '%s', session_hash = '%s' WHERE email = '%s' AND hash = '%s';",
				mysqli_real_escape_string($connect, md5(rand(0,1000))),
				mysqli_real_escape_string($connect, md5(rand(0,1000))),
				mysqli_real_escape_string($connect, $email),
				mysqli_real_escape_string($connect, $hash));
				$update_hash_query = mysqli_query($connect, $update_hash);
				$_SESSION['success_message'] = "Vaše heslo bylo úspěšně změněno!";
				$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
				header("Location: " . SITE_ROOT ."signin");
			} else {
				handle_error("Něco se stalo špatně při změňování Vašeho hesla.");
			}
		} else {
			handle_error("Zadána nesprávná kombinace hash a emailu.");
		}
	} else {
		handle_error("Něco se stalo špatně při ověřování uživatele!");
	}