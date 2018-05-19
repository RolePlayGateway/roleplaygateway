<?php
	
	require('includes/library.php');
	
	$my_creature = new creature();
	
	$my_creature->creature_id = 1;
	
	echo $my_creature->show();
	
?>