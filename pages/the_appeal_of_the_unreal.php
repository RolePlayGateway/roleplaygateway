<?php

define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();


page_header('The Appeal of the Unreal');

$template->set_filenames(array(
	'body' => 'page_appeal_unreal.html')
);

page_footer();


?>