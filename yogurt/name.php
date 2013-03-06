<?php 

require('includes/header.php');

require('config.php');

	$creature_id = intval($_REQUEST['id']);
	$creature_name = mysqli_real_escape_string($_REQUEST['name']);
	$user_id = $_SESSION['user_id'];

if (isset($creature_id) && isset($creature_name)) {
		

	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()) {
		echo "Connect failed: ". mysqli_connect_error();
	} else {

		session_start();

		$creature_id = intval($_REQUEST['id']);
		$creature_name = mysqli_real_escape_string($_REQUEST['name']);
		$user_id = $_SESSION['user_id'];

		$sql = "UPDATE creatures SET name = '".$creature_name."' WHERE id = ".$creature_id." AND owner = ".$user_id;

		if (($result = $mysqli->query($sql))===false) {
			echo "Failed to name this creature.";
		} else {
			echo "You named this creature!";
		}

		mysqli_close($mysqli);
	}
} else {

?>

<h2>Name Your Creature</h2>
<form action="name.php" method="post">
<strong>New Name:</strong> <input name="name" /><br />
<input type="submit" value="Name it!" />
</form>

<?

require('includes/footer.php');

?>