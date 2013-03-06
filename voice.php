<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');


page_header('Voice Chat from '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'voice_page.html')
);

page_footer();

?>