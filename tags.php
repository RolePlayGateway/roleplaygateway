<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_phpBBFolk.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$template->assign_vars(array(
	'S_PAGE_ONLY' => true,
  'TAG_CLOUD' => get_tag_cloud(10, 50, $config['pbf_colour1'], $config['pbf_colour2'], 250)
  //
  //get_tag_cloud($min_size, $max_size, $col1, $col2, $limit)
));

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'tags.html'
));

page_footer();

?>
