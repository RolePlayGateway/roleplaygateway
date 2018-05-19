<?php 
// m_lock controlls if moderator can lock / unlock / edit ratings
/**
*
* @package prs
* @version 1.0.0 2007/12/23 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
        exit;
}

function &prs_switches($mode = '', $data = NULL)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$enabled = $config['prs_enabled'];
	$k_enabled = $config['prs_karma_enabled'] && $enabled;
	$mp_enabled = $config['prs_modpoints_enabled'] && $enabled;
	$p_enabled = $config['prs_modpoints_enabled'] && $enabled;

	$arr = array(
		'S_PRS_ENABLED'		=> $enabled,
		'S_PRS_KARMA_ENABLED'		=> $k_enabled,
		'S_PRS_MODPOINTS_ENABLED'	=> $mp_enabled,
		'S_PRS_PENALTY_ENABLED'	=> $p_enabled,
		'S_PRS_EXTRA_TOPICS_RATINIGS'	=> $config['prs_extra_topic_rating'],
	);


        switch($config['prs_extra_topic_rating'])
        {
                case PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST:
                        $arr['PRS_EXTRA_TOPICS_RATINGS'] =
                                $user->lang['PRS_FIRST_POSTS_SCORE'];
 
                break;
 
                case PRS_EXTRA_TOPICS_RATINIGS_AVERAGE_POSTS:
                        $arr['PRS_EXTRA_TOPICS_RATINGS'] =
                                $user->lang['PRS_AVERAGE_POSTS_SCORE'];
                break;
        }

	switch($mode)
	{
		case 'forum_view':
			$arr = array_merge($arr, array(
				'S_PRS_CAN_LOCK_VOTES'	=> $auth->acl_get('m_lock', $data['forum_id']),
				'S_PRS_CAN_SET_VOTE'		=> $auth->acl_get('m_lock', $data['forum_id']),
			));
		break;

		case 'topic_view':
			$arr = array_merge($arr, array(
				'S_PRS_CAN_LOCK_VOTE'		=> $auth->acl_get('m_lock', $data['forum_id']),
				'S_PRS_CAN_SET_VOTE'		=> $auth->acl_get('m_lock', $data['forum_id']),
			));
		break;

		case 'post_details':
			$closed_list = prs_are_closed(array($data['post_id']));
			$arr = array_merge($arr, array(
				'S_PRS_CAN_LOCK_VOTE'		=> $auth->acl_get('m_lock', $data['forum_id']),
				'S_PRS_CAN_SET_VOTE'		=> $auth->acl_get('m_lock', $data['forum_id']),
				'S_PRS_VOTEROUND_CLOSED'	=> $closed_list[$data['post_id']],
			));
		break;
	}
	return $arr;
}

function prs_is_votable_basis($post_id, $score, $trigger = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0 || $score <= 0)
	{
		return FALSE;
	}

	// User must not be anonymous
	if ($user->data['user_id'] == ANONYMOUS)
	{
		if ($trigger)
		{
			// EDITED 6/17 to allow anonymous vote!
			return FALSE;
			// END EDITED
			trigger_error('PRS_NO_ANONYMOUS_VOTE');
		}
		else
		{
			return FALSE;
		}
	}

	// User may not rate his own post.
	$row = prs_get_select_post($post_id);
	if (!$config['prs_extra_own_vote'] &&
		$row['poster_id'] == $user->data['user_id'])
	{
		if ($trigger)
		{
			trigger_error('PRS_NO_OWN_VOTE');
		}
		else
		{
			return FALSE;
		}
	}

	// User must have 'prs_votes_min_posts' post and have bin
	// a member for 'prs_votes_membership_period' days
	$begin_date = time() - $config['prs_votes_membership_period'];
	if ($user->data['user_posts'] < $config['prs_votes_min_posts'] || $user->data['user_regdate'] > $begin_date)
	{
		if ($trigger)
		{
			trigger_error(sprintf($user->lang['PRS_USER_REQUIREMENTS'],
				$config['prs_votes_membership_period'] / 86400,
				$config['prs_votes_min_posts']));
		}
		else
		{
			return FALSE;
		}
	}

	// $score needs to be between 1 and PRS_MAX_NUMBER_STARS
	if ($score < 1 || $score > PRS_MAX_NUMBER_STARS)
	{
		if ($trigger)
		{
			trigger_error('PRS_NO_VOTE');
		}
		else
		{
			return FALSE;
		}
	}

	// post is locked
	$sql = 'SELECT topic_id, post_edit_locked
		FROM ' . POSTS_TABLE . '
		WHERE post_id = ' . $post_id;
	$result = $db->sql_query_limit($sql, 1);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	if ($row['post_edit_locked'])
	{
		if ($trigger)
		{
			trigger_error('PRS_POST_LOCKED');
		}
		else
		{
			return FALSE;
		}
	}

	// topic is loked (keep this under post is locked!)
	$sql = 'SELECT topic_status
		FROM ' . TOPICS_TABLE . '
		WHERE topic_id = ' . $row['topic_id'];
	$result = $db->sql_query_limit($sql, 1);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	if ($row['topic_status'] % 2)
	{
		if ($trigger)
		{
			trigger_error('PRS_TOPIC_LOCKED');
		}
		else
		{
			return FALSE;
		}
	}

	// User may vote only ones
	if ($config['prs_extra_vote_only_ones'])
	{
		$sql = 'SELECT time
			FROM ' . PRS_VOTES_TABLE . '
			WHERE post_id = ' . $post_id . '
				AND user_id = ' . $user->data['user_id'];
		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		if ($row !== FALSE)
		{
			if ($trigger)
			{
				trigger_error('PRS_VOTE_ONLY_ONES');
			}
			else
			{
				return FALSE;
			}
		}
		$db->sql_freeresult($result);
	}

	// Users may vote only on first post
	if ($config['prs_extra_first_post_only'])
	{
		$sql = 'SELECT topic_id
			FROM ' . TOPICS_TABLE . '
			WHERE t.topic_first_post_id = ' . $post_id;
                $result = $db->sql_query_limit($sql, 1);
                $row = $db->sql_fetchrow($result);
                if ($row === FALSE)
                {
                        if ($trigger)
                        {
                                trigger_error('PRS_VOTE_ONLY_FIRST_POST');
                        }
                        else
                        {
                                return FALSE;
                        }
                }
                $db->sql_freeresult($result);
	}
	return TRUE;
}

function prs_are_closed($post_list)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$arr = array();
	$sql = 'SELECT post_id
		FROM ' . POSTS_TABLE . '
		WHERE prs_score > 0
			AND ' . $db->sql_in_set('post_id', $post_list);
	$result = $db->sql_query($sql, 300);
	while($row = $db->sql_fetchrow($result))
	{
		$arr[$row['post_id']] = 1;
	}
	$db->sql_freeresult($result);
	foreach ($post_list as $post_id)
	{
		if (!isset($arr[$post_id]))
		{
			 $arr[$post_id] = 0;
		}
	}
	return $arr;
}

function prs_is_voteround_open_basis($post_id, $row = NULL, $score = 3, $trigger = FALSE)

{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0 || $score <= 0)
	{
		return FALSE;
	}

	$closing_time = time() - $config['prs_votes_period'];
	if ($row == NULL)
	{
		$row = prs_get_select_post($post_id);
	}

	// Voting round is closed
	if ($closing_time >= @$row['post_time'])
	{
		if ($trigger)
		{
			trigger_error('PRS_VOTEROUND_CLOSED');
		}
		else
		{
			return FALSE;
		}
	}

	// Find prs_voteround_closed and add it to row.
	if (!isset($row['prs_score']))
	{
		$sql = 'SELECT prs_score
			FROM ' . POSTS_TABLE . '
			WHERE post_id = ' . $post_id;
		$result = $db->sql_query($sql);
		foreach($db->sql_fetchrow($result) as $key => $value)
		{
			$row[$key] = $value;
		}
		$db->sql_freeresult($result);
	}

	// Is post not marked as closed?
	return !$row['prs_score'];
}

function &prs_get_votes_dataset($post_list, $user_list = NULL, $sql_array = NULL, $related_tables = TRUE, $cache_ttl = 3600, $from_cron = false)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
	
	// This code will limit the post query to the maximum number of posts per page...
	if ($from_cron != true) {
		for ($i = 0; $i <= $config['posts_per_page']; $i++) {
			$new_array[] = @$post_list[$i];
		}
		$post_list = $new_array;
	}
		
	if ($post_list == NULL)
	{
		$arr = array(
			'score'	=>	0,
			'n'	=>	0,
		);
		return $arr;
	}

	$sql = 'SELECT ';
	if (isset($sql_array['SELECT']))
	{
		$sql .= 'v.post_id, v.user_id, '.$sql_array['SELECT'];
	}
	else
	{
		$sql .= 'v.*';
		if ($related_tables)
		{
			$sql .= ', ' . prs_sql_multiselect(
				array('u', 'p'),
				array(
					'user_id',
					'username',
					'user_colour',
			)) . ', ' . prs_sql_multiselect(
				array('up', 'pp'),
				array(
					'post_username'
			));
		}
	}

	$sql .= ' FROM ' . PRS_VOTES_TABLE . ' AS v';
	if (isset($sql_array['FROM']))
	{
		$sql .= ', ' . $sql_array['FROM'];
	}
	else
	{
		if ($related_tables)
		{
			$sql .=', ' . USERS_TABLE . ' AS u, ' .
				USERS_TABLE . ' AS p, ' .
				POSTS_TABLE . ' AS up, ' .
				POSTS_TABLE . ' AS pp';
		}
	}
	
	// This code sucks big SQL donkey balls.
	// TODO: fix.

	$sql .= ' WHERE 1 ';
	if ($post_list != NULL)
	{
		$sql .= ' AND ' .  $db->sql_in_set('v.post_id', $post_list);
	}
	if ($user_list != NULL)
	{
		$sql .= ' AND ' .  $db->sql_in_set('v.user_id', $user_list);
	}
	if (isset($sql_array['WHERE']))
	{
		$sql .= ' AND ' . $sql_array['WHERE'];
	}
	if (!isset($sql_array['FROM']))
	{
		if ($related_tables)
		{
			$sql .= ' AND v.post_id = pp.post_id
				AND pp.poster_id = p.user_id
				AND v.user_id = u.user_id
				AND u.user_id = up.poster_id';
		}
	}	
	/*
	if (strlen($sql) >= 4096) {
		$to = "admin@roleplaygateway.com";
		$subject = "RPG: Long Query in functions_prs_basis.php";
		$body = "Found a long query from ".$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'].": $sql";
	
		mail($to, $subject, $body);
	}
	*/
	$result = $db->sql_query($sql, $cache_ttl);

	$dataset = array();
	while($row = $db->sql_fetchrow($result))
	{
		foreach ($row as $key => $value)
		{

			$post_id = $row['post_id'];
			$user_id = $row['user_id'];
			
			$karma_stuff = prs_karma($user_id);
			$reputation_weight = ($karma_stuff['karma'] * 20) / 70;
			$reputation_weight = 1;
			
			switch($key)
			{
				case 'score':
					if ($value < 0)
					{
						$value = 0;
					}
					elseif ($value > PRS_MAX_NUMBER_STARS * PRS_MULTIPLIER_SCORE * $reputation_weight)
					{
						$value = PRS_MAX_NUMBER_STARS * PRS_MULTIPLIER_SCORE * $reputation_weight;
					}
				break;
			}
			if (!isset($dataset[$post_id]))
			{
				$dataset[$post_id] = array();
			}
			if (!isset($dataset[$post_id][$user_id]))
			{
				$dataset[$post_id][$user_id] = array();
			}
			$dataset[$post_id][$user_id][$key] = $value;
		}
	}
	$db->sql_freeresult($result);
	$grand_score = $grand_n = 0;
	$grand_arr = array();
	foreach($dataset as $post_id => $data)
	{
		$score = $n = 0;
		$dataset[$post_id]['score'] = 0;
		$arr = array();
		foreach ($data as $user_id => $row)
		{
			$arr[] = $row['score'];
			$grand_arr[] = $row['score'];
			$score += $row['score'];
			$n++;
		}
		$grand_score += $score;
		$grand_n += $n;
		$dataset[$post_id]['n'] = $n;
		if ($n)
		{
			$score /= $n;
		}
		$dataset[$post_id]['score'] = ($n)
			? round($score)
			: $config['prs_default_rating'];
		$tmp = &prs_stat_standard_diviation($arr, $score, $n);
		$dataset[$post_id]['o'] = ($n) ? $tmp['o'] : 0;
		$dataset[$post_id]['n'] = $n;
	}
	$dataset['n'] = $grand_n;
	if ($grand_n)
	{
		$grand_score /= $grand_n;
	}
	
	$dataset['score'] = ($grand_n)
		? round($grand_score)
		: $config['prs_default_rating'];
	$tmp = &prs_stat_standard_diviation($grand_arr, $grand_score, $grand_n);
	$dataset['o'] = ($grand_n) ? $tmp['o'] : 0;
	$dataset['n'] = $grand_n;
	return $dataset;
}

