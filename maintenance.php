<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';

$config = "SELECT maintain_mode FROM config;";
$config_query = mysqli_query($connect, $config);
$config_row = mysqli_fetch_row($config_query);

if ($config_row[0] == 0) {
    header("Location: ". SITE_ROOT ."profile");
	  exit();
}
last_login($user_id); 

setcookie('session_hash', $hash, 1, '/');
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['balance']);
unset($_SESSION['first_name']);
unset($_SESSION['last_name']);
unset($_SESSION['school_id']);
unset($_SESSION['image_path']);

?>
<!DOCTYPE html>
<html lang="cz">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Material Design Bootstrap</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
  <!-- Bootstrap core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="css/mdb.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="css/style.css" rel="stylesheet">
</head>

<body>
<div class="" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <img src="img/maintenance.png" class="d-flex mx-auto img-fluid" style="width: 300px;">
      <h5 class="text-center font-weight-bold my-4">Omlouváme se, ale na serveru aktuálně probíhají technické práce. Vraťte se později!</h5>
      <div class="d-flex">
        <a class="btn btn-primary btn-sm mx-auto" href="index">Domů</a>
      </div>
  </div>

</body>
  <!-- JQuery -->
  <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.js"></script>
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/custom-script.js"></script>
</body>

</html>