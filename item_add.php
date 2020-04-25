<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';

session_start();
authorize_user();
full_register();
update_activity();
display_messages();
school_check();
page_start("Přidat test");
tutorial("item_add");

//Modal window, generating if the 'item_add' parameter in the table tutorial is set to 1
function tutorialModal($page){
	echo '
	<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">Přidání vašeho prvního testu</h4>
				</div>
				<div class="modal-body text-center">
					<p class="font-weight-bold" style="font-size:18px;">
						Nyní si vyzkoušejte přidat svůj první test do našeho systému!
					</p>
					<hr style="width:100px;" class="black">
					<p>
						Přidávaný test musí mít obsahu odpovídající název a popis.
						Po zmáčknutí tlačítka <button class="btn btn-info btn-sm sunny-morning-gradient"><i class="fas fa-plus mr-2"></i>Přidat</button> se Váš přidaný test posílá na kontrolu <span class="text-secondary">Validátorům</span>.
						<br>Pokud přidaný test <u>neporuší podmínky</u>, bude schválen a přidán do obchodu nejpozěji do 12 hodin od podání žádosti.
					</p>
					<hr style="width:100px;" class="black">
					<p class="text-danger font-weight-bold">
						Před přidáváním testů do našeho obchodu, přečtěte si prosím pozorně krátké podmínky přidávání testů.
					</p>
				</div>
				<div class="modal-footer d-flex">
					<a class="btn btn-success btn-sm mx-auto" href="tutorial_action?action=disable&page='.$page.'">Přečteno</a>
				</div>
			</div>
		</div>
	</div>';
}
/* function tutorialModal($page){
	echo '
	<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">Tutoriál</h4>
				</div>
				<div class="modal-body text-center">
					<p>
						Na této stránce můžete přidávat testy na prodej do našeho obchodu.
					</p>
					<hr style="width:100px;" class="black">
					<p>
						Přidávaný test musí mít obsahu odpovídající název a popis.
						Po zmáčknutí tlačítka <button class="btn btn-info btn-sm sunny-morning-gradient"><i class="fas fa-plus mr-2"></i>Přidat</button> se Váš přidaný test posílá na kontrolu <span class="text-secondary">Validátorům</span>.
						<br>Pokud přidaný test <u>neporuší podmínky</u>, bude schválen a přidán do obchodu nejpozěji do 12 hodin od podání žádosti.
					</p>
					<hr style="width:100px;" class="black">
					<p class="text-danger font-weight-bold" style="font-size:18px;">
						Před přidáváním testů do našeho obchodu, přečtěte si prosím pozorně krátké podmínky přidávání testů.
					</p>
				</div>
				<div class="modal-footer d-flex">
					<a class="btn btn-success btn-sm mx-auto" href="tutorial_action?action=disable&page='.$page.'">Přečteno</a>
				</div>
			</div>
		</div>
	</div>';
} */

//(DONE) Добавить проверку на уже пост с таким же именем.
//(DONE) Добавить интервал в 2 минуты для создания постов. (С помощью куки файлов которые будут истекат спустя 2 минуты. Если тест на наличии данного куки будет false то будет выполнен код.)
//(DONE) Добавить ограничения на длину названий
//(DONE) Сделать минимальную цену в 30 Крон.
//(DONE) Добавить выбор класса(учебного года) в форму для добаления предмета в магазин.

//Исправить сохранение данных после нажатия f5 - после создания поста переадресовать на страницу с выводом постов и выдать сообщение о успешном завершении операции.
//(DONE) Добавить проверку на наличие прикрепленной фотографии при отправке формы (JavaScript).

//(DONE)Сдлеать ограничение в загрузку 4 файлов!!!(JS)

