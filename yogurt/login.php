<?php 

require('includes/header.php');

require('config.php');

$username = $_REQUEST['username'];
$password = md5($_REQUEST['password']);

if (isset($password) && isset($username)) {

	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

	if (mysqli_connect_errno()) {
	    echo "Connect failed: ". mysqli_connect_error();
	} else {

		$sql = "SELECT * FROM users WHERE email = '".$username."'";
		
		if ($result = $mysqli->query($sql)) {
			if (mysqli_num_rows($result) == 0 ) {
				echo "No such username.";
			} else {
			
				while ($row = mysqli_fetch_assoc($result)) {
					
					if ($row['password'] == $password) {
						echo 'Login win as user id '.$row["id"].'. <a href="http://gwing.net/yogurt">...continue...</a>';
						session_start();
						$_SESSION['user_id'] = $row['id'];
					} else {
						echo "Password did not match.";
					}
				
				}
			}
		} else {
				echo "Login fail. ".$username.":".$password.mysqli_error($mysqli);
		}
		
		mysqli_close($mysqli);
	}
} else {
?>
<h2>Log In</h2>
<form action="login.php" method="post">
<strong>Username:</strong> <input name="username" /><br />
<strong>Password:</strong> <input name="password" type="password" />
<input type="submit" value"Register!" />
</form>
<a href="register.php">Register</a>
<?php

}

require('includes/footer.php');

?>