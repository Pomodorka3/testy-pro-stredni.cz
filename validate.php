<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';

	session_start();

	$email = $_GET['email'];
	$hash = $_GET['hash'];

	if (isset($email) && !empty($email) && isset($hash) && !empty($hash)) {
		$check_user = sprintf("SELECT user_id FROM users WHERE email = '%s' AND hash = '%s';",
		mysqli_real_escape_string($connect, $email),
		mysqli_real_escape_string($connect, $hash));
		$check_user_query = mysqli_query($connect, $check_user);

		if (mysqli_num_rows($check_user_query) == 1) {
			$add_user = sprintf("UPDATE users SET activated = 1 WHERE email = '%s' AND hash = '%s';",
			mysqli_real_escape_string($connect, $email),
			mysqli_real_escape_string($connect, $hash));
			$add_user_query = mysqli_query($connect, $add_user);

			$update_hash = sprintf("UPDATE users SET hash = '%s' WHERE email = '%s' AND hash = '%s';",
			mysqli_real_escape_string($connect, md5(rand(0,1000))),
			mysqli_real_escape_string($connect, $email),
			mysqli_real_escape_string($connect, $hash));
			$update_hash_query = mysqli_query($connect, $update_hash);

			$_SESSION['success_message'] = "Váš účet byl <span class='font-weight-bold'>úspěšně</span> aktivován!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."signin");
		} else {
			handle_error("Špatná kombinace hash a emailu.");
		}
	} else {
		handle_error("Něco se stalo špatně při aktivaci Vašeho účtu!");
	}