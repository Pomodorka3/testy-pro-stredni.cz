<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user(array("Main administrator"));

$user_id = $_SESSION['user_id'];

if ($_POST['log'] == 'bannedUsers') {

	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT u1.user_id banned_id, u1.username banned_username, u2.user_id banned_by_id, u2.username banned_by_username, bu.id, bu.ban_time, bu.ban_description, bu.ban_active FROM users u1, users u2, banned_users bu WHERE u1.user_id = bu.banned_id AND u2.user_id = bu.banned_by';
	$total_pages_sql = 'SELECT COUNT(*) FROM users u1, users u2, banned_users bu WHERE u1.user_id = bu.banned_id AND u2.user_id = bu.banned_by';

	if (!empty($_POST['bannedUser'])) {
		$_POST['bannedUser'] = trim($_POST['bannedUser']);
		$search .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['bannedUser']."%'";
		$total_pages_sql .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['bannedUser']."%'";
	}
	if (!empty($_POST['bannedBy'])) {
		$_POST['bannedBy'] = trim($_POST['bannedBy']);
		$search .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['bannedBy']."%'";
		$total_pages_sql .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['bannedBy']."%'";
	}
	if ($_POST['ban_active'] == '1') {
		$banActive_checked = 'checked';
		$search .= " AND bu.ban_active = 1";
		$total_pages_sql .= " AND bu.ban_active = 1";
	} else {
		$banActive_checked = '';
	}
	
	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY bu.ban_time DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="banned_id" id="banned_id" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Banned user" value="'.$_POST['bannedUser'].'">
			</div>
			<div class="m-0">
				<input type="text" name="banned_by" id="banned_by" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Banned by" value="'.$_POST['bannedBy'].'">
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="ban_active" '.$banActive_checked.'><label class="custom-control-label" for="ban_active">Active</label>
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	/*<div class="col-md-3 text-center">
			<a id="bannedUsers" class="btn btn-primary btn-sm">Ban log</a>
		</div>
		<div class="col-md-3 text-center">
			<a id="unbannedUsers" class="btn btn-primary btn-sm">Unban log</a>
		</div>
		<div class="col-md-3 text-center">
			<a id="depositLog" class="btn btn-primary btn-sm">Deposit log</a>
		</div>
		<div class="col-md-3 text-center">
			<a id="" class="btn btn-primary btn-sm">-</a>
		</div>*/
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bu.id" style="font-family: \'Baloo\', cursive; font-size: 16px;">#</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Banned user</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u2.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Banned by</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bu.ban_description" style="font-family: \'Baloo\', cursive; font-size: 16px;">Ban description</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bu.ban_active" style="font-family: \'Baloo\', cursive; font-size: 16px;">Ban active</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bu.ban_time" style="font-family: \'Baloo\', cursive; font-size: 16px;">Ban time</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {
			if ($search_row['ban_active'] == 0) {
				$banActive = '-';
			} else {
				$banActive = '<i class="fas fa-check text-success"></i>';
			}
			printf("
			<tr class='h-100'>
				<td class='my-auto'>%d</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
			</tr>",
			$search_row['id'],
			$search_row['banned_id'],
			$search_row['banned_username'],
			$search_row['banned_by_id'],
			$search_row['banned_by_username'],
			$search_row['ban_description'],
			$banActive,
			date('d.m.Y H:i', strtotime($search_row['ban_time'])));
		}
		echo '</tbody></table></div>';
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
} elseif ($_POST['log'] == 'unbannedUsers') {
	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT u1.user_id unbanned_id, u1.username unbanned_username, u2.user_id unbanned_by_id, u2.username unbanned_by_username, uu.id, uu.unban_time FROM users u1, users u2, unbanned_users uu WHERE u1.user_id = uu.unbanned_id AND u2.user_id = uu.unbanned_by';
	$total_pages_sql = 'SELECT COUNT(*) FROM users u1, users u2, unbanned_users uu WHERE u1.user_id = uu.unbanned_id AND u2.user_id = uu.unbanned_by';

	if (!empty($_POST['unbannedUser'])) {
		$_POST['unbannedUser'] = trim($_POST['unbannedUser']);
		$search .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['unbannedUser']."%'";
		$total_pages_sql .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['unbannedUser']."%'";
	}
	if (!empty($_POST['unbannedBy'])) {
		$_POST['unbannedBy'] = trim($_POST['unbannedBy']);
		$search .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['unbannedBy']."%'";
		$total_pages_sql .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['unbannedBy']."%'";
	}

	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY uu.unban_time DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="unbanned_id" id="unbanned_id" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Unbanned user" value="'.$_POST['unbannedUser'].'">
			</div>
			<div class="m-0">
				<input type="text" name="unbanned_by" id="unbanned_by" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Unbanned by" value="'.$_POST['unbannedBy'].'">
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="uu.id" style="font-family: \'Baloo\', cursive; font-size: 16px;">#</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Unbanned user</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u2.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Unbanned by</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="uu.unban_time" style="font-family: \'Baloo\', cursive; font-size: 16px;">Unban time</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {
			printf("
			<tr class='h-100'>
				<td class='my-auto'>%d</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%s</td>
			</tr>",
			$search_row['id'],
			$search_row['unbanned_id'],
			$search_row['unbanned_username'],
			$search_row['unbanned_by_id'],
			$search_row['unbanned_by_username'],
			date('d.m.Y H:i', strtotime($search_row['unban_time'])));
		}
		echo '</tbody></table></div>';
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
} elseif ($_POST['log'] == 'deposit') {
	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT u.user_id, u.username, d.id, d.amount, d.status, d.time_requested, d.time_completed FROM users u, deposits d WHERE d.user_id = u.user_id';
	$total_pages_sql = 'SELECT COUNT(*) FROM users u, deposits d WHERE d.user_id = u.user_id';

	if (!empty($_POST['depositBy'])) {
		$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['depositBy']."%'";
		$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['depositBy']."%'";
	}
	if (isset($_POST['depositStatus'])) {
		if ($_POST['depositStatus'] == '0') {
			$status0 = 'checked';
			$status1 = '';
			$status2 = '';
			$search .= " AND d.status = 0";
			$total_pages_sql .= " AND d.status = 0";
		} elseif ($_POST['depositStatus'] == '1') {
			$status0 = '';
			$status1 = 'checked';
			$status2 = '';
			$search .= " AND d.status = 1";
			$total_pages_sql .= " AND d.status = 1";
		} elseif ($_POST['depositStatus'] == '2') {
			$status0 = '';
			$status1 = '';
			$status2 = 'checked';
			$search .= " AND d.status = 2";
			$total_pages_sql .= " AND d.status = 2";
		}
	}

	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY d.time_requested DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="depositBy" id="depositBy" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Username" value="'.$_POST['depositBy'].'">
			</div>
			<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input depositStatus" id="radio1" name="depositStatus" value="0" '.$status0.'>
				<label for="radio1" class="custom-control-label">Zpracovává se</label>
			</div>
			<div class="custom-control custom-radio mt-2">
				<input type="radio" class="custom-control-input depositStatus" id="radio2" name="depositStatus" value="1" '.$status1.'>
				<label for="radio2" class="custom-control-label">Schválená platba</label>
			</div>
			<div class="custom-control custom-radio mt-2">
				<input type="radio" class="custom-control-input depositStatus" id="radio3" name="depositStatus" value="2" '.$status2.'>
				<label for="radio3" class="custom-control-label">Zamítnutá platba</label>
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	/*<div class="col-md-3 text-center">
			<a id="bannedUsers" class="btn btn-primary btn-sm">Ban log</a>
		</div>
		<div class="col-md-3 text-center">
			<a id="unbannedUsers" class="btn btn-primary btn-sm">Unban log</a>
		</div>
		<div class="col-md-3 text-center">
			<a id="depositLog" class="btn btn-primary btn-sm">Deposit log</a>
		</div>
		<div class="col-md-3 text-center">
			<a id="" class="btn btn-primary btn-sm">-</a>
		</div>*/
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="d.id" style="font-family: \'Baloo\', cursive; font-size: 16px;">#</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Username</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="d.amount" style="font-family: \'Baloo\', cursive; font-size: 16px;">Amount</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="d.status" style="font-family: \'Baloo\', cursive; font-size: 16px;">Status</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="d.time_requested" style="font-family: \'Baloo\', cursive; font-size: 16px;">Requested</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="d.time_completed" style="font-family: \'Baloo\', cursive; font-size: 16px;">Completed</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {

			$status = '';
			if ($search_row['status'] == 0) {
				$status = "<span class='text-primary font-weight-bold'><i class='fas fa-circle-notch text-primary' data-toggle='tooltip' title='Zpracovává se'></i></span>";
			} elseif ($search_row['status'] == 1) {
				$status = "<span class='text-primary font-weight-bold'><i class='fas fa-check text-success' data-toggle='tooltip' title='Platba byla schválena'></i></span>";
			} elseif ($search_row['status'] == 2) {
				$status = "<span class='text-primary font-weight-bold'><i class='fas fa-times text-danger' data-toggle='tooltip' title='Platba byla zamítnuta'></i></span>";
			}
			$time_completed = '';
			if ($search_row['time_completed'] == '0000-00-00 00:00:00') {
				$time_completed = '-';
			} else {
				$time_completed = date('d.m.Y H:i', strtotime($search_row['time_completed']));
			}
			printf("
			<tr class='h-100'>
				<td class='my-auto'>%d</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%d CZK</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
			</tr>",
			$search_row['id'],
			$search_row['user_id'],
			$search_row['username'],
			$search_row['amount'] / 100,
			$status,
			date('d.m.Y H:i', strtotime($search_row['time_requested'])),
			$time_completed);
		}
		echo '</tbody></table></div>';
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
}  elseif ($_POST['log'] == 'groups') {
	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT u1.user_id, u1.username, u2.user_id setby_user_id, u2.username setby_username, g.group_name, ug.event_date, ug.set_method FROM users u1, users u2, users_groups ug, groups g WHERE u1.user_id = ug.user_id AND u2.user_id = ug.set_by AND ug.group_id = g.group_id';
	$total_pages_sql = 'SELECT COUNT(*) FROM users u1, users u2, users_groups ug, groups g WHERE u1.user_id = ug.user_id AND u2.user_id = ug.set_by AND ug.group_id = g.group_id';

	if (!empty($_POST['username'])) {
		$_POST['username'] = trim($_POST['username']);
		$search .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%'";
		$total_pages_sql .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['username']."%'";
	}
	if (!empty($_POST['setby_username'])) {
		$_POST['setby_username'] = trim($_POST['setby_username']);
		$search .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['setby_username']."%'";
		$total_pages_sql .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['setby_username']."%'";
	}

	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY ug.event_date DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="username" id="username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Username" value="'.$_POST['username'].'">
			</div>
			<div class="m-0">
				<input type="text" name="setby_username" id="setby_username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Set by" value="'.$_POST['setby_username'].'">
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Username</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="g.group_name" style="font-family: \'Baloo\', cursive; font-size: 16px;">Group</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u2.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Set by</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="ug.set_method" style="font-family: \'Baloo\', cursive; font-size: 16px;">Method</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="ug.event_date" style="font-family: \'Baloo\', cursive; font-size: 16px;">Set time</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {
			if ($search_row['group_name'] == 'Administrator') {
				$group = '<span class="text-success">Administrator</span>';
			} elseif ($search_row['group_name'] == 'Support') {
				$group = '<span class="text-warning">Support</span>';
			} elseif ($search_row['group_name'] == 'Validator') {
				$group = '<span class="text-secondary">Validator</span>';
			}
			printf("
			<tr class='h-100'>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
			</tr>",
			$search_row['user_id'],
			$search_row['username'],
			$group,
			$search_row['setby_user_id'],
			$search_row['setby_username'],
			$search_row['set_method'],
			date('d.m.Y H:i', strtotime($search_row['event_date'])));
		}
		echo '</tbody></table></div>';
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
} elseif ($_POST['log'] == 'referral') {
	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT u1.user_id, u1.username, u1.register_date, u1.bought_items, u2.user_id invitedby_user_id, u2.username invitedby_username FROM users u1, users u2, referrals r WHERE r.referrals_userby = u2.user_id AND r.referrals_userid = u1.user_id AND u1.activated = 1';
	$total_pages_sql = 'SELECT COUNT(*) FROM users u1, users u2, referrals r WHERE r.referrals_userby = u2.user_id AND r.referrals_userid = u1.user_id AND u1.activated = 1';

	if (!empty($_POST['invited_username'])) {
		$_POST['invited_username'] = trim($_POST['invited_username']);
		$search .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['invited_username']."%'";
		$total_pages_sql .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['invited_username']."%'";
	}
	if (!empty($_POST['invitedby_username'])) {
		$_POST['invitedby_username'] = trim($_POST['invitedby_username']);
		$search .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['invitedby_username']."%'";
		$total_pages_sql .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['invitedby_username']."%'";
	}
	if ($_POST['boughtItem'] == 1) {
		$search .= " AND u1.bought_items > 0";
		$total_pages_sql .= " AND u1.bought_items > 0";
		$checked = 'checked';
	} else {
		$checked = '';
	}

	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY u1.register_date DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="invited_username" id="invited_username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Username" value="'.$_POST['invited_username'].'">
			</div>
			<div class="m-0">
				<input type="text" name="invitedby_username" id="invitedby_username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Invited by" value="'.$_POST['invitedby_username'].'">
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="boughtItem" '.$checked.'><label class="custom-control-label" for="boughtItem">Bought sth</label>
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Username</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u2.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Invited by</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.bought_items" style="font-family: \'Baloo\', cursive; font-size: 16px;">Bought items</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.register_date" style="font-family: \'Baloo\', cursive; font-size: 16px;">Invited on</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {
			printf("
			<tr class='h-100'>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
			</tr>",
			$search_row['user_id'],
			$search_row['username'],
			$search_row['invitedby_user_id'],
			$search_row['invitedby_username'],
			$search_row['bought_items'],
			date('d.m.Y H:i', strtotime($search_row['register_date'])));
		}
		echo '</tbody></table></div>';
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
} elseif ($_POST['log'] == 'shop') {
	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT u1.user_id buyer_id, u1.username buyer_username, u2.user_id seller_id, u2.username seller_username, s.item_name, be.item_id, be.price, be.seller_multiplier, be.buy_time, be.rated FROM users u1, users u2, buy_events be, shop s WHERE be.buyer_id = u1.user_id AND be.seller_id = u2.user_id AND s.item_id = be.item_id';
	$total_pages_sql = 'SELECT COUNT(*) FROM users u1, users u2, buy_events be, shop s WHERE be.buyer_id = u1.user_id AND be.seller_id = u2.user_id AND s.item_id = be.item_id';

	if (!empty($_POST['buyer_username'])) {
		$_POST['buyer_username'] = trim($_POST['buyer_username']);
		$search .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['buyer_username']."%'";
		$total_pages_sql .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['buyer_username']."%'";
	}
	if (!empty($_POST['seller_username'])) {
		$_POST['seller_username'] = trim($_POST['seller_username']);
		$search .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['seller_username']."%'";
		$total_pages_sql .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['seller_username']."%'";
	}

	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY be.buy_time DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="buyer_username" id="buyer_username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Buyer" value="'.$_POST['buyer_username'].'">
			</div>
			<div class="m-0">
				<input type="text" name="seller_username" id="seller_username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Seller" value="'.$_POST['seller_username'].'">
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Buyer</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u2.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Seller</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="be.item_id" style="font-family: \'Baloo\', cursive; font-size: 16px;">Item (ID)</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="be.seller_multiplier" style="font-family: \'Baloo\', cursive; font-size: 16px;">Multiplier</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="be.price" style="font-family: \'Baloo\', cursive; font-size: 16px;">Price</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="be.rated" style="font-family: \'Baloo\', cursive; font-size: 16px;">Rated</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="be.buy_time" style="font-family: \'Baloo\', cursive; font-size: 16px;">Buy time</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {
			if ($search_row['rated'] == 0) {
				$rated = "Ne";
			} else {
				$rated = "Ano";
			}
			printf("
			<tr class='h-100'>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%s (%d)</td>
				<td class='my-auto'>%d</td>
				<td class='my-auto'>%d</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
			
			</tr>",
			$search_row['buyer_id'],
			$search_row['buyer_username'],
			$search_row['seller_id'],
			$search_row['seller_username'],
			$search_row['item_name'],
			$search_row['item_id'],
			$search_row['seller_multiplier'],
			$search_row['price'],
			$rated,
			date('d.m.Y H:i', strtotime($search_row['buy_time'])));
		}
		echo '</tbody></table></div>';
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
} elseif ($_POST['log'] == 'transactions') {
	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT t.t_id, t.t_from, t.t_sum, t.t_description, t.t_date, u.username FROM transactions t INNER JOIN users u ON u.user_id = t.t_from';
	$total_pages_sql = 'SELECT COUNT(*) FROM transactions t INNER JOIN users u ON u.user_id = t.t_from';

	if (!empty($_POST['transaction_username'])) {
		$_POST['transaction_username'] = trim($_POST['transaction_username']);
		$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['transaction_username']."%'";
		$total_pages_sql .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['transaction_username']."%'";
	}
	if (isset($_POST['transaction_description'])) {
		if ($_POST['transaction_description'] == 'checked_items') {
			$itemCheck_checked = 'checked';
			$referrals_checked = '';
			$lottery_checked = '';
			$search .= " AND t.t_description = 'Checked items reward'";
			$total_pages_sql .= " AND t.t_description = 'Checked items reward'";
		} elseif ($_POST['transaction_description'] == 'referrals') {
			$itemCheck_checked = '';
			$referrals_checked = 'checked';
			$lottery_checked = '';
			$search .= " AND t.t_description = 'Referrals reward'";
			$total_pages_sql .= " AND t.t_description = 'Referrals reward'";
		} elseif ($_POST['transaction_description'] == 'lottery') {
			$itemCheck_checked = '';
			$referrals_checked = '';
			$lottery_checked = 'checked';
			$search .= " AND t.t_description = 'Lottery reward'";
			$total_pages_sql .= " AND t.t_description = 'Lottery reward'";
		}
	}

	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY t.t_date DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="transaction_username" id="transaction_username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Username" value="'.$_POST['transaction_username'].'">
			</div>
			<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input transaction_desc" id="radio1" name="transaction_desc" value="checked_items" '.$itemCheck_checked.'>
				<label for="radio1" class="custom-control-label">Checked items reward</label>
			</div>
			<div class="custom-control custom-radio mt-2">
				<input type="radio" class="custom-control-input transaction_desc" id="radio2" name="transaction_desc" value="referrals" '.$referrals_checked.'>
				<label for="radio2" class="custom-control-label">Referrals reward</label>
			</div>
			<div class="custom-control custom-radio mt-2">
				<input type="radio" class="custom-control-input transaction_desc" id="radio3" name="transaction_desc" value="lottery" '.$lottery_checked.'>
				<label for="radio3" class="custom-control-label">Lottery reward</label>
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="t.t_id" style="font-family: \'Baloo\', cursive; font-size: 16px;">#</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">User</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="t.t_sum" style="font-family: \'Baloo\', cursive; font-size: 16px;">Sum</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="t.t_description" style="font-family: \'Baloo\', cursive; font-size: 16px;">Description</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="t.t_date" style="font-family: \'Baloo\', cursive; font-size: 16px;">Date</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {
			printf("
			<tr class='h-100'>
				<td class='my-auto'>%d</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%d Kč</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
			</tr>",
			$search_row['t_id'],
			$search_row['t_from'],
			$search_row['username'],
			$search_row['t_sum'],
			$search_row['t_description'],
			date('d.m.Y H:i', strtotime($search_row['t_date'])));
		}
		echo '</tbody></table></div>';
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
} elseif ($_POST['log'] == 'blackPoints') {
	$column_name = $_POST['column_name'];
	$order = $_POST['order'];
	if ($order == 'desc' || $order == 'DESC' ) {
		$order = 'ASC';
	} else {
		$order = 'DESC';
	}

	$search = 'SELECT bp.bp_id, u1.username, bp.bp_userid, u2.username givenby_username, bp.bp_givenby, bp.bp_description, bp.bp_date, bp.bp_active FROM black_points bp, users u1, users u2 WHERE u1.user_id = bp.bp_userid AND u2.user_id = bp.bp_givenby';
	$total_pages_sql = 'SELECT COUNT(*) FROM black_points bp INNER JOIN users u1 ON u1.user_id = bp.bp_userid INNER JOIN users u2 ON u2.user_id = bp.bp_givenby';

	if (!empty($_POST['bp_username'])) {
		$_POST['bp_username'] = trim($_POST['bp_username']);
		$search .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['bp_username']."%'";
		$total_pages_sql .= " AND u1.username COLLATE utf8_general_ci LIKE '%".$_POST['bp_username']."%'";
	}
	if (!empty($_POST['bp_givenby'])) {
		$_POST['bp_givenby'] = trim($_POST['bp_givenby']);
		$search .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['bp_givenby']."%'";
		$total_pages_sql .= " AND u2.username COLLATE utf8_general_ci LIKE '%".$_POST['bp_givenby']."%'";
	}
	if ($_POST['bp_active'] == '1') {
		$bpActive_checked = 'checked';
		$search .= " AND bp.bp_active = 1";
		$total_pages_sql .= " AND bp.bp_active = 1";
	} else {
		$bpActive_checked = '';
	}

	//Count results
	$results = $total_pages_sql;
	$results_query = mysqli_query($connect, $results);
	$results_row = mysqli_fetch_row ($results_query);

	//Column order/sort
	if ((isset($_POST['column_name'])) && (isset($_POST['order']))) {
		$search .= " ORDER BY ".$_POST['column_name']." ".$order;
	} else {
		$search .= " ORDER BY bp.bp_date DESC";
	}
	//Page
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

	echo '
	<div class="row mb-3">
		<div class="col-md-6">
			<a class="text-center"><div id="bannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Ban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="unbannedUsers" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Unban log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="deposit" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Deposit log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="groups" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Groups log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="referral" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Referral log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="shop" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Shop log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="transactions" class="cloudy-knoxville-gradient" style="font-family: \'Baloo\', cursive;">Transactions log<br><hr class="p-0 m-0"></div></a>
			<a class="text-center"><div id="blackPoints" style="background-image: linear-gradient(90deg,#fdfbfb 0,#FD4000 50%, #fdfbfb 100%); font-family: \'Baloo\', cursive;">Black points log<br></div></a>
		</div>
		<div class="col-md-6">
			<div class="mt-4 mt-md-2">
				<input type="text" name="bp_username" id="bp_username" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Username" value="'.$_POST['bp_username'].'">
			</div>
			<div class="m-0">
				<input type="text" name="bp_givenby" id="bp_givenby" class="form-control mb-4 filter_field" autocomplete="off" style="font-family: \'Baloo\', cursive;" placeholder="Given by" value="'.$_POST['bp_givenby'].'">
			</div>
			<div class="custom-control custom-checkbox">
				<input type="checkbox" class="custom-control-input" id="bp_active" '.$bpActive_checked.'><label class="custom-control-label" for="bp_active">Active</label>
			</div>
			<div class="text-center text-md-left">
				<button class="btn btn-sm btn-primary search"><i class="fas fa-search mr-2"></i>Search</button>
			</div>
		</div>
	</div>
	<hr>';
	if (mysqli_num_rows($search_query) == 0) {
		echo '<p class="font-weight-bold text-center">Nothing was found!</p>';
	} else {
		echo 'Results: '.$results_row[0];
		echo '<div class="table-responsive">
		<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bp.bp_id" style="font-family: \'Baloo\', cursive; font-size: 16px;">#</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u1.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">User</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="u2.username" style="font-family: \'Baloo\', cursive; font-size: 16px;">Given by</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bp.bp_description" style="font-family: \'Baloo\', cursive; font-size: 16px;">Description</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bp.bp_active" style="font-family: \'Baloo\', cursive; font-size: 16px;">Active</a></u></th>
					<th scope="col"><u><a class="column_sort" data-order="'.$order.'" id="bp.bp_date" style="font-family: \'Baloo\', cursive; font-size: 16px;">Date</a></u></th>
				</tr>
			</thead>
		<tbody>';
		while ($search_row = mysqli_fetch_array($search_query)) {
			if ($search_row['bp_active'] == 0) {
				$bpActive = '-';
			} else {
				$bpActive = '<i class="fas fa-check text-success"></i>';
			}
			printf("
			<tr class='h-100'>
				<td class='my-auto'>%d</td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'><a href='".SITE_ROOT."profile_show?profile_id=%d' class='text-primary font-weight-bold'>%s</a></td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
				<td class='my-auto'>%s</td>
			</tr>",
			$search_row['bp_id'],
			$search_row['bp_userid'],
			$search_row['username'],
			$search_row['bp_givenby'],
			$search_row['givenby_username'],
			$search_row['bp_description'],
			$bpActive,
			date('d.m.Y H:i', strtotime($search_row['bp_date'])));
		}
		echo '</tbody></table></div>';
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
}
?>