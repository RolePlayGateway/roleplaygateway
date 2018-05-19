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

if (!isset($config['prs_lock']))
{
	set_config('prs_lock', '0', true);
}

function prs_is_locked()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	// if the other process is running more than an hour already
	// we have to assume it aborted without cleaning the lock
	if (!isset($config['prs_lock']) ||
		$config['prs_lock'] + 3600 >= time())
	{
		set_config('prs_lock', '0', true);
	}
	return $config['prs_lock'];
}

function prs_lock()
{
	set_config('prs_lock', time(), true);

}

function prs_unlock()
{
	set_config('prs_lock', '0', true);
}

function &prs_get_post_list_time($min = 0, $max = 0)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
	
	$arr = array();
	if ($max == 0 || $max < $min)
	{
		return $arr;
	}
	$sql = 'SELECT post_id
		FROM ' . POSTS_TABLE . '
		WHERE post_time >= ' . $min . '
			AND post_time < ' .$max;
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$post_id = $row['post_id'];
		$arr[$post_id] = $post_id;
	}
	$db->sql_freeresult($result);
	return $arr;
}

function &prs_get_first_post_in_post_list($post_list)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$first_post_list = array();
	$sql = 'SELECT topic_first_post_id
		FROM ' . TOPICS_TABLE . '
		WHERE ' . $db->sql_in_set('topic_first_post_id', $post_list);
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$first_post_list[$row['topic_first_post_id']] = 1;
	}
	$db->sql_freeresult($result);
	return $first_post_list;
}

function &prs_get_select_post($post_id)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id < 0)
	{
		return -1;
	}

	$sql = 'SELECT *
		FROM ' . POSTS_TABLE . '
		WHERE post_id = ' . $post_id;
	$result = $db->sql_query_limit($sql, 1);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	return $row;
}

function prs_sql_multiselect($prefixes, $columns)
{
	$sql = '';
	$bind = '';

	if ($prefixes == NULL || $columns == NULL)
	{
		return '';
	}
	if (!is_array($prefixes))
	{
		return prs_sql_multiselect(array($prefixes), $columns);
	}
	if (!is_array($columns))
	{
		return prs_sql_multiselect($prefixes, array($columns));
	}

	foreach ($prefixes as $prefix)
	{
		foreach ($columns as $column)
		{
			$sql .= $bind . $prefix . '.' . $column . '
				AS ' . $prefix . '_' . $column;
			$bind = ', ';
		}
	}
	return $sql;
}

/*
function prs_sql_update_post($post_id, $data)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = 'UPDATE ' . POSTS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data);

}
*/

function prs_is_votable($post_id, $score = 3, $trigger = FALSE)
// returns FALSE if the post can not be voted on
// returns TRUE if the post can be voted on
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0 || $score <= 0)
	{
		return FALSE;
	}

	if (prs_is_votable_basis($post_id, $score, $trigger) == FALSE)
	{
	
		// OVERRIDE, DO NOT END VOTING!
		return true;
		// END OVERRIDE
	
		return FALSE;
	}

	if ($config['prs_modpoints_enabled'] && 
		prs_is_votable_modpoints($post_id, $trigger) == FALSE)
	{
		return FALSE;
	}

	if ($config['prs_penalty_enabled'] &&
		prs_is_votable_penalty($post_id, $trigger) == FALSE)
	{
		return FALSE;
	}
	
	if (prs_is_voteround_open($post_id, NULL, $score, $trigger) != FALSE)
	{
		//return prs_is_voteround_open($post_id, NULL, $score, $trigger);
		return true;
	} else {
	
	
		// OVERRIDE, DO NOT END VOTING!
		return true;
		// END OVERRIDE
	
	
		return false;
	}
		
}

function prs_is_voteround_open($post_id, $row = NULL, $score = 3, $trigger = FALSE)
{
	if ($post_id <= 0 || $score <= 0)
	{
		return FALSE;
	}

	return prs_is_voteround_open_basis($post_id, $row, $score, $trigger);
}

function &prs_stars($data, $base = 'prs_star_s_', $n = 2)
{

	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!is_array($data))
	{
		return prs_stars(array('score' => $data, 'n' => 0), $base, $n);
	}

	$arr = array();
	for ($i = 0; $i < PRS_MAX_NUMBER_STARS; $i++)
	{
		$key = 'PRS_STAR'.($i+1);
		$arr[$key] = $base;
		if ($data['score'] <=  $i * PRS_MULTIPLIER_SCORE)
		{
			$arr[$key] .= 0;
		}
		elseif ($data['score'] >=  ($i + 1) * PRS_MULTIPLIER_SCORE)
		{
			$arr[$key] .= 10;
		}
		else
		{
			$arr[$key] .= $data['score'] % PRS_MULTIPLIER_SCORE;
		}
	}
	

	
	$stars = '';
	foreach ($arr as $item)
	{
		$stars .= $user->img($item);
	}
	foreach ($arr as $key => $value)
	{
		$arr[$key.'_IMG'] = $user->img($value, $key.'_EXPLAIN');
	}
	$arr = array_merge($arr, array(
		'PRS_STARS'		=> $stars,
		'PRS_BASE'		=> substr($base, 0,
							strlen($base) - $n),
		'PRS_SCORE'		=> $data['score'] / PRS_MULTIPLIER_SCORE,
		'PRS_N'		=> $data['n'],
	));

	return $arr;
}

function &prs_users_who_voted_n_time($n)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($n < 0)
	{
		$n = 0;
	}

	$users = array();
	$sql = 'SELECT user_id
		FROM ' . PRS_VOTES_TABLE . '
		GROUP BY user_id
		HAVING COUNT(*) >= ' . $n;
	$result = $db->sql_query($sql);

	while($row = $db->sql_fetchrow($result))
	{
		$users[] = $row['user_id'];
	}
	$db->sql_freeresult($result);
	return $users;
}

function prs_get_post_from_topic($topic_list, $all = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
	 
	$sql = $all ? 'SELECT post_id FROM ' . POSTS_TABLE
		: 'SELECT topic_first_post_id FROM ' . TOPICS_TABLE;
	$sql .= ' WHERE ' . $db->sql_in_set('topic_id', $topic_list);
	$result = $db->sql_query($sql);
	$post_list = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$post_list[] = $all ? $row['post_id']
			: $row['topic_first_post_id'];
	}
	$db->sql_freeresult($result);
	return $post_list;
}

function get_default_rating()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	return 30 / PRS_MULTIPLIER_SCORE;
}

?>
