<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user(array("Support", "Administrator", "Main administrator"));

$output = '';
$column_name = $_POST['column_name'];
$order = $_POST['order'];
if ($order == 'desc' || $order == 'DESC' ) {
	$order = 'ASC';
} else {
	$order = 'DESC';
}

$user_id =  $_SESSION['user_id'];
$search = "SELECT sc.id, sc.user_id, sc.request_date, sc.last_setdate, u.username, sch1.school_id schoolid_from, sch1.school_name schoolname_from, sch2.school_id schoolid_to, sch2.school_name schoolname_to FROM school sch1, school sch2, school_change sc, users u WHERE sc.user_id = u.user_id AND sc.change_school_id_from = sch1.school_id AND sc.change_school_id_to = sch2.school_id";
$total_pages_sql = "SELECT COUNT(*) FROM school sch1, school sch2, school_change sc, users u WHERE sc.user_id = u.user_id AND sc.change_school_id_from = sch1.school_id AND sc.change_school_id_to = sch2.school_id";

if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY sc.request_date ASC";
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
				echo '<p class="font-weight-bold text-center">Zatím nejsou žádné žádosti o změnu školy!</p>';
			} else {
				$i=0;
				echo $output = '<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u.username" style="font-family: \'Baloo\', cursive;">Uživatel</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sch1.school_name" style="font-family: \'Baloo\', cursive;">Původní škola</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sch2.school_name" style="font-family: \'Baloo\', cursive;">Změna na</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sc.request_date" style="font-family: \'Baloo\', cursive;">Datum</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="sc.last_setdate" style="font-family: \'Baloo\', cursive;">Poslední změna školy</a></u></th>
							<th scope="col"></th>
						</tr>
					</thead>
				<tbody>';
				while ($post = mysqli_fetch_array($search_query)) {

					$daysDifference = date('j', strtotime($post['request_date']) - strtotime($post['last_setdate']));
					$daysDifference -= 1;
					if ($daysDifference == 1) {
						$daysDifference = 'před 1 dnem';
					} else {
						$daysDifference = 'před '.$daysDifference.' dny';
					}
					$i++;
					echo $post_row = sprintf("
					<tr class=' h-100'>
						<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='text-primary font-weight-bold'>%s</td>
						<td class='my-auto'><a href='".SITE_ROOT."school_info.php?school_id=%d' class='text-secondary font-weight-bold'>%s</td>
						<td class='my-auto'><a href='".SITE_ROOT."school_info.php?school_id=%d' class='text-secondary font-weight-bold'>%s</td>
						<td class='my-auto'>%s</td>
						<td class='my-auto'>%s (%s)</td>
						<td><a class='mx-2' href='".SITE_ROOT."school_check_action.php?accept_id=%d'><i class='fas fa-check text-success' data-toggle='tooltip' title='Potvrdit'></i></a>
						<a class='mx-2' data-toggle='modal' data-target='#declineModal%d'><i class='fas fa-times text-danger' data-toggle='tooltip' title='Odmítnout'></i></a></td>
					</tr>
						",
						$post['user_id'],
						$post['username'],
						$post['schoolid_from'],
						$post['schoolname_from'],
						$post['schoolid_to'],
						$post['schoolname_to'],
						date('d.m.Y H:i', strtotime($post['request_date'])),
						date('d.m.Y H:i', strtotime($post['last_setdate'])),
						$daysDifference,
						$post['id'],
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
					                  <form action='".SITE_ROOT."school_check_action.php?decline_id=".$post2['id']." class='text-center' method='post'>
					                    <textarea name='decline_reason' id='decline_reason' rows='6' class='form-control rounded mb-4' maxlength='1000' autocomplete='off' required placeholder=\"Důvod: ('time' = Školu lze změnit nejdříve po uplynutí 2 týdnů od chvíle původního výběru)\"></textarea>
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