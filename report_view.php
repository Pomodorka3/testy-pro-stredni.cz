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
	page_start("Reklamace");
	display_messages();
	update_activity();

	if (!isset($_GET['page']) || ($_GET['page'] == "") || ($_GET['page'] == 0)) {
		$page = 1;
	} else {
		$page = $_GET['page'];
	}
	$items_per_page = 6;
	$offset = ($page-1)*$items_per_page;
	$total_pages_sql = sprintf("SELECT COUNT(*) FROM report r, shop s WHERE r.report_item = s.item_id;",
	mysqli_real_escape_string($connect, $_SESSION['user_id']));
	$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
	$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
	$total_pages = ceil($total_rows/$items_per_page);

	$report_cycle = sprintf("SELECT r.report_item, r.report_message, r.report_from, r.report_on, r.report_date, s.item_name, s.item_description, s.item_subject, s.item_type, s.school_class, s.teacher, s.item_answers, u1.user_id report_from_userid, u1.username report_from_username, u2.user_id report_on_userid, u2.username report_on_username FROM report r, shop s, users u1, users u2 WHERE r.report_item = s.item_id AND u1.user_id = r.report_from AND u2.user_id = r.report_on ORDER BY r.report_date ASC LIMIT %s, %s;",
	$offset,
	$items_per_page);
	$report_cycle_query = mysqli_query($connect, $report_cycle);

	?>
	<div class="m-4">
		<h4 class="text-center font-weight-bold mt-4" style="font-family: 'Baloo', cursive;"><i class="far fa-flag"></i> Reklamace</h4>
		<hr class="black mt-0 z-depth-1" style="width:100px;">
			<?php
				if (mysqli_num_rows($report_cycle_query) == 0) {
					echo "<p class='font-weight-bold text-center'>Zatím nejsou žádné reklamace!</p>";
				} else {
					echo '<div class="table-responsive">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Předmět</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Ročník</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Učitel</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Typ</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Název</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Popis</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Odeslal</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Na</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Datum</th>
								<th scope="col" style="font-family: \'Baloo\', cursive;">Přílohy</th>
								<th scope="col"></th>
							</tr>
						</thead>
					<tbody>';
					while ($report = mysqli_fetch_array($report_cycle_query)) {
						$report_from = sprintf("SELECT u.username FROM users u, report r WHERE u.user_id = r.report_from AND r.report_from = '%d';",
						mysqli_real_escape_string($connect, $report['report_from']));
						$report_from_query = mysqli_query($connect, $report_from);
						$report_from_row = mysqli_fetch_array($report_from_query);
						$report_on = sprintf("SELECT u.username FROM users u, report r WHERE u.user_id = r.report_on AND r.report_on = '%d';",
						mysqli_real_escape_string($connect, $report['report_on']));
						$report_on_query = mysqli_query($connect, $report_on);
						$report_on_row = mysqli_fetch_array($report_on_query);

						$image = NULL;
						$no_image = false;
						$select_image = sprintf("SELECT file_path FROM images WHERE shop_id = '%d';",
						mysqli_real_escape_string($connect, $report['report_item']));
						$select_image_query = mysqli_query($connect, $select_image);
						
						if (mysqli_num_rows($select_image_query) != 0) {
							$i=0;
							while ($select_image_row = mysqli_fetch_row($select_image_query)) {
								$image[$i] = $select_image_row[0];
								$i++;
							}
						} else {
							$no_image = true;
						}
						$str = "abcdefghijklmn";
						$shuffle1 = str_shuffle($str);
						$shuffle2 = str_shuffle($str);
						$shuffle3 = str_shuffle($str);
						$shuffle4 = str_shuffle($str);
						$report_date = strtotime($report['report_date']);

						$empty = ($no_image) ? 'Žádná příloha!' : '';
						$button1 = (!empty($image[0])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 1"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle1.'"></i></a>
						<div class="modal fade" id="'.$shuffle1.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle1.'" aria-hidden="true">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title w-100" id="'.$shuffle1.'">Příloha 1</h4>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        <img src="'.$image[0].'" class="img-fluid mx-auto d-flex">
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
						      </div>
						    </div>
						  </div>
						</div>' : '';
						$button2 = (!empty($image[1])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 2"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle2.'"></i></a>
						<div class="modal fade" id="'.$shuffle2.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle2.'" aria-hidden="true">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title w-100" id="'.$shuffle2.'">Příloha 2</h4>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        <img src="'.$image[1].'" class="img-fluid mx-auto d-flex">
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
						      </div>
						    </div>
						  </div>
						</div>' : '';
						$button3 = (!empty($image[2])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 3"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle3.'"></i></a>
						<div class="modal fade" id="'.$shuffle3.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle3.'" aria-hidden="true">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title w-100" id="'.$shuffle3.'">Příloha 3</h4>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        <img src="'.$image[2].'" class="img-fluid mx-auto d-flex">
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
						      </div>
						    </div>
						  </div>
						</div>' : '';
						$button4 = (!empty($image[3])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 4"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle4.'"></i></a>
						<div class="modal fade" id="'.$shuffle4.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle4.'" aria-hidden="true">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title w-100" id="'.$shuffle4.'">Příloha 4</h4>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
						        <img src="'.$image[3].'" class="img-fluid mx-auto d-flex">
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
						      </div>
						    </div>
						  </div>
						</div>' : '';
						if (strlen($report['item_description']) < 50) {
							$itemDescription = $report['item_description'];
						} else {
							$itemDescription = substr($report['item_description'], 0, 50);
							$itemDescription .= '<span class="font-weight-bold text-primary" data-toggle="tooltip" title="'.$report['item_description'].'">...</span>';
						}
						$type = ($report['item_type'] == 0) ? "<i class='far fa-square' data-toggle='tooltip' title='Malý test'></i>" : "<i class='fas fa-square' data-toggle='tooltip' title='Velký test'></i>";
						if ($report['item_answers'] == 1) {
							$itemAnswers = ' <i class="fas fa-certificate text-primary" data-toggle="tooltip" title="S odpověďmi na jedničku"></i>';
						} else {
							$itemAnswers = '';
						}
						echo $report_row = sprintf('
						<tr class=" h-100">
							<td class="my-auto">%s</td>
							<td class="my-auto">%d.</td>
							<td class="my-auto">%s</td>
							<td class="my-auto">%s</td>
							<td class="my-auto">%s</td>
							<td class="my-auto">%s</td>
							<td class="my-auto"><a href="profile_show?profile_id=%d" class="font-weight-bold text-primary">%s</a></td>
							<td class="my-auto"><a href="profile_show?profile_id=%d" class="font-weight-bold text-primary">%s</a></td>
							<td class="my-auto">%s</td>
							<td class="my-auto">%s</td>
							<td><a href="item_report_action?confirm_id=%d?buyer_id=%d" class="mx-2"><i class="fas fa-check text-success" data-toggle="tooltip" title="Potvrdit"></i></a>
							<a href="item_report_action?decline_id=%d" class="mx-2"><i class="fas fa-times text-danger" data-toggle="tooltip" title="Odmítnout"></i></a></td>
						</tr>',
						$report['item_subject'],
						$report['school_class'],
						$report['teacher'],
						$type.$itemAnswers,
						$report['item_name'],
						$itemDescription,
						$report['report_from_userid'],
						$report['report_from_username'],
						$report['report_on_userid'],
						$report['report_on_username'],
						date('d.m.Y H:i', $report_date),
						$empty.$button1.$button2.$button3.$button4,
						$report['report_item'],
						$report['report_from'],
						$report['report_item']);
					}
					echo '</tbody></table></div>';
					//Pagination
					$current_number = $page;
					if ($total_pages == 1) {
						$prev_class = 'd-none';
						$next_class = 'd-none';
						$backward_class = 'd-none';
						$forward_class = 'd-none';
						$current_class = 'd-none';
						$current_page = $current_number;
						$prev_number = '';
						$next_number = '';
					} else {
						$prev_number = $current_number - 1;
						$next_number = $current_number + 1;
					}
					if ($page <= 1) {
						$prev_class = 'd-none';
						$prev_number = '';
					} else {
						$prev_class = '';
					}
					if ($page >= $total_pages) {
						$next_class = 'd-none';
						$next_number = '';
					} else {
						$next_class = '';
					}
					echo $pagination = "
					<div class='container d-flex mx-auto'>
						<nav aria-label='Page nav' class='mx-auto'>
							<ul class='pagination pg-blue'>
								<li class='page-item $backward_class'><a href='report_view?page=1' id='1' class='page-link page'><i class='fas fa-fast-backward'></i></a></li>
								<li class='page-item $prev_class'><a href='report_view?page=$prev_number' id='".$prev_number."' class='page-link page'>".$prev_number."</a></li>
								<li class='page-item $current_class'><a href='report_view?page=$current_number' id='".$current_number."' class='page-link page'><u>".$current_number."</u></a></li>
								<li class='page-item $next_class'><a href='report_view?page=$next_number' id='".$next_number."' class='page-link page'>".$next_number."</a></li>
								<li class='page-item $forward_class'><a href='report_view?page=$total_pages' id='$total_pages' class='page-link page'><i class='fas fa-fast-forward'></i></a></li>
							</ul>
						</nav>
					</div>";
				}
	?>
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
	<?php echo $_SESSION['js_modal_show']; ?>

</html>
<?php

	unset($_SESSION['modal']);
	unset($_SESSION['success_message']);
	unset($_SESSION['error_message']);
	unset($_SESSION['info_message']);
	unset($_SESSION['js_modal_show']);

?>