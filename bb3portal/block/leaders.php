<?php
/*
*
* @name leaders.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: leaders.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
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

// Display a listing of board admins, moderators
$user->add_lang('groups');

$user_ary = $auth->acl_get_list(false, array('a_', 'm_'), false);

$admin_id_ary = $mod_id_ary = $forum_id_ary = array();
foreach ($user_ary as $forum_id => $forum_ary)
{
	foreach ($forum_ary as $auth_option => $id_ary)
	{
		if (!$forum_id && $auth_option == 'a_')
		{
			$admin_id_ary = array_merge($admin_id_ary, $id_ary);
			continue;
		}
		else
		{
			$mod_id_ary = array_merge($mod_id_ary, $id_ary);
		}

		if ($forum_id)
		{
			foreach ($id_ary as $id)
			{
				$forum_id_ary[$id][] = $forum_id;
			}
		}
	}
}

$admin_id_ary = array_unique($admin_id_ary);
$mod_id_ary = array_unique($mod_id_ary);

// Admin group id...
$sql = 'SELECT group_id
	FROM ' . GROUPS_TABLE . "
	WHERE group_name = 'ADMINISTRATORS'";
$result = $db->sql_query($sql);
$admin_group_id = (int) $db->sql_fetchfield('group_id');
$db->sql_freeresult($result);

$sql = 'SELECT forum_id, forum_name 
	FROM ' . FORUMS_TABLE . '
	WHERE forum_type = ' . FORUM_POST;
$result = $db->sql_query($sql);

$forums = array();
while ($row = $db->sql_fetchrow($result))
{
	$forums[$row['forum_id']] = $row['forum_name'];
}
$db->sql_freeresult($result);

$sql = $db->sql_build_query('SELECT', array(
	'SELECT'	=> 'u.user_id, u.group_id as default_group, u.username, u.user_colour, u.user_allow_pm, g.group_id, g.group_name, g.group_colour, g.group_type, ug.user_id as ug_user_id',

	'FROM'		=> array(
		USERS_TABLE		=> 'u',
		GROUPS_TABLE	=> 'g'
	),

	'LEFT_JOIN'	=> array(
		array(
			'FROM'	=> array(USER_GROUP_TABLE => 'ug'),
			'ON'	=> 'ug.group_id = g.group_id AND ug.user_pending = 0 AND ug.user_id = ' . $user->data['user_id']
		)
	),

	'WHERE'		=> $db->sql_in_set('u.user_id', array_unique(array_merge($admin_id_ary, $mod_id_ary))) . '
		AND u.group_id = g.group_id',

	'ORDER_BY'	=> 'g.group_name ASC, u.username_clean ASC'
));

$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result))
{
	$which_row = (in_array($row['user_id'], $admin_id_ary)) ? 'admin' : 'mod';

	// We sort out admins not having the admin group as default
	// The drawback is that only those admins are displayed which are within
	// the special group 'Administrators' and also having it assigned as their default group.
	// - might change
	if ($which_row == 'admin' && $row['default_group'] != $admin_group_id)
	{
		// Remove from admin_id_ary, because the user may be a mod instead
		unset($admin_id_ary[array_search($row['user_id'], $admin_id_ary)]);

		if (!in_array($row['user_id'], $mod_id_ary))
		{
			continue;
		}
		else
		{
			$which_row = 'mod';
		}
	}



	if ($row['group_type'] == GROUP_HIDDEN && !$auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel') && $row['ug_user_id'] != $user->data['user_id'])
	{
		$group_name = $user->lang['GROUP_UNDISCLOSED'];
		$u_group = '';
	}
	else
	{
		$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];
		$u_group = append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']);
	}

	$template->assign_block_vars($which_row, array(
		'USER_ID'		=> $row['user_id'],
		'GROUP_NAME'	=> $group_name,
		'GROUP_COLOR'	=> $row['group_colour'],

		'U_GROUP'			=> $u_group,

		'USERNAME_FULL'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'USERNAME'			=> get_username_string('username', $row['user_id'], $row['username'], $row['user_colour']),
		'USER_COLOR'		=> get_username_string('colour', $row['user_id'], $row['username'], $row['user_colour']),
		'U_VIEW_PROFILE'	=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
	));
}
$db->sql_freeresult($result);


/*
// Generate online information for user
if ($config['load_onlinetrack'] && sizeof($id_cache))
{
	$sql = 'SELECT session_user_id, MAX(session_time) as online_time, MIN(session_viewonline) AS viewonline
		FROM ' . SESSIONS_TABLE . '
		WHERE ' . $db->sql_in_set('session_user_id', $id_cache) . '
		GROUP BY session_user_id';
	$result = $db->sql_query($sql);

	$update_time = $config['load_online_time'] * 60;
	while ($row = $db->sql_fetchrow($result))
	{
		$user_cache[$row['session_user_id']]['online'] = (time() - $update_time < $row['online_time'] && (($row['viewonline'] && $user_cache[$row['session_user_id']]['viewonline']) || $auth->acl_get('u_viewonline'))) ? true : false;
	}
	$db->sql_freeresult($result);
}
unset($id_cache);

// Assign specific vars
$template->assign_vars(array(
	'ONLINE_IMG'			=> ($poster_id == ANONYMOUS || !$config['load_onlinetrack']) ? '' : (($user_cache[$poster_id]['online']) ? $user->img('icon_user_online', 'ONLINE') : $user->img('icon_user_offline', 'OFFLINE')),
	'S_ONLINE'				=> ($poster_id == ANONYMOUS || !$config['load_onlinetrack']) ? false : (($user_cache[$poster_id]['online']) ? true : false),

	)
);
*/

?>