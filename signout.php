<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';

session_start();

$user_id = $_SESSION['user_id'];

last_login($user_id); 

setcookie('session_hash', $hash, 1, '/');
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['balance']);
unset($_SESSION['first_name']);
unset($_SESSION['last_name']);
unset($_SESSION['school_id']);
unset($_SESSION['image_path']);
unset($_SESSION['status']);
unset($_SESSION['notifications_count']);
unset($_SESSION['first_setup']);

$_SESSION['success_message'] = "Uživatel byl <span class='font-weight-bold'>úspěšně</span> odhlášen!";
$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
header("Location: signin");
exit();

?>