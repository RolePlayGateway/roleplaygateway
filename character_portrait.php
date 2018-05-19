<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';

$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);  // moar setup...
$user->setup();

// just so we know it is broken
error_reporting(E_ALL);

$character_id = request_var('character_id', 0);

// some basic sanity checks
if(isset($character_id) && is_numeric($character_id)) {

	// get the image from the db
	$sql = "SELECT image,image_type FROM rpg_characters WHERE id = ".$db->sql_escape($character_id);

	// the result of the query
	if (!$result = $db->sql_query($sql)) {
		trigger_error("query failed");
	} else {
		if (!$image = $db->sql_fetchrow($result)) {
			trigger_error("no image");
		} else {
			if (strlen($image['image']) <= 0) {
				trigger_error("image empty");
			} else {
				header("Content-type: ".$image['image_type']);
				echo $image['image'];
			}
		}
	}

} else {
	echo 'Please use a real id number';
}
	
?>