function &prs_get_votes_dataset_simple($post_list, $user_list = NULL, $sql_array = NULL)
{
	return prs_get_votes_dataset($post_list, $user_list, $sql_array, FALSE);
}

function prs_display_votes(&$dataset, $post_list = NULL, $user_list = NULL)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$n = 0;
	foreach ($dataset as $post_id => $row)
	{
		if ($post_list != NULL && !isset($post_lis[$post_id]) ||
			!is_int($post_id))
		{
			continue;
		}
		foreach ($row as $user_id => $data)
		{
			if ($post_list != NULL &&
					!isset($user_list[$user_id]) ||
				!is_int($user_id))
			{
				continue;
			}

			$arr = prs_stars($data['score']);
			$arr = array_merge($arr, array(
				'S_ROW_COUNT'		=> $n,
				'VOTEE_FULL'		=> get_username_string('full', $data['u_user_id'], $data['u_username'], $data['u_user_colour'], $data['u_post_username']),
//				'IP'			=> $row['ip'],
//				'HOSTNAME'		=> '',
//				'U_WHOIS'		=> '',
//				'U_LOOKUP_IP'		=> '',
				'TIME'			=> $user->format_date($row['time']),
				'PRS_SHADOW'	=> $row['shadow'],
			));
			$template->assign_block_vars('postsratingsrow', $arr);
			$n++;
		}
	}

	// PRS_STAR[1-5S]
	$arr = prs_stars($row['score']);
	return $arr;
}

