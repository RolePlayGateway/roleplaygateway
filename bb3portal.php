<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'config.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

if ($user->data['user_id'] == 4) {
	error_reporting(0);
	ini_set('display_errors',true);
}

page_header($config['sitename']);


	$template->assign_vars(array(
		//'S_SHOW_ROLEPLAYS' => ($auth->acl_get('m_')) ? true : false,	
		'S_SHOW_ROLEPLAYS' => true,	
	));

	$sql = "SELECT DISTINCT r.id,
				/* log(s.average_words) * log(1 / (unix_timestamp() - unix_timestamp(r.updated))) as ranking, */
				id,title,type,description,owner,player_slots,username,require_approval,updated FROM rpg_roleplays r
				INNER JOIN gateway_tags t
					ON r.id = t.roleplay_id
				INNER JOIN gateway_users u
					ON r.owner = u.user_id
				INNER JOIN rpg_roleplay_stats s
					ON s.roleplay_id = r.id
				WHERE (r.status = 'Open'
          AND s.posts > 2
          AND s.average_words > 250
          AND s.average_grade_level > 8
					AND length(r.image) > 0
          AND length(r.description) > 5
					AND t.tag = 'original'
					AND r.id NOT IN (SELECT roleplay_id FROM gateway_tags WHERE tag IN
							('romance', 'high school', 'school', 'disney', 'love', 'gay', 'academy',
								'mature', 'yaoi', 'yuri', 'adult', 'anime', 'manga', 'vampire', 'neko',
								'sex', 'demon', 'demons', 'gods', 'slave', 'vampires', 'mutants', 'teenager',
								'hitler', 'nazi'
								))
				) OR featured = 1
				/* ORDER BY r.updated DESC */
        ORDER BY created DESC";
        
	/* $sql = 'select id,title,type,description,owner,player_slots,require_approval,updated FROM rpg_roleplay_stats s INNER JOIN rpg_roleplays r ON s.roleplay_id = r.id
    WHERE posts > 50  ORDER BY average_words DESC'; */
	$result = $db->sql_query_limit($sql,15 ,null,3600);

	while($row = $db->sql_fetchrow($result)) {

    $sql          = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $row['owner'] ;
    $countResult  = $db->sql_query($sql);
    $row['username'] = $db->sql_fetchfield('username');
    $db->sql_freeresult($countResult);

  
		$template->assign_block_vars('roleplays', array(
			'S_CAN_EDIT'		=> (($auth->acl_get('a_')) || ($row['owner'] == $user->data['user_id'])) ? true : false,
			'ID'				=> $row['id'],
			'TITLE'				=> $row['title'],
			'URL'				=> urlify($row['title']),
			'DESCRIPTION'		=> $row['description'],
			'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
			'TOTAL_SLOTS'		=> $row['player_slots'],
			'CHARACTERS'		=> $row['characters'],
			'POSTS'				=> $row['posts'],
			'WORDS_PER_POST' 	=> $row['words_per_post'],
			'TYPE'				=> $row['type'],
			'TYPE_DESCRIPTION'	=> @$type_description,
			'ACTIONS'			=> @$actions,
			'TAGS'				=> @display_roleplay_tags(get_roleplay_tags($row['id'])),
			'LAST_ACTIVITY'		=> $user->format_date($row['last_activity']),
		));
	}
	// free the result
	$db->sql_freeresult($result);
	
	// BEGIN USER STATS
	/* 
	$sql = 'SELECT s.total_words,u.username,s.user_id FROM gateway_user_stats s INNER JOIN gateway_users u ON s.user_id = u.user_id WHERE s.posts > 10 ORDER BY total_words DESC';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('top_words_written', array(
			'WORDS'		=> $row['total_words'],
			'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username']),
		));

	}
	$db->sql_freeresult($result);
	
	$sql = 'SELECT s.average_words,u.username,s.user_id FROM gateway_user_stats s INNER JOIN gateway_users u ON s.user_id = u.user_id WHERE s.posts > 10 ORDER BY average_words DESC';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('top_average_words', array(
			'WORDS'		=> $row['average_words'],
			'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username']),
		));

	}
	$db->sql_freeresult($result);
	
	$sql = 'SELECT s.average_grade_level,u.username,s.user_id FROM gateway_user_stats s INNER JOIN gateway_users u ON s.user_id = u.user_id WHERE s.posts > 10 ORDER BY average_grade_level DESC';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('top_grade_level', array(
			'AVERAGE_GRADE_LEVEL'		=> $row['average_grade_level'],
			'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username']),
		));

	}
	$db->sql_freeresult($result);	
	 */
	// END USER STATS
	
	
	
	
	//BEGIN ROLEPLAY STATS

	$sql = 'SELECT total_words,url,title FROM rpg_roleplay_stats s INNER JOIN rpg_roleplays r ON s.roleplay_id = r.id WHERE s.posts >= 10 ORDER BY total_words DESC';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('longest_roleplays', array(
			'WORDS'		=> number_format($row['total_words']),
			'LINK'	=> '<a href="http://www.roleplaygateway.com/roleplay/'.$row['url'].'/">'.$row['title'].'</a>',
		));

	}
	$db->sql_freeresult($result);

	$sql = 'SELECT average_words,url,title FROM rpg_roleplay_stats s INNER JOIN rpg_roleplays r ON s.roleplay_id = r.id WHERE s.posts >= 10 ORDER BY average_words DESC';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('longest_posts_in_roleplays', array(
			'AVERAGE_WORDS'		=> number_format($row['average_words']),
			'LINK'	=> '<a href="http://www.roleplaygateway.com/roleplay/'.$row['url'].'/">'.$row['title'].'</a>',
		));

	}
	$db->sql_freeresult($result);
	
	$sql = 'SELECT average_grade_level,url,title FROM rpg_roleplay_stats s INNER JOIN rpg_roleplays r ON s.roleplay_id = r.id WHERE s.posts >= 10 ORDER BY average_grade_level DESC';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('most_advanced_roleplays', array(
			'AVERAGE_GRADE_LEVEL'		=> number_format($row['average_grade_level']),
			'LINK'						=> '<a href="http://www.roleplaygateway.com/roleplay/'.$row['url'].'/">'.$row['title'].'</a>',
		));

	}
	$db->sql_freeresult($result);
	
	$sql = 'select url, title FROM rpg_roleplay_stats s INNER JOIN rpg_roleplays r ON s.roleplay_id = r.id WHERE posts > 50  ORDER BY average_words DESC';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('awesome_roleplays', array(
			'LINK'						=> '<a href="http://www.roleplaygateway.com/roleplay/'.$row['url'].'/">'.$row['title'].'</a>',
		));

	}
	$db->sql_freeresult($result);
	
	$sql = 'SELECT url,title,DATEDIFF(CURRENT_DATE,created) as age FROM rpg_roleplays r WHERE id IN (SELECT roleplay_id FROM rpg_content WHERE DATEDIFF(CURRENT_DATE,written) < 14) AND status = "Open" ORDER BY created';
	$result = $db->sql_query_limit($sql,10,null,3600);
	while($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('oldest_active_roleplays', array(
			'AGE'						=> number_format($row['age']),
			'LINK'						=> '<a href="http://www.roleplaygateway.com/roleplay/'.$row['url'].'/">'.$row['title'].'</a>',
		));

	}
	$db->sql_freeresult($result);

	// now we run the query again to get the total rows...
	// the query is identical except we count the rows instead
	$sql = "SELECT count(*) as total_roleplays FROM rpg_roleplays";
	$result = $db->sql_query($sql,600);

	// get the total users, this is a single row, single field.
	$total_roleplays = (int) $db->sql_fetchfield('total_roleplays');
	// free the result
	$db->sql_freeresult($result);

	// Assign the pagination variables to the template.
	$template->assign_vars(array(
		'TOTAL_ROLEPLAYS'   => number_format($total_roleplays) . ' roleplays listed',
		'S_PAGE_ONLY' => true,
		'S_HIDE_AD' => true,
	));


#and finally, specifying which template file is to be used
#these can be found in /styles/RolePlayGateway/templates/
$template->set_filenames(array(
  'body' => 'front_page.html')
);

page_footer();

function get_roleplay_tags($id) {

	global $db, $auth;

	$sql = "SELECT tag FROM gateway_tags WHERE roleplay_id = ".$id;

	$result     = $db->sql_query($sql);

	while( $row = $db->sql_fetchrow($result) )
	{
		$tags[] = '<a href="http://www.roleplaygateway.com/tag/'.$row['tag'].'">'.$row['tag'].'</a>';
	}
	
	$db->sql_freeresult($result);
	
	
	return $tags;

}

function display_roleplay_tags($tags) {

	$output = @implode(", ",$tags);

	return $output;
}

?>
