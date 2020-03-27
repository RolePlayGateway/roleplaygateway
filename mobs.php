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

$targetID = request_var('id', 0);
$mobName = null;

if (!empty($targetID)) {
  $sql = 'SELECT i.id, m.name, i.location_id, i.mood, m.roleplay_id FROM rpg_mob_instances i
    INNER JOIN rpg_mobs m
      ON m.id = i.mob_id
    WHERE i.id = '.(int) $targetID;

  $template->assign_vars(array(
    'S_IS_SINGLE' => true
  ));
} else {
  $sql = 'SELECT i.id, m.name, i.location_id, i.mood, m.roleplay_id FROM rpg_mob_instances i
    INNER JOIN rpg_mobs m
      ON m.id = i.mob_id';
}

$result = $db->sql_query($sql);
while ($mob = $db->sql_fetchrow($result)) {
  if (!empty($targetID)) {
    $mobName = $mob['name'];
  }

  $sql = 'SELECT id, title, url, owner FROM rpg_roleplays WHERE id = '.(int) $mob['roleplay_id'];
  $roleplayResult = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($roleplayResult);
  $db->sql_freeresult($roleplayResult);

  $mob['roleplay'] = $roleplay;

  $game_masters = array();
  $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
  $gmResult = $db->sql_query($sql);
  while ($gm_row = $db->sql_fetchrow($gmResult)) {
    $game_masters[] = $gm_row['user_id'];
  }
  $db->sql_freeresult($gmResult);

  $template->assign_block_vars('mobs', array(
    'ID' => $mob['id'],
    'NAME' => $mob['name'],
    'MOOD' => $mob['mood'],
    'DESCRIPTION' => $mob['description'],
    'ROLEPLAY_ID'         => $mob['roleplay']['id'],
    'ROLEPLAY_NAME'       => $mob['roleplay']['title'],
    'ROLEPLAY_URL'        => $mob['roleplay']['url'],
    'S_HAS_IMAGE' => (strlen($mob['image']) > 0) ? true : false
  ));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
  'S_PAGE_ONLY' => true
));

if (!empty($targetID)) {
  page_header($mobName. ' | Mobs | Roleplay on ' . $config['sitename']);
} else {
  page_header('Mobs | Roleplay on ' . $config['sitename']);
}

$template->set_filenames(array(
  'body' => 'rpg_mobs.html'
));

page_footer();

?>
