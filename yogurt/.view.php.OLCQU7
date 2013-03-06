<?php
header("Content-type: image/png");
$image = imagecreatefrompng("images/WATERorb.png");
imagealphablending($image, true);
imagesavealpha($image, true);

require('config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    echo "Connect failed: ". mysqli_connect_error();
} else {

	$creature_id = intval($_REQUEST['id']);
	$viewer = md5($_SERVER['REMOTE_ADDR']);
	
	$ignore = $_REQUEST['ignore'];
	
	if (($ignore != "true")) {
		$sql = "INSERT INTO views ( `id`,`viewer` ) VALUES ( '".$creature_id."','".$viewer."' )";
		
		if (($result = $mysqli->query($sql))===false) {
			$sql = "UPDATE creatures SET views=views+1 WHERE id=".$creature_id." AND owner != 0";
			$mysqli->query($sql);
		} else {
			$sql = "UPDATE creatures SET views=views+1,met=met+1 WHERE id=".$creature_id." AND owner != 0";
			$mysqli->query($sql);
		}
	}
	//echo "Successfully viewed creature #".$creature_id.".";
	
	mysqli_close($mysqli);
}
imagepng($image);
?>