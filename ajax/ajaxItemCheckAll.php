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
$search = "SELECT s.item_id, s.item_name, s.item_description, s.item_price, s.item_createdby_username, s.item_createdby_userid, s.create_date, s.item_type, s.item_subject, s.school_class, s.teacher, s.school_id, s.item_answers, sch.school_name FROM shop s, school sch WHERE s.checked = 0 AND s.school_id = sch.school_id";
$total_pages_sql = "SELECT COUNT(*) FROM shop s, school sch WHERE s.checked = 0 AND s.school_id = sch.school_id";
if (!empty($_POST['schoolName'])) {
	$search .= " AND sch.school_name COLLATE utf8_general_ci LIKE '%".$_POST['schoolName']."%'";
	$total_pages_sql .= " AND sch.school_name COLLATE utf8_general_ci LIKE '%".$_POST['schoolName']."%'";
}
if (!empty($_POST['itemName'])) {
	$search .= " AND s.item_name COLLATE utf8_general_ci LIKE '%".$_POST['itemName']."%'";
	$total_pages_sql .= " AND s.item_name COLLATE utf8_general_ci LIKE '%".$_POST['itemName']."%'";
}
if (!empty($_POST['itemDescription'])) {
	$search .= " AND s.item_description COLLATE utf8_general_ci LIKE '%".$_POST['itemDescription']."%'";
	$total_pages_sql .= " AND s.item_description COLLATE utf8_general_ci LIKE '%".$_POST['itemDescription']."%'";
}
if (!empty($_POST['itemSubject'])) {
	$search .= " AND s.item_subject COLLATE utf8_general_ci LIKE '%".$_POST['itemSubject']."%'";
	$total_pages_sql .= " AND s.item_subject COLLATE utf8_general_ci LIKE '%".$_POST['itemSubject']."%'";
}
if (isset($_POST['teacher'])) {
	$teacherFilter = implode("','", $_POST['teacher']);
	$search .= " AND s.teacher = '".$teacherFilter."'";
	$total_pages_sql .= " AND s.teacher = '".$teacherFilter."'";
}
if (isset($_POST['itemAnswers'])) {
	$search .= " AND s.item_answers = 1";
	$total_pages_sql .= " AND s.item_answers = 1";
}
if (isset($_POST['itemType'])) {
	$search .= " AND s.item_type = '".$_POST['itemType']."'";
	$total_pages_sql .= " AND s.item_type = '".$_POST['itemType']."'";
}
if (isset($_POST['schoolClass'])) {
	$search .= " AND s.school_class = '".$_POST['schoolClass']."'";
	$total_pages_sql .= " AND s.school_class = '".$_POST['schoolClass']."'";
}
if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
	$search .= " ORDER BY ".$_POST['column_name']." ".$order;
} else {
	$search .= " ORDER BY s.create_date ASC";
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
				echo "<p class='font-weight-bold text-center'>Nebyly nalezeny žádné testy na kontrolu!</p>";
			} else {
				$i=0;
				echo $output = '<div class="table-responsive">
				<table class="table table-hover table-striped table-sm">
					<thead>
						<tr>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_subject" style="font-family: \'Baloo\', cursive;">Předmět</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="school_class" style="font-family: \'Baloo\', cursive;">Ročník</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="teacher" style="font-family: \'Baloo\', cursive;">Učitel</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_type" style="font-family: \'Baloo\', cursive;">Typ</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_name" style="font-family: \'Baloo\', cursive;">Název</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_description" style="font-family: \'Baloo\', cursive;">Popis</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_createdby_username" style="font-family: \'Baloo\', cursive;">Od</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="item_price" style="font-family: \'Baloo\', cursive;">Cena</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="create_date" style="font-family: \'Baloo\', cursive;">Datum</a></u></th>
							<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="school_name" style="font-family: \'Baloo\', cursive;">Škola</a></u></th>
							<th scope="col" style="font-family: \'Baloo\', cursive;">Přílohy</th>
							<th scope="col"></th>
						</tr>
					</thead>
				<tbody>';
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
					$type = ($post['item_type'] == 0) ? "<i class='far fa-square' data-toggle='tooltip' title='Malý test'></i>" : "<i class='fas fa-square' data-toggle='tooltip' title='Velký test'></i>";
					if ($post['item_answers'] == 1) {
						$itemAnswers = ' <i class="fas fa-certificate text-primary" data-toggle="tooltip" title="S odpověďmi na jedničku"></i>';
					} else {
						$itemAnswers = '';
					}
					$image = NULL;
					$no_image = false;
					$select_image = sprintf("SELECT file_path FROM images WHERE shop_id = '%d';",
					mysqli_real_escape_string($connect, $post['item_id']));
					$select_image_query = mysqli_query($connect, $select_image);
					
					if (mysqli_num_rows($select_image_query) != 0) {
						$j=0;
						while ($select_image_row = mysqli_fetch_row($select_image_query)) {
							$image[$j] = $select_image_row[0];
							$j++;
						}
					} else {
						$no_image = true;
					}
					$str = "abcdefghijklmn";
					$shuffle1 = str_shuffle($str);
					$shuffle2 = str_shuffle($str);
					$shuffle3 = str_shuffle($str);
					$shuffle4 = str_shuffle($str);
					$empty = ($no_image) ? 'Zde není žádná příloha, odmítněte žádost!' : '';
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
					echo $post_row = sprintf("
						<tr class=' h-100'>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%d.</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'><a class='text-primary font-weight-bold' href='".SITE_ROOT."profile_show.php?profile_id=%d'><u>%s</u></a></td>
							<td class='my-auto'>%d Kč</td>
							<td class='my-auto'>%s</td>
							<td class='my-auto'><a href='".SITE_ROOT."school_info.php?school_id=%d' class='font-weight-bold text-primary'>%s</a></td>
							<td class='my-auto'>%s</td>
							<td><a href='".SITE_ROOT."item_check_action.php?confirm_id=%d&seller_id=%d' class='mx-2'><i class='fas fa-check text-success' data-toggle='tooltip' title='Potvrdit'></i></a>
								<a data-toggle='modal' data-target='#declineModal%d' class='mx-2'><i class='fas fa-times text-danger' data-toggle='tooltip' title='Odmítnout'></i></a></td>
						</tr>
						",
						$post['item_subject'],
						$post['school_class'],
						$post['teacher'],
						$type.$itemAnswers,
						$post['item_name'],
						$itemDescription,
						$post['item_createdby_userid'],
						$post['item_createdby_username'],
						$post['item_price'],
						date('d.m.Y H:i', strtotime($post['create_date'])),
						$post['school_id'],
						$post['school_name'],
						$empty.$button1.$button2.$button3.$button4,
						$post['item_id'],
						$post['item_createdby_userid'],
						$i);
				}
				echo $table_end = '</tbody></table></div>';
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
				$i=0;
				while ($post2 = mysqli_fetch_array($search_query2)) {
					$i++;
					echo "
					<div class='modal fade' id='declineModal".$i."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					  <div class='modal-dialog modal-center' role='document'>
					    <div class='modal-content'>
					      <div class='modal-header'>
					        <h4 class='modal-title w-100' id='myModalLabel".$i."'>Odmítnout test?</h4>
					        <button type='button' class='close' data-dismiss='modal' aria-label='Zavřít'>
					          <span aria-hidden='true'>&times;</span>
					        </button>
					      </div>
					      <div class='modal-body'>
					        <form action='".SITE_ROOT."item_check_action.php?decline_id=".$post2['item_id']."&seller_id=".$post2['item_createdby_userid']."' class='text-center' method='post'>
					        <textarea name='decline_reason' id='decline_reason".$i."' rows='6' class='form-control rounded mb-4' maxlength='1000' autocomplete='off' required placeholder='Důvod: (&#39;image&#39; = Špatná příloha; &#39;content&#39; = Špatný obsah; &#39;image+blackpoint&#39; = Špatná příloha + trestný bod; &#39;content+blackpoint&#39; = Špatný obsah + trestný bod; &#39;first_name&#39; = Na obrázku je uvedeno něčí jméno...)'></textarea>
					        <button type='submit' class='btn btn-danger btn-sm d-flex mx-auto mt-3' value='asd'>Potvrdit</button>
					        </form>
					      </div>
					    </div>
					  </div>
					</div>";
				}
			}