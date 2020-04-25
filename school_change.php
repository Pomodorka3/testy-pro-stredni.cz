<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';

$city = "SELECT city_id, city_name FROM city ORDER BY city_name ASC LIMIT 6;";
$city_query = mysqli_query($connect, $city);
$city_query2 = mysqli_query($connect, $city);

if (isset($_POST['school'])) {
	$school_name = htmlspecialchars(trim($_POST['school']));

	$school_id = sprintf("SELECT school_id FROM school WHERE school_name = '%s';",
	mysqli_real_escape_string($connect, $school_name));
	$school_id_query = mysqli_query($connect, $school_id);
	$school_id_row = mysqli_fetch_row($school_id_query);

	if ($_SESSION['school_id'] == $school_id_row[0]) {
		$_SESSION['error_message'] = "Byla vybrána stejná škola!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."school_change");
		exit();
	}

	$last_setup = sprintf("SELECT school_setdate FROM users WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $_SESSION['user_id']));
	$last_setup_query = mysqli_query($connect, $last_setup);
	$last_setup_row = mysqli_fetch_array($last_setup_query);

	$request_change = sprintf("INSERT INTO school_change(change_school_id_from, change_school_id_to, user_id, request_date, last_setdate) VALUES ('%d', '%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, $_SESSION['school_id']),
	mysqli_real_escape_string($connect, $school_id_row[0]),
	mysqli_real_escape_string($connect, $_SESSION['user_id']),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
	mysqli_real_escape_string($connect, $last_setup_row['school_setdate']));
	$request_change_query = mysqli_query($connect, $request_change);

	if ($request_change_query) {
		$_SESSION['success_message'] = "Vaše žádost o změnu školy byla úspěšně odeslána Administrátorům!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header('Location: '. SITE_ROOT . 'profile');
		exit();
	} else {
		//handle_error("Došlo k chybě při změně školy. Je možné, že tato škola není v naší databázi. V tom případě můžete požádat o její přidání.", "school_change");
		$_SESSION['error_message'] = "Došlo k chybě při změně školy. Je možné, že tato škola není v naší databázi. V tom případě můžete požádat o její přidání";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header('Location: '. SITE_ROOT . 'school_change');
		exit();
	}
	
}

authorize_user();
full_register();
school_check();
page_start("Změna školy");
tutorial("school_change");
display_messages();
update_activity();

//Modal window, generating if the 'school_change' parameter in the table tutorial is set to 1
function tutorialModal($page){
	echo '
	<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">Tutoriál</h4>
				</div>
				<div class="modal-body text-center">
					<p>
						Na této stránce si můžete požádat o změnu školy.
					</p>
					<p>
						Prvně vyberte město, poté vyberte čtvrť. A pro vybranou čtvrť budou načteny příslušné školy. Můžete do pole \'Vyberte školu\' zadávat název své školy a systém bude s každým napsaným znakem hledat školy.
					</p>
					<p>
						Pokud v seznamu škol nenaleznete svou školu, tak zmáčkněte \'<span class="mx-auto font-weight-bold text-primary">Nemůžete najít svou školu?</span>\'. Dále vyberte město, čtvrť a napište plné jméno své školy.
					</p>
					<p>
						O schválení Vašeho návrhu o přidání nové školy, Vás informujeme pomocí upozornění.
					</p>
					<p class="text-danger font-weight-bold" style="font-size:18px;">
						O změnu lze žádat nejdříve po 14 dnech od posledního nastavení, nebo poslední změny školy.
					</p>
				</div>
				<div class="modal-footer d-flex">
					<a class="btn btn-success btn-sm mx-auto" href="tutorial_action?action=disable&page='.$page.'">Přečteno</a>
				</div>
			</div>
		</div>
	</div>';
}

//(DONE) Сделать <datalist> вместо <select>.
//(DONE) Ограничить видимую часть списка <droplist> на 6 предметов - LIMIT 6.

?>

<div class="container">
	<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;">Změna školy</h4>
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