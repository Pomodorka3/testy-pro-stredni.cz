<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

if (($_GET['action'] == 'createPost') && isset($_POST['post_title']) && isset($_POST['post_content'])) {

	$post_title = htmlspecialchars(trim($_POST['post_title']));
	$post_content = trim($_POST['post_content']);

	$insert_post = sprintf("INSERT INTO news (news_title, news_content, news_date, news_createdby) VALUES ('%s', '%s', '%s', '%d');",
	mysqli_real_escape_string($connect, $post_title),
	mysqli_real_escape_string($connect, $post_content),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $user_id));
	$insert_post_query = mysqli_query($connect, $insert_post);
	
	if ($insert_post_query) {
		$_SESSION['success_message'] = "Nový post byl <span class='font-weight-bold'>úspěšně</span> vytvořen!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."index");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při vytváření nového postu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."index");
		exit();
	}
} elseif (($_GET['action'] == 'removePost') && isset($_GET['post_id'])) {
	
	$post_id = trim($_GET['post_id']);

	$if_exists = sprintf("SELECT news_id FROM news WHERE news_id = %d;",
	mysqli_real_escape_string($connect, $post_id));
	$if_exists_query = mysqli_query($connect, $if_exists);

	if (mysqli_num_rows($if_exists_query) == 0) {
		$_SESSION['error_message'] = "Post s tímto id neexistuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."index");
		exit();
	}

	$remove_post = sprintf("UPDATE news SET news_visible = 0 WHERE news_id = %d;",
	mysqli_real_escape_string($connect, $post_id));
	$remove_post_query = mysqli_query($connect, $remove_post);

	if ($remove_post_query) {
		$_SESSION['success_message'] = "Vybraný post byl <span class='font-weight-bold'>úspěšně</span> odstraněn!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."index");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odstraňování postu!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."index");
		exit();
	}
} else {
	handle_error("Nebylo přijato dostatečně parametrů!", "user_codes_action");
}