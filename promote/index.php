<?php

define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
// PRS
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

page_header('Promote Your Roleplay on RolePlayGateway');

$template->set_filenames(array(
	'body' => 'promote_body.html',)
);

page_footer();

?>