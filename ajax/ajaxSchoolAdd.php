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
$search = "SELECT sa.sa_id, sa.sa_userid, sa.sa_city, sa.sa_district, sa.sa_school, sa.sa_date, u.username FROM school_add sa, users u WHERE sa.sa_confirmedby = 0 AND u.user_id = sa.sa_userid";
$total_pages_sql = "SELECT COUNT(*) FROM school_add WHERE sa_confirmedby = 0";

if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY sa.sa_date ASC";
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
$items_per_page = 7;
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
	echo "<p class='font-weight-bold text-center'>Zatím nejsou žádné žádosti o přidání školy!</p>";
} else {
	$i=0;
	echo $output = '<div class="table-responsive">
	<table class="table table-hover table-striped">
		<thead>
			<tr>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sa_city" style="font-family: \'Baloo\', cursive;">Město</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sa_district" style="font-family: \'Baloo\', cursive;">Čtvrť</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sa_school" style="font-family: \'Baloo\', cursive;">Škola</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sa_date" style="font-family: \'Baloo\', cursive;">Datum</a></u></th>
				<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sa_userid" style="font-family: \'Baloo\', cursive;">Od</a></u></th>
				<th scope="col"></th>
			</tr>
		</thead>
	<tbody>';
	while ($post = mysqli_fetch_array($search_query)) {
		$check_city = sprintf("SELECT city_id FROM city WHERE city_name = '%s';",
		mysqli_real_escape_string($connect, $post['sa_city']));
		$check_city_query = mysqli_query($connect, $check_city);
		if (mysqli_num_rows($check_city_query) == 0) {
			$city_status = '<i class="fas fa-plus-circle text-success" data-toggle="tooltip" title="Nové město"></i>';
		} else {
			$city_status = '';
		}
		$check_district = sprintf("SELECT district_id FROM district WHERE district_name = '%s';",
		mysqli_real_escape_string($connect, $post['sa_district']));
		$check_district_query = mysqli_query($connect, $check_district);
		if (mysqli_num_rows($check_district_query) == 0) {
			$district_status = '<i class="fas fa-plus-circle text-success" data-toggle="tooltip" title="Nová čtvrť"></i>';
		} else {
			$district_status = '';
		}
		$i++;
		echo $post_row = sprintf("
			<tr class=' h-100'>
				<td class='my-auto'>%s %s</td>
				<td class='my-auto'>%s %s</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td><a href='".SITE_ROOT."school_add_action.php?confirm_id=%d' class='mx-2'><i class='fas fa-check text-success' data-toggle='tooltip' title='Potvrdit'></i></a>
					<a data-toggle='modal' data-target='#declineModal%d' class='mx-2'><i class='fas fa-times text-danger' data-toggle='tooltip' title='Odmítnout'></i></a></td>
			</tr>
			",
			$post['sa_city'],
			$city_status,
			$post['sa_district'],
			$district_status,
			$post['sa_school'],
			date('d.m.Y H:i', strtotime($post['sa_date'])),
			$post['sa_userid'],
			$post['username'],
			$post['sa_id'],
			$i);
	}
	echo $table_end = '</tbody></table></div>';
	$i=0;
	while ($post2 = mysqli_fetch_array($search_query2)) {
		$i++;
		echo "
		<div class='modal fade' id='declineModal".$i."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel".$i."' aria-hidden='true'>
	            <div class='modal-dialog modal-center' role='document'>
	              <div class='modal-content'>
	                <div class='modal-header'>
	                  <h4 class='modal-title w-100' id='declineModalLabel".$i."'>Odmítnout žádost?</h4>
	                  <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
	                    <span aria-hidden='true'>&times;</span>
	                  </button>
	                </div>
	                <div class='modal-body'>
	                  <form action='".SITE_ROOT."school_add_action.php?decline_id=".$post2['sa_id']."' class='text-center' method='post'>
	                    <textarea name='decline_reason' id='decline_reason' rows='6' class='form-control rounded mb-4' maxlength='1000' autocomplete='off' required placeholder=\"Důvod: ('not_exists' = Tato škola neexistuje; 'already' = Tato škola už je v naší databázi)\"></textarea>
	                    <button type='submit' class='btn btn-danger btn-sm d-flex mx-auto mt-3'>Potvrdit</button>
	                  </form>
	                </div>
	              </div>
	            </div>
	          </div>";
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