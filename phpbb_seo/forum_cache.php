<?php
/** 
*
* @package Advanced phpBB SEO mod Rewrite
* @version $Id: phpbb_seo_class.php 2007/05/26 13:48:48 dcz Exp $
* @copyright (c) 2006, 2007 dcz - www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl.php RPL Public License 
*
*/
/**
* phpBB_SEO Class
* www.phpBB-SEO.com
* @package Advanced phpBB3 SEO mod Rewrite
*/
if (!defined('IN_PHPBB')) {
	exit;
}
$forum_id_check = request_var('forum_id_check', 0);
$template->assign_block_vars('navlinks', array(
	'S_IS_CAT'	=>  true,
	'S_IS_LINK'	=> false,
	'S_IS_POST'	=> false,
	'FORUM_NAME'	=> $user->lang['SEO_FORUM_TITLE'],
	'FORUM_ID'	=> 0,
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_seo->seo_path['phpbb_url']}phpbb_seo.$phpEx?forum_url"))
);
$template->assign_vars(array(
	'MESSAGE_TITLE' => $user->lang['SEO_FORUM_TITLE'],
	'MESSAGE_TEXT' => $user->lang['SEO_FORUM_MSG'],
	'DETAIL_TITLE' => $user->lang['SEO_CACHE_DETAIL_TITLE'],
	'ID_TITLE' => $user->lang['SEO_CACHE_ID_TITLE'],
	'STATUS_TITLE' => $user->lang['SEO_CACHE_STATUS_TITLE'],
	'DETAILS_TITLE' => $user->lang['SEO_CACHE_DETAIL_TITLE'],
	'U_CACHE_ACTION'	=> $phpbb_seo->seo_path['phpbb_url'] . 'phpbb_seo.php?forum_url',
	'S_CACHE_FORUM_ACTION'	=> true,
	)
);
$forum_data = array();
$seo_advice = array();
$forum_url_title_parts = array();
$forum_url_title = '';
$forum_update = '';
//nice_print($phpbb_seo->cache_config);

// Check for submit
if ($submit && $forum_id_check > 0) {
	$forum_url_title = $phpbb_seo->format_url(request_var('forum_url' . $forum_id_check, ''), $phpbb_seo->seo_static['forum']);
	while (preg_match('`^[a-z0-9_-]+' . $phpbb_seo->seo_delim['forum'] . '[0-9]+$`i', $forum_url_title)) {
		$forum_url_title = preg_replace('`^([a-z0-9_-]+)' . $phpbb_seo->seo_delim['forum'] . '[0-9]+$`i', "\\1", $forum_url_title);
		if ($phpbb_seo->cache_config['settings']['rem_ids']) {
			$seo_advice[$forum_id_check] = '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_DELIM_REM'] . '</li>';
		}
	}
	// Forums cannot end with the pagination param
	while (preg_match('`^[a-z0-9_-]+' . $phpbb_seo->seo_delim['start'] . '[0-9]+$`i', $forum_url_title)) {
		$forum_url_title = preg_replace('`^([a-z0-9_-]+)' . $phpbb_seo->seo_delim['start'] . '[0-9]+$`i', "\\1", $forum_url_title);
		$seo_advice[$forum_id_check] = '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_START'] . '</li>';
	}
	if ($forum_url_title != $phpbb_seo->seo_static['forum'] && $forum_url_title != $phpbb_seo->seo_static['global_announce']) {
		if (!array_search($forum_url_title, $phpbb_seo->cache_config['forum'])) {
			$forum_update = $forum_url_title . ($phpbb_seo->cache_config['settings']['rem_ids'] ? '': $phpbb_seo->seo_delim['forum'] . $forum_id_check);
		} else {
			$seo_advice[$forum_id_check] = '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_DUPE'] . '</li>';
		}
	}
	if (!empty($forum_update)) {	
		$phpbb_seo->cache_config['forum'][$forum_id_check] = $forum_update;
		$redirect_url = append_sid("{$phpbb_root_path}phpbb_seo.$phpEx?forum_url&amp;submit") . '#forum_' . $forum_id_check;
		meta_refresh(3, $redirect_url);
		ksort($phpbb_seo->cache_config['forum']);
		if ($phpbb_seo->write_cache() ) {
			$msg = !empty($seo_advice[$forum_id_check]) ? '<ul class="topiclist">' . $seo_advice[$forum_id_check] . '</ul>' : '';
			trigger_error($user->lang['SEO_CACHE_MSG_OK'] . '<br />' . $msg . '<br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect_url . '">', '</a>'));
		} else {
			trigger_error($user->lang['SEO_CACHE_MSG_FAIL'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect_url . '">', '</a>'));
		}
	} else {
		seo_advices($forum_url_title, $url_color, $seo_advice, $forum_id_check);
		$redirect_url = append_sid("{$phpbb_root_path}phpbb_seo.$phpEx?forum_url&amp;submit") . '#seo_top';
		meta_refresh(5, $redirect_url);
		$msg = '<b style="color:red">' . $user->lang['SEO_CACHE_UPDATE_FAIL'] . '</b>';
		$msg .= '<br/><u style="color:red">' . $user->lang['SEO_CACHE_URL'] . '&nbsp;:</u>&nbsp;' . $forum_url_title . $phpbb_seo->seo_ext['forum'];
		$msg .= '<ul class="topiclist">' . $seo_advice[$forum_id_check] . '</ul>';
		trigger_error($msg . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect_url . '">', '</a>'));
	}
}
$sql = "SELECT forum_id, forum_name
	FROM " . FORUMS_TABLE . "
	ORDER BY forum_id ASC";
