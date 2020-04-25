<?php 

session_start();

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/debug.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';
require_once '../../scripts/ip_check.php';

authorize_user();
page_start("Tickets");
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
			<h4 style="font-family: 'Baloo', cursive;" class="text-center"><i class="fas fa-ticket-alt"></i> Tickets</h4>
			<hr class="black mt-0 mb-4 z-depth-1" style="width:100px;">
			<div class="row">
				<div class="col-md-9">
					<div class="md-form">
						<input type="text" class="form-control my-2" id="search">
						<label for="search"><i class="fas fa-search"></i> Search</label>
					</div>
				</div>
				<div class="col-md-3 my-auto">
					<div class='custom-control custom-checkbox'>
	                  <input type='checkbox' class='custom-control-input' id='bug' value='1' checked><label class='custom-control-label' for='bug'>Bug</label>
	                </div>
	                <div class='custom-control custom-checkbox'>
	                  <input type='checkbox' class='custom-control-input' id='question' value='1' checked><label class='custom-control-label' for='question'>Question</label>
	                </div>
				</div>
			</div>
			<?php
					if ($supports_online_row[0] == 0 ) {
						echo '<p>There are no <span class="text-warning">supports</span> online right now!</p>';
					} else {
						echo '<p><span class="text-warning">Supports</span> online: <span class="font-weight-bold">'.$supports_online_row[0].'</span></p>';
					}
				?>
			<div id="tickets_list">
				
			</div>
			<!--<h4 class="mb-3 text-center font-weight-bold">Question tickets</h4>
			<input type="text" class="form-control my-2">
			<?php
			if (!$content_query) {
				echo "<p>There are no tickets.</p>";
			} else {
				$i = 0;
				echo '
				<table class="table table-sm">
				  <thead>
				    <tr>
				      <th scope="col">ID</th>
				      <th scope="col">Type</th>
				      <th scope="col">Title</th>
				      <th scope="col">From</th>
				      <th scope="col">On</th>
				      <th scope="col">Status</th>
				      <th scope="col"></th>
				    </tr>
				  </thead>
				  <tbody>';
				while ($row = mysqli_fetch_array($content_query)) {
					$i++;
					if (user_in_group("Administrator", $user_id)) {
						$removeButton = '<a data-toggle="modal" data-target="#centralModalSm'.$i.'"><i class="fas fa-trash-alt text-danger mr-2"></i></a>';
						$removeModal = '
						<div class="modal fade" id="centralModalSm'.$i.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-center" role="document">
							<div class="modal-content">
								<div class="modal-header">
								<h4 class="modal-title w-100" id="myModalLabel'.$i.'">Delete selected ticket?</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
									<span aria-hidden="true">&times;</span>
								</button>
								</div>
								<div class="modal-body d-flex">
									<a class="btn btn-success btn-sm mx-auto" href="ticket_action.php?action=removeTicket&for='.$row['ticket_id'].'">Confirm</a>
								</div>
							</div>
							</div>
						</div>';
					}
					$ticket_type = '';
					if ($row['ticket_type'] == 1) {
						$ticket_type = 'Bug';
					} else {
						$ticket_type = 'Question';
					}
					$status = '';
					if ($row['ticket_answered'] == 0) {
						$status = '<i class="fas fa-lock-open text-success ml-2"></i>';
					} else {
						$status = '<i class="fas fa-lock text-danger ml-2"></i>';
					}
				printf('
					<tr>
				      <th scope="row">#%d</th>
				      <td>%s</td>
				      <td>%s</td>
				      <td><a class="text-primary font-weight-bold" href="profile_show.php?profile_id=%d">%s</a></td>
				      <td>%s</td>
				      <td>%s</td>
				      <td><a href="ticket_show.php?ticket_id=%s" class="btn btn-outline-info btn-sm">Show</a></td>
					  <td>%s</td>
					</tr>
					%s',
				$row['ticket_id'],
				$ticket_type,
				$row['ticket_title'],
				$row['user_id'],
				$row['username'],
				$row['ticket_created'],
				$status,
				$row['ticket_id'],
				$removeButton,
				$removeModal);
				}
			}
			?>
				</tbody>
			</table>-->
		</div>
		<div class="col-md-4 mt-3">
			<div>
				<h5 class="text-center font-weight-bold my-3" style="font-family: 'Baloo', cursive;">Mate dalsi otazky?</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam excepturi repellat dicta! Nam eligendi hic sequi possimus quia, vitae veritatis ratione!<br> Quibusdam possimus nemo, deleniti molestiae sint voluptate tenetur, illum, delectus reiciendis dolorem labore. Assumenda, delectus. Quisquam, assumenda ipsam maxime.
				You can ask other questions <a href="tickets.php"><u>here</u></a>
				</p>
			</div>
			<hr>
			<div>
				<h5 class="text-center font-weight-bold my-3" style="font-family: 'Baloo', cursive;">Lorem ipsum dolor?</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem perspiciatis itaque dolorum, iste, ex, voluptatem eum odio tempore illum neque repellendus earum doloremque odit, mollitia dolore.<br> Fugiat nobis aliquid dignissimos, ipsam quo omnis ratione. Sunt molestiae libero, maiores aut optio!</p>
			</div>
			<hr>
			<div class='text-center mb-3 mb-md-0'><a class='text-primary font-weight-bold' id='ticket_add_button'><i class="fas fa-plus text-success"></i> Add new ticket</a></div>
			<form action='ticket_action.php?action=createTicket' method='post' style='display: none;' id='ticket_add_form' class="mt-3" enctype="multipart/form-data">
				<div class="md-form mb-0">
					<input type='text' class='form-control mb-3' name='ticket_title' maxlength='100' autocomplete="off" required>
					<label for="ticket_title">Title*</label>
				</div>
				<div class="md-form mb-0">
					<textarea name='ticket_content' class=' md-textarea form-control mb-3' maxlength='1000' rows='5' required></textarea>
					<label for="ticket_content">Content*</label>
				</div>
				<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
				<label for="ticket_image">Add image:</label>
				<input type="file" name="ticket_image" class="mt-2" accept=".jpeg, .jpg, .png">
				<div class="text-center my-3">
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="radio1" name="ticket_type" value="1" required>
						<label for="radio1" class="custom-control-label">Bug</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" class="custom-control-input" id="radio2" name="ticket_type" value="0">
						<label for="radio2" class="custom-control-label">Question</label>
					</div>
				</div>
				<p class="text-center mt-3">*required fields</p>
				<button type='submit' class='btn btn-success d-flex mx-auto btn-sm mb-4'>Add</button>
			</form>
		</div>
	</div>
</div>

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