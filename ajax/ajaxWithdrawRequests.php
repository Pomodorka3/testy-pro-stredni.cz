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
$search = "SELECT w.withdraw_id, w.withdraw_from, w.withdraw_sum, w.withdraw_description, w.withdraw_status, w.withdraw_date, u.username, u.bank_number, u.last_withdraw FROM withdraw w, users u WHERE w.withdraw_from = u.user_id";
$total_pages_sql = "SELECT COUNT(*) FROM withdraw w, users u WHERE w.withdraw_from = u.user_id";
if (!empty($_POST['username'])) {
	$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%'";
	$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%'";
}
if (!empty($_POST['withdrawSum'])) {
	$search .= " AND w.withdraw_sum COLLATE utf8_general_ci LIKE '%".$_POST['withdrawSum']."%'";
	$total_pages_sql .= " AND w.withdraw_sum COLLATE utf8_general_ci LIKE '%".$_POST['withdrawSum']."%'";
}
if (!empty($_POST['bankAccount'])) {
	$search .= " AND u.bank_number COLLATE utf8_general_ci LIKE '%".$_POST['bankAccount']."%'";
	$total_pages_sql .= " AND u.bank_number COLLATE utf8_general_ci LIKE '%".$_POST['bankAccount']."%'";
}
if (isset($_POST['statusFilter'])) {
	$search .= " AND w.withdraw_status=".$_POST['statusFilter'];
	$total_pages_sql .= " AND w.withdraw_status=".$_POST['statusFilter'];
}

if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY w.withdraw_status ASC, w.withdraw_date ASC";
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
	echo "<p class='font-weight-bold text-center'>Nebyly nalezeny žádné žádosti o výběr!</p>";
} else {
	echo $output = '<div class="table-responsive">
	<table class="table table-striped table-hover table-sm">
		<thead>
			<tr>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="w.withdraw_date" style="font-family: \'Baloo\', cursive;">Datum</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="w.withdraw_from" style="font-family: \'Baloo\', cursive;">Žadatel</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="w.withdraw_sum" style="font-family: \'Baloo\', cursive;">Částka</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="w.withdraw_description" style="font-family: \'Baloo\', cursive;">Popis</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u.bank_number" style="font-family: \'Baloo\', cursive;">Bank. účet</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u.last_withdraw" style="font-family: \'Baloo\', cursive;">Poslední výběr</a></u></th>												
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="w.withdraw_status" style="font-family: \'Baloo\', cursive;">Status</a></u></th>						
				<th scope="col"></th>
			</tr>
		</thead>
	<tbody>';
	$i = 0;
	while ($withdraw_row = mysqli_fetch_array($search_query)) {
		$status = '';
		$accept = $deny = '';
		if ($withdraw_row['withdraw_status'] == 0) {
			$status = "<span class='text-primary font-weight-bold'><i class='fas fa-circle-notch text-primary' data-toggle='tooltip' title='Zpracovává se'></i></span>";
			$accept = "<a class='mx-2' href='".SITE_ROOT."withdraw_requests_action.php?accept_id=".$withdraw_row['withdraw_id']."'><i class='fas fa-check text-success' data-toggle='tooltip' title='Potvrdit'></i></a>";
			$deny = "<a class='mx-2' href='".SITE_ROOT."withdraw_requests_action.php?decline_id=".$withdraw_row['withdraw_id']."'><i class='fas fa-times text-danger' data-toggle='tooltip' title='Odmítnout'></i></a>";
			//Show time of last user's withdraw + days counter since last withdraw
			if ($withdraw_row['last_withdraw'] == '0000-00-00 00:00:00') {
				$daysDifference = '';
				$lastWithdraw = '-';
			} else {
				$lastWithdraw = date('d.m.Y H:i', strtotime($withdraw_row['last_withdraw']));
				$daysDifference = date('j', time() - strtotime($withdraw_row['last_withdraw']));
				$daysDifference -= 1;
				if ($daysDifference == 1) {
					$daysDifference = '(před 1 dnem)';
				} else {
					$daysDifference = '(před '.$daysDifference.' dny)';
				}
			}
			//--------------------------------------------------------------------
		}
		if ($withdraw_row['withdraw_status'] == 1) {
			$status = "<span class='text-success font-weight-bold'><i class='fas fa-circle text-success' data-toggle='tooltip' title='Potvrzeno'></i></span>";
			$completed = $completed + $withdraw_row['withdraw_sum'];
			$daysDifference = '';
			$lastWithdraw = '-';
		}
		if ($withdraw_row['withdraw_status'] == 2) {
			$status = "<span class='text-danger font-weight-bold'><i class='fas fa-circle text-danger' data-toggle='tooltip' title='Odmítnuto'></i></span>";
			$daysDifference = '';
			$lastWithdraw = '-';
		}
		echo $post_row = sprintf("
		<tr class='h-100'>
			<td class='my-auto'>%s</td>
			<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='text-primary font-weight-bold'><u>%s</u></a></td>
			<td class='my-auto'>%d Kč</td>
			<td class='my-auto'>%s</td>
			<td class='my-auto'>%s</td>
			<td class='my-auto'>%s %s</td>
			<td class='my-auto'>%s</td>
			<td class='text-center'>%s</td>
		</tr>
		",
		date('d.m.Y H:i', strtotime($withdraw_row['withdraw_date'])),
		$withdraw_row['withdraw_from'],
		$withdraw_row['username'],
		$withdraw_row['withdraw_sum'],
		$withdraw_row['withdraw_description'],
		$withdraw_row['bank_number'],
		$lastWithdraw,
		$daysDifference,
		$status,
		$accept.$deny);
	}
	echo $table_end .= '</tbody></table></div>';
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