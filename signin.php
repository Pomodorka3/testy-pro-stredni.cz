<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';

session_start();

//Check if session_hash from table corresponds with session_hash from $_COOKIE
$sessionHash = sprintf("SELECT session_hash FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $_SESSION['user_id']));
$sessionHash_query = mysqli_query($connect, $sessionHash);
$sessionHash_row = mysqli_fetch_row($sessionHash_query);

if ($sessionHash_row[0] != $_COOKIE['session_hash']) {
	setcookie('session_hash', $hash, 1, '/');
}

if (!isset($_COOKIE['session_hash'])) {
	//Advertisement
	if (ADVERTISEMENT_MODAL) {
		$advertisement_modal = '
		<div class="modal bounceIn fade" id="advertisementModal" tabindex="-1" role="dialog" aria-labelledby="advertisementModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-header">
					<h4 class="modal-title w-100 text-center" id="advertisementModalLabel" style="font-family: \'Baloo\', cursive;">Reklama</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						<span aria-hidden="true">&times;</span>
					</button>
					</div>
					<div class="modal-body">
						<img src="'.ADVERTISEMENT_PICTURE_PATH.'" class="img-fluid rounded">		
					</div>
				</div>
			</div>
		</div>';
	}
	if (isset($_POST['username'])) {
		$username = trim($_POST['username']);
		$password = crypt(trim($_POST['password']), $username);

		$if_exists = sprintf("SELECT user_id FROM users WHERE username = '%s';",
		mysqli_real_escape_string($connect, $username));
		$if_exists_query = mysqli_query($connect, $if_exists);
		if (mysqli_num_rows($if_exists_query) == 0) {
			$_SESSION['error_message'] = "Tento uživatel neexistuje.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: ". SITE_ROOT ."signin");
			exit();
		}

		$if_activated = sprintf("SELECT user_id FROM users WHERE activated = 1 AND username = '%s';",
		mysqli_real_escape_string($connect, $username));
		$if_activated_query = mysqli_query($connect, $if_activated);

		if (mysqli_num_rows($if_activated_query) != 0) {
			$mysqli_user_param = sprintf("SELECT user_id, username, email, balance, balance_fake, first_name, last_name, school_id, image_path FROM users WHERE username = '%s' AND password = '%s'", 
			mysqli_real_escape_string($connect, $username),
			mysqli_real_escape_string($connect, $password));
			$mysqli_user_param_result = mysqli_query($connect, $mysqli_user_param);
			if (mysqli_num_rows($mysqli_user_param_result) == 1) {
				$results = mysqli_fetch_array($mysqli_user_param_result);
				//---------- Show advertisement ------------
				$_SESSION['modal'] = $advertisement_modal;
				$_SESSION['js_modal_show'] = "<script>$('#advertisementModal').modal('show')</script>";
				//---------- Notifications counter ------------
				$count_notifications = sprintf("SELECT COUNT(*) FROM messages WHERE message_to = '%d' AND message_removed = 0;",
				mysqli_real_escape_string($connect, $results['user_id']));
				$count_notifications_query = mysqli_query($connect, $count_notifications);
				$count_notifications_row = mysqli_fetch_row($count_notifications_query);
				$_SESSION['notifications_count'] = $count_notifications_row[0];
				//---------- Get user group ------------
				$get_group = sprintf("SELECT user_id FROM users_groups WHERE user_id = '%d';",
				mysqli_real_escape_string($connect, $results['user_id']));
				$get_group_query = mysqli_query($connect, $get_group);
				if (mysqli_num_rows($get_group_query) != 0) {
					//Don't show advertisement if user is in one of the groups
					$_SESSION['modal'] = '';
					$_SESSION['js_modal_show'] = '';
				}
				//---------- Get user status ------------
				$get_status = sprintf("SELECT status_id, status, status_expiration FROM statuses WHERE status_userid = '%d';",
				mysqli_real_escape_string($connect, $results['user_id']));
				$get_status_query = mysqli_query($connect, $get_status);
				if (mysqli_num_rows($get_status_query) != 0) {
					$get_status_row = mysqli_fetch_array($get_status_query);
					if ($get_status_row['status_expiration'] < date('Y-m-d H:i:s')) {
						$remove_status = sprintf("DELETE FROM statuses WHERE status_id = '%d' AND status_userid = '%d';",
						mysqli_real_escape_string($connect, $get_status_row['status_id']),
						mysqli_real_escape_string($connect, $results['user_id']));
						$remove_status_query = mysqli_query($connect, $remove_status);
						$restore_multiplier = sprintf("UPDATE users SET sell_multiplier = 0.7 WHERE user_id = '%d';",
						mysqli_real_escape_string($connect, $results['user_id']));
						$restore_multiplier_query = mysqli_query($connect, $restore_multiplier);
						if ($remove_status_query && $restore_multiplier_query) {
							$message_send = sprintf("INSERT INTO messages(message_from, message_to, message_content, message_date) VALUES ('%d', '%d', '%s', '%s');",
							mysqli_real_escape_string($connect, 0),
							mysqli_real_escape_string($connect, $results['user_id']),
							mysqli_real_escape_string($connect, "Váš ".$get_status_row['status']." status vypršel!"),
							mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
							$message_send_query = mysqli_query($connect, $message_send);
							if ($message_send_query) {
								$_SESSION['notifications_count'] += 1;
							}
						}
					} elseif (($get_status_row['status_expiration'] > date('Y-m-d H:i:s')) && ($get_status_row['status'] == 'vip')) {
						$_SESSION['status'] = 'VIP';
						//Don't show advertisement if user has a VIP status
						$_SESSION['modal'] = '';
						$_SESSION['js_modal_show'] = '';
					}
				} else {
					$_SESSION['status'] = '';
				}
				if ($results['balance'] < 0) {
					$fix_balance = sprintf("UPDATE users SET balance = 0 WHERE username = '%s';",
						mysqli_real_escape_string($connect, $username));
					$fix_balance_query = mysqli_query($connect, $fix_balance);
					$_SESSION['balance'] = 0;
					$_SESSION['balance_real'] = 0;
				} else {
					$_SESSION['balance'] = $results['balance'];
					$_SESSION['balance_real'] = 0;
				}
				$_SESSION['balance_fake'] = $results['balance_fake'];
				$_SESSION['user_id'] = $results['user_id'];
				$_SESSION['username'] = $results['username'];
				$_SESSION['email'] = $results['email'];
				$_SESSION['last_name'] = $results['last_name'];
				$_SESSION['first_name'] = $results['first_name'];
				$_SESSION['school_id'] = $results['school_id'];
				$_SESSION['image_path'] = $results['image_path'];
				//Create cookie session and update session_hash in users table
				$hash = md5(rand(0,1000));
				$updateHash = sprintf("UPDATE users SET session_hash = '%s' WHERE user_id = '%d';",
				$hash,
				$results['user_id']);
				$updateHash_query = mysqli_query($connect, $updateHash);
				setcookie('session_hash', $hash, time()+SESSION_TIME*60, '/');
				//setcookie('cookies_accepted', $hash, time()+120*60, '/');	//Ask cookie confirmation each 120 minutes
				//Add event to users_log table
				$usersLog = sprintf("INSERT INTO users_log(ul_user_id, ul_date) VALUES ('%d', '%s');",
				$results['user_id'],
				date('Y-m-d H:i:s'));
				$usersLog_query = mysqli_query($connect, $usersLog);
				$_SESSION['check_referralMultiplier'] = true;
				// $_SESSION['logged_in'] = 'true';
				//-----------------------------
				header('Location:' . SITE_ROOT . 'profile');
				exit();
			} else {
				$_SESSION['error_message'] = "Chybně zadané uživatelské jméno nebo heslo.";
				$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			}
		} else {
			$_SESSION['error_message'] = "Tento uživatel není aktivován!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		}
	}
} else {
	header("Location: profile");
}

page_start("Přihlášení");
display_messages();
$_POST['username']='';
$_POST['email']='';
//signin_action.php
cookies_modal();
?>

<div class="container h-100">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="text-center border border-light px-4 py-5 mx-auto my-5 rounded cloudy-knoxville-gradient z-depth-1" id="signin-window">
		<h4 class="mb-4" style="font-family: 'Baloo', cursive;">Přihlášení</h4>
		<div class="md-form mb-0">
			<input type="text" name="username" id="username" class="form-control mb-4" maxlength="20">
			<label for="username"><i class="fas fa-user-alt"></i> Uživatelské jméno</label>
		</div>
		<div class="md-form mb-0">
			<input type="password" name="password" id="password" class="form-control mb-4" maxlength="20">
			<label for="password"><i class="fas fa-key"></i> Heslo</label>
		</div>
		<div class="d-flex justify-content-around">
			<div>
				<a href="forgotten_password">Zapomenuté heslo?</a>
			</div>
		</div>
		<button class="btn btn-info btn-block my-4" type="submit" value="submit"><i class="fas fa-paper-plane mr-2"></i>Přihlásit se</button>
		<p>Nejste členem?
			<a href="signup">Zaregistrovat se</a>
		</p>
	</form>
</div>

<?php
	page_end(true, 13);
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
  <script type="text/javascript" src="js/custom-script-noAjax.js"></script>
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
		setTimeout(cookiesModal_show, 1000);
	<?php
	}
	?>
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