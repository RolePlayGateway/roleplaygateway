<?php
/** 
*
* @package auto_group
* @version 0.2.1
* @copyright (c) 2007 A_Jelly_Doughnut 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

/** Handles automagic grouping based on post count, time registered, and/or warnings.
* @param arrat $users - user IDs to act on.  default $user->data['user_id']
* @param array $check - keys are fields in the users table, values will be numerical
*/
function auto_group($users = false, $check = false)
{
	global $db, $user, $phpbb_root_path, $phpEx;

	// guests cannot belong to groups
	if (!$user->data['is_registered'])
	{
		return false;
	}

	// if we have a list of user IDs, clean them up
	if (is_array($users))
	{
		$users = array_map('intval', $users);
	}

	if ($users === false)
	{
		$users = array($user->data['user_id']);

		$user_row[$user->data['user_id']] = $user->data;
		$check['posts'][$user->data['user_id']] = (int) $user->data['user_posts'];
		$check['days'][$user->data['user_id']] = (int) max(1, round((time() - $user->data['user_regdate']) / 86400));
		$check['warnings'][$user->data['user_id']] = (int) $user->data['user_warnings'];
	}
	else
	{
		// we need some info from the DB...to avoid possible SQL injection, we select *
		$sql = 'SELECT * FROM ' . USERS_TABLE .
			" WHERE " . $db->sql_in_set('user_id', $users);
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$user_row[$row['user_id']] = $row;
			$check['posts'][$row['user_id']] = (int) $row['user_posts'];
			$check['days'][$row['user_id']] = (int) max(1, round((time() - $row['user_regdate']) / 86400));
			$check['warnings'][$row['user_id']] = (int) $row['user_warnings'];
		}
	}

	// build SQL query...now using AND logic
	$sql_select = $sql_or = '';
	foreach ($check as $column => $value)
	{
		$sql_select .= "group_min_$column, group_max_$column, ";
		$sql_or .= " group_min_$column <> 0 OR group_max_$column <> 0 OR ";
	}

	// trim trailing ,
	$sql_select = substr($sql_select, 0, strrpos($sql_select, ','));
	$sql_or = substr($sql_or, 0, -3);

	$sql = "SELECT group_id, group_auto_default, $sql_select FROM " . GROUPS_TABLE . "
		WHERE group_type <> " . GROUP_SPECIAL . " 
			AND ($sql_or)";
	$result = $db->sql_query($sql, 7200);

	$group_data = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$group_data[$row['group_id']] = $row;
	}

	if (!sizeof($group_data))
	{
		return false;
	}

	// The meat of the MOD...
	// go through groups one at a time
	$remove_groups = $add_groups = $group_make_default = array();
	foreach ($group_data as $group_id => $data)
	{
		$group_make_default[$group_id] = $data['group_auto_default'];

		// To achieve this, we check each column...
		foreach ($check as $column => $value_ary)
		{
			// ... for each user in $users
			foreach ($value_ary as $user_id => $value)
			{
				if ($user_id == ANONYMOUS)
				{
					continue;
				}

				// If there is no limit, skip it.
				if ((int) $data['group_min_' . $column] == 0 && (int) $data['group_max_' . $column] == 0)
				{
					continue;
				}
				/**
				* If values got out of whack, make sure user is properly kicked out of 
				* special status that new groups may assign.
				* Cases where users might get kicked out:
				* - Warnings expire
				* - Post count drops due to post/topic/forum deletion
				*
				* This also handles hitting the maximum "as predicted"
				*/ 
				if (($data['group_min_' . $column] > 0 && $value < $data['group_min_' . $column]) ||
					($data['group_max_' . $column] > 0 && $value > $data['group_max_' . $column]))
				{
					$remove_groups[$group_id][] = $user_id;
				}
				/**
				* We also handle the event that an admin created a group and this user falls
				* between the min_value and max_value for the new group.
				* i.e.: user has 47 posts, and a group for users between 40 and 50 posts is
				* created.  The user is added to that group.
				*/
				else if ((($data['group_min_' . $column] > 0 && $value >= $data['group_min_' . $column]) ||
					($data['group_max_' . $column] > 0 && $value <= $data['group_max_' . $column])) && (!isset($remove_groups[$group_id]) || !in_array($user_id, $remove_groups[$group_id])))
				{
					$add_groups[$group_id][] = $user_id;
				}
			}
		}
	}
	$db->sql_freeresult($result);

	if (sizeof($remove_groups))
	{
		if (!function_exists('group_user_del'))
		{
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		}

		// because phpBB doesn't check if users are actually members of the group
		// before removing them, we check it before calling group_user_del()
		$user_ids = array();
		foreach ($remove_groups as $group_id => $user_id_ary)
		{
			if (is_array($user_id_ary) && sizeof($user_id_ary))
			{
				$user_ids = array_merge($user_ids, $user_id_ary);
			}
		}
           
		$sql = 'SELECT user_id, group_id FROM ' . USER_GROUP_TABLE . '
			WHERE ' . $db->sql_in_set('group_id', array_keys($remove_groups)) . '
			AND ' . $db->sql_in_set('user_id', $user_ids);
		$result = $db->sql_query($sql);

		// we replace $remove_groups with the results from this SQL query...
		// It returns people who need to be removed _and_ are members of the group
		$remove_groups = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$remove_groups[$row['group_id']][] = $row['user_id'];
		}

		foreach ($remove_groups as $group_id => $user_id_ary)
		{
			if (!is_array($user_id_ary))
			{
				$user_id_ary = array($user_id_ary);
			}

			group_user_del($group_id, $user_id_ary, false, false, true);
		}
	}

	if (sizeof($add_groups))
	{
		if (!function_exists('group_user_add'))
		{
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		}

		foreach ($add_groups as $group_id => $user_id_ary)
		{
			if (!is_array($user_id_ary))
			{
				$user_id_ary = array($user_id_ary);
			}
			$user_id_ary = array_unique($user_id_ary);

			// if there are users to be both added and removed from this group,
			// handle that.  Remove overrides add for obvious reasons.
			if (isset($remove_groups[$group_id]))
			{
				// we go thru and unset keys that exist in both arrays
				$intersection = array_intersect($add_groups[$group_id], $remove_groups[$group_id]);

				if (sizeof($intersection))
				{
					foreach ($intersection as $key => $void)
					{
						unset($user_id_ary[$group_id][$key]);
					}
				}
				unset($intersecton);
			}

			// If admin requests it, user can get new default group by this method.
			// Useful primarily for colouring.
			group_user_add($group_id, $user_id_ary, false, false, $group_make_default[$group_id], 0, 0, false, true);
		}
	}

	return false;
}

