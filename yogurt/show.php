<?php
require('includes/header.php');

$id = intval($_REQUEST['id']);
$my_creature = new creature();
$my_creature->creature_id = $id;

echo '<div style="float:right; border:3px solid blue; padding:10px;"><strong>Want one?</strong><br /><a href="http://gwing.net/yogurt/">Get a creature!</a></div>';
echo $my_creature->show();

require('includes/footer.php');
?>