$result = $db->sql_query($sql);
$row = array();
while( $row = $db->sql_fetchrow($result) ) {
	$forum_id = $row['forum_id'];
	$url_color = 'blue';
	$forum_data[$forum_id] = $row;
	$status_msg = '';
	$status_img = '';
	// Is the URL cached already ?
	if ( empty($phpbb_seo->cache_config['forum'][$forum_id]) ) {
		// Suggest the one from the title
		$forum_url_title = $phpbb_seo->format_url($row['forum_name'], $phpbb_seo->seo_static['forum']);
		if ($forum_url_title != $phpbb_seo->seo_static['forum'] && $forum_url_title != $phpbb_seo->seo_static['global_announce']) {
			if (array_search($forum_url_title, $phpbb_seo->cache_config['forum'])) {
				$forum_data[$forum_id]['forum_url'] = $forum_url_title .  $phpbb_seo->seo_delim['forum'] . $forum_id;
				@$seo_advice[$forum_id] .= '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_DUPE'] . '</li>';
			} else {
				$forum_data[$forum_id]['forum_url'] = $forum_url_title . ($phpbb_seo->cache_config['settings']['rem_ids'] ? '': $phpbb_seo->seo_delim['forum'] . $forum_id);
			}
		} else {
			$forum_data[$forum_id]['forum_url'] = $forum_url_title . $phpbb_seo->seo_delim['forum'] . $forum_id;
		}
		seo_advices($forum_data[$forum_id]['forum_url'], $url_color, $seo_advice, $forum_id);
		$title = '<b style="color:red">' . $row['forum_name'] . '</b>';
		$url_title = '<u style="color:red">' . $user->lang['SEO_CACHE_URL_NOT_OK'] . '&nbsp;:</u>&nbsp;<b style="color:' . $url_color . '">' . $forum_data[$forum_id]['forum_url'] . '</b>';
		$url = '<u style="color:red">' . $user->lang['SEO_CACHE_URL'] . '&nbsp;:</u>&nbsp;' . $forum_data[$forum_id]['forum_url'] . $phpbb_seo->seo_delim['forum'] . $forum_id . $phpbb_seo->seo_ext['forum'];
		$status_msg = '<b style="color:red">' . $user->lang['SEO_CACHE_NOT_OK'] . '</b>';
		$status_img = $user->img('topic_unread_locked', 'NEW_POSTS', false, '', 'src');
	} else { // Cached
		$forum_data[$forum_id]['forum_url'] = $phpbb_seo->cache_config['forum'][$forum_id];
		seo_advices($phpbb_seo->cache_config['forum'][$forum_id], $url_color, $seo_advice, $forum_id);
		$title = '<b style="color:green">' . $row['forum_name'] . '</b>';
		$url_title = '<u style="color:green">' . $user->lang['SEO_CACHE_URL_OK'] . '&nbsp;:</u>&nbsp;<b style="color:' . $url_color . '">' . $phpbb_seo->cache_config['forum'][$forum_id] . '</b>';
		$url = '<u style="color:green">' . $user->lang['SEO_CACHE_URL'] . '&nbsp;:</u>&nbsp;' . $phpbb_seo->cache_config['forum'][$forum_id] . $phpbb_seo->seo_ext['forum'];
		$status_msg = '<b style="color:green">' . $user->lang['SEO_CACHE_OK'] . '</b>';
		$status_img = $user->img('topic_read_locked', 'NEW_POSTS', false, '', 'src');
	}
	$template->assign_block_vars('forumrow', array(
		'FORUM_NAME' => $title,
		'FORUM_ID' => $forum_id,
		'FORUM_URL_TITLE' => $url_title,
		'FORUM_URL' => $url,
		'URL' => $forum_data[$forum_id]['forum_url'],
		'STATUS_MSG' => $status_msg,
		'FORUM_ADVICES' => !empty($seo_advice[$forum_id]) ? '<ul>' . $seo_advice[$forum_id] . '</ul>' : '',
		'FORUM_FOLDER_STATUS_IMG'	=> $status_img,
		)
	);
}
$db->sql_freeresult($result);
?>
