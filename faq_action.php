<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';

session_start();
authorize_user(array("Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_POST['title']) && isset($_POST['content'])) {
	$title = htmlspecialchars(trim($_POST['title']));
	$content = trim($_POST['content']);

	$insert_faq = sprintf("INSERT INTO faq (faq_title, faq_content, faq_created, faq_createdby) VALUES ('%s', '%s', '%s', '%d');",
	mysqli_real_escape_string($connect, $title),
	mysqli_real_escape_string($connect, $content),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $user_id));
	$insert_faq_query = mysqli_query($connect, $insert_faq);
	if ($insert_faq_query) {
		$_SESSION['success_message'] = "FAQ byla <span class='text-success font-weight-bold'>úspěšně</span> přidána!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."faq");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při přidávaní FAQ";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."faq");
		exit();
	}
} elseif (isset($_GET['remove_id'])) {
	$remove_id = $_GET['remove_id'];

	$if_exists = sprintf("SELECT faq_id FROM faq WHERE faq_id = '%d';",
	mysqli_real_escape_string($connect, $remove_id));
	$if_exists_query = mysqli_query($connect, $if_exists);
	if (mysqli_num_rows($if_exists_query) == 0) {
		handle_error("FAQ s tímto ID neexistuje", "faq_action=remove");
	}

	$remove_faq = sprintf("UPDATE faq SET faq_visible = '0' WHERE faq_id = '%d';",
	mysqli_real_escape_string($connect, $remove_id));
	$remove_faq_query = mysqli_query($connect, $remove_faq);
	if ($remove_faq_query) {
		$_SESSION['success_message'] = "Vybraná FAQ byla<span class='text-success font-weight-bold'>úspěšně</span> odstraněna!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."faq");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odstraňování FAQ";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."faq");
		exit();
	}
} else {
	handle_error("Stala se neočekávaná chyba", "faq_action");
}