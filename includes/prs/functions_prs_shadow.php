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

function prs_update_votes_chi_table($users = NULL)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($users == NULL)
	{
		$n = $config['prs_shadow_min_votes'];
		$users = prs_users_who_voted_n_time($n);
	}
	$t = $config['prs_shadow_refresh_chi'];
	if (isset($config['prs_chi_time']) &&
		  $config['prs_chi_time'] > time() - $t / 4) // 15min
	{
		return;
	}
	$time = time();
	set_config('prs_chi_time', $time);

	// remove values older then 90 days
	$sql = 'DELETE FROM ' . PRS_VOTES_CHI_TABLE . '
		WHERE time = ' . (time() - 2160 * $t); // 90d
	$db->sql_query($sql);

	// get data
	$sql_array = NULL;
	if ($config['prs_extra_first_post_only'])
	{
		$sql_array = array(
			'FROM'	=> TOPICS_TABLE . ' AS t',
			'WHERE' => 'v.post_id = t.topic_first_post_id',
		);
	}
	$dataset =& prs_get_votes_dataset_simple(
		array($post_id), array($user_id), $sql_arr
	);
	foreach ($votes_dataset as $post_id => $votes_row)
	{
		if (!is_int($post_id))
		{
			unset($dataset[$post_id]);
			continue;
		}
		foreach ($votes_row as $user_id => $votes_data)
		{
			if (!is_int($user_id))
			{
				unset($dataset[$post_id][$user_id]);
				continue;
			}
		}
	}

	// check if user $i and user $j matches
	$n = sizeof($users);
	for ($i = 0; $i < $n; $i++)
	{
		for ($j = 0; $j < $n; $j++)
		{
			// no need to check a user against him self
			if ($i == $j)
			{
				continue;
			}

			// check if result is already in the database
			$sql = 'SELECT time, num
				FROM ' . PRS_VOTES_CHI_TABLE . '
				WHERE user1_id = ' . $users[$i] . '
				  AND user2_id = ' . $users[$j] . '
				ORDER BY time DESC';
			$result = $db->sql_query($sql);
			$do_update = FALSE;
			if ($row = $db->sql_fetchrow($result))
			{
				if ($time - 720 * $t < $row['time']) // 30d
				{
					continue;
				}
				else if ($time - 336 * $t < $row['time']) // 14d
				{
					if ($row['num'] < 50)
					{
						continue;
					}
				}
				else if ($time -  168 * $t < $row['time']) // 7d
				{
					if ($row['num'] < 20)
					{
						continue;
					}
				}
				else if ($time -  24 * $t < $row['time']) // 1d
				{
					if ($row['num'] < 10)
					{
						continue;
					}
				}
				else if ($time - $t < $row['time']) // 1h
				{
					if ($row['num'] < 5)
					{
						continue;
					}
				}
				$do_update = TRUE;
			}

			// calculate average difference
			$chi = $diff = $k = 0;
			$list = array();
			foreach($dataset as $data)
			{
				if (isset($data[$users[$i]]) &&
				    isset($data[$users[$j]]))
				{
					$list[] = $data;
					$a = $data[$users[$i]]['score'];
					$b = $data[$users[$j]]['score'];
					$diff += $a - $b;
					$k++;
					// 100 is the maximum for prs_stat_chi
					if ($k == 100)
					{
						break;
					}
				}
			}
			if ($k < 2) // if there are less then 5 
			{
				continue;
			}
			$diff /= $k;

			// caclulate chi value
			foreach($list as $data)
			{
				$a = $data[$users[$i]]['score'];
				$b = $data[$users[$j]]['score'];
				$chi += pow($b - $a + $diff, 2) / $a; 
			}

			// Values of 1 or higher indicates user do not match
			$v = $config['prs_shadow_chi_chance'];
			$tmp = prs_stat_chi($v, $k - 1);
			if ($tmp != 0)
			{
				$chi /= $tmp;
			}
			else if ($chi > 0)
			{
				$chi = 5;
			}
			else
			{
				$chi = 0;
			}

			// store result in the database
			$diff = (int) PRS_MULTIPLIER_DIFF * $diff;
			$chi = ($chi > PRS_MAX_CHI / PRS_MULTIPLIER_CHI)
			     ? PRS_MAX_CHI
			     : PRS_MULTIPLIER_CHI * $chi;
			$sql_data = array();
			$sql_data[PRS_VOTES_CHI_TABLE]['sql'] = array(
				'user1_id'	=> $users[$i],
				'user2_id'	=> $users[$j],
				'time'		=> $time,
				'chi'		=> $chi,
				'diff'		=> $diff,
				'num'		=> $k,
			);
			if ($do_update)
			{
				$sql = 'UPDATE ' . PRS_VOTES_CHI_TABLE . ' SET time = ' . $time . ',  chi = ' . $chi . ', num = ' . $k . ' WHERE user1_id = ' . $users[$i] . ' AND user2_id = ' . $users[$j];
			}
			else
			{
				$sql = 'INSERT INTO ' . PRS_VOTES_CHI_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[PRS_VOTES_CHI_TABLE]['sql']);
			}
			$db->sql_query($sql);
		}
	}
}

