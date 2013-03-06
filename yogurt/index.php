<?php require('includes/header.php'); ?>

<style type="text/css">
	div.creature {
		width:22%;
		display:inline-block;
	}
</style>
	
	<h3>The Orbus</h3>
	<p><em>You enter this mysterious place surrounding by a curiously dry mist.  You can't see more than a few feet in front of you, and you're not quite sure how big the place is.</em></p>
	
	<br />

	</p>
	
<?php

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
	echo "Connect failed: ". mysqli_connect_error();
} else {
	$sql = "SELECT * FROM creatures WHERE owner = 0 LIMIT 3";
	
	if ($result = $mysqli->query($sql)) {
		if (mysqli_num_rows($result) == 0 ) {
			echo "Aww, it looks like the whole room is empty. You'll have to try again later. Sorry!";
		} else {
		
			echo "<strong><em>In front of you are several small orbs that hover mysteriously over the ground.  You look around, but don't see anyone - if you took one, you might be able to get away with it.</em></strong>";
			
			while ($row = mysqli_fetch_assoc($result)) {
				$this_creature = new creature();
				
				echo "\n";
			
				$this_creature->creature_id = $row['id'];
				echo $this_creature->show("true");
			}
			
		}
	} else {
		echo "fail.";
		echo mysqli_error($mysqli);
	}
}

?>

	</p>
		
<? require('includes/footer.php'); ?>