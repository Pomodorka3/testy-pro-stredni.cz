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
	page_start("User profile");
	tutorial("profile");
	update_activity();
	last_ip();
	display_messages();
	
	//Modal window, generating if the 'profile' parameter in the table tutorial is set to 1
	function tutorialModal($page){
		echo '
		<div class="modal bounceIn fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
				<div class="modal-content">
					<div class="modal-header">
					<h4 class="modal-title w-100 text-center" id="myModalLabel" style="font-family: \'Baloo\', cursive;">\'Profile\' Tutorial</h4>
					</div>
					<div class="modal-body">
					<p class="text-center">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Saepe placeat quos reprehenderit nihil totam nulla doloribus cum ipsa sapiente, velit eius ut veniam ab accusamus magnam a in dolorem? Assumenda est ratione consequuntur nulla in veritatis, quis ducimus consequatur voluptates.</p>
							
					</div>
					<div class="modal-footer d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="tutorial_action.php?action=disable&page='.$page.'">Understood</a>
					</div>
				</div>
			</div>
		</div>';
	}

	$user_id = $_SESSION['user_id'];

	$mysqli_user_select = sprintf("SELECT username, password, email, balance, first_name, last_name, last_session, instagram, facebook, snapchat FROM users WHERE user_id = '%s';", 
		mysqli_real_escape_string($connect, $user_id));
	$user_school = sprintf("SELECT s.school_id, s.school_name FROM school s, users u WHERE u.user_id = '%d' AND s.school_id = u.school_id;",
	mysqli_real_escape_string($connect, $user_id));
	$confirmed_items = sprintf("SELECT COUNT(*) FROM shop WHERE confirmed_by = '%d';",
	mysqli_real_escape_string($connect, $user_id));
	$user_group = sprintf("SELECT ug.group_id FROM users_groups ug, users u WHERE ug.user_id = u.user_id AND u.user_id = '%d';",
	mysqli_real_escape_string($connect, $user_id));


	$result = mysqli_query($connect, $mysqli_user_select);
	$user_school_query = mysqli_query($connect, $user_school);
	$confirmed_items_query = mysqli_query($connect, $confirmed_items);
	$confirmed_items_row = mysqli_fetch_row($confirmed_items_query);
	$user_group_query = mysqli_query($connect, $user_group);

	if ($result) {
		$row = mysqli_fetch_array($result);
		$username = $row['username'];
		$password = $row['password'];
		$email = $row['email'];
		$balance = $row['balance'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$last_session = date('d.m.Y H:i', strtotime($row['last_session']));
		$instagram = $row['instagram'];
		$facebook = $row['facebook'];
		$snapchat = $row['snapchat'];
	} else {
		handle_error("There is some problem with database. We will fix it as soon, as possible!", "Something went wrong during extracting user unformation from database!");
	}

	if (!isset($_GET['page']) || ($_GET['page'] == "") || ($_GET['page'] == 0)) {
		$page = 1;
	} else {
		$page = $_GET['page'];
	}
	$items_per_page = 6;
	$offset = ($page-1)*$items_per_page;
	$total_pages_sql = sprintf("SELECT COUNT(*) FROM messages WHERE message_to = '%d' AND message_removed = 0;",
	mysqli_real_escape_string($connect, $user_id));
	$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
	$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
	$total_pages = ceil($total_rows/$items_per_page);

?>
<div class="row mx-1 my-5">
	<div class="col-md-3">
		<div class='modal fade' id='change_image' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
			<div class='modal-dialog modal-center' role='document'>
				<div class='modal-content'>
					<div class='modal-header'>
					<h4 class='modal-title w-100' id='myModalLabel' style="font-family: 'Baloo', cursive;">Change image</h4>
					<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
						<span aria-hidden='true'>&times;</span>
					</button>
					</div>
					<div class='modal-body'>
						<label for="edit_profile_image">Choose new profile image</label><br>
						<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
						<input type="file" class="mb-4" name="edit_profile_image" accept=".jpeg, .jpg, .png" id="new_profile_image">
						<div id="image_crop" style="display: none;">
							<div class="row">
								<div class="col-md-12 text-center">
									<div id="image_demo"></div>
								</div>
								<div class="col-md-4" style="padding-top:30px;"></div>
								<!--<button type="button" id="rotateLeft" data-rotate="-90">Left</button>
								<button type="button" id="rotateRight" data-rotate="90">Right</button>-->
							</div>
							<div class="modal-footer d-flex">
								<button class="btn btn-success btn-sm crop_image mx-auto">Crop & Upload Image</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card rounded px-2 py-3 text-center cloudy-knoxville-gradient">
			<!--<div class="view overlay zoom">-->
				<img id="profile_image" src="<?php if (!empty($_SESSION['image_path'])) { echo $_SESSION['image_path']; } else { echo 'profile_pictures/default/default.png'; } ?>" alt="" class="img-fluid mx-auto my-3 rounded z-depth-1">
				<!--<div class="mask flex-center rgba-black-light">
					<a href="#" id="change_picture" data-toggle='modal' data-target='#change_image'><i class="fas fa-pencil-alt"></i> Change profile picture</a>
				</div>
			</div>-->
			<p style="font-family: 'Jura', sans-serif; font-size: 24px;" class="font-weight-bold"><?php echo $first_name." ".$last_name; ?></p>
			<?php
			if ($user_group_query) {
				$user_group_row = mysqli_fetch_row($user_group_query);
				if ($user_group_row[0] == 1) {
					echo '<p><span class="text-success font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Administrator</span></p>';
				} elseif ($user_group_row[0] == 2) {
					echo '<p><span class="text-secondary font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Validator</span></p>';
				} elseif ($user_group_row[0] == 3) {
					echo '<p><span class="text-warning font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Support</span></p>';
				} elseif ($user_group_row[0] == 4) {
					echo '<p><span class="deep-orange-text font-weight-bold" style="font-family: \'Righteous\', cursive; font-size: 18px;">Main administrator</span></p>';
				}
			}
			if (!empty($_SESSION['status'])) {
				echo "<p>Status: ".$_SESSION['status']."</p>";
			}
			?>
			<div class="row">
				<?php
					if (empty($instagram) && empty($facebook) && empty($snapchat)) {
						echo '<div class="col-12">';
					} else {
						echo '<div class="col-6">';
					}
				?>
					<p><span style="color: #999999;">Username:</span> <?php echo $username; ?></p>
					<p><span style="color: #999999;">Balance:</span> <?php echo $balance; ?> Kc <a href="deposit.php"><i class="fas fa-plus text-success"></i></a></p>
					<?php
						if (mysqli_num_rows($user_school_query) != 0) {
							$user_school_row = mysqli_fetch_array($user_school_query);
							echo "<p><span style='color: #999999;'>School:</span> <a href='".SITE_ROOT."school_info.php'>".$user_school_row['school_name']."</a></p>";
						} else {
							echo "<p><span style='color: #999999;'>School:</span> Not set</p>";
						}
					?>
				</div>
				<div class="col-6 my-auto">
					<?php
						if (!empty($instagram)) {
							echo "<a href='https://www.instagram.com/".$instagram."'><img src='".SITE_ROOT."img/instagram.png' class='img-fluid mb-1 mx-2' style='width: 22px;'>Instagram</a><br>";
						} else {
							echo "";
						}
						if (!empty($facebook)) {
							echo "<a href='https://www.facebook.com/".$facebook."'><img src='".SITE_ROOT."img/facebook.png' class='img-fluid mb-1 mx-2' style='width: 22px;'>Facebook</a><br>";
						} else {
							echo "";
						}
						if (!empty($snapchat)) {
							echo "<a href='https://www.instagram.com/".$snapchat."'><img src='".SITE_ROOT."img/snapchat.png' class='img-fluid mb-1 mx-2' style='width: 22px;'>VKontakte</a>";
						}
					?>
				</div>
			</div>
			<p><span style="color: #999999;">Last session:</span> <?php echo $last_session; ?></p>
			<?php
				if (user_in_group("Validator", $user_id) ||user_in_group("Support", $user_id) || user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
					echo '<p><span style="color: #999999;">Confirmed items: </span>'.$confirmed_items_row[0].'</p>';
				}
			?>
			<a href="#" id="change_picture" data-toggle='modal' data-target='#change_image'><i class="fas fa-pencil-alt"></i> Change profile picture</a>
			<a href="profile_settings.php"><i class="fas fa-cog"></i> Settings</a>
		</div>
	</div>
	<div class="col-md-9">
		<h4 class="text-center font-weight-bold mt-4 mt-md-0" style="font-family: 'Baloo', cursive;"><i class="fas fa-bell"></i> Notifications</h4>
		<hr class="black mt-0 z-depth-1" style="width:100px;">
		<div id="notifications" style="height:200px;">
			
		</div>
	</div>
</div>

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
  <script type="text/javascript" src="js/ajax-profile.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
  <script type="text/javascript" src="js/Croppie/croppie.js"></script>
  <script>  
$(document).ready(function(){

 $image_crop = $('#image_demo').croppie({
	enableExif: true,
	enableOrientation: false,
    viewport: {
      width:200,
      height:200,
	  type:'square' //circle
    },
    boundary:{
      width:300,
      height:300
	}
  });

  $('#new_profile_image').on('change', function(){
    var reader = new FileReader();
    reader.onload = function (event) {
      $image_crop.croppie('bind', {
		url: event.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
    }
	reader.readAsDataURL(this.files[0]);
	$("#image_crop").slideToggle("slow");
	//$('#change_image').modal('hide');
    //$('#uploadimageModal').modal('show');
  });

  $('.crop_image').click(function(event){
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(response){
      $.ajax({
        url:"ajax/ajaxProfilePicture.php",
        type: "POST",
        data:{"image": response},
        success:function(data)
        {
		  $('#change_image').modal('hide');
		  location.reload(true);
          //$('#uploaded_image').html(data);
        }
      });
    })
  });
  /*$( "#rotateLeft" ).click(function() {
      $image_crop.croppie('rotate', parseInt($(this).data('rotate')));
  });
  
  $( "#rotateRight" ).click(function() {
      $image_crop.croppie('rotate',parseInt($(this).data('rotate')));
  });*/

});  
</script>
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