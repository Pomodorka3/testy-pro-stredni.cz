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
	page_start("Profil");
	tutorial("profile");
	update_activity();
	last_ip();
	display_messages();
	blackPoints_check();

	//Check if users is redirected from signin.php
	if (isset($_SESSION['check_referralMultiplier'])) {
		referralMultiplier_check();
		unset($_SESSION['check_referralMultiplier']);
	}

	//Check if users just completed first_setup
	if (isset($_SESSION['first_setup'])) {
		unset($_SESSION['first_setup']);
	}

	//Modal window, generating if the 'profile' parameter in the table tutorial is set to 1
	function tutorialModal($page, $referral_code){
		if (REFERRAL_PROMO_END != '' && (strtotime(REFERRAL_PROMO_END) >= time())) {
			$referral_promo = '
			<hr style="width:100px;" class="black">
			<p class="text-warning font-weight-bold" style="font-size:17px;">
				Do '.REFERRAL_PROMO_END.' platí akce! Při pozvání 100 nových uživatel pomocí Vašeho kódu, dostanete 100 Kč na účet! 
			</p>';
		}
		/* echo '
		<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">Úvodní tutoriál</h4>
					</div>
					<div class="modal-body text-center">
						<p>
							Dobrý den, vítáme Vás na našem projektu <span class="orange-text" style="font-size: 20px; font-family: \'Baloo\', cursive;">'.SITE_NAME.'</span> beta. Tento systém byl vytvořen nejen pro bezpečný nákup a prodej testů, ale také i pro zlepšení známek těch, co se vůbec nechtějí učit.
						</p>
						<hr style="width:100px;" class="black">
						<p>
							Teď se nacházíte na stránce Vašeho <span class="font-weight-bold text-primary">profilu</span>, k němuž máte přístup pomocí pravé části horní lišty. <img src="img/system/btn_profile.png" class="img-fluid rounded" alt="">
							<br>Tlačítko <img src="img/system/btn_shop.png" class="img-fluid" alt=""> Vás nasměruje do obchodu, a pokud nemáte nastavenou školu, nasměruje Vás na výběr Vaší školy. 
							<br>Pomocí tlačítka <img src="img/system/btn_item_add.png" class="img-fluid" alt=""> můžete přidat test do našeho obchodu a tím začít vydělávat.
						</p>
						'.$referral_promo.'
						<hr style="width:100px;" class="black">
						<p class="h5" style="font-family: \'Lobster\', cursive;">
							Přejeme Vám pohodlné využívání našeho systému!
						</p>
					</div>
					<div class="modal-footer d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="tutorial_action?action=disable&page='.$page.'">Přečteno</a>
					</div>
				</div>
			</div>
		</div>'; */
		echo '
		<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">Úvodní tutoriál</h4>
					</div>
					<div class="modal-body text-center">
						<p>
							Dobrý den, vítáme Vás na našem projektu <span class="orange-text" style="font-size: 20px; font-family: \'Baloo\', cursive;">'.SITE_NAME.'</span>. Tento systém byl vytvořen nejen pro bezpečný nákup a prodej testů, ale také i pro zlepšení známek těch, co se vůbec nechtějí učit.
						</p>
						<hr style="width:100px;" class="black">
						<p>
							Nezapomeňte pozvat i své přátele a spolužáky. Můžete získávat až do <u class="font-weight-bold">5% z nákupů</u> pozvaných lidí. Váš zvací kód je: <span class="font-weight-bold">'.$referral_code.'</span><br>Sdílet pozvánku se zvacím kódem: <div class="fb-share-button px-2 py-1" data-href="https://testy-pro-stredni.cz/index?ref='.$referral_code.'" data-layout="button" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Ftesty-pro-stredni.cz%2Findex%3Fref%3D'.$referral_code.'&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Sdílet</a></div>
						</p>
						'.$referral_promo.'
						<hr style="width:100px;" class="black">
						<p class="h5" style="font-family: \'Lobster\', cursive;">
							Přejeme Vám pohodlné využívání našeho systému!
						</p>
					</div>
					<div class="modal-footer d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="tutorial_action?action=disable&page='.$page.'">Přečteno</a>
					</div>
				</div>
			</div>
		</div>';
	}

	$user_id = $_SESSION['user_id'];

	$mysqli_user_select = sprintf("SELECT username, password, email, balance, first_name, last_name, last_session, instagram, facebook FROM users WHERE user_id = '%s';", 
	mysqli_real_escape_string($connect, $user_id));
	$user_school = sprintf("SELECT s.school_id, s.school_name FROM school s, users u WHERE u.user_id = '%d' AND s.school_id = u.school_id;",
	mysqli_real_escape_string($connect, $user_id));
	$confirmed_items = sprintf("SELECT COUNT(*) FROM shop WHERE confirmed_by = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$user_group = sprintf("SELECT ug.group_id FROM users_groups ug, users u WHERE ug.user_id = u.user_id AND u.user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$confirmedMoney = sprintf("SELECT SUM(be.price), be.buy_time FROM shop s, buy_events be WHERE s.confirmed_by = '%d' AND s.item_id = be.item_id GROUP BY MONTH (be.buy_time);",
	mysqli_real_escape_string($connect, $user_id));
	$confirmedMoney_query = mysqli_query($connect, $confirmedMoney);
	$availableConfirmedMoney = sprintf("SELECT SUM(be.price) FROM shop s, buy_events be WHERE s.confirmed_by = '%d' AND s.item_id = be.item_id AND be.confirmedby_withdrawed = 0 AND MONTH(be.buy_time) = MONTH(CURRENT_DATE());",
	mysqli_real_escape_string($connect, $user_id));
	$availableConfirmedMoney_query = mysqli_query($connect, $availableConfirmedMoney);
	/* //---------- Notifications counter ------------
	$count_notifications = sprintf("SELECT COUNT(*) FROM messages WHERE message_to = '%d' AND message_removed = 0;",
	mysqli_real_escape_string($connect, $user_id));
	$count_notifications_query = mysqli_query($connect, $count_notifications);
	if ($count_notifications_query) {
		$count_notifications_row = mysqli_fetch_row($count_notifications_query);
		$_SESSION['notifications_count'] = $count_notifications_row[0];
	} */


	$result = mysqli_query($connect, $mysqli_user_select);
	$user_school_query = mysqli_query($connect, $user_school);
	$confirmed_items_query = mysqli_query($connect, $confirmed_items);
	$confirmed_items_row = mysqli_fetch_row($confirmed_items_query);
	$user_group_query = mysqli_query($connect, $user_group);

	if ($result) {
		$row = mysqli_fetch_array($result);
		$username = $row['username'];
		$password = $row['password'];
		$email = $row['email'];
		$balance = $row['balance'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		if ($row['last_session'] == '0000-00-00 00:00:00') {
			$last_session = '-';
		} else {
			$last_session = date('d.m.Y H:i', strtotime($row['last_session']));
		}
		$instagram = $row['instagram'];
		$facebook = $row['facebook'];
	} else {
		handle_error("Vyskytl se problém s databází, opravíme ho, jakmile to bude možné.", "Něco se pokazilo při zíkávání informace o uživateli z databáze!");
	}
?>
<div class="row mx-1 my-3 my-md-5">
	<div class="col-md-3">
		<div class='modal fade' id='change_image' tabindex='-1' role='dialog' aria-labelledby='change_image_label' aria-hidden='true'>
			<div class='modal-dialog modal-center' role='document'>
				<div class='modal-content'>
					<div class='modal-header'>
					<h4 class='modal-title w-100' id='change_image_label' style="font-family: 'Baloo', cursive;">Změnit profilový obrázek</h4>
					<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
						<span aria-hidden='true'>&times;</span>
					</button>
					</div>
					<div class='modal-body'>
						<label for="edit_profile_image">Vybrat nový profilový obrázek</label><br>
						<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
						<input type="file" class="mb-4" name="edit_profile_image" accept=".jpeg, .jpg, .png" id="new_profile_image">
						<div id="image_crop" style="display: none;">
							<div class="row">
								<div class="col-md-12 text-center">
									<div id="image_demo"></div>
								</div>
								<div class="col-md-4" style="padding-top:30px;"></div>
								<!--<button type="button" id="rotateLeft" data-rotate="-90">Left</button>
								<button type="button" id="rotateRight" data-rotate="90">Right</button>-->
							</div>
							<div class="modal-footer d-flex">
								<button class="btn btn-success btn-sm crop_image mx-auto">Oříznout a nahrát obrázek</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
			if (user_in_group("Validator", $user_id) || user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			echo "
			<div class='modal fade' id='validators_earn' tabindex='-1' role='dialog' aria-labelledby='validators_earn_label' aria-hidden='true'>
				<div class='modal-dialog modal-center' role='document'>
					<div class='modal-content'>
						<div class='modal-header'>
						<h4 class='modal-title w-100' id='validators_earn_label' style=\"font-family: 'Baloo', cursive;\">Zisky z kontrolování testů</h4>
						<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
							<span aria-hidden='true'>&times;</span>
						</button>
						</div>
						<div class='modal-body'>";
						if (mysqli_num_rows($confirmedMoney_query) != 0) {
							echo '<div class="table-responsive">
							<table class="table table-sm table-striped table-hover">
								<thead>
									<tr>
										<th scope="col" style="font-family: \'Baloo\', cursive;">Měsíc/Rok</th>
										<th scope="col" style="font-family: \'Baloo\', cursive;">Zisk</th>
									</tr>
								</thead>
							<tbody>';
							while ($row = mysqli_fetch_array($confirmedMoney_query)) {
								printf("
								<tr>
									<td>%s</td>
									<td>%.1f Kč</td>
								</tr>
								",
								date('m/Y', strtotime($row['buy_time'])),
								$row['SUM(be.price)']*VALIDATORS_MULTIPLIER
								);
							}
							$availableConfirmedMoney = mysqli_fetch_row($availableConfirmedMoney_query)[0] * VALIDATORS_MULTIPLIER;
							echo '</tbody></table></div>';
							echo '<p>Dostupno na výběr: <span class="font-weight-bold">'.$availableConfirmedMoney.' Kč</span></p>';
							echo '<p>Vybírat můžete pouze peníze získané za tento měsíc. Pokud je do konce měsíce nevyberete, už je nebudete moci vybrat.</p>';
							echo '
							<div class="text-center">
								<a href="withdraw_action?for=itemCheck" class="btn btn-sm btn-outline-success">Vybrat vše</a>
							</div>';
						} else {
							echo '<p class="text-center font-weight-bold">Zatím nemáte potvrzené žádné testy!</p>';
						}
						echo'
						</div>
					</div>
				</div>
			</div>';
			}
		?>
		<a href="#notifications" class="d-block d-md-none"><p class="text-default text-center"><u><i class="fas fa-angle-double-down"></i> Níže se nachází upozornění <i class="fas fa-angle-double-down"></i></u></p></a>
		<div class="card rounded px-2 py-3 text-center cloudy-knoxville-gradient">
			<!--<div class="view overlay zoom">-->
				<img id="profile_image" src="<?php if (!empty($_SESSION['image_path'])) { echo $_SESSION['image_path']; } else { echo 'profile_pictures/default/default.png'; } ?>" alt="" class="img-fluid mx-auto my-3 rounded z-depth-1">
				<!--<div class="mask flex-center rgba-black-light">
					<a href="#" id="change_picture" data-toggle='modal' data-target='#change_image'><i class="fas fa-pencil-alt"></i> Change profile picture</a>
				</div>
			</div>-->
			<p style="font-family: 'Jura', sans-serif; font-size: 24px;" class="font-weight-bold"><?php echo $first_name." ".$last_name; ?></p>
			<?php
			if ($user_group_query) {
				$user_group_row = mysqli_fetch_row($user_group_query);
				if ($user_group_row[0] == 1) {
					echo '<p><span class="text-success font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Administrátor</span></p>';
				} elseif ($user_group_row[0] == 2) {
					echo '<p><span class="text-secondary font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Validátor</span></p>';
				} elseif ($user_group_row[0] == 3) {
					echo '<p><span class="text-warning font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Support</span></p>';
				} elseif ($user_group_row[0] == 4) {
					echo '<p><span class="deep-orange-text font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Hlavní administrátor</span></p>';
				}
			}
			if (!empty($_SESSION['status'])) {
				echo "<p><span class='profile-page-lightgrey'>Status:</span> <span class='font-weight-bold text-warning'>".$_SESSION['status']."</span> <a class='font-weight-bold' data-toggle='modal' data-target='#statusInfoModal'><u>?</u></a></p>";
				if ($_SESSION['status'] == 'VIP') {
					echo $statusInfoModal = '
					<div class="modal fade" id="statusInfoModal" tabindex="-1" role="dialog" aria-labelledby="statusInfoModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
							<div class="modal-content">
								<div class="modal-header">
								<h4 class="modal-title w-100 text-center" id="statusInfoModalLabel" style="font-family: \'Baloo\', cursive;">VIP status</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
									<span aria-hidden="true">&times;</span>
								</button>
								</div>
								<div class="modal-body">
									<span class="font-weight-bold">Pokud máte platný VIP status, máte následující výhody:</span><br>
									- Zvětšený koeficient zisku z prodeje testů.<br>
									- Veškerá reklama je vypnuta.
									
									<br><br><small>~~~ Možnosti VIP statusu se stále rozšiřují ~~~</small>
								</div>
							</div>
						</div>
					</div>';
				} else {
					echo $_SESSION['status'];
				}
			}
			?>
			<div class="row">
				<?php
					if (empty($instagram) && empty($facebook)) {
						echo '<div class="col-12">';
					} else {
						echo '<div class="col-6">';
					}
				?>
					<p><span class='profile-page-lightgrey'>Uživatelské jméno:</span> <?php echo $username; ?></p>
					<p><span class='profile-page-lightgrey'>Konto:</span> <?php echo $balance; ?> Kč <a data-toggle='modal' data-target='#depositModal'><i class="fas fa-plus text-success" data-toggle="tooltip" title="Vklad"></i></a></p>
					<?php
						if (mysqli_num_rows($user_school_query) != 0) {
							$user_school_row = mysqli_fetch_array($user_school_query);
							echo "<p><span class='profile-page-lightgrey'>Škola:</span> <a href='".SITE_ROOT."school_info'>".$user_school_row['school_name']."</a></p>";
						} else {
							echo "<p><span class='profile-page-lightgrey'>Škola:</span> Není vybrána</p>";
						}
					?>
				</div>
				<div class="col-6 my-auto">
					<?php
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
					?>
				</div>
			</div>
			<p><span class='profile-page-lightgrey'>Poslední přihlášení:</span> <?php echo $last_session; ?></p>
			<?php
				if (user_in_group("Validator", $user_id) ||user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
					echo '<p><span class="profile-page-lightgrey">Schváleno testů: </span>'.$confirmed_items_row[0].'</p>';
				}
			?>
			<a href="#" id="change_picture" data-toggle='modal' data-target='#change_image'><i class="fas fa-pencil-alt"></i> Změnit profilový obrázek</a>
			<?php
				if (user_in_group("Validator", $user_id) || user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
					echo '<a href="#" data-toggle="modal" data-target="#validators_earn"><i class="fas fa-dollar-sign"></i> Validátorské odměny</a>';
				}
			?>
			<a href="profile_settings"><i class="fas fa-cog"></i> Nastavení</a>
		</div>
	</div>
	<div class="col-md-9">
		<h4 class="text-center font-weight-bold mt-4 mt-md-0" style="font-family: 'Baloo', cursive;"><i class="fas fa-bell"></i> Upozornění</h4>
		<hr class="black mt-0 z-depth-1" style="width:100px;">
		<div id="notifications">
			
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
  <script type="text/javascript" src="js/ajax-profile.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
  <script type="text/javascript" src="js/Croppie/croppie.js"></script>
  <script>  
$(document).ready(function(){

 $image_crop = $('#image_demo').croppie({
	enableExif: true,
	enableOrientation: false,
    viewport: {
      width:200,
      height:200,
	  type:'square' //circle
    },
    boundary:{
      width:300,
      height:300
	}
  });

  $('#new_profile_image').on('change', function(){
    var reader = new FileReader();
    reader.onload = function (event) {
      $image_crop.croppie('bind', {
		url: event.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
    }
	reader.readAsDataURL(this.files[0]);
	$("#image_crop").slideToggle("slow");
	//$('#change_image').modal('hide');
    //$('#uploadimageModal').modal('show');
  });

  $('.crop_image').click(function(event){
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(response){
      $.ajax({
        url:"ajax/ajaxProfilePicture",
        type: "POST",
        data:{"image": response},
        success:function(data)
        {
		  $('#change_image').modal('hide');
		  location.reload(true);
          //$('#uploaded_image').html(data);
        }
      });
    })
  });
  /*$( "#rotateLeft" ).click(function() {
      $image_crop.croppie('rotate', parseInt($(this).data('rotate')));
  });
  
  $( "#rotateRight" ).click(function() {
      $image_crop.croppie('rotate',parseInt($(this).data('rotate')));
  });*/

});  
</script>
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