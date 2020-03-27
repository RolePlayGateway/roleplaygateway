<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once 'Image/GraphViz.php';
$gv = new Image_GraphViz(true, array(
  'rankdir' => 'BT'
), 'Map');

$gv->setAttributes(array(
  'rankdir' => 'TB',
  'strict' => true,
  'concentrate' => false,
  'mode'  => 'hier',
  //'model' => 'mds',
  //'nodesep' => 1,
  //'dim' => 1,
  //'pack'  => false,
  'overlap' => 'orthoyx',
  'decorate' => true,
//  'z' => 3,
  //'len' => 2,
  //'defaultdist' => 2,
  //'mindist' => 1,
  'esep' => 0.1,
  //'sep' => 10
));

@$roleplayID = (int) $_REQUEST['roleplayID'];
$roleplayURL = $_REQUEST['roleplay_url'];

if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$roleplayID = $roleplay['id'];

$sql = 'SELECT id, name, description, url, parent_id FROM rpg_places WHERE roleplay_id = '.(int) $roleplayID.' AND visibility = "Public" AND visible = 1';
$result = $db->sql_query($sql, 3600);
while ($origin = $db->sql_fetchrow($result)) {
  /* $sql = 'SELECT id, name, description, url FROM rpg_places WHERE id IN (SELECT destination_id FROM rpg_exits WHERE place_id = '.(int) $origin['id'].' AND direction = "ascend")';
  $groupResult = $db->sql_query($sql);
  $group = $db->sql_fetchrow($result);
  $db->sql_freeresult($groupResult); */

  $sql = 'SELECT id, name, parent_id FROM rpg_places WHERE id = '.(int) $origin['parent_id'].' AND roleplay_id = '.(int) $roleplayID . ' AND visibility = "Public" and visible = 1';
  $parentResult = $db->sql_query($sql, 3600);
  $parent = $db->sql_fetchrow($parentResult);
  $db->sql_freeresult($parentResult);

  $gv->addNode($origin['name'], array(
    'URL'      => 'https://www.roleplaygateway.com/universes/'.$roleplay['url'].'/places/'.$origin['url'].'',
    'fontsize' => '8',
    'fontname' => 'courier'
  )/*, $origin['parent_id']*/);

  if (!empty($parent['id'])) {
    /* $gv->addEdge(array(
      $origin['name'] => $parent['name']
    ),  array(
      'label' => 'ascend',
      'fontsize' => '6',
      'fontname' => 'courier',
      'color' => '#cccccc',
    )); */
    // $gv->addCluster($parent['id'], $parent['name'], array());
  }

  $sql = 'SELECT * FROM rpg_exits WHERE place_id = '.(int) $origin['id'].' AND direction NOT IN ("ascend", "descend")';
  $exitResult = $db->sql_query($sql, 3600);
  while ($exit = $db->sql_fetchrow($exitResult)) {
    $sql = 'SELECT id, name, parent_id FROM rpg_places WHERE id = '.(int) $exit['destination_id'].' AND roleplay_id = '.(int) $roleplayID . ' AND visibility = "Public" and visible = 1';
    $destinationResult = $db->sql_query($sql, 3600);
    $destination = $db->sql_fetchrow($destinationResult);
    $db->sql_freeresult($destinationResult);

    if (!empty($origin['name']) && !empty($destination['name'])) {
      $gv->addEdge(array(
        $origin['name'] => $destination['name']
      ),  array(
        'label' => $exit['direction'],
        'fontsize' => '6',
        'fontname' => 'courier',
        'color' => '#cccccc',
      ));
    }
  }
  $db->sql_freeresult($exitResult);
}
$db->sql_freeresult($result);

//$gv->setDirected(false);

//$gv->image('svg', 'neato');
$gv->image('svg', 'dot');
//$gv->image();

function getPlace($placeID) {
  global $db,$cache;

  $sql = 'SELECT id, name, description, parent_id FROM rpg_places WHERE id = '.(int) $placeID;
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  return $row;

}

function returningDirection($direction) {
  switch ($direction) {
    case 'north':
      $return = 'south';
    break;
    case 'south':
      $return = 'north';
    break;
    case 'east':
      $return = 'west';
    break;
    case 'west':
      $return = 'east';
    break;
    case 'northeast':
      $return = 'southwest';
    break;
    case 'southeast':
      $return = 'northwest';
    break;
    case 'southwest':
      $return = 'northeast';
    break;
    case 'northwest':
      $return = 'southeast';
    break;
    case 'up':
      $return = 'down';
    break;
    case 'down':
      $return = 'up';
    break;
    case 'ascend':
      $return = 'descend';
    break;
    case 'descend':
      $return = 'ascend';
    break;
    case 'in':
      $return = 'out';
    break;
    case 'out':
      $return = 'in';
    break;
  }
  return $return;
}

?>
