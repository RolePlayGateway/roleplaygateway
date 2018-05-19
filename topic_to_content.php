<?php
$phpbb_root_path = '/var/www/html/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$thread_id = request_var('thread_id',0);
$place_id = request_var('place_id',0);
$roleplay_id = request_var('roleplay_id',0);

$sql = 'SELECT id,roleplay_id FROM rpg_places WHERE id = '.(int) $place_id;
$result = $db->sql_query($sql);
$place = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if ($place['roleplay_id'] && $place['id']) {

	$sql = 'SELECT * FROM gateway_posts WHERE topic_id = '.(int) $thread_id;

	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result)) {

		echo "\nInserting post#".$row['post_id']." into the content database...";
		$sql = 'INSERT IGNORE INTO rpg_content
							(roleplay_id,	place_id,	author_id, text, bbcode_bitfield,	bbcode_uid, written, old_post_id)
					VALUES 	('.(int) $roleplay_id.','.(int) $place_id.', '.(int) $row["poster_id"].',"'.$db->sql_escape($row["post_text"]).'","'.$row["bbcode_bitfield"].'","'.$row["bbcode_uid"].'","'.date("Y-m-d H:i:s", $row["post_time"]).'",'.$row["post_id"].')';
		
		$db->sql_query($sql);

	}

	$db->sql_freeresult($result);
	
	die('done');
	
} else {
	trigger_error('There is no place by that ID.');
}

?>
