<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

	session_start();
	authorize_user(array("Support", "Administrator", "Main administrator"));
	full_register();
	page_start("Kontrola všech testů");
	display_messages();
	update_activity();

	$user_id = $_SESSION['user_id'];
	
?>
	<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-cart-plus"></i> Kontrola všech přidaných testů</h4>
	<hr class="black mt-0 z-depth-1" style="width:100px;">
	<div class="row mx-1 my-4">
		<div class="col-md-2">
			<h5 class="text-center font-weight-bold mb-3" style="font-family: 'Baloo', cursive;">Filtr</h5>
			<div class="row">
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_name" id="search_name" class="form-control mb-4 filter_field">
						<label for="search_name" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Název</label>
					</div>
				</div>
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_description" id="search_description" class="form-control mb-4 filter_field">
						<label for="search_description" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Popis</label>
					</div>
				</div>
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_school" id="search_school" class="form-control mb-4 filter_field">
						<label for="search_school" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Škola</label>
					</div>
				</div>
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" list="subject_list" id="item_subject" name="item_subject" class="form-control mb-4 filter_field" autocomplete="off">
						<label for="item_subject" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Předmět</label>
						<datalist id="subject_list">
							<?php
							$subjects = sprintf("SELECT DISTINCT item_subject FROM shop WHERE item_createdby_userid = '%d' AND checked = 0 ORDER BY item_subject ASC;",
							mysqli_real_escape_string($connect, $_SESSION['user_id']));
							$subjects_query = mysqli_query($connect, $subjects);

							while ($subjects_row = mysqli_fetch_row($subjects_query)) {
								echo "<option value='".$subjects_row[0]."'>".$subjects_row[0]."</option>";
							}
						?>
						</datalist>
					</div>
				</div>
				<div class="col-md-12 mb-3">
					<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Typ</h6>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input itemType" id="radio1" name="itemType" value="0">
						<label for="radio1" class="custom-control-label">Malý test</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input itemType" id="radio2" name="itemType" value="1">
						<label for="radio2" class="custom-control-label">Velký test</label>
					</div>
					<div class='custom-control custom-checkbox'>
						<input type='checkbox' class='custom-control-input itemAnswers' id='itemAnswers' value='itemAnswers'>
						<label class='custom-control-label' for='itemAnswers'>S odpověďmi na jedničku</label>
					</div>
				</div>
				<div class="col-md-12">
					<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Ročník</h6>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass" id="radio3" name="school_class" value="1">
						<label for="radio3" class="custom-control-label">1.</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass" id="radio4" name="school_class" value="2">
						<label for="radio4" class="custom-control-label">2.</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass" id="radio5" name="school_class" value="3">
						<label for="radio5" class="custom-control-label">3.</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass" id="radio6" name="school_class" value="4">
						<label for="radio6" class="custom-control-label">4.</label>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-10 mt-4 mt-md-0">
			<div id="item_check_all">
				
			</div>
		</div>
	</div>
<?php
page_end(true);
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
  <script type="text/javascript" src="js/ajax-item-check-all.js"></script>
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