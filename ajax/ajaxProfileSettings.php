<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user();

$user_id = $_SESSION['user_id'];
$select = sprintf("SELECT first_name, last_name, instagram, facebook, bank_number, ref_code, social_show, debug_mode FROM users WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));
$select_query = mysqli_query($connect, $select);
$select_row = mysqli_fetch_array($select_query);
if (user_in_group("Main administrator", $user_id)) {
	$admSettings_default = '<a class="text-center"><div id="admin" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Administrativní<br></div></a>';
	$admSettings_selected = '<a class="text-center"><div id="admin" style="background-image: linear-gradient(120deg,#fdfbfb 0,#f6d365 100%); font-family: \'Baloo\', cursive;">Administrativní<br></div></a>';
}

$config = "SELECT maintain_mode FROM config;";
$config_query = mysqli_query($connect, $config);
$config_row = mysqli_fetch_array($config_query);

if ($_POST['page'] == 'general') {
	$blackPoints = sprintf("SELECT COUNT(*) FROM black_points WHERE bp_userid = '%d' AND bp_active = 1;",
	mysqli_real_escape_string($connect, $user_id));
	$blackPoints_query = mysqli_query($connect, $blackPoints);
	$blackPoints = mysqli_fetch_row($blackPoints_query)[0];
	if ($blackPoints == 0) {
		$black_points = '';
	} else {
		$black_points = 'Trestné body: '.$blackPoints;
	}
	if ($select_row['social_show'] == 1) {
		$switch_socialShow = '
		<div class="custom-control custom-switch mt-3">
			<input type="checkbox" class="custom-control-input" id="social_show" checked>
			<label class="custom-control-label" for="social_show" id="social_show_label">Zobrazit sociální sítě na svém profilu pro ostatní uživatele.</label>
		</div>';
		if (isset($_POST['socialShowChange'])) {
			$socialShow_change = sprintf("UPDATE users SET social_show = 0 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $user_id));
			$socialShow_change_query = mysqli_query($connect, $socialShow_change);
			$switch_socialShow = '
			<div class="custom-control custom-switch mt-3">
				<input type="checkbox" class="custom-control-input" id="social_show">
				<label class="custom-control-label" for="social_show" id="social_show_label">Zobrazit sociální sítě na svém profilu pro ostatní uživatele.</label>
			</div>';
		}
	} else {
		$switch_socialShow = '
		<div class="custom-control custom-switch mt-3">
			<input type="checkbox" class="custom-control-input" id="social_show">
			<label class="custom-control-label" for="social_show" id="social_show_label">Zobrazit sociální sítě na svém profilu pro ostatní uživatele.</label>
		</div>';
		if (isset($_POST['socialShowChange'])) {
			$socialShow_change = sprintf("UPDATE users SET social_show = 1 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $user_id));
			$socialShow_change_query = mysqli_query($connect, $socialShow_change);
			$switch_socialShow = '
			<div class="custom-control custom-switch mt-3">
				<input type="checkbox" class="custom-control-input" id="social_show" checked>
				<label class="custom-control-label" for="social_show" id="social_show_label">Zobrazit sociální sítě na svém profilu pro ostatní uživatele.</label>
			</div>';
		}
	}
	echo '
      	<div class="row">
			<div class="col-md-4">
      			<a class="text-center"><div id="general" style="background-image: linear-gradient(120deg,#fdfbfb 0,#f6d365 100%); font-family: \'Baloo\', cursive;">Obecné<br><hr class="p-0 m-0"></div></a>
      			<a class="text-center"><div id="payment" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Platby<br><hr class="p-0 m-0"></div></a>
      			<a class="text-center"><div id="security" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Zabezpečení<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="codes" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Kódy<br><hr class="p-0 m-0"></div></a>
				'.$admSettings_default.'
      		</div>
			  <div class="col-md-8 mt-4">
			  	<div class="container">
					<form action="'.SITE_ROOT.'profile_settings_action.php?action=generalInfo" method="post" class="" enctype="multipart/form-data">
						<label for="edit_name">Křestní jméno</label>
						<input type="text" name="edit_name" id="edit_name" class="form-control mb-4" value="'.$select_row['first_name'].'" autocomplete="off" maxlength="15" required>
						<label for="edit_name">Příjmení</label>
						<input type="text" name="edit_lastname" id="edit_lastname" class="form-control mb-4" autocomplete="off" maxlength="20" value="'.$select_row['last_name'].'">
						<p class="mt-2 text-center" style="font-size:13px; color:#999999;">Profilovou fotku můžete změnit na profilové stránce</p>
						<div class="text-center">
							<button class="btn btn-success btn-sm" type="submit">Uložit</button>
						</div>
					</form>
					<form action="'.SITE_ROOT.'profile_settings_action.php?action=socialSet" method="post" class="mt-3 text-center">
						<u><p class="mb-1 text-center" style="font-family: \'Baloo\', cursive; font-size: 20px;">Sociální sítě</p></u>
						<div class="row">
							<div class="col-md-12">
								<a id="set-instagram" class="text-primary"><i class="fas fa-caret-down" id="instagram-arrow-down"></i><i class="fas fa-caret-up" id="instagram-arrow-up" style="display:none;"></i> Instagram <i class="fab fa-instagram"></i></a>
								<input type="text" name="edit_instagram" id="edit_instagram" class="form-control my-2 mx-auto" placeholder="Account name" autocomplete="off" maxlength="40" style="width:220px; display:none;" value="'.$select_row['instagram'].'">
							</div>
							<div class="col-md-12">
								<a id="set-facebook" class="text-primary"><i class="fas fa-caret-down" id="facebook-arrow-down"></i><i class="fas fa-caret-up" id="facebook-arrow-up" style="display:none;"></i> Facebook <i class="fab fa-facebook-f"></i></a>
								<input type="text" name="edit_facebook" id="edit_facebook" class="form-control my-2 mx-auto" placeholder="Enter your facebook username" autocomplete="off" maxlength="40" style="width:220px; display:none;" value="'.$select_row['facebook'].'">
							</div>
						</div>
						<p class="mt-2" style="font-size:13px; color:#999999;">Pro celkové odstranění svých sociálnich sítí z profilu, smažte obsah těchto polí</p>
						'.$switch_socialShow.'
						<div class="text-center">
							<button class="btn btn-primary btn-sm" type="submit">Uložit</button>
						</div>
					</form>
					<p class="text-center text-danger" data-toggle="tooltip" title="Po dosažení třech trestných bodů, bude Váš účet automaticky zablokován">'.$black_points.'</p>
					<div class="d-flex mt-2">
						<a class="mx-auto text-primary" href="'.SITE_ROOT.'school_change.php"><u>Změna školy</u></a>
					</div>
					<div class="d-flex mt-1">
						<a class="mx-auto text-primary" href="'.SITE_ROOT.'tutorial_action.php?action=enable&page=all"><u>Zapnout veškerý tutoriál</u></a>
					</div>
				</div>
      		</div>
      	</div>';
} elseif ($_POST['page'] == 'payment') {
	if ($select_row['bank_number'] == 0) {
		$select_row['bank_number'] = '';
	}
	echo '
      	<div class="row">
      		<div class="col-md-4">
				<a class="text-center"><div id="general" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Obecné<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="payment" style="background-image: linear-gradient(120deg,#fdfbfb 0,#f6d365 100%); font-family: \'Baloo\', cursive;">Platby<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="security" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Zabezpečení<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="codes" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Kódy<br><hr class="p-0 m-0"></div></a>
				  '.$admSettings_default.'
      		</div>
			  <div class="col-md-8 mt-4">
			  	<div class="container">
	      			<form action="'.SITE_ROOT.'profile_settings_action.php?action=paymentSet" method="post" class="">
	      					<label for="bank_number">Bankovní účet <span class="font-weight-bold" data-toggle="tooltip" title="Zadejte číslo bankovnho účtu (s kódem banky) s CZK měnou. Pozorně si zkontrolujte přidaný bankovní účet! Po výběru peněz, na reklamace typu špatně zadého čísla bankovního účtu nebude brán ohled.">?</span></label>
							<input type="text" name="bank_number" id="bank_number" class="form-control mb-4" autocomplete="off" maxlength="15" required placeholder="Číslo bankovního účtu (např.: 1234567890/2700)" value="'.$select_row['bank_number'].'" pattern="[0-9]{10}+/+[0-9]{4}">
							<div class="text-center">
								<button class="btn btn-success btn-sm" type="submit">Uložit</button>
							</div>
					</form>
				</div>
      		</div>
      	</div>';
} elseif ($_POST['page'] == 'security') {
	echo '
      	<div class="row">
      		<div class="col-md-4">
				<a class="text-center"><div id="general" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Obecné<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="payment" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Platby<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="security" style="background-image: linear-gradient(120deg,#fdfbfb 0,#f6d365 100%); font-family: \'Baloo\', cursive;">Zabezpečení<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="codes" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Kódy<br><hr class="p-0 m-0"></div></a>
				  '.$admSettings_default.'
      		</div>
			  <div class="col-md-8 mt-4">
			  	<div class="container">
					<form action="'.SITE_ROOT.'profile_settings_action.php?action=pwdChange" method="post" class="">
						<label for="new_password">Nové heslo</label>
						<input type="password" name="new_password" id="new_password" class="form-control mb-4" autocomplete="off" maxlength="20" required placeholder="Zadejte nové heslo (nejméně 6 znaků)" pattern=".{6,}">
						<label for="repeat_password">Zopakujte nové heslo</label>
						<input type="password" name="repeat_password" id="repeat_password" class="form-control mb-4" autocomplete="off" maxlength="20" required placeholder="Zopakujte nové heslo">
						<div class="text-center">
							<button class="btn btn-success btn-sm" type="submit">Potvrdit</button>
						</div>
					</form>
				</div>
      		</div>
      	</div>';
} elseif ($_POST['page'] == 'codes') {
	if (is_null($select_row['ref_code'])) {
		$select_row['ref_code'] = '-';
		$FbShare_button = '';
	} else {
		$FbShare_button = '<div class="fb-share-button" data-href="https://testy-pro-stredni.cz/index?ref='.$select_row['ref_code'].'" data-layout="button" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Ftesty-pro-stredni.cz%2Findex%3Fref%3D'.$select_row['ref_code'].'&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Sdílet</a></div>';
	}
	echo '
      	<div class="row">
      		<div class="col-md-4">
				<a class="text-center"><div id="general" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Obecné<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="payment" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Platby<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="security" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Zabezpečení<br><hr class="p-0 m-0"></div></a>
				<a class="text-center"><div id="codes" style="background-image: linear-gradient(120deg,#fdfbfb 0,#f6d365 100%); font-family: \'Baloo\', cursive;">Kódy<br><hr class="p-0 m-0"></div></a>
				  '.$admSettings_default.'
      		</div>
			<div class="col-md-8 mt-4">
			  	<div class="container">
      				<form action="'.SITE_ROOT.'profile_settings_action.php?action=refferalCode" method="post" class="">
						<p class="mb-0"><span style="color: #999999;">Stávající referální kód:</span> '.$select_row['ref_code'].''.$FbShare_button.'</p>
						<label for="refferal_code">Nový referální kód</label>
						<input id="refferal_code" name="refferal_code" class="form-control mb-4" placeholder="Referální kód: (6 až 10 znaků)" required autocomplete="off" pattern=".{6,}" maxlength="10">
						<div class="text-center">
							<button class="btn btn-outline-primary btn-sm" type="submit">Uložit</button>
						</div>
					</form>
					<form action="'.SITE_ROOT.'user_codes_action.php?action=activateCode" method="post" class="">
						<label for="code">Bonusový kód</label>
						<input id="code" name="code" class="form-control mb-4" placeholder="Zadejte bonusový kód:" required autocomplete="off">
						<div class="text-center">
							<button class="btn btn-success btn-sm" type="submit">Uplatnit</button>
						</div>
					</form>
				</div>
      		</div>
      	</div>';
} elseif ($_POST['page'] == 'admin') {
	if ($select_row['debug_mode'] == 1) {
		$switch_debugMode = '
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="debug_mode" checked>
			<label class="custom-control-label" for="debug_mode" id="debug_mode_label">Debug mode</label>
		</div>';
		if (isset($_POST['debugModeChange'])) {
			$debugMode_change = sprintf("UPDATE users SET debug_mode = 0 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $user_id));
			$debugMode_change_query = mysqli_query($connect, $debugMode_change);
			$switch_debugMode = '
			<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input" id="debug_mode">
				<label class="custom-control-label" for="debug_mode" id="debug_mode_label">Debug mode</label>
			</div>';
		}
	} else {
		$switch_debugMode = '
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="debug_mode">
			<label class="custom-control-label" for="debug_mode" id="debug_mode_label">Debug mode</label>
		</div>';
		if (isset($_POST['debugModeChange'])) {
			$debugMode_change = sprintf("UPDATE users SET debug_mode = 1 WHERE user_id = '%d';",
			mysqli_real_escape_string($connect, $user_id));
			$debugMode_change_query = mysqli_query($connect, $debugMode_change);
			$switch_debugMode = '
			<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input" id="debug_mode" checked>
				<label class="custom-control-label" for="debug_mode" id="debug_mode_label">Debug mode</label>
			</div>';
		}
	}
	if ($config_row['maintain_mode'] == 1) {
		$switch_maintainMode = '
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="maintain_mode" checked>
			<label class="custom-control-label" for="maintain_mode" id="maintain_mode_label">Maintain mode</label>
		</div>';
		if (isset($_POST['maintainModeChange'])) {
			$maintainMode_change = "UPDATE config SET maintain_mode = 0";
			$maintainMode_change_query = mysqli_query($connect, $maintainMode_change);
			$switch_maintainMode = '
			<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input" id="maintain_mode">
				<label class="custom-control-label" for="maintain_mode" id="maintain_mode_label">Maintain mode</label>
			</div>';
		}
	} else {
		$switch_maintainMode = '
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="maintain_mode">
			<label class="custom-control-label" for="maintain_mode" id="maintain_mode_label">Maintain mode</label>
		</div>';
		if (isset($_POST['maintainModeChange'])) {
			$maintainMode_change = "UPDATE config SET maintain_mode = 1";
			$maintainMode_change_query = mysqli_query($connect, $maintainMode_change);
			$switch_maintainMode = '
			<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input" id="maintain_mode" checked>
				<label class="custom-control-label" for="maintain_mode" id="maintain_mode_label">Maintain mode</label>
			</div>';
		}
	}
	echo '
      	<div class="row">
      		<div class="col-md-4">
			  <a class="text-center"><div id="general" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Obecné<br><hr class="p-0 m-0"></div></a>
			  <a class="text-center"><div id="payment" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Platby<br><hr class="p-0 m-0"></div></a>
			  <a class="text-center"><div id="security" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Zabezpečení<br><hr class="p-0 m-0"></div></a>
			  <a class="text-center"><div id="codes" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Kódy<br><hr class="p-0 m-0"></div></a>
				  '.$admSettings_selected.'
      		</div>
      		<div class="col-md-8 mt-4">
			  '.$switch_debugMode.'
			  !!! Add maintain mode switcher only for main admins !!!
			  '.$switch_maintainMode.'
      		</div>
      	</div>';
}
?>