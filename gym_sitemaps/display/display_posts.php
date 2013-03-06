<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: display_posts.php 170 2009-11-20 09:56:31Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* display_posts Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class display_posts {
	function display_posts(&$master) {
		global $user, $template, $config, $phpEx, $db, $auth, $phpbb_root_path, $cache;
		static $bbcode;
		static $display_orders = array('first' => 't.topic_id', 'last' => 't.topic_last_post_time');
		global $phpbb_seo;
		// Usefull for multi bb topic & forum tracking
		// Leave default for single forum eg : '_track'
		$tracking_cookie_name = (defined('XLANG_AKEY') ? XLANG_AKEY : '') . '_track';
		$forum_read_auth = & $master->actions['auth_view_read'];
		// Specific options
		$display_file = &$master->call['display_file'];
		$display_user_info = &$master->call['display_user_info'];
		$display_user_link = !empty($master->call['display_user_link']) ? true : false;
		$display_user_link_key = $display_user_link ? 'full' : 'no_profile';
		$display_link = &$master->call['display_link'];
		$display_pagination = &$master->call['display_pagination'];
		$display_tracking = &$master->call['display_tracking'];
		$display_sig = !empty($master->call['display_sig']) ? (boolean) ($config['allow_sig'] && $user->optionget('viewsigs')) : false;
		$display_order = isset($display_orders[$master->call['display_order']]) ? $display_orders[$master->call['display_order']] : $display_orders['first'];
		$display_post_buttons = &$master->call['display_post_buttons'];
		$display_sumarize = &$master->call['display_sumarize'];
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
		$bbcode_bitfield = '';
		// Do some reset
		$topic_datas = $topic_ids = $forum_ids = $user_cache = $id_cache = $post_datas = $forum_datas = array();
		$forum_id = $master->call['forum_id'];
		$now = getdate(time() + $user->timezone + $user->dst - date('Z'));
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
		// First we parse the basic data
		while ($row = $db->sql_fetchrow($result)) {
			$forum_id = (int) $row['forum_id'];
			$topic_id = (int) $row['topic_id'];
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
			// Topic post count
			$row['replies'] = !empty($master->forum_datas[$forum_id]['m_approve']) ? $row['topic_replies_real'] : $row['topic_replies'];
			$row['enable_icons'] = !empty($master->forum_datas[$forum_id]['enable_icons']);
			// www.phpBB-SEO.com SEO TOOLKIT BEGIN
			$phpbb_seo->prepare_iurl($row, 'topic', $row['topic_type'] == POST_GLOBAL ? $phpbb_seo->seo_static['global_announce'] : $phpbb_seo->seo_url['forum'][$forum_id]);
			// www.phpBB-SEO.com SEO TOOLKIT END
			$topic_datas[$forum_id][$topic_id] = $row;
			// @TODO deal with last post case ?
			$topic_ids[$topic_id] = /*$master->call['display_order'] == 'first' ?*/ $row['topic_first_post_id'] /*: $row['topic_last_post_id']*/;
			$forum_ids[$topic_id] = $forum_id;
		}
		$db->sql_freeresult($result);
		unset($forum_datas);
		// Let's go
		$has_result = false;
		if (!empty($topic_datas)) {
			$has_result = true;
			$bbcode_filter = false;
			if (!class_exists('bbcode')) {
				global $phpbb_root_path, $phpEx;
				include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
			}
			$patterns = $replaces = array();
			if ( !empty($master->module_config['html_msg_filters']['pattern']) ) {
				$patterns = $master->module_config['html_msg_filters']['pattern'];
				$replaces = $master->module_config['html_msg_filters']['replace'];
				$bbcode_filter = true;
			}
			// Grab ranks
			$ranks = $cache->obtain_ranks();
			// Grab icons
			if (empty($master->icons)) {
				$master->icons = $cache->obtain_icons();
			}
			// Go ahead and pull all data for these topics
			$sql_array = array();
			$sql_array['SELECT'] = $sql_array['WHERE'] = '';
			if ($display_user_info) {
				$sql_array['SELECT'] = 'u.*, z.friend, z.foe, ';
				$sql_array['FROM'] = array(USERS_TABLE => 'u');
				$sql_array['LEFT_JOIN'] = array(
					array(
						'FROM'	=> array(ZEBRA_TABLE => 'z'),
						'ON'	=> 'z.user_id = ' . $user->data['user_id'] . ' AND z.zebra_id = p.poster_id'
					)
				);
				$sql_array['WHERE'] = 'AND u.user_id = p.poster_id';
			}
			$sql_array['SELECT'] .= 'p.*';
			$sql_array['FROM'][POSTS_TABLE] = 'p';
			$sql_array['WHERE'] = $db->sql_in_set('p.post_id', $topic_ids) . $sql_array['WHERE'];
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result)) {
				$forum_id = (int) $row['forum_id'];
				$topic_id = (int) $row['topic_id'];
				// Define the global bbcode bitfield, will be used to load bbcodes
				$bbcode_bitfield = $bbcode_bitfield | base64_decode($row['bbcode_bitfield']);
				// Only compute profile data if required
				if ($display_user_info) {
					// www.phpBB-SEO.com SEO TOOLKIT BEGIN
					$phpbb_seo->set_user_url( $row['username'], $row['poster_id'] );
					// www.phpBB-SEO.com SEO TOOLKIT END
					// Is a signature attached? Are we going to display it?
					if ($display_sig && $row['enable_sig']) {
						$bbcode_bitfield = $bbcode_bitfield | base64_decode($row['user_sig_bbcode_bitfield']);
					}
				} else {
					// @TODO deal with last post case ?
					$row['user_id'] = $row['poster_id'];
					$row['username'] = $topic_datas[$forum_id][$topic_id]['topic_first_poster_name'];
					$row['user_colour'] = $topic_datas[$forum_id][$topic_id]['topic_first_poster_colour'];
				}
				$poster_id = (int) $row['poster_id'];
				$post_datas[$forum_id][$topic_id] = array(
						'hide_post' => false,
						'post_id' => $row['post_id'],
						'post_time' => $row['post_time'],
						'user_id' => $row['user_id'],
						'username' => $row['username'],
						'user_colour' => $row['user_colour'],
						'topic_id' => $row['topic_id'],
						'forum_id' => $row['forum_id'],
						'post_subject' => $row['post_subject'],
						'post_edit_count' => $row['post_edit_count'],
						'post_edit_time' => $row['post_edit_time'],
						'post_edit_reason' => $row['post_edit_reason'],
						'post_edit_user' => $row['post_edit_user'],
						// Make sure the icon actually exists
						'icon_id' => (isset($master->icons[$row['icon_id']]['img'], $master->icons[$row['icon_id']]['height'], $master->icons[$row['icon_id']]['width'])) ? $row['icon_id'] : 0,
						'post_attachment' => $row['post_attachment'],
						'post_approved' => $row['post_approved'],
						'post_reported' => $row['post_reported'],
						'post_username' => $row['post_username'],
						'post_text' => $row['post_text'],
						'bbcode_uid' => $row['bbcode_uid'],
						'bbcode_bitfield' => $row['bbcode_bitfield'],
						'enable_smilies' => $row['enable_smilies'],
						'enable_sig' => $row['enable_sig'],
						'friend' => false,
						'foe' => false,
				);
				// Cache various user specific data ... so we don't have to recompute
				// this each time the same user appears on this page
				if (!isset($user_cache[$poster_id])) {
					if ($poster_id == ANONYMOUS || !$display_user_info) {
						$user_cache[$poster_id] = array(
							'joined' => '',
							'posts' => '',
							'from' => '',
							'sig' => '',
							'sig_bbcode_uid' => '',
							'sig_bbcode_bitfield' => '',
							'online' => false,
							'avatar' => '',
							'rank_title' => '',
							'rank_image' => '',
							'rank_image_src' => '',
							'profile' => '',
							'pm' => '',
							'email' => '',
							'www' => '',
							'icq_status_img' => '',
							'icq' => '',
							'aim' => '',
							'msn' => '',
							'yim' => '',
							'jabber' => '',
							'search' => '',
							'age' => '',
							'username' => $row['username'],
							'user_colour' => $row['user_colour'],
							'warnings' => 0,
							'allow_pm' => 0,
						);
					} else {
						$user_sig = '';
						// We add the signature to every posters entry because enable_sig is post dependant
						if ($display_sig && $row['user_sig'] ) {
							$user_sig = $row['user_sig'];
						}
						$id_cache[] = $poster_id;
						$user_cache[$poster_id] = array(
							'joined' => $user->format_date($row['user_regdate']),
							'posts' => $row['user_posts'],
							'warnings' => (isset($row['user_warnings'])) ? $row['user_warnings'] : 0,
							'from' => (!empty($row['user_from'])) ? $row['user_from'] : '',
							'sig' => $user_sig,
							'sig_bbcode_uid' => (!empty($row['user_sig_bbcode_uid'])) ? $row['user_sig_bbcode_uid'] : '',
							'sig_bbcode_bitfield' => (!empty($row['user_sig_bbcode_bitfield'])) ? $row['user_sig_bbcode_bitfield'] : '',
							'viewonline' => $row['user_allow_viewonline'],
							'allow_pm' => $row['user_allow_pm'],
							'avatar' => ($user->optionget('viewavatars')) ? $master->gym_master->get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']) : '',
							'age' => '',
							'rank_title' => '',
							'rank_image' => '',
							'rank_image_src' => '',
							'username' => $row['username'],
							'user_colour' => $row['user_colour'],
							'online' => false,
							'profile' => append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=$poster_id"),
							'www' => $row['user_website'],
							'aim' => ($row['user_aim'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=aim&amp;u=$poster_id") : '',
							'msn' => ($row['user_msnm'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=msnm&amp;u=$poster_id") : '',
							'yim' => ($row['user_yim']) ? 'http://edit.yahoo.com/config/send_webmesg?.target=' . urlencode($row['user_yim']) . '&amp;.src=pg' : '',
							'jabber' => ($row['user_jabber'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=jabber&amp;u=$poster_id") : '',
							'search' => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.$phpEx", "author_id=$poster_id&amp;sr=posts") : '',
						);
						$master->gym_master->get_user_rank($row['user_rank'], $row['user_posts'], $user_cache[$poster_id]['rank_title'], $user_cache[$poster_id]['rank_image'], $user_cache[$poster_id]['rank_image_src']);
						if (!empty($row['user_allow_viewemail']) || $auth->acl_get('a_email')) {
							$user_cache[$poster_id]['email'] = ($config['board_email_form'] && $config['email_enable']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=email&amp;u=$poster_id") : (($config['board_hide_emails'] && !$auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
						} else {
							$user_cache[$poster_id]['email'] = '';
						}
						if (!empty($row['user_icq'])) {
							$user_cache[$poster_id]['icq'] = 'http://www.icq.com/people/webmsg.php?to=' . $row['user_icq'];
							$user_cache[$poster_id]['icq_status_img'] = '<img src="http://web.icq.com/whitepages/online?icq=' . $row['user_icq'] . '&amp;img=5" width="18" height="18" alt="" />';
						} else {
							$user_cache[$poster_id]['icq_status_img'] = '';
							$user_cache[$poster_id]['icq'] = '';
						}
						if ($config['allow_birthdays'] && !empty($row['user_birthday'])) {
							list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $row['user_birthday']));
							if ($bday_year) {
								$diff = $now['mon'] - $bday_month;
								if ($diff == 0) {
									$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
								} else {
									$diff = ($diff < 0) ? 1 : 0;
								}
								$user_cache[$poster_id]['age'] = (int) ($now['year'] - $bday_year - $diff);
							}
						}
					}
					$user_cache[$poster_id]['post_author_full'] = get_username_string($display_user_link_key, $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
					$user_cache[$poster_id]['post_author_colour'] = get_username_string('colour', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
					$user_cache[$poster_id]['post_author'] = get_username_string('username', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
					$user_cache[$poster_id]['u_post_author'] = $display_user_link ? get_username_string('profile', $poster_id, $row['username'], $row['user_colour'], $row['post_username']) : '';
				}
			}
			$db->sql_freeresult($result);
			// Load custom profile fields
			if ($display_user_info && $config['load_cpf_viewtopic']) {
				include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
				$cp = new custom_profile();
				// Grab all profile fields from users in id cache for later use - similar to the poster cache
				$profile_fields_cache = $cp->generate_profile_fields_template('grab', $id_cache);
			}
			// Generate online information for user
			if (@$master->call['display_online'] && sizeof($id_cache)) {
				$sql = 'SELECT session_user_id, MAX(session_time) as online_time, MIN(session_viewonline) AS viewonline
					FROM ' . SESSIONS_TABLE . '
					WHERE ' . $db->sql_in_set('session_user_id', $id_cache) . '
					GROUP BY session_user_id';
				$result = $db->sql_query($sql);
				$update_time = $config['load_online_time'] * 60;
				while ($row = $db->sql_fetchrow($result)) {
					$user_cache[$row['session_user_id']]['online'] = (time() - $update_time < $row['online_time'] && (($row['viewonline']) || $auth->acl_get('u_viewonline'))) ? true : false;
				}
				$db->sql_freeresult($result);
			}
			unset($id_cache);
			// Instantiate BBCode
			if (empty($bbcode)) {
				$bbcode = new bbcode(base64_encode($bbcode_bitfield));
			} else {
				$bbcode->bbcode(base64_encode($bbcode_bitfield));
			}
			$prev_post_id = '';
			// Parse messages
			foreach ($forum_ids as $topic_id => $forum_id) {
				if (!isset($post_datas[$forum_id][$topic_id])) {
					continue;
				}
				$row =& $post_datas[$forum_id][$topic_id];
				$topic_data =& $topic_datas[$forum_id][$topic_id];
				$poster_id = (int) $row['user_id'];
				$l_edited_by = $l_bumped_by = '';
				$s_first_unread = false;
				// End signature parsing, only if needed
				if (@$user_cache[$poster_id]['sig'] && $row['enable_sig'] && empty($user_cache[$poster_id]['sig_parsed'])) {
					$user_cache[$poster_id]['sig'] = censor_text($user_cache[$poster_id]['sig']);
					if ($user_cache[$poster_id]['sig_bbcode_bitfield']) {
						if ( $bbcode_filter ) {
							$user_cache[$poster_id]['sig'] = preg_replace($patterns, $replaces, $user_cache[$poster_id]['sig']);
						}
						$bbcode->bbcode_second_pass($user_cache[$poster_id]['sig'], $user_cache[$poster_id]['sig_bbcode_uid'], $user_cache[$poster_id]['sig_bbcode_bitfield']);
					}
					$user_cache[$poster_id]['sig'] = bbcode_nl2br($user_cache[$poster_id]['sig']);
					$user_cache[$poster_id]['sig'] = $master->gym_master->smiley_text($user_cache[$poster_id]['sig'], !$master->module_config['html_allow_smilies']);
					$user_cache[$poster_id]['sig_parsed'] = true;
				} else { // Remove sig
					$user_cache[$poster_id]['sig'] = '';
				}
				// Parse the message and subject
				$message = &$row['post_text'];
				if ( $bbcode_filter ) {
					$message = preg_replace($patterns, $replaces, $message);
				}
				if ($display_sumarize > 0 ) {
					$message = $master->gym_master->summarize( $message, $display_sumarize, $master->call['display_sumarize_method'] );
					// Clean broken tag at the end of the message
					$message = preg_replace('`\<[^\<\>]*$`i', ' ...', $message);
					// Close broken bbcode tags requiring it, only quotes for now
					$master->gym_master->close_bbcode_tags($message, $row['bbcode_uid']);
				}
				// Parse the message and subject
				$message = censor_text($message);
				// Second parse bbcode here
				if ($row['bbcode_bitfield']) {
					$bbcode->bbcode_second_pass($message, $row['bbcode_uid'], $row['bbcode_bitfield']);
				}
				$message = bbcode_nl2br($message);
				$message = $master->gym_master->smiley_text($message, !$master->module_config['html_allow_smilies']);
				if ($display_sumarize > 0 ) { // Clean up
					static $find = array('`\<\!--[^\<\>]+--\>`Ui', '`\[\/?[^\]\[]*\]`Ui');
					$message = preg_replace($find, '', $message);
				}
				// Replace naughty words such as farty pants
				$row['post_subject'] = censor_text(!empty($row['post_subject']) ? $row['post_subject'] : $topic_data['topic_title']);
				// custom profile fields
				$cp_row = array();
				if ($display_user_info && $config['load_cpf_viewtopic']) {
					$cp_row = (isset($profile_fields_cache[$poster_id])) ? $cp->generate_profile_fields_template('show', false, $profile_fields_cache[$poster_id]) : array();
				}
				$post_unread = (isset($topic_tracking_info[$topic_id]) && $row['post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
				// Generate all the URIs ...
				if (!$s_global && !isset($master->module_config['global_exclude_list'][$forum_id])) {
					$view_topic_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id") . '#p' . $row['post_id'];
					$view_forum_url = $master->forum_datas[$forum_id]['forum_url'];
				} else {
					$view_topic_url = $view_forum_url = '';
				}
				$postrow = array(
					'FORUM_NAME' => !$s_global ? $master->forum_datas[$forum_id]['forum_name'] : '',
					'U_VIEW_FORUM' => $view_forum_url,
					'VIEWS' => $topic_data['topic_views'],
					'POST_DATE' => $user->format_date($row['post_time']),
					'POST_SUBJECT' => $row['post_subject'],
					'MESSAGE' => $message,
					'EDITED_MESSAGE' => $l_edited_by,
					'EDIT_REASON' => $row['post_edit_reason'],
					'BUMPED_MESSAGE' => $l_bumped_by,
					'MINI_POST_IMG' => ($post_unread) ? $user->img('icon_post_target_unread', 'NEW_POST') : $user->img('icon_post_target', 'POST'),
					'POST_ICON_IMG' => ($topic_data['enable_icons'] && !empty($row['icon_id'])) ? $master->icons[$row['icon_id']]['img'] : '',
					'POST_ICON_IMG_WIDTH' => ($topic_data['enable_icons'] && !empty($row['icon_id'])) ? $master->icons[$row['icon_id']]['width'] : '',
					'POST_ICON_IMG_HEIGHT' => ($topic_data['enable_icons'] && !empty($row['icon_id'])) ? $master->icons[$row['icon_id']]['height'] : '',
					'U_MCP_REPORT' => ($auth->acl_get('m_report', $forum_id)) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=report_details&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $user->session_id) : '',
					'U_MCP_APPROVE' => ($auth->acl_get('m_approve', $forum_id)) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=approve_details&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $user->session_id) : '',
					// www.phpBB-SEO.com SEO TOOLKIT BEGIN
					'U_MINI_POST' => $view_topic_url,
					'U_NEWEST_POST' => $post_unread ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' .  $forum_id . '&amp;t=' . $topic_id . '&amp;view=unread#unread') : '',
					// www.phpBB-SEO.com SEO TOOLKIT END
					//'U_NEXT_POST_ID' => ($i < $i_total && isset($rowset[$post_list[$i + 1]])) ? $rowset[$post_list[$i + 1]]['post_id'] : '',
					//'U_PREV_POST_ID' => $prev_post_id,

					'POST_ID' => $row['post_id'],
					'POSTER_ID' => $poster_id,
					'S_HAS_ATTACHMENTS' => (!empty($attachments[$row['post_id']])) ? true : false,
					'S_POST_UNAPPROVED' => ($row['post_approved']) ? false : true,
					'S_POST_REPORTED' => ($row['post_reported'] && $auth->acl_get('m_report', $forum_id)) ? true : false,
					'S_DISPLAY_NOTICE' => 0 /*$display_notice && $row['post_attachment']*/,
					'S_FRIEND' => ($row['friend']) ? true : false,
					'S_UNREAD_POST' => $post_unread,
					//'S_FIRST_UNREAD' => $s_first_unread,
					'S_CUSTOM_FIELDS' => (isset($cp_row['row']) && sizeof($cp_row['row'])) ? true : false,
					'S_TOPIC_POSTER' => ($topic_data['topic_poster'] == $poster_id) ? true : false,
					'S_IGNORE_POST' => ($row['hide_post']) ? true : false,
					// www.phpBB-SEO.com SEO TOOLKIT BEGIN
					'L_IGNORE_POST' => ($row['hide_post']) ? sprintf($user->lang['POST_BY_FOE'], get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']), '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id&amp;p={$row['post_id']}&amp;view=show") . '#p' . $row['post_id'] . '">', '</a>') : '',
					// www.phpBB-SEO.com SEO TOOLKIT END
					'REPLIES' => $topic_datas[$forum_id][$topic_id]['replies'],
				);
				if ($display_user_info) {
					$postrow += array(
						'POST_AUTHOR_FULL' => $user_cache[$poster_id]['post_author_full'],
						'POST_AUTHOR_COLOUR' => $user_cache[$poster_id]['post_author_colour'],
						'POST_AUTHOR' => $user_cache[$poster_id]['post_author'],
						'U_POST_AUTHOR' => $user_cache[$poster_id]['u_post_author'],
						'SIGNATURE' => $user_cache[$poster_id]['sig'],
						'RANK_TITLE' => $user_cache[$poster_id]['rank_title'],
						'RANK_IMG' => $user_cache[$poster_id]['rank_image'],
						'RANK_IMG_SRC' => $user_cache[$poster_id]['rank_image_src'],
						'POSTER_JOINED' => $user_cache[$poster_id]['joined'],
						'POSTER_POSTS' => $user_cache[$poster_id]['posts'],
						'POSTER_FROM' => $user_cache[$poster_id]['from'],
						'POSTER_AVATAR' => $user_cache[$poster_id]['avatar'],
						'POSTER_WARNINGS' => $user_cache[$poster_id]['warnings'],
						'POSTER_AGE' => $user_cache[$poster_id]['age'],
						'ICQ_STATUS_IMG' => $user_cache[$poster_id]['icq_status_img'],
						'ONLINE_IMG' => ($poster_id == ANONYMOUS || !$config['load_onlinetrack']) ? '' : (($user_cache[$poster_id]['online']) ? $user->img('icon_user_online', 'ONLINE') : $user->img('icon_user_offline', 'OFFLINE')),
						'S_ONLINE' => ($poster_id == ANONYMOUS || !$config['load_onlinetrack']) ? false : (($user_cache[$poster_id]['online']) ? true : false),
						'U_PROFILE' => $user_cache[$poster_id]['profile'],
						'U_SEARCH' => $user_cache[$poster_id]['search'],
						'U_PM' => ($poster_id != ANONYMOUS && $config['allow_privmsg'] && $auth->acl_get('u_sendpm') && ($user_cache[$poster_id]['allow_pm'] || $auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_'))) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;mode=compose&amp;action=quotepost&amp;p=' . $row['post_id']) : '',
						'U_EMAIL' => $user_cache[$poster_id]['email'],
						'U_WWW' => $user_cache[$poster_id]['www'],
						'U_ICQ' => $user_cache[$poster_id]['icq'],
						'U_AIM' => $user_cache[$poster_id]['aim'],
						'U_MSN' => $user_cache[$poster_id]['msn'],
						'U_YIM' => $user_cache[$poster_id]['yim'],
						'U_JABBER' => $user_cache[$poster_id]['jabber'],

					);
				}
				if ($display_post_buttons) {
					$postrow += array(
						'U_EDIT' => (!$user->data['is_registered']) ? '' : ((($user->data['user_id'] == $poster_id && $auth->acl_get('f_edit', $forum_id) && ($row['post_time'] > time() - ($config['edit_time'] * 60) || !$config['edit_time'])) || $auth->acl_get('m_edit', $forum_id)) ? append_sid("{$phpbb_root_path}posting.$phpEx", "mode=edit&amp;f=$forum_id&amp;p={$row['post_id']}") : ''),
						'U_QUOTE' => ($auth->acl_get('f_reply', $forum_id)) ? append_sid("{$phpbb_root_path}posting.$phpEx", "mode=quote&amp;f=$forum_id&amp;p={$row['post_id']}") : '',
						'U_INFO' => ($auth->acl_get('m_info', $forum_id)) ? append_sid("{$phpbb_root_path}mcp.$phpEx", "i=main&amp;mode=post_details&amp;f=$forum_id&amp;p=" . $row['post_id'], true, $user->session_id) : '',
						'U_DELETE' => (!$user->data['is_registered']) ? '' : ((($user->data['user_id'] == $poster_id && $auth->acl_get('f_delete', $forum_id) && $topic_data['topic_last_post_id'] == $row['post_id'] && ($row['post_time'] > time() - ($config['edit_time'] * 60) || !$config['edit_time'])) || $auth->acl_get('m_delete', $forum_id)) ? append_sid("{$phpbb_root_path}posting.$phpEx", "mode=delete&amp;f=$forum_id&amp;p={$row['post_id']}") : ''),
						'U_REPORT' => ($auth->acl_get('f_report', $forum_id)) ? append_sid("{$phpbb_root_path}report.$phpEx", 'f=' . $forum_id . '&amp;p=' . $row['post_id']) : '',
						'U_NOTES' => ($auth->acl_getf_global('m_')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=notes&amp;mode=user_notes&amp;u=' . $poster_id, true, $user->session_id) : '',
						'U_WARN' => ($auth->acl_get('m_warn') && $poster_id != $user->data['user_id'] && $poster_id != ANONYMOUS) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=warn&amp;mode=warn_post&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $user->session_id) : '',
					);
				}
				if (isset($cp_row['row']) && sizeof($cp_row['row'])) {
					$postrow = array_merge($postrow, $cp_row['row']);
				}
				// Dump vars into template
				$template->assign_block_vars('postrow', $postrow);
				if (!empty($cp_row['blockrow'])) {
					foreach ($cp_row['blockrow'] as $field_data) {
						$template->assign_block_vars('postrow.custom_fields', $field_data);
					}
				}
				// Display not already displayed Attachments for this post, we already parsed them. ;)
				if (!empty($attachments[$row['post_id']])) {
					foreach ($attachments[$row['post_id']] as $attachment) {
						$template->assign_block_vars('postrow.attachment', array(
							'DISPLAY_ATTACHMENT'	=> $attachment)
						);
					}
				}
				$prev_post_id = $row['post_id'];
				unset($topic_datas[$forum_id][$topic_id]);
			}
			unset($topic_datas, $user_cache);
		}
		$news_desc = false;
		$rules_info = array('forum_rules' => false, 'forum_rules_link' => false);
		// In case we are listing category's children
		if ($master->call['cat_forum']) {
			$forum_id = $master->call['cat_forum'];
		}
		if ($master->call['display_desc']) {
			$news_desc = !empty($master->module_config['html_site_desc']) ? $master->module_config['html_site_desc'] : '';
			if ($master->call['single_forum'] || $master->call['cat_forum']) {
				$news_desc = $master->generate_forum_info($master->forum_datas[$forum_id]);
			}
		}
		if ($master->call['display_rules'] && ($master->call['single_forum'] || $master->call['cat_forum']) ) {
			$rules_info = $master->generate_forum_info($master->forum_datas[$forum_id], 'rules');
		}
		$tpl_vars = array(
			'H1_POSTS' => $master->outputs['page_title'],
			'DISPLAY_POSTS_H1' => $display_link,
			'U_POSTS' => $display_link ? append_sid($display_file) : false,
			'DISPLAY_USER_INFO' => $display_user_info,
			'DISPLAY_POSTS' => $has_result,
			'DISPLAY_POST_BUTTONS' => $display_post_buttons,
			'NEWS_DESC' => $news_desc,
			'NEWS_RULES' => $rules_info['forum_rules'],
			'NEWS_RULES_LINK' => $rules_info['forum_rules_link'],
			'REPORTED_IMG' => $user->img('icon_topic_reported', 'POST_REPORTED'),
			'UNAPPROVED_IMG' => $user->img('icon_topic_unapproved', 'POST_UNAPPROVED'),
			'T_ICONS_PATH' => "{$phpbb_root_path}{$config['icons_path']}/",
			'NEWS_IMG_SRC' => $master->gym_master->path_config['gym_img_url'] . 'html_news.gif',
		);
		if ($master->call['single_forum'] || $master->call['cat_forum']) {
			$tpl_vars += array(
				'FORUM_MAP_URL' => $master->module_config['html_allow_cat_map'] ? append_sid($master->gym_master->html_build_url('html_forum_cat_map', $phpbb_seo->seo_url['forum'][$forum_id], $forum_id)) : '',
				'FORUM_MAP' => sprintf($user->lang['HTML_MAP_OF'], $master->forum_datas[$forum_id]['forum_name']),
				'FORUM_URL' => $master->forum_datas[$forum_id]['forum_url'],
				'FORUM_NAME' => $master->forum_datas[$forum_id]['forum_name'],
				'S_SINGLE_FORUM' => $master->call['cat_forum'] ? false : true,
			);
		} else {
			$tpl_vars += array(
				'FORUM_MAP' => sprintf($user->lang['HTML_MAP_OF'], $master->module_config['html_sitename']),
				'FORUM_MAP_URL' => $master->module_config['html_allow_map'] ? append_sid($master->module_config['html_url'] . $master->url_settings['html_forum_map']) : '',
				'FORUM_URL' => append_sid("{$phpbb_root_path}index.$phpEx"),
				'FORUM_NAME' => $master->module_config['html_sitename'],
			);
		}
		if ($display_user_info) {
			$tpl_vars += array(
				'PROFILE_IMG' => $user->img('icon_user_profile', 'READ_PROFILE'),
				'SEARCH_IMG' => $user->img('icon_user_search', 'SEARCH_USER_POSTS'),
				'PM_IMG' => $user->img('icon_contact_pm', 'SEND_PRIVATE_MESSAGE'),
				'EMAIL_IMG' => $user->img('icon_contact_email', 'SEND_EMAIL'),
				'WWW_IMG' => $user->img('icon_contact_www', 'VISIT_WEBSITE'),
				'ICQ_IMG' => $user->img('icon_contact_icq', 'ICQ'),
				'AIM_IMG' => $user->img('icon_contact_aim', 'AIM'),
				'MSN_IMG' => $user->img('icon_contact_msnm', 'MSNM'),
				'YIM_IMG' => $user->img('icon_contact_yahoo', 'YIM'),
				'JABBER_IMG' => $user->img('icon_contact_jabber', 'JABBER'),
			);
		}
		if ($display_post_buttons) {
			$tpl_vars += array(
				'QUOTE_IMG' => $user->img('icon_post_quote', 'REPLY_WITH_QUOTE'),
				'EDIT_IMG' => $user->img('icon_post_edit', 'EDIT_POST'),
				'DELETE_IMG' => $user->img('icon_post_delete', 'DELETE_POST'),
				'INFO_IMG' => $user->img('icon_post_info', 'VIEW_INFO'),
				'REPORT_IMG' => $user->img('icon_post_report', 'REPORT_POST'),
				'WARN_IMG' => $user->img('icon_user_warn', 'WARN_USER'),
			);
		}
		if ($display_pagination) {
			$l_total_topic_s = ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';
			$tpl_vars += array(
				'DISPLAY_PAGINATION'	=> generate_pagination(append_sid($display_file), $total_topics, $limit, $start),
				'DISPLAY_PAGE_NUMBER'	=> on_page($total_topics, $limit, $start),
				'DISPLAY_TOTAL_TOPICS' => sprintf($user->lang[$l_total_topic_s], $total_topics),
			);
		}
		$template->assign_vars($tpl_vars);
		unset($tpl_vars);
	}
}
?>