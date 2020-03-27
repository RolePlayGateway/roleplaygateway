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

$roleplayURL = $_REQUEST['roleplayURL'];
$start      = (int) $_REQUEST['start'];

$sql = 'SELECT id, title, url, require_approval FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$template->assign_vars(array(
  'S_PAGE_ONLY' => true,
  'ROLEPLAY_ID' => $roleplay['id'],
  'ROLEPLAY_NAME' => $roleplay['title'],
  'ROLEPLAY_URL' => $roleplay['url']
));

page_header('Create a Quest | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'rpg-quest-manager.html'
	)
);

page_footer();
