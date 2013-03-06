<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: display_forums.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* display_forums Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class display_forums {
	function display_forums(&$master) {
		global $user, $template, $config, $phpEx, $db, $auth, $cache, $phpbb_root_path;
		global $phpbb_seo;
		static $tpl = array(
			'link' => '<a href="%1$s" title="%3$s">%2$s</a>',
			'img' => '<img src="%1$s" alt="%2$s"/>'
		);
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
		$forum_sql = &$master->call['forum_sql'];
		$forum_read_auth = & $master->actions['auth_view_read'];
		$forum_list_auth = & $master->actions['auth_view_list'];
		if (!$display_tracking) {
			$load_db_lastread = $load_anon_lastread = false;
		} else {
			$load_db_lastread = (boolean) ($config['load_db_lastread'] && $user->data['is_registered']);
			$load_anon_lastread = (boolean) ($config['load_anon_lastread'] || $user->data['is_registered']);
		}
		// Do some reset
		$forum_datas = $forum_ids = $sub_forums = array();
		$sql_array = array(
			'SELECT'	=> 'f.*',
			'FROM'		=> array(
				FORUMS_TABLE	=> 'f',
			),
			'LEFT_JOIN' => array(),
			'WHERE' => $forum_sql,
			'ORDER_BY' => 'f.left_id ASC',
		);
		// www.phpBB-SEO.com SEO TOOLKIT BEGIN -> no dupe
		if (!empty($phpbb_seo->seo_opt['no_dupe']['on'])) {
			$sql_array['SELECT'] .= ', t.topic_id, t.topic_title, t.topic_replies, t.topic_replies_real, t.topic_status, t.topic_type, t.topic_moved_id' . (!empty($phpbb_seo->seo_opt['sql_rewrite']) ? ', t.topic_url ' : ' ');
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(TOPICS_TABLE => 't'),
				'ON'	=> "t.topic_last_post_id = f.forum_last_post_id"
			);
		}
		// www.phpBB-SEO.com SEO TOOLKIT END -> no dupe
		if ($load_db_lastread) {
			$sql_array['SELECT'] .= ', ft.mark_time as forum_mark_time';
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(FORUMS_TRACK_TABLE => 'ft'),
				'ON'	=> 'ft.user_id = ' . $user->data['user_id'] . ' AND ft.forum_id = f.forum_id'
			);
		} elseif ($load_anon_lastread && empty($master->tracking_topics)) {
			$master->tracking_topics = (isset($_COOKIE[$config['cookie_name'] . $tracking_cookie_name])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . $tracking_cookie_name]) : $_COOKIE[$config['cookie_name'] . $tracking_cookie_name]) : '';
			$master->tracking_topics = ($master->tracking_topics) ? tracking_unserialize($master->tracking_topics) : array();
			if (!$user->data['is_registered']) {
				$user->data['user_lastmark'] = (isset($master->tracking_topics['l'])) ? (int) (base_convert($master->tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
			}
		}
		$right = 0;
		$level_store = array(0 => 0);
		$processed = array();
		$level = $last_level = 0;
		$html = $html_before = $html_after = '';
		$separator = ' &nbsp; ';
		$news_img = sprintf($tpl['img'], $master->gym_master->path_config['gym_img_url'] . 'html_news.gif', $user->lang['HTML_NEWS']);
		$map_img = sprintf($tpl['img'], $master->gym_master->path_config['gym_img_url'] . 'maps-icon.gif', $user->lang['HTML_MAP']);
		$subf_img = $user->img('subforum_read', 'NO_NEW_POSTS');
		$sql = $db->sql_build_query('SELECT', $sql_array);
		unset($sql_array);
		$result = $db->sql_query_limit($sql, 600);
		while ($row = $db->sql_fetchrow($result)) {
			$forum_id = (int) $row['forum_id'];
			//@TODO Find why in hell the above query could return more than one row per forum
			if (isset($processed[$forum_id])) {
				continue;
			}
			$processed[$forum_id] = $forum_id;
			$is_cat = $row['parent_id'] == 0 ? true : false;
			if (empty($master->forum_datas[$forum_id])) {
				// www.phpBB-SEO.com SEO TOOLKIT BEGIN
				$phpbb_seo->seo_url['forum'][$forum_id] = $phpbb_seo->set_url($row['forum_name'], $forum_id, $phpbb_seo->seo_static['forum']);
				// www.phpBB-SEO.com SEO TOOLKIT END
				$master->forum_datas[$forum_id] = array_merge($row,
					array(
						'm_approve' => $auth->acl_get('m_approve', $forum_id),
						'forum_name' => !empty($row['forum_name']) ? $row['forum_name'] : '',
						'forum_status' => !empty($row['forum_status']) ? $row['forum_status'] : '',
						'forum_last_post_time' => !empty($row['forum_last_post_time']) ? $row['forum_last_post_time'] : 0,
						'enable_icons' => !empty($row['enable_icons']) ? $row['enable_icons'] : 0,
						'forum_url' => append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id"),
					)
				);
				if ($load_db_lastread) {
					$master->forum_tracking_info[$forum_id] = !empty($row['forum_mark_time']) ? $row['forum_mark_time'] : $user->data['user_lastmark'];
				} elseif ($load_anon_lastread) {
					$master->forum_tracking_info[$forum_id] = isset($master->tracking_topics['f'][$forum_id]) ? (int) (base_convert($master->tracking_topics['f'][$forum_id], 36, 10) + $config['board_startdate']) : $user->data['user_lastmark'];
				}
			}
			$master->forum_datas[$forum_id]['forum_news_url'] = $master->forum_datas[$forum_id]['forum_map_url'] = '';
			if (!isset($root_forum_id)) {
				$root_forum_id = $forum_id;
				$sub_forums[$root_forum_id] = '';
			}
			$parent_id = (int) $row['parent_id'];
			if ($row['left_id'] < $right) {
				$level++;
				$level_store[$parent_id] = $level;
			} else if ($row['left_id'] > $right + 1) {
				if (isset($level_store[$parent_id])) {
					$level = $level_store[$parent_id];
				} else {
					$level = 0;
				}
			}
			$right = (int) $row['right_id'];
			if ($level > 1) { // sub forums
				if ($level > $last_level) { // going one or several level down
					$diff = $level - $last_level;
					$html_before = str_repeat("\n<ul><li>", $diff );
					$html_after = "";
				}
				if ($level < $last_level) { // Going one or several level up
					$diff = $last_level - $level;
					$html_before = str_repeat("</li></ul>\n", $diff ) . "</li>\n<li>";
					$html_after = "";
				}
				if ($level == $last_level) { // Adding a link at the same level
					$html_before = isset($forum_list_auth[$forum_id]) ? "</li>\n<li>" : '';
					$html_after = "";
				}
				if ($display_tracking) {
					$forum_unread = (isset($master->forum_tracking_info[$forum_id]) && $master->forum_datas[$forum_id]['forum_last_post_time'] > $master->forum_tracking_info[$forum_id]) ? true : false;
					if ($forum_unread) {
						$folder_image = 'subforum_unread';
						$folder_alt = 'NEW_POSTS';
					} else {
						$folder_image = 'subforum_read';
						$folder_alt = 'NO_NEW_POSTS';
					}
					$subf_img = $user->img($folder_image, $folder_alt);
				}
				$link = '';
				if (isset($forum_list_auth[$forum_id])) {
					if (!empty($row['forum_topics']) && (isset($forum_read_auth[$forum_id]) || $is_cat)) {
						if ($master->module_config['html_allow_cat_news']) {
							$title = sprintf($user->lang['HTML_NEWS_OF'], $row['forum_name']);
							$link = isset($forum_read_auth[$forum_id]) ? sprintf($tpl['link'], append_sid($master->module_config['html_url'] . $master->gym_master->html_build_url('html_forum_cat_news', $phpbb_seo->seo_url['forum'][$forum_id], $forum_id)), $news_img . ' ' . $title, $title) : $title;
						}
						if ($master->module_config['html_allow_cat_map']) {
							$title = sprintf($user->lang['HTML_MAP_OF'], $row['forum_name']);
							$link .= $separator . (isset($forum_read_auth[$forum_id]) ? sprintf($tpl['link'], append_sid($master->module_config['html_url'] . $master->gym_master->html_build_url('html_forum_cat_map', $phpbb_seo->seo_url['forum'][$forum_id], $forum_id)), $map_img . ' ' . $title, $title) : $title);
						}
					} else {
						$title = $link = '<b>' . $row['forum_name'] . '</b>';
					}
				}
				$sub_forums[$root_forum_id] .= $html_before . $link . $html_after;

			} else {
				$forum_datas[$forum_id] = array_merge(array('level' => $level), $row);
				$forum_ids[$forum_id] = $forum_id;
				if (($level < $last_level)) { // We went up in this root forum
					$_level = $level > 0 ? 0 : 1;
					$sub_forums[$root_forum_id] .= str_repeat("</li></ul>\n", ($last_level - $level - $_level));
				}
				if ($level == 1) { // next root forum
					$root_forum_id = $forum_id;
					$sub_forums[$root_forum_id] = '';
				}
			}
			$last_level = $level;
		}
		unset($processed);
		if (!empty($sub_forums[$root_forum_id]) && ($last_level > 1)) { // In case we need to close the last sub forum tag
			$sub_forums[$root_forum_id] .= str_repeat("</li></ul>\n", ($last_level - 1));
		}
		$db->sql_freeresult($result);
		// Let's go
		$has_result = false;
		if (!empty($forum_datas)) {
			$has_result = true;
			// Grab icons
			if (empty($master->icons)) {
				$master->icons = $cache->obtain_icons();
			}
			$s_type_switch = 0;
			$last_catless = true;
			$root_data = array('forum_id' => 0);
			$s_is_cat = true;
			$level = 0;
			$folder_alt = 'NO_NEW_POSTS';
			$folder_image = 'forum_read';
			$forum_folder_img = $user->img($folder_image, $folder_alt);
			$forum_folder_img_src = $user->img($folder_image, $folder_alt, false, '', 'src');
			// Let's go
			foreach($forum_ids as $forum_id) {
				$row = &$forum_datas[$forum_id];
				$catless = $row['level'] == 0 ? true : false;
				$is_cat = $row['parent_id'] == 0 ? true : false;
				if (!isset($forum_list_auth[$forum_id]) || $row['forum_type'] == FORUM_LINK) {
					continue;
				}
				$forum_map_link = $forum_news_link = $forum_map_title = $forum_news_title = '';
				if (!empty($row['forum_topics']) && (isset($forum_read_auth[$forum_id]) || $is_cat)) {
					if ($master->module_config['html_allow_cat_map']) {
						$forum_map_title = sprintf($user->lang['HTML_MAP_OF'], $row['forum_name']);
						$forum_map_link = sprintf($tpl['link'], append_sid($master->module_config['html_url'] . $master->gym_master->html_build_url('html_forum_cat_map', $phpbb_seo->seo_url['forum'][$forum_id], $forum_id)), $map_img . ' ' . $forum_map_title, $forum_map_title);
					}
					if ($master->module_config['html_allow_cat_news']) {
						$forum_news_title = sprintf($user->lang['HTML_NEWS_OF'], $row['forum_name']);
						$forum_news_link = sprintf($tpl['link'], append_sid($master->module_config['html_url'] . $master->gym_master->html_build_url('html_forum_cat_news', $phpbb_seo->seo_url['forum'][$forum_id], $forum_id)), $news_img . ' ' . $forum_news_title, $forum_news_title);
					}
				} else {
					$forum_news_title = $forum_news_link = '<b>' . $row['forum_name'] . '</b>';
					$forum_news_link .= '<br/>';
				}
				$forum_unread = (isset($master->forum_tracking_info[$forum_id]) && $master->forum_datas[$forum_id]['forum_last_post_time'] > $master->forum_tracking_info[$forum_id]) ? true : false;
				if ($display_topic_status) {
					// Which folder should we display?
					$folder_alt = ($forum_unread) ? 'NEW_POSTS' : 'NO_NEW_POSTS';
					if (!empty($sub_forums[$forum_id])) {
						$folder_image = ($forum_unread) ? 'forum_unread_subforum' : 'forum_read_subforum';
					} else {
						if ($row['forum_status'] == ITEM_LOCKED) {
							$folder_image = ($forum_unread) ? 'forum_unread_locked' : 'forum_read_locked';
							$folder_alt = 'FORUM_LOCKED';
						} else if ($row['forum_type'] == FORUM_POST) {
							$folder_image = ($forum_unread) ? 'forum_unread' : 'forum_read';
						} else {
							$folder_image = 'forum_link';
						}

					}
					$forum_folder_img = $user->img($folder_image, $folder_alt);
					$forum_folder_img_src = $user->img($folder_image, $folder_alt, false, '', 'src');
				}
				$tpl_data = array(
					'S_IS_CAT' => $is_cat,
					'S_NO_CAT' => $catless && !$last_catless ? true : false,
					'FORUM_ID' => $forum_id,
					'FORUM_NAME' => $forum_map_title,
					'FORUM_NEWS' => $forum_news_title,
					'FORUM_MAP_LINK' => $forum_map_link,
					'FORUM_NEWS_LINK' => $forum_news_link,
				);
				if (!$is_cat) {
					$tpl_data += array(
						'FORUM_DESC' => ($master->call['display_desc'] && !$is_cat) ? $master->generate_forum_info($row) : '',
						'FORUM_FOLDER_IMG' => $forum_folder_img,
						'FORUM_FOLDER_IMG_SRC' => $forum_folder_img_src,
						'FORUM_FOLDER_IMG_ALT' => $user->lang[$folder_alt],
						'FORUM_IMAGE' => ($row['forum_image']) ? '<img src="' . $phpbb_root_path . $row['forum_image'] . '" alt="' . $user->lang[$folder_alt] . '" />' : '',
						'FORUM_IMAGE_SRC' => ($row['forum_image']) ? $phpbb_root_path . $row['forum_image'] : '',
						'SUBFORUMS' => !empty($sub_forums[$forum_id]) ? $sub_forums[$forum_id] : '',
					);
					if ($display_last_post) {
						// Create last post link information, if appropriate
						if ($row['forum_last_post_id']) {
							$last_post_subject = $row['forum_last_post_subject'];
							$last_post_time = $user->format_date($row['forum_last_post_time']);
							if (!empty($phpbb_seo->seo_opt['no_dupe']['on']) && !empty($row['topic_id']) && !$row['forum_password']) {
								if ($row['topic_status'] == ITEM_MOVED) {
									$row['topic_id'] = $row['topic_moved_id'];
								}
								$topic_id = (int) $row['topic_id'];
								$row['topic_title'] = censor_text($row['topic_title']);
								// www.phpBB-SEO.com SEO TOOLKIT BEGIN
								$phpbb_seo->prepare_iurl($row, 'topic', $row['topic_type'] == POST_GLOBAL ? $phpbb_seo->seo_static['global_announce'] : $phpbb_seo->seo_url['forum'][$forum_id]);
								// www.phpBB-SEO.com SEO TOOLKIT END
								$last_post_url =  append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id&amp;start=" . @intval($phpbb_seo->seo_opt['topic_last_page'][$topic_id]) ) . '#p' . $row['forum_last_post_id'];
								$last_post_link = '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id") . '" title="' . $row['topic_title'] . ' : ' . $row['forum_name'] . '">' . $row['topic_title'] . '</a>';
							} else {
								$last_post_url =  append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;p=" . $row['forum_last_post_id']) . '#p' . $row['forum_last_post_id'];
								$last_post_link = '';
							}
						} else {
							$last_post_subject = $last_post_time = $last_post_url = $last_post_link = '';
						}
						$tpl_data += array(
							'LAST_POST_SUBJECT' => censor_text($last_post_subject),
							'LAST_POST_TIME' => $last_post_time,
							'LAST_POST_LINK' => $last_post_link,
							'U_LAST_POST' => $last_post_url,
						);
						if ($display_user_info) {
							$tpl_data += array(
								'LAST_POSTER' => get_username_string('username', $row['forum_last_poster_id'], $row['forum_last_poster_name'], $row['forum_last_poster_colour']),
								'LAST_POSTER_COLOUR' => get_username_string('colour', $row['forum_last_poster_id'], $row['forum_last_poster_name'], $row['forum_last_poster_colour']),
								'LAST_POSTER_FULL' => get_username_string($display_user_link_key, $row['forum_last_poster_id'], $row['forum_last_poster_name'], $row['forum_last_poster_colour']),
							);
						}
					}
				}
				$template->assign_block_vars('forumrow', $tpl_data);
				$last_catless = $catless;
				unset($forum_datas[$forum_id]);
			}
		}
		$map_desc = false;
		if ($master->call['display_desc']) {
			$map_desc = !empty($master->module_config['html_site_desc']) ? $master->module_config['html_site_desc'] : '';
		}
		$template->assign_vars(array(
			'H1_FORUMS' => $master->module_config['html_sitename'],
			'DISPLAY_FORUMS_H1' => $display_link,
			'U_FORUMS' => $display_link ? append_sid("{$phpbb_root_path}index.$phpEx") : false,
			'FORUM_MAP_URL' => $master->module_config['html_allow_map'] ? append_sid($master->module_config['html_url'] . $master->url_settings['html_forum_map']) : '',
			'FORUM_NEWS' => sprintf($user->lang['HTML_NEWS_OF'], $master->module_config['html_sitename']),
			'NEWS_IMG_SRC' => $master->gym_master->path_config['gym_img_url'] . 'html_news.gif',
			'FORUM_NEWS_URL' => $master->module_config['html_allow_news'] ? append_sid($master->module_config['html_url'] . $master->url_settings['html_forum_news']) : '',
			'DISPLAY_FORUMS' => $has_result,
			'DISPLAY_USER_INFO' => $display_user_info,
			'DISPLAY_LAST_POST' => $display_last_post,
			'NEWEST_POST_IMG' => $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
			'LAST_POST_IMG' => $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			'MAP_DESC' => $map_desc,
		));
	}
}
?>