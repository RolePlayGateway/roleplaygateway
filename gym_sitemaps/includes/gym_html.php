<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_html.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
require_once($phpbb_root_path . 'gym_sitemaps/includes/gym_sitemaps.' . $phpEx);
/**
* gym_html Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class gym_html extends gym_sitemaps {
	var	$html_config = array();
	var	$start = 0;
	/**
	* constuctor
	*/
	function gym_html($standalone = false) {
		global $phpbb_seo, $config;
		$this->gym_sitemaps('html');
		// Check the main vars
		$this->init_get_vars();
		if (empty($this->actions['action_modules'])) {
			$this->gym_error(404, '', __FILE__, __LINE__);
		}
		$this->output_data['tpl'] = $this->output_data['page_title'] = '';
		// Used to store module data upon index calls
		$this->output_data['module_data'] = array();
		$this->actions['pagination_limit'] = 0;
		// Setup the output
		$this->cache_config = array_merge(
			// Global
			$this->cache_config,
			// Other stuff required here
			array(
				'main_cache_on' => (boolean) $this->set_module_option('main_cache_on', $this->gym_config['html_override']),
				'opt_cache_on' => (boolean) $this->set_module_option('opt_cache_on', $this->gym_config['html_override']),
				'main_cache_ttl' => round($this->set_module_option('main_cache_ttl', $this->gym_config['html_override']),2) * 3600,
				'opt_cache_ttl' => round($this->set_module_option('opt_cache_ttl', $this->gym_config['html_override']),2) * 3600,
			)
		);
		$this->html_config = array(
			'html_c_info' => $this->gym_config['html_c_info'],
			'html_url' => $this->gym_config['html_url'],
			'html_pagination' => (int) $this->set_module_option('pagination', $this->override['pagination']),
			'html_item_pagination' => (int) $this->set_module_option('item_pagination', $this->override['pagination']),
			'html_pagination_limit' => (int) max(1, $this->set_module_option('pagination_limit', $this->override['pagination'])),
			'html_news_pagination' => $this->set_module_option('news_pagination', $this->override['pagination']),
			'html_news_pagination_limit' => (int) max(1, $this->set_module_option('news_pagination_limit', $this->override['pagination'])),
			'html_map_time_limit' => (int) max(0, $this->set_module_option('map_time_limit', $this->override['limit'])*3600*24),
			'html_cat_time_limit' => (int) max(0, $this->set_module_option('cat_time_limit', $this->override['limit'])*3600*24),
			'html_news_time_limit' => (int) max(0, $this->set_module_option('news_time_limit', $this->override['limit'])*3600*24),
			'html_cat_news_time_limit' => (int) max(0, $this->set_module_option('cat_news_time_limit', $this->override['limit'])*3600*24),
			'html_modrewrite' => (boolean) $this->set_module_option('modrewrite', $this->override['modrewrite']),
			'html_modrtype' => (int) $this->set_module_option('modrtype', $this->override['modrewrite']),
			'html_stats_on_news' => (boolean) $this->gym_auth_value($this->set_module_option('stats_on_news', $this->gym_config['html_override'])),
			'html_stats_on_map' => (boolean) $this->gym_auth_value($this->set_module_option('stats_on_map', $this->gym_config['html_override'])),
			'html_birthdays_on_news' => (boolean) $this->gym_auth_value($this->set_module_option('birthdays_on_news', $this->gym_config['html_override'])),
			'html_birthdays_on_map' => (boolean) $this->gym_auth_value($this->set_module_option('html_birthdays_on_map', $this->gym_config['html_override'])),
			'html_sitename' => $this->set_module_option('sitename'),
			'html_site_desc' => $this->set_module_option('site_desc'),
			'html_logo_url' => trim($this->set_module_option('logo_url'), '/'),
			'html_sort' => $this->set_module_option('sort', $this->override['sort']),
			'html_cat_sort' => $this->set_module_option('cat_sort', $this->override['sort']),
			'html_news_sort' => $this->set_module_option('news_sort', $this->override['sort']),
			'html_cat_news_sort' => $this->set_module_option('cat_news_sort', $this->override['sort']),
			'html_allow_auth' => (boolean) $this->set_module_option('allow_auth', $this->gym_config['html_override']),
			'html_disp_online' => (boolean) $this->gym_auth_value($this->set_module_option('disp_online', $this->gym_config['html_override'])),
			'html_disp_tracking' => (boolean) $this->gym_auth_value($this->set_module_option('disp_tracking', $this->gym_config['html_override'])),
			'html_disp_status' => (boolean) $this->gym_auth_value($this->set_module_option('disp_status', $this->gym_config['html_override'])),
			'html_allow_profile' => (boolean) $this->gym_auth_value($this->set_module_option('allow_profile', $this->gym_config['html_override'])),
			'html_allow_profile_links' => (boolean) $this->gym_auth_value($this->set_module_option('allow_profile_links', $this->gym_config['html_override'])),
			'html_allow_map' => (boolean) $this->set_module_option('allow_map', $this->gym_config['html_override']),
			'html_allow_cat_map' => (boolean) $this->set_module_option('allow_cat_map', $this->gym_config['html_override']),
			'html_allow_news' => (boolean) $this->set_module_option('allow_news', $this->gym_config['html_override']),
			'html_allow_cat_news' => (boolean) $this->set_module_option('allow_cat_news', $this->gym_config['html_override']),
			'html_sumarize' => (int) $this->set_module_option('sumarize', $this->gym_config['html_override']),
			'html_sumarize_method' => trim($this->set_module_option('sumarize_method', $this->gym_config['html_override'])),
			'html_allow_bbcode' => (boolean) $this->gym_auth_value($this->set_module_option('allow_bbcode', $this->gym_config['html_override'])),
			'html_strip_bbcode' => trim($this->set_module_option('strip_bbcode', $this->gym_config['html_override'])),
			'html_allow_links' => (boolean) $this->gym_auth_value($this->set_module_option('allow_links', $this->gym_config['html_override'])),
			'html_allow_emails' => (boolean) $this->set_module_option('allow_emails', $this->gym_config['html_override']),
			'html_allow_smilies' => (boolean) $this->gym_auth_value($this->set_module_option('allow_smilies', $this->gym_config['html_override'])),
			'html_allow_sig' => (boolean) $this->gym_auth_value($this->set_module_option('allow_sig', $this->gym_config['html_override'])),
			'html_msg_filters' => array(),
			'html_auth_msg' => '',
			'html_do_explain' => false,
		);
		$config['gzip_compress'] = $config['gzip_compress'] ? 1 : (boolean) $this->set_module_option('gzip', $this->override['gzip']);
		$this->html_config['html_auth_guest'] = ($this->html_config['html_allow_auth'] && $this->gym_auth['reg']) ? false : true;
		$this->cache_config['do_cache_main'] = (boolean) ($this->html_config['html_auth_guest'] && $this->cache_config['main_cache_on']);
		$this->cache_config['do_cache_opt'] = (boolean) ($this->html_config['html_auth_guest'] && $this->cache_config['opt_cache_on']);
		$this->output_data['left_col_cache_file'] = $this->output_data['right_col_cache_file'] = '';
		$this->actions['is_auth'] = $this->actions['is_active'] = $this->actions['is_public'] = false;

		if (!$standalone) {
			// Check the rss specific vars and do basic set_up for msg output
			$this->init_html_vars();
			$this->html_output();
		}
		return;
	}
	/**
	* init_html_vars()
	* Set up the specific rss modules GET vars.
	* @access private
	*/
	function init_html_vars() {
		global $user, $phpEx, $phpbb_seo;
		// Take care about module categorie urls, assuming that they are of the proper form
		// news|map/module_main/module_sub/
		// this code will filter in between special map|news and categroy map and news
		// assuming that the cat urls will be of the following form title-sepXX (forum-title-fxx.html or /)
		// or without ID, if the phpbb_seo cache array is properly set.
		if (!empty($this->actions['module_main']) && !empty($this->actions['module_sub']) && $this->actions['module_sub'] != 'map' && $this->actions['module_sub'] != 'news') {
			if (preg_match('`^[a-z0-9_-]*-[a-z]{1}+([0-9]+)`', $this->actions['module_sub'], $match)) {
				$this->actions['module_sub'] = $match[1];
			} else if ($id = @array_search($this->actions['module_sub'], $phpbb_seo->cache_config[$this->actions['module_main']]) ) {
				$this->actions['module_sub'] = (int) $id;
			}
		}
		$this->start = max(0, request_var('start', 0));
		$this->actions['html_news_list'] = $this->actions['html_map_list'] = false;
		$this->html_config['extra_title'] = $this->url_config['extra_params_full'] = $this->url_config['extra_params'] = '';
		if ( isset($_GET['news']) && ($this->html_config['html_allow_cat_news'] || $this->html_config['html_allow_news'])) {
			$this->actions['html_news_list'] = true;
			if (empty($this->actions['module_sub']) && $this->html_config['html_allow_news']) {
				$this->actions['module_sub'] = 'news';
			}
		} else if (@$this->actions['module_sub'] == 'news' && $this->html_config['html_allow_news']) {
			$this->actions['html_news_list'] = true;
		} else if (!empty($this->actions['module_sub']) && $this->html_config['html_allow_cat_map']) {
			$this->actions['html_map_list'] = true;
		} else if (!empty($this->actions['module_main']) && (empty($this->actions['module_sub']) || $this->actions['module_sub'] == 'map' ) && $this->html_config['html_allow_map']) {
			$this->actions['html_map_list'] = true;
		}
		if ($this->actions['html_news_list']) {
			$this->actions['html_type'] = 'news';
			$this->actions['display_stats'] = $this->html_config['html_stats_on_news'];
			$this->actions['display_birthdays'] = $this->html_config['html_birthdays_on_news'];

		} else {
			$this->actions['html_type'] = 'map';
			$this->actions['display_stats'] = $this->html_config['html_stats_on_map'];
			$this->actions['display_birthdays'] = $this->html_config['html_birthdays_on_map'];
		}
		// Adjust variable a bit
		if ($this->actions['html_news_list']) { // requested and auth
			$this->html_config['html_msg_filters'] = $this->set_msg_strip($this->html_config['html_strip_bbcode']);
		}
		// Set up module's urls : Pagination
		$this->url_config['html_start_ext'] = '';
		if ($this->html_config['html_modrewrite']) {
			$page = !empty($phpbb_seo->seo_static['page']) ? $phpbb_seo->seo_static['page'] : 'page';
			$html = !empty($phpbb_seo->seo_ext['page']) ? $phpbb_seo->seo_ext['page'] : '.html';
			$this->url_config['html_start'] = "$page%1\$s$html";
			$this->url_config['html_default'] = 'maps/';
			$this->url_config['html_news_default'] = 'news/';
		} else {
			$this->url_config['html_start'] = '&amp;start=%1$s';
			$this->url_config['html_news_default'] = $this->url_config['html_default'] . '?news';
		}
	}
	/**
	* html_add_start($start, $tpl_start_key) builds the pagination bit
	* @access private
	*/
	function html_add_start($start, $tpl_start_key = 'html_start') {
		return $start > 0 ? sprintf($this->url_config[$tpl_start_key], $start) : @$this->url_config[$tpl_start_key . '_ext'];
	}
	/**
	* html_build_url($tpl_key, $title, $id, $start = 0, $tpl_start_key) builds the url
	* $title must be properly formated prior to injection
	* @access private
	*/
	function html_build_url($tpl_key, $title = '', $id = 0, $start = 0, $tpl_start_key = 'html_start') {
		return sprintf($this->url_config[$tpl_key], $title, $id) . $this->html_add_start($start, $tpl_start_key);
	}
	/**
	* html_output() will build all html output
	* @access private
	*/
	function html_output() {
		global $phpEx, $db, $config, $phpbb_root_path, $user, $template, $cache, $phpbb_seo;
		$module_obj = null;
		$left_col = $right_col = false;
		// Set up the base href tag, could be done better but saves a file edit this way ( and works too ;-) )
		// Assuming that map.php is either in phpBB's dir or above (not under).
		$bhref_ulr = ($phpbb_root_path === './') ? $phpbb_seo->seo_path['phpbb_url'] : str_replace(ltrim($phpbb_root_path, './'), '', $phpbb_seo->seo_path['phpbb_url']);
		$template->assign_vars(array(
			'META' => '<base href="' . $bhref_ulr . '"/>' . "\n",
			'S_CONTENT_DIRECTION'	=> $user->lang['DIRECTION'],
			'S_CONTENT_FLOW_BEGIN'	=> ($user->lang['DIRECTION'] == 'ltr') ? 'left' : 'right',
			'S_CONTENT_FLOW_END'	=> ($user->lang['DIRECTION'] == 'ltr') ? 'right' : 'left',
			'NEWEST_POST_IMG' => $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
			'LAST_POST_IMG' => $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
		));
		// module action
		if (in_array($this->actions['module_main'], $this->actions['action_modules'])) { // List item from the module
			// Add index page in navigation links
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME' => $user->lang['HTML_MAP'],
				'U_VIEW_FORUM'	=> append_sid($this->html_config['html_url']. $this->url_config['html_default']))
			);
			$module_class = $this->actions['action_type'] . '_' . $this->actions['module_main'];
			$module_obj = $this->load_module($module_class, 'html_init', true);
		} else { // sitemap index
			// We are working on all available modules
			$left_col = $this->html_index();
		}
		$page_title = (!empty($this->output_data['page_title']) ? $this->output_data['page_title'] : $user->lang['HTML_' . strtoupper($this->actions['html_type'])]) . $this->html_config['extra_title'];
		// Add current page in navigation links
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME' => $page_title,
			'U_VIEW_FORUM'	=> append_sid($this->url_config['current']))
		);
		if (!$this->actions['is_active']) {
			header('HTTP/1.1 404 Not Found');
			global $msg_title;
			$msg_title = $user->lang['GYM_ERROR_404'];
			trigger_error('GYM_ERROR_404_EXPLAIN');
		}
		if (!$this->actions['is_auth']) {
			header('HTTP/1.1 401 Unauthorized');
			if ($user->data['user_id'] != ANONYMOUS) {
				trigger_error('GYM_ERROR_401');
			}
			login_box('', $user->lang['GYM_LOGIN']);
		}
		// Let's kill duplicate now !
		if (!empty($this->url_config['current'])) {
			$this->seo_kill_dupes(append_sid($this->url_config['current']));
		}
		$template->set_filenames(array('body' => 'gym_sitemaps/index_body.html'));
		$this->cache_config['do_cache_main'] = (boolean) ($this->cache_config['do_cache_main'] && $this->actions['is_public']);
		$cache_find = array('`(\?|&amp;|&)sid\=[a-z0-9]+`i', '`[\s]+`');
		$cache_replace = array('',' ');
		// Main output
		if (!empty($this->output_data['left_col_tpl'])) {
			$template->set_filenames(array('left_col' => $this->output_data['left_col_tpl']));
			if ($this->cache_config['do_cache_main'] && !empty($this->output_data['left_col_cache_file'])) {
				$cache_file = '_gym_html_' . $this->output_data['left_col_cache_file'] . '_' . $user->data['user_lang'] . '_' . $this->start;
				if (($left_col = $cache->get($cache_file)) === false) {
					$module_obj->html_main();
					$left_col = $template->assign_display('left_col', '', true);
					// Strip whitespaces
					$left_col = preg_replace($cache_find, $cache_replace, $left_col );
					$cache->put($cache_file, $left_col, $this->cache_config['main_cache_ttl']);
				}
			}
			if (!$left_col) {
				$module_obj->html_main();
				$left_col = $template->assign_display('left_col', '', true);
			}
		}
		// Optional output
		if (!empty($this->output_data['right_col'])) {
			if ($this->cache_config['do_cache_opt'] && !empty($this->output_data['right_col_cache_file'])) {
				$cache_file = '_gym_html_' . $this->output_data['right_col_cache_file'] . '_' . $user->data['user_lang'];
				if (($right_col = $cache->get($cache_file)) === false) {
					$module_obj->html_module();
					$template->set_filenames(array('right_col' => $this->output_data['right_col_tpl']));
					$right_col = $template->assign_display('right_col', '', true);
					// Strip whitespaces
					$right_col = preg_replace($cache_find, $cache_replace, $right_col );
					$cache->put($cache_file, $right_col, $this->cache_config['opt_cache_ttl']);
				}
			}
			if (!$right_col) {
				$module_obj->html_module();
				$template->set_filenames(array('right_col' => $this->output_data['right_col_tpl']));
				$right_col = $template->assign_display('right_col', '', true);
			}
		}
		unset($module_obj);
		$tpl_data = array();
		if ($this->actions['display_stats']) {
			// Set some stats, get posts count from forums data if we... hum... retrieve all forums data
			$total_posts	= $config['num_posts'];
			$total_topics	= $config['num_topics'];
			$total_users	= $config['num_users'];
			$l_total_user_s = ($total_users == 0) ? 'TOTAL_USERS_ZERO' : 'TOTAL_USERS_OTHER';
			$l_total_post_s = ($total_posts == 0) ? 'TOTAL_POSTS_ZERO' : 'TOTAL_POSTS_OTHER';
			$l_total_topic_s = ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';
			$tpl_data += array(
				'TOTAL_POSTS'	=> sprintf($user->lang[$l_total_post_s], $total_posts),
				'TOTAL_TOPICS'	=> sprintf($user->lang[$l_total_topic_s], $total_topics),
				'TOTAL_USERS'	=> sprintf($user->lang[$l_total_user_s], $total_users),
			);
			if ($this->html_config['html_allow_profile']) {
				$tpl_data += array(
					'NEWEST_USER'	=> sprintf($user->lang['NEWEST_USER'], get_username_string( $this->html_config['html_allow_profile_links'] ? 'full' : 'no_profile', $config['newest_user_id'], $config['newest_username'], $config['newest_user_colour'])),
				);
			}
		}
		// Generate birthday list if required ...
		if ($this->actions['display_birthdays']) {
			$birthday_list = '';
			$now = getdate(time() + $user->timezone + $user->dst - date('Z'));
			$sql = 'SELECT u.user_id, u.username, u.user_colour, u.user_birthday
				FROM ' . USERS_TABLE . ' u
				LEFT JOIN ' . BANLIST_TABLE . " b ON (u.user_id = b.ban_userid)
				WHERE (b.ban_id IS NULL
					OR b.ban_exclude = 1)
					AND u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%'
					AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result)) {
				$birthday_list .= (($birthday_list != '') ? ', ' : '') . get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				if ($age = (int) substr($row['user_birthday'], -4)) {
					$birthday_list .= ' (' . ($now['year'] - $age) . ')';
				}
			}
			$db->sql_freeresult($result);
			$tpl_data += array(
				'BIRTHDAY_LIST'	=> $birthday_list,
				'S_DISPLAY_BIRTHDAY_LIST' => !empty($birthday_list),
			);
		}
		$template->assign_vars($tpl_data + array(
				'S_SINGLE_TRAKING' => !empty($this->output_data['single_traking']) ? true : false,
				'S_LOGIN_ACTION' => append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
				'S_SEO_FORUM' => strpos($config['default_lang'], 'fr') !== false ? 'fr/' : 'en/',
				'LEFT_COL' => $left_col,
				'RIGHT_COL' => $right_col,
			)
		);
		if ($this->actions['pagination_limit'] > 0) { // Add page number to title
			$page_title .= $this->start > 0 ? ' - ' . $user->lang['HTML_PAGE'] . ' ' . ( floor( $this->start / $this->actions['pagination_limit'] ) + 1 ) : '';
		}
		page_header($page_title, $this->html_config['html_disp_online']);
		if (!empty($this->html_config['html_logo_url'])) {
			$template->assign_vars(array(
					'SITE_LOGO_IMG' => '<img src="' . $this->path_config['gym_img_url'] . $this->html_config['html_logo_url'] . '" alt="' . $this->output_data['page_title'] . '" />',
				)
			);
		}
		page_footer();
		return;
	}
	/**
	* html_index()
	* Builds the site map index
	* @access private
	*/
	function html_index() {
		global $phpEx, $phpbb_root_path, $user, $template, $cache, $phpbb_seo;
		if ($this->actions['html_news_list']) {
			// Add index page in navigation links
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME' => $user->lang['HTML_MAP'],
				'U_VIEW_FORUM'	=> append_sid($this->html_config['html_url']. $this->url_config['html_default']))
			);
			$this->url_config['current'] = $this->html_config['html_url'] . $this->url_config['html_news_default'];
			$this->actions['is_auth'] = true;
			$this->actions['is_active'] = !empty($this->gym_config['html_rss_news_url']);
			$params = array(
				// Full URL to the RSS 2.0 feed
				'url' => str_replace('&amp;', '&', $this->gym_config['html_rss_news_url']),
				'desc' => true,
				'html' => true,
				'limit' => (int) $this->gym_config['html_rss_news_limit'],
			);
			display_feed($params);
			$template->assign_vars(array('GYM_RSS_SLIDE_SP' => false));
			$template->set_filenames(array('index_data' => 'gym_sitemaps/gym_link_body.html'));
		} else {
			$cache_file = '_gym_html_map_' . $user->data['user_lang'];
			if (($this->output_data['module_data'] = $cache->get($cache_file)) === false) {
				$this->load_modules('html_index');
				$cache->put($cache_file, $this->output_data['module_data']);
			}
			$this->output_data['page_title'] = sprintf($user->lang['HTML_MAP_OF'], $this->html_config['html_sitename']);
			$this->actions['is_active'] = $this->actions['is_auth'] = true;
			$this->url_config['current'] = $this->html_config['html_url'] . $this->url_config['html_default'];
			$template->set_filenames(array('index_data' => 'gym_sitemaps/index_list.html'));
			if (!empty($this->output_data['module_data'])) {
				foreach ($this->output_data['module_data'] as $module_name => $module_data) {
					// First modules
					$template->assign_block_vars('module', array(
							'TITLE' => $module_data['title'],
							'NEWS_TITLE' => sprintf($user->lang['HTML_NEWS_OF'], $module_data['title']),
							'MAP_TITLE' => sprintf($user->lang['HTML_MAP_OF'], $module_data['title']),
							'DESC' => $module_data['desc'],
							'IMG' => $module_data['img'],
							'MAP_URL' => append_sid($module_data['map_url']),
							'NEWS_URL' => append_sid($module_data['news_url']),
						)
					);
					// Then the module maps & news pages
					foreach ($module_data['links'] as $data) {
						$template->assign_block_vars('module.links', array(
								'MAP_TITLE' => $data['map_title'],
								'MAP_URL' => append_sid($data['map_url']),
								'NEWS_TITLE' => $data['news_title'],
								'NEWS_URL' => append_sid($data['news_url']),
							)
						);
					}
				}
			}
		}
		$template->assign_vars(array(
			'HTML_SITENAME' => $this->html_config['html_sitename'],
			'HTML_SITEDESC' => $this->html_config['html_site_desc'],
			'HTML_URL' => $this->html_config['html_allow_map'] ? append_sid($this->html_config['html_url'] . $this->url_config['html_default']) : '',
			'HTML_NEWS_TITLE' => sprintf($user->lang['HTML_NEWS_OF'], $this->html_config['html_sitename']),
			'HTML_NEWS_URL' => $this->html_config['html_allow_news'] ? append_sid($this->html_config['html_url'] . $this->url_config['html_news_default']) : '',
			'NEWS_IMG_SRC' => $this->path_config['gym_img_url'] . 'html_news.gif',
			'ROOT_PATH' => $phpbb_root_path,
			)
		);
		$return = $template->assign_display('index_data', '', true);
		return $return;
	}
	/**
	* prepare_message($message, $bbcode_uid, $bbcode_bitfield, $patterns = array(), $replaces = array())
	* will put together BBcodes and smilies before the output
	* @access private
	*/
	function prepare_message(&$message, $bbcode_uid, $bitfield, $patterns = array(), $replaces = array()) {
		global $config, $user, $phpbb_root_path;
		static $bbcode;
		if (!empty($patterns)) {
			$message = preg_replace($patterns, $replaces, $message);
		}
		if ($this->html_config['html_sumarize'] > 0 ) {
			$message = $this->summarize( $message, $this->html_config['html_sumarize'], $this->html_config['html_sumarize_method'] );
			// Clean broken tag at the end of the message
			$message = preg_replace('`\<[^\<\>]*$`i', ' ...', $message);
			// Close broken bbcode tags requiring it, only quotes for now
			$this->close_bbcode_tags($message, $bbcode_uid);
		}
		$message = censor_text($message);
		if ($bitfield && $this->html_config['html_allow_bbcode']) {
			if (!class_exists('bbcode')) {
				global $phpbb_root_path, $phpEx;
				include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
			}
			if (empty($bbcode)) {
				$bbcode = new bbcode($bitfield);
			} else {
				$bbcode->bbcode($bitfield);
			}
			$bbcode->bbcode_second_pass($message, $bbcode_uid);
		}
		$message = bbcode_nl2br($message);
		// Parse smilies
		$message = $this->smiley_text($message, !$this->html_config['html_allow_smilies']);
		if ($this->html_config['html_sumarize'] > 0 ) {
			// last clean up
			static $_find = array('`\<\!--[^\<\>]+--\>`Ui', '`\[\/?[^\]\[]*\]`Ui');
			$message = preg_replace($_find, '', $message);
		}
		return true;
	}
	/**
	* close_bbcode_tags(&$message, $uid, $bbcodelist)
	* will tend to do it nicely ;-)
	* Will close the bbcode tags requiring it in the list (quote|b|u|i|color|*|list)
	* Beware, bo not reduce $bbcodelist without knowing what you are doing
	*/
	function close_bbcode_tags(&$message, $uid, $bbcodelist = 'quote|b|u|i|color|*|list') {
		global $config, $user, $phpbb_seo;
		$open_lists = $close_lists = array();
		$bbcodelist = str_replace('|*', '|\*', $bbcodelist);
		$open_count = preg_match_all('`\[(' . $bbcodelist . ')(\=([a-z0-9]{1}))?[^\]\[]*\:' . $uid . '\]`i', $message, $open_matches);
		$close_count = preg_match_all('`\[/(' . $bbcodelist . ')(\:([a-z]{1}))?[^\]\[]*\:' . $uid . '\]`i', $message, $close_matches);
		if ($open_count == $close_count) { // No need to go further
			return;
		}
		if (!empty($open_matches[1])) {
			$open_list = array_count_values($open_matches[1]);
			$close_list = !empty($close_matches[1]) ? array_count_values($close_matches[1]) : array();
			$list_to_close = array();
			if (isset($open_list['list'])) {
				foreach ($open_matches[1] as $k => $v) {
					if ($v == 'list') {
						$open_lists[] = !empty($open_matches[3][$k]) ? 'o' : 'u';
					}
				}
				if (!empty($close_matches[1])) {
					foreach ($close_matches[1] as $k => $v) {
						if ($v == 'list') {
							$close_lists[] = !empty($close_matches[3][$k]) ? 'o' : 'u';
						}
					}
				}
				$list_to_close = array_reverse(array_diff_assoc($open_lists, $close_lists));
			}
			unset($open_list['*'], $open_list['list'], $open_matches, $close_matches);
			foreach ($open_list as $bbcode => $total) {
				if (empty($close_list[$bbcode]) || $close_list[$bbcode] < $total) {
					// close the tags
					$diff = empty($close_list[$bbcode]) ? $total : $total - $close_list[$bbcode];
					$message .= str_repeat("[/$bbcode:$uid]", $diff);
				}
			}
			// Close the lists if required
			foreach ($list_to_close as $ltype) {
				$message .= "[/*:m:$uid][/list:$ltype:$uid]";
			}
		}
		return;
	}
	/**
	* set_msg_strip($bbcode_list) will build up the unauthed bbcode list
	* $bbcode_list = 'code:0,img:1,quote';
	* $bbcode_list = 'all';
	* 1 means the bbcode and it's content will be striped.
	* all means all bbcodes.
	* $returned_list = array('patern' => $matching_patterns, 'replace' => $replace_patterns);
	* @access private
	*/
	function set_msg_strip($bbcode_list) {
		$patterns = $replaces = array();
		// Now the bbcodes
		if (!$this->html_config['html_allow_bbcode'] || preg_match('`all\:?([0-1]*)`i', $bbcode_list, $matches)) {
			if ( (@$matches[1] != 1 ) ) {
				$patterns[] = '`\[\/?[a-z0-9\*\+\-]+(?:=(?:&quot;.*&quot;|[^\]]*))?(?::[a-z])?(\:[0-9a-z]{5,})\]`i';
				$replaces[] = '';
			} else {
				$patterns[] = '`\[([a-z0-9\*\+\-]+)((=|:)[^\:\]]*)?\:[0-9a-z]{5,}\].*\[/(?1)(:?[^\:\]]*)?\:[0-9a-z]{5,}\]`Usi';
				$replaces[] = "{ \\1 }";
			}
			$patterns[] = '`<[^>]*>(.*<[^>]*>)?`Usi'; // All html
			$replaces[] = '';
		} else {
			// Take care about links & emails
			if ( !$this->html_config['html_allow_links'] ) {
				if ( !$this->html_config['html_allow_emails'] ) { // Saves couple RegEx
					$email_find = '[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*[a-z]+';
					$email_replace = 'str_replace(array("@", "."), array("  AT  ", " DOT "),"\\1")';
					$email_option = 'e';
				} else {
					$email_find = '.*?';
					$email_replace = "\\1";
					$email_option = '';
				}
				$patterns[] = '`<!\-\- ([lmw]+) \-\-><a (?:class="[\w-]+" )?href="(.*?)">.*?</a><!\-\- \1 \-\->`i';
				$replaces[] = "\\2";
				$patterns[] = '`\[/?url[^\]\[]*\]`i';
				$replaces[] = '';
				$patterns[] = '`<!\-\- e \-\-><a href="mailto:(' . $email_find . ')">.*?</a><!\-\- e \-\->`i' . $email_option;
				$replaces[] = $email_replace;
			}
			if ( !$this->html_config['html_allow_emails'] && $this->html_config['html_allow_links'] ) {
				$patterns[] = '`<!\-\- e \-\-><a href="mailto:([a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*[a-z]+)">.*?</a><!\-\- e \-\->`ei';
				$replaces[] = 'str_replace(array("@", "."), array("  AT  ", " DOT "),"\\1")';
			}
			$exclude_list =  ( empty($bbcode_list) ? array() : explode(',', trim($bbcode_list, ', ')) );
			$RegEx_unset = $RegEx_remove = '';
			foreach ($exclude_list as $key => $value ) { // Group the RegEx
				$value = trim($value, ', ');
				if (preg_match("`[a-z0-9]+(\:([0-1]*))?`i", $value, $matches) ) {
					$values = (strpos($value, ':') !== false) ?  explode(':', $value) : array($value);
					if ( (@$matches[2] != 1 ) ) {
						$RegEx_unset .= (!empty($RegEx_unset) ? '|' : '' ) . $values[0];
					} else {
						$RegEx_remove .= (!empty($RegEx_remove) ? '|' : '' ) . $values[0];
					}
				}
			}
			if (!empty($RegEx_remove) ) {
				$patterns[] =  '`\[(' . $RegEx_remove . ')((=|:)[^\:\]]*)?\:[0-9a-z]{5,}\].*\[/(?1)(:?[^\:\]]*)?\:[0-9a-z]{5,}\]`Usi';
				$replaces[] = "{ \\1 }";
			}
			if (!empty($RegEx_unset) ) {
				$patterns[] =  '`\[/?(' . $RegEx_unset . ')(?:=(?:&quot;.*&quot;|[^\]]*))?(?::[a-z])?(\:[0-9a-z]{5,})?\]`i';
				$replaces[] = '';
			}
		}
		return  array('pattern' => $patterns, 'replace' => $replaces);
	}
	/**
	* Summarize method selector
	* @access private
	*/
	function summarize($string, $limit, $method = 'lines') {
		switch ($method) {
			case 'words':
				return $this->word_limit($string, $limit);
				break;
			case 'chars':
				return $this->char_limit($string, $limit);
				break;
			case 'lines':
			default:
				return $this->line_limit($string, $limit);
				break;
		}
	}
	/**
	* Cut the text by lines
	* @access private
	*/
	function line_limit($string, $limit = 10, $ellipsis = ' ...') {
		return count($lines = preg_split("`[\n\r]+`", ltrim($string), $limit + 1)) > $limit ? rtrim(utf8_substr($string, 0, utf8_strlen($string) - utf8_strlen(end($lines)))) . $ellipsis : $string;
	}
	/**
	* Cut the text according to the number of words.
	* Borrowed from www.php.net http://www.php.net/preg_replace
	* @access private
	*/
	function word_limit($string, $limit = 50, $ellipsis = ' ...') {
		return count($words = preg_split('`\s+`', ltrim($string), $limit + 1)) > $limit ? rtrim(utf8_substr($string, 0, utf8_strlen($string) - utf8_strlen(end($words)))) . $ellipsis : $string;
	}
	/**
	* Cut the text according to the number of characters.
	* Borrowed from www.php.net http://www.php.net/preg_replace
	* @access private
	*/
	function char_limit($string, $limit = 100, $ellipsis = ' ...') {
		return utf8_strlen($fragment = utf8_substr($string, 0, $limit + 1 - utf8_strlen($ellipsis))) < utf8_strlen($string) + 1 ? preg_replace('`\s*\S*$`', '', $fragment) . $ellipsis : $string;
	}
	/**
	* Get forum children (one level or all, with or without info)
	* @access private
	*/
	function get_forum_children($forum_id, $skip_pass = true, $only_post = true, $ids_only = true, $one_lvl = true) {
		global $db, $auth;
		$rows = array();
		$_sql_pre = $one_lvl ? 'f1' : 'f2';
		$sql_sel = $ids_only ? "$_sql_pre.forum_id, $_sql_pre.forum_type, $_sql_pre.forum_password" : "$_sql_pre.*";
		$sql_where = $only_post ? " AND $_sql_pre.forum_type = " . FORUM_POST : '';
		if (!$one_lvl) {
			$sql = "SELECT $sql_sel
				FROM " . FORUMS_TABLE . " f1
				LEFT JOIN " . FORUMS_TABLE . " f2 ON (f2.left_id BETWEEN f1.left_id AND f1.right_id)
				WHERE f1.forum_id = $forum_id
				$sql_where
				ORDER BY f2.left_id ASC";
		} else {
			$sql = "SELECT $sql_sel
				FROM " . FORUMS_TABLE . " f1
				WHERE f1.parent_id = $forum_id
				$sql_where
				ORDER BY f1.left_id ASC";
		}
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			if ($skip_pass && $row['forum_password']) {
				continue;
			}
			$rows[$row['forum_id']] = $row;
		}

		$db->sql_freeresult($result);
		// Do not return this forum info
		if (!$one_lvl) {
			unset($rows[$forum_id]);
		}
		// only keep authed forum
		foreach ($rows as $_forum_id => $null) {
			if (!$auth->acl_get('f_read', $_forum_id)) {
				unset($rows[$_forum_id]);
			}
		}
		return !empty($rows) ? $rows : false;
	}
	/**
	* Generate topic status
	*/
	function topic_status(&$topic_row, $replies, $unread_topic, &$folder_img, &$folder_alt, &$topic_type) {
		global $user, $config;
		$folder = $folder_new = '';
		if ($topic_row['topic_status'] == ITEM_MOVED) {
			$topic_type = $user->lang['VIEW_TOPIC_MOVED'];
			$folder_img = 'topic_moved';
			$folder_alt = 'TOPIC_MOVED';
		} else {
			switch ($topic_row['topic_type']) {
				case POST_GLOBAL:
					$topic_type = $user->lang['VIEW_TOPIC_GLOBAL'];
					$folder = 'global_read';
					$folder_new = 'global_unread';
				break;
				case POST_ANNOUNCE:
					$topic_type = $user->lang['VIEW_TOPIC_ANNOUNCEMENT'];
					$folder = 'announce_read';
					$folder_new = 'announce_unread';
				break;
				case POST_STICKY:
					$topic_type = $user->lang['VIEW_TOPIC_STICKY'];
					$folder = 'sticky_read';
					$folder_new = 'sticky_unread';
				break;
				default:
					$topic_type = '';
					$folder = 'topic_read';
					$folder_new = 'topic_unread';
					if ($config['hot_threshold'] && $replies >= $config['hot_threshold'] && $topic_row['topic_status'] != ITEM_LOCKED) {
						$folder .= '_hot';
						$folder_new .= '_hot';
					}
				break;
			}
			if ($topic_row['topic_status'] == ITEM_LOCKED) {
				$topic_type = $user->lang['VIEW_TOPIC_LOCKED'];
				$folder .= '_locked';
				$folder_new .= '_locked';
			}
			$folder_img = ($unread_topic) ? $folder_new : $folder;
			$folder_alt = ($unread_topic) ? 'NEW_POSTS' : (($topic_row['topic_status'] == ITEM_LOCKED) ? 'TOPIC_LOCKED' : 'NO_NEW_POSTS');
			// Posted image?
			if (!empty($topic_row['topic_posted']) && $topic_row['topic_posted']) {
				$folder_img .= '_mine';
			}
		}
		if ($topic_row['poll_start'] && $topic_row['topic_status'] != ITEM_MOVED) {
			$topic_type = $user->lang['VIEW_TOPIC_POLL'];
		}
	}
	/**
	* Generate topic pagination
	* Duplicated here to prevent from including functions_display.php
	*/
	function topic_generate_pagination($replies, $url) {
		global $config, $user;
		// www.phpBB-SEO.com SEO TOOLKIT BEGIN
		global $phpbb_seo, $phpEx;
		// www.phpBB-SEO.com SEO TOOLKIT END
		// Make sure $per_page is a valid value
		$per_page = ($config['posts_per_page'] <= 0) ? 1 : $config['posts_per_page'];
		if (($replies + 1) > $per_page) {
			$total_pages = ceil(($replies + 1) / $per_page);
			$pagination = '';
			$times = 1;
			for ($j = 0; $j < $replies + 1; $j += $per_page) {
				$pagination .= '<a href="' . $url . '&amp;start=' . $j . '">' . $times . '</a>';
				if ($times == 1 && $total_pages > 5) {
					$pagination .= ' ... ';
					// Display the last three pages
					$times = $total_pages - 3;
					$j += ($total_pages - 4) * $per_page;
				} else if ($times < $total_pages) {
					$pagination .= '<span class="page-sep">' . $user->lang['COMMA_SEPARATOR'] . '</span>';
				}
				$times++;
			}
			// www.phpBB-SEO.com SEO TOOLKIT BEGIN
			if (!empty($phpbb_seo->seo_opt['url_rewrite'])) {
				static $pagin_find = array();
				static $pagin_replace = array();
				if (empty($pagin_find)) {
					$pagin_find = array( '`(\.(?!' . $phpEx . ')[a-z0-9]+)([\w\#$%&~\-;:=,?@+]*)&amp;start=([0-9]+)`i', '`/([\w\#$%&~\-;:=,?@+]*)&amp;start=([0-9]+)`i' );
					$pagin_replace = array( $phpbb_seo->seo_delim['start'] . '\\3\\1\\2', '/' . $phpbb_seo->seo_static['pagination'] . '\\2' . $phpbb_seo->seo_ext['pagination'] .'\\1' );
				}
				$pagination = str_replace( '&amp;start=0', '', $pagination );
				$pagination = preg_replace( $pagin_find, $pagin_replace, $pagination );
			}
			// www.phpBB-SEO.com SEO TOOLKIT END
		} else {
			$pagination = '';
		}
		return $pagination;
	}
	/**
	* Get user rank title and image
	*
	* @param int $user_rank the current stored users rank id
	* @param int $user_posts the users number of posts
	* @param string &$rank_title the rank title will be stored here after execution
	* @param string &$rank_img the rank image as full img tag is stored here after execution
	* @param string &$rank_img_src the rank image source is stored here after execution
	*
	*/
	function get_user_rank($user_rank, $user_posts, &$rank_title, &$rank_img, &$rank_img_src) {
		global $ranks, $config, $phpbb_root_path;
		if (empty($ranks)) {
			global $cache;
			$ranks = $cache->obtain_ranks();
		}
		if (!empty($user_rank)) {
			$rank_title = (isset($ranks['special'][$user_rank]['rank_title'])) ? $ranks['special'][$user_rank]['rank_title'] : '';
			$rank_img = (!empty($ranks['special'][$user_rank]['rank_image'])) ? '<img src="' . $phpbb_root_path . $config['ranks_path'] . '/' . $ranks['special'][$user_rank]['rank_image'] . '" alt="' . $ranks['special'][$user_rank]['rank_title'] . '" title="' . $ranks['special'][$user_rank]['rank_title'] . '" />' : '';
			$rank_img_src = (!empty($ranks['special'][$user_rank]['rank_image'])) ? $phpbb_root_path . $config['ranks_path'] . '/' . $ranks['special'][$user_rank]['rank_image'] : '';
		} else {
			if (!empty($ranks['normal'])) {
				foreach ($ranks['normal'] as $rank) {
					if ($user_posts >= $rank['rank_min']) {
						$rank_title = $rank['rank_title'];
						$rank_img = (!empty($rank['rank_image'])) ? '<img src="' . $phpbb_root_path . $config['ranks_path'] . '/' . $rank['rank_image'] . '" alt="' . $rank['rank_title'] . '" title="' . $rank['rank_title'] . '" />' : '';
						$rank_img_src = (!empty($rank['rank_image'])) ? $phpbb_root_path . $config['ranks_path'] . '/' . $rank['rank_image'] : '';
						break;
					}
				}
			}
		}
	}
	/**
	* Get user avatar
	*
	* @param string $avatar Users assigned avatar name
	* @param int $avatar_type Type of avatar
	* @param string $avatar_width Width of users avatar
	* @param string $avatar_height Height of users avatar
	* @param string $alt Optional language string for alt tag within image, can be a language key or text
	*
	* @return string Avatar image
	*/
	function get_user_avatar($avatar, $avatar_type, $avatar_width, $avatar_height, $alt = 'USER_AVATAR') {
	global $user, $config, $phpbb_root_path, $phpEx;
		if (empty($avatar) || !$avatar_type) {
			return '';
		}
		$avatar_img = '';
			switch ($avatar_type) {
			case AVATAR_UPLOAD:
				$avatar_img = $phpbb_root_path . "download/file.$phpEx?avatar=";
			break;
			case AVATAR_GALLERY:
				$avatar_img = $phpbb_root_path . $config['avatar_gallery_path'] . '/';
			break;
		}
		$avatar_img .= $avatar;
		return '<img src="' . $avatar_img . '" width="' . $avatar_width . '" height="' . $avatar_height . '" alt="' . ((!empty($user->lang[$alt])) ? $user->lang[$alt] : $alt) . '" />';
	}
}
?>