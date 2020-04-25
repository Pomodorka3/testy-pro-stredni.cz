<?php 

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

authorize_user();
page_start("Tikety");
full_register();
last_ip();
display_messages();
update_activity();

$user_id = $_SESSION['user_id'];

$content = "SELECT t.ticket_id, t.ticket_title, t.ticket_created, t.ticket_answered, t.ticket_type, u.username, u.user_id FROM tickets t, users u WHERE t.ticket_visible = '1' AND u.user_id = t.ticket_createdby ORDER BY t.ticket_answered ASC, t.ticket_created DESC;";
$content_query = mysqli_query($connect, $content);

$supports_online = "SELECT COUNT(*) FROM users u, users_groups ug WHERE u.user_id = ug.user_id AND ug.group_id = '3' AND u.last_action > DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY  u.last_action DESC, u.user_id ASC;";
$supports_online_query = mysqli_query($connect, $supports_online);
$supports_online_row = mysqli_fetch_row($supports_online_query);

//$admins_online = "SELECT COUNT(*) FROM users u, users_groups ug WHERE u.user_id = ug.user_id AND ug.group_id = '1' AND u.last_action > DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY  u.last_action DESC, u.user_id ASC;";
//$admins_query_online = mysqli_query($connect, $admins_online);

?>

<div class="container">
	<div class="row mt-4">
		<div class="col-md-8">
			<h4 style="font-family: 'Baloo', cursive;" class="text-center"><i class="fas fa-ticket-alt"></i> Tikety</h4>
			<hr class="black mt-0 mb-4 z-depth-1" style="width:100px;">
			<div class="row">
				<div class="col-md-9">
					<div class="md-form">
						<input type="text" class="form-control my-2" id="search">
						<label for="search"><i class="fas fa-search"></i> Vyhledat</label>
					</div>
				</div>
				<div class="col-md-3 my-auto">
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input ticketType" id="radio1" name="ticketType" value="1">
						<label for="radio1" class="custom-control-label">Bug</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input ticketType" id="radio2" name="ticketType" value="0">
						<label for="radio2" class="custom-control-label">Otázka</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input ticketType" id="radio3" name="ticketType" value="2">
						<label for="radio3" class="custom-control-label">Návrh</label>
					</div>
				</div>
			</div>
			<?php
				if ($supports_online_row[0] == 0 ) {
					echo '<p>Teď nejsou žádní <span class="text-warning">Supporti</span> online!</p>';
				} else {
					echo '<p><span class="text-warning">Supporti</span> online: <span class="font-weight-bold">'.$supports_online_row[0].'</span></p>';
				}
			?>
			<div id="tickets_list">
				
			</div>
		</div>
		<div class="col-md-4 mt-3">
			<div>
				<h5 class="text-center font-weight-bold my-3" style="font-family: 'Baloo', cursive;">Co jsou to tikety?</h5>
				<p>Tikety slouží zákazníkům jako doplnění informací o tomto e-shopu. Pokud na svůj speciální dotaz nenaleznete odpověď na stránce <a href="faq">FAQ</a>. Vytvořte ticket a vyčkejte si na odpověď.</p>
			</div>
			<hr>
			<div class='text-center mb-3 mb-md-0'><a class='text-primary font-weight-bold' id='ticket_add_button'><i class="fas fa-plus text-success"></i> Přidat nový tiket</a></div>
			<form action='ticket_action?action=createTicket' method='post' style='display: none;' id='ticket_add_form' class="mt-3" enctype="multipart/form-data">
				<div class="md-form mb-0">
					<input type='text' class='form-control mb-3' name='ticket_title' maxlength='100' autocomplete="off" required>
					<label for="ticket_title">Název*</label>
				</div>
				<div class="md-form mb-0">
					<textarea name='ticket_content' class=' md-textarea form-control mb-3' maxlength='1000' rows='5' required></textarea>
					<label for="ticket_content">Obsah*</label>
				</div>
				<input type="hidden" name="MAX_FILE_SIZE" value="5100000">
				<label for="ticket_image">Přidat obrázek:</label>
				<input type="file" name="ticket_image" class="mt-2" accept=".jpeg, .jpg, .png" onchange="ValidateSize(this)">
				<div class="text-center my-3">
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="radio4" name="ticket_type" value="1" required>
						<label for="radio4" class="custom-control-label">Bug</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="radio5" name="ticket_type" value="0">
						<label for="radio5" class="custom-control-label">Otázka</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="radio6" name="ticket_type" value="2">
						<label for="radio6" class="custom-control-label">Návrh</label>
					</div>
				</div>
				<p class="text-center mt-3">*povinná pole</p>
				<button type='submit' class='btn btn-success d-flex mx-auto btn-sm mb-4'>Přidat</button>
			</form>
		</div>
	</div>
</div>

<?php
	page_end(true, 8);
?>
</body>

  <!-- JQuery -->
  <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
  <script src="//code.jquery.com/jquery-1.12.4.js"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.js"></script>
  <!-- Accordion plugin -->
  <script type="text/javascript" src="js/plug_accordion/accordion.js"></script>
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/custom-script.js"></script>
  <script type="text/javascript" src="js/ajax-tickets-search.js"></script>
  <script>
	function ValidateSize(file) {
        var FileSize = file.files[0].size / 1024 / 1024; // in MB
        if (FileSize > 5) {
        	alert('Soubor je větší než 5 MB!');
        	$(file).val(''); //for clearing with Jquery
        } else {

        }
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