<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user(array("Support", "Administrator", "Main administrator"));

$column_name = $_POST['column_name'];
$order = $_POST['order'];
if ($order == 'desc' || $order == 'DESC' ) {
	$order = 'ASC';
} else {
	$order = 'DESC';
}

$user_id =  $_SESSION['user_id'];

$search = "SELECT sch.school_id, sch.school_name, sch.school_id, u1.username, u1.user_id, u2.user_id checkedby_user_id, u2.username checkedby_username, d.district_name, c.city_name FROM school sch, users u1, users u2, district d, city c WHERE sch.checked_by = u2.user_id AND sch.added_by = u1.user_id AND sch.district_id = d.district_id AND d.city_id = c.city_id AND sch.visible = 1";
$total_pages_sql = "SELECT COUNT(*) FROM school sch, users u, district d, city c WHERE sch.added_by = u.user_id AND sch.district_id = d.district_id AND d.city_id = c.city_id";
if (!empty($_POST['schoolName'])) {
	$search .= " AND sch.school_name COLLATE utf8_general_ci LIKE '%".trim($_POST['schoolName'])."%'";
	$total_pages_sql .= " AND sch.school_name COLLATE utf8_general_ci LIKE '%".trim($_POST['schoolName'])."%'";
}
if (!empty($_POST['cityName'])) {
	$search .= " AND c.city_name COLLATE utf8_general_ci LIKE '%".trim($_POST['cityName'])."%'";
	$total_pages_sql .= " AND c.city_name COLLATE utf8_general_ci LIKE '%".trim($_POST['cityName'])."%'";
}
if (!empty($_POST['districtName'])) {
	$search .= " AND d.district_name COLLATE utf8_general_ci LIKE '%".trim($_POST['districtName'])."%'";
	$total_pages_sql .= " AND d.district_name COLLATE utf8_general_ci LIKE '%".trim($_POST['districtName'])."%'";
}
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

if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY sch.school_id ASC";
}

$items_per_page = 10;
$offset = ($page-1)*$items_per_page;
if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<b>COUNT SQL: </b>".$total_pages_sql."<br>";
}
$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
$total_pages = ceil($total_rows/$items_per_page);
if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<b>TOTAL_ROWS SQL: </b>".$total_rows;
}
$search .= " LIMIT $offset, $items_per_page";

if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<br>".$search;
}

$search_query = mysqli_query($connect, $search);

			if (mysqli_num_rows($search_query) == 0) {
				echo "<p class='font-weight-bold text-center'>Nebyly nalezeny žádné školy!</p>";
			} else {
				$i = 0;
				echo $output_start = '<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sch.school_id" style="font-family: \'Baloo\', cursive;">ID</a></u></th>
									<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sch.school_name" style="font-family: \'Baloo\', cursive;">Název</a></u></th>
									<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="c.city_name" style="font-family: \'Baloo\', cursive;">Město</a></u></th>
									<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="d.district_name" style="font-family: \'Baloo\', cursive;">Čtvrť</a></u></th>
									<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.username" style="font-family: \'Baloo\', cursive;">Přidal</a></u></th>
									<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u2.username" style="font-family: \'Baloo\', cursive;">Potvrdil</a></u></th>
									<th scope="col" style="font-family: \'Baloo\', cursive;">Členů</th>
									<th scope="col" style="font-family: \'Baloo\', cursive;">Testů na prodej</th>
									<th scope="col" style="font-family: \'Baloo\', cursive;">Celkem peněz</th>
									<th scope="col"></th>
								</tr>
							</thead>
							<tbody>';
				while ($post = mysqli_fetch_array($search_query)) {
					$i++;
					$members = sprintf("SELECT COUNT(*) FROM users WHERE school_id = '%d';",
					mysqli_real_escape_string($connect, $post['school_id']));
					$members_query = mysqli_query($connect, $members);
					$members_row = mysqli_fetch_row($members_query);

					$selling_items = sprintf("SELECT COUNT(*) FROM shop WHERE school_id = '%d' AND checked = 1 AND visible = 1;",
					mysqli_real_escape_string($connect, $post['school_id']));
					$selling_items_query = mysqli_query($connect, $selling_items);
					$selling_items_row = mysqli_fetch_row($selling_items_query);

					$total_balance = sprintf("SELECT balance FROM users WHERE school_id = '%d';",
					mysqli_real_escape_string($connect, $post['school_id']));
					$total_balance_query = mysqli_query($connect, $total_balance);
					$total_users_balance = 0;

					while ($total_balance_row = mysqli_fetch_row($total_balance_query)) {
						$total_users_balance += $total_balance_row[0];
					}
					if (user_in_group("Main administrator", $user_id)) {
						$removeButton = "<a class='mx-2' data-toggle='modal' data-target='#removeModal".$i."'><i class='fas fa-trash-alt text-danger' data-toggle='tooltip' title='Odstranit'></i></a>";
						$removeModal = "<div class='modal fade' id='removeModal".$i."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel".$i."' aria-hidden='true'>
						<div class='modal-dialog modal-center' role='document'>
						  <div class='modal-content'>
							<div class='modal-header'>
							  <h4 class='modal-title w-100' id='removeModalLabel".$i."'>Odstranit vybranou školu?</h4>
							  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
								<span aria-hidden='true'>&times;</span>
							  </button>
							</div>
							<div class='modal-body d-flex'>
							  <a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."school_check_action.php?action=removeSchool&school_id=".$post['school_id']."'>Potvrdit</a>
							</div>
						  </div>
						</div>
					  </div>";
					}
					echo $post_row_start = sprintf("
						<tr class=' h-100'>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
							<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%d</td>
							<td class='my-auto'><a class='mx-2' href='".SITE_ROOT."school_info.php?school_id=%d'><i class='fas fa-info text-primary' data-toggle='tooltip' title='Info'></i></a>
							%s</td>
							%s
							", 
							$post['school_id'],
							$post['school_name'],
							$post['city_name'],
							$post['district_name'],
							$post['user_id'],
							$post['username'],
							$post['checkedby_user_id'],
							$post['checkedby_username'],
							$members_row[0],
							$selling_items_row[0],
							$total_users_balance,
							$post['school_id'],
							$removeButton,
							$removeModal);
				
					$post_row_end .= "</tr>";
					echo $post_row_end;
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