<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

	session_start();
	authorize_user(array("Validator", "Support", "Administrator", "Main administrator"));
	full_register();
	page_start("Kontrola testů");
	tutorial("item_check");
	last_ip();
	display_messages();
	update_activity();

	//Modal window, generating if the 'item_check' parameter in the table tutorial is set to 1
	function tutorialModal($page){
		echo '
		<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">Tutoriál</h4>
					</div>
					<div class="modal-body text-center">
						<p class="h5" style="font-family: \'Lobster\', cursive;">
							Ještě jednou Vám gratulujeme k získání statusu \'<span class="text-secondary" style="font-family: \'Lobster\', cursive;">Validátor</span>\'!
						</p>
						<hr style="width:100px;" class="black">
						<p>
							Jako 1 z '.VALIDATORS_NEEDED.' <span class="text-secondary">Validátorů</span> ve Vaší škole máte povinnost kontrolovat přidávané testy do Vaší školy.
						</p>
						<p>
							A to tím způsobem, že si prohlédnete všechny přílohy <i class="fas fa-file-image text-primary" data-toggle="tooltip" title="Příloha"></i> k testu a pokud popis testu odpovídá přílohám, žádost <span class="text-success">schválíte</span> pomocí tlačítka <i class="fas fa-check text-success" data-toggle="tooltip" title="Potvrdit"></i>. V opačném případě žádost <span class="text-danger">odmítnete</span> pomocí tlačítka <i class="fas fa-times text-danger" data-toggle="tooltip" title="Odmítnout"></i>.
						</p>
						<p>
						<span class="text-secondary">Validátoři</span> mají také své výhody... Za každou kontrolu, dostávájí na konci kalendářního měsíce peněžní odměnu.
						<br>A to v přepočtu 0.3 Kč za každý zkontrolovaný test.
						</p>
						<hr style="width:100px;" class="black">
						<p class="h5" style="font-family: \'Lobster\', cursive;">
							Vzhůru do práce!
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

?>
	<div class="m-4">
	<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-cart-plus"></i> Kontrola přidaných testů</h4>
	<hr class="black mt-0 z-depth-1" style="width:100px;">
		<div id="item_check">

		</div>
	</div>
<?php
page_end(true, 32);
?>
</body>
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
  <script type="text/javascript" src="js/ajax-item-check.js"></script>
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