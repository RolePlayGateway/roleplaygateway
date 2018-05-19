<?php
/**
*
* @name stat_adv.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: stat_adv.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
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

// switch idea from phpbb2 :p
function get_db_stat($mode)
{
	global $db, $user;

	switch( $mode )
	{
		case 'newposttotal':
			$sql = "SELECT COUNT(post_id) AS newpost_total
				FROM " . POSTS_TABLE . "
				WHERE post_time > " . $user->data['session_last_visit'];
			break;

		case 'newtopictotal':
			$sql = "SELECT COUNT(distinct p.post_id) AS newtopic_total
				FROM " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t
				WHERE p.post_time > " . $user->data['session_last_visit'] . "
				AND p.post_id = t.topic_first_post_id";
			break;
			
		case 'newannouncmenttotal':
			$sql = "SELECT COUNT(distinct t.topic_id) AS newannouncment_total
				FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p
				WHERE t.topic_type = 2
				AND p.post_time > " . $user->data['session_last_visit'] . "
				AND p.post_id = t.topic_first_post_id";
			break;

		case 'newstickytotal':
			$sql = "SELECT COUNT(distinct t.topic_id) AS newsticky_total
				FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p
				WHERE t.topic_type = 1
				AND p.post_time > " . $user->data['session_last_visit'] . "
				AND p.post_id = t.topic_first_post_id";
			break;	

		case 'announcmenttotal':
			$sql = "SELECT COUNT(distinct t.topic_id) AS announcment_total
				FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p
				WHERE t.topic_type = 2
				AND p.post_id = t.topic_first_post_id";
			break;

		case 'stickytotal':
			$sql = "SELECT COUNT(distinct t.topic_id) AS sticky_total
				FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p
				WHERE t.topic_type = 1
				AND p.post_id = t.topic_first_post_id";
			break;
	}
	
	if ( !($result = $db->sql_query($sql)) )
	{
		return false;
	}

	$row = $db->sql_fetchrow($result);
 
	switch ( $mode )
	{
		case 'newposttotal':
			return $row['newpost_total'];
			break;

		case 'newtopictotal':
			return $row['newtopic_total'];
			break;

		case 'newannouncmenttotal':
			return $row['newannouncment_total'];
			break;

		case 'announcmenttotal':
			return $row['announcment_total'];
			break;
			
		case 'newstickytotal':
			return $row['newsticky_total'];
			break;

		case 'stickytotal':
			return $row['sticky_total'];
			break;
	}
	return false;
}

// Set some stats, get posts count from forums data if we... hum... retrieve all forums data
$total_posts		= $config['num_posts'];
$total_topics		= $config['num_topics'];
$total_users		= $config['num_users'];
$newest_user		= $config['newest_username'];
$newest_uid			= $config['newest_user_id'];

$l_total_user_s 	= ($total_users == 0) ? 'TOTAL_USERS_ZERO' : 'TOTAL_USERS_OTHER';
$l_total_post_s 	= ($total_posts == 0) ? 'TOTAL_POSTS_ZERO' : 'TOTAL_POSTS_OTHER';
$l_total_topic_s	= ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';

// Assign specific vars
$template->assign_vars(array(
	'TOTAL_POSTS'	=> sprintf($user->lang[$l_total_post_s], $total_posts),
	'TOTAL_TOPICS'	=> sprintf($user->lang[$l_total_topic_s], $total_topics),
	'TOTAL_USERS'	=> sprintf($user->lang[$l_total_user_s], $total_users),
	'NEWEST_USER'	=> sprintf($user->lang['NEWEST_USER'], '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $newest_uid) . '">', $newest_user, '</a>'),

	'S_NEW_POSTS'	=> get_db_stat('newposttotal'),
	'S_NEW_TOPIC'	=> get_db_stat('newtopictotal'),
	'S_NEW_ANN'		=> get_db_stat('newannouncmenttotal'),
	'S_NEW_SCT'		=> get_db_stat('newstickytotal'),
	'S_ANN'			=> get_db_stat('announcmenttotal'),
	'S_SCT'			=> get_db_stat('stickytotal'),
	)
);

?>