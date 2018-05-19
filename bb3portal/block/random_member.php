<?php
/*
*
* @name random_member.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: random_member.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
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

$sql = 'SELECT user_id, username, user_posts, user_regdate, user_colour, user_occ, user_from, user_website
	FROM ' . USERS_TABLE . '
	WHERE user_type <> 2
	AND user_inactive_time = 0
	ORDER BY RAND() 
	LIMIT 0,1';

$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);

$template->assign_block_vars('random_member', array(
	'USERNAME'		=> censor_text($row['username']),
	'USERNAME_COLOR'=> ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] .'"' : '',
	'U_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
	'USER_POSTS'	=> $row['user_posts'],
	'JOINED'		=> $user->format_date($row['user_regdate'], $format = 'd.n.Y'),
	'USER_OCC'		=> censor_text($row['user_occ']),
	'USER_FROM'		=> censor_text($row['user_from']),
	'U_WWW'			=> censor_text($row['user_website']),
	)
);
$db->sql_freeresult($result);


?>