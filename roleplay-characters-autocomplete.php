<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

error_reporting(0);
ini_set('display_errors', true);

$roleplayURL = $_REQUEST['roleplayURL'];
$characterName = $_REQUEST['search'];

$result = $db->sql_query('SELECT id FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"');
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$sql = 'SELECT id, name, synopsis, url FROM rpg_characters WHERE roleplay_id = '.(int) $roleplay['id'].' AND name LIKE "'.$db->sql_escape($characterName).'%"
          ORDER BY id ASC
          LIMIT 10';
$result = $db->sql_query($sql);
while ($character = $db->sql_fetchrow($result)) {
  $characters[$character['id']] = $character;
}
$db->sql_freeresult($result);

header('Content-Type: application/json');
echo json_encode($characters);

?>