function prs_close_posts($post_list = NULL, $force = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!$config['prs_enabled'] || prs_is_locked())
	{
		return;
	}
        prs_lock();

	// Is post_list a single ID?
	if (!is_array($post_list))
	{
		print "prs_close_posts(int) is obsolite<br>";
		return prs_close_posts(array($post_list));
	}

	// Which post need to be closed? (assigned a score)
	$closing_time = time() - $config['prs_votes_period'];
	$sql = 'SELECT post_id
		FROM ' . POSTS_TABLE . '
                WHERE prs_score = 0';
	if (!$force)
	{
		$sql .= ' AND post_time < ' . $closing_time;
	}
	if ($post_list != NULL)
	{
		$sql .= ' AND ' . $db->sql_in_set('post_id', $post_list);
	}
	$result = $db->sql_query($sql);
	$post_list = array(); // filter out already set
	while ($row = $db->sql_fetchrow($result))
	{
		$post_list[] = $row['post_id'];
	}
	$db->sql_freeresult($result);

	// Get votes data
	$dataset = &prs_get_votes_dataset_simple($post_list);

	// Close the posts
	$arr = array();
	foreach ($dataset as $post_id => $data)
	{
		if (!is_int($post_id) || !$data['score'])
		{
			continue;
		}
		$sql = 'UPDATE ' . POSTS_TABLE . '
                        SET prs_score = ' . $data['score'] . ',
				prs_standard_diviation = ' . $data['o'] . '
			WHERE post_id = ' . $post_id;
		$db->sql_freeresult($db->sql_query($sql));
		unset($post_list[$post_id]);
		$arr[] = $post_list;
	}
	
	//	removed, as per possible issues with table locking and large WHERE IN statements. ~ Eric, 9/9/2008
	/*
	$diff = array_diff($post_list, $arr);
	if (sizeof($diff))
	{
		$sql = 'UPDATE ' . POSTS_TABLE . '
			SET prs_score = ' . PRS_DEFAULT_SCORE_NULL . ',
				prs_standard_diviation = ' . 0 . '
			WHERE ' . $db->sql_in_set('post_id', $diff);
		$db->sql_freeresult($db->sql_query($sql));
	}
	*/
	prs_unlock();
}

