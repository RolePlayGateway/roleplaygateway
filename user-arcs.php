<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

//ini_set('display_errors', true);
//error_reporting(E_ALL);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$roleplayID = (int) $_REQUEST['roleplayID'];
$roleplayURL = $_REQUEST['roleplayURL'];


if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
if ($roleplay = $db->sql_fetchrow($result)) {

  $sql = 'SELECT id, name, description, slug, creator FROM rpg_arcs WHERE roleplay_id = '.(int) $roleplay['id'] . ' AND creator = '.(int) $user->data['user_id'];
  $arcResult = $db->sql_query($sql);
  while ($arc = $db->sql_fetchrow($arcResult)) {
  
    $arcs[ $arc['id'] ] = $arc;

  }
  $db->sql_freeresult($arcResult);

  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'success',
    'arcs' => $arcs,
  ));
}
$db->sql_freeresult($result);


?>
