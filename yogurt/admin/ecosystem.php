<?php

echo "<h2>Lol. Ecosystem.</h2>";

require('../config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    echo "Connect failed: ". mysqli_connect_error();
} else {

	$sql = "SELECT * FROM creatures";
	if ($result = $mysqli->query($sql)) {
		echo mysqli_num_rows($result)." creatures.";
	}

	$sql = "SELECT * FROM creatures WHERE owner != 0";
	if ($result = $mysqli->query($sql)) {
		echo "<br />".mysqli_num_rows($result)." owned.";
	}

	$sql = "SELECT * FROM species";
	if ($result = $mysqli->query($sql)) {
		while ($row = mysqli_fetch_assoc($result)) {
				for ($i = 1; $i <= $row['frequency']; $i++) {
				
					$possible_types[] = $row['id'];

				}
		}
	}	
	

}

?>