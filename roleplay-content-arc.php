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

$arcID = (int) $_REQUEST['arcID'];
$postID = (int) $_REQUEST['postID'];


if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
if ($roleplay = $db->sql_fetchrow($result)) {

  $sql = 'SELECT id, name, description, slug, creator FROM rpg_arcs WHERE roleplay_id = '.(int) $roleplay['id'] . '
    AND id = '.(int) $arcID;
  $arcResult = $db->sql_query($sql);
  $arc = $db->sql_fetchrow($arcResult);
  $db->sql_freeresult($arcResult);

  if (empty($arc)) {
    die('No such arc.');
  }

  $sql = 'SELECT id FROM rpg_content WHERE roleplay_id = '.(int) $roleplay['id'] . '
    AND id = '.(int) $postID;
  $postResult = $db->sql_query($sql);
  $post = $db->sql_fetchrow($postResult);
  $db->sql_freeresult($postResult);

  if (empty($post)) {
    die('No such post.');
  }

  $sql = 'INSERT IGNORE INTO rpg_arc_content (arc_id, content_id) VALUES ('.(int) $arc['id'].','.(int) $post['id'].')';
  $db->sql_query($sql);

  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'success'
  ));
}
$db->sql_freeresult($result);


?>
