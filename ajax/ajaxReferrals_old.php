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
$search = "SELECT user_id, username, register_date, bought_items, ref_money_given FROM users WHERE ref_by = $user_id AND bought_items > 0";
$total_pages_sql = "SELECT COUNT(*) FROM users WHERE ref_by = $user_id AND bought_items > 0";

if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= "  ORDER BY register_date DESC";
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
				echo "<p class='font-weight-bold text-center'>Zatím jste nikoho nepozval!</p>";
			} else {
				echo '<div class="table-responsive">
				<table class="table table-striped table-hover table-sm">
					<thead>
						<tr>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="username" style="font-family: \'Baloo\', cursive;">Uživatel</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="register_date" style="font-family: \'Baloo\', cursive;">Registrován</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bought_items" style="font-family: \'Baloo\', cursive;">Koupeno položek</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="ref_money_given" style="font-family: \'Baloo\', cursive;" data-toggle="tooltip" title="K dispozici pro výběr">Celkem získáno</a></u></th>
						</tr>
					</thead>
				<tbody>';
				while ($referral_row = mysqli_fetch_array($search_query)) {
					echo $post_row = sprintf("
						<tr class='h-100'>
							<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='font-weight-bold text-primary'>%s</u></td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%dx</td>
							<td class='my-auto'>%d Kč</td>
						</tr>
						",
						$referral_row['user_id'],
						$referral_row['username'],
						date('d.m.Y H:i', strtotime($referral_row['register_date'])),
						$referral_row['bought_items'],
						$referral_row['ref_money_given']);
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