?>
<body>
	<div class="container h-100 mb-4">
		<form action="<?php echo SITE_ROOT."item_add_action"; ?>" method="POST" enctype="multipart/form-data" class="text-center border border-light px-4 py-5 mx-auto my-4 rounded cloudy-knoxville-gradient z-depth-1" id="itemAdd-window">
			<h4 style="font-family: 'Baloo', cursive;" class="text-center"><i class="fas fa-cart-plus"></i> Přidat test</h4>
			<hr class="black mt-0 mb-4 z-depth-1" style="width:100px;">
			<div class="md-form mb-0">
				<input type="text" name="item_name" id="item_name" class="form-control mb-4" required autocomplete="off" maxlength="45">
				<label for="item_name">Název* (opište název testu, téma)</label>
			</div>
			<div class="md-form mb-0">
				<textarea name="item_description" id="item_description" rows="5" class="md-textarea form-control rounded" autocomplete="off" maxlength="150"></textarea>
				<label for="item_description">Popis (skupina, detaily, apod.)</label>
			</div>
			<div class="row mt-3">
				<div class="col-md-6">
					<!--<input type="text" name="item_subject" id="item_subject" class="form-control" placeholder="Subject" required autocomplete="off" maxlength="5">-->
					<input id="subject" type="text" class="form-control mb-3 md-mb-0 cloudy-knoxville-gradient z-depth-1" name="item_subject" list="item_subject" maxlength="5" placeholder="Předmět (zkratka)*" required>
					<datalist id="item_subject" class="cloudy-knoxville-gradient">
				        <option value="Aj">Anglický jazyk</option>
				        <option value="Cj">Český jazyk</option>
				        <option value="Fj">Francouzský jazyk</option>
				        <option value="Nj">Německý jazyk</option>
				        <option value="Bi">Biologie</option>
				        <option value="M">Matematika</option>
				        <option value="Fy">Fyzika</option>
				        <option value="Ch">Chemie</option>
				        <option value="On">Občanská nauka</option>
				        <option value="Zsv">Základy společenských věd</option>
				        <option value="D">Dějepis</option>
				    </datalist>
				</div>
				<div class="col-md-6">
					<input id="teacher_input" type="text" class="form-control mb-3 md-mb-0 cloudy-knoxville-gradient z-depth-1" name="teacher" list="teacher" maxlength="30" placeholder="Učitel (příjmení)*" required autocomplete="off">
					<datalist id="teacher">
				    </datalist>
				</div>
				<div class="col-md-6 my-auto text-left">
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="radio1" name="item_type" value="0" required>
						<label for="radio1" class="custom-control-label">Malý test</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="radio2" name="item_type" value="1">
						<label for="radio2" class="custom-control-label">Velký test</label>
					</div>
				</div>
				<div class="col-md-6 mt-3 mt-md-0">
					<select name="school_class" id="school_class" class="form-control cloudy-knoxville-gradient z-depth-1" required>
						<option value="">Ročník*</option>
						<option value="1">1. ročník</option>
						<option value="2">2. ročník</option>
						<option value="3">3. ročník</option>
						<option value="4">4. ročník</option>
					</select>
				</div>
				<div class='custom-control custom-checkbox text-center mx-auto mt-3'>
					<input type='checkbox' class='custom-control-input checkbox_filter_admin' id='item_answers' name='item_answers'>
					<label class='custom-control-label' for='item_answers'>Odpovědi na jedničku</label>
				</div>
			</div>
			<p id="test"></p>
			<!--<input type="hidden" name="MAX_FILE_SIZE" value="30000000">-->
			<label for="files[]" class="mt-3">Přílohy*: </label>
			<input type="file" name="files[]" id="files" size="30" class="mb-4" multiple accept=".jpeg, .jpg, .png" required>
			<!--<input type="text" name="item_price" id="item_price" class="form-control mb-4" placeholder="Price" required autocomplete="off" value="<?php echo $item_price; ?>" maxlength="3">-->
			<input type="number" name="item_price" min="30" max="1000" class="form-control mx-auto mb-4 cloudy-knoxville-gradient z-depth-1" style="width: 170px;" required autocomplete="off" placeholder="Cena* (min. 30 Kč)">
			<div class="d-flex justify-content-around">
				<div>
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="checkbox" required>
						<label for="checkbox" class="custom-control-label">Souhlasím s <a data-toggle="modal" data-target="#rulesModal" class="font-weight-bold text-primary"><u>podmínkami přidání testů</u></a>*</label>
					</div>
				</div>
			</div>
			<hr>
			<p>*povinná pole</p>
			<button class="btn btn-info btn-block sunny-morning-gradient" type="submit" name="submit" id="submit"><i class="fas fa-plus mr-2"></i>Přidat</button>
		</form>
		<div class='modal fade' id='rulesModal' tabindex='-1' role='dialog' aria-labelledby='rulesModalLabel' aria-hidden='true'>
			<div class='modal-dialog modal-center' role='document'>
				<div class='modal-content'>
					<div class='modal-header'>
						<h4 class="modal-title w-100" id="rulesModalLabel" style="font-family: 'Baloo', cursive;">Podmínky nahrávaní testů</h4>
						<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
							<span aria-hidden='true'>&times;</span>
						</button>
					</div>
					<div class='modal-body'>
						<p class="h5 text-danger text-center font-weight-bold">
							! V případě porušení těchto podmínek Váš test bude odmítnut !
						</p>
						<p>
							<span class="font-weight-bold">1.</span> Nahráváním testu na náš server nám předáváte veškerá autorská práva na tento test.
						</p>
						<p>
							<span class="font-weight-bold">2.</span> Jméno studenta musí být na testu zakryto papírkem, nebo pomocí editoru smazáno.
							<br><span class="font-weight-bold">2.1</span> Na testu nesmí být text/obrázky zesměšňující, nebo urážející nečí osobnost. Pokud jsou, musí být odstraněny, nebo přikryty.
						</p>
						<p>
							<span class="font-weight-bold">3.</span> Přílohy musí odpovídat veškerým informacím o testu. (název, popis, předmět, učitel, atd.)
							<br><span class="font-weight-bold">3.1</span> Maximální počet nahrávaných příloh je omezen na 4, a maximální velikost každé je 5 MB.
						</p>
						<p>
							<span class="font-weight-bold">4.</span> Prodejce testu získává 70% z celkové prodejní ceny.
							<br><span class="font-weight-bold">4.1</span> Prodejce s VIP statusem získává 80% z celkové prodejní ceny.
						</p>
						<p>
							<span class="font-weight-bold">5.</span> Administrátoři mají vyhrazené právo test bez jakýchkoli důvodů odstranit.
						</p>
					</div>
					<div class='modal-footer d-flex'>
						<button type="button" class="btn btn-success btn-sm mx-auto" data-dismiss="modal">Přečteno</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	page_end(true);
