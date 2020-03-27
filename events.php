<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$sql = 'SELECT * FROM rpg_events';
$result = $db->sql_query($sql);
while ($event = $db->sql_fetchrow($result)) {
  
  $sql = 'SELECT id, title, url FROM rpg_roleplays WHERE id = '.(int) $event['roleplay_id'];
  $roleplayResult = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($roleplayResult);
  $db->sql_freeresult($roleplayResult);
  
  $event['roleplay'] = $roleplay;
  
  $template->assign_block_vars('events', array(
    'ID' => $event['id'],
    'TITLE' => $event['title'],
    'START_TIME' => $event['start_time'],
    'END_TIME' => $event['end_time'],
    'ROLEPLAY_ID'         => $event['roleplay']['id'],
    'ROLEPLAY_NAME'       => $event['roleplay']['title'],
    'ROLEPLAY_URL'        => $event['roleplay']['url'],
  ));

}
$db->sql_freeresult($result);

$template->assign_vars(array(
  'S_PAGE_ONLY' => true
));

page_header();

$template->set_filenames(array(
  'body' => 'events.html'
));

page_footer();

?>