function prs_determine_score($user_id, &$data, $admin = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!$data['voteround_open'] || $config['prs_extra_always_show_rating'] || $admin)
	{
		$score = $data['score'];
		if ($score == PRS_DEFAULT_SCORE_NULL)
		{
			$score = $config['prs_default_rating'];
		}
		return  $score;
	}
	return isset($data[$user_id]['score'])
		? $data[$user_id]['score']
               	: PRS_START_SCORE;
}

function prs_submit_vote($mode, &$data)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!$config['prs_enabled'])
	{
		return;
	}

	$current_time = time();
	$sql_data = array();

	// This if statement allows duplicate anonymous posts
	// be careful about indexes; SQL might not allow it if there is a duplicate index
	if ($row['user_id'] != 1) {
		// has the user already voted?
			$sql = 'SELECT user_id
					FROM ' . PRS_VOTES_TABLE . '
					WHERE post_id = ' . $data['post_id'] . '
						AND user_id = ' . $user->data['user_id'];
	} else {
		// has the user already voted from this ip address?
			$sql = 'SELECT user_id
					FROM ' . PRS_VOTES_TABLE . '
					WHERE post_id = ' . $data['post_id'] . '
						AND user_ip = ' . $data['user_ip'] . '
						AND user_id = ' . $user->data['user_id'];			
	}
	
	
		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	//	$data['vote_id'] = ($row) ? $row['vote_id'] : 0;

  
	if (isset($row['user_id']))
	{
	
		$sql = 'UPDATE ' . PRS_VOTES_TABLE . '
			SET score = ' . $data['score'] . ',
				time = ' . time() . ',
				user_ip = \'' . $_SERVER["REMOTE_ADDR"] . '\'
			WHERE user_id = ' . $data['user_id'] . '
			  AND post_id = ' . $data['post_id'];
		$db->sql_freeresult($db->sql_query($sql));
	}
	else
	{
		$sql_data[PRS_VOTES_TABLE]['sql'] = array(
			'post_id'	=> $data['post_id'],
			'score'		=> $data['score'],
			'user_id'	=> $data['user_id'],
			'user_ip'	=> $_SERVER['REMOTE_ADDR'],
			'time'		=> time(),
		);
		$sql = 'INSERT INTO ' . PRS_VOTES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[PRS_VOTES_TABLE]['sql']);
		$db->sql_freeresult($db->sql_query($sql));
		prs_reduce_modpoints($data['user_id']);
	}


	$params = $add_anchor = '';
	$params .= '&amp;t=' . $data['topic_id'];
	$params .= '&amp;p=' . $data['post_id'];

	$add_anchor = '#p' . $data['post_id'];

	$url = (!$params) ? "{$phpbb_root_path}viewforum.$phpEx" : "{$phpbb_root_path}viewtopic.$phpEx";
	$url = append_sid($url, 'f=' . $data['forum_id'] . $params) . $add_anchor;
	return $url;
}

