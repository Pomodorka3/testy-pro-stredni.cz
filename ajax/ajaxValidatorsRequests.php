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
$search = "SELECT u.username, vr.request_id, vr.request_from, vr.request_biography, vr.request_date, vr.request_school, s.school_name FROM users u, validators_requests vr, school s WHERE vr.checked = 0 AND vr.request_from = u.user_id AND vr.request_school = s.school_id";
$total_pages_sql = "SELECT COUNT(*) FROM users u, validators_requests vr, school s WHERE vr.checked = 0 AND vr.request_from = u.user_id AND vr.request_school = s.school_id";
if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY vr.request_date DESC";
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
$search_query2 = mysqli_query($connect, $search);
	
				if (mysqli_num_rows($search_query) == 0) {
					echo "<p class='font-weight-bold text-center'>Nejsou žádné žádosti k potvrzení!</p>";
				} else {
					$i = 0;
					echo $output = '<div class="table-responsive">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="vr.request_from" style="font-family: \'Baloo\', cursive;">Uživatel</a></u></th>
								<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="vr.request_biography" style="font-family: \'Baloo\', cursive;">Životopis</a></u></th>
								<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="s.school_name" style="font-family: \'Baloo\', cursive;">Škola</a></u></th>
								<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="vr.request_date" style="font-family: \'Baloo\', cursive;">Datum</a></u></th>
								<th scope="col"></th>
							</tr>
						</thead>
					<tbody>';
					while ($check = mysqli_fetch_array($search_query)) {
						$i++;
						$request_date = strtotime($check['request_date']);
						echo $post_row = sprintf('
						<tr class=" h-100">
							<td class="my-auto"><a class="text-primary font-weight-bold" href="'.SITE_ROOT.'profile_show.php?profile_id=%d">%s</td>
							<td class="my-auto">%s</td>
							<td class="my-auto"><a href="'.SITE_ROOT.'school_info.php?school_id=%d" class="font-weight-bold text-primary">%s</a></td>
							<td class="my-auto">%s</td>
							<td><a href="'.SITE_ROOT.'validators_requests_action.php?confirm_id=%d" class="mx-2"><i class="fas fa-check text-success" data-toggle="tooltip" title="Potvrdit"></i></a>
							<a data-toggle="modal" data-target="#declineModal%d" class="mx-2"><i class="fas fa-times text-danger" data-toggle="tooltip" title="Odmítnout"></i></a></td>
						</tr>',
						$check['request_from'],
						$check['username'],
						$check['request_biography'],
						$check['request_school'],
						$check['school_name'],
						date('d.m.Y H:i', $request_date),
						$check['request_id'],
						$i);
					}
					echo $table_end .= '</tbody></table>';
					$i = 0;
					while ($check2 = mysqli_fetch_array($search_query2)) {
						$i++;
						printf('<div class="modal fade" id="declineModal%d" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-center" role="document">
						  <div class="modal-content">
							<div class="modal-header">
							  <h4 class="modal-title w-100" id="declineModalLabel%d">Odmítnout žádost</h4>
							  <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
								<span aria-hidden="true">&times;</span>
							  </button>
							</div>
							<div class="modal-body">
							  <form action="'.SITE_ROOT.'validators_requests_action.php?decline_id=%d" class="text-center" method="post">
								<textarea name="decline_reason" id="decline_reason" rows="6" class="form-control rounded mb-4" maxlength="1000" autocomplete="off" required placeholder="Důvod: (\'requirements\' = Nevyhovujete požadavkům; \'activity\' = Vaše aktivita není dostatečná)"></textarea>
								<button type="submit" class="btn btn-danger btn-sm d-flex mx-auto mt-3">Submit</button>
							  </form>
							</div>
						  </div>
						</div>
					  </div>',
					$i,
					$i,
					$check2['request_id']);
					}
				}
			
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
?>