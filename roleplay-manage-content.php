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

$sql = 'SELECT id, roleplay_id, place_id, author_id, old_chat_id, text FROM rpg_content WHERE id = '.(int) $contentID;
$result = $db->sql_query($sql);
$content = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "GET") {

	$sql = 'SELECT text,dateTime FROM ajax_chat_messages WHERE id = '.(int) $content['old_chat_id'];
	$result = $db->sql_query($sql);
	$message = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$obj = array();

	$obj['content'] = $content;
	$obj['message'] = $message;

	echo json_encode($obj);

} else if ($method === "DELETE") {

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
		$sql = 'UPDATE rpg_content SET deleted = CURRENT_TIMESTAMP() WHERE id = ' .(int) $contentID;
		$db->sql_query($sql);
		echo "The content no longer exists in this universe.";
	} else {
		echo "You're not allowed to do that.";
	}
}

?>
