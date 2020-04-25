<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';

if (isset($_SESSION['user_id'])) {
	header("Location: ".SITE_ROOT."profile");
}

if (isset($_POST['email']) && isset($_POST['username'])) {

		$email = trim($_POST['email']);
		$username = htmlspecialchars(trim($_POST['username']));

		$user_exists = sprintf("SELECT hash FROM users WHERE username = '%s' AND email = '%s'", 
			mysqli_real_escape_string($connect, $username),
			mysqli_real_escape_string($connect, $email));
		$user_exists_query = mysqli_query($connect, $user_exists);
		
		if (mysqli_num_rows($user_exists_query) == 0) {
			handle_error("Tento uživatel neexistuje!", "(forgotten_password)");
		}

		$user_exists_row = mysqli_fetch_array($user_exists_query);
		$_SESSION['success_message'] = "Na Vámi zadaný email byl odeslán odkaz pro obnovení Vašeho hesla!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";

		//Отправить письмо на почту в формате reset_password?username=user&email=asd@asd.asd&hash=asda12414h14n14k
		//Send mail with activation link
		mail($email, 'Obnovení hesla', "Zde je odkaz pro obnovení hesla k Vašemu účtu: http://testy-pro-stredni.cz/reset_password?username=".$username."&email=".$email."&hash=".$user_exists_row[0]."\n\r\n\rS pozdravem tým Testy-pro-střední");
}

page_start("Zapomenuté heslo");
display_messages();

?>

<body>


<div class="container h-100">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="text-center border border-light px-4 py-5 mx-auto my-5 rounded cloudy-knoxville-gradient z-depth-1" id="forgottenPassword-window">
		<p class="h4 mb-4" style="font-family: 'Baloo', cursive;">Zapomenuté heslo</p>
		<div class="md-form mb-0">
			<input type="email" name="email" id="email" class="form-control mb-4" maxlength="40" autocomplete="off" required>
			<label for="email"><i class="fas fa-envelope"></i> E-mail zadaný při registraci</label>
		</div>
		<div class="md-form mb-0">
			<input type="text" name="username" id="username" class="form-control mb-4" maxlength="20" autocomplete="off" required>
			<label for="username"><i class="fas fa-user-alt"></i> Uživatelské jméno</label>
		</div>
		<button class="btn btn-info btn-block my-4" type="submit"><i class="fas fa-paper-plane mr-2"></i>Odeslat e-mail</button>
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