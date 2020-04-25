<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

session_start();
authorize_user();
full_register();
authorize_user(array("Support", "Administrator", "Main administrator"));
page_start("Seznam škol");
display_messages();
update_activity();

?>
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-shopping-cart"></i> Školy</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
    <div class="row mx-1 my-4">
        <div class="col-md-2">
        <h5 class="text-center font-weight-bold mb-3" style="font-family: 'Baloo', cursive;">Filtr</h5>
         <div class="md-form">
            <label for="schoolName" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Název školy</label>
            <input type="text" name="schoolName" id="schoolName" class="form-control schoolName" autocomplete="off" maxlength="20">
          </div>
          <div class="md-form">
            <label for="cityName" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Název města</label>
            <input type="text" name="cityName" id="cityName" class="form-control cityName" autocomplete="off" maxlength="20">
          </div>
          <div class="md-form">
            <label for="districtName" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Název čtvrti</label>
            <input type="text" name="districtName" id="districtName" class="form-control districtName" autocomplete="off" maxlength="20">
          </div>
        </div>
        <div class="col-md-10 mt-4 mt-md-0">
          <div id="user_table">
        
          </div>
      </div>
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
  <script type="text/javascript" src="js/ajax-schools.js"></script>
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

</html>
<?php

	unset($_SESSION['modal']);
  unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
  unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>