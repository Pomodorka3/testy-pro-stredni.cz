<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/ip_check.php';

session_start();
authorize_user();
full_register();
school_check();
page_start("Výběr z konta");
tutorial("withdraw");
last_ip();
display_messages();
//check_bank_number();
update_activity();

//Modal window, generating if the 'withdraw' parameter in the table tutorial is set to 1
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
						Teď se nacházíte na stránce <span class="font-weight-bold text-primary">výběrů</span>. Zde se můžete podívat na své výběry, nebo také požádat o nový výběr.
					</p>
					<p>
						Pro požádání o výběr musíte mít uvedený bankovní účet (včetně kódu banky) na který chcete uskutečnit výběr. Nastavit bankovní účet lze v <a href="profile_settings" class="font-weight-bold text-primary">Nastavení >> Platby</a><br>
						Výběr z konta a z pozvaných lidí se uskutečňuje zvlášť.
					</p>
					<p>
						<span class="font-weight-bold text-danger">! Žádat o výběr lze každých 14 dní !</span>
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

<div class="container">
	<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-cash-register"></i> Výběr</h4>
	<hr class="black mt-0 z-depth-1" style="width:100px;">
	<div id="withdraws_list">
	
	</div>
	<h6 class="text-center">Peníze dostupné pro výběr: <span class="font-weight-bold"><?php echo $_SESSION['balance_real']; ?> Kc</span></h6>
	<form id="withdraw_form" action="<?php echo SITE_ROOT."withdraw_action"; ?>" method="POST" class="text-center mx-auto">
		<input type="number" name="amount" id="withdraw-sum" min="<?php echo MIN_WITHDRAW_SUM; ?>" max="<?php echo $_SESSION['balance_real']; ?>" class="form-control mx-auto my-3" style="width: 170px;" required autocomplete="off" placeholder="Množství Kč" >
		<p class="text-center" id="withdraw-sum-count-p" style="display:none;" data-toggle="tooltip" title="Z každého výběru účtujeme 5% poplatek">Dostanete: <span id="withdraw-sum-count" class="font-weight-bold"></span> <span class="font-weight-bold">Kč</span></p>
		<button type="submit" class="btn btn-success btn-sm mb-4">Potvrdit</button>
	</form>
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
  <script type="text/javascript" src="js/ajax-withdraw.js"></script>
  <script>
  	$('#withdraw_form input[type=number]').on('change invalid', function() {
    var textfield = $(this).get(0);
    
    // 'setCustomValidity not only sets the message, but also marks
    // the field as invalid. In order to see whether the field really is
    // invalid, we have to remove the message first
    textfield.setCustomValidity('');
    <?php
	if ($_SESSION['balance_real'] > 50) {
	?>
		if (!textfield.validity.valid) {
			textfield.setCustomValidity('Uvedená častka musí být v rozmezí od <?php echo MIN_WITHDRAW_SUM; ?> Kč do <?php echo $_SESSION['balance_real']; ?>Kč.');  
		}
	<?php
	} else {
	?>
		if (!textfield.validity.valid) {
      		textfield.setCustomValidity('Pro uskutečnění výběru musíte mít alespoň 50 Kč.');  
    	}
	<?php
	}
	?>
});
	$('#withdraw-sum').keyup(function(){
		$("#withdraw-sum-count-p").slideDown("slow");
		var withdrawSum = $('#withdraw-sum').val();
		$('#withdraw-sum-count').text(Math.floor(withdrawSum-0.05*withdrawSum));
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