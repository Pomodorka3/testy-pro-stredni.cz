<?php

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/view.php';
require_once '../../scripts/authorize.php';

	//Сделать возможным только одиночный выбор у чекбоксов в фильтре shop.php.

	authorize_user();
	full_register();
	school_check();
	page_start("Obchod");
	tutorial("shop");
	display_messages();
	update_activity();

	//Modal window, generating if the 'shop' parameter in the table tutorial is set to 1
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
							Právě teď se nacházíte na stránce <span class="font-weight-bold text-primary">obchodu</span>, kde si můžete zakoupit potřebné testy.
						</p>
						<hr style="width:100px;" class="black">
						<p>
							Testy můžete vyhledávat pomocí filtru na levé straně obrazovky (na PC).
							<br>V připadě dlouhého popisu, se popis zkracuje na 50 znaků. Celý popis můžete vidět při navedení myši na \'<span class="font-weight-bold text-primary" data-toggle="tooltip" title="Vzor">...</span>\'
						</p>
						<hr style="width:100px;" class="black">
						<p class="text-danger font-weight-bold" style="font-size:18px;">
							Nemějte strach! Jestliže po zakoupení testu bude v příloze obrázek neodpovídající popisu. Stačí zmáčknout tlačítko reklamace, a peníze Vám budou vráceny zpět!
						</p>
					</div>
					<div class="modal-footer d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="tutorial_action?action=disable&page='.$page.'">Přečteno</a>
					</div>
				</div>
			</div>
		</div>';
	}

