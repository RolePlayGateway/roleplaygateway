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

$template->assign_vars(array(
  'S_PAGE_ONLY' => true,
));

page_header('Recent '.$config['sitename'] . ' Changes');

$template->set_filenames(array(
	'body' => 'changelog.html'
	)
);

page_footer();
