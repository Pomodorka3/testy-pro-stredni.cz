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
page_start("Odstraněné testy");
display_messages();
update_activity();

?>
<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="fas fa-cart-plus"></i> Administrátory odstraněné testy</h4>
<hr class="black mt-0 z-depth-1" style="width:100px;">
<div class="row mx-1 my-4">
    <div class="col-md-2">
      <h5 class="text-center font-weight-bold mb-3" style="font-family: 'Baloo', cursive;">Filtr</h5>
      <div class="row">
        <div class="col-md-12">
          <div class="md-form m-0">
            <label for="school_name" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Škola:</label>
            <input type="text" name="school_name" id="school_name" class="form-control mb-4 school_name" autocomplete="off" maxlength="40">
          </div>
        </div>
        <div class="col-md-12">
          <div class="md-form m-0">
            <label for="item_createdby" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Přidal:</label>
            <input type="text" name="item_createdby" id="item_createdby" class="form-control mb-4 item_createdby" autocomplete="off" maxlength="20">
          </div>
        </div>
        <div class="col-md-12">
          <div class="md-form m-0">
            <label for="removed_by" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Odstranil:</label>
            <input type="text" name="removed_by" id="removed_by" class="form-control mb-4 removed_by" autocomplete="off" maxlength="20">
          </div>
        </div>
        <div class="col-md-12">
          <div class="md-form m-0">
            <label for="item_name" style="font-family: 'Baloo', cursive;"><i class="fas fa-search"></i> Název:</label>
            <input type="text" name="item_name" id="item_name" class="form-control mb-4 item_name" autocomplete="off" maxlength="45">
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-10 mt-4 mt-md-0">
        <div id="shop_removed">
  
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
  <script type="text/javascript" src="js/ajax-shop-removed.js"></script>
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