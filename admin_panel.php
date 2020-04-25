<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

session_start();
authorize_user(array("Administrator", "Main administrator"));
full_register();
page_start("Admin panel");
display_messages();
update_activity();

?>
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-users"></i> Admin panel</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
  <div class="row mx-1 my-4">
    <div class="col-md-3">
    <h5 class="text-center font-weight-bold mb-3" style="font-family: 'Baloo', cursive;">Filtr</h5>
      <div class="md-form mt-2">
        <label for="username" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Uživatelské jméno:</label>
        <input type="text" name="username" id="username" class="form-control username" autocomplete="off" maxlength="20">
      </div>
      <div class="md-form mt-2">
        <label for="first_name_search" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Křestní jméno:</label>
        <input type="text" name="first_name_search" id="first_name_search" class="form-control first_name_search" autocomplete="off" maxlength="15">
      </div>
      <div class="md-form mt-2">
        <label for="last_name_search" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Příjmení:</label>
        <input type="text" name="last_name_search" id="last_name_search" class="form-control last_name_search" autocomplete="off" maxlength="30">
      </div>
      <div class="md-form mt-2">
        <label for="instagram_search" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Instagram:</label>
        <input type="text" name="instagram_search" id="instagram_search" class="form-control instagram_search" autocomplete="off" maxlength="40">
      </div>
      <div class="md-form mt-2">
        <label for="facebook_search" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Facebook:</label>
        <input type="text" name="facebook_search" id="facebook_search" class="form-control facebook_search mb-3" autocomplete="off" maxlength="40">
      </div>
        <h5 class="text-center font-weight-bold" style="font-family: 'Baloo', cursive;">Zobrazit:</h5>
        <div class="row mt-3">
          <div class="col-md-6">
            <div class="list-group">
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='first_name' value='u.first_name'><label class='custom-control-label' for='first_name'>Křestní jméno</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='last_name' value='u.last_name'><label class='custom-control-label' for='last_name'>Příjmení</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='email' value='u.email'><label class='custom-control-label' for='email'>Email</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='balance' value='u.balance'><label class='custom-control-label' for='balance'>Konto</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='bought_items' value='u.bought_items'><label class='custom-control-label' for='bought_items'>Koupeno testů</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='confirmed_items' value='u.confirmed_items'><label class='custom-control-label' for='confirmed_items'>Schváleno testů</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='declined_items' value='u.declined_items'><label class='custom-control-label' for='declined_items'>Odmítnuto testů</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='confirmed_reports' value='u.confirmed_reports'><label class='custom-control-label' for='confirmed_reports'>Schváleno reklamací</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='declined_reports' value='u.declined_reports'><label class='custom-control-label' for='declined_reports'>Odmítnuto reklamací</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='removed_items' value='u.removed_items'><label class='custom-control-label' for='removed_items'>Odstraněno testů</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='bank_number' value='u.bank_number'><label class='custom-control-label' for='bank_number'>Bankovní účet</label>
          </div>
          <!--<div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='last_action_check' value='u.last_action'><label class='custom-control-label' for='last_action_check'>Naposled online</label>
          </div>-->
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='register_date' value='u.register_date'><label class='custom-control-label' for='register_date'>Datum registrace</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='school_id' value='u.school_id'><label class='custom-control-label' for='school_id'>Škola</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='instagram' value='u.instagram'><label class='custom-control-label' for='instagram'>Instagram</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='facebook' value='u.facebook'><label class='custom-control-label' for='facebook'>Facebook</label>
          </div>
          <?php
            if (user_in_group("Main administrator", $_SESSION['user_id'])) {
              echo "
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='register_ip' value='u.register_ip'><label class='custom-control-label' for='register_ip'>Registrační IP</label>
              </div>
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input checkbox_filter_admin' id='last_ip' value='u.last_ip'><label class='custom-control-label' for='last_ip'>Poslední IP</label>
              </div>
          ";
            }
          ?>
          <hr>
          <!--<div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input' id='activated' value='1'><label class='custom-control-label' for='activated'>Activated</label>
          </div>
          <div class='custom-control custom-checkbox'>
            <input type='checkbox' class='custom-control-input' id='banned' value='1'><label class='custom-control-label' for='banned'>Banned</label>
          </div>-->
          <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" id="activated" name="radionButtonGroup1" value="1">
            <label for="activated" class="custom-control-label">Aktivován</label>
          </div>
          <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" id="banned" name="radionButtonGroup1" value="1">
            <label for="banned" class="custom-control-label">Zablokován</label>
          </div>
        </div>
          </div>
          <div class="col-md-6">
            <div class="list-group">
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input group' id='administrator' value='1'><label class='custom-control-label' for='administrator'>Administrátor</label>
              </div>
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input group' id='validator' value='2'><label class='custom-control-label' for='validator'>Validátor</label>
              </div>
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input group' id='support' value='3'><label class='custom-control-label' for='support'>Support</label>
              </div>
              <hr>
              <!--<div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" id="online" name="radionButtonGroup2" value="1">
                <label for="online" class="custom-control-label">Online</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" id="offline" name="radionButtonGroup2" value="1">
                <label for="offline" class="custom-control-label">Offline</label>
              </div>-->
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='online' value='1' checked><label class='custom-control-label' for='online'>Online</label>
              </div>
              <div class='custom-control custom-checkbox'>
                <input type='checkbox' class='custom-control-input' id='offline' value='1' checked><label class='custom-control-label' for='offline'>Offline</label>
              </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-9 mt-4 mt-md-0">
          <div id="user_table">
        
          </div>
        </div>
      </div>
  </div>
</div>
<?php
	page_end(false);
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
  <script type="text/javascript" src="js/ajax-admin-panel.js"></script>
	<?php echo $_SESSION['js_modal_show']; ?>
  <!--<script type="text/javascript">
    $(document).ready(function(){
      $(".removeAdmin").click(function() {  //use a class, since your ID gets mangled
        $('.removeReason').addClass("d-block");      //add the class to the clicked element
      });
      function asd() {
        $('.removeReason').removeClass("d-none");
      }
    });
  </script>-->
  <script>
  $(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
})</script>
</body>

</html>
<?php

	unset($_SESSION['modal']);
  unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
  unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>