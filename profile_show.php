<?php 

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
	
	authorize_user();
	full_register();
	//display_messages();
	update_activity();

	if (isset($_GET['profile_id'])) {
		if ($_GET['profile_id'] == 0) {
			handle_error("Získáno špatné id uživatele.", "profile_show (0 id - System)");
		}
		$profile_id = $_GET['profile_id'];

		$mysqli_user_select = sprintf("SELECT username, email, balance, first_name, last_name, last_action, instagram, facebook, register_ip, last_ip, image_path, social_show, activated, register_date, gdpr_accepted FROM users WHERE user_id = '%s';", 
		mysqli_real_escape_string($connect, $profile_id));
		$user_school = sprintf("SELECT s.school_id, s.school_name FROM school s, users u WHERE u.user_id = '%d' AND s.school_id = u.school_id;",
		mysqli_real_escape_string($connect, $profile_id));
		$if_banned = sprintf("SELECT u.user_id FROM users u, banned_users bu WHERE u.user_id = '%d' AND u.user_id = bu.banned_id AND bu.ban_active = 1", 
		mysqli_real_escape_string($connect, $profile_id));
		$user_group = sprintf("SELECT ug.group_id, ug.event_date, ug.set_method, u2.user_id set_by_userid, u2.username set_by_username FROM users_groups ug, users u1, users u2 WHERE ug.user_id = u1.user_id AND u1.user_id = '%d' AND ug.set_by = u2.user_id;",
		mysqli_real_escape_string($connect, $profile_id));

		$result = mysqli_query($connect, $mysqli_user_select);
		$user_school_query = mysqli_query($connect, $user_school);
		$if_banned_query = mysqli_query($connect, $if_banned);
		$user_group_query = mysqli_query($connect, $user_group);

		if (mysqli_num_rows($result) == 0) {
			handle_error("Tento uživatel neexistuje!", "profile_show (wrong id)");
		}

		if ($result) {
			$row = mysqli_fetch_array($result);
			$username = $row['username'];
			$email = $row['email'];
			$balance = $row['balance'];
			$first_name = $row['first_name'];
			$last_name = $row['last_name'];
			$last_action = $row['last_action'];
			$instagram = $row['instagram'];
			$facebook = $row['facebook'];
			$register_date = date('d.m.Y H:i', strtotime($row['register_date']));
			$register_ip = $row['register_ip'];
			$last_ip = $row['last_ip'];
			$image_path = $row['image_path'];
			if ($row['last_action'] == '0000-00-00 00:00:00') {
				$last_action = '-';
			} else {
				$last_action = date('d.m.Y H:i', strtotime($row['last_action']));
			}
			if ($row['gdpr_accepted'] == 1) {
				$gdpr = '<i class="fas fa-check text-success"></i>';
			} else {
				$gdpr = '<i class="fas fa-times text-danger"></i>';
			}

			if ($row['activated'] == 0) {
				if (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
					$first_name = '<span class="text-danger" style="font-size:20px;">Není aktivován!</span><br>';
				} else {
					handle_error('Tento uživatel nebyl aktivován!', 'profile_show');
				}
			}

			if (!isset($_GET['page']) || ($_GET['page'] == "") || ($_GET['page'] == 0)) {
				$page = 1;
			} else {
				$page = $_GET['page'];
			}
			$items_per_page = 8;
			$offset = ($page-1)*$items_per_page;
			$total_pages_sql = sprintf("SELECT COUNT(*) FROM shop WHERE item_createdby_userid = '%d' AND visible = 1 AND checked = 1;",
			mysqli_real_escape_string($connect, $profile_id));
			$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
			$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
			$total_pages = ceil($total_rows/$items_per_page);
			$selling_items = sprintf("SELECT item_id, item_name, item_description, item_price, likes, dislikes, bought_times, item_type, item_subject, school_class FROM shop WHERE item_createdby_userid = '%d' AND visible = 1 AND checked = 1 ORDER BY confirmed_date DESC LIMIT %s, %s;",
			mysqli_real_escape_string($connect, $profile_id),
			$offset,
			$items_per_page);
			$selling_items_query = mysqli_query($connect, $selling_items);
		} else {
			handle_error("Došlo k chybě při komunikaci s databází, opravíme ji co nejdřív to půjde.", "Došlo k chybě při výběru informace o uživateli z databáze! (profile_show)");
		}

	} else {
		handle_error("Nebyly získány potřebné parametry", "profile_show");
	}

	page_start("Profil uživatele ".$username);
	
?>

