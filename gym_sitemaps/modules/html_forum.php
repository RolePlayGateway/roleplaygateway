<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: html_forum.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* html_forum Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class html_forum {
	var $url_settings = array();
	var $options = array();
	var $module_config = array();
	var $outputs = array();
	var $forum_cache = array();
	var $call = array();
	var $topic_tracking_info = array();
	var $tracking_topics = array();
	var $forum_tracking_info = array();
	var $forum_datas = array();
	var $icons = array();
	var $html_switch = array();
	var $start = 0;
	var $module_auth = array();
	var $actions = array();
	/**
	* constuctor
	*/
	function html_forum(&$gym_master) {
		global $phpbb_seo;
		$this->gym_master = &$gym_master;
		$this->actions = &$this->gym_master->actions;
		$this->outputs = &$this->gym_master->output_data;
		$this->module_config = &$this->gym_master->html_config;
		$this->url_settings = &$this->gym_master->url_config;
		$this->start = &$this->gym_master->start;
		$this->html_switch = &$this->gym_master->html_switch;
		$this->module_auth = &$this->gym_master->module_auth;
		$this->module_config['html_last_topics_exclude_list'] = '';
		$this->module_config = array_merge(
			// Global
			$this->module_config,
			// Other stuff required here
			array(
				'html_forum_news_ids' => trim($this->gym_master->gym_config['html_forum_news_ids'], ','),
				'html_forum_ltopic' => (int) $this->gym_master->gym_config['html_forum_ltopic'],
				'html_forum_ltopic_pagination' => (int) $this->gym_master->gym_config['html_forum_ltopic_pagination'],
				'html_forum_cat_ltopic' => (int) $this->gym_master->gym_config['html_forum_cat_ltopic'],
				'html_forum_news_ltopic' => (int) $this->gym_master->gym_config['html_forum_news_ltopic'],
				'html_forum_cat_news_ltopic' => (int) $this->gym_master->gym_config['html_forum_cat_news_ltopic'],
				'html_forum_last_post' => (boolean) $this->gym_master->gym_auth_value($this->gym_master->gym_config['html_forum_last_post']),
				'html_forum_first' => ((int) $this->gym_master->gym_config['html_forum_first']) ? 'first' : 'last',
				'html_forum_news_first' => ((int) $this->gym_master->gym_config['html_forum_news_first']) ? 'first' : 'last',
				'html_forum_post_buttons' => (boolean) $this->gym_master->gym_auth_value($this->gym_master->gym_config['html_forum_post_buttons']),
				'html_exclude_list' => trim($this->gym_master->gym_config['html_forum_exclude'], ','),
				'html_ltopic_exclude' => trim($this->gym_master->gym_config['html_forum_ltopic_exclude'], ','),
				'html_forum_desc' => (boolean) $this->gym_master->gym_auth_value($this->gym_master->gym_config['html_forum_desc']),
				'html_forum_rules' => (boolean) $this->gym_master->gym_auth_value($this->gym_master->gym_config['html_forum_rules']),
			)
		);
		// Build unauthed arrays
		$this->module_config['exclude_list'] = $this->gym_master->set_exclude_list($this->module_config['html_exclude_list']);
		// Wee need to check auth here
		$this->gym_master->check_forum_auth($this->module_config['html_auth_guest']);
		$this->actions['auth_guest_list'] = array_diff_assoc($this->module_auth['forum']['public_list'], $this->module_config['exclude_list']);
		$this->actions['auth_guest_read'] = array_diff_assoc($this->module_auth['forum']['public_read'], $this->module_config['exclude_list']);
		$this->actions['auth_view_list'] = array_diff_assoc($this->module_auth['forum']['list'], $this->module_config['exclude_list']);
		$this->actions['auth_view_read'] = array_diff_assoc($this->module_auth['forum']['read'], $this->module_config['exclude_list']);
		// Mod rewrite type auto detection
		$this->url_settings['modrtype'] = ($phpbb_seo->modrtype >= 0) ? intval($phpbb_seo->modrtype) : intval($this->module_config['html_modrtype']);
		// make sure virtual_folder uses the proper value
		// Set up urls
		$html_def = $this->url_settings['html_default'];
		if ($this->module_config['html_modrewrite']) {
			$html_news_def = $this->url_settings['html_news_default'];
			$this->url_settings['html_forum_map'] = $html_def . 'forum/';
			$this->url_settings['html_forum_cat_map'] = $html_def . 'forum/%1$s/';
			$this->url_settings['html_forum_news'] = $html_news_def . 'forum/';
			$this->url_settings['html_forum_cat_news'] = $html_news_def . 'forum/%1$s/';
			$this->url_settings['html_forum_global_map'] = $html_def . 'forum/global/';
			$this->url_settings['html_forum_global_news'] = $html_news_def . 'forum/global/';
			$this->url_settings['html_forum_announce_map'] = $html_def . 'forum/announce/';
			$this->url_settings['html_forum_announce_news'] = $html_news_def . 'forum/announce/';
			$this->url_settings['html_forum_sticky_map'] = $html_def . 'forum/sticky/';
			$this->url_settings['html_forum_sticky_news'] = $html_news_def . 'forum/sticky/';
		} else {
			$this->url_settings['html_forum_map'] = $html_def . '?forum';
			$this->url_settings['html_forum_cat_map'] = $html_def . '?forum=%2$s';
			$this->url_settings['html_forum_news'] = $html_def . '?forum=news';
			$this->url_settings['html_forum_cat_news'] = $html_def . '?forum=%2$s&amp;news';
			$this->url_settings['html_forum_global_map'] = $html_def . '?forum=global';
			$this->url_settings['html_forum_global_news'] = $html_def . '?forum=global&amp;news';
			$this->url_settings['html_forum_announce_map'] = $html_def . '?forum=announce';
			$this->url_settings['html_forum_announce_news'] = $html_def . '?forum=announce&amp;news';
			$this->url_settings['html_forum_sticky_map'] = $html_def . '?forum=sticky';
			$this->url_settings['html_forum_sticky_news'] = $html_def . '?forum=sticky&amp;news';
		}
	}
	/**
	* Initialize forum output.
	* Will as well compute all required info to be able to :
	* 	- Know what url should be used
	* 	- Know if the call is active and auth
	* 	- Set up all params for the up comming call if necessary (when not caching)
	* Please note :
	* 	This method must exist in other modules, called by html_output in gym_html.php through load_module
	* @access private
	*/
	function html_init() {
		global $user, $db, $phpbb_seo, $auth, $config, $phpEx, $phpbb_root_path;
		$nav_url = $nav_title = false;
		$this->url_settings['current'] = $this->module_config['html_url'];

		$this->outputs['right_col_cache_file'] = $this->actions['html_news_list'] ? 'forum_ltopics_news' : 'forum_ltopics_map';
		// will pass variables to the render class
		$this->call = array(
			'forum_id' => 0,
			'topic_id' => 0,
			'limit' => 0,
			'limit_time' => 0,
			'sort' => 'DESC',
			'display_user_info' => $this->module_config['html_allow_profile'],
			'display_user_link' => $this->module_config['html_allow_profile_links'],
			'display_link' => true,
			'display_online' => $this->module_config['html_disp_online'],
			'display_post_buttons' => $this->module_config['html_forum_post_buttons'],
			'display_pagination' => 0,
			'display_last_post' => $this->module_config['html_forum_last_post'],
			'display_file' => $this->url_settings['html_default'],
			'display_tracking' => $this->module_config['html_disp_tracking'],
			'display_topic_status' => $this->module_config['html_disp_status'],
			'display_sig' => $this->module_config['html_allow_sig'],
			'display_order' => 'first',
			'display_desc' => $this->module_config['html_forum_desc'],
			'display_rules' => $this->module_config['html_forum_rules'],
			'display_sumarize' => $this->module_config['html_sumarize'],
			'display_sumarize_method' => $this->module_config['html_sumarize_method'],
			'display_topic_pagination' => $this->module_config['html_item_pagination'],
			'function' => false,
			'method' => false,
			'file' => false,
			's_global' => false,
			'forum_sql' => '',
			'topic_sql' => '',
			'single_forum' => false,
			'cat_forum' => false,
			'display_last_topic' => 0,
			'last_topic_pagination' => $this->module_config['html_forum_ltopic_pagination'],
		);
		//$this->module_config['exclude_list'] = $this->module_config['exclude_list'] + $this->module_config['global_exclude_list'];
		$pre_set = false;
		$type_key = $_key = '';
		switch ($this->actions['module_sub']) {
		case 'global':
			$this->actions['is_auth'] = $this->actions['is_active'] = true;
			$this->call['s_global'] = $this->actions['is_public'] = true;
			$this->call['forum_sql'] = "t.forum_id = 0";
			$this->call['topic_sql'] = "t.topic_type = " . POST_GLOBAL;
			$type_key = 'forum_global';
			$pre_set = true;
		case 'announce':
			if (!$pre_set) {
				$this->actions['is_public'] = $this->gym_master->gym_auth['reg'];
				$this->actions['is_auth'] = $this->actions['is_active'] = !empty($this->module_auth['forum']['read_post']);
				if (empty($this->actions['auth_view_read'])) {
					$this->gym_master->gym_error(404, '', __FILE__, __LINE__);
				}
				$this->call['forum_sql'] = $db->sql_in_set('t.forum_id', $this->actions['auth_view_read'], false, true);
				$this->call['topic_sql'] = "t.topic_type = " . POST_ANNOUNCE;
				$type_key = 'forum_announce';
				$pre_set = true;
			}
		case 'sticky':
			if (!$pre_set) {
				$this->actions['is_public'] = $this->gym_master->gym_auth['reg'];
				$this->actions['is_auth'] = $this->actions['is_active'] = !empty($this->module_auth['forum']['read_post']);
				if (empty($this->actions['auth_view_read'])) {
					$this->gym_master->gym_error(404, '', __FILE__, __LINE__);
				}
				$this->call['forum_sql'] = $db->sql_in_set('t.forum_id', $this->actions['auth_view_read'], false, true);
				$this->call['topic_sql'] = "t.topic_type = " . POST_STICKY;
				$type_key = 'forum_sticky';
				$pre_set = true;
			}
			if ($this->module_config['html_allow_news'] || $this->module_config['html_allow_map']) {
				if ($this->actions['html_news_list'] && $this->module_config['html_allow_news']) {
					$_key = 'news';
					$this->outputs['left_col_tpl'] = 'gym_sitemaps/display_posts_list.html';
					$this->outputs['right_col'] = $this->call['display_last_topic'] = $this->module_config['html_forum_news_ltopic'];
					$this->actions['pagination_limit'] = $this->call['limit'] = $this->module_config['html_news_pagination_limit'];
					$this->call['method'] = 'display_posts';
					$this->call['display_pagination'] = $this->module_config['html_news_pagination'];
					$this->call['limit_time'] = $this->module_config['html_news_time_limit'];
					$this->call['sort'] = $this->module_config['html_news_sort'];
					$this->call['display_pagination'] = $this->module_config['html_news_pagination'];
					$this->call['display_order'] = $this->module_config['html_forum_news_first'];
					$this->call['file'] = 'display_posts.' . $phpEx;
				} else if ($this->actions['html_map_list'] && $this->module_config['html_allow_map']) {
					$_key = 'map';
					$this->outputs['left_col_tpl'] = 'gym_sitemaps/display_topics_list.html';
					$this->outputs['right_col'] = $this->call['display_last_topic'] = $this->module_config['html_forum_ltopic'];
					$this->actions['pagination_limit'] = $this->call['limit'] = $this->module_config['html_pagination_limit'];
					$this->call['method'] = 'display_topics';
					$this->call['display_pagination'] = $this->module_config['html_pagination'];
					$this->call['limit_time'] = $this->module_config['html_map_time_limit'];
					$this->call['sort'] = $this->module_config['html_sort'];
					$this->call['display_pagination'] = $this->module_config['html_pagination'];
					$this->call['display_order'] = $this->module_config['html_forum_first'];
					$this->call['file'] = 'display_topics.' . $phpEx;
				}
				if (!empty($_key)) {
					$this->outputs['page_title'] = $user->lang['HTML_' . strtoupper($type_key) . '_' . strtoupper($_key)];
					$this->outputs['left_col_cache_file'] = $type_key . '_' . $_key;
					$this->url_settings['current'] .= $this->gym_master->html_build_url('html_' . $type_key . '_' . $_key);
				}
				$this->start = $this->call['display_pagination'] ? $this->gym_master->chk_start($this->start, $this->call['limit']) : 0;
			}
			// current url
			$this->call['display_file'] = $this->url_settings['current'];
			$this->url_settings['current'] .= $this->gym_master->html_add_start($this->start);
			break;
		case 'news':
			if ($this->actions['html_news_list']) {
				$this->outputs['left_col_tpl'] = 'gym_sitemaps/display_posts_list.html';
				$this->outputs['left_col_cache_file'] = "forum_news";
				$this->outputs['page_title'] = sprintf($user->lang['HTML_NEWS_OF'], $this->module_config['html_sitename']);
				// Auth and active switches
				$this->actions['is_auth'] = $this->actions['is_active'] = $this->actions['is_public'] = true;
				$this->outputs['right_col'] = $this->call['display_last_topic'] = $this->module_config['html_forum_news_ltopic'];
				$this->actions['pagination_limit'] = $this->call['limit'] = $this->module_config['html_news_pagination_limit'];
				$this->module_config['html_forum_news_ids'] = $this->gym_master->set_exclude_list($this->module_config['html_forum_news_ids']);
				if (empty($this->module_config['html_forum_news_ids'])) {
					$this->actions['auth_view_read'] = array_diff_assoc($this->module_auth['forum']['read_post'], $this->module_config['exclude_list']);
					$this->actions['is_auth'] = $this->actions['is_active'] = !empty($this->actions['auth_view_read']);
					$this->call['single_forum'] = sizeof($this->actions['auth_view_read']) > 1 ? false : true;
					if (empty($this->actions['auth_view_read'])) {
						$this->gym_master->gym_error(404, '', __FILE__, __LINE__);
					}
					// Output news from all authed forums
					$this->call['forum_sql'] = $db->sql_in_set('t.forum_id', $this->actions['auth_view_read'], false, true);
				} else {
					$this->call['single_forum'] = sizeof($this->module_config['html_forum_news_ids']) > 1 ? false : true;
					// No exclude list here !
					$this->call['forum_sql'] = $db->sql_in_set('t.forum_id', $this->module_config['html_forum_news_ids'], false, true);
				}
				$this->call['method'] = 'display_posts';
				$this->call['display_pagination'] = $this->module_config['html_news_pagination'];
				$this->call['limit_time'] = $this->module_config['html_news_time_limit'];
				$this->call['sort'] = $this->module_config['html_news_sort'];
				$this->call['display_pagination'] = $this->module_config['html_news_pagination'];
				$this->call['display_order'] = $this->module_config['html_forum_news_first'];
				$this->call['file'] = 'display_posts.' . $phpEx;
				// current url
				$this->start = $this->call['display_pagination'] ? $this->gym_master->chk_start($this->start, $this->call['limit']) : 0;
				$this->url_settings['current'] .= $this->gym_master->html_build_url('html_forum_news');
				$this->call['display_file'] = $this->url_settings['current'];
				$this->url_settings['current'] .= $this->gym_master->html_add_start($this->start);
			}
			break;
		default:
			if ($this->actions['html_map_list'] && (empty($this->actions['module_sub']) || $this->actions['module_sub'] == 'map')) {
				// Expected URL
				$this->url_settings['current'] .= $this->url_settings['html_forum_map'];
				$this->call['display_file'] = $this->url_settings['current'];
				$this->outputs['left_col_tpl'] = 'gym_sitemaps/display_forums_list.html';
				$this->outputs['left_col_cache_file'] = "forum_map";
				$this->actions['is_public'] = $this->gym_master->gym_auth['reg'];
				$this->actions['is_auth'] = true;
				$this->actions['is_active'] = (boolean) ($this->module_config['html_allow_cat_news'] || $this->module_config['html_allow_cat_map'] );
				$this->outputs['right_col'] = $this->call['display_last_topic'] = $this->module_config['html_forum_ltopic'];
				$this->outputs['page_title'] = sprintf($user->lang['HTML_MAP_OF'], $this->module_config['html_sitename']);
				$this->call['file'] = 'display_forums.' . $phpEx;
				$this->call['method'] = 'display_forums';
				// Here we need to be able to list categories as well as forums
				// List all listable forums except excluded and links
				if (empty($this->actions['auth_view_list'])) {
					$this->gym_master->gym_error(404, '', __FILE__, __LINE__);
				}
				$this->call['forum_sql'] = $db->sql_in_set('f.forum_id', $this->actions['auth_view_list'], false, true);
			} else if ($this->actions['html_news_list'] || $this->actions['html_map_list']) {
				// Filter $this->actions['module_sub'] var type
				$this->actions['module_sub'] = (int) $this->actions['module_sub'];
				if ($this->actions['module_sub'] > 0) { // Forum map or news
					$forum_id = $this->call['forum_id'] = $this->actions['module_sub'];

					// Here we need to be able to list categories as well as forums
					// A forum news or map is viewable when is a readable postable forum or a listable forum cat
					// (with authed children see below)
					$this->actions['is_auth'] = (boolean) ( isset($this->actions['auth_view_read'][$forum_id]) || (isset($this->module_auth['forum']['skip_cat'][$forum_id]) && isset($this->actions['auth_view_list'][$forum_id])) );
					$this->actions['is_public'] = (boolean) isset($this->actions['auth_guest_list'][$forum_id]);
					$this->call['single_forum'] = true;
					if ($this->actions['html_news_list'] ) {
						$this->actions['is_active'] = true;
						$key = 'news';
						$this->actions['pagination_limit'] = $this->call['limit'] = $this->module_config['html_news_pagination_limit'];
						$this->outputs['right_col'] = $this->call['display_last_topic'] = $this->module_config['html_forum_cat_news_ltopic'];
						$this->outputs['left_col_tpl'] = 'gym_sitemaps/display_posts_list.html';
						$this->call['file'] = 'display_posts.' . $phpEx;
						$this->call['display_pagination'] = $this->module_config['html_news_pagination'];
						$this->call['limit_time'] = $this->module_config['html_cat_news_time_limit'];
						$this->call['display_order'] = $this->module_config['html_forum_news_first'];
						$this->call['sort'] = $this->module_config['html_cat_news_sort'];
						$this->call['method'] = 'display_posts';
					} else if ($this->actions['html_map_list']) {
						$this->actions['is_active'] = true;
						$key = 'map';
						$this->actions['pagination_limit'] = $this->call['limit'] = $this->module_config['html_pagination_limit'];
						$this->outputs['right_col'] = $this->call['display_last_topic'] = $this->module_config['html_forum_cat_ltopic'];
						$this->outputs['left_col_tpl'] = 'gym_sitemaps/display_topics_list.html';
						$this->call['file'] = 'display_topics.' . $phpEx;
						$this->call['display_pagination'] = $this->module_config['html_pagination'];
						$this->call['limit_time'] = $this->module_config['html_cat_time_limit'];
						$this->call['sort'] = $this->module_config['html_cat_sort'];
						$this->call['display_order'] = $this->module_config['html_forum_first'];
						$this->call['method'] = 'display_topics';
					}
					// Upon single forum calls, grabb forum data separatelly to allow access to forum data when there is no topic to list
					// As well prevent topic row from listing repeated forum data
					if ($this->actions['is_active']) {
						$sql = "SELECT *
							FROM " . FORUMS_TABLE . "
							WHERE forum_id = $forum_id";
						$result = $db->sql_query($sql);
						if ($row = $db->sql_fetchrow($result)) {
							// www.phpBB-SEO.com SEO TOOLKIT BEGIN
							$phpbb_seo->set_url($row['forum_name'], $forum_id, $phpbb_seo->seo_static['forum']);
							// www.phpBB-SEO.com SEO TOOLKIT END
							$this->forum_datas[$forum_id] = array_merge($row,  array(
								'forum_url' => append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id"),
								'm_approve' => $auth->acl_get('m_approve', $forum_id),
							));
							if ($row['forum_password']) {
								login_forum_box($row);
							}
						} else { // Forum does not exist
							$this->actions['is_active'] = false;
						}
					}
					if ($this->actions['is_active']) {
						$this->call['forum_sql'] = "t.forum_id = $forum_id";
						if ($this->forum_datas[$forum_id]['forum_type'] == FORUM_CAT) {
							$this->call['cat_forum'] = $forum_id;
							// lets check childrens
							if ($f_ids = $this->gym_master->get_forum_children($forum_id)) {
								$this->call['forum_sql'] = $db->sql_in_set('t.forum_id', array_keys($f_ids), false, true);
								$this->call['single_forum'] = false;
							} else {
								// Cat with no readable sub forums
								$this->actions['is_active'] = false;
							}
						}
						$this->start = $this->call['display_pagination'] ? $this->gym_master->chk_start($this->start, $this->call['limit']) : 0;
						$this->url_settings['current'] .= $this->gym_master->html_build_url('html_forum_cat_' . $key, $phpbb_seo->seo_url['forum'][$forum_id], $forum_id);
						$this->call['display_file'] = $this->url_settings['current'];
						$this->url_settings['current'] .= $this->gym_master->html_add_start($this->start);
						$this->outputs['page_title'] = sprintf($user->lang['HTML_' . strtoupper($key) . '_OF'], $this->forum_datas[$forum_id]['forum_name']);
						if ($this->module_config['html_allow_' . $key]) {
							$nav_url = $this->url_settings['html_forum_' . $key];
							$nav_title = sprintf($user->lang['HTML_' . strtoupper($key) . '_OF'], $this->module_config['html_sitename']);
						}
						$this->outputs['left_col_cache_file'] = "forum_$forum_id$key";
						// Enable forum tracking
						$_REQUEST['f'] = $this->call['forum_id'];
						// Track user viewing this forum
						$this->outputs['single_traking'] = true;
					}
				}
			}
			break;
		}
		if ($nav_url) {
			global $template;
			// Add Module page in navigation links
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME' => $nav_title,
				'U_VIEW_FORUM'	=> append_sid($this->module_config['html_url'] . $nav_url))
			);
		}
		return;
	}
	/**
	* html_main() will build the module's main output
	* @access private
	*/
	function html_main() {
		if (!empty($this->call['file'])) {
			require_once($this->gym_master->path_config['gym_path'] . 'display/' . $this->call['file']);
		}
		if (!empty($this->call['method'])) {
			$output = new $this->call['method']($this);
		}
		return;
	}
	/**
	* html_module()
	* Add local optional module content to the main output, last_topics in our case
	* @access private
	*/
	function html_module() {
		if (!empty($this->call['display_last_topic'])) {
			$this->last_topics($this->call['display_last_topic']);
		}
		return;
	}
	/**
	* html_index()
	* Add local links to the main site map
	* @access private
	*/
	function html_index() {
		global $user;
		// We need to take care about overrides
		$override = $this->gym_master->gym_config['html_override'];
		$forum_allow_cat_map = (boolean) get_gym_option('html', 'forum', 'allow_cat_map', $override, $this->gym_master->gym_config);
		$forum_allow_cat_news = (boolean) get_gym_option('html', 'forum', 'allow_cat_news', $override, $this->gym_master->gym_config);
		$forum_allow_map = (boolean) get_gym_option('html', 'forum', 'allow_map', $override, $this->gym_master->gym_config);
		$forum_allow_news = (boolean) get_gym_option('html', 'forum', 'allow_news', $override, $this->gym_master->gym_config);
		if (!$forum_allow_cat_map && !$forum_allow_cat_news && !$forum_allow_map && !$forum_allow_news) {
			return;
		}
		$this->outputs['module_data']['forum'] = array(
			'title' => $this->gym_master->gym_config['html_forum_sitename'],
			'desc' => $this->gym_master->gym_config['html_forum_site_desc'],
			'img' => '',
			'map_url' => $forum_allow_map && ($forum_allow_cat_map || $forum_allow_cat_news) ? $this->module_config['html_url'] . $this->url_settings['html_forum_map'] : false,
			'news_url' => $forum_allow_news ? $this->module_config['html_url'] . $this->url_settings['html_forum_news'] : false,
			'links' => array(),
		);
		// Add other supported links such as global annoucements etc ...
		if ( $forum_allow_map  || $forum_allow_news ) {
			$links = &$this->outputs['module_data']['forum']['links'];
			$linkables = array( 'html_forum', 'html_forum_global', 'html_forum_announce', 'html_forum_sticky');
			foreach ($linkables as $type) {
				$links[$type] = array(
					'map_title' => $forum_allow_news ? $user->lang[strtoupper($type . '_map')] : '',
					'map_url' => $forum_allow_news ? $this->url_settings[$type . '_map'] : '',
					'news_title' => $forum_allow_map ? $user->lang[strtoupper($type . '_news')] : '',
					'news_url' => $forum_allow_map ? $this->url_settings[$type . '_news'] : '',
				);
			}
		}
		return;
	}
	function last_topics($limit = 10) {
		global $db, $template, $user, $config, $phpEx, $phpbb_root_path, $phpbb_seo, $auth, $cache;
		// Usefull for multi bb topic & forum tracking
		// Leave default for single forum eg : '_track'
		$tracking_cookie_name = (defined('XLANG_AKEY') ? XLANG_AKEY : '') . '_track';
		$this->outputs['right_col_tpl'] = 'gym_sitemaps/last_topics_list.html';
		// wa can use start here since there are always more topics overall than in a single forum
		$start = $this->start ? $this->gym_master->chk_start($this->start, $limit) : 0;
		$template->assign_vars(array('LAST_POST_IMG' => $user->img('icon_topic_latest', 'VIEW_LATEST_POST')));
		// Wee need to check auth here
		$this->module_config['last_topics_exclude_list'] = $this->gym_master->set_exclude_list($this->module_config['html_ltopic_exclude']);
		$forum_auth_ids = array_diff_assoc($this->module_auth['forum']['read_post'], $this->module_config['last_topics_exclude_list']);
		if (!empty($forum_auth_ids)) {
			$topic_sql_auth = $db->sql_in_set('t.forum_id', $forum_auth_ids, false, true);
			$template->assign_vars(array(
				'NEWEST_POST_IMG' => $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
				'LAST_POST_IMG' => $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			));
			$display_tracking = &$this->call['display_tracking'];
			$display_user_info = &$this->call['display_user_info'];
			$display_topic_status = &$this->call['display_topic_status'];
			if (!$display_tracking) {
				$load_db_lastread = $load_anon_lastread = false;
			} else {
				$load_db_lastread = (boolean) ($config['load_db_lastread'] && $user->data['is_registered']);
				$load_anon_lastread = (boolean) ($config['load_anon_lastread'] || $user->data['is_registered']);
			}
			$template->assign_vars(array(
					'LASTX_TOPICS_TITLE' => sprintf($user->lang['HTML_LASTX_TOPICS_TITLE'], $limit)
				)
			);
			// Get The Data, first forums
			if (!empty($this->forum_datas)) {
				$f_id_done = array_keys($this->forum_datas);
				$f_id_done = array_combine($f_id_done, $f_id_done);
				unset($f_id_done[0]);
				$forum_query_ids = array_diff_assoc($forum_auth_ids, $f_id_done);
			} else {
				$forum_query_ids = $forum_auth_ids;
			}
			// Only get the required forums data
			if (!empty($forum_query_ids)) {
				$forum_sql_auth = $db->sql_in_set('f.forum_id', $forum_query_ids, false, true);
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
				$sql_array['WHERE'] = $forum_sql_auth;
				$result = $db->sql_query($db->sql_build_query('SELECT', $sql_array));
				$all_forum_datas = $forum_datas = array();
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
			if ($display_user_info && $display_topic_status && $user->data['is_registered']) {
				$sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_POSTED_TABLE => 'tp'), 'ON' => 'tp.topic_id = t.topic_id AND tp.user_id = ' . $user->data['user_id']);
				$sql_array['SELECT'] .= ', tp.topic_posted';
			}
			if ($load_db_lastread) {
				$sql_array['SELECT'] .= ', tt.mark_time';
				$sql_array['LEFT_JOIN'][] = array(
					'FROM'	=> array(TOPICS_TRACK_TABLE => 'tt'),
					'ON'	=> 'tt.user_id = ' . $user->data['user_id'] . ' AND tt.topic_id = t.topic_id'
				);
			} elseif ($load_anon_lastread && empty($this->tracking_topics)) {
				$this->tracking_topics = (isset($_COOKIE[$config['cookie_name'] . $tracking_cookie_name])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . $tracking_cookie_name]) : $_COOKIE[$config['cookie_name'] . $tracking_cookie_name]) : '';
				$this->tracking_topics = ($this->tracking_topics) ? tracking_unserialize($this->tracking_topics) : array();
				if (!$user->data['is_registered']) {
					$user->data['user_lastmark'] = (isset($this->tracking_topics['l'])) ? (int) (base_convert($this->tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
				}
			}
			$sql_array['WHERE'] = "$topic_sql_auth
						AND t.topic_status <> " . ITEM_MOVED . ' AND t.topic_approved = 1';
			$sql_array['ORDER_BY'] = 'topic_last_post_time DESC';
			$result = $db->sql_query_limit($db->sql_build_query('SELECT', $sql_array), $limit, $start);
			while ($row = $db->sql_fetchrow($result)) {
				$topic_id = (int) $row['topic_id'];
				$forum_id = (int) $row['forum_id'];
				$all_forum_datas[$forum_id][$topic_id] = $row;
			}
			$db->sql_freeresult($result);
			// Grab icons
			if (empty($this->icons)) {
				$this->icons = $cache->obtain_icons();
			}
			$folder_alt = 'NO_NEW_POSTS';
			$topic_type = '';
			$folder_img = 'topic_read';
			$topic_folder_img = $user->img($folder_img, $folder_alt);
			$topic_folder_img_src = $user->img($folder_img, $folder_alt, false, '', 'src');
			foreach ($all_forum_datas as $forum_id => $topic_datas) {
				// Start with the forum
				$forum_id = (int) $forum_id;
				if (empty($this->forum_datas[$forum_id])) {
					$row = & $forum_datas[$forum_id];
					// www.phpBB-SEO.com SEO TOOLKIT BEGIN
					$phpbb_seo->set_url($row['forum_name'], $forum_id, $phpbb_seo->seo_static['forum']);
					// www.phpBB-SEO.com SEO TOOLKIT END
					$this->forum_datas[$forum_id] = array_merge($row,  array(
						'forum_url' => append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id"),
						'm_approve' => $auth->acl_get('m_approve', $forum_id),
					));
					if ($load_db_lastread) {
						$this->forum_tracking_info[$forum_id] = !empty($row['forum_mark_time']) ? $row['forum_mark_time'] : $user->data['user_lastmark'];
					} elseif ($load_anon_lastread) {
						$this->forum_tracking_info[$forum_id] = isset($this->tracking_topics['f'][$forum_id]) ? (int) (base_convert($this->tracking_topics['f'][$forum_id], 36, 10) + $config['board_startdate']) : $user->data['user_lastmark'];
					}
				}
				$forum_unread = (isset($this->forum_tracking_info[$forum_id]) && $this->forum_datas[$forum_id]['forum_last_post_time'] > $this->forum_tracking_info[$forum_id]) ? true : false;
				$folder_image = $folder_alt = '';
				$folder_image = ($forum_unread) ? 'forum_unread' : 'forum_read';
				// Which folder should we display?
				if ($this->forum_datas[$forum_id]['forum_status'] == ITEM_LOCKED) {
					$folder_image = ($forum_unread) ? 'forum_unread_locked' : 'forum_read_locked';
					$folder_alt = 'FORUM_LOCKED';
				} else {
					$folder_alt = ($forum_unread) ? 'NEW_POSTS' : 'NO_NEW_POSTS';
				}
				$template->assign_block_vars('last_forums', array(
						'FORUM_NAME' => $this->forum_datas[$forum_id]['forum_name'],
						'FORUM_FOLDER_IMG' => $user->img($folder_image, $folder_alt),
						'FORUM_FOLDER_IMG_SRC' => $user->img($folder_image, $folder_alt, false, '', 'src'),
						'FORUM_FOLDER_IMG_ALT'	=> isset($user->lang[$folder_alt]) ? $user->lang[$folder_alt] : '',
						'U_VIEWFORUM' => append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id"),
					)
				);
				// Now the topics
				foreach ($topic_datas as $topic_id => $topic_data) {
					$topic_id = (int) $topic_id;
					if ($load_db_lastread) {
						$this->topic_tracking_info[$topic_id] = !empty($topic_data['mark_time']) ? $topic_data['mark_time'] : $user->data['user_lastmark'];
					} else if ($load_anon_lastread) {
						$topic_id36 = base_convert($topic_id, 10, 36);
						if (isset($this->tracking_topics['t'][$topic_id36])) {
							$this->tracking_topics['t'][$topic_id] = base_convert($this->tracking_topics['t'][$topic_id36], 36, 10) + $config['board_startdate'];
						}
						$this->topic_tracking_info[$topic_id] = isset($this->tracking_topics['t'][$topic_id]) ? $this->tracking_topics['t'][$topic_id] : $user->data['user_lastmark'];
					}
					if (!empty($this->forum_tracking_info[$forum_id])) {
						$this->topic_tracking_info[$topic_id] = $this->topic_tracking_info[$topic_id] > $this->forum_tracking_info[$forum_id] ? $this->topic_tracking_info[$topic_id] : $this->forum_tracking_info[$forum_id];
					}
					$topic_data['topic_title'] = censor_text($topic_data['topic_title']);
					// www.phpBB-SEO.com SEO TOOLKIT BEGIN
					$phpbb_seo->prepare_iurl($topic_data, 'topic', $topic_data['topic_type'] == POST_GLOBAL ? $this->seo_static['global_announce'] : $phpbb_seo->seo_url['forum'][$forum_id]);
					// www.phpBB-SEO.com SEO TOOLKIT END
					// Replies
					$replies = $this->forum_datas[$forum_id]['m_approve'] ? $topic_data['topic_replies_real'] : $topic_data['topic_replies'];
					$last_page = (($replies + 1) > $config['posts_per_page']) ? floor($replies / $config['posts_per_page']) * $config['posts_per_page'] : 0;
					$last_post_url =  append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id" . ($last_page ? "&amp;start=$last_page" : '')) . '#p' . $topic_data['topic_last_post_id'];
					$unread_topic = (isset($this->topic_tracking_info[$topic_id]) && $topic_data['topic_last_post_time'] > $this->topic_tracking_info[$topic_id]) ? true : false;
					// Get folder img, topic status/type related information
					if ($display_topic_status) {
						$this->gym_master->topic_status($topic_data, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);
						$topic_folder_img = $user->img($folder_img, $folder_alt);
						$topic_folder_img_src = $user->img($folder_img, $folder_alt, false, '', 'src');
					}
					$view_topic_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id");
					$template->assign_block_vars('last_forums.last_topics', array(
							'TOPIC_TITLE' => $topic_data['topic_title'],
							'PAGINATION' => $this->call['last_topic_pagination'] ? $this->gym_master->topic_generate_pagination($replies, $view_topic_url) : '',
							'TOPIC_TYPE' => $topic_type,
							'TOPIC_FOLDER_IMG' => $topic_folder_img,
							'TOPIC_FOLDER_IMG_SRC' => $topic_folder_img_src,
							'TOPIC_FOLDER_IMG_ALT' => $user->lang[$folder_alt],
							'TOPIC_ICON_IMG' => (!empty($this->icons[$topic_data['icon_id']])) ? $this->icons[$topic_data['icon_id']]['img'] : '',
							'TOPIC_ICON_IMG_WIDTH' => (!empty($this->icons[$topic_data['icon_id']])) ? $this->icons[$topic_data['icon_id']]['width'] : '',
							'TOPIC_ICON_IMG_HEIGHT' => (!empty($this->icons[$topic_data['icon_id']])) ? $this->icons[$topic_data['icon_id']]['height'] : '',
							'U_NEWEST_POST' => $unread_topic ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' .  $forum_id . '&amp;t=' . $topic_id . '&amp;view=unread#unread') : '',
							'U_VIEW_TOPIC' => $view_topic_url,
							'U_LAST_POST' => $last_post_url,
							'S_UNREAD_TOPIC' => $unread_topic,
						)
					);
				}
			}
			unset($forum_datas, $all_forum_datas);
		}
	}
	/**
	* Generates forum rules / desc for given forum
	*/
	function generate_forum_info($forum_data, $type = 'desc') {
		static $types = array('desc' => 1, 'rules' => 1);
		if (!isset($types[$type])) {
			return '';
		}
		$forum_info = array('forum_desc' => false, 'forum_rules' => false, 'forum_rules_link' => false);
		if (!empty($forum_data["forum_$type"])) {
			$forum_info["forum_$type"] = generate_text_for_display($forum_data["forum_$type"], $forum_data["forum_{$type}_uid"], $forum_data["forum_{$type}_bitfield"], $forum_data["forum_{$type}_options"]);
		} else if (isset($forum_data["forum_{$type}_link"])) {
			$forum_info['forum_rules_link'] = $forum_data['forum_rules_link'];
		}
		return $type == 'rules' ? $forum_info : $forum_info["forum_$type"];
	}
}
?>