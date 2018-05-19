<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');


$user_id = $_REQUEST['id'];

echo get_user_avatar($user_id, "AVATAR_UPLOAD", 100, 100);
echo "win";
?>