?>
</body>
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.js"></script>
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/custom-script.js"></script>
  <script type="text/javascript" src="js/ajax-item-add.js"></script>
  <script>
  	$('#withdraw_form input[type=number]').on('change invalid', function() {
	    var textfield = $(this).get(0);
	    
	    // 'setCustomValidity not only sets the message, but also marks
	    // the field as invalid. In order to see whether the field really is
	    // invalid, we have to remove the message first
	    textfield.setCustomValidity('');
	    
	    if (!textfield.validity.valid) {
	      textfield.setCustomValidity('Zde musí být částka v rozmezí od 30 Kč do 1000 Kč.');  
	    }
	});

	//Limit number of uploaded files
	$(function() {
		var // Define maximum number of files.
			max_file_number = 4,
			// Define your form id or class or just tag.
			$form = $('form'), 
			// Define your upload field class or id or tag.
			$file_upload = $('#files', $form), 
			// Define your submit class or id or tag.
			$button = $('#submit', $form); 

		// Disable submit button on page ready.
		$button.prop('disabled', 'disabled');

		$file_upload.on('change', function () {

			
			var number_of_images = $(this)[0].files.length;
			if (number_of_images > max_file_number) {
				alert(`Můžete nahrát nejvýše ${max_file_number} příloh.`);
				$(this).val('');
				$button.prop('disabled', 'disabled');
			} else {
				var fi = document.getElementById('files'); // GET THE FILE INPUT.
				// VALIDATE OR CHECK IF ANY FILE IS SELECTED.
				if (fi.files.length > 0) {
					// RUN A LOOP TO CHECK EACH SELECTED FILE.
					for (var i = 0; i <= fi.files.length - 1; i++) {
						var fsize = fi.files.item(i).size;      
						if (fsize/1024 > 5000) {
							alert('Maximální velikost každé přílohy je 5 MB.');
							fi.value = "";
							$button.prop('disabled', 'disabled');
						} else {
							$button.prop('disabled', false);
						}
						//Show size of each file
						/*document.getElementById('test').innerHTML =
						document.getElementById('test').innerHTML + '<br /> ' +
						'<b>' + Math.round((fsize / 1024)) + '</b> KB';*/
					}
				}
			}
		});
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