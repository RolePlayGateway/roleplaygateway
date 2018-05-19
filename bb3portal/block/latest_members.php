<?php
/*
*
* @name latest_members.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: latest_members.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
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

$sql = 'SELECT user_id, username, user_regdate, user_colour
	FROM ' . USERS_TABLE . '
	WHERE user_type <> 2
	AND user_inactive_time = 0
	ORDER BY user_regdate DESC';
	
$result = $db->sql_query_limit($sql, $CFG['max_last_member']);

while( ($row = $db->sql_fetchrow($result)) && ($row['username'] != '') )
{
	$template->assign_block_vars('latest_members', array(
		'USERNAME'		=> censor_text($row['username']),
		'USERNAME_COLOR'=> ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] .'"' : '',
		'U_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
		'JOINED'		=> $user->format_date($row['user_regdate'], $format = 'd M'),
		)
	);
}
$db->sql_freeresult($result);

?>