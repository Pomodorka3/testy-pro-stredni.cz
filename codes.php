<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';
require_once '../../scripts/authorize.php';

	authorize_user(array("Administrator", "Main administrator"));
	full_register();
	school_check();
	page_start("Kódy");
	display_messages();
	update_activity();

?>

<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-barcode"></i> Kódy</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
<div class="row mx-1 my-4">
	<div class="col-md-2">
		<h5 class="text-center font-weight-bold" style="font-family: 'Baloo', cursive;">Přidávaní kódů</h5>
		<p>Zde si můžete prohlédnout stávající kódy a vygenerovat nový kód. Je přísně <span class="font-weight-bold">ZAKÁZANO</span> rozdávat kódy uživatelům jen tak.</p>
		<?php
			if (user_in_group("Main administrator", $_SESSION['user_id'])) {
				echo '<div class="text-center">
				<a href="#" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#createCodeModal">Vygenerovat kód</a>
			</div>';
			}
		?>
		<div class='modal fade' id='createCodeModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
       		<div class='modal-dialog modal-center' role='document'>


              <div class='modal-content'>
                <div class='modal-header'>
                  <h4 class='modal-title w-100' id='myModalLabel'>Vygenerovat kód</h4>
                  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
                    <span aria-hidden='true'>&times;</span>
                  </button>
                </div>
                <div class='modal-body'>
                  <form action='admin_codes_action?action=generateCode' class='' method='post'>
                    <div class="row">
                    	<div class="col-md-6">
                    		<h5 class="">Typ kódu:</h5>
                    		<div class="custom-control custom-radio" id="balance_radiobutton">
								<input type="radio" class="custom-control-input" id="balance" name="code_type" value="balance" required>
								<label for="balance" class="custom-control-label" onclick="show_balanceAmount()">Peníze</label>
							</div>
							<div class="custom-control custom-radio" id="vip_radiobutton">
								<input type="radio" class="custom-control-input" id="vip" name="code_type" value="vip">
								<label for="vip" class="custom-control-label" onclick="show_vipAmount()">VIP</label>
							</div>
							<div>
								<input type="number" class="form-control mt-3" name="vip_value" min="1" max="365" placeholder="Počet dní:" style="display: none;" id="vip_amount">
							</div>
							<div>
								<input type="number" class="form-control mt-3" name="code_value" min="1" max="1000" placeholder="Množství peněz (Kč):" style="display: none;" id="balance_amount">
							</div>
							
                    	</div>
                    	<div class="col-md-6 mt-3 mt-md-0">
                    		<h5 class="">Kód vyprší za:</h5>
                    		<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" id="expiration1" name="code_expiration" value="5d" required>
								<label for="expiration1" class="custom-control-label">5 dní</label>
							</div>
							<div class="custom-control custom-radio">
								<input type="radio" class="custom-control-input" id="expiration2" name="code_expiration" value="30d">
								<label for="expiration2" class="custom-control-label">30 dní</label>
							</div>
                    	</div>
                    </div>
                    <button type='submit' class='btn btn-success btn-sm d-flex mx-auto mt-3'>Potvrdit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
		  <h5 class="text-center font-weight-bold m-3" style="font-family: 'Baloo', cursive;">Filtr</h5>
			<div class="row">
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_code" id="search_code" class="form-control mb-4 filter_field">
						<label for="search_code" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Kód</label>
					</div>
				</div>
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_createdby" id="search_createdby" class="form-control mb-4 filter_field">
						<label for="search_createdby" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Vytvořil</label>
					</div>
				</div>
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_activatedby" id="search_activatedby" class="form-control mb-4 filter_field">
						<label for="search_activatedBy" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Aktivoval</label>
					</div>
				</div>
				<div class="col-md-12 my-auto">
					<div class='custom-control custom-checkbox'>
						<input type='checkbox' class='custom-control-input checkbox_sort' id='activated' value='1'>
						<label class='custom-control-label' for='activated'>Aktivován</label>
					</div>
				</div>
				<div class="col-md-12 mt-3">
					<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Typ kódu</h6>
						<?php
						$code_type = 'SELECT DISTINCT code_type FROM codes;';
						$code_type_query = mysqli_query($connect, $code_type);
						$i = 3;

						while ($code_type_row = mysqli_fetch_row($code_type_query)) {
							$i++;
							if ($code_type_row[0] == 'balance') {
								$code_type_row[0] = 'Balance';
							} elseif ($code_type_row[0] == 'vip') {
								$code_type_row[0] = 'VIP';
							}
							echo "<div class='custom-control custom-checkbox'>
									<input type='checkbox' class='custom-control-input checkbox_sort itemSubject' id='".$i."' value='".$code_type_row[0]."'>
									<label class='custom-control-label' for='".$i."'>".$code_type_row[0]."</label>
								</div>";
						}
						?>
				</div>
		</div>
	</div>
	<div class="col-md-10 mt-4 mt-md-0">
		<div id="shop_table">

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
  <script type="text/javascript" src="js/ajax-codes.js"></script>
  <!-- Code generator - balance field toggle -->
  <script>
  	function show_balanceAmount(){
  		var bal = $("#balance_amount");
  		if (bal.prop('required')) {
  			bal.prop('required', false);
  		} else {
  			bal.prop('required', true);
  		}
		bal.toggle("slow");
		$("#vip_radiobutton").toggle("slow");
	}
	function show_vipAmount(){
  		var bal = $("#vip_amount");
  		if (bal.prop('required')) {
  			bal.prop('required', false);
  		} else {
  			bal.prop('required', true);
  		}
		bal.toggle("slow");
		$("#balance_radiobutton").toggle("slow");
	}
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