function prs_declair_post_shadowed($post_id)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0)
	{
		return;
	}

	$sql = 'UPDATE ' . POSTS_TABLE . '
		SET prs_shadowed = 1
		WHERE post_id = ' . $post_id;
	$db->sql_query($sql);

	// lets make sure the score is updated
	prs_close_posts(array($post_id));
}

function prs_create_shadow_votes()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if (!$config['prs_penalty_enabled'])
	{
		return;
	}

	if (prs_is_locked())
	{
		return;
	}
	prs_lock();

	$enabled = $config['prs_enabled'] &&
		   $config['prs_shadow_enabled'];

	if (!$config['prs_shadow_votes_enabled'])
	{
		return;
	}

	$n = $config['prs_shadow_min_votes'];
	$users = prs_users_who_voted_n_time($n);
	prs_update_votes_chi_table($users);
	$closing_time = time() - $config['prs_votes_period'];

	// get a list of posts where we have not look jet for shadow votes
        $sql = 'SELECT post_id
		FROM ' . POSTS_TABLE . '
		WHERE prs_score > 0
		  AND prs_shadowed = 0';
	$result = $db->sql_query($sql);
/*
	$post_list = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$post_list[] = $row['post_id'];
	}
	$votes_dataset =& prs_get_votes_dataset_simple($post_list);
	foreach ($votes_dataset as $post_id => $votes_row)
	{
		if (!is_int($post_id))
		{
			continue;
		}
		$sql_data = array();

		// split the voters from the non-voters
		$select = $voters = array();
		foreach ($votes_row as $user_id => $votes_data)
		{
			if (!is_int($user_id))
			{
				continue;
			}
			$select[$user_id] = array(
				'post_id'	=> $post_id,
				'score'		=> $votes_data['score'],
			);
			$voters[$user_id] = $user_id;
		}

*/
	while ($row = $db->asql_fetchrow($result))
	{
		$post_id = $row['post_id'];
		$sql_data = array();

		// split the voters from the non-voters
		$where_clause = 'post_id = ' . $row['post_id'];
		$sql = 'SELECT user_id, post_id, score
			FROM ' . PRS_VOTES_TABLE . '
			WHERE post_id = ' . $post_id;
		$result = $db->sql_query($sql);
		$select = $voters = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$select[$row['user_id']] = array(
				'post_id'	=> $row['post_id'],
				'score'		=> $row['score'],
			);
			$voters[$row['user_id']] = $row['user_id'];
		}
		$db->sql_freeresult($result2);
// replace code until here
		if (!sizeof($voters))
		{
			prs_declair_post_shadowed($post_id);
			continue;
		}
		$nonvoters = array_diff($users, $voters);
		if (!sizeof($nonvoters))
		{
			prs_declair_post_shadowed($post_id);
	 		continue;
		}

		// select one users vote to clone
/*
		$sql = 'SELECT user1_id, user2_id, diff, chi
			FROM ' . PRS_VOTES_CHI_TABLE . '
			WHERE (';
		$where_or = '';
		foreach ($nonvoters as $item)
		{
			$sql .= $where_or . ' user1_id = ' .$item;
			$where_or = ' OR ';
		}
		$sql .= ') AND (';
		$where_or = '';
		foreach ($voters as $item)
		{
			$sql .= $where_or . ' user2_id = ' .$item;
			$where_or = ' OR ';
		}
		$sql .= ') ORDER BY chi ASC';
*/
		$sql = 'SELECT user1_id, user2_id, diff, chi
			FROM ' . PRS_VOTES_CHI_TABLE . '
			WHERE ' . $db->sql_in_set('user1_id', $nonvoters) . '
				AND ' . $db->sql_in_set('user2_id', $voters) . '
			ORDER BY chi ASC';
		$result2 = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result2);
		if (!isset($row['chi']) || $row['chi'] > PRS_MULTIPLIER_CHI)
		{
			continue;
		}
		$diff = $row['diff'] / PRS_MULTIPLIER_DIFF;
		$db->sql_freeresult($result2);

		// insert shadow votes
		$sql_data[PRS_VOTES_TABLE]['sql'] = array(
			'user_id'	=> $row['user1_id'],
			'post_id'	=> $post_id,
			'score'		=> round($select[$row['user2_id']]['score'] - $diff),
			'time'		=> $closing_time,
			'shadow'	=> 1,
		);
		$sql = 'INSERT INTO ' . PRS_VOTES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[PRS_VOTES_TABLE]['sql']);
		$db->sql_query($sql);

		// declair this post shadowed
		prs_declair_post_shadowed($post_id);
	}
	$db->sql_freeresult($result);
	prs_unlock();
}
?>
