<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';

session_start();
update_activity();

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
    <div class="d-flex">
        <i class="fas fa-times mx-auto fa-10x mb-3 animated rotateIn text-danger"></i>
    </div>
      <p class="text-center font-weight-bold my-4">Při platbě došlo k chybě! Více info v upozorněních.</p>
      <p class="text-center font-weight-bold my-4">Přesměrování na profil...</p>
      <div class="text-center">
        <!-- <a href="index" class="btn btn-primary font-weight-bold mx-2 btn-sm" style="border-radius: 50px;">Domů</a>
        <a href="profile" class="btn btn-primary font-weight-bold mx-2 btn-sm" style="border-radius: 50px;">Profil</a> -->
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
<?php
    header("refresh:4;url=profile");
?>