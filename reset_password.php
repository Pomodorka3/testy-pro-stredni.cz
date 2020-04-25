<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';

if (isset($_SESSION['user_id'])) {
	header("Location: ".SITE_ROOT."profile");
}

if (isset($_GET['email']) && isset($_GET['hash'])) {

	$email = $_GET['email'];
	$hash = $_GET['hash'];
	$username = $_GET['username'];

} else {
	handle_error("Byly získány nekorektní parametry!");
}

page_start("Obnovení hesla");
display_messages();

?>

<body>

<div class="container h-100">
	<form action="<?php echo SITE_ROOT."reset_password_action"; ?>" method="POST" class="text-center border border-light px-4 py-5 mx-auto my-5 rounded cloudy-knoxville-gradient z-depth-1" id="resetPassword-window">
		<p class="h4 mb-4" style="font-family: 'Baloo', cursive;">Obnovení hesla</p>
		<div class="md-form mb-0">
			<input type="password" name="new_password" id="new_password" class="form-control mb-4" pattern=".{6,}" title="Minimum 6 symbolů." maxlength="20" autocomplete="off" required>
			<label for="new_password"><i class="fas fa-key"></i> Nové heslo</label>
		</div>
		<div class="md-form mb-0">
			<input type="password" name="new_password_retype" id="new_password_retype" class="form-control mb-4" maxlength="20" autocomplete="off" required>
			<label for="new_password_retype"><i class="fas fa-key"></i> Zopakujte zadané heslo</label>
		</div>
		<input type="hidden" id="email" name="email" value="<?php echo $email; ?>">
		<input type="hidden" id="hash" name="hash" value="<?php echo $hash; ?>">
		<input type="hidden" id="username" name="username" value="<?php echo $username; ?>">
		<button class="btn btn-outline-primary" type="submit"><i class="fas fa-redo-alt mr-2"></i>Obnovit heslo</button>
	</form>
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
  <?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>