<?php

require_once '../../scripts/app_config.php';
require_once '../../scripts/db_connect.php';
require_once '../../scripts/authorize.php';
require_once '../../scripts/view.php';

session_start();
authorize_user();
update_activity();

//Fix image orientation
//from: https://medium.com/thetiltblog/fixing-rotated-mobile-image-uploads-in-php-803bb96a852c
function correctImageOrientation($filename) {
	if (function_exists('exif_read_data')) {
	  $exif = exif_read_data($filename);
	  if($exif && isset($exif['Orientation'])) {
		$orientation = $exif['Orientation'];
		if($orientation != 1){
		  $img = imagecreatefromjpeg($filename);
		  $deg = 0;
		  switch ($orientation) {
			case 3:
			  $deg = 180;
			  break;
			case 6:
			  $deg = 270;
			  break;
			case 8:
			  $deg = 90;
			  break;
		  }
		  if ($deg) {
			$img = imagerotate($img, $deg, 0);        
		  }
		  // then rewrite the rotated image back to the disk as $filename 
		  imagejpeg($img, $filename, 95);
		} // if there is some rotation necessary
	  } // if have the exif orientation info
	} // if function exists      
  }

if (isset($_POST['submit'])) {	
$item_name = htmlspecialchars(trim(ucfirst($_POST['item_name'])));
$item_description = htmlspecialchars(trim(ucfirst($_POST['item_description'])));
$item_price = htmlspecialchars(trim($_POST['item_price']));
$item_type = htmlspecialchars(trim($_POST['item_type']));
$item_subject = htmlspecialchars(trim(ucfirst(strtolower($_POST['item_subject']))));
$school_class = htmlspecialchars(trim($_POST['school_class']));
$teacher = htmlspecialchars(trim(ucfirst(strtolower($_POST['teacher']))));
if (is_numeric($item_price)) {
	//Check if exists item with same name in user's school
	$name_check = sprintf("SELECT item_name FROM shop WHERE item_name = '%s' AND item_description = '%s' AND school_id = '%d';",
	mysqli_real_escape_string($connect, $item_name),
	mysqli_real_escape_string($connect, $item_description),
	mysqli_real_escape_string($connect, $_SESSION['school_id']));
	$name_check_query = mysqli_query($connect, $name_check);
	if (/*mysqli_num_rows($query_check_check) !=0 || */mysqli_num_rows($name_check_query) !=0 ) {
		$_SESSION['error_message'] = "Test se stejným názvem a stejným popiskem již existuje!";
		$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
		header("Location: " . SITE_ROOT ."item_add");
		exit();
	} else {
		//Count attached images
		$countfiles = count($_FILES['files']['name']);
		if ($countfiles > 4) {
			$_SESSION['error_message'] = "Můžete nahrát maximálně 4 přilohy!";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."item_add");
			exit();
		}
		for ($i=0; $i < $countfiles - 3; $i++) { 
			if ($_FILES['files']['size'][$i] > 5000000) {
				$_SESSION['error_message'] = "Maximální velikost každé přílohy je 5 MB!";
				$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
				header("Location: " . SITE_ROOT ."item_add");
				exit();
			}
		}
		if (isset($_POST['item_answers'])) {
			$itemAnswers = 1;
		} else {
			$itemAnswers = 0;
		}
		$item_mysql = sprintf("INSERT INTO shop (item_name, item_createdby_username, item_createdby_userid, item_description, item_price, create_date, school_id, item_type, item_subject, school_class, teacher, item_answers) VALUES ('%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%d');",
		mysqli_real_escape_string($connect, $item_name),
		mysqli_real_escape_string($connect, $_SESSION['username']),
		mysqli_real_escape_string($connect, $_SESSION['user_id']),
		mysqli_real_escape_string($connect, $item_description),
		mysqli_real_escape_string($connect, $item_price),
		mysqli_real_escape_string($connect, date('Y-m-d H:i:s')),
		mysqli_real_escape_string($connect, $_SESSION['school_id']),
		mysqli_real_escape_string($connect, $item_type),
		mysqli_real_escape_string($connect, $item_subject),
		mysqli_real_escape_string($connect, $school_class),
		mysqli_real_escape_string($connect, $teacher),
		$itemAnswers);
		$item_mysql_query = mysqli_query($connect, $item_mysql);
		$inserted_id = mysqli_insert_id($connect);
		//setcookie("item_created", true, time()+20);
		if ($item_mysql_query) {
			// Looping all files
			for($i = 0; $i < $countfiles; $i++){
				$filename = $_FILES['files']['name'][$i];
				// Upload file
				$newFilename = 'uploads/'.$_SESSION['username'].'_'.date('dmy_His').mysqli_real_escape_string($connect, $filename);
				if(move_uploaded_file($_FILES['files']['tmp_name'][$i], $newFilename)){
					$insert_values = "('".$newFilename."', '".mysqli_real_escape_string($connect, date('Y-m-d H:i:s'))."', '".$inserted_id."', '".$_SESSION['user_id']."')";
					$insert = sprintf("INSERT INTO images (file_path, upload_date, shop_id, image_createdby_userid) VALUES %s ;", $insert_values);
					$insert_query = mysqli_query($connect, $insert);
					//Rotate image if the format is .jpg or .JPG (Apple)
					if ((strpos($newFilename, '.jpg') !== false) || (strpos($newFilename, '.JPG') !== false) || (strpos($newFilename, '.jpeg') !== false) || (strpos($newFilename, '.JPEG') !== false)) {
						correctImageOrientation($newFilename);
					}
				}
			}
			if ($insert_query) {
				//Debug mode
				/*print_r($_FILES);
				echo $countfiles;
				exit();*/
				$_SESSION['success_message'] = "Děkujeme Vám za přidání nového testu do naší databáze! Přidaný test bude zkontrolován našimi Validádorami, Supporty, nebo Administrátory co nejdříve to půjde!";
				$_SESSION['js_modal_show'] = "<script>$('#successModal').modal('show')</script>";
				header("Location: " . SITE_ROOT ."profile");
				exit();
			} else {
				//print_r($_FILES);
				//echo $insert_values;
				//exit();
				$_SESSION['error_message'] = "Omlouváme se, ale došlo k chybě při nahrávaní přílohy!";
				$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
				header("Location: " . SITE_ROOT ."item_add");
				exit();
			}
		} else {
			$_SESSION['error_message'] = "Něco se stalo špatně při nahrávaní přílohy k testu do databáze.";
			$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
			header("Location: " . SITE_ROOT ."item_add");
			exit();
		}
	}
} else {
	$_SESSION['error_message'] = "Zadaná částka je špatná!";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."item_add");
	exit();
}

	$_SESSION['error_message'] = "Došlo k neočekávané chybě!";
	$_SESSION['js_modal_show'] = "<script>$('#errorModal').modal('show')</script>";
	header("Location: " . SITE_ROOT ."item_add");
	exit();
	//echo $statusMsg;
}