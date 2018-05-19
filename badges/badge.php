<?php
header("Content-type: image/jpg");

$image = imagecreatefrompng('blue-468x60.png');

$white = imagecolorallocate($image,255,255,255);
$black = imagecolorallocate($image,0,0,0);

$font = 'visitor-tt2-brk.ttf';

if (!include('../config.php')) {

	imagettftext($image,12,0,35,12,$white,$font,'Sorry, an error has occurred.');
	
	imagepng($image);
		
	die();		
}

if (!mysql_connect($dbhost,$dbuser,$dbpasswd))
{
	echo "fail.";
}
if (!mysql_select_db($dbname))
{
	echo "Dil.";
}

//get string from URL, member id
$id = mysql_real_escape_string(intval($_GET['id']));

$sql = "SELECT * FROM gateway_user_stats us
	WHERE us.user_id = ".$id;

if( !($result = mysql_query($sql)) ) {
	echo "error.";
} else {

	while($row = mysql_fetch_assoc($result)) {
	
		$sql = "SELECT username,user_avatar_type FROM gateway_users WHERE user_id = ".$row['user_id'];
		
		if(!$result = mysql_query($sql)) {
			echo "Couldn't get username.";
		} else {
		
			while($user_row = mysql_fetch_assoc($result)) {
				$username = $user_row['username'];
				
				switch ($user_row['user_avatar_type'])
				{
					case "1":
						$avatar = "images/avatars/".$row['user_avatar'];
					break;
					case "2":
					
						@$handle = fopen($row['user_avatar'], "rb");
						@$contents = stream_get_contents($handle);
						@fclose($handle);
						
						$user_avatar = substr($row['user_avatar'],strrpos($row['user_avatar'],"/"));
						
						$fh = fopen('images/avatars/remote/'.$user_avatar, 'w+');
						fwrite($fh, $contents);
						fclose($fh);
					
						$avatar = "images/avatars/remote/".$user_avatar;
					break;
				}
				
				
				
			}
			
			

			
		}
		
		
		
		
		imagettftext($image,12,0,320,12,$white,$font,"www.roleplaygateway.com");
		
		imagettftext($image,12,0,135,12,$white,$font,$username);
		imagettftext($image,12,0,135,24,$white,$font,"Reputation:");
		imagettftext($image,12,0,135,36,$white,$font,"Words Written:");

		
		imagettftext($image,12,0,225,24,$white,$font,$row['prs_reputation']);
		imagettftext($image,12,0,225,36,$white,$font,$row['total_words']);
	}
}

mysql_close();

imagepng($image);

?>