<?php

define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();


page_header('Involving Gambling in Your RPG Campaign');

$template->set_filenames(array(
	'body' => 'page_gambling.html')
);

page_footer();


?>