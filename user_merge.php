<?php
define('DEBUG', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

$old_user = 33793;
$new_user = 16619;

user_merge($old_user, $new_user);

trigger_error('Wheee done :P');

/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2007 eviL3
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

/**
 * Merge two user accounts into one
 *
 * @param int $old_user User id of the old user
 * @param int $new_user User id of the new user
 */
function user_merge($old_user, $new_user)
{
	global $user, $db;
	global $phpbb_root_path, $phpEx;

	if (!function_exists('user_add'))
	{
		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	}

	$old_user = (int) $old_user;
	$new_user = (int) $new_user;

	// get both users userdata
	$userdata_ary = array();
	foreach (array($old_user, $new_user) as $key)
	{
		$sql = 'SELECT user_id, username, user_colour
			FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $key;
		$result = $db->sql_query($sql);
		$userdata_ary[$key] = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	}

	$update_ary = array(
		ATTACHMENTS_TABLE		=> array('poster_id'),
		"rpg_characters"		=> array('owner'),
		"rpg_roleplays"			=> array('owner'),
		PRS_VOTES_TABLE			=> array('user_id'),
		POST_STATS_TABLE		=> array('poster_id'),
		FORUMS_TABLE			=> array(array('forum_last_poster_id', 'forum_last_poster_name', 'forum_last_poster_colour')),
		LOG_TABLE				=> array('user_id', 'reportee_id'),
		MODERATOR_CACHE_TABLE	=> array(array('user_id', 'username')),
		POSTS_TABLE				=> array(array('poster_id', 'post_username'), 'post_edit_user'),
		POLL_VOTES_TABLE		=> array('vote_user_id'),
		PRIVMSGS_TABLE			=> array('author_id', 'message_edit_user'),
		PRIVMSGS_TO_TABLE		=> array('user_id', 'author_id'),
		REPORTS_TABLE			=> array('user_id'),
		TOPICS_TABLE			=> array(array('topic_poster', 'topic_first_poster_name', 'topic_first_poster_colour'), array('topic_last_poster_id', 'topic_last_poster_name', 'topic_last_poster_colour')),
	);

	foreach ($update_ary as $table => $field_ary)
	{
		foreach ($field_ary as $field)
		{
			if (is_array($field))
			{
				$username = $db->sql_escape($userdata_ary[$new_user]['username']);
				$user_colour = $db->sql_escape($userdata_ary[$new_user]['user_colour']);

				$set_sql = "SET {$field[0]} = $new_user";
				$set_sql .= ", {$field[1]} = '$username'";
				$set_sql .= isset($field[2]) ? ", {$field[2]} = '$user_colour'" : '';
				$where_sql = "WHERE {$field[0]} = $old_user";
			}
			else
			{
				$set_sql = "SET $field = $new_user";
				$where_sql = "WHERE $field = $old_user";
			}

			$sql = "UPDATE $table
				$set_sql
					$where_sql";
			$db->sql_query($sql);
		}
	}

	user_delete('remove', $old_user);
	
	add_log('admin', "Merged user ID #".$old_user." into ".$username." (user ID #".$new_user.")");
}

?>
