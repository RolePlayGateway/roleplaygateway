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

$sql = 'SELECT * FROM rpg_item_spawns';
$result = $db->sql_query($sql);
while ($spawn = $db->sql_fetchrow($result)) {
  $sql = 'SELECT id, name, quantity FROM rpg_items WHERE id = '.(int) $spawn['item_id'];
  $itemResult = $db->sql_query($sql);
  $item = $db->sql_fetchrow($itemResult);
  $db->sql_freeresult($itemResult);
  
  $sql = 'SELECT count(id) as total FROM rpg_item_instances WHERE item_id = '.(int) $item['id'].' AND location_id = '.(int) $spawn['location_id'];
  $statsResult = $db->sql_query($sql);
  $stats = $db->sql_fetchrow($statsResult);
  $db->sql_freeresult($statsResult);
  
  $sql = 'SELECT count(id) as total FROM rpg_item_instances WHERE item_id = '.(int) $item['id'];
  $overallStatsResult = $db->sql_query($sql);
  $limits = $db->sql_fetchrow($overallStatsResult);
  $db->sql_freeresult($overallStatsResult);
  
  $sql = 'SELECT roleplay_id FROM rpg_places WHERE id = '.(int) $spawn['location_id'];
  $placeResult = $db->sql_query($sql);
  $place = $db->sql_fetchrow($placeResult);
  $db->sql_freeresult($placeResult);
  
  if (empty($stats['total']) && $limits['total'] < $item['quantity']) {
    echo "\nspawning #".$spawn['item_id'];
    
    $message = 'You notice [url=/items/'.$spawn['item_id'].']'.$item['name'].'[/url].  It seems to belong here.';

    $sql = 'INSERT INTO rpg_item_instances (item_id, location_id) VALUES ('.(int) $spawn['item_id'].', '.(int) $spawn['location_id'].')';
    $db->sql_query($sql);
    
    $sql = "INSERT INTO ajax_chat_messages
          (`userId`,`userRole`,`ip`,`dateTime`,`userName`,`channel`,`text`,`roleplayID`)
        VALUES (
          '2147483647',
          '4',
          '127.0.0.1',
          NOW(),
          'Game Master (GM)',
          ".(int) $spawn['location_id'].",
          '".$db->sql_escape($message)."',
          ".(int) $place['roleplay_id']."
        )";
    $db->sql_query($sql);
  }
  
  echo json_encode($stats);
}
$db->sql_freeresult($result);

?>
