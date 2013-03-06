<?php

function get_roleplays() {
	global $config, $db, $user, $auth, $phpbb_root_path, $phpEx;
	
	$sql = "SELECT title FROM rpg_roleplays";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
	
		$roleplays[]	= $row['title'];
		
	}
	$db->sql_freeresult($result);
	
	return $roleplays;
}

function get_topics() {
	global $config, $db, $user, $auth, $phpbb_root_path, $phpEx;
	
	$sql = "SELECT topic_title FROM gateway_topics ORDER BY topic_time DESC";
	$result = $db->sql_query_limit($sql,10,null,300);
	while ($row = $db->sql_fetchrow($result)) {
	
		$topics[]	= $row['topic_title'];
		
	}
	$db->sql_freeresult($result);
	
	return $topics;
}

?>