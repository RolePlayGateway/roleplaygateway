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

function prs_set_score($score, $mode, $action, $data, $force = FALSE)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$score *= PRS_MULTIPLIER_SCORE;

	if ($mode == 'forum_view')
	{
		$sql = '';
		$all = FALSE;
		switch($action)
		{
			case 'prs_votes_lock':
			case 'prs_votes_unlock':
			case 'prs_vote_lock':
			case 'prs_vote_unlock':
			case 'prs_vote_reset':
				$all = TRUE;
			break;

			case 'prs_vote_score_1':
			case 'prs_vote_score_2':
			case 'prs_vote_score_3':
			case 'prs_vote_score_4':
			case 'prs_vote_score_5':
			default:
				$all = FALSE;
			break;
		}
		$data['post_list'] = prs_get_post_from_topic($data['topic_list'], $all || $force);
	}

	$sql = 'UPDATE ' . POSTS_TABLE .'
		SET prs_score = ' . $score . '
			prs_shadowed = ' . ($score ? 1 : 0) . '
			prs_penaltized = ' . ($score ? 1 : 0) . '
		WHERE ' . $db->sql_in_set('post_id', $data['post_list']);
	$db->sql_freeresult($db->sql_query($sql));
}

function prs_vote_unlock($mode, $action, $data)
{
	prs_set_score(0, $mode, $action, $data, TRUE);
}

function prs_vote_lock($mode, $data)
{
	if ($data == NULL)
	{
		return;
	}

	switch($mode)
	{
		case 'forum_view':
			$data['post_list'] = prs_get_post_from_topic(
				$data['topic_list'], TRUE
			);
		case 'topic_view':
		case 'post_details':
			prs_close_posts($data['post_list'], TRUE);
		break;
	}
}

function prs_mcp_main($mode, $action, $quickmod)
{
	// PRS
	$score = 0;

	switch ($action)
	{
		// PRS
		case 'prs_votes_lock':
		case 'prs_vote_lock':
			$topic_ids = (!$quickmod) ? request_var('topic_id_list', array(0)) : array(request_var('t', 0));
			$post_ids = (!$quickmod) ? request_var('post_id_list', array(0)) : array(request_var('p', 0));
			prs_vote_lock($mode, $action, array(
				'topic_list'	=> $topic_ids,
				'post_list'	=> $post_ids,
			));
		break;

		case 'prs_votes_unlock':
		case 'prs_vote_unlock':
			$topic_ids = (!$quickmod) ? request_var('topic_id_list', array(0)) : array(request_var('t', 0));
			$post_ids = (!$quickmod) ? request_var('post_id_list', array(0)) : array(request_var('p', 0));
			prs_vote_unlock($mode, $action, array(
				'topic_list'	=> $topic_ids,
				'post_list'	=> $post_ids,
			));
		break;

		case 'prs_vote_score_5':
			$score++;
		case 'prs_vote_score_4':
			$score++;
		case 'prs_vote_score_3':
			$score++;
		case 'prs_vote_score_2':
			$score++;
		case 'prs_vote_score_1':
			$score++;
		case 'prs_vote_reset':
			$topic_ids = (!$quickmod) ? request_var('topic_id_list', array(0)) : array(request_var('t', 0));
			$post_ids = (!$quickmod) ? request_var('post_id_list', array(0)) : array(request_var('p', 0));
			prs_set_score($score, $mode, $action, array(
				'topic_list'	=> $topic_ids,
				'post_list'	=> $post_ids,
			));
		break;
	}
}
?>
