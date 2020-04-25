<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';

session_start();
authorize_user();

$column_name = $_POST['column_name'];
$order = $_POST['lottery_order'];
if ($order == 'desc' || $order == 'DESC' ) {
	$order = 'ASC';
} else {
	$order = 'DESC';
}

$joinedUsers = 'SELECT COUNT(*) FROM lottery WHERE MONTH(lottery_joined) = MONTH(CURRENT_DATE());';
$joinedUsers_query = mysqli_query($connect, $joinedUsers);
$joinedUsers_row = mysqli_fetch_row($joinedUsers_query);

$search = 'SELECT u.username, l.lottery_userid, l.lottery_joined, l.lottery_place FROM lottery l, users u WHERE l.lottery_userid = u.user_id AND MONTH(l.lottery_joined) = MONTH(CURRENT_DATE())';
$search_query = mysqli_query($connect, $search);

$lottery_user = sprintf("SELECT lottery_joined, lottery_place FROM lottery WHERE lottery_userid = '%d' AND MONTH(lottery_joined) = MONTH(CURRENT_DATE());",
mysqli_real_escape_string($connect, $_SESSION['user_id']));
$lottery_user_query = mysqli_query($connect, $lottery_user);

$user_id =  $_SESSION['user_id'];

if (!empty($_POST['lottery_username'])) {
	$_POST['lottery_username'] = trim($_POST['lottery_username']);
	$search .= " AND u.username COLLATE utf8_general_ci LIKE '%".$_POST['lottery_username']."%'";
}
if ((isset($_POST['lottery_column'])) && (isset($_POST['lottery_order']))) {
	$search .= " ORDER BY ".$_POST['lottery_column']." ".$order;
} else {
	$search .= " ORDER BY l.lottery_joined DESC";
}

if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<br><b>Search SQL</b>: ".$search;
}
$search_query = mysqli_query($connect, $search);

if (mysqli_num_rows($search_query) == 0) {
	echo "<h5 class='font-weight-bold text-center'>Zatím se do slosování nikdo nepřihlásil!</h5>";
} else {
	if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
		$winner = '<th scope="col"><u><a class="lottery_sort no-effect" data-order="'.$order.'" id="l.lottery_winner" style="font-family: \'Baloo\', cursive;">Výherce</a></u></th>';
	}
	echo '<div class="table-responsive">
	<table class="table table-striped table-hover table-sm text-center border">
		<thead>
			<tr>
				<th scope="col"><u><a class="lottery_sort no-effect" data-order="'.$order.'" id="u.username" style="font-family: \'Baloo\', cursive;">Uživatel</a></u></th>
				<th scope="col"><u><a class="lottery_sort no-effect" data-order="'.$order.'" id="l.lottery_joined" style="font-family: \'Baloo\', cursive;">Datum registrace</a></u></th>
				'.$winner.'
			</tr>
		</thead>
	<tbody>';
	if (mysqli_num_rows($lottery_user_query) != 0) {
		$lottery_user_row = mysqli_fetch_array($lottery_user_query);
		if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			if ($lottery_user_row['lottery_place'] == 0) {
				$winner_row = '<td class="my-auto">Ne</td>';
			} elseif ($lottery_user_row['lottery_place'] == 1) {
				$winner_row = '<td class="my-auto">1. místo</td>';
			} elseif ($lottery_user_row['lottery_place'] == 2) {
				$winner_row = '<td class="my-auto">2. místo</td>';
			} elseif ($lottery_user_row['lottery_place'] == 3) {
				$winner_row = '<td class="my-auto">3. místo</td>';
			}
		}
		printf("
		<tr class='h-100 sunny-morning-gradient'>
		  <td class='my-auto'><a class='text-primary font-weight-bold' href='".SITE_ROOT."profile_show.php?profile_id=%d'>%s</a></td>
		  <td class='my-auto'>%s</td>
		  %s
		</tr>",
		$_SESSION['user_id'],
		$_SESSION['username'],
		date('d.m.Y', strtotime($lottery_user_row[0])),
		$winner_row);
	}
	while ($row = mysqli_fetch_array($search_query)) {
		if (user_in_group("Administrator", $user_id) || user_in_group("Main administrator", $user_id)) {
			if ($row['lottery_place'] == 0) {
				$winner_row = '<td class="my-auto">Ne</td>';
			} elseif ($row['lottery_place'] == 1) {
				$winner_row = '<td class="my-auto">1. místo</td>';
			} elseif ($row['lottery_place'] == 2) {
				$winner_row = '<td class="my-auto">2. místo</td>';
			} elseif ($row['lottery_place'] == 3) {
				$winner_row = '<td class="my-auto">3. místo</td>';
			}
		}
		if ($row['lottery_userid'] == $_SESSION['user_id']) {
			continue;
		}
		printf("
		<tr class='h-100'>
			<td class='my-auto'><a class='text-primary font-weight-bold' href='".SITE_ROOT."profile_show.php?profile_id=%d'>%s</a></td>
			<td class='my-auto'>%s</td>
			%s
		</tr>",
		$row['lottery_userid'],
		$row['username'],
		date('d.m.Y', strtotime($row['lottery_joined'])),
		$winner_row);
	}
	echo $table_end .= '</tbody></table></div>';
}
?>