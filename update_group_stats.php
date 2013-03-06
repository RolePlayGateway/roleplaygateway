<?php
$phpbb_root_path = '/var/www/html/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
// last 30 days
$threshold = time() - (86400);

$sql = 'SELECT id, roleplay_id, name FROM rpg_groups';

$result = $db->sql_query($sql);
while ($group = $db->sql_fetchrow($result))
{

  echo "\nWorking on group #".$group['id']. ' ('.$group['name'].') in roleplay #'.$group['roleplay_id'].'...';

  $sql = 'SELECT * FROM rpg_group_members WHERE group_id = '.(int) $group['id'];
  $thisResult = $db->sql_query($sql);
  while ($member = $db->sql_fetchrow($thisResult)) {
  
    if (!empty($member['character_id'])) {
      $group['characters'][$member['character_id']] = $member;
      $group['characterIDs'][] = $member['character_id'];
      
      $sql = 'SELECT owner FROM rpg_characters WHERE id = '.(int) $member['character_id'];
      $myResult = $db->sql_query($sql);
      while ($player = $db->sql_fetchrow($myResult)) {
        $group['players'][$player['owner']] = $player;
      }
      $db->sql_freeresult($myResult);
    }
    
  }
  $db->sql_freeresult($thisResult);
  
  if (!empty($group['characterIDs'])) {
    $sql = 'SELECT id FROM rpg_places WHERE sovereignty IN ('.implode(',', $group['characterIDs']).')';
    $thisResult = $db->sql_query($sql);
    while ($place = $db->sql_fetchrow($thisResult)) {
       $group['places'][$place['id']] = $place;  
    }
    $db->sql_freeresult($thisResult);
  }
  
  $sql = 'INSERT INTO rpg_group_stats ( group_id, characters, players, places ) VALUES
            ( '.(int) $group['id'].', '.(int) count($group['characters']).', '.(int) count($group['players']).', '.(int) count($group['places']).' ) ON DUPLICATE KEY UPDATE
              characters = '.(int) count($group['characters']).',
              players = '.(int) count($group['players']).',
              places = '.(int) count($group['places']).'';
  $db->sql_query($sql);
  
}
$db->sql_freeresult($result);

?>