function &prs_fetch_votes_posts($post_list, &$rowset)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;	
	
	if (!$config['prs_enabled'] || !count($post_list))
	{
		$arr = array();
		return $arr;
	}

	$dataset = array();
	$votes_set = &prs_get_votes_dataset_simple($post_list);

	$first_post_list = &prs_get_first_post_in_post_list($post_list);

	foreach ($post_list as $post_id)
	{
		$dataset[$post_id] = array();
		$dataset[$post_id]['first_post'] = isset($first_post_list[$post_id]);
		$votable = prs_is_voteround_open($post_id, $rowset[$post_id]);
		$dataset[$post_id]['voteround_open'] = $votable;
		$dataset[$post_id]['n'] = 0;
		$dataset[$post_id]['score'] = $votable ? PRS_START_SCORE : $config['prs_default_rating'];
	}
	foreach ($votes_set as $post_id => $votes_row)
	{
		if (!is_int($post_id))
		{
			continue;
		}
		foreach ($votes_row as $user_id => $votes_data)
		{
			if (!is_int($user_id))
			{
				continue;
			}
			if ($dataset[$post_id]['voteround_open'] &&
				$user_id != $user->data['user_id'])
			{
				continue;
			}
			$dataset[$post_id][$user_id] = array(
				'score'		=> $votes_data['score'],
//				'o'		=> $votes_data['o'],
	
				'post_id'	=> $votes_data['post_id'],
//				'ip'		=> $votes_data['ip'],
				'time'		=> $votes_data['time'],
				'shadow'	=> $votes_data['shadow'],
			);
		}
		$dataset[$post_id]['n']		= $votes_row['n'];
		$dataset[$post_id]['score']	= $votes_row['score'];
	}
	
 	// When the voting round has closed the scores should be read
	// out of the posts table.
	$sql = 'SELECT post_id, prs_score
		FROM ' . POSTS_TABLE .'
		WHERE ' . $db->sql_in_set('post_id', $post_list);
	$result = $db->sql_query($sql, 0);
	while ($row = $db->sql_fetchrow($result))
	{
		$post_id = $row['post_id'];
		$score = $row['prs_score'];
		if ($score > 0)
		{
			$dataset[$post_id]['score'] = $score;
		}
	}
	return $dataset;
}

