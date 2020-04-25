<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';

$user_error_message = $_COOKIE['user_error_message'];
$system_error_message = $_COOKIE['system_error_message'];

if (!isset($user_error_message)) {
  $user_error_message = "Došlo k neznámé chybě!";
}
if (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id']) || user_in_group("Support", $_SESSION['user_id'])) {
  
} else {
  $system_error_message = "";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Chyba</title>
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
    <img src="img/404.png" class="d-flex mx-auto img-fluid">
      <p class="text-center font-weight-bold my-4">Error... <?php echo $user_error_message; ?>
      </p>
      <p class="text-center"><?php echo $system_error_message; ?></p>
      <div class="text-center">
        <a href="index" class="btn btn-primary font-weight-bold mx-2 btn-sm" style="border-radius: 50px;">Domů</a>
        <a href="profile" class="btn btn-primary font-weight-bold mx-2 btn-sm" style="border-radius: 50px;">Profil</a>
      </div>
  </div>

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