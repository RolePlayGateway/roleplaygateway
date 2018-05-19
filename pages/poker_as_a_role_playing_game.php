<?php

define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();


page_header('Poker as a Role Playing Game');

$template->set_filenames(array(
	'body' => 'page_poker.html')
);

page_footer();


?>