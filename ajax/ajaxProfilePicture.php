<?php
require_once '../../../scripts/app_config.php';
require_once '../../../scripts/db_connect.php';
require_once '../../../scripts/authorize.php';
//require_once 'imageRotate.php';

session_start();
authorize_user();
update_activity();

if(isset($_POST["image"])){
	$data = $_POST["image"];
	$image_array_1 = explode(";", $data);
	$image_array_2 = explode(",", $image_array_1[1]);
	$data = base64_decode($image_array_2[1]);
	/*$image = imagecreatefromstring($data);
	$exif = exif_read_data($data);
		if(!empty($exif['Orientation'])) {
			switch($exif['Orientation']) {
				case 8:
					$image = imagerotate($image,90,0);
					break;
				case 3:
					$image = imagerotate($image,180,0);
					break;
				case 6:
					$image = imagerotate($image,-90,0);
					break;
			}
		}*/
	$time = time();
	$imageNamePath = '../profile_pictures/'.$_SESSION['username'].$time . '.png';
	$imageNameSQL = 'profile_pictures/'.$_SESSION['username'].$time . '.png';
	$insert = sprintf("UPDATE users SET image_path = '%s' WHERE user_id = '%d'",
	$imageNameSQL,
	mysqli_real_escape_string($connect, $_SESSION['user_id']));
	$insert_query = mysqli_query($connect, $insert);
	if ($insert_query) {
		file_put_contents($imageNamePath, $data);
		$_SESSION['image_path'] = $imageNameSQL;
	}
}