function &prs_display_rating_posts(&$row, &$dataset, $admin = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!$config['prs_enabled'])
	{
		return array();
	}

	$post_id = $row['post_id'];
	$user_id = $user->data['user_id'];
	$data = &$dataset[$post_id];

	$votable = $data['voteround_open'];
	
	$mode = 'mode=vote';
	$params  = "&amp;p={$row['post_id']}&amp;t={$row['topic_id']}&amp;f={$row['forum_id']}";

	// Close voting round for this post
//	if (!$votable && $row['prs_score'] == 0)
	if (!$votable)
	{
		prs_close_posts(array($post_id));
	}
	

	// Determine the score for this post
	$score = prs_determine_score($user_id, $data, $admin);
	
	$sql = 'SELECT count(*) as votes FROM gateway_prs_votes WHERE post_id = '.$post_id;
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
		$votes = $row['votes'];
	}
	
		// Insert and/or update the score into the table that keeps track of these things
		$sql = 'INSERT INTO `gateway_post_stats` (post_id,prs_rating,votes)
			VALUES ('.$post_id.','.$score.','.$votes.') ON DUPLICATE KEY UPDATE prs_rating='.$score.',post_id='.$post_id.',votes='.$votes;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);


	// Return template variables with the right star images
	$base = 'prs_star_' . ($votable ? 'v_' : 'uv_');
	$postrow = &prs_stars(array('score' => $score, 'n' => $data['n']), $base, $votable ? 2 : 3);
	
	$postrow = array_merge($postrow, array(
		'S_PRS_VOTABLE'	=> $votable,
		'PRS_VOTEROUND_CLOSED_SCORE'       => $user->lang['PRS_VOTEROUND_CLOSED'] . ' ' . sprintf($user->lang['PRS_SCORE'], ($score / PRS_MULTIPLIER_SCORE), $data['n']),
		'U_PRS_STAR1'	=> append_sid("{$phpbb_root_path}prs.$phpEx", "${mode}&amp;s=1{$params}"),
		'U_PRS_STAR2'	=> append_sid("{$phpbb_root_path}prs.$phpEx", "${mode}&amp;s=2{$params}"),
		'U_PRS_STAR3'	=> append_sid("{$phpbb_root_path}prs.$phpEx", "${mode}&amp;s=3{$params}"),
		'U_PRS_STAR4'	=> append_sid("{$phpbb_root_path}prs.$phpEx", "${mode}&amp;s=4{$params}"),
		'U_PRS_STAR5'	=> append_sid("{$phpbb_root_path}prs.$phpEx", "${mode}&amp;s=5{$params}"),
		'S_PRS_ENABLED' => $config['prs_enabled'] && (!$config['prs_extra_first_post_only'] || $dataset[$post_id]['first_post']),
	));
	return $postrow;
}

