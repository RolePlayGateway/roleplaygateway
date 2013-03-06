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

$sql = 'SELECT * FROM gateway_posts WHERE topic_id = 17468';
$result = $db->sql_query($sql);
while ($post = $db->sql_fetchrow($result)) {

  $sql = 'SELECT * FROM gateway_users WHERE user_id = '.(int) $post['poster_id'];
  $userResult = $db->sql_query($sql);
  $user = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $data[$post['post_id']] = array(
    'post_id' => $post['post_id'],
    'ip'  => $post['poster_ip']
  );

  $ips[$post['poster_ip']]++;
  //$ips[$user['user_ip']]++;

  $firstThree[ substr( $post['poster_ip'], 0, strrpos( $post['poster_ip'], '.' ) ) ]++;

}
$db->sql_freeresult($result);

//foreach ($firstThree as $region => $count) {
foreach ($ips as $region => $count) {
  if (!empty($region)) {
    $sql = 'SELECT count(*) as posts FROM gateway_posts WHERE poster_ip LIKE "'.$region.'.%" AND topic_id <> 17468';
    $result = $db->sql_query($sql);
    $totals = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $misses[$region] = $totals['posts'];

    $sql = 'SELECT post_id FROM gateway_posts WHERE poster_ip LIKE "'.$region.'.%" AND topic_id <> 17468';
    $result = $db->sql_query($sql);
    while ($missedPost = $db->sql_fetchrow($result)) {
      $missedPosts[] = 'http://www.roleplaygateway.com/viewtopic.php?p='.$missedPost['post_id'];
    }
    $db->sql_freeresult($result);
  }

}

uasort($ips, function($a, $b) {
  return $b - $a;
});

uasort($firstThree, function($a, $b) {
  return $b - $a;
});

uasort($misses, function($a, $b) {
  return $b - $a;
});


echo json_encode(array(
  'topRegions' => $firstThree,
  'spammers' => $ips,
  'missedPosts' => $missedPosts,
  'misses' => $misses,
  'data' => $data
));


?>