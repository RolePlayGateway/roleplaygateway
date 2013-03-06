<?php
/**
*
* @name most_poster.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: most_poster.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
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

$sql = 'SELECT user_id, username, user_posts, user_colour
	FROM ' . USERS_TABLE . '
	WHERE user_type <> 2
	AND user_posts <> 0
	ORDER BY user_posts DESC';

$result = $db->sql_query_limit($sql, $CFG['max_most_poster']);

while( ($row = $db->sql_fetchrow($result)) && ($row['username'] != '') )
{
	$template->assign_block_vars('most_poster', array(
		'S_SEARCH_ACTION'=> append_sid("{$phpbb_root_path}search.$phpEx", 'author_id=' . $row['user_id'] . '&amp;sr=posts'),
		'USERNAME'		=> censor_text($row['username']),
		'USERNAME_COLOR'=> ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] .'"' : '',
		'U_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']),
		'POSTER_POSTS'	=> $row['user_posts'],
		)
	);
}
$db->sql_freeresult($result);

?>