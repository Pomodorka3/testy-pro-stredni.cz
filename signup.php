<?php 

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

session_start();

function generateRndString($code_length = 10){
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $code_length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

if (!isset($_SESSION['user_id'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$password_noCrypt = trim($_POST['password']);
	$password = crypt(trim($_POST['password']), $username);
	$repeat_password = crypt(trim($_POST['repeat_password']), $username);
	$name = htmlspecialchars(trim($_POST['name']));
	$email = trim($_POST['email']);
	$hash = md5(rand(0,1000));
	$register_date = date('Y-m-d H:i:s');
	if (isset($_POST['referred_by']) && !empty($_POST['referred_by'])) {
		$referred_by = trim($_POST['referred_by']);
	} else {
		$referred_by = trim($_POST['referred_hidden']);
	}
	if ($_POST['gdpr'] == 1) {
		$gdpr = 1;
	} else {
		$gdpr = 0;
	}

	$_POST['username']=$username;
	$_POST['email']=$email;
	$_POST['referral']=$referred_by;

	if ((isset($_POST['username'])) && (strlen($_POST['username'])>0) ){
			//Проверка пользователя с таким же именем
			$user_exists_test = sprintf("SELECT username FROM users WHERE username = '%s'", mysqli_real_escape_string($connect, $username));
			$user_exists_test_query = mysqli_query($connect, $user_exists_test);
			$email_exists_test = sprintf("SELECT email FROM users WHERE email = '%s'", mysqli_real_escape_string($connect, $email));
			$email_exists_test_query = mysqli_query($connect, $email_exists_test);
			/*@is_uploaded_file($image['tmp_name'])
			or handle_error("Name of this file doesn't meet requirements. Try to rename this file!");
			@getimagesize($image['tmp_name'])
			or handle_error("You have to choose a picture file!");*/
			if (mysqli_num_rows($user_exists_test_query) == 0) {
				if (mysqli_num_rows($email_exists_test_query) == 0) {
					if (strcmp($password, $repeat_password) == 0) {
						//Игнорировать реферальный код, если он не введен
						if (isset($referred_by) && !empty($referred_by)) {
							//Проверка на существование реферала
							$ref_check = sprintf("SELECT user_id FROM users WHERE ref_code = '%s';",
							mysqli_real_escape_string($connect, $referred_by));
							$ref_check_query = mysqli_query($connect, $ref_check);
							if (mysqli_num_rows($ref_check_query) == 0) {
								$_POST['referral'] = '';
								$_SESSION['error_message'] = "Tento referální kód neexistuje!";
								$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
								header("Location: ". SITE_ROOT ."signup");
								exit();
							}
						}
						$referral_code = generateRndString();
						$mysqli_insert = sprintf("INSERT INTO users (username, password, email, register_ip, hash, register_date, gdpr_accepted, ref_code) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')",
						mysqli_real_escape_string($connect, $username),
						mysqli_real_escape_string($connect, $password),
						mysqli_real_escape_string($connect, $email),
						mysqli_real_escape_string($connect, get_user_ip()),
						mysqli_real_escape_string($connect, $hash),
						mysqli_real_escape_string($connect, $register_date),
						mysqli_real_escape_string($connect, $gdpr),
						mysqli_real_escape_string($connect, $referral_code));
						$mysqli_insert_query = mysqli_query($connect, $mysqli_insert);
						if ($mysqli_insert_query){
							$inserted_id = mysqli_insert_id($connect);

							$tutorial_create = sprintf("INSERT INTO tutorial (user_id) VALUES (%d);",
							mysqli_real_escape_string($connect, $inserted_id));
							$tutorial_create_query = mysqli_query($connect, $tutorial_create);
							if (isset($referred_by) && !empty($referred_by)) {

								//Add vip status as a bonus
								$status_expiration = date('Y-m-d H:i:s', strtotime('+ '.SIGNUP_VIP_DAYS.' days'));
								$status_expiration_formatted = date('d.m.Y H:i', strtotime('+ '.SIGNUP_VIP_DAYS.' days'));
								$add_status = sprintf("INSERT INTO statuses (status_userid, status , status_expiration) VALUES ('%d', '%s', '%s');",
								mysqli_real_escape_string($connect, $inserted_id),
								mysqli_real_escape_string($connect, 'vip'),
								mysqli_real_escape_string($connect, $status_expiration));
								$add_status_query = mysqli_query($connect, $add_status);

								//Update user's sell multiplier
								$sell_multiplier = sprintf("UPDATE users SET sell_multiplier = 0.8 WHERE user_id = '%d';",
								mysqli_real_escape_string($connect, $inserted_id));
								$sell_multiplier_query = mysqli_query($connect, $sell_multiplier);

								//Create notification
								$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
								mysqli_real_escape_string($connect, 0),
								mysqli_real_escape_string($connect, $inserted_id),
								mysqli_real_escape_string($connect, "Jako dárek za registaci s referálním kódem dostáváte vip status na ".SIGNUP_VIP_DAYS." dní zdarma! Datum expirace: <span class='font-weight-bold'>".$status_expiration_formatted."</span>"),
								mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
								$message_send_query = mysqli_query($connect, $message_send);

								//Add new record to referrals table
								$ref_check_row = mysqli_fetch_row($ref_check_query);
								$add_referral = sprintf("INSERT INTO referrals (referrals_userid, referrals_userby) VALUES ('%d', '%d');",
								mysqli_real_escape_string($connect, $inserted_id),
								mysqli_real_escape_string($connect, $ref_check_row[0]));
								$add_referral_query = mysqli_query($connect, $add_referral);
							}
							//Create welcome notification
							$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
							mysqli_real_escape_string($connect, 0),
							mysqli_real_escape_string($connect, $inserted_id),
							mysqli_real_escape_string($connect, "<span class='font-weight-bold'>Vítejte!</span> Nezapomeňte pozvat i své přátele a spolužáky. Můžete získávat až do <u>5% z nákupů</u> pozvaných lidí. Váš zvací kód: <span class='font-weight-bold'>".$referral_code."</span>. Sdílet se zvacím kódem: <div class='fb-share-button px-2 py-1' data-href='https://testy-pro-stredni.cz/index?ref=".$referral_code."' data-layout='button' data-size='large'><a target='_blank' href='https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Ftesty-pro-stredni.cz%2Findex%3Fref%3D".$referral_code."&amp;src=sdkpreparse' class='fb-xfbml-parse-ignore'>Sdílet</a></div>"),
							mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
							$message_send_query = mysqli_query($connect, $message_send);
							//mysqli_real_escape_string($connect, "<span class='font-weight-bold'>Vítejte!</span> Nezapomeňte pozvat i své přátele a spolužáky. Můžete za ně taky získávat peníze pokud si nastavíte referální kód a využijete sdílení v <a href='profile_settings?settingsPage=codes'><span class='text-warning'>Nastavení > Kódy</span></a>"),

							//Create hint notification
							$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
							mysqli_real_escape_string($connect, 0),
							mysqli_real_escape_string($connect, $inserted_id),
							mysqli_real_escape_string($connect, "<span class='font-weight-bold'>TIP:</span> Pokud ve Vaší škole ještě nejsou žádné testy. Přidejte jich co nejvíc, jelikož budete první, kdo na tom vydělá."),
							mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
							$message_send_query = mysqli_query($connect, $message_send);

							//Send mail with activation link
							mail($email, 'Aktivace účtu', "Děkujeme Vám za registraci v našem systému. Zde je odkaz pro aktivaci uživatele: http://testy-pro-stredni.cz/validate?hash=$hash&email=$email\r\nRegistrační údaje:\nUživatelské jméno: $username\nHeslo: $password_noCrypt\n\r\n\rS pozdravem tým Testy-pro-střední");
							$_SESSION['success_message'] = "Teď už jen zbývá aktivovat účet pomocí odkazu, který jsme Vám odeslali na $email! <span class='font-weight-bold'>Pokud se Vám zpráva neobjeví v příchozích mailech, zkuste zkontrolovat složku SPAM!</span>";
							$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
							header("Location: ". SITE_ROOT ."signin");
							exit();
						} else {
							handle_error("Došlo k chybě v databázi!", mysqli_error($connect)."signup");
						}
					} else {
						$_SESSION['error_message'] = "Hesla se neshodují!";
						$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
					}
				} else {
						$_POST['email'] = '';
						$_SESSION['error_message'] = "Uživatel s tímto emailem už existuje!";
						$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
				}
			} else {
				/*error_message("User with this username already exists. Try to choose other username.");
				$js_modal_show = "<script>$('#errorModal').modal('show')</script>";*/
				$_POST['username'] = '';
				$_SESSION['error_message'] = "Toto uživatelské jméno je již obsazené, zkuste prosím jiné!";
				$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			}
		}
} else {
	$_SESSION['error_message'] = "Už jste přihlášen!";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."profile");
	exit();
}

page_start("Registrace");
display_messages();
cookies_modal();
?>
<div class="container h-100">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="text-center border border-light px-4 py-5 mx-auto my-5 rounded cloudy-knoxville-gradient z-depth-1" id="signup-window">
		<h4 class="mb-4" style="font-family: 'Baloo', cursive;">Registrace</h4>
		<div class="md-form mb-0">
			<input type="text" name="username" id="username" class="form-control mb-4"  autocomplete="off" minlength="5" maxlength="20" required value="<?php if (isset($_POST['username'])){ echo $_POST['username']; }?>">
			<label for="username"><i class="fas fa-user-alt"></i> Uživatelské jméno*</label>
		</div>
		<div class="md-form mb-0">
			<input type="email" name="email" id="email" class="form-control mb-4" required autocomplete="off" maxlength="40" value="<?php if (isset($_POST['email'])){ echo $_POST['email']; }?>">
			<label for="email"><i class="fas fa-at"></i> E-mail*</label>
		</div>
		<div class="md-form mb-0">
			<input type="password" name="password" id="password" class="form-control mb-4" required autocomplete="off" pattern=".{6,}" title="Minimum 6 symbolů." maxlength="20">
			<label for="password"><i class="fas fa-key"></i> Heslo*</label>
		</div>
		<div class="md-form mb-0">
			<input type="password" name="repeat_password" id="repeat_password" class="form-control mb-4" required autocomplete="off" maxlength="20">
			<label for="repeat_password"><i class="fas fa-key"></i> Zopakujte heslo*</label>
		</div>
		<div class="md-form mb-0">
			<input type="text" name="referred_by" id="referred_by" class="form-control mb-4" maxlength="10" autocomplete="off" value="<?php if (isset($_POST['referral'])){ echo $_POST['referral']; }?>">
			<label for="referred_by"><i class="fas fa-user-friends"></i> Referální kód</label>
		</div>
		<input type="hidden" name="referred_hidden" value="<?php if (isset($_GET['ref'])) {echo $_GET['ref'];} ?>">
		<div class="d-flex justify-content-around">
			<div>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="siteRules" name="siteRules">
					<label for="siteRules" class="custom-control-label">Souhlasím s <a data-toggle="modal" data-target="#rulesModal" class="font-weight-bold text-primary"><u>pravidly našeho systému</u></a>*</label>
				</div>
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="gdpr" name="gdpr" value="1">
					<label for="gdpr" class="custom-control-label">Souhlasím se <a href="agreement.html#gdpr" target="_blank" class="font-weight-bold text-primary"><u>zpracováním osobních údajů</u></a>*</label>
				</div>
			</div>
		</div>
		<p>*povinná pole</p>
		<button class="btn btn-info btn-block my-4" type="submit"><i class="fas fa-paper-plane mr-2"></i>Zaregistrovat se</button>
		<p>Už máte účet?
			<a href="signin">Přihlásit se</a>
		</p>
	</form>
	<div class='modal fade' id='rulesModal' tabindex='-1' role='dialog' aria-labelledby='rulesModalLabel' aria-hidden='true'>
		<div class='modal-dialog modal-center' role='document'>
			<div class='modal-content'>
				<div class='modal-header'>
				<h4 class="modal-title w-100" id="rulesModalLabel" style="font-family: 'Baloo', cursive;">Podmínky využívání našeho systému</h4>
					<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
						<span aria-hidden='true'>&times;</span>
					</button>
				</div>
				<div class='modal-body'>
					<p>
						<span class="font-weight-bold">1.</span> Je zakázano vybírat urážející/sprostá uživatelská a křestní jména a příjmení.
						<br><span class="font-weight-bold">1.2</span> Je zakázano nastavovat urážející nebo zesměšňujíci profilové obrázky.
						<br><span class="font-weight-bold">1.3</span> Je zakázano urážející jednání vůči ostatním uživatelům.						
					</p>
					<p>
						<span class="font-weight-bold">2.</span> Veškeré údaje budou bezpečně uloženy v naší databázi v souladu s GDPR.
						<br><span class="font-weight-bold">2.1</span> Osobní údaje nikdy nebudou zprostředkovány třetím osobám bez svolení osoby, které tyto údaje patří.
					</p>
					<p>
						<span class="font-weight-bold">3.</span> Je zakázano registrovat více než 1 účet na 1 osobu. Administrátoři si vyhrazují právo odstranit všechny vedlejší účty.
						<br><span class="font-weight-bold">3.1</span> Je přísný zákaz prodeje účtů. Pokud k tomu dojde, potom mají Administrátoři právo daný účet odstranit.
					</p>
					<p>
						<span class="font-weight-bold">4.</span> Administrátory mají vyhrazené právo navždy zablokovat nebo odstranit uživatele, pokud: 
						<br><span class="font-weight-bold">=</span> dotyčný porušil výše uvedené podmínky využívání našeho systému.
						<br><span class="font-weight-bold">-</span> se dotyčný dopustil, nebo pokoušel jakéhokoli podvodu systému nebo jiného uživatele.
						<br><span class="font-weight-bold">-</span> dotyčný nenahlásil závažnou chybu v systému, nebo chybu díky které docházelo k podvodům.
						<br><span class="font-weight-bold">-</span> dotyčný neodpovídá na zprávy od Administrátorů.
					</p>
					<p>
						<span class="font-weight-bold">5.</span> Veškeré ceny jsou uvedeny v CZK a s DPH
					</p>
				</div>
				<div class='modal-footer d-flex'>
					<button type="button" class="btn btn-success btn-sm mx-auto" data-dismiss="modal">Přečteno</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	page_end(true, 5);
?>
</body>

<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.js"></script>
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/custom-script-noAjax.js"></script>
  <script type="text/javascript" src="js/password-strength-meter/dist/password.min.js"></script>
  <script>
	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();
	})
  </script>
  <script>
  	<?php
	if (!isset($_COOKIE['cookies_accepted'])) {
  	?>
		function cookiesModal_show(){
			$('#cookiesModal').modal({
				show: true,
				backdrop: true,
				focus: false
			});
		}
		setTimeout(cookiesModal_show, 3000);
	<?php
	}
	?>
  </script>
  <script>
	$('#password').password({
		shortPass: 'Heslo musí mít minimálně 6 symbolů',
		badPass: 'Slabé heslo; zkombinujte písmena a čísla',
		goodPass: 'Středně silné heslo; zkuste speciální znaky',
		strongPass: 'Silné heslo',
		containsUsername: 'Heslo obashuje uživatelské jméno',
		enterPass: 'Zadejte nové heslo',
		showPercent: false,
		showText: true, // shows the text tips
		animate: true, // whether or not to animate the progress bar on input blur/focus
		animateSpeed: 'fast', // the above animation speed
		username: $('#username'), // select the username field (selector or jQuery instance) for better password checks
		usernamePartialMatch: true, // whether to check for username partials
		minimumLength: 6 // minimum password length (below this threshold, the score is 0)
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