<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

authorize_user();
full_register();
page_start("Log");
update_activity();
display_messages();

?>
<div class="container">
  <h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-tools"></i> Log</h4>
	<hr class="black mt-0 z-depth-1" style="width:100px;">
    <div class="d-flex">
      <div id="log-container" class="border rounded p-3 cloudy-knoxville-gradient z-depth-1 mx-auto my-4">

      </div>
    </div>
</div>


<?php
	page_end(true);
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
  <script type="text/javascript" src="js/ajax-log.js"></script>
  <?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>