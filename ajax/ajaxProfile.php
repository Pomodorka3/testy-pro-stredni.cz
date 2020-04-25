<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user();

$output = '';
$column_name = $_POST['column_name'];
$order = $_POST['order'];
if ($order == 'desc' || $order == 'DESC' ) {
	$order = 'ASC';
} else {
	$order = 'DESC';
}

$user_id =  $_SESSION['user_id'];
$search = "SELECT u.user_id, u.username, m.message_id, m.message_from, m.message_content, m.message_date, m.message_removed FROM users u, messages m WHERE m.message_to = $user_id AND m.message_from = u.user_id AND m.message_removed = 0";
$total_pages_sql = "SELECT COUNT(*) FROM users u, messages m WHERE m.message_to = $user_id AND m.message_from = u.user_id AND m.message_removed = 0";
if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY m.message_date DESC";
}
//$search .= " LIMIT 6";
if (isset($_POST['page'])) {
	$page = $_POST['page'];
	if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
		echo "<b>Page</b>: ".$page."<br>";
	}
} else {
	$page = 1;
	if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
		echo "<b>Page</b>: ".$page."<br>";
	}
}
$items_per_page = 6;
$offset = ($page-1)*$items_per_page;
if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<b>COUNT SQL: </b>".$total_pages_sql;
}
$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
$total_pages = ceil($total_rows/$items_per_page);
$search .= " LIMIT $offset, $items_per_page";

if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<br><b>Search SQL</b>: ".$search;
	echo "<br><b>Total pages</b>: ".$total_pages;
}
$search_query = mysqli_query($connect, $search);
$search_query2 = mysqli_query($connect, $search);

			if (mysqli_num_rows($search_query) == 0) {
				echo "<p class='font-weight-bold text-center'>Zatím nemáte žádné upozornění!</p>";
			} else {
				$i=0;
				$removeButton = '<a data-toggle="modal" data-target="#removeAll"><i class="fas fa-trash-alt text-danger mr-2" data-toggle="tooltip" title="Odstranit vše"></i></a>';
				echo $output = '<div class="table-responsive">
				<table class="table">
					<thead class="winter-neva-gradient">
						<tr>
							<th scope="col"></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="m.message_from" style="font-family: \'Baloo\', cursive; font-size: 16px;">Odesílatel</a></u></th>
							<th scope="col" class="th-lg"><u><a class="column_sort" data-order="'.$order.'" id="m.message_content" style="font-family: \'Baloo\', cursive; font-size: 16px;">Upozornění</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="m.message_date" style="font-family: \'Baloo\', cursive; font-size: 16px;">Datum</a></u></th>
							<th scope="col"></th>
							<th scope="col" class="text-center">'.$removeButton.'</u></th>
						</tr>
					</thead>
				<tbody>';
				while ($row = mysqli_fetch_array($search_query)) {
					$i++;
					$icon_system = ($row['message_from'] == 0) ? '<i class="fas fa-info"></i>' : '<i class="fas fa-envelope"></i>';
					$color_system = ($row['message_from'] == 0) ? 'winter-neva-gradient' : '';
					$from_system = ($row['message_from'] == 0) ? 'Systém' : ("<a href=".SITE_ROOT."profile_show.php?profile_id=".$row['user_id']." class='text-primary'>".$row['username']."</a>");
					$reply_system = ($row['message_from'] == 0) ? '' : ("<a data-toggle='modal' data-target='#centralModalSm".$i."'><i class='fas fa-reply text-primary mr-3' data-toggle='tooltip' title='Odpovědět'></i></a>");
					echo $row_row = sprintf("
					<tr class='h-100 %s'>
						<td class='my-auto'>%s</td>
						<td class='my-auto font-weight-bold'>%s</td>
						<td class='my-auto'>%s</td>
						<td class='my-auto'>%s</td>
						<td>%s</td>
						<td><a class='text-center mx-auto' href='".SITE_ROOT."message_remove.php?message_id=%d'><i class='fas fa-trash-alt text-danger mr-2' data-toggle='tooltip' title='Odstranit'></i></a></td>
					</tr>
						",
						$color_system,
					$icon_system,
					$from_system,
					$row['message_content'],
					date('d.m.Y H:i', strtotime($row['message_date'])),
					$reply_system,
					$row['message_id']);
				}
				echo $table_end .= '</tbody></table></div>';
				$i=0;
				while ($row2 = mysqli_fetch_array($search_query2)) {
					$i++;
					echo "
					<div class='modal fade' id='centralModalSm".$i."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
						  <div class='modal-dialog modal-center' role='document'>
							<div class='modal-content'>
							  <div class='modal-header'>
								<h4 class='modal-title w-100' id='myModalLabel".$i."'>Odpovědět</h4>
								<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
								  <span aria-hidden='true'>&times;</span>
								</button>
							  </div>
							  <div class='modal-body'>
								<form action='".SITE_ROOT."message_reply.php?message_to=".$row2['message_from']."' class='text-center' method='post'>
									<textarea name='message_content' id='message_content' rows='6' class='form-control rounded' maxlength='400' autocomplete='off' required placeholder='Vaše zpráva:'></textarea>
										<button type='submit' class='btn btn-success btn-sm d-flex mx-auto mt-3'>Odeslat</button>
								</form>
							  </div>
							</div>
						  </div>
						</div>";
				}
				echo $removeModal = '
				<div class="modal fade" id="removeAll" tabindex="-1" role="dialog" aria-labelledby="removeAllLabel" aria-hidden="true">
					<div class="modal-dialog modal-center" role="document">
						<div class="modal-content">
							<div class="modal-header">
							<h4 class="modal-title w-100" id="removeAllLabel">Odstranit všechna upozornění?</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
								<span aria-hidden="true">&times;</span>
							</button>
							</div>
							<div class="modal-body d-flex">
								<a class="btn btn-success btn-sm mx-auto" href="'.SITE_ROOT.'message_remove.php?message_id=all">Potvrdit</a>
							</div>
						</div>
					</div>
				</div>';

				$current_number = $page;
				if ($total_pages == 1) {
					$prev_class = 'd-none';
					$next_class = 'd-none';
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
							<li class='page-item'><a id='1' class='page-link page'><i class='fas fa-fast-backward'></i></a></li>
							<li class='page-item $prev_class'><a id='".$prev_number."' class='page-link page'>".$prev_number."</a></li>
							<li class='page-item $current_class'><a id='".$current_number."' class='page-link page'><u>".$current_number."</u></a></li>
							<li class='page-item $next_class'><a id='".$next_number."' class='page-link page'>".$next_number."</a></li>
							<li class='page-item'><a id='$total_pages' class='page-link page'><i class='fas fa-fast-forward'></i></a></li>
						</ul>
					</nav>
				</div>";
			}
?>