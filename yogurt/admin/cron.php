<?php

include('/var/www/vhosts/roleplaygateway.com/httpdocs/yogurt/config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    echo "Connect failed: ". mysqli_connect_error();
} else {

	$sql = "SELECT * FROM evolutions";
	if ($result = $mysqli->query($sql)) {
		while ($row = mysqli_fetch_assoc($result)) {
			
			$sql = "UPDATE creatures SET evolution = ".$row['id']." WHERE
				views >= ".$row['views']." AND
				met >= ".$row['met']." AND
				evolution != ".$row['id']."
				
				";
				
			if ($evolved = $mysqli->query($sql)) {
			
				echo "<!-- Evolved creature id #".$row['id']."-->\n";
			
			}
		
		}
	}
	
}

?>