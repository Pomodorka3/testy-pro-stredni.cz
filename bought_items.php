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
	page_start("Koupené testy");
	tutorial("bought_items");
	last_ip();
	display_messages();
	update_activity();

	//Modal window, generating if the 'bought_items' parameter in the table tutorial is set to 1
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
							V tomto seznamu můžete vidět veškeré testy, které jste si koupil/-a.
						</p>
						<p>
							Pro otevření přílohy musíte kliknout na <i class="fas fa-file-image text-primary" data-toggle="tooltip" title="Přiloha"></i>.
						</p>
						<p>
							Po zakoupení testu, nezapomeňte prosím zakoupený test ohodnotit pomocí tlačítek <i class="fas fa-thumbs-up text-success" data-toggle="tooltip" title="Líbí se mi"></i> <i class="fas fa-thumbs-down text-danger" data-toggle="tooltip" title="Nelíbí se mi"></i>. A to aby ostatní uživatelé věděli, co je lepší koupit.
						</p>
						<p>
							Pomocí tlačítka <i class="fas fa-flag text-danger" data-toggle="tooltip" title="Reklamovat"></i> můžete tento test v přípaďe neshodování popisu a přílohy reklamovat.
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
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-shopping-cart"></i> Koupené testy</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
<div class="row mx-1 my-4">
<div class="col-md-2 d-none d-md-block">
	<h5 class="text-center font-weight-bold mb-3" style="font-family: 'Baloo', cursive;"><i class="fas fa-caret-down" id="instagram-arrow-down"></i><i class="fas fa-caret-up" id="instagram-arrow-up" style="display:none;"></i> Filtr</h5>
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
						$subjects = sprintf("SELECT DISTINCT s.item_subject FROM shop s, buy_events be WHERE be.buyer_id = '%d' AND be.item_id = s.item_id ORDER BY s.item_subject ASC;",
						mysqli_real_escape_string($connect, $user_id));
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
						$teacher = sprintf("SELECT DISTINCT s.teacher FROM shop s, buy_events be WHERE be.buyer_id = '%d' AND be.item_id = s.item_id ORDER BY s.teacher ASC;",
						mysqli_real_escape_string($connect, $user_id));
						$teacher_query = mysqli_query($connect, $teacher);
						while ($teacher_row = mysqli_fetch_row($teacher_query)) {
							echo "<option value='".$teacher_row[0]."'>".$teacher_row[0]."</option>";
						}
						?>
					</datalist>
				</div>
			</div>
			<div class="col-12">
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
			<div class="col-12 mt-3">
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
						<input type="text" name="search_name" id="search_name_hidden" class="form-control mb-4 filter_field_hidden">
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
					<!--<h6 class="font-weight-bold" style="font-family: 'Baloo', cursive;">Subject</h6>-->
					<div class="md-form m-0">
						<input type="text" list="subject_list" id="item_subject_hidden" name="item_subject" class="form-control mb-4 filter_field_hidden" autocomplete="off">
						<label for="item_subject" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Předmět</label>
						<datalist id="subject_list">
							<?php
							$subjects = sprintf("SELECT DISTINCT s.item_subject FROM shop s, buy_events be WHERE be.buyer_id = '%d' AND be.item_id = s.item_id ORDER BY s.item_subject ASC;",
							mysqli_real_escape_string($connect, $user_id));
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
						<input type="text" list="teacher_list" name="teacher" id="teacher_hidden" class="form-control mb-4 filter_field_hidden" autocomplete="off">
						<label for="teacher" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Učitel</label>
						<datalist id="teacher_list">
							<?php
							$teacher = sprintf("SELECT DISTINCT s.teacher FROM shop s, buy_events be WHERE be.buyer_id = '%d' AND be.item_id = s.item_id ORDER BY s.teacher ASC;",
							mysqli_real_escape_string($connect, $user_id));
							$teacher_query = mysqli_query($connect, $teacher);
							while ($teacher_row = mysqli_fetch_row($teacher_query)) {
								echo "<option value='".$teacher_row[0]."'>".$teacher_row[0]."</option>";
							}
							?>
						</datalist>
					</div>
				</div>
				<div class="col-12">
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
				<div class="col-12 mt-3">
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
		<div id="bought_items">
			
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
  <script type="text/javascript" src="js/ajax-bought-items.js"></script>
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