/**
 * Ask Mr. Database for all users who meet the critera of the group being created
 * @param array $group_id - $group_id used in the group_create() function
 * @param arry $group_attributes - Passed in from group_create()
 * @param bool &$make_default - Found by this SQL query, pass-by-ref
 * @return array $auto_add_users - users who should be added to this group.
 */
function auto_groups_create($group_id, $group_attributes, &$make_default)
{
	global $db;

	if (empty($group_attributes))
	{
		$sql = 'SELECT * FROM ' . GROUPS_TABLE . ' 
			WHERE group_id = ' . (int) $group_id;
		$result = $db->sql_query($sql);

		$group_attributes = $db->sql_fetchrow($result);
	}

	$make_default = $group_attributes['group_auto_default'];


	$where_sql = $auto_add_users = array();
	// auto groups mod added
	if (!empty($group_attributes['group_min_posts']) || !empty($group_attributes['group_max_posts']) || !empty($group_attributes['group_min_days']) ||
		!empty($group_attributes['group_max_days']) || !empty($group_attributes['group_max_days']) || !empty($group_attributes['group_min_warnings']) || !empty($group_attributes['group_max_warnings']))
	{
		$field_ary = array(
			'posts'		=> 'user_posts',
			'days'		=> 'user_regdate',
			'warnings'	=> 'user_warnings',);

		// we find users who meet requirements for this group by building 
		// an sql query
		$extremes = array('min' => '>=', 'max' => '<');
		foreach ($field_ary as $mode => $field)
		{
			foreach ($extremes as $end => $sign)
			{
				// building this 'where' clause isn't easy...
				if (isset($group_attributes['group_' . $end . '_' . $mode]) && $group_attributes['group_' . $end . '_' . $mode])
				{
					if ($mode == 'days')
					{
						/**
						* this isn't stored in the same format we present the user
						* user_regdate is seconds, 'group_(min/max)' is in days
						**/
						$time_required = (time() - ($group_attributes['group_' . $end . '_days'] * 86400));
						// because we're dealing with timestamps, our min/max is wrong
						$sign = ($sign == '>=') ? '<' : '>=';
						$where_sql[] = "$field $sign $time_required";
					}
					else
					{
						$where_sql[] = "$field $sign " . $group_attributes['group_' . $end . '_' . $mode];
					}
				}
			}
		}

		$sql = 'SELECT user_id FROM ' . USERS_TABLE . '
			WHERE ' . $db->sql_in_set('user_type', array(USER_NORMAL, USER_FOUNDER)) . "
				AND " . implode(' AND ', $where_sql);
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$auto_add_users[] = $row['user_id'];
		}
		$db->sql_freeresult($result);

		// remove users who do not belong in this group and were added by 
		// the auto groups MOD
		// This completes the sync
		$sql = 'DELETE FROM ' . USER_GROUP_TABLE . '
			WHERE group_id = ' . (int) $group_id . '
				AND auto_group = 1
				AND ' . $db->sql_in_set('user_id', $auto_add_users, true, true);
		$db->sql_query($sql);

		return $auto_add_users;
	}
}

?>