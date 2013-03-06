<?php
$phpbb_root_path = '/var/www/html/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
/* include($phpbb_root_path . 'includes/functions_prs.' . $phpEx); */

// last 30 days
$threshold = time() - (86400);

$sql = 'SELECT user_id,username,user_posts
	FROM ' . USERS_TABLE . ' WHERE user_id > 0';

$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result))
{
	$this_id = $row['user_id'];

	$user_list[$this_id]['id'] = $row['user_id'];
	$user_list[$this_id]['username'] = $row['username'];
	$user_list[$this_id]['posts'] = $row['user_posts'];
}
$db->sql_freeresult($result);

foreach ($user_list as $account) {
	
	echo "\nWorking on member ID #".$account['id']."...";

  $sql = 'SELECT count(*) as total_content FROM rpg_content_stats WHERE poster_id = '.(int) $account['id'];
  $result = $db->sql_query($sql);
  $account['total_content'] = $db->sql_fetchfield('total_content');
  $db->sql_fetchfield($result);
	

  $sql = 'SELECT count(DISTINCT roleplay_id) as total_roleplays FROM rpg_content_stats WHERE poster_id = '.(int) $account['id'];
  $result = $db->sql_query($sql);
  $account['total_roleplays'] = $db->sql_fetchfield('total_roleplays');
  $db->sql_fetchfield($result);

	$sql = "SELECT character_count,word_count,flesch_kincaid_grade,prs_rating FROM rpg_content_stats WHERE poster_id = ".$account['id'];
	if ($user_post_stats = $db->sql_query($sql)) {
	
		$account['total_characters'] 	= 0;
		$account['total_words']			= 0;
		$account['total_grade_level']	= 0;
		$account['total_prs_rating'] 	= 0;
		$account['rated_posts'] 		= 0;
		
		while ($post = $db->sql_fetchrow($user_post_stats)) {
			$account['total_characters']	= @$account['total_characters'] 	+ $post['character_count'];
			$account['total_words']			= @$account['total_words']			+ $post['word_count'];
			$account['total_grade_level']	= @$account['total_grade_level'] 	+ $post['flesch_kincaid_grade'];
		}	

		$rated_sql = "SELECT prs_rating FROM gateway_post_stats WHERE poster_id = ".$account['id']." AND prs_rating <> 0 AND votes > 0";
		
		if ($user_rating_stats = $db->sql_query($rated_sql)) {
			while ($rated_posts = $db->sql_fetchrow($user_rating_stats)) {
				$account['total_prs_rating'] = @$account['total_prs_rating']	+ $rated_posts['prs_rating'];
				$account['rated_posts']++;
			}			
		} else {
			echo "UBERFAIL";
		}
	} else {
		echo "SEMIFAIL";
	}
	$db->sql_freeresult($user_post_stats);


  $sql = 'SELECT count(content_id) as post_count FROM rpg_content_stats WHERE poster_id = '.(int) $account['id'].' GROUP BY roleplay_id';
  $result = $db->sql_query($sql);
  while ($row = $db->sql_fetchrow($result)) {
    $account['total_posts'] = @$account['total_posts'] + $row['post_count'];
  }
  $db->sql_freeresult($result);

  $sql = 'SELECT DATEDIFF(MAX(written), MIN(written)) as days FROM rpg_content WHERE author_id = '.(int) $account['id'].' GROUP BY roleplay_id;';
  $result = $db->sql_query($sql);
  while ($row = $db->sql_fetchrow($result)) {
    $account['total_days'] = @$account['total_days'] + $row['days'];
  }
  $db->sql_freeresult($result);


	if ($account['total_content'] > 0) {
		$account['average_characters'] 	= $account['total_characters'] 	/ $account['total_content'];
		$account['average_words'] 		= $account['total_words'] 		/ $account['total_content'];
		$account['average_posts'] 		= $account['total_content'] 		/ $account['total_roleplays'];
		$account['average_days'] 		= $account['total_days'] 		/ $account['total_roleplays'];
		$account['average_grade_level'] = $account['total_grade_level'] / $account['total_content'];
	
	} else {
		$account['average_characters'] 	= 0;
		$account['average_words'] 		= 0;	
		$account['average_grade_level'] = 0;
		$account['average_posts'] = 0;
		$account['average_days'] = 0;
		$account['average_prs_rating'] 	= 30;
		$account['rated_posts'] 		= 0;
	}
	
		if ($account['rated_posts'] > 0) {
			$account['average_prs_rating'] 	= $account['total_prs_rating'] 	/ $account['rated_posts'];
		} else {
			$account['average_prs_rating']  = 0;
		}	
	
	$account['average_characters'] 	= round($account['average_characters'],2);
	$account['average_words'] 		= round($account['average_words'],2);
	$account['average_posts'] 		= round($account['average_posts'],2);
	$account['average_days'] 		= round($account['average_days'],2);
	$account['average_grade_level'] = round($account['average_grade_level'],2);
	$account['average_prs_rating'] 	= round(($account['average_prs_rating'] / 10),2);

	//$karma = prs_karma($account['id'],true,true);
	//print_r($karma);

	$sql = "INSERT INTO `gateway_user_stats` (
			`user_id`,
			`stats_updated`,
			`posts`,
			`total_characters`,
			`total_words`,
			`total_roleplays`,
			`average_characters`,
			`average_words`,
			`average_time`,
			`average_grade_level`,
			`average_posts`,
			`prs_reputation`
		) VALUES (
			'".$account['id']."',
			'".time()."',
			'".$account['total_content']."',
			'".$account['total_characters']."',
			'".$account['total_words']."',
			'".$account['total_roleplays']."',
			'".$account['average_characters']."',
			'".$account['average_words']."',
			'".$account['average_days']."',
			'".$account['average_grade_level']."',
			'".$account['average_posts']."',
			'".$account['average_prs_rating']."'
		) ON DUPLICATE KEY UPDATE
			stats_updated=".time().",
			posts=".$account['total_content'].",
			total_characters=".$account['total_characters'].",
			total_words=".$account['total_words'].",
			total_roleplays=".$account['total_roleplays'].",
			average_characters=".$account['average_characters'].",
			average_words=".$account['average_words'].",
			average_time=".$account['average_days'].",
			average_grade_level=".$account['average_grade_level'].",
			average_posts=".$account['average_posts'].",
			prs_reputation=\"".$account['average_prs_rating']."\""
	;
	if (!$result = $db->sql_query($sql)) {
		echo "query is teh fail";
	} else {
		//$user_karma = prs_karma($account['id'],TRUE,TRUE);
	
		echo "...done, (".$account['total_prs_rating']." / ".$account['rated_posts']." = ".$account['average_prs_rating'].") versus karma: (".$user_karma['karma'].")";
	}
	$db->sql_freeresult($result);
	
	unset($account);

}

unset($user_list);

?>
