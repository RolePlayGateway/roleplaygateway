<?php 
ini_set('display_errors', true);

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

/*
$medalID = 3;
$message = 'Retroactively awarded for a previous donation!';
$sql = 'SELECT user_id as id, user_id, year, month, day, time FROM donations WHERE user_id > 0';
$result = $db->sql_query($sql);
while ($donation = $db->sql_fetchrow($result)) {
  echo "\nNew medal to award: " . $medalID . ' to ' . $donation['id'] ;
  award_medal_from(0, $medalID, $donation['id'], $message, $donation['time']);
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
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
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
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

} */

/*$medalID = 7;
$message = 'You created a universe!';
$sql = 'SELECT DISTINCT user_id FROM gateway_medals_awarded WHERE medal_id = 7';
$awardsResult = $db->sql_query($sql);
while ($award = $db->sql_fetchrow($awardsResult)) {
  $awards[] = $award['user_id'];
  $sql = 'SELECT DISTINCT owner FROM rpg_roleplays WHERE owner NOT IN ('.implode(',', $awards).')';
  $result = $db->sql_query($sql);
  while ($roleplay = $db->sql_fetchrow($result)) {
    if (!in_array($roleplay['owner'], $awards)) {
      award_medal_from(0, $medalID, $roleplay['owner'], $message, time());
    }
  }
}*/

/*
$medalID = 8;
$message = 'You\'ve responded to 10 different introductions in the Welcome Forum!';
$sql = 'SELECT SUM(1) as total, poster_id as id FROM gateway_posts WHERE forum_id = 95 GROUP BY topic_id, poster_id';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  if ($user['total'] >= 10) {
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }
}
*/

/*
$medalID = 9;
$message = 'You created your first discussion topic in the forum!';
$awards = array();
$sql = 'SELECT id, user_id, time, data FROM gateway_medals_awarded
  WHERE medal_id ='.(int) $medalID;
$awardResult = $db->sql_query($sql);
while ($award = $db->sql_fetchrow($awardResult)) {
  $awards[] = $award['user_id'];
}
$sql = 'SELECT DISTINCT topic_poster as id FROM gateway_topics WHERE roleplay_id IS NULL AND topic_poster NOT IN ('.implode(',', $awards).')';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
  award_medal_from(0, $medalID, $user['id'], $message, time());
}

$medalID = 10;
$message = 'You wrote your first piece of content in an RPG universe!';
$awards = array();
$sql = 'SELECT id, user_id, time, data FROM gateway_medals_awarded
  WHERE medal_id ='.(int) $medalID;
$awardResult = $db->sql_query($sql);
while ($award = $db->sql_fetchrow($awardResult)) {
  $awards[] = $award['user_id'];
}
$sql = 'SELECT DISTINCT author_id as id FROM rpg_content WHERE author_id NOT IN ('.implode(',', $awards).') AND author_id > 0';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
  award_medal_from(0, $medalID, $user['id'], $message, time());
}

$medalID = 11;
$message = 'Another user created a post in an RPG universe you created!';
$awards = array();
$sql = 'SELECT id, owner FROM rpg_roleplays';
$roleplayResult = $db->sql_query($sql);
while ($roleplay = $db->sql_fetchrow($roleplayResult)) {
  $sql = 'SELECT DISTINCT author_id as id FROM rpg_content WHERE roleplay_id = '. (int) $roleplay['id'] . ' AND author_id <> '. (int) $roleplay['owner'] .' AND author_id > 0 GROUP BY roleplay_id';
  $result = $db->sql_query($sql);
  $content = $db->sql_fetchrow($result);
  
  if (!empty($content['id'])) {
    echo "\nNew medal to award: " . $medalID . ' to '. $roleplay['owner'];
    award_medal_from(0, $medalID, $roleplay['owner'], $message, time());
  }
}

$medalID = 12;
$message = 'You created your first non-default location in an RPG universe!';
$sql = 'SELECT DISTINCT owner as id, count(id) as total FROM rpg_places WHERE parent_id > 0 GROUP BY owner';
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
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }

}

$medalID = 14;
$message = 'You participated in 10 different discussions on the forum!';
$sql = 'SELECT count(post_id) as total, poster_id as id FROM gateway_posts GROUP BY poster_id';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  if ($user['total'] >= 10) {
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }
}

$medalID = 15;
$message = 'You introduced yourself in the Welcome forum!';
$sql = 'SELECT count(topic_id) as total, topic_poster as id FROM gateway_topics WHERE forum_id = 95 GROUP BY topic_poster';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  if ($user['total'] >= 1) {
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }
}

$medalID = 17;
$message = 'You have written over 80,000 words!';
$sql = 'SELECT *, user_id as id FROM gateway_user_stats WHERE total_words >= 80000';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  if ($user['total_words'] >= 80000) {
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }
} */

/*$medalID = 18;
$message = 'You wrote over 1,000,000 words!  Holy cow!';
$sql = 'SELECT *, user_id as id FROM gateway_user_stats WHERE total_words >= 1000000';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  if ($user['total_words'] >= 1000000) {
    echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
    award_medal_from(0, $medalID, $user['id'], $message, time());
  }
}*/

/*
$medalID = 20;
$message = 'You created a storyline arc in an existing universe!';
$sql = 'SELECT DISTINCT creator as id FROM rpg_arcs';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
  award_medal_from(0, $medalID, $user['id'], $message, time());
}
*/

/*
$medalID = 22;
//$message = '';
$sql = 'SELECT DISTINCT author_id as id FROM rpg_content WHERE roleplay_id IN (SELECT r.id FROM rpg_roleplays r
  INNER JOIN rpg_roleplay_stats s
    ON r.id = s.roleplay_id
  WHERE r.status = "Completed")';
$statsResult = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($statsResult)) {
  echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
  award_medal_from(0, 22, $user['id'], 'You wrote at least one post in a universe which was previously marked as complete!', time());
}
$db->sql_freeresult($statsResult);*/


$medalID = 23;
$message = 'You previously completed a quest!';
$sql = 'SELECT DISTINCT owner_id as id FROM rpg_quest_characters WHERE verified IS NOT NULL AND owner_id > 0';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
  award_medal_from(0, $medalID, $user['id'], $message, time());
}

$medalID = 24;
$message = 'You previously created a quest!';
$sql = 'SELECT DISTINCT creator as id FROM rpg_quests';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {
  echo "\nNew medal to award: " . $medalID . ' to '. $user['id'];
  award_medal_from(0, $medalID, $user['id'], $message, time());
}


echo "\nAll done!";

?>