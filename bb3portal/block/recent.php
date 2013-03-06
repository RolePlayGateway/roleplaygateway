<?php
/*
*
* @name recent.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: recent.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

$cache = 3600;

//
// Exclude forums
//
$sql_where = '';
if ($CFG['exclude_forums'])
{
	$exclude_forums = explode(',', $CFG['exclude_forums']);
	foreach ($exclude_forums as $i => $id)
	{
		if ($id > 0)
		{
			$sql_where .= ' AND forum_id != ' . trim($id);
		}
	}
}

$threshold = time() - (2629743 * 3); // 1 month times 3 = three months

//Absolutely EXCLUDE from promotion in all cases  ~Eric, 9/26/08
$sql_where .= '
		AND topic_title NOT LIKE "%female%"
		AND topic_title NOT LIKE "%girl%"
		AND topic_title NOT LIKE "%slave%"
		AND topic_title NOT LIKE "%teenage%"
		AND topic_title NOT LIKE "%romance%"
		AND topic_title NOT LIKE "%partner%"
		AND topic_title NOT LIKE "%vampire%"
		AND topic_title NOT LIKE "%vampyre%"
		AND topic_title NOT LIKE "%one%one%"
		AND topic_title NOT LIKE "%prp%"
		AND forum_id <> 164
';

//
// Recent announcements
//
$sql = 'SELECT topic_title, forum_id, topic_id
	FROM ' . TOPICS_TABLE . '
	WHERE topic_status <> 2 
		AND topic_approved = 1 
		AND ( topic_type = 2 OR topic_type = 3 )
		' . $sql_where . '
	ORDER BY topic_time DESC';

$result = $db->sql_query_limit($sql, $CFG['max_topics'], null, $cache);

while( ($row = $db->sql_fetchrow($result)) && ($row['topic_title'] != '') )
{
	// auto auth
	if ( ($auth->acl_get('f_read', $row['forum_id'])) || ($row['forum_id'] == '0') )
	{
		$template->assign_block_vars('latest_announcements', array(
			'TITLE'	 		=> character_limit($row['topic_title'], $CFG['recent_title_limit']),
			'FULL_TITLE'	=> censor_text($row['topic_title']),
			'U_VIEW_TOPIC'	=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id'])
			)
		);
	}
}
$db->sql_freeresult($result);

//
// Recent hot topics
//
$sql = 'SELECT topic_title, forum_id, topic_id
	FROM ' . TOPICS_TABLE . '
	WHERE topic_approved = 1 
		AND topic_replies >=' . $config['hot_threshold'] . '
		' . $sql_where . '
	ORDER BY topic_time DESC';

// $result = $db->sql_query_limit($sql, 10, null, $cache);

while( ($row = $db->sql_fetchrow($result)) && ($row['topic_title'] != '') )
{
	// auto auth
	if ( ($auth->acl_get('f_read', $row['forum_id'])) || ($row['forum_id'] == '0') )
	{
		$template->assign_block_vars('latest_hot_topics', array(
			'TITLE'	 		=> character_limit($row['topic_title'], $CFG['recent_title_limit']),
			'FULL_TITLE'	=> censor_text($row['topic_title']),
			'U_VIEW_TOPIC'	=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id'])
			)
		);
	}
}
$db->sql_freeresult($result);

/* //
// Recent topic (only show normal topic)
//
$sql = 'SELECT topic_title, forum_id, topic_id
	FROM ' . TOPICS_TABLE . '
	WHERE topic_status <> 2 
		AND topic_approved = 1 
		AND topic_type = 0
		' . $sql_where . '
	ORDER BY topic_time DESC';

$result = $db->sql_query_limit($sql, $CFG['max_topics'], null, 600);

while( ($row = $db->sql_fetchrow($result)) && ($row['topic_title'] != '') )
{
	// auto auth
	if ( ($auth->acl_get('f_read', $row['forum_id'])) || ($row['forum_id'] == '0') )
	{
		$template->assign_block_vars('latest_topics', array(
			'TITLE'	 		=> character_limit($row['topic_title'], $CFG['recent_title_limit']),
			'FULL_TITLE'	=> censor_text($row['topic_title']),
			'U_VIEW_TOPIC'	=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id'])
			)
		);
	}	
}
$db->sql_freeresult($result); */

