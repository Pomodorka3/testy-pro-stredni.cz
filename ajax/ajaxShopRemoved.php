<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user(array("Administrator", "Main administrator"));

$output = '';
$column_name = $_POST['column_name'];
$order = $_POST['order'];
if ($order == 'desc' || $order == 'DESC' ) {
	$order = 'ASC';
} else {
	$order = 'DESC';
}

$user_id =  $_SESSION['user_id'];
$search = "SELECT s.item_name, s.item_description, s.item_type, s.item_answers, sch.school_name, u1.user_id removedby_userid, u1.username removedby_username, u2.user_id createdby_userid, u2.username createdby_username, srl.removed_id, srl.removed_item, srl.removed_time FROM shop s, users u1, users u2, school sch, shop_remove_log srl WHERE srl.removed_item = s.item_id AND srl.removed_by = u1.user_id AND s.school_id = sch.school_id AND s.item_createdby_userid = u2.user_id";
$total_pages_sql = "SELECT COUNT(*) FROM shop s, users u1, users u2, school sch, shop_remove_log srl WHERE srl.removed_item = s.item_id AND srl.removed_by = u1.user_id AND s.school_id = sch.school_id AND s.item_createdby_userid = u2.user_id";
if (!empty($_POST['itemName'])) {
	$search .= " AND s.item_name COLLATE utf8_general_ci LIKE '%".trim($_POST['itemName'])."%'";
	$total_pages_sql .= " AND s.item_name COLLATE utf8_general_ci LIKE '%".trim($_POST['itemName'])."%'";
}
if (!empty($_POST['schoolName'])) {
	$search .= " AND sch.school_name COLLATE utf8_general_ci LIKE '%".trim($_POST['schoolName'])."%'";
	$total_pages_sql .= " AND sch.school_name COLLATE utf8_general_ci LIKE '%".trim($_POST['schoolName'])."%'";
}
if (!empty($_POST['removedBy'])) {
	$search .= " AND u1.username COLLATE utf8_general_ci LIKE '%".trim($_POST['removedBy'])."%'";
	$total_pages_sql .= " AND u1.username COLLATE utf8_general_ci LIKE '%".trim($_POST['removedBy'])."%'";
}
if (!empty($_POST['itemCreatedby'])) {
	$search .= " AND s.item_createdby_username COLLATE utf8_general_ci LIKE '%".trim($_POST['itemCreatedby'])."%'";
	$total_pages_sql .= " AND s.item_createdby_username COLLATE utf8_general_ci LIKE '%".trim($_POST['itemCreatedby'])."%'";
}
if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY srl.removed_time DESC";
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

			if (mysqli_num_rows($search_query) == 0) {
				echo "<p class='font-weight-bold text-center'>Nebyly nalezeny žádné testy odstraněné Administrátory!</p>";
			} else {
				$i = 0;
				echo $output = '<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="srl.removed_id">#</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="s.item_type">Typ</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="s.item_name">Název</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="s.item_description">Popis</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="createdby_username">Přidal</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="sch.school_name">Škola</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="removedby_username">Odstranil</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" style="font-family: \'Baloo\', cursive;" id="removed_time">Datum</a></u></th>
							<th scope="col"></th>
						</tr>
					</thead>
				<tbody>';
				while ($post = mysqli_fetch_array($search_query)) {
					$i++;
					if (strlen($post['item_description']) < 50) {
						$itemDescription = $post['item_description'];
					} else {
						$itemDescription = substr($post['item_description'], 0, 50);
						$itemDescription .= '<span class="font-weight-bold text-primary" data-toggle="tooltip" title="'.$post['item_description'].'">...</span>';
					}
					$type = ($post['item_type'] == 0) ? "<i class='far fa-square' data-toggle='tooltip' title='Malý test'></i>" : "<i class='fas fa-square' data-toggle='tooltip' title='Velký test'></i>";
					if ($post['item_answers'] == 1) {
						$itemAnswers = ' <i class="fas fa-certificate text-primary" data-toggle="tooltip" title="S odpověďmi na jedničku"></i>';
					} else {
						$itemAnswers = '';
					}
					//s.item_name, sch.school_name, u.username removed_by, u.username created_by, srl.removed_time
					echo $post_row = sprintf("
						<tr class=' h-100'>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'><a class='font-weight-bold text-primary' href='".SITE_ROOT."profile_show.php?profile_id=%d'>%s</a></td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'><a class='font-weight-bold text-primary' href='".SITE_ROOT."profile_show.php?profile_id=%d'>%s</a></td>
							<td class='my-auto'>%s</td>
							<td><a data-toggle='modal' data-target='#centralModalSm%d'><i class='fas fa-trash-restore-alt text-success' data-toggle='tooltip' title='Vrátit'></i></a></td>
						</tr>
						<div class='modal fade' id='centralModalSm%d' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog modal-center' role='document'>
								<div class='modal-content'>
									<div class='modal-header'>
										<h4 class='modal-title w-100' id='myModalLabel%d'>Vrátit vybraný test do obchodu?</h4>
										<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
											<span aria-hidden='true'>&times;</span>
										</button>
									</div>
									<div class='modal-body d-flex'>
										<a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."shop_action_admin.php?action=restore&restore_id=".$post['removed_item']."'>Potvrdit</a>
									</div>
								</div>
							</div>
						</div>
						",
						$post['removed_id'],
						$type.$itemAnswers,
						$post['item_name'],
						$itemDescription,
						$post['createdby_userid'],
						$post['createdby_username'],
						$post['school_name'],
						$post['removedby_userid'],
						$post['removedby_username'],
						date('d.m.Y H:i', strtotime($post['removed_time'])),
						$i,
						$i,
						$i);
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