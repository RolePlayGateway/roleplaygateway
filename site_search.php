<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');


page_header('Searching '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'google_search_body.html')
);

page_footer();

?>