$sql = 'SELECT title,url,description as synopsis FROM rpg_roleplays WHERE status = "Open" ORDER BY created DESC';

$result = $db->sql_query_limit($sql,$CFG['max_topics']);
while ($row = $db->sql_fetchrow($result)) {
	$template->assign_block_vars('newest_roleplays', array(
		'TITLE'	 		=> character_limit($row['title'], $CFG['recent_title_limit']),
		'FULL_TITLE'	=> $row['title'],
		'SYNOPSIS'		=> $row['synopsis'],
		'URL'			=> 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/'
		)
	);	
}
$db->sql_freeresult($result); 


//
// Random excerpt (only show normal topic)
//
/*
$cutoff_date = time() - (1 * 86400);
$sql = 'SELECT post_text,topic_id FROM ' . POSTS_TABLE . ' WHERE post_time >= '.$cutoff_date.' ORDER BY RAND()';

$result = $db->sql_query_limit($sql, 1);

while($row = $db->sql_fetchrow($result))
{
	$excerpt = $row['post_text'];
	strip_bbcode($excerpt);

	$template->assign_block_vars('random_excerpt', array(
		'TEXT'	 		=> html_entity_decode(character_limit($excerpt,160)),
		'U_VIEW_TOPIC'	=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 't=' . $row['topic_id'])
		)
	);
}
$db->sql_freeresult($result);
*/

/* $sql = 'SELECT * FROM gateway_post_stats 
			INNER JOIN gateway_topics
				ON gateway_topics.topic_first_post_id = gateway_post_stats.post_id
			WHERE gateway_post_stats.prs_rating >= 42
				ORDER BY post_id DESC'; */
$sql = 'SELECT topic_title,topic_id FROM gateway_topics t
			INNER JOIN gateway_post_stats s ON t.topic_first_post_id = s.post_id
			WHERE s.votes > 2
				AND s.prs_rating >= 40
				' . $sql_where . '
				GROUP BY t.topic_title
				ORDER BY t.topic_id DESC';
// $result = $db->sql_query_limit($sql,$CFG['max_topics'], null, $cache);
while ($row = $db->sql_fetchrow($result))
{
/* 	$template->assign_block_vars('top_rated_posts', array(
		'POST_LINK' => '<a href="http://www.roleplaygateway.com/viewtopic.php?p='.$row["post_id"].'#p'.$row["post_id"].'">'.$row["post_subject"].'</a>'
	)); */
	
	$template->assign_block_vars('top_rated_posts', array(
		'TITLE'	 		=> character_limit($row['topic_title'], $CFG['recent_title_limit']),
		'FULL_TITLE'	=> censor_text($row['topic_title']),
		'U_VIEW_TOPIC'	=> @append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id'])
		)
	);
}
$db->sql_freeresult($result);


$sql = 'SELECT title,url,description FROM rpg_roleplay_stats s
			INNER JOIN rpg_roleplays r
				ON r.id = s.roleplay_id
			ORDER BY roleplay_rating DESC, unique_reviews DESC';
$result = $db->sql_query_limit($sql,$CFG['max_topics'], null, $cache);
while ($row = $db->sql_fetchrow($result))			
{
	$template->assign_block_vars('top_rated_roleplays', array(
		'TITLE'	 		=> character_limit($row['title'], $CFG['recent_title_limit']),
		'DESCRIPTION'	=> censor_text($row['description']),
		'URL'			=> @append_sid('http://www.roleplaygateway.com/roleplay/' . $row['url'] . '/#reviews')
		)
	);
}
$db->sql_freeresult($result);			

$sql = 'SELECT topic_title,topic_id FROM gateway_topics WHERE topic_time >= '.$threshold.' ORDER BY topic_replies DESC';
$result = $db->sql_query_limit($sql,10, null, $cache);
while ($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('most_popular_topics', array(
		'POST_LINK' => '<a href="http://www.roleplaygateway.com/viewtopic.php?t='.$row["topic_id"].'">'.$row["topic_title"].'</a>'
	));
}
$db->sql_freeresult($result);

?>
