<?php 
// m_lock controlls if moderator can lock / unlock / edit ratings
/**
*
* @package prs
* @version 1.0.0 2007/12/23 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* This file contains general function prs_that act as hooks)
* and binds them to prs building blocks functions.
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
        exit;
}

function prs_new_posts($data, $mode, $time = 0)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	if ($time <= 0)
	{
		$time = time();
	}

	if ($mode == 'post' || $mode = 'reply')
	{
		prs_clean_modpoints();
		prs_increase_modpoints($data['poster_id'],
			$data['post_id'],
			$data['post_time']);
	}
}

function prs_delete_post($post_id, &$data)
{
	if ($post_id <= 0)
	{
		return;
	}
	prs_delete_votes($post_id);
	prs_reduce_modpoints_deleted_post($post_id);
}

function prs_change_owner($post_id, $new_owner, $post_time)
{
	if ($post_id <= 0)
	{
		return;
	}
	prs_reduce_modpoints_deleted_post($post_id);
	prs_increase_modpoints($userdata['user_id'],
		$post_id,
		$post_info['post_time']);
	prs_clean_modpoints();
}

function &prs_profile($user_id,$accuracy = 1,$from_cache = true)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$karma = prs_karma($user_id);
	$penalty = prs_penalty($user_id);
	$modpoints = prs_modpoints($user_id);
	
	
	if ($from_cache == true) {
		$sql = 'SELECT prs_reputation,prs_o
		FROM `gateway_user_stats`
		WHERE user_id = '.$user_id;
		$result = $db->sql_query($sql, 0);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$karma['karma'] = $row['prs_reputation'];
		$karma['o'] = $row['prs_o'];
	}
	
	if ($accuracy == 1) {
		$score = round($karma['karma'], $accuracy);
	} else {
		$score = $karma['karma'];
	}
	
	$stars = round($score);
	
	switch ($stars) {
		default:
			$my_stars = '<img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" />';
		break;
		case 1:
			$my_stars = '<img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" />';
		break;
		case 2:
			$my_stars = '<img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" />';
		break;
		case 3:
			$my_stars = '<img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" />';
		break;
		case 4:
			$my_stars = '<img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star_0.gif" />';
		break;
		case 5:
			$my_stars = '<img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" /><img src="http://www.roleplaygateway.com/styles/RolePlayGateway/imageset/prs_star.gif" />';
		break;
	}

	$ret = array(
		'PRS_KARMA_STARS'	=> $my_stars,
		'PRS_KARMA_SCORE'	=> $score,
		'PRS_KARMA_O'		=> round($karma['o'], 2),
		'PRS_PENALTY'		=> $penalty ? $penalty : '0',
		'PRS_MODPOINTS'		=> $modpoints ? $modpoints : '0',
	);
	
	return $ret;
}

function prs_cron()
{
	if (!$config['prs_enabled'])
	{
		return;
	}
	prs_close_posts();
	prs_create_shadow_votes();
	prs_determine_penalties();
	prs_clean_modpoints();
}
?>