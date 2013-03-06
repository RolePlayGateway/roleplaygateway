<?php

require('includes/header.php');

echo "<h2>The Hatchery</h2>";
			
if(isset($_SESSION['user_id'])) {
	
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()) {
	    echo "Connect failed: ". mysqli_connect_error();
	} else {
		$sql = "SELECT * FROM creatures WHERE owner = 0";
		
		if ($result = $mysqli->query($sql)) {
			if (mysqli_num_rows($result) == 0 ) {
				echo "Aww, the hatchery is empty. You'll have to try again later. Sorry!";
			} else {
				
				while ($row = mysqli_fetch_assoc($result)) {
					$this_creature = new creature();
				
					$this_creature->creature_id = $row['id'];
					echo $this_creature->show("true");
				}
				
			}
		} else {
			echo "fail.";
			echo mysqli_error($mysqli);
		}
	}
	
} else {
	echo 'You are <strong>not</strong> logged in. <a href="login.php">Log In</a>';
}


require('includes/footer.php');

?>