function &prs_fetch_votes_topics($topic_list, &$rowset, $admin = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!$config['prs_enabled'] || count($topic_list) == 0)
	{
		$arr = array();
		return $arr;
	}

	// Add moved topic id to the list
	foreach ($topic_list as $topic_id)
	{
		if (!isset($rowset[$topic_id]))
		{
			continue;
		}
		$tmp = $rowset[$topic_id];
		if (isset($tmp['topic_status'])
			&& $tmp['topic_status'] == ITEM_MOVED)
		{
			$topic_list[] = $tmp['topic_moved_id'];
		}
	}

	$post_list = $postset = array();
        $dataset = array(
                'score'         => 0,
                'n'             => 0,
        );

	
	// get a list of topic_ids and topic_first_post_ids
	$sql = 'SELECT topic_id, topic_first_post_id
		FROM ' . TOPICS_TABLE .'
		WHERE ' . $db->sql_in_set('topic_id', $topic_list);
	$result = $db->sql_query($sql, 300);
	while ($row = $db->sql_fetchrow($result))
	{
		$topic_id = $row['topic_id'];
		$dataset[$topic_id]['first_post_id'] = $row['topic_first_post_id'];
	}
	foreach ($topic_list as $topic_id)
	{
		$dataset[$topic_id]['post_list'] = array();
	}

	switch($config['prs_extra_topic_rating'])
	{
		default:
		case PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST:
			foreach ($dataset as $topic_id => $data)
			{
				if (!is_int($topic_id))
				{
					continue;
				}
				$post_id = $data['first_post_id'];
				$post_list[] = $post_id;
				$postset[$post_id] = prs_get_select_post($post_id);
				$postset[$post_id]['topic_id'] = $topic_id;
			}
		break;

		// This code removed 6/9/2009 to completely eliminate the possibility of long $post_list - causes load. always calculate topic value on first post.
/* 		// get a list of topis_ids and post_ids
		case PRS_EXTRA_TOPICS_RATINIGS_AVERAGE_POSTS:
			$sql = 'SELECT topic_id, post_id
				FROM ' . POSTS_TABLE . '
				WHERE ' . $db->sql_in_set('topic_id', $topic_list);
			$result = $db->sql_query($sql, 300);
			while ($row = $db->sql_fetchrow($result))
			{
				$post_id = $row['post_id'];
				$post_list[] = $post_id;
				$postset[$post_id] = prs_get_select_post($post_id);
				$postset[$post_id]['topic_id'] = $row['topic_id'];
				$dataset[$topic_id]['post_list'][] = $post_id;
			}
		break; */
	}
	$db->sql_freeresult($result);

	// fetching votes
	$arr = &prs_fetch_votes_posts($post_list, $postset);

	// change dataset from [$post_id][$user_id] to [$topic_id][$user_id]
	$score = array();
	foreach ($topic_list as $topic_id)
	{
		$score[$topic_id]['score'] = 0;
		$score[$topic_id]['n'] = 0;
	}

	// Get rating
	$set_default = 0; // YES 1 NO 0, 2, 3
	foreach ($arr as $post_id => $row)
	{
		$topic_id		= $postset[$post_id]['topic_id'];
//		$dataset[$topic_id]	= $row;
		switch($config['prs_extra_topic_rating'])
		{
                	case PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST:
			case PRS_EXTRA_TOPICS_RATINIGS_AVERAGE_POSTS:
				$votable = prs_is_voteround_open($post_id);
				if ($votable && !$admin)
				{
					$dataset[$topic_id]['score'] = 0;
					$dataset[$topic_id]['n'] = 0;
					continue;
				}

				$tmp = isset($arr[$post_id]['score']);
				$tmp = isset($arr[$post_id]['score']) ?
					$arr[$post_id]['score'] : 0;
				if ($tmp == PRS_DEFAULT_SCORE_NULL)
				{
					$set_default &= 1;
				}
				else
				{
					$set_default &= 2;
					$score[$topic_id]['score'] +=  $tmp;
					$score[$topic_id]['n']++;
				}
			break;
		}
	}
	if (!($set_default & 2) && $set_default & 1) // NO 2, 3 YES 1
	{
		$score[$topic_id]['score'] = $config['prs_default_rating'];
	}

	// Set default value if all voting round 

	// Get votes when the voting rounds on all post where open
	$user_id = $user->data['user_id'];
	foreach ($topic_list as $topic_id)
	{
		if ($score[$topic_id]['score'])
		{
			continue;
		}
		switch($config['prs_extra_topic_rating'])
		{
                	case PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST:
				$topic_post_list = array(
					$dataset[$topic_id]['first_post_id'],
				);
			break;

			case PRS_EXTRA_TOPICS_RATINIGS_AVERAGE_POSTS:
				$topic_post_list = $dataset[$topic_id]['post_list'];
			break;
		}
		foreach ($topic_post_list as $post_id)
		{
			$data = $arr[$post_id];
			$tmp = prs_determine_score($user_id, $data, $admin);
			if (!$tmp)
			{
				break;
			}
			$topic_id = $postset[$post_id]['topic_id'];
			$score[$topic_id]['score'] += $tmp;
			$score[$topic_id]['n']++;
		}

/*
		if ($score[$topic_id]['score'])
		{
			continue;
		}

		// set default is score is zero
		foreach ($topic_post_list as $post_id)
		{
			$score[$topic_id]['score'] += PRS_START_SCORE;
			$score[$topic_id]['n']++;
		}
*/
	} 

	// Set default in case
	foreach ($topic_list as $topic_id)
	{
		if (!is_int($topic_id))
		{
//			continue;
		}
                if  (!$score[$topic_id]['score'])
		{
                	$dataset[$topic_id]['score'] = $score[$topic_id]['score'];
			continue;
		}
                $dataset[$topic_id]['score'] = round(
			$score[$topic_id]['score'] / $score[$topic_id]['n']
		);
	}

	// Copy movde topic data to shadow topic
	foreach ($topic_list as $topic_id)
	{
		if (!isset($rowset[$topic_id]))
		{
			continue;
		}
		$tmp = $rowset[$topic_id];
		if (isset($tmp['topic_status'])
			&& $tmp['topic_status'] == ITEM_MOVED)
		{
			$dataset[$topic_id] = $dataset[$tmp['topic_moved_id']];
		}
	}

	return $dataset;
}

