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
$search = "SELECT item_id, item_name, item_description, item_price, confirmed_date, bought_times, likes, dislikes, item_type, item_subject, school_class, teacher, item_answers FROM shop WHERE item_createdby_userid = $user_id AND visible = 1 AND checked = 1";
$total_pages_sql = "SELECT COUNT(*) FROM shop s WHERE item_createdby_userid = $user_id AND visible = 1 AND checked = 1";

if (!empty($_POST['itemName'])) {
	$search .= " AND item_name COLLATE utf8_general_ci LIKE '%".$_POST['itemName']."%'";
	$total_pages_sql .= " AND item_name COLLATE utf8_general_ci LIKE '%".$_POST['itemName']."%'";
}
if (!empty($_POST['itemDescription'])) {
	$search .= " AND item_description COLLATE utf8_general_ci LIKE '%".$_POST['itemDescription']."%'";
	$total_pages_sql .= " AND item_description COLLATE utf8_general_ci LIKE '%".$_POST['itemDescription']."%'";
}
if (isset($_POST['teacher'])) {
	$search .= " AND teacher COLLATE utf8_general_ci LIKE '%".$_POST['teacher']."%'";
	$total_pages_sql .= " AND teacher COLLATE utf8_general_ci LIKE '%".$_POST['teacher']."%'";
}
if (isset($_POST['itemSubject'])) {
	$search .= " AND item_subject COLLATE utf8_general_ci LIKE '%".$_POST['itemSubject']."%'";
	$total_pages_sql .= " AND item_subject COLLATE utf8_general_ci LIKE '%".$_POST['itemSubject']."%'";
}
if (isset($_POST['itemAnswers'])) {
	$search .= " AND item_answers = 1";
	$total_pages_sql .= " AND item_answers = 1";
}
if (isset($_POST['itemType'])) {
	$search .= " AND item_type = '".$_POST['itemType']."'";
	$total_pages_sql .= " AND item_type = '".$_POST['itemType']."'";
}
if (isset($_POST['schoolClass'])) {
	$search .= " AND school_class = '".$_POST['schoolClass']."'";
	$total_pages_sql .= " AND school_class = '".$_POST['schoolClass']."'";
}
if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY confirmed_date DESC";
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
				echo "<p class='font-weight-bold text-center'>Nebyly nalezeny žádné prodávané testy!</p>";
			} else {
				$j = 0;
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
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="confirmed_date" style="font-family: \'Baloo\', cursive;">Na prodeji od</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="likes" style="font-family: \'Baloo\', cursive;"><i class="far fa-thumbs-up"></i></a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="dislikes" style="font-family: \'Baloo\', cursive;"><i class="far fa-thumbs-down"></i></a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bought_times" style="font-family: \'Baloo\', cursive;">Koupeno</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_price" style="font-family: \'Baloo\', cursive;">Cena</a></u></th>
							<th scope="col" style="font-family: \'Baloo\', cursive;">Zisk</th>
							<th scope="col" style="font-family: \'Baloo\', cursive;">Přílohy</th>
							<th scope="col"></th>
						</tr>
					</thead>
				<tbody>';
				while ($row = mysqli_fetch_array($search_query)) {
					$j++;
					//Cut description
					if (strlen($row['item_description']) < 50) {
						$itemDescription = $row['item_description'];
					} else {
						$itemDescription = substr($row['item_description'], 0, 50);
						$itemDescription .= '<span class="font-weight-bold text-primary" data-toggle="tooltip" title="'.$row['item_description'].'">...</span>';
					}
					//-----------------------------------------
					$multipliers = sprintf("SELECT price, seller_multiplier FROM buy_events WHERE item_id = '%d' AND seller_id = '%d';",
					mysqli_real_escape_string($connect, $row['item_id']),
					mysqli_real_escape_string($connect, $user_id));
					$multipliers_query = mysqli_query($connect, $multipliers);
					$total_earned = 0;
					while ($multipliers_row = mysqli_fetch_array($multipliers_query)) {
						$total_earned += $multipliers_row['price'] * $multipliers_row['seller_multiplier'];
					}
					$image = NULL;
					$no_image = false;
					$select_image = sprintf("SELECT file_path FROM images WHERE shop_id = '%d';",
					mysqli_real_escape_string($connect, $row['item_id']));
					$select_image_query = mysqli_query($connect, $select_image);
					
					if (mysqli_num_rows($select_image_query) != 0) {
						$i=0;
						while ($select_image_row = mysqli_fetch_row($select_image_query)) {
							$image[$i] = $select_image_row[0];
							$i++;
						}
					} else {
						$no_image = true;
					}
					$str = "abcdefghijklmn";
					$shuffle1 = str_shuffle($str);
					$shuffle2 = str_shuffle($str);
					$shuffle3 = str_shuffle($str);
					$shuffle4 = str_shuffle($str);

					$empty = ($no_image) ? 'Žádná příloha. Smažte tento test, a nahlašte chybu pomocí <a href="'.SITE_ROOT.'tickets.php" class="text-primary">tiketů</a>!' : '';
					$button1 = (!empty($image[0])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 1"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle1.'"></i></a>
					<div class="modal fade" id="'.$shuffle1.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle1.'" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h4 class="modal-title w-100" id="'.$shuffle1.'">Příloha 1</h4>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        <img src="'.$image[0].'" class="img-fluid mx-auto d-flex">
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
					      </div>
					    </div>
					  </div>
					</div>' : '';
					$button2 = (!empty($image[1])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 2"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle2.'"></i></a>
					<div class="modal fade" id="'.$shuffle2.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle2.'" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h4 class="modal-title w-100" id="'.$shuffle2.'">Příloha 2</h4>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        <img src="'.$image[1].'" class="img-fluid mx-auto d-flex">
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
					      </div>
					    </div>
					  </div>
					</div>' : '';
					$button3 = (!empty($image[2])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 3"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle3.'"></i></a>
					<div class="modal fade" id="'.$shuffle3.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle3.'" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h4 class="modal-title w-100" id="'.$shuffle3.'">Příloha 3</h4>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        <img src="'.$image[2].'" class="img-fluid mx-auto d-flex">
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
					      </div>
					    </div>
					  </div>
					</div>' : '';
					$button4 = (!empty($image[3])) ? '<a class="mx-2" data-toggle="tooltip" title="Příloha 4"><i class="fas fa-file-image text-primary" data-toggle="modal" data-target="#'.$shuffle4.'"></i></a>
					<div class="modal fade" id="'.$shuffle4.'" tabindex="-1" role="dialog" aria-labelledby="'.$shuffle4.'" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h4 class="modal-title w-100" id="'.$shuffle4.'">Příloha 4</h4>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        <img src="'.$image[3].'" class="img-fluid mx-auto d-flex">
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-primary btn-sm mx-auto" data-dismiss="modal">Zavřít</button>
					      </div>
					    </div>
					  </div>
					</div>' : '';
					$type = ($row['item_type'] == 0) ? "<i class='far fa-square' data-toggle='tooltip' title='Malý test'></i>" : "<i class='fas fa-square' data-toggle='tooltip' title='Velký test'></i>";
					if ($row['item_answers'] == 1) {
						$itemAnswers = ' <i class="fas fa-certificate text-primary" data-toggle="tooltip" title="S odpověďmi na jedničku"></i>';
					} else {
						$itemAnswers = '';
					}
					printf("
						<tr class=' h-100'>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%d.</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%d</td>
							<td class='my-auto'>%dx</td>
							<td class='my-auto'>%d Kč</td>
							<td class='my-auto font-weight-bold'>%d Kc</td>
							<td class='my-auto'>%s</td>
							<td><a data-toggle='modal' data-target='#removeModal%d'><i class='fas fa-trash-alt text-danger' data-toggle='tooltip' title='Odstranit'></i></a></td>
						</tr>
						<div class='modal fade' id='removeModal%d' tabindex='-1' role='dialog' aria-labelledby='removeModalLabel%d' aria-hidden='true'>
							<div class='modal-dialog modal-center' role='document'>
								<div class='modal-content'>
									<div class='modal-header'>
										<h4 class='modal-title w-100' id='removeModalLabel%d'>Odstranit tento test z prodeje?</h4>
										<button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
											<span aria-hidden='true'>&times;</span>
										</button>
									</div>
									<div class='modal-body d-flex'>
										<a class='btn btn-success btn-sm mx-auto' href='".SITE_ROOT."item_remove.php?remove_id=%d'>Potvrdit</a>
									</div>
								</div>
							</div>
						</div>
						",
						$row['item_subject'],
						$row['school_class'],
						$row['teacher'],
						$type.$itemAnswers,
						$row['item_name'],
						$itemDescription,
						date('d.m.Y H:i', strtotime($row['confirmed_date'])),
						$row['likes'],
						$row['dislikes'],
						$row['bought_times'],
						$row['item_price'],
						$total_earned,
						$empty.$button1.$button2.$button3.$button4,
						$j,
						$j,
						$j,
						$j,
						$row['item_id']);
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