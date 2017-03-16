<?php 
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_medals.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$sql = 'SELECT id, user_id, year, month, day, time FROM donations WHERE user_id > 0';
$result = $db->sql_query($sql);
while ($donation = $db->sql_fetchrow($result)) {
  $medalDonationIDs = array();
  
  $sql = 'SELECT id, time, data FROM gateway_medals_awarded
    WHERE user_id ='.(int) $donation['user_id'] .'
  ';
  $donationResult = $db->sql_query($sql);
  while ($medal = $db->sql_fetchrow($donationResult)) {
    if ($donation['time'] == $medal['time']) {
      $data = array('donation' => (int) $donation['id']);
      $medal['data'] = $data;
    } else {
      $medal['data'] = json_decode($medal['data']);
    }
    
    $medalDonationIDs[] = $medal['data']['donation'];
  }
  
  if (!in_array($donation['id'], $medalDonationIDs)) {
    echo "<br />New medal to award: " . $donation['user_id'] ;
    $message = 'Retroactively awarded for a previous donation!';
    award_medal_from(0, 3, $donation['user_id'], $message, $donation['time']);
  }
  
}

$medalID = 1;
$message = 'Awarded for being a member of RPG Staff.  Thank you for helping us build the community!';
$sql = 'SELECT DISTINCT a.user_id as id
	FROM gateway_user_group a
	INNER JOIN gateway_groups b ON a.group_id=b.group_id
	INNER JOIN gateway_users c ON c.user_id=a.user_id
	WHERE a.group_id IN (2625,2629,2635) OR c.user_id = 4';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  $awardMedalIDs = array();
  $sql = 'SELECT id, medal_id, time, data FROM gateway_medals_awarded
    WHERE user_id ='.(int) $user['id'];
  $awardResult = $db->sql_query($sql);
  while ($award = $db->sql_fetchrow($awardResult)) {
    $awardMedalIDs[] = (int) $award['medal_id'];
  }
  
  if (!in_array($medalID, $awardMedalIDs)) {
    echo "<br />New medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

}

$medalID = 6;
$message = 'Awarded for being a part of the GWC Veterans group, signifying your participation in the GWC community. Thanks for inspiring the creation of RPG!';
$sql = 'SELECT DISTINCT a.user_id as id
	FROM gateway_user_group a
	INNER JOIN gateway_groups b ON a.group_id=b.group_id
	INNER JOIN gateway_users c ON c.user_id=a.user_id
	WHERE a.group_id IN (8)';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  $awardMedalIDs = array();
  $sql = 'SELECT id, medal_id, time, data FROM gateway_medals_awarded
    WHERE user_id ='.(int) $user['id'];
  $awardResult = $db->sql_query($sql);
  while ($award = $db->sql_fetchrow($awardResult)) {
    $awardMedalIDs[] = (int) $award['medal_id'];
  }
  
  if (!in_array($medalID, $awardMedalIDs)) {
    echo "<br />New medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

}

$medalID = 7;
$message = 'You created a universe!';
$sql = 'SELECT DISTINCT user_id FROM gateway_medals_awarded WHERE medal_id = 7';
$awardsResult = $db->sql_query($sql);
while ($award = $db->sql_fetchrow($awardsResult)) {
  $awards[] = $award['user_id'];
  $sql = 'SELECT DISTINCT owner FROM rpg_roleplays WHERE owner NOT IN ('.implode(',', $awards).')';
  $result = $db->sql_query($sql);
  while ($roleplay = $db->sql_fetchrow($result)) {
    award_medal_from(0, $medalID, $roleplay['owner'], $message, time());
  }
}

$medalID = 8;
$message = 'You\'ve responded to 10 different introductions in the Welcome Forum!';
$sql = 'SELECT count(post_id) as total, poster_id as id FROM gateway_posts WHERE forum_id = 98 AND total >= 10 GROUP BY poster_id';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  $awardMedalIDs = array();
  $sql = 'SELECT id, medal_id, time, data FROM gateway_medals_awarded
    WHERE user_id ='.(int) $user['id'];
  $awardResult = $db->sql_query($sql);
  while ($award = $db->sql_fetchrow($awardResult)) {
    $awardMedalIDs[] = (int) $award['medal_id'];
  }
  
  if (!in_array($medalID, $awardMedalIDs) && $user['total'] >= 10) {
    echo "<br />New medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

}

$medalID = 9;
$message = 'You created your first discussion topic in the forum!';
$sql = 'SELECT DISTINCT poster_id as id FROM gateway_topics WHERE roleplay_id IS NULL';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  $awardMedalIDs = array();
  $sql = 'SELECT id, medal_id, time, data FROM gateway_medals_awarded
    WHERE user_id ='.(int) $user['id'];
  $awardResult = $db->sql_query($sql);
  while ($award = $db->sql_fetchrow($awardResult)) {
    $awardMedalIDs[] = (int) $award['medal_id'];
  }
  
  if (!in_array($medalID, $awardMedalIDs)) {
    echo "<br />New medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

}

$medalID = 10;
$message = 'You wrote your first piece of content in an RPG universe!';
$sql = 'SELECT DISTINCT author_id as id FROM rpg_content';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  $awardMedalIDs = array();
  $sql = 'SELECT id, medal_id, time, data FROM gateway_medals_awarded
    WHERE user_id ='.(int) $user['id'];
  $awardResult = $db->sql_query($sql);
  while ($award = $db->sql_fetchrow($awardResult)) {
    $awardMedalIDs[] = (int) $award['medal_id'];
  }
  
  if (!in_array($medalID, $awardMedalIDs)) {
    echo "<br />New medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

}

$medalID = 11;
$message = 'Another user created a post in an RPG universe you created!';
$sql = 'SELECT id, owner FROM rpg_roleplays WHERE roleplay_id > 1';
$roleplayResult = $db->sql_query($sql);
while ($roleplay = $db->sql_fetchrow($roleplayResult)) {
  $sql = 'SELECT DISTINCT author_id as id FROM rpg_content WHERE roleplay_id = '. (int) $roleplay['id'] . ' AND author_id <> '. (int) $roleplay['owner'];
  $result = $db->sql_query($sql);
  while ($user = $db->sql_fetchrow($result)) {
    $awardMedalIDs = array();
    $sql = 'SELECT id, medal_id, time, data FROM gateway_medals_awarded
      WHERE user_id ='.(int) $user['id'];
    $awardResult = $db->sql_query($sql);
    while ($award = $db->sql_fetchrow($awardResult)) {
      $awardMedalIDs[] = (int) $award['medal_id'];
    }
    
    if (!in_array($medalID, $awardMedalIDs)) {
      echo "<br />New medal to award: " . $medalID . ' to '. $user['id'];
      award_medal_from(0, $medalID, $user['id'], $message, time());
    }
  }
}


$medalID = 12;
$message = 'You created your first non-default location in an RPG universe!';
$sql = 'SELECT DISTINCT owner as id, count(id) as total FROM rpg_places GROUP BY owner';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  $awardMedalIDs = array();
  $sql = 'SELECT id, medal_id, time, data FROM gateway_medals_awarded
    WHERE user_id ='.(int) $user['id'];
  $awardResult = $db->sql_query($sql);
  while ($award = $db->sql_fetchrow($awardResult)) {
    $awardMedalIDs[] = (int) $award['medal_id'];
  }
  
  if (!in_array($medalID, $awardMedalIDs) && $user['total'] > 1) {
    echo "<br />New medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

}

echo "<br />All done!";

?>