<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';

$user_id = $_SESSION['user_id'];

$already_set_up = sprintf("SELECT full_register FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $user_id));
$already_set_up_query = mysqli_query($connect, $already_set_up);
$already_set_up_row = mysqli_fetch_array($already_set_up_query);

if ($already_set_up_row['full_register'] == 1) {
	handle_error("Váš profil byl již nastaven. Pro další změny, využijte nastavení!");
}

authorize_user();
page_start("Úvodní nastavení");
display_messages();
update_activity();

?>
<div id="uploadimageModal" class="modal" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
        <div class="modal-header">
			<h4 class="modal-title">Nahrát a obříznout profilový obrázek</h4>
    		<button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
        <div class="row">
			<div class="col-md-12 text-center">
				<div id="image_demo"></div>
			</div>
      <div class="col-md-4" style="padding-top:30px;"></div>
      <!--<button type="button" id="rotateLeft" data-rotate="-90">Left</button>
			<button type="button" id="rotateRight" data-rotate="90">Right</button>-->
    	</div>
        </div>
        <div class="modal-footer">
			<button class="btn btn-success btn-sm crop_image">Obříznout a nahrát</button>
        </div>
     </div>
    </div>
</div>
<div class="container">
  <div class="text-center border border-light px-4 py-5 mx-auto my-5 rounded cloudy-knoxville-gradient z-depth-1" id="first-setup">
    <h4 class="mb-4" style="font-family: 'Baloo', cursive;">Úvodní nastavení</h4>
    <form action="first_setup_submit" method="post" class="">
      <div class="md-form mb-0">
        <label for="name">Křestní jméno*</label>
        <input type="text" name="name" id="name" class="form-control mb-4" autocomplete="off" maxlength="15" required>
      </div>
      <div class="md-form mb-0">
        <label for="name">Příjmení</label>
        <input type="text" name="lastname" id="lastname" class="form-control mb-4" autocomplete="off" maxlength="20">
      </div>
      <p class="font-weight-bold">Profilový obrázek:</p>
      <p class="mt-2" style="font-size:13px; color:#999999;">Jestli si nevyberete svůj profilový obrázek, bude Vám nastaven standardní.</p>
      <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
      <input type="file" class="mb-4" name="profile_image" accept=".jpeg, .jpg, .png" id="profile_image">
      <p class="mb-0 font-weight-bold">Sociální síťě:</p>
      <p class="mt-2" style="font-size:13px; color:#999999;">Jestli nechcete, aby Vaše sociální byly viditelné pro ostatní uživatele, můžete je vypnout v nastavení svého profilu.</p>
      <div class="row">
        <div class="col-md-12">
          <a id="set-instagram" class="text-primary"><i class="fas fa-caret-down" id="instagram-arrow-down"></i><i class="fas fa-caret-up" id="instagram-arrow-up" style="display:none;"></i> Link Instagram <i class="fab fa-instagram"></i></a>
          <input type="text" name="instagram" id="instagram" class="form-control my-2 mx-auto" placeholder="Název účtu Instagramu" autocomplete="off" maxlength="40" style="width:300px; display:none;">
        </div>
        <div class="col-md-12">
          <a id="set-facebook" class="text-primary"><i class="fas fa-caret-down" id="facebook-arrow-down"></i><i class="fas fa-caret-up" id="facebook-arrow-up" style="display:none;"></i> Link Facebook <i class="fab fa-facebook-f"></i></a>
          <input type="text" name="facebook" id="facebook" class="form-control my-2 mx-auto" placeholder="Přihlaš. jméno Facebooku" autocomplete="off" maxlength="40" style="width:300px; display:none;">
        </div>
      </div>
      <hr>
      <p>*povinné pole</p>
      <div class="text-center">
        <button class="btn btn-success" type="submit"><i class="fas fa-paper-plane mr-2"></i>Potvrdit</button>
      </div>
    </form>
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
  <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
  <script type="text/javascript" src="js/Croppie/croppie.js"></script>
  <script type="text/javascript" src="js/first-setup.js"></script>
  <?php echo $js_modal_show; ?>
<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
  unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
  unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>