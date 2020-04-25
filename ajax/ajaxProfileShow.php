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

$user_id = $_POST['profileId'];
$username = sprintf("SELECT username FROM users WHERE user_id = '%d';",
mysqli_real_escape_string($connect, $user_id));
$username_query = mysqli_query($connect, $username);
$username = mysqli_fetch_row($username_query)[0];

$search = "SELECT item_id, item_name, item_description, item_price, likes, dislikes, bought_times, item_type, item_subject, school_class, teacher, item_answers FROM shop WHERE item_createdby_userid = $user_id AND visible = 1 AND checked = 1";
$total_pages_sql = "SELECT COUNT(*) FROM shop WHERE item_createdby_userid = $user_id AND visible = 1 AND checked = 1";
if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY confirmed_date DESC";
}
//$search .= " LIMIT 6";
if (isset($_POST['page'])) {
	$page = $_POST['page'];
	if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
		echo "<span class='font-weight-bold'>Page</span>: ".$page."<br>";
	}
} else {
	$page = 1;
	if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
		echo "<span class='font-weight-bold'>Page</span>: ".$page."<br>";
	}
}
$items_per_page = 8;
$offset = ($page-1)*$items_per_page;
if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<span class='font-weight-bold'>COUNT SQL: </span>".$total_pages_sql;
}
$total_pages_sql_result = mysqli_query($connect, $total_pages_sql);
$total_rows = mysqli_fetch_array($total_pages_sql_result)[0];
$total_pages = ceil($total_rows/$items_per_page);
$search .= " LIMIT $offset, $items_per_page";

if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<br><span class='font-weight-bold'>Search SQL</span>: ".$search;
	echo "<br><span class='font-weight-bold'>Total pages</span>: ".$total_pages;
}
$search_query = mysqli_query($connect, $search);

			if (mysqli_num_rows($search_query) == 0) {
				echo "<p class='font-weight-bold text-center'>Uživatel ".$username." nemá žádné testy na prodej!</p>";
			} else {
				echo $output = '<div class="table-responsive">
				<table class="table table-striped table-hover table-sm">
					<thead>
						<tr>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_subject" style="font-family: \'Baloo\', cursive;">Předmět</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="school_class" style="font-family: \'Baloo\', cursive;">Ročník</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="teacher" style="font-family: \'Baloo\', cursive;">Učitel</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_type" style="font-family: \'Baloo\', cursive;">Typ</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_name" style="font-family: \'Baloo\', cursive;">Název</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_description" style="font-family: \'Baloo\', cursive;">Popis</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="likes" style="font-family: \'Baloo\', cursive;"><i class="far fa-thumbs-up" data-toggle="tooltip" title="Líbí se"></i></a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="dislikes" style="font-family: \'Baloo\', cursive;"><i class="far fa-thumbs-down" data-toggle="tooltip" title="Nelíbí se"></i></a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bought_times" style="font-family: \'Baloo\', cursive;">Koupeno</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_price" style="font-family: \'Baloo\', cursive;">Cena</a></u></th>
							<th scope="col"></th>
						</tr>
					</thead>
				<tbody>';
				$i = 0;
				while ($post = mysqli_fetch_array($search_query)) {
					$i++;
					//Cut description
					if (strlen($post['item_description']) < 50) {
						$itemDescription = $post['item_description'];
					} else {
						$itemDescription = substr($post['item_description'], 0, 50);
						$itemDescription .= '<span class="font-weight-bold text-primary" data-toggle="tooltip" title="'.$post['item_description'].'">...</span>';
					}
					//-----------------------------------------
					if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
						$button_itemDelete = '<td><a data-toggle="modal" data-target="#centralModalSm'.$i.'"><i class="fas fa-trash-alt text-danger" data-toggle="tooltip" title="Odstranit"></i></a></td>';
						$modal_itemDelete = "
						<div class='modal fade' id='centralModalSm".$i."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog modal-center' role='document'>
							<div class='modal-content'>
								<div class='modal-header'>
								<h4 class='modal-title w-100' id='myModalLabel".$i."'>Odstranit vybranou položku?</h4>
								<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
									<span aria-hidden='true'>&times;</span>
								</button>
								</div>
								<div class='modal-body d-flex'>
									<a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."shop_action_admin.php?action=remove&remove_id=".$post['item_id']."'>Potvrdit</a>
								</div>
							</div>
							</div>
						</div>";
					}
					$type = ($post['item_type'] == 0) ? "<i class='far fa-square' data-toggle='tooltip' title='Malý test'></i>" : "<i class='fas fa-square' data-toggle='tooltip' title='Velký test'></i>";
					if ($post['item_answers'] == 1) {
						$itemAnswers = ' <i class="fas fa-certificate text-primary" data-toggle="tooltip" title="S odpověďmi na jedničku"></i>';
					} else {
						$itemAnswers = '';
					}
					echo $post_row = sprintf("
						<tr class=' h-100'>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%d.</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%dx</td>
							<td class='my-auto'>%d Kč</td>
							<td><a data-toggle='modal' data-target='#buyModal%d'><i class='fas fa-shopping-cart text-primary' data-toggle='tooltip' title='Koupit'></i></a></td>
							%s
							%s
						</tr>
						<div class='modal fade' id='buyModal%d' tabindex='-1' role='dialog' aria-labelledby='buyModalLabel' aria-hidden='true'>
							<div class='modal-dialog modal-center' role='document'>
								<div class='modal-content'>
									<div class='modal-header'>
										<h4 class='modal-title w-100' id='buyModalLabel%d'>Koupit tuto položku?</h4>
										<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
											<span aria-hidden='true'>&times;</span>
										</button>
									</div>
									<div class='modal-body d-flex'>
										<a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."item_buy.php?item_id=%d'>Potvrdit</a>
									</div>
								</div>
							</div>
						</div>
						",
						$post['item_subject'],
						$post['school_class'],
						$post['teacher'],
						$type.$itemAnswers,
						$post['item_name'],
						$itemDescription,
						$post['likes'],
						$post['dislikes'],
						$post['bought_times'],
						$post['item_price'],
						$i,
						$button_itemDelete,
						$modal_itemDelete,
						$i,
						$i,
						$post['item_id']);
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