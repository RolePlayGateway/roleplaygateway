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

function prs_is_votable_penalty($post_id, $trigger = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0)
	{
		return FALSE;
	}

	// User may not rate based on prs_penalty
	if ($config['prs_penalty_enabled'])
	{
		// overall
		if (prs_penalty($user->data['user_id']) > $config['prs_penalty_user_overall'] / PRS_MULTIPLIER_PERCENT)
		{
			if ($trigger)
			{
				trigger_error('PENALTY_VOTES_DISABLED_OVERALL');
			}  
			else
			{
				return FALSE;
			}
		}

		// per user
		if (prs_penalty($user->data['user_id'], $post_id) > $config['prs_penalty_user_poster'] / PRS_MULTIPLIER_PERCENT)
		{
			if ($trigger)
			{
				trigger_error('PENALTY_VOTES_DISABLED_POSTER');
			}  
			else
			{
				return FALSE;
			}
		}
	}
	return TRUE;
}

function prs_penalty($user_id, $poster_id = 0)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($user_id <= 0)
	{
		return 0;
	}

	// overall
	$sql = 'SELECT penalty
		FROM ' . PRS_PENALTY_TABLE . '
		WHERE user_id = ' . $user_id . '
		  AND poster_id = ' . $poster_id;
	$result = $db->sql_query_limit($sql, 1);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	return $row['prs_penalty'];
}

function prs_determine_penalties()
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

	$border = prs_stat_normal_reverse((float) $config['prs_penalty_border'] / PRS_MULTIPLIER_PERMILL_DOUBLE);

	// find all closed posts
	$sql = 'SELECT post_id, prs_standard_diviation
		FROM ' . POSTS_TABLE .' 
		WHERE prs_score > 0
			AND prs_penaltized = 0';
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$post_id = $row['post_id'];
		$o = $row['prs_standard_diviation'] /
			PRS_MULTIPLIER_PERMILL;

		$sql = 'SELECT user_id
			FROM ' . PRS_VOTES_TABLE . '
			WHERE post_id = ' . $post_id;
		$result2 = $db->sql_query($sql);
		while ($row2 = $db->sql_fetchrow($result2))
		{
			$user_id = $row2['user_id'];
			$z = $row2['standard_diviation'];
			if ($z < $border)	{ continue; }
			
			// how many votes did this user cast?
			$sql = 'SELECT COUNT(*) AS count
				FROM ' . PRS_VOTES_TABLE . '
				WHERE user_id = ' . $user_id . '
				  AND post_id != ' . $post_id;
			$result3 = $db->sql_query($sql);
			$row3 = $db->sql_fetchrow($result3);
			$prs_penalty_overall = $row3['count'];

			// has he made the minimal amount of votes?
			if ($prs_penalty_overall < $config['prs_penalty_minimum_votes'])
			{
				$prs_penalty_overall = $config['prs_penalty_minimum_votes'];
			}

			// how many of there had a to high z-value?
			$sql = 'SELECT COUNT(*) AS count
				FROM ' . PRS_VOTES_TABLE . '
				WHERE user_id = ' . $user_id . '
				  AND post_id != ' . $post_id . '
				  AND standard_diviation >= ' . $border;
			$result3 = $db->sql_query_limit($sql, 1);
			$row3 = $db->sql_fetchrow($result3);
			$prs_penalty_overall = $row3['count'] / $penalty_overall;

			// who is the poster?
			$sql = 'SELECT poster_id
				FROM ' . POSTS_TABLE . ' 
				WHERE post_id = ' . $post_id;
			$result3 = $db->sql_query_limit($sql, 1);
			$row3 = $db->sql_fetchrow($result3);
			$poster_id = $row3['poster_id'];

			// how many votes did this user cast on the poster?
			$sql = 'SELECT COUNT(*) AS count
				FROM ' . PRS_VOTES_TABLE . ' AS v,
					' . POSTS_TABLE . ' AS p
				WHERE v.user_id = ' . $user_id . '
				  AND v.post_id != ' . $post_id . '
				  AND p.poster_id = ' . $poster_id . '
				  AND v.post_id = p.post_id'; 
			$result3 = $db->sql_query_limit($sql, 1);
			$row3 = $db->sql_fetchrow($result3);
			$prs_penalty_per_poster = $row3['count'];

			// has he made the minimal amount of votes?
			if ($prs_penalty_per_poster < $config['prs_penalty_minimum_votes'])
			{
				$prs_penalty_per_poster = $config['prs_penalty_minimum_votes'];
			}

			// how many of there had a to high z-value?
			$sql = 'SELECT COUNT(*) AS count
				FROM ' . PRS_VOTES_TABLE . ' AS v,
					' . POSTS_TABLE . ' AS p
				WHERE v.user_id = ' . $user_id . '
				  AND v.post_id != ' . $post_id . '
				  AND p.poster_id = ' . $poster_id . '
				  AND v.standard_diviation >= ' .$border. '
				  AND v.post_id = p.post_id';
			$result3 = $db->sql_query_limit($sql, 1);
			$row3 = $db->sql_fetchrow($result3);
			$prs_penalty_per_poster = $row3['count'] / $penalty_per_poster;

			// find out if a update or insert is required
			$sql = 'SELECT count(*) AS count
				FROM ' . PRS_PENALTY_TABLE . '
				WHERE user_id = ' . $user_id . ' 
				  AND poster_id = ' . $poster_id;
			$result3 = $db->sql_query_limit($sql, 1);
			$row3 = $db->sql_fetchrow($result3);
			$do_update_overall = $row3['count'];

			$sql = 'SELECT count(*) AS count
				FROM ' . PRS_PENALTY_TABLE . '
				WHERE user_id = ' . $user_id . ' 
				  AND poster_id = 0';
			$result3 = $db->sql_query_limit($sql, 1);
			$row3 = $db->sql_fetchrow($result3);
			$do_update_per_poster = $row3['count'];

			// make the values sutable for the database
			$prs_penalty_overall = round($penalty_overall *
				PRS_MULTIPLIER_PERCENT);
			$prs_penalty_per_poster = round($penalty_per_poster *
				PRS_MULTIPLIER_PERCENT);

			// store the results
			if ($do_update_overall)
			{
				$sql = 'UPDATE ' . PRS_PENALTY_TABLE . '
					SET prs_penalty = ' . $penalty_overall . '
					WHERE user_id = ' . $user_id . '
					  AND poster_id = ' . $poster_id;
			}
			else
			{
				$sql = 'INSERT INTO ' . PRS_PENALTY_TABLE . ' (user_id, poster_id, prs_penalty)
					VALUES
					(' . $user_id . ',
						' . $poster_id . ',
						' . $prs_penalty_overall . ')';

			}
			if ($do_update_per_poster)
			{
				$sql = 'UPDATE ' . PRS_PENALTY_TABLE . '
					SET prs_penalty = ' . $penalty_per_user . '
					WHERE user_id = ' . $user_id . '
					  AND poster_id = 0';
			}
			else
			{
				$sql = 'INSERT INTO ' .PRS_PENALTY_TABLE . ' (user_id, poster_id, prs_penalty)
					VALUES
					(0' . $poster_id . ',
						' . $prs_penalty_per_user . ')';

			}
		}
		$db->sql_freeresult($result2);
	}
	$db->sql_freeresult($result);
	prs_unlock();
}
?>
