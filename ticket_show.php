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
last_ip();
display_messages();
update_activity();

$user_id = $_SESSION['user_id'];

if (isset($_GET['ticket_id'])) {
	$ticket_id = $_GET['ticket_id'];
	$i = 0;

	$select_ticket = sprintf("SELECT t.ticket_id, t.ticket_title, t.ticket_content, t.ticket_answered, t.ticket_created, t.ticket_type, t.ticket_image, u.username, u.user_id, u.image_path FROM tickets t, users u WHERE t.ticket_id = '%d' AND t.ticket_createdby = u.user_id AND t.ticket_visible = 1;",
	mysqli_real_escape_string($connect, $ticket_id));
	$select_ticket_query = mysqli_query($connect, $select_ticket);
	if (mysqli_num_rows($select_ticket_query) == 0) {
		$_SESSION['error_message'] = "This ticket doesn't exist!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	}
	$select_ticket_row = mysqli_fetch_array($select_ticket_query);

	//Count comments
	$comments_count = sprintf("SELECT COUNT(*) FROM ticket_comments WHERE ticket_id = '%d';",
	mysqli_real_escape_string($connect, $ticket_id));
	$comments_count_query = mysqli_query($connect, $comments_count);
	$commentsTotal = mysqli_fetch_row($comments_count_query)[0];


	if ($select_ticket_row['ticket_answered'] == 0) {
		$status = '<i class="fas fa-lock-open text-success ml-2"></i>';
		if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			$status .= " <a data-toggle='modal' data-target='#statusChangeModal".$i."' href='' class='font-weight-bold'><u>Změnit</u></a>";
			$status .= '<div class="modal fade" id="statusChangeModal'.$i.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-center" role="document">
				<div class="modal-content">
					<div class="modal-header">
					<h4 class="modal-title w-100" id="statusChangeModalLabel'.$i.'">Změnit status na \'Uzavřen\'?</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						<span aria-hidden="true">&times;</span>
					</button>
					</div>
					<div class="modal-body d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="ticket_action?action=changeStatus&for='.$ticket_id.'">Potvrdit</a>
					</div>
				</div>
				</div>
			</div>';
		}
	} else {
		$status = '<i class="fas fa-lock text-danger ml-2"></i>';
		if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			$status .= " <a data-toggle='modal' data-target='#statusChangeModal".$i."' href='' class='font-weight-bold'><u>Změnit</u></a>";
			$status .= '<div class="modal fade" id="statusChangeModal'.$i.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-center" role="document">
				<div class="modal-content">
					<div class="modal-header">
					<h4 class="modal-title w-100" id="statusChangeModalLabel'.$i.'">Změnit status na \'Otevřen\'?</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						<span aria-hidden="true">&times;</span>
					</button>
					</div>
					<div class="modal-body d-flex">
						<a class="btn btn-success btn-sm mx-auto" href="ticket_action?action=changeStatus&for='.$ticket_id.'">Potvrdit</a>
					</div>
				</div>
				</div>
			</div>';
		}
	}
	if ($select_ticket_row['ticket_type'] == 1) {
		$ticket_type = '<i class="fas fa-bug orange-text" data-toggle="tooltip" title="Bug"></i>';
	} elseif ($select_ticket_row['ticket_type'] == 2) {
		$ticket_type = '<i class="fas fa-lightbulb amber-text" data-toggle="tooltip" title="Návrh"></i>';
	} else {
		$ticket_type = '<i class="fas fa-question-circle text-primary" data-toggle="tooltip" title="Otázka"></i>';
	}
	$ticket_created = strtotime($select_ticket_row['ticket_created']);
} else {
	handle_error("Nebylo uvedeno id.", "ticket_show");
}

page_start("Tikety");

?>

<div class="container mt-4">
	<h4 style="font-family: 'Baloo', cursive;" class="text-center"><i class="fas fa-ticket-alt"></i> Tiket #<?php echo $select_ticket_row['ticket_id']; ?></h4>
	<div class="border border-light rounded p-3 cloudy-knoxville-gradient z-depth-1">
		<div class="row">
			<div class="col-md-3 text-center">
				<img src="<?php if (!empty($select_ticket_row['image_path'])) { echo $select_ticket_row['image_path']; } else { echo 'profile_pictures/default/default.png'; } ?>" alt="" class="img-fluid z-depth-1 mb-3 rounded">
				<p><span class="font-weight-bold">Vytvořil:</span> <?php echo "<a class='text-primary font-weight-bold' href='profile_show?profile_id=".$select_ticket_row['user_id']."'>".$select_ticket_row['username']."</a>"; ?></p>
				<p><span class="font-weight-bold">Datum:</span> <?php echo date('d.m.Y H:i', $ticket_created); ?></p>
				<p><span class="font-weight-bold">Typ:</span> <?php echo $ticket_type; ?></p>
				<p><span class="font-weight-bold">Status:</span> <?php echo $status; ?></p>
			</div>
			<div class="col-md-9">
				<h5 class="font-weight-bold text-center mt-2"><span class="font-weight-normal"><u>Název:</u></span> <?php echo $select_ticket_row['ticket_title']; ?></h5>
				<hr>
				<p class="text-center"><span class="font-weight-normal"><u>Popis:</u></span> <?php echo $select_ticket_row['ticket_content']; ?></p>
				<?php
				if (!empty($select_ticket_row['ticket_image'])) {
					echo '<button type="button" class="btn btn-primary btn-sm d-flex mx-auto" data-toggle="modal" data-target="#attachedImage"><i class="far fa-file-image mr-2"></i> Příloha</button>
						<div class="modal fade" id="attachedImage" tabindex="-1" role="dialog" aria-labelledby="attachedImage" aria-hidden="true">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title w-100">Příloha</h4>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        <img src="'.$select_ticket_row['ticket_image'].'" class="img-fluid mx-auto d-flex">
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
						      </div>
						    </div>
						  </div>
						</div>';
				}
				?>
			</div>
		</div>
	</div>

	<div class="border border-light rounded p-3 my-3 cloudy-knoxville-gradient z-depth-1">
		<section id="comments_list">
		</section>
	</div>
</div>

<?php
	if ($commentsTotal == 0) {
		page_end(true);
	} else {
		page_end(false);
	}
?>
</body>

<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
  <script src="//code.jquery.com/jquery-1.12.4.js"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.js"></script>
  <!-- Custom JavaScript -->
  <script type="text/javascript" src="js/custom-script.js"></script>
  <script type="text/javascript" src="js/ajax-ticket-comments.js"></script>
  <?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>