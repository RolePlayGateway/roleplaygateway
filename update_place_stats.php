<?php
$phpbb_root_path = '/var/www/html/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

$sql = "SELECT place_id, count(*) as posts, SUM(character_count) as total_characters, SUM(word_count) as total_words, SUM(flesch_kincaid_grade) as total_grade_level, SUM(prs_rating) as total_prs_rating FROM rpg_content_stats GROUP BY place_id";
$result_stats=$db->sql_query($sql);
while($row=$db->sql_fetchrow($result_stats))
{
	$place['id']			= $row['place_id'];
	$place['total_characters'] 	= $row['total_characters'];
	$place['total_words']		= $row['total_words'];
	$place['total_grade_level']	= $row['total_grade_level'];
	$place['total_prs_rating'] 	= $row['total_prs_rating'];
	$place['rated_posts'] 		= 0;
	$place['posts'] 		= $row['posts'];
	
	if ($place['posts'] > 0) {
		$place['average_characters'] 	= $place['total_characters'] 	/ $place['posts'];
		$place['average_words'] 	= $place['total_words'] 	/ $place['posts'];
		$place['average_grade_level'] 	= $place['total_grade_level'] 	/ $place['posts'];

		if ($place['rated_posts'] > 0) {
			$place['average_prs_rating'] = $place['total_prs_rating'] / $place['rated_posts'];
		} else {
			$place['average_prs_rating'] = 0;
		}

	} else {
		$place['average_characters'] 	= 0;
		$place['average_words'] 	= 0;
		$place['average_grade_level'] 	= 0;
		$place['average_prs_rating'] 	= 0;
		$place['rated_posts'] 		= 0;
	}
	
	$place['total_reviews'] = 0;
	$place['unique_reviews'] = 0;	
	
	$place['average_characters'] 	= round($place['average_characters'],2);
	$place['average_words'] 	= round($place['average_words'],2);
	$place['average_grade_level'] 	= round($place['average_grade_level'],2);
	$place['average_prs_rating'] 	= round(($place['average_prs_rating'] / 10),2);

	//$karma = prs_karma($account['id'],true,true);
	//print_r($karma);

	$sql = "INSERT INTO `rpg_places_stats` (
			`place_id`,
			`stats_updated`,
			`posts`,
			`total_characters`,
			`total_words`,
			`average_characters`,
			`average_words`,
			`average_grade_level`,
			`prs_reputation`
		) VALUES (
			'".$place['id']."',
			'".time()."',
			'".$place['posts']."',
			'".$place['total_characters']."',
			'".$place['total_words']."',
			'".$place['average_characters']."',
			'".$place['average_words']."',
			'".$place['average_grade_level']."',
			'".$place['average_prs_rating']."'
		) ON DUPLICATE KEY UPDATE
			stats_updated=".time().",
			posts=".$place['posts'].",
			total_characters=".$place['total_characters'].",
			total_words=".$place['total_words'].",
			average_characters=".$place['average_characters'].",
			average_words=".$place['average_words'].",
			average_grade_level=".$place['average_grade_level'].",
			prs_reputation=\"".$place['average_prs_rating']."\"	
			";
	
	
	if (!$result = $db->sql_query($sql)) {
		echo "query is teh fail";
	} else {
		//$user_karma = prs_karma($account['id'],TRUE,TRUE);
	
		echo "Place ID:" .$place['id'] . " updated";
	}
	
	$db->sql_freeresult($result);

	unset($place);
}

?>
