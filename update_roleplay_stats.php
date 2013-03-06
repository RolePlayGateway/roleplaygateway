<?php
$phpbb_root_path = '/var/www/html/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

// last 30 days
$threshold = time() - (86400);

$sql = 'SELECT id FROM rpg_roleplays';

$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result))
{
	$this_id = $row['id'];

	$roleplay_list[$this_id]['id'] = $row['id'];
}
$db->sql_freeresult($result);

foreach ($roleplay_list as $roleplay) {
	
	echo "\nWorking on roleplay ID #".$roleplay['id']."...";
	
	$sql = "SELECT character_count,word_count,flesch_kincaid_grade,prs_rating FROM rpg_content_stats WHERE roleplay_id = ".$roleplay['id'];

	if ($roleplay_content_stats = $db->sql_query($sql)) {
	
		$roleplay['total_characters'] 	= 0;
		$roleplay['total_words']		    = 0;
		$roleplay['total_grade_level']	= 0;
		$roleplay['total_prs_rating'] 	= 0;
		$roleplay['rated_posts'] 		    = 0;
		
		while ($content = $db->sql_fetchrow($roleplay_content_stats)) {
			$roleplay['total_characters']		= @$roleplay['total_characters'] 	+ $content['character_count'];
			$roleplay['total_words']			= @$roleplay['total_words']			+ $content['word_count'];
			$roleplay['total_grade_level']		= @$roleplay['total_grade_level'] 	+ $content['flesch_kincaid_grade'];
		}	

	} else {
		echo "SEMIFAIL";
	}

	$db->sql_freeresult($roleplay_content_stats);
	
		
	$sql = "SELECT count(*) as posts FROM rpg_content WHERE roleplay_id = ".$roleplay['id'];
	$db->sql_query($sql);
	$roleplay['posts'] = $db->sql_fetchfield('posts');
	
	if ($roleplay['posts'] > 0) {
		$roleplay['average_characters'] 	= $roleplay['total_characters'] 	/ $roleplay['posts'];
		$roleplay['average_words'] 			= $roleplay['total_words'] 			/ $roleplay['posts'];
		$roleplay['average_grade_level'] 	= $roleplay['total_grade_level'] 	/ $roleplay['posts'];
		if ($roleplay['rated_posts'] > 0) {
			$roleplay['average_prs_rating'] = $roleplay['total_prs_rating'] 	/ $roleplay['rated_posts'];
		} else {
			$roleplay['average_prs_rating']  = 0;
		}
		
		$sql = 'SELECT DISTINCT author_id as user_id FROM rpg_content WHERE roleplay_id = '.(int) $roleplay['id'];
		$thisResult = $db->sql_query($sql);
		while ($author = $db->sql_fetchrow($thisResult)) {
		  if (!empty($author['user_id']) && ($author['user_id']) > 0) {
		
		    $sql = 'SELECT SUM(word_count) as words FROM rpg_content_stats WHERE poster_id = '.(int) $author['user_id'].' AND roleplay_id = '.(int) $roleplay['id'];
		    $wordResult = $db->sql_query($sql);
		    $roleplay['authors'][$author['user_id']] = $db->sql_fetchrow($wordResult);
		    $db->sql_freeresult($wordResult);
		    
		    
		    $sql = 'INSERT INTO rpg_roleplay_author_stats (roleplay_id, author_id, words) VALUES ('.(int) $roleplay['id'].', '.(int) $author['user_id'].', '.(int) $roleplay['authors'][$author['user_id']]['words'].') ON DUPLICATE KEY UPDATE 
		      words = '.(int) $roleplay['authors'][$author['user_id']]['words'];
		    $db->sql_query($sql);  
		  }
		}
		
		
		
	} else {
		$roleplay['average_characters'] 	= 0;
		$roleplay['average_words'] 			= 0;	
		$roleplay['average_grade_level'] 	= 0;
		$roleplay['average_prs_rating'] 	= 30;
		$roleplay['rated_posts'] 			= 0;
	}
	
	$roleplay['total_reviews'] = 0;
	$roleplay['unique_reviews'] = 0;	

	
	$sql = 'SELECT *,count(*) as total_reviews,count(author) as unique_reviews FROM rpg_reviews WHERE roleplay_id = '.$roleplay['id'] . ' GROUP BY author ORDER BY timestamp';
	$reviews_result = $db->sql_query($sql);
	
	
	while ($review = $db->sql_fetchrow($reviews_result)) {
	
		$roleplay['total_reviews'] = $review['total_reviews'];
		$roleplay['unique_reviews'] = $review['unique_reviews'];
	
		$roleplay['total_characterization']	= $roleplay['total_characterization'] + scoreRubric($review['characterization']);
		$roleplay['total_plot']	= $roleplay['total_plot'] + scoreRubric($review['plot']);
		$roleplay['total_depth']	= $roleplay['total_depth'] + scoreRubric($review['depth']);
		$roleplay['total_style']	= $roleplay['total_style'] + scoreRubric($review['style']);
		$roleplay['total_mechanics']	= $roleplay['total_mechanics'] + scoreRubric($review['mechanics']);
		$roleplay['total_overall']	= $roleplay['total_overall'] + scoreRubric($review['overall']);
	}
	$db->sql_freeresult($reviews_result);
	
	$roleplay['average_characterization'] = round($roleplay['total_characterization'] / $roleplay['unique_reviews'],2);
	$roleplay['average_plot'] = round($roleplay['total_plot'] / $roleplay['unique_reviews'],2);
	$roleplay['average_depth'] = round($roleplay['total_depth'] / $roleplay['unique_reviews'],2);
	$roleplay['average_style'] = round($roleplay['total_style'] / $roleplay['unique_reviews'],2);
	$roleplay['average_mechanics'] = round($roleplay['total_mechanics'] / $roleplay['unique_reviews'],2);
	$roleplay['average_overall'] = round($roleplay['total_overall'] / $roleplay['unique_reviews'],2);
	
	$roleplay['roleplay_rating'] = $roleplay['average_characterization'] + $roleplay['average_plot'] + $roleplay['average_depth'] + $roleplay['average_style'] + $roleplay['average_mechanics'] + $roleplay['average_overall'];
	
	
	$roleplay['average_characters'] 	= round($roleplay['average_characters'],2);
	$roleplay['average_words'] 			= round($roleplay['average_words'],2);
	$roleplay['average_grade_level'] 	= round($roleplay['average_grade_level'],2);
	$roleplay['average_prs_rating'] 	= round(($roleplay['average_prs_rating'] / 10),2);

	//$karma = prs_karma($account['id'],true,true);
	//print_r($karma);

	$sql = "INSERT INTO `rpg_roleplay_stats` (
			`roleplay_id`,
			`stats_updated`,
			`posts`,
			`total_characters`,
			`total_words`,
			`average_characters`,
			`average_words`,
			`average_grade_level`,
			`prs_reputation`,
			`roleplay_rating`,
			`unique_reviews`
		) VALUES (
			'".$roleplay['id']."',
			'".time()."',
			'".$roleplay['posts']."',
			'".$roleplay['total_characters']."',
			'".$roleplay['total_words']."',
			'".$roleplay['average_characters']."',
			'".$roleplay['average_words']."',
			'".$roleplay['average_grade_level']."',
			'".$roleplay['average_prs_rating']."',
			'".$roleplay['roleplay_rating']."',
			'".$roleplay['unique_reviews']."'
		) ON DUPLICATE KEY UPDATE
			stats_updated=".time().",
			posts=".$roleplay['posts'].",
			total_characters=".$roleplay['total_characters'].",
			total_words=".$roleplay['total_words'].",
			average_characters=".$roleplay['average_characters'].",
			average_words=".$roleplay['average_words'].",
			average_grade_level=".$roleplay['average_grade_level'].",
			prs_reputation=\"".$roleplay['average_prs_rating']."\",
			roleplay_rating=\"".$roleplay['roleplay_rating']."\",
			unique_reviews=\"".$roleplay['unique_reviews']."\"
			
			";
	
	
	if (!$result = $db->sql_query($sql)) {
		echo "query is teh fail";
	} else {
		//$user_karma = prs_karma($account['id'],TRUE,TRUE);
	
		echo "...done, (".$roleplay['total_prs_rating']." / ".$roleplay['rated_posts']." = ".$roleplay['average_prs_rating'].") versus karma: (".$user_karma['karma'].")";
	}
	$db->sql_freeresult($result);
	
	unset($roleplay);

}

unset($roleplay_list);

function scoreRubric($value) {
	switch ($value) {
		case 'Advanced': return 5;
		case 'Proficient': return 3;
		case 'In-Progress': return 1;
	}
}

?>
