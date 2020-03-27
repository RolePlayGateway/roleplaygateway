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

$roleplayID = (int) $_GET['roleplayID'];
$contentID = (int) $_REQUEST['contentID'];
$authorID = (int) $user->data['user_id'];

$content = $db->sql_escape($_REQUEST['content']);

$sql = 'INSERT INTO rpg_footnotes (author,content_id,footnote) VALUES ('.(int) $authorID.','.(int) $contentID.',"'.$content.'");';
$db->sql_query($sql);

echo json_encode(array(
  'status' => 'success',
  'message' => 'Content added.'
));

//meta_refresh(3, "http://www.roleplaygateway.com/roleplay/");
//trigger_error('Successfully submited this roleplay for closure.');

?>
