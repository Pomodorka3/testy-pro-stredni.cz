<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

	session_start();
	authorize_user(array("Administrator", "Main administrator"));
	full_register();
	page_start("Žádosti Validátorů");
	display_messages();
	update_activity();

?>
<div class="container">
	<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="far fa-address-card"></i> Žádosti do skupiny Validátorů</h4>
	<hr class="black mt-0 z-depth-1" style="width:100px;">
	<div id="requests_list">

	</div>
</div>

<?php
	page_end(true, 30);
?>
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
  <script type="text/javascript" src="js/ajax-validators-requests.js"></script>
	<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>