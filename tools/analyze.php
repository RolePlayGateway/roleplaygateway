<?php
define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

page_header('Writing Analysis - Role Playing Tools');

$template->set_filenames(array(
	'body' => 'tools_analyze_body.html',)
);

page_footer();

?>