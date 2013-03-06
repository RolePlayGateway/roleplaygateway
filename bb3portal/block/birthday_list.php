<?php
/*
*
* @name birthday_list.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: birthday_list.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
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

//
// birthday list borrowed from index.php (phpBB-3.0.B3) 
// if this gets changed (in index.php) and I don't notice it, please tell me)
//

// Generate birthday list if required ...
$birthday_list = '';
if ($config['load_birthdays'])
{
	$now = getdate(time() + $user->timezone + $user->dst - (date('H', time()) - gmdate('H', time())) * 3600);
	$sql = 'SELECT user_id, username, user_colour, user_birthday
		FROM ' . USERS_TABLE . "
		WHERE user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%'
			AND user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$user_colour = ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] .'"' : '';
		$birthday_list .= (($birthday_list != '') ? ', ' : '') . '<a' . $user_colour . ' href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']) . '">' . $row['username'] . '</a>';

		if ($age = (int) substr($row['user_birthday'], -4))
		{
			$birthday_list .= ' (' . ($now['year'] - $age) . ')';
		}
	}
	$db->sql_freeresult($result);
}

// Assign specific vars
$template->assign_vars(array(
	'BIRTHDAY_LIST'				=> $birthday_list,
	'S_DISPLAY_BIRTHDAY_LIST'	=> ($config['load_birthdays']) ? true : false,
	)
);

?>