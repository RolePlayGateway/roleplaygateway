<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$contentID = (int) $_REQUEST['contentID'];

$sql = 'SELECT id, roleplay_id, place_id, author_id, old_chat_id FROM rpg_content WHERE id = '.(int) $contentID;
$result = $db->sql_query($sql);
$content = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$sql = 'SELECT id,owner FROM rpg_roleplays WHERE id = '.(int) $content['roleplay_id'];
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$game_masters = array();
$sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].")";
$result = $db->sql_query($sql);
while ($gm_row = $db->sql_fetchrow($result)) {
  $game_masters[] = $gm_row['user_id'];
}
$db->sql_freeresult($result);

$isCoGM = (in_array($user->data['user_id'], $game_masters)) ? true : false;
$isModerator = $auth->acl_get('m_');

if ($isCoGM || $isModerator) {
	echo "Content would have been deleted (debug).";
} else {
  	echo "You're not allowed to do that.";
}

?>
