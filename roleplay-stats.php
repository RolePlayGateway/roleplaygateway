<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$places = array();

$sql = 'SELECT id,name,url,parent_id,owner,length(synopsis) as synopsis,length(description) as description,length(image) as image FROM rpg_places WHERE roleplay_id = 1';
$result = $db->sql_query($sql);
while ($place = $db->sql_fetchrow($result)) {
  $places[$place['id']] = $place;
  $places[$place['id']]['posts'] = 0;
  $places[$place['id']]['parent'] = $place['parent_id'];
}

$sql = 'SELECT place_id,written,author_id FROM rpg_content WHERE roleplay_id = 1';
$result = $db->sql_query($sql);
while ($content = $db->sql_fetchrow($result)) {
  if (empty($places[$content['place_id']])) {
    $places[$content['place_id']] = array(
      'posts' => 1
    );
  } else {
    $places[$content['place_id']]['posts']++;
  }
}

function sortByPostCount($a, $b) {
  if ($a['posts'] == $b['posts']) {
    return 0;
  }
  return ($a['posts'] < $b['posts']) ? 1 : -1;
}

uasort($places, 'sortByPostCount');

foreach ($places as $id => $place) {
  $sql = 'SELECT username,user_id,FROM_UNIXTIME(user_lastvisit) as user_lastvisit FROM gateway_users WHERE user_id = '.(int) $place['owner'];
  $result = $db->sql_query($sql);
  while ($user = $db->sql_fetchrow($result)) {
    $places[$id]['owner'] = $user['username'];
    $places[$id]['owner-id'] = $user['user_id'];
    $places[$id]['owner-link'] = 'https://www.roleplaygateway.com/member/'.$user['username'];
    $places[$id]['owner-lastvisit'] = $user['user_lastvisit'];

    $sql = 'SELECT count(id) as writingCount FROM rpg_content WHERE author_id = '.(int) $user['user_id'];
    $otherResult = $db->sql_query($sql);
    while ($posts = $db->sql_fetchrow($otherResult)) {
      $places[$id]['owner-posts'] = $posts['writingCount'];
    }
  }

}

header('Content-Type: text/plain');
echo "id,name,link,posts,inside of,image size,synopsis,description,owner id,owner,last online,recent posts,owner link,minimap\n";
foreach ($places as $place) {
  echo $place['id'].',';
  echo '"'.$place['name'].'",';
  echo 'https://www.roleplaygateway.com/roleplay/the-multiverse/places/'.$place['url'].',';
  echo $place['posts'].',';
  echo $place['parent'].',';
  echo $place['image'].',';
  echo $place['synopsis'].',';
  echo $place['description'].',';
  echo $place['owner-id'].',';
  echo $place['owner'].',';
  echo $place['owner-lastvisit'].',';
  echo $place['owner-posts'].',';
  echo $place['owner-link'].',';
  echo 'https://www.roleplaygateway.com/roleplay/the-multiverse/places/'.$place['url'].'/map,';
  echo "\n";
}

?>
