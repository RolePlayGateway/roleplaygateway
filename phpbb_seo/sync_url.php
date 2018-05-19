<?php
/**
*
* @package Ultimate SEO URL phpBB SEO
* @version $Id: sync_url.php 222 2010-02-27 13:08:48Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl1.5.txt Reciprocal Public License 1.5
*
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// Try to override some limits - maybe it helps some...
@set_time_limit(0);
$mem_limit = @ini_get('memory_limit');
if (!empty($mem_limit)) {
	$unit = strtolower(substr($mem_limit, -1, 1));
	$mem_limit = (int) $mem_limit;
	if ($unit == 'k') {
		$mem_limit = floor($mem_limit / 1024);
	} else if ($unit == 'g') {
		$mem_limit *= 1024;
	} else if (is_numeric($unit)) {
		$mem_limit = floor((int) ($mem_limit . $unit) / 1048576);
	}
	$mem_limit = max(128, $mem_limit) . 'M';
} else {
	$mem_limit = '128M';
}
@ini_set('memory_limit', $mem_limit);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/acp_phpbb_seo');
// Security check
// Circumvent a potential phpbb bug with paths
$redirect = append_sid(generate_board_url() . "/phpbb_seo/sync_url.$phpEx");
if (!$user->data['is_registered']) {
	login_box($redirect, $user->lang['SEO_LOGIN'],'', false, false);
}
if (!$auth->acl_get('a_')) {
	$user->session_kill(true);
	login_box($redirect, $user->lang['SEO_LOGIN_ADMIN'],'', false, false);
}
if ($user->data['user_type'] != USER_FOUNDER) {
	login_box($redirect, $user->lang['SEO_LOGIN_FOUNDER'],'', false, false);
}
$start = max(0, request_var('start', 0));
$limit = max(100, request_var('limit', 0));
// Do not go over 1000 topic in a row
$limit = min(1000, $limit);
$go = max(0, request_var('go', 0));
$mode = request_var('mode', '');
$poll_processed = 0;
// Add navigation links
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' => "Sync Topic URL",
	'U_VIEW_FORUM'	=> append_sid("./sync_url.$phpEx"))
);
$msg_title = $user->lang['SYNC_TITLE'];
if (empty($phpbb_seo->seo_opt['sql_rewrite'])) {
	trigger_error($user->lang['SYNC_REQ_SQL_REW'], E_USER_WARNING);
}
if(!$go) {
	trigger_error($user->lang['SYNC_WARN'] . '<br/><br/><b> &bull; <a href="' . append_sid("./sync_url.$phpEx?go=1&amp;mode=sync") . '">' . $user->lang['SYNC_TOPIC_URLS'] . '</a><br/><br/> &bull; <a href="' . append_sid("./sync_url.$phpEx?go=1&amp;mode=reset") . '" >' . $user->lang['SYNC_RESET_TOPIC_URLS'] . '</a></b>');
}

$forum_data = array();
$url_updated = 0;
if ($mode === 'sync') {
	// get all forum info
	$sql = 'SELECT forum_id, forum_name FROM ' . FORUMS_TABLE;
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
		$forum_data[$row['forum_id']] = $row['forum_name'];
		$phpbb_seo->set_url($row['forum_name'], $row['forum_id'], $phpbb_seo->seo_static['forum']);
	}
	$db->sql_freeresult($result);
	// let's work
	$sql = 'SELECT * FROM ' . TOPICS_TABLE . '
			ORDER BY topic_id ASC';
	$result = $db->sql_query_limit($sql, $limit, $start);
	while ($row = $db->sql_fetchrow($result)) {
		$forum_id = (int) $row['forum_id'];
		$topic_id = (int) $row['topic_id'];
		$_parent = $row['topic_type'] == POST_GLOBAL ? $phpbb_seo->seo_static['global_announce'] : $phpbb_seo->seo_url['forum'][$forum_id];
		if ( !$phpbb_seo->check_url('topic', $row['topic_url'], $_parent)) {
			if (!empty($row['topic_url'])) {
				// Here we get rid of the seo delim (-t) and put it back even in simple mod
				// to be able to handle all cases at once
				$_url = preg_replace('`' . $phpbb_seo->seo_delim['topic'] . '$`i', '', $row['topic_url']);
				$_title = $phpbb_seo->get_url_info('topic', $_url . $phpbb_seo->seo_delim['topic'] . $topic_id, 'title');
			} else {
				$_title = $phpbb_seo->modrtype > 2 ? censor_text($row['topic_title']) : '';
			}
			unset($phpbb_seo->seo_url['topic'][$topic_id]);
			$row['topic_url'] = $phpbb_seo->get_url_info('topic', $phpbb_seo->prepare_url( 'topic', $_title, $topic_id, $_parent, (( empty($_title) || ($_title == $phpbb_seo->seo_static['topic']) ) ? true : false) ), 'url');
			unset($phpbb_seo->seo_url['topic'][$topic_id]);
			if ($row['topic_url']) {
				// Update the topic_url field for later re-use
				$sql = "UPDATE " . TOPICS_TABLE . " SET topic_url = '" . $db->sql_escape($row['topic_url']) . "'
					WHERE topic_id = $topic_id";
				$db->sql_query($sql);
				$url_updated++;
			}
		}
	}
	$db->sql_freeresult($result);
	$sql = 'SELECT count(topic_id) as topic_cnt FROM ' . TOPICS_TABLE;
	$result = $db->sql_query($sql);
	$cnt = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	if ($cnt['topic_cnt'] > ($start + $limit)) {
		$endtime = array_sum(explode(' ', microtime()));
		$duration = $endtime - $starttime;
		$speed = round($limit/$duration, 2);
		$percent = round((($start + $limit) / $cnt['topic_cnt']) * 100, 2);
		$message = sprintf($user->lang['SYNC_PROCESSING'], $percent, ($start + $limit), $cnt['topic_cnt'], $limit, $speed, round($duration, 2) , round((($cnt['topic_cnt'] - $start)/$speed)/60, 2));
		if ($url_updated) {
			$message.= sprintf($user->lang['SYNC_ITEM_UPDATED'], '<br/>' . $url_updated);
		}
		$new_limit = ($duration < 10) ? $limit + 50 : $limit - 10;
		meta_refresh(1, append_sid('./sync_url.' . $phpEx . '?go=1&amp;start=' . ($start + $limit) . "&amp;limit=$new_limit&amp;mode=sync"));
		trigger_error("$message<br/>");
	} else {
		trigger_error($user->lang['SYNC_COMPLETE'] . sprintf($user->lang['RETURN_INDEX'], '<br/><br/><a href="' . append_sid($phpbb_root_path) . '" >', '</a>'));
	}
} elseif ($mode === 'reset') {
	if (confirm_box(true)) {
		$sql = "UPDATE " . TOPICS_TABLE . " SET topic_url = ''";
		$db->sql_query($sql);
		trigger_error($user->lang['SYNC_RESET_COMPLETE'] . '<br/><br/><b> &bull; <a href="' . append_sid("./sync_url.$phpEx?go=1&amp;mode=sync") . '">' . $user->lang['SYNC_TOPIC_URLS'] . '</a><br/><br/> &bull; ' . sprintf($user->lang['RETURN_INDEX'], '<a href="' . append_sid($phpbb_root_path) . '" >', '</a></b>'));
	} else {
		confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array('go' => '1', 'mode' => 'reset')), 'confirm_body.html', append_sid("./phpbb_seo/sync_url.$phpEx"));
	}
} else {
	trigger_error($user->lang['SYNC_WARN'] . '<br/><br/><b> &bull; <a href="' . append_sid("./sync_url.$phpEx?go=1&amp;mode=sync") . '">' . $user->lang['SYNC_TOPIC_URLS'] . '</a><br/><br/> &bull; <a href="' . append_sid("./sync_url.$phpEx?go=1&amp;mode=reset") . '" >' . $user->lang['SYNC_RESET_TOPIC_URLS'] . '</a></b>');
}
?>