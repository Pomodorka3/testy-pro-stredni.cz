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
$search = "SELECT withdraw_id, withdraw_sum, withdraw_description, withdraw_status, withdraw_date FROM withdraw WHERE withdraw_from = $user_id";
$total_pages_sql = "SELECT COUNT(*) FROM withdraw WHERE withdraw_from = $user_id";

if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= "  ORDER BY withdraw_date DESC";
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
$items_per_page = 8;
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
				echo "<p class='font-weight-bold text-center'>Zatím nemáte žádné žádosti o výběr!</p>";
			} else {
				echo '<div class="table-responsive">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="withdraw_date" style="font-family: \'Baloo\', cursive;">Datum</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="withdraw_sum" style="font-family: \'Baloo\', cursive;">Částka</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="withdraw_description" style="font-family: \'Baloo\', cursive;">Popis</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="withdraw_status" style="font-family: \'Baloo\', cursive;">Status</a></u></th>
							<th scope="col"></th>
						</tr>
					</thead>
				<tbody>';
				while ($withdraw_row = mysqli_fetch_array($search_query)) {
					$cancel = '';
					if ($withdraw_row['withdraw_status'] == 0) {
						$status = "<span class='text-primary font-weight-bold'><i class='fas fa-circle-notch text-primary' data-toggle='tooltip' title='Zpracovává se'></i></span>";
						$cancel = "<a href='".SITE_ROOT."withdraw_action.php?cancel_id=".$withdraw_row['withdraw_id']."' data-toggle='tooltip' title='Zrušit'><i class='fas fa-times text-danger'></i></a>";
					}
					if ($withdraw_row['withdraw_status'] == 1) {
						$status = "<span class='text-success font-weight-bold'><i class='fas fa-circle text-success' data-toggle='tooltip' title='Potvrzeno'></i></span>";
					}
					if ($withdraw_row['withdraw_status'] == 2) {
						$status = "<span class='text-danger font-weight-bold'><i class='fas fa-circle text-danger' data-toggle='tooltip' title='Zamítnuto'></i></span>";
					}
					$date = strtotime($withdraw_row['withdraw_date']);
					echo $post_row = sprintf("
						<tr class=' h-100'>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%d Kč</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='text-center'>%s</td>
						</tr>
						",
						date('d.m.Y H:i', $date),
						$withdraw_row['withdraw_sum'],
						$withdraw_row['withdraw_description'],
						$status,
						$cancel);
				}
				echo '</tbody></table></div>';
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