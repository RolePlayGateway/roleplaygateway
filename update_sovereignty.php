<?php
$phpbb_root_path = '/var/www/html/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

$sql = 'SELECT id,name,roleplay_id FROM rpg_places';
$result = $db->sql_query($sql);
while ($place = $db->sql_fetchrow($result)) {
	echo "\nLocation ".$place['name'].' in roleplay '.$place['roleplay_id'].'...';
	
	echo "\n\tTop roleplayesr:";
	
	$sql = 'SELECT COUNT(*),author_id,username FROM rpg_content a INNER JOIN gateway_users u ON u.user_id = a.author_id WHERE roleplay_id = 1 GROUP BY author_id order by count(*) desc;'
	
}
$db->sql_freeresult($result);

?>