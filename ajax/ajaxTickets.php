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
$search = "SELECT DISTINCT t.ticket_id, t.ticket_title, t.ticket_created, t.ticket_answered, t.ticket_type, u.username, u.user_id FROM tickets t, users u WHERE t.ticket_visible = '1' AND u.user_id = t.ticket_createdby";
$total_pages_sql = "SELECT COUNT(*) FROM tickets t, users u WHERE t.ticket_visible = '1' AND u.user_id = t.ticket_createdby";
if (!empty($_POST['search'])) {
	$search .= " AND t.ticket_title COLLATE utf8_general_ci LIKE '%".$_POST['search']."%'";
	$total_pages_sql .= " AND t.ticket_title COLLATE utf8_general_ci LIKE '%".$_POST['search']."%'";
}
if (isset($_POST['ticketType'])) {
	$search .= " AND t.ticket_type = '".$_POST['ticketType']."'";
	$total_pages_sql .= " AND t.ticket_type = '".$_POST['ticketType']."'";
}
if (($_POST['column_name'] != 'default') && (isset($_POST['order'])) && (isset($_POST['column_name']))) {
	$search .= " ORDER BY t.".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY t.ticket_answered ASC, t.ticket_created DESC";
}
if (isset($_POST['page'])) {
	$page = $_POST['page'];
	//echo "<b>Page</b>: ".$page."<br>";
} else {
	$page = 1;
	//echo "<b>Page</b>: ".$page."<br>";
}

$items_per_page = 10;
$offset = ($page-1)*$items_per_page;
//echo "<b>COUNT SQL: </b>".$total_pages_sql;
$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
$total_pages = ceil($total_rows/$items_per_page);
$search .= " LIMIT $offset, $items_per_page";

//echo "<br><b>Search SQL</b>: ".$search;
//echo "<br><b>Total pages</b>: ".$total_pages;
$search_query = mysqli_query($connect, $search);

			if (mysqli_num_rows($search_query) == 0) {
				echo "<p class='font-weight-bold text-center'>Nebyly nalezeny žádné tikety!</p>";
			} else {
				$i = 0;
				echo $output = '<div class="table-responsive">
				<table class="table table-sm table-striped table-hover">
				  <thead>
				    <tr>
				      <th scope="col">
				      	<u><a class="column_sort" data-order="'.$order.'" id="ticket_id" style="font-family: \'Baloo\', cursive; font-size: 16px;">ID</a></u>
				      </th>
				      <th scope="col">
				      	<u><a class="column_sort" data-order="'.$order.'" id="ticket_type" style="font-family: \'Baloo\', cursive; font-size: 16px;">Typ</a></u>
				      </th>
				      <th scope="col">
				      	<u><a class="column_sort" data-order="'.$order.'" id="ticket_title" style="font-family: \'Baloo\', cursive; font-size: 16px;">Název</a></u>
				      </th>
				      <th scope="col">
				     	<u><a class="column_sort" data-order="'.$order.'" id="ticket_createdby" style="font-family: \'Baloo\', cursive; font-size: 16px;">Vytvořil</a></u>
				      </th>
				      <th scope="col">
				      	<u><a class="column_sort" data-order="'.$order.'" id="ticket_created" style="font-family: \'Baloo\', cursive; font-size: 16px;">Datum</a></u>
				      </th>
				      <th scope="col">
				      	<u><a class="column_sort" data-order="'.$order.'" id="ticket_answered" style="font-family: \'Baloo\', cursive; font-size: 16px;">Status</a></u>
				      </th>
				      <th scope="col"></th>
				    </tr>
				  </thead>
				<tbody>';
				while ($row = mysqli_fetch_array($search_query)) {
					$i++;
					if (user_in_group("Administrator", $user_id)) {
						$removeButton = '<a data-toggle="modal" data-target="#centralModalSm'.$i.'"><i class="fas fa-trash-alt text-danger mr-2" data-toggle="tooltip" title="Odstranit"></i></a>';
						$removeModal = '
						<div class="modal fade" id="centralModalSm'.$i.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel'.$i.'" aria-hidden="true">
							<div class="modal-dialog modal-center" role="document">
							<div class="modal-content">
								<div class="modal-header">
								<h4 class="modal-title w-100" id="myModalLabel'.$i.'">Odstranit vybraný tiket?</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
									<span aria-hidden="true">&times;</span>
								</button>
								</div>
								<div class="modal-body d-flex">
									<a class="btn btn-success btn-sm mx-auto" href="'.SITE_ROOT.'ticket_action.php?action=removeTicket&for='.$row['ticket_id'].'">Potvrdit</a>
								</div>
							</div>
							</div>
						</div>';
					}
					$ticket_type = '';
					if ($row['ticket_type'] == 1) {
						$ticket_type = '<i class="fas fa-bug orange-text" data-toggle="tooltip" title="Bug"></i>';
					} elseif ($row['ticket_type'] == 2) {
						$ticket_type = '<i class="fas fa-lightbulb amber-text" data-toggle="tooltip" title="Návrh"></i>';
					} else {
						$ticket_type = '<i class="fas fa-question-circle text-primary" data-toggle="tooltip" title="Otázka"></i>';
					}
					$status = '';
					if ($row['ticket_answered'] == 0) {
						$status = '<i class="fas fa-lock-open text-success ml-2" data-toggle="tooltip" title="Otevřen"></i>';
					} else {
						$status = '<i class="fas fa-lock text-danger ml-2" data-toggle="tooltip" title="Uzavřen"></i>';
					}
					$date = strtotime($row['ticket_created']);
					echo $post_row = sprintf("
						<tr>
					      <th scope='row'>#%d</th>
					      <td>%s</td>
					      <td>%s</td>
					      <td><a class='text-primary font-weight-bold' href='".SITE_ROOT."profile_show.php?profile_id=%d'>%s</a></td>
					      <td>%s</td>
					      <td>%s</td>
					      <td><a href='".SITE_ROOT."ticket_show.php?ticket_id=%s' data-toggle='tooltip' title='Zobrazit'><i class='fas fa-eye text-primary'></i></a></td>
						  <td>%s</td>
						</tr>
						%s
						",
						$row['ticket_id'],
						$ticket_type,
						$row['ticket_title'],
						$row['user_id'],
						$row['username'],
						date('d.m.Y H:i', $date),
						$status,
						$row['ticket_id'],
						$removeButton,
						$removeModal);
				}
				echo $table_end .= '</tbody></table></div>';
				//Pagination
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