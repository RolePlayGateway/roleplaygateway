<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

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

if (!$user->data['is_registered']) {
  trigger_error('Forbidden');
}

$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$roleplay['link'] = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/';

$sql = 'SELECT id FROM rpg_content WHERE id = '.(int) $_REQUEST['post_id']. ' AND roleplay_id = '.(int) $roleplay['id'];
$result = $db->sql_query($sql);
$post = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$post['link'] = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/post/'.$post['id'].'/#roleplay'.$post['id'];

header('Content-Type: application/json');
if (isset($_POST['characters']) && isset($post['id'])) {

  foreach ($_POST['characters'] as $characterID) {
    $characterIDs[] = (int) $characterID;
  }

  $sql = 'SELECT id, name, url, owner FROM rpg_characters WHERE id IN ('.implode(',', $characterIDs).') AND roleplay_id = '.(int) $roleplay['id'];
  $characterResult = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($characterResult)) {
    $characters[$character['id']] = $character;
  }
  $db->sql_freeresult($characterResult);

  foreach ($characters as $character) {
    $sql = 'INSERT IGNORE INTO rpg_content_tags (character_id, content_id, tagger_id) VALUES ('.(int) $character['id'].' , '.(int) $post['id'].', '.(int) $user->data['user_id'].')';
    $db->sql_query($sql);

    $sql = "INSERT INTO arrowchat_notifications (to_id, author_id, author_name, type, alert_time, data)
      VALUES ('".$character['owner']."', '".$user->data['user_id']."', '".$user->data['username']."', '14', '".time()."',
        \"".$db->sql_escape(json_encode(array(
          'roleplayName'  => $roleplay['title'],
          'roleplayURL'   => $roleplay['url'],
          'placeName'     => $roleplay['place_name'],
          'placeURL'      => $roleplay['place_url'],
          'characterName' => $character['name'],
          'characterURL'  => $character['url'],
          'postID'        => $contentID,
        )))."\"
        )";
    $db->sql_query($sql);
    $notificationID = $db->sql_nextid();
    
    notify_user_maybe($character['owner'], $notificationID);

    $messageData = array(
      'character' => $character,
      'content'   => $post,
      'roleplay'  => $roleplay,
      'user'      => array(
        'username'  => $user->data['username'],
        'id'  => $user->data['user_id']
      ),
    );

    /*$redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $redis->publish('roleplay.'.$roleplay , json_encode(array(
      'type' => 'tag-character',
      'data' => $messageData
    ), JSON_FORCE_OBJECT));
    $redis->close();*/
  }

  die(json_encode(array(
    'status' => 'success',
    'message' => 'Successfully tagged those characters!',
    'characters' => $characters
  )));

} else {

  die(json_encode(array(
    'status' => 'error',
    'message' => 'No characters submitted!'
  )));

}

?>
