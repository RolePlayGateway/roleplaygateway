<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'config.' . $phpEx);

//error_reporting(0);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

page_header($config['sitename']);

$template->assign_vars(array(
	'TAG_CLOUD'				=> @get_tag_cloud(9, 30, $config['pbf_colour1'], $config['pbf_colour2'], 100000)
));

$template->set_filenames(array(
  'body' => 'tag_index_body.html')
);

page_footer();

?>
