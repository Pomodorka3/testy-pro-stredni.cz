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
		if (REFERRAL_PROMO_END != '' && (strtotime(REFERRAL_PROMO_END) >= time())) {
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
							Na této stránce se zobrazují uživatelé, kteří se registrovali přes Váš odkaz, nebo při pregistraci zadali Váš referální kód a koupili minimálně 1 test v našem obchodě.
						</p>
						<p>
							Za každého pozvaného uživatele budete získávat určité procento z celkové částky všech jeho nákupů. Viz. tabulka níže<br>
						</p>
						<div class="table-responsive my-4 mx-auto text-center">
							<table class="table table-striped table-hover table-sm table-bordered">
								<thead>
									<tr>
										<th scope="col" style="font-family: \'Baloo\', cursive;" class="py-1 px-4">Pozvaní uživatelé</th>
										<th scope="col" style="font-family: \'Baloo\', cursive;" class="py-1 px-4">Zisk</th>
									</tr>
								</thead>
								<tbody>
									<tr class="h-100">
										<td class="my-auto p-0">< 100</td>
										<td class="my-auto p-0">3%</td>
									</tr>
									<tr class="h-100">
										<td class="my-auto p-0">101-150</td>
										<td class="my-auto p-0">4%</td>
									</tr>
									<tr class="h-100">
										<td class="my-auto p-0">> 151</td>
										<td class="my-auto p-0">5%</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p>
							Svůj referální kód můžete nastavit v <a href="profile_settings" class="font-weight-bold text-primary">Nastavení >> Kódy</a>
						</p>
						'.$referral_promo.'
					</div>
					<div class="modal-footer d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="tutorial_action?action=disable&page='.$page.'">Přečteno</a>
					</div>
				</div>
			</div>
		</div>';
	}
	
	$user_id = $_SESSION['user_id'];
	
	//Get user ref_multiplier
	$refMultiplier = sprintf("SELECT ref_multiplier FROM users WHERE user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$refMultiplier_query = mysqli_query($connect, $refMultiplier);
	$refMultiplier = mysqli_fetch_row($refMultiplier_query)[0];

	//Query to get total available withdraw
	$referrals = sprintf("SELECT r.referrals_money FROM referrals r, users u WHERE r.referrals_userby = '%d' AND r.referrals_userid = u.user_id AND u.bought_items > 0;",
	mysqli_real_escape_string($connect, $user_id));
	$referrals_query = mysqli_query($connect, $referrals);
	$available_withdraw = 0;

	while ($row = mysqli_fetch_array($referrals_query)) {
		$available_withdraw += $row['referrals_money'];
	}

?>

<div class="container">
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-user-friends"></i> Pozvaní uživatelé</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
	<p>Váš zisk: <span class="font-weight-bold"><?php echo $refMultiplier*100; ?>%</span></p>
	<div id="referral_list">

	</div>
	<?php
	if ($available_withdraw != 0) {
		echo "<h6 class='text-center'>K dispozici pro výběr: <span class='font-weight-bold mt-3 md-mt-0'>".$available_withdraw." Kč</span></h6>";
		echo "<div class='d-flex'><a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."referrals_withdraw_action'>Vybrat</a></div>";
	} else {
		echo "<h6 class='text-center mt-3 md-mt-0'>Zatím nemáte žádné peníze z pozvaných uživatelů.</h6>";
	}
	?>
	</div>
<?php
page_end(true, 25);
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
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/ajax-referrals.js"></script>
	<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>