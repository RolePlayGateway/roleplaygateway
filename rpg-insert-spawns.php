<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/Parsedown.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$excludeLocations = array(51742);

/* item spawns */
$sql = 'select id, name, url from rpg_places where roleplay_id = 1 and (name like "%Volcano%" or name like "%Fire%" or name like "%Mountain%")';
$result = $db->sql_query($sql);
while ($location = $db->sql_fetchrow($result)) {
  $db->sql_query('INSERT IGNORE INTO rpg_item_spawns (item_id, location_id) VALUES (212415, '.(int) $location['id'].')');
}
$db->sql_freeresult($result);

$sql = 'select id, name, url from rpg_places where roleplay_id = 1 and (name like "%Ocean%" or name like "%Sea%" or name like "%Beach%")';
$result = $db->sql_query($sql);
while ($location = $db->sql_fetchrow($result)) {
  $db->sql_query('INSERT IGNORE INTO rpg_item_spawns (item_id, location_id) VALUES (212416, '.(int) $location['id'].')');
}
$db->sql_freeresult($result);

$sql = 'select id, name, url from rpg_places where roleplay_id = 1 and (name like "%Cave%") AND id NOT IN (51742)';
$result = $db->sql_query($sql);
while ($location = $db->sql_fetchrow($result)) {
  $db->sql_query('INSERT IGNORE INTO rpg_item_spawns (item_id, location_id) VALUES (212417, '.(int) $location['id'].')');
}
$db->sql_freeresult($result);

$sql = 'select id, name, url from rpg_places where roleplay_id = 1 and (name like "%Ruin%" or name like "%Dungeon%")';
$result = $db->sql_query($sql);
while ($location = $db->sql_fetchrow($result)) {
  $db->sql_query('INSERT IGNORE INTO rpg_item_spawns (item_id, location_id) VALUES (212418, '.(int) $location['id'].')');
}
$db->sql_freeresult($result);

/* mob spawns */
$sql = 'select id, name, url from rpg_places where roleplay_id = 1 and (name like "%Beach%" or name like "%Shore%")';
$result = $db->sql_query($sql);
while ($location = $db->sql_fetchrow($result)) {
  $db->sql_query('INSERT IGNORE INTO rpg_mob_spawns (mob_id, location_id, mood) VALUES (101258, '.(int) $location['id'].', "aggressive")');
}
$db->sql_freeresult($result);

?>
