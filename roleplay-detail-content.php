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

$sql = 'SELECT text,dateTime FROM ajax_chat_messages WHERE id = '.(int) $content['old_chat_id'];
$result = $db->sql_query($sql);
$message = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$obj = new Array();

$obj['content'] = $content;
$obj['message'] = $message;

echo json_encode($obj);

?>