?>
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-shopping-cart"></i> Obchod</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
<p class="text-center font-weight-lighter text-danger px-2">Přidávejte testy a šiřte tuto stránku po vaší škole, jinak to Vám, ani nikomu jinému nic přinášet nebude!</p>
<div class="row mx-1 my-4">
	<div class="col-md-2 d-none d-md-block">
	<!-- <div class="col-md-2"> -->
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
			<div class="col-12">
				<!--<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Subject</h6>-->
				<div class="md-form m-0">
					<input type="text" list="subject_list" id="item_subject" name="item_subject" class="form-control mb-4 filter_field" autocomplete="off">
					<label for="item_subject" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Předmět</label>
					<datalist id="subject_list">
						<?php
						$subjects = sprintf("SELECT DISTINCT s.item_subject FROM shop s, users u WHERE u.user_id = '%d' AND s.visible = 1 AND s.checked = 1 AND u.school_id = s.school_id ORDER BY s.item_subject ASC;",
						mysqli_real_escape_string($connect, $_SESSION['user_id']));
						$subjects_query = mysqli_query($connect, $subjects);
						while ($subjects_row = mysqli_fetch_row($subjects_query)) {
							echo "<option value='".$subjects_row[0]."'>".$subjects_row[0]."</option>";
						}
						?>
					</datalist>
				</div>
			</div>
			<div class="col-12">
				<!--<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Teacher</h6>-->
				<div class="md-form m-0">
					<input type="text" list="teacher_list" name="teacher" id="teacher" class="form-control mb-4 filter_field" autocomplete="off">
					<label for="teacher" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Učitel</label>
					<datalist id="teacher_list">
						<?php
						$teacher = sprintf("SELECT DISTINCT s.teacher FROM shop s, users u WHERE u.user_id = '%d' AND s.visible = 1 AND s.checked = 1 AND u.school_id = s.school_id ORDER BY s.teacher ASC;",
						mysqli_real_escape_string($connect, $_SESSION['user_id']));
						$teacher_query = mysqli_query($connect, $teacher);
						while ($teacher_row = mysqli_fetch_row($teacher_query)) {
							echo "<option value='".$teacher_row[0]."'>".$teacher_row[0]."</option>";
						}
						?>
					</datalist>
				</div>
			</div>
			<div class="col-12 mb-3">
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
			<div class="col-12">
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
	<div class="col-md-2 d-md-none">
		<a id="filter-toggle" class="text-primary"><h5 class="text-center font-weight-bold mb-3" style="font-family: 'Baloo', cursive;"><i class="fas fa-caret-down" id="instagram-arrow-down"></i><i class="fas fa-caret-up" id="instagram-arrow-up" style="display:none;"></i> Filtr</h5></a>
		<div id="filter" style="display:none;">
			<div class="row">
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_name_hidden" id="search_name_hidden" class="form-control mb-4 filter_field_hidden">
						<label for="search_name" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Název</label>
					</div>
				</div>
				<div class="col-md-12">
					<div class="md-form m-0">
						<input type="text" name="search_description" id="search_description_hidden" class="form-control mb-4 filter_field_hidden">
						<label for="search_description" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Popis</label>
					</div>
				</div>
				<div class="col-12">
					<div class="md-form m-0">
						<input type="text" list="subject_list" id="item_subject_hidden" name="item_subject" class="form-control mb-4 filter_field_hidden" autocomplete="off">
						<label for="item_subject" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Předmět</label>
						<datalist id="subject_list">
							<?php
							$subjects = sprintf("SELECT DISTINCT s.item_subject FROM shop s, users u WHERE u.user_id = '%d' AND s.visible = 1 AND s.checked = 1 AND u.school_id = s.school_id ORDER BY s.item_subject ASC;",
							mysqli_real_escape_string($connect, $_SESSION['user_id']));
							$subjects_query = mysqli_query($connect, $subjects);
							while ($subjects_row = mysqli_fetch_row($subjects_query)) {
								echo "<option value='".$subjects_row[0]."'>".$subjects_row[0]."</option>";
							}
							?>
						</datalist>
					</div>
				</div>
				<div class="col-12">
					<div class="md-form m-0">
						<input type="text" list="teacher_list" name="teacher" id="teacher_hidden" class="form-control mb-4 filter_field_hidden" autocomplete="off">
						<label for="teacher" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Učitel</label>
						<datalist id="teacher_list">
							<?php
							$teacher = sprintf("SELECT DISTINCT s.teacher FROM shop s, users u WHERE u.user_id = '%d' AND s.visible = 1 AND s.checked = 1 AND u.school_id = s.school_id ORDER BY s.teacher ASC;",
							mysqli_real_escape_string($connect, $_SESSION['user_id']));
							$teacher_query = mysqli_query($connect, $teacher);
							while ($teacher_row = mysqli_fetch_row($teacher_query)) {
								echo "<option value='".$teacher_row[0]."'>".$teacher_row[0]."</option>";
							}
							?>
						</datalist>
					</div>
				</div>
				<div class="col-12 mb-3">
					<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Typ</h6>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input itemType_hidden" id="radio1" name="itemType" value="0">
						<label for="radio1" class="custom-control-label">Malý test</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input itemType_hidden" id="radio2" name="itemType" value="1">
						<label for="radio2" class="custom-control-label">Velký test</label>
					</div>
					<div class='custom-control custom-checkbox'>
						<input type='checkbox' class='custom-control-input itemAnswers_hidden' id='itemAnswers' value='itemAnswers'>
						<label class='custom-control-label' for='itemAnswers'>S odpověďmi na jedničku</label>
					</div>
				</div>
				<div class="col-12">
					<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Ročník</h6>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass_hidden" id="radio3" name="school_class" value="1">
						<label for="radio3" class="custom-control-label">1.</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass_hidden" id="radio4" name="school_class" value="2">
						<label for="radio4" class="custom-control-label">2.</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass_hidden" id="radio5" name="school_class" value="3">
						<label for="radio5" class="custom-control-label">3.</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input schoolClass_hidden" id="radio6" name="school_class" value="4">
						<label for="radio6" class="custom-control-label">4.</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-10 mt-4 mt-md-0">
		<div id="shop_table">
			
		</div>
	</div>
</div>

<?php
	page_end(true, 30);
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
  <script type="text/javascript" src="js/ajax-shop-sort.js"></script>
<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>