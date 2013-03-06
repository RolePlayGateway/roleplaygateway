<?php 

require('includes/header.php');
require_once('includes/library.php');
require('config.php');


$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    echo "Connect failed: ". mysqli_connect_error();
} else {

	session_start();

	$creature_id = intval($_REQUEST['id']);
	$user_id = $_SESSION['user_id'];
	
	if (isset($user_id)) {
		
		$sql = "UPDATE creatures SET owner = ".$user_id." WHERE id = ".$creature_id;
		
		if (($result = $mysqli->query($sql))===false) {
			echo "Sorry, it looks like you didn't have the right papers. Try again later.";
		} else {
			echo "Congratulations! You were able to make away with the egg!<br /><br />Now you'll want to use the special code (shown below) to help the creature survive!";
			
			$this_creature = new creature();
				
			$this_creature->creature_id = $creature_id;
			
			echo $this_creature->show("true");
		
		}
		
	} else {

		echo "It doesn't look like you're logged in.  You'll have to do that first!";
		
	}
	mysqli_close($mysqli);
}



require('includes/footer.php');

?>