function &prs_display_rating_topics(&$row, &$dataset, $admin = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!$config['prs_enabled'])
	{
		return array();
	}

	$topic_id = $row['topic_id'];
	$post_id = $row['topic_first_post_id'];
	$user_id = $user->data['user_id'];
	$data = &$dataset[$topic_id];
	
/*
	$votable = prs_is_voteround_open($post_id);

	// Close voting round for this post
	if (!$votable && $row['prs_score'] == 0)
	{
		prs_close_posts(array($post_id));
	}
*/

	// Determine the score for this post

	$score = $dataset[$topic_id]['score'];
	if ($score == PRS_DEFAULT_SCORE_NULL)
	{
		$score = PRS_DEFAULT_SCORE;
	}

	// Return template variables with the right star images
	$arr = &prs_stars($score);
	$prs_karma = prs_karma($row['topic_last_poster_id']);
	$arr = array_merge($arr, array(
		//'S_PRS_VOTABLE'	=> $votable,
		'PRS_KARMA_SCORE'     => $prs_karma['karma'],
	));
	return $arr;
}

function &prs_fetch_votes($list, &$rowset, $mode = 'posts')
{
	$ret = array();
	switch($mode)
	{
		case 'topic':
		case 'topics':
			$ret = prs_fetch_votes_topics(
				$list, $rowset
			);
		break;
		case 'post':
		case 'posts':
			$ret = prs_fetch_votes_posts(
				$list, $rowset
			);
	}
	return $ret;
}

function &prs_display_rating(&$row, &$dataset, $mode = 'posts', $admin = FALSE)
{
	$ret = array();
	switch($mode)
	{
		case 'topic':
		case 'topics':
			$ret = &prs_display_rating_topics(
				$row, $dataset, $admin
			);
		break;
		case 'post':
		case 'posts':
			$ret = &prs_display_rating_posts(
				$row, $dataset, $admin
			);
		break;
	}
	return $ret;
}

function prs_delete_votes($post_id)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0)
	{
		return;
	}

	$sql = 'DELETE FROM ' . PRS_VOTES_TABLE . '
		WHERE post_id = ' . $post_id;
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);
}
?>
