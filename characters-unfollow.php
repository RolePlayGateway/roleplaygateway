<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$roleplayURL = request_var('roleplayURL', '');
$characterURL = request_var('characterURL', '');
$characterID = request_var('characterID', 0);

if (empty($characterID)) {
	$sql = 'SELECT id, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
	$roleplayResult = $db->sql_query($sql);
	$roleplay = $db->sql_fetchrow($roleplayResult);
	$db->sql_freeresult($roleplayResult);

	$sql = 'SELECT id, url FROM rpg_characters WHERE url = "'.$db->sql_escape($characterURL).'" AND roleplay_id = '.(int) $roleplay['id'];
	$characterResult = $db->sql_query($sql);
	$character = $db->sql_fetchrow($characterResult);
	$db->sql_freeresult($characterResult);
} else {
	$sql = 'SELECT id, url FROM rpg_characters WHERE id = '.(int) $characterID;
	$characterResult = $db->sql_query($sql);
	$character = $db->sql_fetchrow($characterResult);
	$db->sql_freeresult($characterResult);
}

if (empty($character)) {
	trigger_error('No such character found.');
}

$sql = 'DELETE FROM rpg_characters_followed WHERE `character_id` = '.(int) $character['id'].' AND user_id = '.$user->data['user_id'];
$db->sql_query($sql);

header('Content-Type: application/json');
echo(json_encode(array(
	'status' => 'success',
	'message' => 'Character successfully unfollowed!'
)));

?>
