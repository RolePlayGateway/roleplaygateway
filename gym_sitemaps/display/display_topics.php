<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: display_topics.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* display_topics Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class display_topics {
	function display_topics(&$master) {
		global $user, $template, $config, $phpEx, $db, $auth, $cache, $phpbb_root_path;
		static $display_orders = array('first' => 't.topic_id', 'last' => 't.topic_last_post_time');
		global $phpbb_seo;
		// Usefull for multi bb topic & forum tracking
		// Leave default for single forum eg : '_track'
		$tracking_cookie_name = (defined('XLANG_AKEY') ? XLANG_AKEY : '') . '_track';
		// Specific options
		$display_file = &$master->call['display_file'];
		$display_user_info = &$master->call['display_user_info'];
		$display_link = &$master->call['display_link'];
		$display_pagination = &$master->call['display_pagination'];
		$display_tracking = &$master->call['display_tracking'];
		$display_topic_status = &$master->call['display_topic_status'];
		$display_user_link = !empty($master->call['display_user_link']) ? true : false;
		$display_user_link_key = $display_user_link ? 'full' : 'no_profile';
		$display_last_post = &$master->call['display_last_post'];
		$display_order = isset($display_orders[$master->call['display_order']]) ? $display_orders[$master->call['display_order']] : $display_orders['first'];
		$limit_time_sql = !empty($master->call['limit_time']) ? ' AND t.topic_last_post_time > ' . ($user->time_now - $master->call['limit_time']) : '';
		$order_sql = @$master->call['sort'] == 'ASC' ? ' ASC' : ' DESC';
		if (!$display_tracking) {
			$load_db_lastread = $load_anon_lastread = false;
		} else {
			$load_db_lastread = (boolean) ($config['load_db_lastread'] && $user->data['is_registered']);
			$load_anon_lastread = (boolean) ($config['load_anon_lastread'] || $user->data['is_registered']);
		}
		// hanlde options
		$limit = $master->call['limit'] >= 1 ? (int) $master->call['limit'] : 5;
		$start = &$master->start;
		if (!$display_pagination || empty($display_file)) {
			$start = 0;
			$display_pagination = false;
		}
		$total_topics = 0;
		$topic_sql = $master->call['topic_sql'];
		$forum_sql = $master->call['forum_sql'];
		$s_global = $master->call['s_global'];
		// Do some reset
		$topic_datas = $topic_ids = $forum_ids = $user_cache = $id_cache = $post_datas = $forum_datas = array();
		$forum_id = $master->call['forum_id'];
		// Get The Data, first forums
		if ((!$s_global && !$master->call['single_forum']) || ($master->call['single_forum'] && empty($master->forum_datas[$master->call['forum_id']])) ) {
			$sql_array = array(
				'SELECT'	=> 'f.*',
				'FROM'		=> array(
					FORUMS_TABLE	=> 'f',
				),
				'LEFT_JOIN' => array(),
			);
			if ($load_db_lastread) {
				$sql_array['SELECT'] .= ', ft.mark_time as forum_mark_time';
				$sql_array['LEFT_JOIN'][] = array(
					'FROM'	=> array(FORUMS_TRACK_TABLE => 'ft'),
					'ON'	=> 'ft.user_id = ' . $user->data['user_id'] . ' AND ft.forum_id = f.forum_id'
				);
			}
			$sql_array['WHERE'] = $forum_sql ? str_replace('t.forum_id', 'f.forum_id', $forum_sql) : '';
			$sql = $db->sql_build_query('SELECT', $sql_array);
			unset($sql_array);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result)) {
				$forum_id = (int) $row['forum_id'];
				$forum_datas[$forum_id] = $row;
			}
			$db->sql_freeresult($result);
		}
		// Now the topics
		$sql_array = array(
			'SELECT'	=> 't.*',
			'FROM'		=> array(
				TOPICS_TABLE	=> 't',
			),
			'LEFT_JOIN' => array(),
		);
		if ($load_db_lastread) {
			$sql_array['SELECT'] .= ', tt.mark_time';
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(TOPICS_TRACK_TABLE => 'tt'),
				'ON'	=> 'tt.user_id = ' . $user->data['user_id'] . ' AND tt.topic_id = t.topic_id'
			);
		} elseif ($load_anon_lastread && empty($master->tracking_topics)) {
			$master->tracking_topics = (isset($_COOKIE[$config['cookie_name'] . $tracking_cookie_name])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . $tracking_cookie_name]) : $_COOKIE[$config['cookie_name'] . $tracking_cookie_name]) : '';
			$master->tracking_topics = ($master->tracking_topics) ? tracking_unserialize($master->tracking_topics) : array();
			if (!$user->data['is_registered']) {
				$user->data['user_lastmark'] = (isset($master->tracking_topics['l'])) ? (int) (base_convert($master->tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
			}
		}
		$sql_where = ($forum_sql ? $forum_sql : '') . $limit_time_sql;
		$sql_where .= $topic_sql ? ($sql_where ? ' AND ' : '') . $topic_sql : '';
		$sql_where .= ($sql_where ? ' AND ' : '') . 't.topic_status <> ' . ITEM_MOVED;
		if ($master->call['single_forum']) {
			$sql_where .= $auth->acl_get('m_approve', $master->call['forum_id']) ? '' : ' AND t.topic_approved = 1';
		} else {
			// only admins and global moderators will see un-approved topics
			// in the forum they have access to.
			$sql_where .= ($auth->acl_gets('a_') || $auth->acl_getf_global('m_')) ? '' : ' AND t.topic_approved = 1';
		}
		// obtain correct topic count if we display pagination
		if ($display_pagination) {
			$sql = "SELECT COUNT(t.topic_id) AS num_topics
				FROM " . TOPICS_TABLE . " t
				WHERE $sql_where";
			$result = $db->sql_query($sql);
			$total_topics = (int) $db->sql_fetchfield('num_topics');
			$db->sql_freeresult($result);
			// Make sure $start is set to the last page if it exceeds the amount
			if ($start < 0 || $start > $total_topics) {
				$start = ($start < 0) ? 0 : floor(($total_topics - 1) / $limit) * $limit;
				// Since we've reached here, $start is not set proper, kill the dupe!
				$url = $display_file . $master->gym_master->html_add_start($start);
				$master->gym_master->seo_kill_dupes($url);
			}
		}
		$sql_array['WHERE'] = $sql_where;
		$sql_array['ORDER_BY'] = $display_order . $order_sql;
		$sql = $db->sql_build_query('SELECT', $sql_array);
		unset($sql_array);
		$result = $db->sql_query_limit($sql, $limit, $start);
		while ($row = $db->sql_fetchrow($result)) {
			$topic_id = (int) $row['topic_id'];
			$forum_id = (int) $row['forum_id'];
			// Start with the forum
			if (!$s_global && empty($master->forum_datas[$forum_id])) {
				// www.phpBB-SEO.com SEO TOOLKIT BEGIN
				$phpbb_seo->set_url($forum_datas[$forum_id]['forum_name'], $forum_id, $phpbb_seo->seo_static['forum']);
				// www.phpBB-SEO.com SEO TOOLKIT END
				$master->forum_datas[$forum_id] = array_merge($forum_datas[$forum_id],  array(
					'forum_url' => append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id"),
					'm_approve' => $auth->acl_get('m_approve', $forum_id),
				));
				if ($load_db_lastread) {
					$master->forum_tracking_info[$forum_id] = !empty($forum_datas[$forum_id]['forum_mark_time']) ? $forum_datas[$forum_id]['forum_mark_time'] : $user->data['user_lastmark'];
				} elseif ($load_anon_lastread) {
					$master->forum_tracking_info[$forum_id] = isset($master->tracking_topics['f'][$forum_id]) ? (int) (base_convert($master->tracking_topics['f'][$forum_id], 36, 10) + $config['board_startdate']) : $user->data['user_lastmark'];
				}
			}
			$topic_datas[$topic_id] = $row;
			$topic_ids[$topic_id] = $topic_id;
		}
		$db->sql_freeresult($result);
		// Let's go
		$has_result = false;
		if (!empty($topic_datas)) {
			$has_result = true;
			// Grab icons
			if (empty($master->icons)) {
				$master->icons = $cache->obtain_icons();
			}
			$s_type_switch = 0;
			$folder_alt = 'NO_NEW_POSTS';
			$topic_type = '';
			$folder_img = 'topic_read';
			$topic_folder_img = $user->img($folder_img, $folder_alt);
			$topic_folder_img_src = $user->img($folder_img, $folder_alt, false, '', 'src');
			foreach ($topic_ids as $topic_id) {
				$topic_id = (int) $topic_id;
				$row = &$topic_datas[$topic_id];
				$forum_id = (int) $row['forum_id'];
				if (empty($master->forum_tracking_info[$forum_id])) {
					if ($load_db_lastread) {
						$master->topic_tracking_info[$topic_id] = !empty($row['mark_time']) ? $row['mark_time'] : $user->data['user_lastmark'];
					} else if ($load_anon_lastread) {
						$topic_id36 = base_convert($topic_id, 10, 36);
						if (isset($master->tracking_topics['t'][$topic_id36])) {
							$master->tracking_topics['t'][$topic_id] = base_convert($master->tracking_topics['t'][$topic_id36], 36, 10) + $config['board_startdate'];
						}
						$master->topic_tracking_info[$topic_id] = isset($master->tracking_topics['t'][$topic_id]) ? $master->tracking_topics['t'][$topic_id] : $user->data['user_lastmark'];
					}
				} else {
					$master->topic_tracking_info[$topic_id] = $master->forum_tracking_info[$forum_id];
				}
				$row['topic_title'] = censor_text($row['topic_title']);
				// www.phpBB-SEO.com SEO TOOLKIT BEGIN
				$phpbb_seo->prepare_iurl($row, 'topic', $row['topic_type'] == POST_GLOBAL ? $phpbb_seo->seo_static['global_announce'] : $phpbb_seo->seo_url['forum'][$forum_id]);
				// www.phpBB-SEO.com SEO TOOLKIT END
				// This will allow the style designer to output a different header
				// or even separate the list of announcements from sticky and normal topics
				$s_type_switch_test = /*($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 :*/ 0;
				// Replies
				$replies = !empty($master->forum_datas[$forum_id]['m_approve']) ? $row['topic_replies_real'] : $row['topic_replies'];
				$unread_topic = (isset($master->topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $master->topic_tracking_info[$topic_id]) ? true : false;
				// Get folder img, topic status/type related information
				if ($display_topic_status) {
					$master->gym_master->topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);
					$topic_folder_img = $user->img($folder_img, $folder_alt);
					$topic_folder_img_src = $user->img($folder_img, $folder_alt, false, '', 'src');
				}
				// Generate all the URIs ...
				$view_topic_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id");
				$view_forum_url = !$s_global ? $master->forum_datas[$forum_id]['forum_url'] : '';
				$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
				$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
				$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $user->session_id) : '';
				// www.phpBB-SEO.com SEO TOOLKIT BEGIN -> no dupe
				if (!empty($phpbb_seo->seo_opt['no_dupe']['on'])) {
					if (($replies + 1) > $phpbb_seo->seo_opt['topic_per_page']) {
						$phpbb_seo->seo_opt['topic_last_page'][$topic_id] = floor($replies / $phpbb_seo->seo_opt['topic_per_page']) * $phpbb_seo->seo_opt['topic_per_page'];
					}
				}
				// www.phpBB-SEO.com SEO TOOLKIT END -> no dupe
				$tpl_data = array(
					'FORUM_ID' => $forum_id,
					'TOPIC_ID' => $topic_id,
					'PAGINATION' => $master->call['display_topic_pagination'] ? $master->gym_master->topic_generate_pagination($replies, $view_topic_url) : '',
					'REPLIES' => $replies,
					'VIEWS' => $row['topic_views'],
					'TOPIC_TITLE' => $row['topic_title'],
					'FORUM_NAME' => !$s_global ? $master->forum_datas[$forum_id]['forum_name'] : '',
					'TOPIC_TYPE' => $topic_type,
					'TOPIC_FOLDER_IMG' => $topic_folder_img,
					'TOPIC_FOLDER_IMG_SRC' => $topic_folder_img_src,
					'TOPIC_FOLDER_IMG_ALT' => $user->lang[$folder_alt],
					'TOPIC_ICON_IMG' => (!empty($master->icons[$row['icon_id']])) ? $master->icons[$row['icon_id']]['img'] : '',
					'TOPIC_ICON_IMG_WIDTH' => (!empty($master->icons[$row['icon_id']])) ? $master->icons[$row['icon_id']]['width'] : '',
					'TOPIC_ICON_IMG_HEIGHT' => (!empty($master->icons[$row['icon_id']])) ? $master->icons[$row['icon_id']]['height'] : '',
					'ATTACH_ICON_IMG' => ($auth->acl_get('u_download') && $auth->acl_get('f_download', $forum_id) && $row['topic_attachment']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
					'UNAPPROVED_IMG' => ($topic_unapproved || $posts_unapproved) ? $user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',
					'FIRST_POST_TIME' => $user->format_date($row['topic_time']),
					'LAST_POST_TIME' => $user->format_date($row['topic_last_post_time']),
					'LAST_VIEW_TIME' => $user->format_date($row['topic_last_view_time']),
					'S_TOPIC_TYPE' => $row['topic_type'],
					'S_USER_POSTED' => (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
					'S_UNREAD_TOPIC' => $unread_topic,
					'S_TOPIC_REPORTED' => (!empty($row['topic_reported']) && $auth->acl_get('m_report', $forum_id)) ? true : false,
					'S_TOPIC_UNAPPROVED' => $topic_unapproved,
					'S_POSTS_UNAPPROVED' => $posts_unapproved,
					'S_HAS_POLL' => ($row['poll_start']) ? true : false,
					'S_POST_ANNOUNCE' => ($row['topic_type'] == POST_ANNOUNCE) ? true : false,
					'S_POST_GLOBAL' => ($row['topic_type'] == POST_GLOBAL) ? true : false,
					'S_POST_STICKY' => ($row['topic_type'] == POST_STICKY) ? true : false,
					'S_TOPIC_LOCKED' => ($row['topic_status'] == ITEM_LOCKED) ? true : false,
					'S_TOPIC_MOVED' => ($row['topic_status'] == ITEM_MOVED) ? true : false,
					'U_NEWEST_POST' => $unread_topic ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' .  $forum_id . '&amp;t=' . $topic_id . '&amp;view=unread#unread') : '',

					'U_VIEW_TOPIC' => $view_topic_url,
					'U_VIEW_FORUM' => $view_forum_url,
					'U_MCP_REPORT' => append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=reports&amp;f=' . $forum_id . '&amp;t=' . $topic_id, true, $user->session_id),
					'U_MCP_QUEUE' => $u_mcp_queue,
					'S_TOPIC_TYPE_SWITCH' => ($s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test,
				);
				if ($display_last_post) {
					$tpl_data += array(
						'TOPIC_LAST_POST_TITLE' => !empty($row['topic_last_post_subject']) ? censor_text($row['topic_last_post_subject']) :  $row['topic_title'],
						// www.phpBB-SEO.com SEO TOOLKIT BEGIN
						'U_MINI_POST' => !empty($phpbb_seo->seo_opt['no_dupe']['on']) ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id&amp;start=" . @intval($phpbb_seo->seo_opt['topic_last_page'][$topic_id]) ) . '#p' . $row['topic_last_post_id'] : append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'p=' . $row['topic_last_post_id'] . (($row['topic_type'] == POST_GLOBAL) ? '&amp;f=' . $forum_id : '')) . '#p' . $row['topic_last_post_id'],
						// www.phpBB-SEO.com SEO TOOLKIT END
					);
				}
				if ($display_user_info) {
					$tpl_data += array(
						'TOPIC_AUTHOR_FULL' => get_username_string($display_user_link_key, $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
						'LAST_POST_SUBJECT' => censor_text($row['topic_last_post_subject']),
						'LAST_POST_AUTHOR' => get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
						'LAST_POST_AUTHOR_COLOUR' => get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
						'LAST_POST_AUTHOR_FULL' => get_username_string($display_user_link_key, $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
						'U_LAST_POST_AUTHOR' => $display_user_link ? get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']) : '',
						'U_TOPIC_AUTHOR' => $display_user_link ? get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']) : '',
					);
				}
				$template->assign_block_vars('topicrow', $tpl_data);
				unset($tpl_data);
				$s_type_switch = /*($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 :*/ 0;
			}
			unset($topic_datas);
		}
		$map_desc = false;
		$rules_info = array('forum_rules' => false, 'forum_rules_link' => false);
		// In case we are listing category's children
		if ($master->call['cat_forum']) {
			$forum_id = $master->call['cat_forum'];
		}
		if ($master->call['display_desc']) {
			$map_desc = !empty($master->module_config['html_site_desc']) ? $master->module_config['html_site_desc'] : '';
			if ($master->call['single_forum'] || $master->call['cat_forum']) {
				$map_desc = $master->generate_forum_info($master->forum_datas[$forum_id]);
			}
		}
		if ($master->call['display_rules'] && ($master->call['single_forum'] || $master->call['cat_forum']) ) {
			$rules_info = $master->generate_forum_info($master->forum_datas[$forum_id], 'rules');
		}
		$tpl_data = array(
			'H1_TOPICS' => $master->outputs['page_title'],
			'DISPLAY_TOPICS_H1' => $display_link,
			'U_TOPICS' => $display_link ? append_sid($display_file) : false,
			'DISPLAY_TOPICS' => $has_result,
			'DISPLAY_LAST_POST' => $display_last_post,
			'NEWEST_POST_IMG' => $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
			'LAST_POST_IMG' => $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			'MAP_DESC' => $map_desc,
			'MAP_RULES' => $rules_info['forum_rules'],
			'MAP_RULES_LINK' => $rules_info['forum_rules_link'],
			'T_ICONS_PATH' => "{$phpbb_root_path}{$config['icons_path']}/",
			'NEWS_IMG_SRC' => $master->gym_master->path_config['gym_img_url'] . 'html_news.gif',
		);
		if ($master->call['single_forum'] || $master->call['cat_forum']) {
			$tpl_data += array(
				'FORUM_NEWS' => sprintf($user->lang['HTML_NEWS_OF'], $master->forum_datas[$forum_id]['forum_name']),
				'FORUM_NEWS_URL' => $master->module_config['html_allow_cat_news'] ? append_sid($master->gym_master->html_build_url('html_forum_cat_news', $phpbb_seo->seo_url['forum'][$forum_id], $forum_id)) : '',
				'FORUM_URL' => $master->forum_datas[$forum_id]['forum_url'],
				'FORUM_NAME' => $master->forum_datas[$forum_id]['forum_name'],
				'S_SINGLE_FORUM' => $master->call['cat_forum'] ? false : true,
			);
		} else {
			$tpl_data += array(
				'FORUM_NEWS' => sprintf($user->lang['HTML_NEWS_OF'], $master->module_config['html_sitename']),
				'FORUM_NEWS_URL' => $master->module_config['html_allow_news'] ? append_sid($master->module_config['html_url'] . $master->url_settings['html_forum_news']) : '',
				'FORUM_URL' => append_sid("{$phpbb_root_path}index.$phpEx"),
				'FORUM_NAME' => $master->module_config['html_sitename'],
			);
		}
		$template->assign_vars($tpl_data);
		unset($tpl_data);
		if ($display_pagination) {
			$l_total_topic_s = ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';
			$template->assign_vars(array(
				'DISPLAY_PAGINATION'	=> generate_pagination(append_sid($display_file), $total_topics, $limit, $start),
				'DISPLAY_PAGE_NUMBER'	=> on_page($total_topics, $limit, $start),
				'DISPLAY_TOTAL_TOPICS' => sprintf($user->lang[$l_total_topic_s], $total_topics),
			));
		}
	}
}
?>