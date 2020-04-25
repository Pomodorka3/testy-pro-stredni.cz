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
$ticket_id = $_POST['ticketId'];
$i = 0;

$select_comments = sprintf("SELECT c.comment_id, c.comment_createdby, c.comment_content, c.comment_created, u.username, u.image_path, ug.group_id FROM ticket_comments c, users u LEFT JOIN users_groups ug ON u.user_id = ug.user_id WHERE c.ticket_id = '%d' AND u.user_id = c.comment_createdby AND c.comment_visible = 1",
mysqli_real_escape_string($connect, $ticket_id));
$count_comments = sprintf("SELECT COUNT(*) FROM ticket_comments c, users u LEFT JOIN users_groups ug ON u.user_id = ug.user_id WHERE c.ticket_id = '%d' AND u.user_id = c.comment_createdby AND c.comment_visible = 1", 
mysqli_real_escape_string($connect, $ticket_id));
$select_ticket = sprintf("SELECT ticket_createdby FROM tickets WHERE ticket_id = '%d';",
mysqli_real_escape_string($connect, $ticket_id));
$count_comments_query = mysqli_query($connect, $count_comments);

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
	echo "<b>COUNT SQL: </b>".$count_comments;
}

$count_comments_row = mysqli_fetch_row($count_comments_query);
$select_comments .= " ORDER BY c.comment_created ASC";
$total_pages = ceil($count_comments_row[0]/$items_per_page);
$select_comments .= " LIMIT $offset, $items_per_page";

if ($count_comments_row[0] == 0) {
	$count_comments_row[0] = 'Žádné komentáře';
} elseif ($count_comments_row[0] == 1) {
	$count_comments_row[0] = '1 komentář';
} elseif ($count_comments_row[0] < 5) {
	$count_comments_row[0] = $count_comments_row[0].' komentáře';
} else {
	$count_comments_row[0] = $count_comments_row[0].' komentářů';
}

if (DEBUG_MODE == true && user_in_group("Main administrator", $_SESSION['user_id'])) {
	echo "<br><b>Search SQL</b>: ".$select_comments;
	echo "<br><b>Total pages</b>: ".$total_pages;
}
$select_comments_query = mysqli_query($connect, $select_comments);
$select_ticket_query = mysqli_query($connect, $select_ticket);
$select_ticket_row = mysqli_fetch_row($select_ticket_query);

echo $output = '
<div class="card-header border-0 font-weight-bold mb-3">'.$count_comments_row[0].'</div>';
while ($row = mysqli_fetch_array($select_comments_query)) {
	$i++;
	$comment_created = strtotime($row['comment_created']);
	$group = '';
	if ($row['group_id'] == 1) {
		$group = "<span class='text-success' style='font-size: 17px;'>Administrátor</span> |";
	} elseif ($row['group_id'] == 2) {
		$group = "<span class='text-secondary' style='font-size: 17px;'>Validátor</span> |";
	} elseif ($row['group_id'] == 3) {
		$group = "<span class='text-warning' style='font-size: 17px;'>Support</span> |";
	} elseif ($row['group_id'] == 4) {
		$group = "<span class='deep-orange-text' style='font-size: 17px;'>Hlavní administrátor</span> |";
	}/* else {
		$group = "<span style='font-size: 17px;'>(User)</span>";
	}*/
	if (!empty($row['image_path'])) {
		$image_path = $row['image_path'];
	} else {
		$image_path = 'profile_pictures/default/default.png';
	}
	if (user_in_group("Administrator", $user_id)) {
		$deleteButton = '<a data-toggle="modal" data-target="#removeModal'.$i.'"><i class="fas fa-trash-alt text-danger ml-2"></i></a>';
		$deleteModal = '
		<div class="modal fade" id="removeModal'.$i.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-center" role="document">
			<div class="modal-content">
				<div class="modal-header">
				<h4 class="modal-title w-100" id="removeModalLabel'.$i.'">Odstranit vybraný komentář?</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Zavřít">
					<span aria-hidden="true">&times;</span>
				</button>
				</div>
				<div class="modal-body d-flex">
					<a class="btn btn-success btn-sm mx-auto" href="'.SITE_ROOT.'ticket_action.php?action=removeComment&for='.$ticket_id.'&comment_id='.$row['comment_id'].'">Potvrdit</a>
				</div>
			</div>
			</div>
		</div>';
	} else {
		$deleteButton = '';
	}
	printf('
	<div class="media d-block d-md-flex my-4">
		<img class="card-img-64 d-flex mx-auto mb-3 rounded" src="%s" alt="Profilový obrázek" style="background-color:#fff;">
		<div class="media-body text-center text-md-left ml-md-3 ml-0">
			<h5 class="mt-0">
				<p>%s <a href="'.SITE_ROOT.'profile_show.php?profile_id=%d" class="font-weight-bold">%s</a> <span class="font-weight-normal" style="font-size: 13px;"> %s</span>%s</p>
			</h5>
			%s
		</div>
	</div>
	%s',
$image_path,
$group,
$row['comment_createdby'],
$row['username'],
date('d.m.Y H:i', $comment_created),
$deleteButton,
$row['comment_content'],
$deleteModal);
}
//---------------- Pagination (displays, only if there are any comments) ---------------
if ($count_comments_row[0] != "No comments") {
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
//---------------- Add new comment ---------------
if ($select_ticket_row['ticket_answered'] == 0) {
	if (user_in_group("Administrator", $user_id) || user_in_group("Support", $user_id) || user_in_group("Validator", $user_id) || ($_SESSION['user_id'] == $select_ticket_row[0])) {
	echo '<form action="'.SITE_ROOT.'ticket_action.php?action=commentAdd&for='.$ticket_id.'" method="post">
		<textarea class="form-control mx-auto" id="new_comment" rows="3" maxlength="600" name="new_comment" placeholder="Nový komentář" required></textarea>

		<div class="text-center my-4">
			<button class="btn btn-primary btn-sm" type="submit"><i class="far fa-comment mr-2"></i>Přidat</button>
		</div>
	</form>';
	} else {
		echo '<h5 class="text-center font-weight-normal mt-3">Nemáte oprávnění pro přidání nového komentáře.</h5>';
	}
} else {
	echo '<h5 class="text-center font-weight-normal mt-3">Nelze přidat komentář, jelikož tento tiket je <span class="text-danger font-weight-bold">uzavřen</span>.</h5>';
}
?>