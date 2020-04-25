<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';

session_start();
authorize_user();
//authorize_user(array("Main administrator"));
update_activity();

$user_id = $_SESSION['user_id'];

function image_fix_orientation(&$image, $filename) {
    $exif = exif_read_data($filename);
	echo $exif['Orientation'];
    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
		}
		imagejpeg($image, $filename);
    }
}

if (($_GET['action'] == 'commentAdd') && isset($_GET['for']) && isset($_POST['new_comment'])) {
	$for = $_GET['for'];
	$new_content = htmlspecialchars(trim($_POST['new_comment']));

	if (empty($new_content)) {
		$_SESSION['error_message'] = "Nelze přidat prázdný komentář!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$for);
		exit();
	}

	$insert_comment = sprintf("INSERT INTO ticket_comments (ticket_id, comment_createdby, comment_content, comment_created) VALUES ('%d', '%d', '%s', '%s');",
	mysqli_real_escape_string($connect, $for),
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $new_content),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$insert_comment_query = mysqli_query($connect, $insert_comment);
	if ($insert_comment_query) {
		$_SESSION['success_message'] = "Váš komentář byl <span class='text-success font-weight-bold'>úspěšně</span> přidán!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$for);
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při přidávaní komentáře.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$for);
		exit();
	}
} elseif (($_GET['action'] == 'changeStatus') && isset($_GET['for'])) {
	authorize_user(array("Main administrator"));
	$for = $_GET['for'];

	$get_status = sprintf("SELECT ticket_answered FROM tickets WHERE ticket_id = '%d';",
	mysqli_real_escape_string($connect, $for));
	$get_status_query = mysqli_query($connect, $get_status);
	$get_status_row = mysqli_fetch_row($get_status_query);
	if ($get_status_row[0] == 0) {
		$change_status = sprintf("UPDATE tickets SET ticket_answered = 1 WHERE ticket_id = '%d';",
		mysqli_real_escape_string($connect, $for));
		$change_status_query = mysqli_query($connect, $change_status);
		if ($change_status_query) {
			$_SESSION['success_message'] = "Status tohoto tiketu byl <span class='text-success font-weight-bold'>úspěšně</span> změněn na 'Uzavřen'!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$for);
			exit();
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při změně statusu tiketu.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$for);
			exit();
		}
	} else {
		$change_status = sprintf("UPDATE tickets SET ticket_answered = 0 WHERE ticket_id = '%d';",
		mysqli_real_escape_string($connect, $for));
		$change_status_query = mysqli_query($connect, $change_status);
		if ($change_status_query) {
			$_SESSION['success_message'] = "Status tohoto tiketu byl <span class='text-success font-weight-bold'>úspěšně</span> změněn na 'Otevřen'!";
			$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$for);
			exit();
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při změně statusu tiketu.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$for);
			exit();
		}
	}
} elseif (($_GET['action'] == 'removeComment') && isset($_GET['comment_id']) && isset($_GET['for'])) {
	authorize_user(array("Main administrator"));
	$comment_id = $_GET['comment_id'];
	$ticket_id = $_GET['for'];

	$if_exists = sprintf("SELECT comment_id FROM ticket_comments WHERE comment_id = '%d' AND ticket_id = '%d';",
	mysqli_real_escape_string($connect, $comment_id),
	mysqli_real_escape_string($connect, $ticket_id));
	$if_exists_query = mysqli_query($connect, $if_exists);
	if (mysqli_num_rows($if_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento komentář neexistuje.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$ticket_id);
		exit();
	}

	$remove_comment = sprintf("UPDATE ticket_comments SET comment_visible = 0 WHERE comment_id = '%d' AND ticket_id = '%d';",
	mysqli_real_escape_string($connect, $comment_id),
	mysqli_real_escape_string($connect, $ticket_id));
	$remove_comment_query = mysqli_query($connect, $remove_comment);
	if ($remove_comment_query) {
		$_SESSION['success_message'] = "Vybraný komentář byl <span class='text-success font-weight-bold'>úspěšně</span> odstraněn!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$ticket_id);
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odstraňování komentáře.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."ticket_show?ticket_id=".$ticket_id);
		exit();
	}
} elseif (($_GET['action'] == 'removeTicket') && isset($_GET['for'])) {
	authorize_user(array("Main administrator"));
	$ticket_id = $_GET['for'];

	$if_exists = sprintf("SELECT ticket_id FROM tickets WHERE ticket_id = '%d';",
	mysqli_real_escape_string($connect, $ticket_id));
	$if_exists_query = mysqli_query($connect, $if_exists);
	if (mysqli_num_rows($if_exists_query) == 0) {
		$_SESSION['error_message'] = "Tento tiket neexistuje.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	}

	$remove_ticket = sprintf("UPDATE tickets SET ticket_visible = 0 WHERE ticket_id = '%d';",
	mysqli_real_escape_string($connect, $ticket_id));
	$remove_ticket_query = mysqli_query($connect, $remove_ticket);
	if ($remove_ticket_query) {
		$_SESSION['success_message'] = "Vybraný tiket byl <span class='text-success font-weight-bold'>úspěšně</span> odstraněn!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při odstraňování tiketu.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	}
} elseif (($_GET['action'] == 'createTicket') && isset($_POST['ticket_type']) && isset($_POST['ticket_title']) && isset($_POST['ticket_content'])) {
	$ticket_title = htmlspecialchars(trim($_POST['ticket_title']));
	$ticket_content = htmlspecialchars(trim($_POST['ticket_content']));
	$ticket_type = $_POST['ticket_type'];

	if (empty($ticket_title) || empty($ticket_content)) {
		$_SESSION['error_message'] = "Nelze vytvořit prázdný tiket!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	}

	$if_exists = sprintf("SELECT ticket_id FROM tickets WHERE ticket_title = '%s';",
	mysqli_real_escape_string($connect, $ticket_title));
	$if_exists_query = mysqli_query($connect, $if_exists);
	if (mysqli_num_rows($if_exists_query) != 0) {
		$_SESSION['error_message'] = "Tiket s tímto názvem už existuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	}

	if ($_FILES['ticket_image']['size'] > 5000000) {
		$_SESSION['error_message'] = "Soubor je větší než 5 MB!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	}

	$insert_ticket = sprintf("INSERT INTO tickets (ticket_createdby, ticket_title, ticket_content, ticket_type, ticket_created) VALUES ('%d', '%s', '%s', '%s', '%s');",
	mysqli_real_escape_string($connect, $user_id),
	mysqli_real_escape_string($connect, $ticket_title),
	mysqli_real_escape_string($connect, $ticket_content),
	mysqli_real_escape_string($connect, $ticket_type),
	mysqli_real_escape_string($connect, date('Y-m-d H:i:s')));
	$insert_ticket_query = mysqli_query($connect, $insert_ticket);

	if ($insert_ticket_query) {
		if ($_FILES['ticket_image']['size'] == 0) {
		} else {
			$inserted_id = mysqli_insert_id($connect);

			$uploadDir = "ticket_images/";
			$allowedTypes = array('jpg', 'jpeg', 'png');

			$statusMsg = $errorMsg = $insert_values = $errorUpload = $errorUploadType = '';
			$file_name = $_FILES['ticket_image']['name'];
			$file_tmp_name = $_FILES['ticket_image']['tmp_name'];

			//File upload path
			$date = date('dmy_His');
			$fileName = basename($file_name);
			//$newFileName = $date."_".$username;
			$uploadFilePath = $uploadDir . $fileName;

			//Check file type
			$fileType = pathinfo($uploadFilePath, PATHINFO_EXTENSION);
			if (in_array($fileType, $allowedTypes)) {
				//Upload file on server
				if (move_uploaded_file($file_tmp_name, $uploadDir.$_SESSION['username']."_".$fileName)) {
					$insert_values .= $uploadDir.$_SESSION['username']."_".mysqli_real_escape_string($connect, $fileName);
				} else {
					$errorUpload .= $file_name.', ';
				}
			} else {
				$errorUploadType .= $file_name.', ';
			}
			$insert_values = trim($insert_values,',');
			$insert_image = sprintf("UPDATE tickets SET ticket_image = '%s' WHERE ticket_id = '%d'",
				$insert_values,
				mysqli_real_escape_string($connect, $inserted_id));
			$insert_image_query = mysqli_query($connect, $insert_image);

			//Fix image orientation
			if (exif_imagetype($insert_values) == IMAGETYPE_JPEG) {
				$im = @imagecreatefromjpeg($insert_values);
				image_fix_orientation($im, $insert_values);
			} elseif (exif_imagetype($insert_values) == IMAGETYPE_PNG) {
				$im = @imagecreatefrompng($insert_values);
				image_fix_orientation($im, $insert_values);
			}
		}
		$_SESSION['success_message'] = "Váš tiket byl <span class='text-success font-weight-bold'>úspěšně</span> vytvořen!";
		$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	} else {
		$_SESSION['error_message'] = "Něco se stalo špatně při vytváření tiketu.";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."tickets");
		exit();
	}
} else {
	handle_error("Došlo k neočekávané chybě.", "ticket_action");
}