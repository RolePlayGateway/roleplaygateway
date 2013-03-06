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

function prs_modpoints($user_id)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($user_id <= 0)
	{
		return 0;
	}

	$points = 0;
	$sql = 'SELECT points, time
		FROM ' . PRS_MODPOINTS_TABLE . '
		WHERE user_id = ' . $user_id . '
		ORDER BY time';
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$points += $row['points'];
	}
	$db->sql_freeresult($result);
	return $points;
}

function prs_is_votable_modpoints($post_id, $trigger = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0)
	{
		return FALSE;
	}

	if (!$config['prs_modpoints_enabled'])
	{
		return TRUE;
	}

	// has the user already voted?
	 $sql = 'SELECT user_id
		FROM ' . PRS_VOTES_TABLE . '
		WHERE post_id = ' . $post_id . '
			AND user_id = ' . $user->data['user_id'];
	$result = $db->sql_query_limit($sql, 1);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	if ($row)
	{
		return TRUE;
	}

	// User may not rate because of lacking prs_modpoints
	if ( !prs_modpoints($user->data['user_id']))
	{
		if ($trigger)
		{
			trigger_error('PRS_VOTES_DISABLED_MODPOINTS');
		}
		else
		{
			return FALSE;
		}
	}
	return TRUE;
}

function prs_reduce_modpoints($user_id, $number = 1)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($user_id <= 0 || $number <= 0)
	{
		return;
	}

	$sql = 'SELECT points, time
		FROM ' . PRS_MODPOINTS_TABLE . '
		WHERE user_id = ' . $user_id . '
		ORDER BY time';
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$points = $row['points'];
		if ($points > 0)
		{
			$time = $row['time'];

			$sql = '';
			if ($points > $number)
			{
				$points -= $number;
				$number = 0;
				$sql = 'UPDATE ' . PRS_MODPOINTS_TABLE . '
					SET points = ' . $points;
			}
			else
			{
				$number -= $points;
				$sql = 'DELETE FROM ' . PRS_MODPOINTS_TABLE;
			}

			$sql .= ' WHERE user_id = ' . $user_id . '
				   AND time = ' . $time;
			$db->sql_freeresult($db->sql_query($sql));

			if (!$number)
			{
				break;
			}
		}
	}
	$db->sql_freeresult($result);
}

function prs_reduce_modpoints_deleted_post($post_id)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($post_id <= 0)
	{
		return;
	}

	$sql = 'DELETE FROM ' . PRS_MODPOINTS_TABLE . '
		WHERE post_id = ' . $post_id;
	return;
}

function get_base_modpoints()
{
	$base =  get_default_rating();
	if ($base < 1)
	{
		return 1;
	}
	return ($base < 5) ? $base : 5;
}

function prs_increase_modpoints($user_id, $post_id, $time = 0, $factor = 1)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($user_id <= 0 || $post_id <= 0 || $factor <= 0 ||
		!$config['prs_modpoints_enabled'])
	{
		return;
	}
	if ($time <= 0)
	{
		$time = time();
	}

	$karma = prs_karma($user_id);
	$factor *= ($config['prs_modpoints_karma'])
		? $karma['karma'] / get_default_rating()
		: 1;
	$points = round($factor * $config['prs_modpoints_newpost']);
	if ($points > PRS_MAX_MODPOINTS_PER_VOTE)
	{
		$points = PRS_MAX_MODPOINTS_PER_VOTE;
	}
	$sql = 'INSERT INTO ' . PRS_MODPOINTS_TABLE . '
		(user_id, post_id, time, points) VALUES
		(' . $user_id . ', ' . $post_id . ', ' .  $time . ', ' .
			$points . ')';
	$db->sql_freeresult($db->sql_query($sql));
}

function prs_clean_modpoints()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = 'DELETE FROM ' . PRS_MODPOINTS_TABLE . '
		WHERE time < ' . (time() - $config['prs_modpoints_period']);
	$db->sql_freeresult($db->sql_query($sql));
}
?>