<div class="row mx-1 my-5">
	<!-- Confirmed items modal -->
	<?php
		if (user_in_group("Main administrator", $_SESSION['user_id'])) {
	?>
	<div class='modal fade' id='confirmedItemsModal' tabindex='-1' role='dialog' aria-labelledby='confirmedItemsModalLabel' aria-hidden='true'>
		<div class='modal-dialog modal-center' role='document'>
			<div class='modal-content'>
				<div class='modal-header'>
				<h4 class='modal-title w-100' id='confirmedItemsModalLabel' style="font-family: 'Baloo', cursive;">Schválené testy</h4>
				<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
					<span aria-hidden='true'>&times;</span>
				</button>
				</div>
				<div class='modal-body'>
					<?php
						$confirmedItems = sprintf("SELECT COUNT(*), confirmed_date FROM shop WHERE confirmed_by = '%d' GROUP BY MONTH (confirmed_date);",
						mysqli_real_escape_string($connect, $profile_id));
						$confirmedItems_query = mysqli_query($connect, $confirmedItems);
						
						if (mysqli_num_rows($confirmedItems_query) == 0) {
							echo '<p class="font-weight-bold text-center">Tento uživatel zatím neschválil žádný test.</p>';
						} else {
							echo'<div class="table-responsive text-center">
							<table class="table table-hover table-striped table-sm mx-auto" style="width:200px;">
								<thead>
									<tr>
										<th scope="col"><a class="column_sort" data-order="desc" id="item_subject">Měsíc/rok</a></th>
										<th scope="col"><a class="column_sort" data-order="desc" id="school_class">Schváleno</a></th>
									</tr>
								</thead>
								<tbody>';
							while ($confirmedItems_row = mysqli_fetch_array($confirmedItems_query)) {
								printf("
								<tr class='h-100'>
									<td class='my-auto'>%s</td>
									<td class='my-auto'>%s</td>
								</tr>",
								date('m/y', strtotime($confirmedItems_row['confirmed_date'])),
								$confirmedItems_row['COUNT(*)']);
							}
							echo '</tbody></table></div>';
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
		}
	?>
	<div class="col-md-3">
		<div class="card rounded px-2 py-3 text-center cloudy-knoxville-gradient">
			<img src="<?php if (!empty($image_path)) { echo $image_path; } else { echo 'profile_pictures/default/default.png'; } ?>" alt="" class="img-fluid mx-auto my-3 rounded z-depth-1">
			<p style="font-family: 'Jura', sans-serif; font-size: 24px;" class="font-weight-bold"><?php echo $first_name." ".$last_name; ?></p>
			<?php
			if (mysqli_num_rows($if_banned_query) != 0) {
				echo "<h3 class='text-danger'>ZABLOKOVÁN</h3>";
			}
			if ($user_group_query) {
				$user_group_row = mysqli_fetch_array($user_group_query);
				if ($user_group_row['group_id'] == 1) {
					if (mysqli_num_rows($if_banned_query) == 0) {
						echo '<p><span class="text-success font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Administrátor</span></p>';
					} elseif (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo '<p><span class="text-success font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Administrátor</span></p>';
					}
					if (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo "<p><span class='profile-page-lightgrey'>Přidán: </span>".date('d.m.Y H:i', strtotime($user_group_row['event_date']))."</p>";
						echo "<p><span class='profile-page-lightgrey'>Přidal: </span><a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_group_row['set_by_userid']."'>".$user_group_row['set_by_username']."</a></p>";
						echo "<p><span class='profile-page-lightgrey'>Způsob přidání: </span>".$user_group_row['set_method']."</p>";
					}
				} elseif ($user_group_row['group_id'] == 2) {
					if (mysqli_num_rows($if_banned_query) == 0) {
						echo '<p><span class="text-secondary font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Validátor</span></p>';
					} elseif (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo '<p><span class="text-secondary font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Validátor</span></p>';
					}
					if (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo "<p><span class='profile-page-lightgrey'>Přidán: </span>".date('d.m.Y H:i', strtotime($user_group_row['event_date']))."</p>";
						echo "<p><span class='profile-page-lightgrey'>Přidal: </span><a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_group_row['set_by_userid']."'>".$user_group_row['set_by_username']."</a></p>";
						echo "<p><span class='profile-page-lightgrey'>Způsob přidání: </span>".$user_group_row['set_method']."</p>";
					}
				} elseif ($user_group_row['group_id'] == 3) {
					if (mysqli_num_rows($if_banned_query) == 0) {
						echo '<p><span class="text-warning font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Support</span></p>';
					} elseif (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo '<p><span class="text-warning font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Support</span></p>';
					}
					if (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo "<p><span class='profile-page-lightgrey'>Přidán: </span>".date('d.m.Y H:i', strtotime($user_group_row['event_date']))."</p>";
						echo "<p><span class='profile-page-lightgrey'>Přidal: </span><a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_group_row['set_by_userid']."'>".$user_group_row['set_by_username']."</a></p>";
						echo "<p><span class='profile-page-lightgrey'>Způsob přidání: </span>".$user_group_row['set_method']."</p>";
					}
				} elseif ($user_group_row['group_id'] == 4) {
					if (mysqli_num_rows($if_banned_query) == 0) {
						echo '<p><span class="deep-orange-text font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Hlavní administrátor</span></p>';
					} elseif (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo '<p><span class="deep-orange-text font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Hlavní administrátor</span></p>';
					}
					if (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo "<p><span class='profile-page-lightgrey'>Přidán: </span>".date('d.m.Y H:i', strtotime($user_group_row['event_date']))."</p>";
						echo "<p><span class='profile-page-lightgrey'>Přidal: </span><a class='text-primary font-weight-bold' href='profile_show?profile_id=".$user_group_row['set_by_userid']."'>".$user_group_row['set_by_username']."</a></p>";
						echo "<p><span class='profile-page-lightgrey'>Způsob přidání: </span>".$user_group_row['set_method']."</p>";
					}
				}
			}
			?>
			<hr class="mx-auto mt-0" style="width:100px;">
			<div class="row">
				<?php
					if ($row['social_show'] == 0) {
						echo '<div class="col-12">';
					} elseif (empty($instagram) && empty($facebook)) {
						echo '<div class="col-12">';
					} else {
						echo '<div class="col-6">';
					}
				?>
					<p><span class="profile-page-lightgrey">Uživatelské jméno:</span> <?php echo $username; ?></p>
					<?php
						if (mysqli_num_rows($user_school_query) != 0) {
							$user_school_row = mysqli_fetch_array($user_school_query);
							echo "<p><span class='profile-page-lightgrey'>Škola:</span> <a href='".SITE_ROOT."school_info?school_id=".$user_school_row['school_id']."'>".$user_school_row['school_name']."</a></p>";
						} else {
							echo "<p><span class='profile-page-lightgrey'>Škola:</span> Není nastavena</p>";
						}
					?>
				</div>
				<div class="col-6 my-auto">
					<?php
						if ($row['social_show'] == 1) {
							if (!empty($instagram)) {
								echo "<a href='https://www.instagram.com/".$instagram."'><img src='".SITE_ROOT."img/instagram.png' class='img-fluid mb-1 mx-2' style='width: 22px;'>Instagram</a><br>";
							} else {
								echo "";
							}
							if (!empty($facebook)) {
								echo "<a href='https://www.facebook.com/".$facebook."'><img src='".SITE_ROOT."img/facebook.png' class='img-fluid mb-1 mx-2' style='width: 22px;'>Facebook</a><br>";
							} else {
								echo "";
							}
						}
					?>
				</div>
			</div>			
				<?php
					if (user_in_group("Administrator", $_SESSION['user_id']) || user_in_group("Main administrator", $_SESSION['user_id'])) {
						echo "<p><u>Admin info:</u></p>";
						echo "<p><span class='profile-page-lightgrey'>Konto: </span>".$balance." Kč</p>";
						echo "<p><span class='profile-page-lightgrey'>GDPR: </span>".$gdpr."</p>";
						echo "<p><span class='profile-page-lightgrey'>E-mail: </span>".$email."</p>";
						echo "<p><span class='profile-page-lightgrey'>Naposled online: </span>".$last_action."</p>";
						echo "<p><span class='profile-page-lightgrey'>Datum registrace: </span>".$register_date."</p>";
						echo "<p><span class='profile-page-lightgrey'>Registrační IP: </span>".$register_ip."</p>";
						echo "<p><span class='profile-page-lightgrey'>Poslední IP: </span>".$last_ip."</p>";
						echo '<a data-toggle="modal" data-target="#confirmedItemsModal" href="#"><i class="fas fa-clipboard-check"></i> Schválené testy</a>';
					}
				?>
		</div>
	</div>
	<div class="col-md-9">
		<h4 class="text-center font-weight-bold mt-4 mt-md-0" style="font-family: 'Baloo', cursive;"><i class="fas fa-shopping-cart"></i> Testy <?php echo $username; ?></h4>
		<hr class="black mt-0 z-depth-1" style="width:100px;">
		<div id="user-items">
				
		</div>
	</div>
</div>
<?php
if ($user_group_row['group_id'] == 4 || $user_group_row['group_id'] == 3 || $user_group_row['group_id'] == 2 || $user_group_row['group_id'] == 1) {
	page_end(false);
} else {
	page_end(true);
}
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
  <?php
   if (isset($_SESSION['user_id'])) {
		echo '<script type="text/javascript" src="js/ajax-profile-show.js"></script>';
   }
   ?>
	<?php echo $_SESSION['js_modal_show']; ?>
</body>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>