<?php 

require('includes/header.php');

require('config.php');

$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

if (isset($password) && isset($username)) {

	$password = md5($password);
	$username = mysqli_real_escape_string(strtolower($username));

	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()) {
	    echo "Connect failed: ". mysqli_connect_error();
	} else {

		$sql = "INSERT INTO users (email,password) VALUES ( '".$username."','".$password."')";
		
		if (($result = $mysqli->query($sql))===false) {
			echo "We couldn't register you, sorry. Try again, or contact the admins.";
		} else {
			$user_id = mysqli_insert_id($mysqli);
			session_start();	
			$_SESSION['user_id'] = $user_id;
		
			echo "We have registered your new account and logged you in.";
		}
		
		mysqli_close($mysqli);
	}
} else {
?>
<h2>Register</h2>
<form action="register.php" method="post">
<strong>Username:</strong> <input name="username" /><br />
<strong>Password:</strong> <input name="password" type="password" />
<input type="submit" value"Register!" />
</form>
<?php

}

require('includes/footer.php');

?>