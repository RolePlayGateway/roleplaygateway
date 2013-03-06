<?php 

require('includes/header.php');

require('config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    echo "Connect failed: ". mysqli_connect_error();
} else {

	session_start();

	$creature_id = intval($_REQUEST['id']);
	$user_id = $_SESSION['user_id'];
	
	$sql = "UPDATE creatures SET owner = 0 WHERE id = ".$creature_id." AND owner = ".$user_id;
	
	if (($result = $mysqli->query($sql))===false) {
		echo "Failed to abandon this creature.";
	} else {
		echo "You abandoned this creature!";
	}
	
	mysqli_close($mysqli);
}

require('includes/footer.php');

?>