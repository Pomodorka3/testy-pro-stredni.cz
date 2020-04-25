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
	page_start("Pozvaní uživatelé");
	tutorial("referrals");
	last_ip();
	display_messages();
	update_activity();

	//Modal window, generating if the 'referrals' parameter in the table tutorial is set to 1
	function tutorialModal($page){
		if (REFERRAL_PROMO_END != '' && (REFERRAL_PROMO_END >= date('d.m.Y'))) {
			$referral_promo = '
			<hr style="width:100px;" class="black">
			<p class="text-warning font-weight-bold" style="font-size:17px;">
				Do '.REFERRAL_PROMO_END.' platí akce! Při pozvání 100 nových uživatel pomocí Vašeho kódu, dostanete 100 Kč na účet!
			</p>';
		}
		echo '
		<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">Tutoriál</h4>
					</div>
					<div class="modal-body text-center">
						<p>
							Na této stránce se zobrazují uživatelé, kteří se registrovali přes Váš odkaz, nebo při pregistraci zadali Váš referální kód.
						</p>
						<p>
							Za každého pozvaného uživatele dostáváte 3% z celkové částky všech jeho nákupů.
							<br>
						</p>
						<p>
							Svůj referální kód můžete nastavit v <a href="profile_settings.php" class="font-weight-bold text-primary">Nastavení >> Kódy</a>
						</p>
						'.$referral_promo.'
					</div>
					<div class="modal-footer d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="tutorial_action.php?action=disable&page='.$page.'">Přečteno</a>
					</div>
				</div>
			</div>
		</div>';
	}
	
	$user_id = $_SESSION['user_id'];

	//Query to get total available withdraw
	$referrals = sprintf("SELECT ref_money_given FROM users WHERE ref_by = '%d' AND bought_items > 0;",
	mysqli_real_escape_string($connect, $user_id));
	$referrals_query = mysqli_query($connect, $referrals);
	$available_withdraw = 0;

	while ($row = mysqli_fetch_array($referrals_query)) {
		$available_withdraw += $row['ref_money_given'];
	}

?>

<div class="container">
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-user-friends"></i> Pozvaní uživatelé</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
	<div id="referral_list">

	</div>
	<?php
	if ($available_withdraw != 0) {
		echo "<h6 class='text-center'>K dispozici pro výběr: <span class='font-weight-bold mt-3 md-mt-0'>".$available_withdraw." Kč</span></h6>";
		echo "<div class='d-flex'><a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."referrals_withdraw_action.php'>Požádat o výběr</a></div>";
	} else {
		echo "<h6 class='text-center mt-3 md-mt-0'>Zatím nemáte žádné peníze z pozvaných uživatelů.</h6>";
	}
	?>
	</div>

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
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/ajax-referrals-1.js"></script>
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