<?php

require('../config.php');

session_start();

if (!$_SESSION['user_id']) {
	echo "No session user id.";
} elseif ($_SESSION['user_id'] != 1) {
	echo "You're not the admin.";	
} else {
	echo "You're the admin.";
	
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()) {
	    echo "Connect failed: ". mysqli_connect_error();
	} else {
	
		$sql = "SELECT * FROM species";
		
		if ($result = $mysqli->query($sql)) {

			while ($row = mysqli_fetch_assoc($result)) {
				
				for ($i = 1; $i <= $row['frequency']; $i++) {
				
					$possible_types[] = $row['id'];

				}
			}
			
			$species = $possible_types[rand(0,count($possible_types)-1)];
			
			$sql = "INSERT INTO creatures (name,species) VALUES ('noname','".$species."')";
			
			if ($result = $mysqli->query($sql)) {
				echo "query win. creature created.";
			} else {
				echo "query fail. no creature. :(";
				echo mysqli_error($mysqli);
			}			
		
		} else {
			echo "query fail. no creature. :(";
			echo mysqli_error($mysqli);
		}		

	}	
	
}

?>