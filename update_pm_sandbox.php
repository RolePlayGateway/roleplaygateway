<?php
define('IN_PHPBB', true);
define('PHPBB_ROOT_PATH','/var/www/html/');
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
// PRS
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

require('/var/www/html/config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
} else {
	$curtime = time();

	$sql = "SELECT a.msg_id,a.author_id,b.username,message_time FROM gateway_privmsgs_to as a
		INNER JOIN gateway_users as b ON a.user_id=b.user_id
		INNER JOIN gateway_privmsgs as c ON a.msg_id=c.msg_id
		WHERE user_spammer=1 AND folder_id=-2";
	
	$mysqli->query($sql);
	
	echo "Current time:".$curtime."\n";
	if ($result = $mysqli->query($sql)) {
		while ($row = $result->fetch_assoc()) {
			$msg_time=$row['message_time'];
			$msg_time=$msg_time+ rand(15,72)*3600;
			echo "Message by:".$row['username'].":time(with random add time):".$msg_time."\n";
			if ($msg_time < $curtime)
			{
				echo "Message moved\n";
				$sql ="UPDATE gateway_privmsgs_to SET folder_id='-1' WHERE folder_id=-2 AND author_id='".$row['author_id']."' AND msg_id='".$row['msg_id'] ."'";
				$update_result = $mysqli->query($sql);
			}
		}
		
		$result->close();
	} else {
		echo "failed: ".mysqli_error($mysqli);
	}
        
	echo "Completed";

	$mysqli->close();
}

?>
