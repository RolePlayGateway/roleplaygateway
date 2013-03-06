<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

   // Check what group a user is in
   if ( !function_exists('group_memberships') )
   {
      include_once($phpbb_root_path . 'includes/functions_user.'.$phpEx);
   }


$groups = group_memberships(array('group_id' => 2634),4);

if ($groups) {
	$is_donator = 'yes';
} else {
	$is_donator = 'no';
}

echo $is_donator;
?>