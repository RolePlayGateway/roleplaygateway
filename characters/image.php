<?php

define('IN_PHPBB', true);
$phpbb_root_path = '../';

$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);  // moar setup...
$user->setup();

// just so we know it is broken
error_reporting(E_ALL);

$character_id = request_var('character_id', '');

// some basic sanity checks
if(isset($character_id) && is_numeric($character_id)) {

	header("Content-type: image/jpeg");

	// get the image from the db
	$sql = "SELECT image,image_type FROM rpg_characters WHERE id = ".$db->sql_escape($character_id);

	// the result of the query
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
			echo $row['image'];
	}

} else {
	echo 'Please use a real id number';
}
	
?>