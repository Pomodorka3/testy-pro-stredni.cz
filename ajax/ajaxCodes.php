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

$search = "SELECT c.code_id, c.code, c.code_type, u.username code_generatedby, u.user_id code_generatedby_userid, u2.username code_activatedby, u2.user_id code_activatedby_userid, c.code_created, c.code_expiration, c.code_used, c.code_activated, c.code_value FROM codes c, users u, users u2 WHERE c.code_generatedby = u.user_id AND c.code_activatedby = u2.user_id";
$total_pages_sql = "SELECT COUNT(*) FROM codes c, users u, users u2 WHERE c.code_generatedby = u.user_id AND c.code_activatedby = u2.user_id";
if (!empty($_POST['searchCode'])) {
	$search .= " AND c.code COLLATE utf8_general_ci LIKE '%".trim($_POST['searchCode'])."%'";
	$total_pages_sql .= " AND c.code COLLATE utf8_general_ci LIKE '%".trim($_POST['searchCode'])."%'";
}
if (!empty($_POST['searchCreatedby'])) {
	$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".trim($_POST['searchCreatedby'])."%'";
	$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".trim($_POST['searchCreatedby'])."%'";
}
if (!empty($_POST['searchActivatedBy'])) {
	$search .= " AND c.code_used = 1 AND u2.username COLLATE utf8_general_ci LIKE '%".trim($_POST['searchActivatedBy'])."%'";
	$total_pages_sql .= " AND c.code_used = 1 AND u2.username COLLATE utf8_general_ci LIKE '%".trim($_POST['searchActivatedBy'])."%'";
}
if (!empty($_POST['activated'])) {
	$search .= " AND c.code_used = 1";
	$total_pages_sql .= " AND c.code_used = 1";
}
/*if (isset($_POST['teacher'])) {
	$teacherFilter = implode("','", $_POST['teacher']);
	$search .= " AND s.teacher = '".$teacherFilter."'";
	$total_pages_sql .= " AND s.teacher = '".$teacherFilter."'";
}
if (isset($_POST['itemType'])) {
	$search .= " AND s.item_type = '".$_POST['itemType']."'";
	$total_pages_sql .= " AND s.item_type = '".$_POST['itemType']."'";
}*/
if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY c.code_id ASC";
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
$items_per_page = 10;
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
				echo "<p class='font-weight-bold text-center'>Nebyly nalezeny žádné kódy!</p>";
			} else {
				echo $output = '<div class="table-responsive">
				<table class="table table-hover table-striped table-sm">
					<thead>
						<tr>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_id" style="font-family: \'Baloo\', cursive;">#</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code" style="font-family: \'Baloo\', cursive;">Kód</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_type" style="font-family: \'Baloo\', cursive;">Typ</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_value" style="font-family: \'Baloo\', cursive;">Hodnota</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_generatedby" style="font-family: \'Baloo\', cursive;">Vytvořil</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_created" style="font-family: \'Baloo\', cursive;">Datum vytvoření</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_expiration" style="font-family: \'Baloo\', cursive;">Expirace</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_used" style="font-family: \'Baloo\', cursive;">Aktivován</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_activatedby" style="font-family: \'Baloo\', cursive;">Aktivoval</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="code_activated" style="font-family: \'Baloo\', cursive;">Datum aktivace</a></u></th>
							<th scope="col"></th>
						</tr>
					</thead>
				<tbody>';
				$i = 0;
				while ($row = mysqli_fetch_array($search_query)) {
					$i++;
					$code_created = strtotime($row['code_created']);
					if ($row['code_used'] == 0) {
						$row['code_used'] = '<i class="fas fa-times text-danger"></i>';
						$row['code_activatedby'] = '-';
						$row['code_activated'] = '-';
						//$removeButton = '<a href="admin_codes_action.php?action=removeCode&code_id='.$row['code_id'].'"><i class="fas fa-trash-alt text-danger mr-2"></i></a>';
						$removeButton = '<a data-toggle="modal" data-target="#removeModal'.$i.'"><i class="fas fa-trash-alt text-danger mr-2" data-toggle="tooltip" title="Odstranit"></i></a>';
						$removeModal = '
						<div class="modal fade" id="removeModal'.$i.'" tabindex="-1" role="dialog" aria-labelledby="removeModalLabel'.$i.'" aria-hidden="true">
							<div class="modal-dialog modal-center" role="document">
								<div class="modal-content">
									<div class="modal-header">
									<h4 class="modal-title w-100" id="removeModalLabel'.$i.'">Odstranit vybraný kód?</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
										<span aria-hidden="true">&times;</span>
									</button>
									</div>
									<div class="modal-body d-flex">
										<a class="btn btn-success btn-sm mx-auto" href="'.SITE_ROOT.'admin_codes_action.php?action=removeCode&code_id='.$row['code_id'].'">Potvrdit</a>
									</div>
								</div>
							</div>
						</div>';
					} else {
						$row['code_used'] = '<i class="fas fa-check text-success"></i>';
						$row['code_activatedby'] = '<a href="'.SITE_ROOT.'profile_show.php?profile_id='.$row['code_activatedby_userid'].'" class="font-weight-bold text-primary">'.$row['code_activatedby'].'</a>';
						$removeButton = '';
						$removeModal = '';
						$code_activated = strtotime($row['code_activated']);
						$row['code_activated'] = date('d.m.Y H:i', $code_activated);
					}
					if ($row['code_type'] == 'balance') {
						$code_type = 'Peníze';
						$code_value = $row['code_value']." Kč";
					} elseif ($row['code_type'] == 'vip') {
						$code_type = 'VIP';
						if ($row['code_value'] == 1) {
							$code_value = $row['code_value']." den";
						} elseif ($row['code_value'] < 5) {
							$code_value = $row['code_value']." dny";
						} else {
							$code_value = $row['code_value']." dní";
						}
					}
					
					if ($row['code_expiration'] < date('Y-m-d H:i:s')) {
						$code_expiration_date = strtotime($row['code_expiration']);
						$row['code_expiration'] = date('d.m.Y H:i', $code_expiration_date);
						$code_expiration = '<span class="text-danger">'.$row['code_expiration'].'</span>';
					} else {
						$code_expiration_date = strtotime($row['code_expiration']);
						$row['code_expiration'] = date('d.m.Y H:i', $code_expiration_date);
						$code_expiration = '<span class="text-success">'.$row['code_expiration'].'</span>';
					}

					echo $row_row = sprintf("
						<tr class=' h-100'>
							<td class='my-auto'>%d</td>
							<td class='my-auto'><span class='codeSecurity' style='color:#000;'>%s</span></td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'><span class='font-weight-bold'>%s</span></td>
							<td class='my-auto'><a href='".SITE_ROOT."profile_show.php?profile_id=%d' class='font-weight-bold text-primary'>%s</a></td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td>%s</td>
						</tr>
						%s
						",
						$row['code_id'],
						$row['code'],
						$code_type,
						$code_value,
						$row['code_generatedby_userid'],
						$row['code_generatedby'],
						date('d.m.Y H:i', $code_created),
						$code_expiration,
						$row['code_used'],
						$row['code_activatedby'],
						$row['code_activated'],
						$removeButton,
						$removeModal);
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