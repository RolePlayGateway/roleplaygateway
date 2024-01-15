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

$sql = 'SELECT * FROM rpg_mob_spawns';
$result = $db->sql_query($sql);
while ($spawn = $db->sql_fetchrow($result)) {
  $sql = 'SELECT id, name, quantity FROM rpg_mobs WHERE id = '.(int) $spawn['mob_id'];
  $mobResult = $db->sql_query($sql);
  $mob = $db->sql_fetchrow($mobResult);
  $db->sql_freeresult($mobResult);
  
  $sql = 'SELECT count(id) as total FROM rpg_mob_instances WHERE mob_id = '.(int) $mob['id'].' AND location_id = '.(int) $spawn['location_id'];
  $statsResult = $db->sql_query($sql);
  $stats = $db->sql_fetchrow($statsResult);
  $db->sql_freeresult($statsResult);
  
  $sql = 'SELECT count(id) as total FROM rpg_mob_instances WHERE mob_id = '.(int) $mob['id'];
  $overallStatsResult = $db->sql_query($sql);
  $limits = $db->sql_fetchrow($overallStatsResult);
  $db->sql_freeresult($overallStatsResult);
  
  $sql = 'SELECT roleplay_id FROM rpg_places WHERE id = '.(int) $spawn['location_id'];
  $placeResult = $db->sql_query($sql);
  $place = $db->sql_fetchrow($placeResult);
  $db->sql_freeresult($placeResult);
  
  if (empty($stats['total']) && $limits['total'] < $mob['quantity']) {
    echo "\nspawning #".$spawn['mob_id'];

    $sql = 'INSERT INTO rpg_mob_instances (mob_id, location_id, mood) VALUES ('.(int) $spawn['mob_id'].', '.(int) $spawn['location_id'].', "'.$spawn['mood'].'")';
    $db->sql_query($sql);

    $mobID = $db->sql_nextid();
    $message = 'A wild [url=https://www.roleplaygateway.com/mobs/'.$mobID.']'.$mob['name'].'[/url] appears!';

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
