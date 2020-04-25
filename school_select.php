<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';

$user_has_school = sprintf("SELECT school_id FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $_SESSION['user_id']));
$user_has_school_query = mysqli_query($connect, $user_has_school);
$user_has_school_row = mysqli_fetch_array($user_has_school_query);
if ($user_has_school_row['school_id'] != 0) {
	handle_error("Už máte nastavenou školu");
	exit();
}

$city = "SELECT city_id, city_name FROM city ORDER BY city_name ASC LIMIT 6;";
$city_query = mysqli_query($connect, $city);
$city_query2 = mysqli_query($connect, $city);

if (isset($_POST['school'])) {
	$school_name = $_POST['school'];

	$school_id = sprintf("SELECT school_id FROM school WHERE school_name = '%s';",
	mysqli_real_escape_string($connect, $school_name));
	$school_id_query = mysqli_query($connect, $school_id);
	// $school_id_row = mysqli_fetch_row($school_id_query);

	if (mysqli_num_rows($school_id_query) > 0) {
		$school_id_row = mysqli_fetch_row($school_id_query);

		$insert_school = sprintf("UPDATE users SET school_id = '%d', school_setdate = '%s' WHERE user_id = '%d';",
		mysqli_real_escape_string($connect, $school_id_row[0]),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $_SESSION['user_id']));
		$insert_school_query = mysqli_query($connect, $insert_school);

		if ($insert_school_query) {
			if ($_SESSION['first_setup'] == true) {
				/* $_SESSION['school_id'] = $school_id_row[0];
				$_SESSION['success_message'] = "Váš profil byl úspěšně nastaven!";
				$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
				header("Location: ". SITE_ROOT ."profile");
				exit(); */
				header("Location: ". SITE_ROOT ."item_add");
			} else {
				$_SESSION['school_id'] = $school_id_row[0];
				$_SESSION['success_message'] = "Vaše škola byla úspěšně nastavena!";
				$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
				header('Location: '. SITE_ROOT . 'shop');
				exit();
			}
		} else {
			$_SESSION['error_message'] = "Došlo k chybě při změně školy. Je možné, že tato škola není v naší databázi. V tom případě můžete požádat o její přidání";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header('Location: '. SITE_ROOT . 'school_select');
			exit();
		}
	} else {
		$_SESSION['error_message'] = "Došlo k chybě při změně školy. Je možné, že tato škola není v naší databázi. V tom případě můžete požádat o její přidání";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header('Location: '. SITE_ROOT . 'school_select');
		exit();
	}

	
}

authorize_user();
full_register();
page_start("Výběr školy");
display_messages();
update_activity();

?>

<div class="container">
	<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;">Nastavení školy</h4>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="my-3">
		<label for="city" class="mt-1">Vyberte město:</label>
		<input list="citylist" id="city" name="city" class="form-control" placeholder="Název města" required autocomplete="off">
        <datalist id="citylist">

        </datalist>	
		<label for="district" class="mt-2">Vyberte čtvrť:</label>
		<input list="districtlist" id="district" name="district" class="form-control" placeholder="Název čtvrtě" required autocomplete="off">
		<datalist id="districtlist">
			<option value="Prvně vyberte město"></option>
        </datalist>	
		<label for="school" class="mt-2">Vyberte školu:</label>
		<input list="schoollist" id="school" name="school" class="form-control" placeholder="Název školy" required autocomplete="off">
        <datalist id="schoollist">
        	<option value="Prvně vyberte čtvrť"></option>
        </datalist>
		<button type="submit" class="btn btn-outline-primary d-flex mx-auto mt-4 rounded">Potvrdit</button>
	</form>
	<div class="container d-flex mt-4">
		<a class="mx-auto font-weight-bold text-primary" data-toggle="modal" data-target="#modalAddNewSchool">Vaše škola není v seznamu?</a>
	</div>
	<div class="modal fade" id="modalAddNewSchool" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h4 class="modal-title w-100 font-weight-bold">Přidat novou školu</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body mx-3">
					<p>Pokud nemůžete najít svou školu v naší databázi, stačí požádat o její přidání. A to pouze vyplněním tohoto krátkého formuláře níže.</p>
					<form action="school_add_create" method="POST" class="my-3">
						<label for="city_add" class="mt-2">Vyberte existující, nebo zadejte nové město:</label>
						<input list="city_addlist" id="city_add" name="city_add" class="form-control" placeholder="Název města" required autocomplete="off">
        				<datalist id="city_addlist">

						</datalist>
						<label for="district_add" class="mt-2">Vyberte existující, nebo zadejte novou čtvrť:</label>
						<input list="district_addlist" id="district_add" name="district_add" class="form-control" placeholder="Název čtvrťě" required autocomplete="off">
						<datalist id="district_addlist">
							
						</datalist>
						<label for="school_add" class="mt-2" maxlength="40">Název školy:</label>
						<input type="text" name="school_add" id="school_add" class="form-control" placeholder="Např.: Gymnázium Poděbradská (maximálně 40 znaků)" required autocomplete="off" maxlength="40">
						<div class="d-flex justify-content-center">
							<button href="#" type="submit" class="btn blue-gradient mt-4">Podat žádost</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	page_end(true, 17);
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
  <script type="text/javascript" src="js/ajax-school-select.js"></script>
  <?php echo $js_modal_show; ?>
<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>