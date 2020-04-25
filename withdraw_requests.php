<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';
require_once '../../scripts/authorize.php';

session_start();
authorize_user(array("Main administrator"));
full_register();
school_check();
page_start("Žádosti o výběr");
display_messages();
update_activity();

$user_id = $_SESSION['user_id'];

$withdraws = 'SELECT SUM(withdraw_sum) FROM withdraw WHERE withdraw_status = 1';
$withdraws_query = mysqli_query($connect, $withdraws);
$withdraws_row = mysqli_fetch_row($withdraws_query);

?>
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-hand-holding-usd"></i> Žádosti o výběr</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
<div class="row mx-1 my-4">
	<div class="col-md-2">
		<h5 class="text-center font-weight-bold mb-3" style="font-family: 'Baloo', cursive;">Filtr</h5>
		<div class="md-form m-0">
			<input type="text" name="username" id="username" class="form-control mb-4 filter_field" autocomplete="off">
			<label for="username" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Žadatel</label>
		</div>
		<div class="md-form m-0">
			<input type="text" name="withdrawSum" id="withdrawSum" class="form-control mb-4 filter_field" autocomplete="off">
			<label for="withdrawSum" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Částka</label>
		</div>
		<div class="md-form m-0">
			<input type="text" name="bankAccount" id="bankAccount" class="form-control mb-4 filter_field" autocomplete="off">
			<label for="bankAccount" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Bank. účet</label>
		</div>
		<div class="my-3">
			<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Ročník</h6>
			<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input statusFilter" id="radio1" name="status" value="0">
				<label for="radio1" class="custom-control-label">Zpracovává se</label>
			</div>
			<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input statusFilter" id="radio2" name="status" value="1">
				<label for="radio2" class="custom-control-label">Potvrzeno</label>
			</div>
			<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input statusFilter" id="radio3" name="status" value="2">
				<label for="radio3" class="custom-control-label">Odmítnuto</label>
			</div>
		</div>
	</div>
	<div class="col-md-10 mt-4 mt-md-0">
		<h6 class="text-center text-md-left">Celkem vyplaceno: <span class="font-weight-bold"><?php echo $withdraws_row[0]; ?> Kč</span></h6>
		<div id="withdraw_requests">

		</div>
	</div>
</div>

<?php
	page_end(true, 10);
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
  <script type="text/javascript" src="js/ajax-withdraw-requests.js"></script>
  <script>
  	$('#withdraw_form input[type=number]').on('change invalid', function() {
    var textfield = $(this).get(0);
    
    // 'setCustomValidity not only sets the message, but also marks
    // the field as invalid. In order to see whether the field really is
    // invalid, we have to remove the message first
    textfield.setCustomValidity('');
    
    if (!textfield.validity.valid) {
      textfield.setCustomValidity('Zadaná částka musí být v rozmezí od 30 Kč do <?php echo $_SESSION['balance']; ?> Kč.');  
    }
});
  </script>
<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>