<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user();
update_activity();

$user_id = $_SESSION['user_id'];
$name = htmlspecialchars(trim(ucfirst($_POST['name'])));
$lastname = htmlspecialchars(trim(ucfirst($_POST['lastname'])));
$instagram = htmlspecialchars(trim($_POST['instagram']));
$facebook = htmlspecialchars(trim($_POST['facebook']));

$insert = sprintf("UPDATE users SET first_name = '%s', last_name = '%s', full_register = '%d', instagram = '%s', facebook = '%s' WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $name),
	mysqli_real_escape_string($connect, $lastname),
	mysqli_real_escape_string($connect, "1"),
	mysqli_real_escape_string($connect, $instagram),
	mysqli_real_escape_string($connect, $facebook),
	mysqli_real_escape_string($connect, $user_id));
$insert_query = mysqli_query($connect, $insert);

if ($insert_query) {
	$_SESSION['first_setup'] = true;
	$_SESSION['info_message'] = "Pro získání přístupu na náš trh testů si musíte nastavit Vaší školu. Vybírejte pozorně, další změna školy bude dostupná až po uplynutí <span class='font-weight-bold'>2 týdnů</span>!";
	$_SESSION['js_modal_show'] = "<script>$('#infoModal').modal('show')</script>";
	header("Location: ". SITE_ROOT ."school_select");
	/*$_SESSION['success_message'] = "Váš profil byl úspěšně nastaven!";
	$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
	header("Location: ". SITE_ROOT ."profile");*/
} else {
	handle_error("Došlo k chybě při nastavování profilu!", mysqli_error($connect, $insert_query));
}