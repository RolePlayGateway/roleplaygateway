<?php

require('includes/header.php');
			
session_start();

if(isset($_SESSION['user_id'])) {
	echo '<h2>Your Backpack</h2>';
	
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()) {
	    echo "Connect failed: ". mysqli_connect_error();
	} else {
		$sql = "SELECT * FROM creatures WHERE owner = ".$_SESSION['user_id'];
		
		if ($result = $mysqli->query($sql)) {
			if (mysqli_num_rows($result) == 0 ) {
				echo "You don't own any creatures.";
			} else {
			
				echo "Your creatures:";
				
				while ($row = mysqli_fetch_assoc($result)) {
					$this_creature = new creature();
				
					$this_creature->creature_id = $row['id'];
					
					echo '<div class="actions"><a href="http://gwing.net/yogurt/abandon.php?id='.$this_creature->creature_id.'">Abandon me... (please, no!)</a></div>';
					
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