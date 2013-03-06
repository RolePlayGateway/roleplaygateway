<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_rss_functions.php 151 2009-11-10 11:59:37Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
define('GYM_RSS_FUNC_INC', true);
/**
* get_gym_links($gym_config).
* retunrs the link array
*/
function get_gym_links($gym_config) {
	global $phpbb_root_path, $config, $phpEx, $user, $cache, $db, $phpbb_seo;
	$links = array();
	$_phpbb_seo = !empty($phpbb_seo);
	$board_url = $_phpbb_seo ? $phpbb_seo->seo_path['phpbb_url'] : generate_board_url() . '/';
	$gym_link_tpl = '<a href="%1$s" title="%3$s" class="gym"><img src="' . $board_url . 'gym_sitemaps/images/%2$s" alt="%3$s"/>&nbsp;%3$s</a>';
	$google_threshold = max(1, (int) $gym_config['google_threshold']);
	//compute guest auth
	$cache_file = '_gym_auth_guests_forum';
	if (($auth_guest_list = $cache->get($cache_file)) === false) {
		$auth_guest_list = array('list' => array(), 'read' => array(), 'list_post' => array(), 'read_post' => array(), 'skip_pass' => array(), 'skip_cat' => array(), 'skip_all' => array(), 'skip_link' => array(), 'thresholded' => array(), 'empty' => array());
		$guest_data = array('user_id' => ANONYMOUS,
			'user_type' => USER_IGNORE,
			'user_permissions' . (defined('XLANG_AKEY') ? XLANG_AKEY : '') => '',
		);
		$g_auth = new auth();
		$g_auth->acl($guest_data);
		// the forum id array
		$forum_list_ary = $g_auth->acl_getf('f_list', true);
		foreach ($forum_list_ary as $forum_id => $null) {
			$auth_guest_list['list'][$forum_id] = (int) $forum_id;
		}
		$forum_read_ary = $g_auth->acl_getf('f_read', true);
		foreach ($forum_read_ary as $forum_id => $null) {
			$auth_guest_list['read'][$forum_id] = (int) $forum_id;
		}
		ksort($auth_guest_list['list']);
		ksort($auth_guest_list['read']);
		$sql = "SELECT forum_id, forum_type, forum_password
			FROM " . FORUMS_TABLE . "
			WHERE	forum_type <> " . FORUM_POST . " OR forum_password <> ''";
		$result = $db->sql_query($sql);
		while ( $row = $db->sql_fetchrow($result) ) {
			$forum_id = (int) $row['forum_id'];
			if ($row['forum_password']) {
				$auth_guest_list['skip_pass'][$forum_id] = $forum_id;
			}
			if ($row['forum_type'] == FORUM_CAT) {
				$auth_guest_list['skip_cat'][$forum_id] = $forum_id;
			} else if ($row['forum_type'] == FORUM_LINK) {
				$auth_guest_list['skip_link'][$forum_id] = $forum_id;
			}
			$auth_guest_list['skip_all'][$forum_id] = $forum_id;
		}
		$db->sql_freeresult($result);
		// Now let's grabb the list of forum with not enough topics to have a sitemap
		// Only care about postable forum ;-)
		$sql = "SELECT forum_id, forum_topics
			FROM " . FORUMS_TABLE . "
			WHERE	forum_type = " . FORUM_POST . "
				AND forum_topics < $google_threshold";
		$result = $db->sql_query($sql);
		while ( $row = $db->sql_fetchrow($result) ) {
			$forum_id = (int) $row['forum_id'];
			$auth_guest_list['thresholded'][$forum_id] = $forum_id;
			if (empty($row['forum_topics'])) {
				$auth_guest_list['empty'][$forum_id] = $forum_id;
			}
		}
		ksort($auth_guest_list['skip_pass']);
		ksort($auth_guest_list['skip_all']);
		ksort($auth_guest_list['skip_link']);
		ksort($auth_guest_list['skip_cat']);
		// Never mind about forum links
		$auth_guest_list['read'] = array_diff_assoc($auth_guest_list['read'], $auth_guest_list['skip_link']);
		$auth_guest_list['list'] = array_diff_assoc($auth_guest_list['list'], $auth_guest_list['skip_link']);
		ksort($auth_guest_list['read']);
		ksort($auth_guest_list['list']);
		$auth_guest_list['list_post'] = array_diff_assoc($auth_guest_list['list'], $auth_guest_list['skip_all']);
		$auth_guest_list['read_post'] = array_diff_assoc($auth_guest_list['read'], $auth_guest_list['skip_all']);
		$cache->put($cache_file, $auth_guest_list);
	}
	$links = array();
	$links['main'] = $links['setup']['google'] = $links['setup']['rss'] = $links['setup']['html'] = array();
	// Find out about active modes
	$google_active = $rss_active = $html_active = false;
	$gym_modules = array(
		'google' => array('forum', 'xml', 'txt'),
		'rss' => array('forum'),
		'html' => array('forum'),
	);
	foreach ($gym_modules as $type => $modules) {
		foreach ($modules as $module) {
			if (!empty($gym_config[$type . '_' . $module . '_installed'])) {
				${$type . '_active'} = true;
				break;
			}
		}
	}
	$do_display_cat = $do_display_main = $do_display_index = false;
	// Google sitemaps
	if ($google_active) {
		$display_google_main_links = (boolean) get_gym_option('google', 'gym', 'link_main', OVERRIDE_MODULE, $gym_config);
		$display_google_index_links = (boolean) get_gym_option('google', 'gym', 'link_index', OVERRIDE_MODULE, $gym_config);
		$display_google_cat_links = (boolean) get_gym_option('google', 'gym', 'link_cat', OVERRIDE_MODULE, $gym_config);
		$override_google_mod_rewrite = get_override('google', 'modrewrite', $gym_config);
		$google_mod_rewrite = (boolean) get_gym_option('google', 'gym', 'modrewrite', $override_google_mod_rewrite, $gym_config);
		$override_google_gzip = get_override('google', 'gzip', $gym_config);
		$google_gzip = (boolean) get_gym_option('google', 'forum', 'gzip', $override_google_gzip, $gym_config);
		$google_gzip_ext = ($google_gzip || $config['gzip_compress']) ? (get_gym_option('google', 'forum', 'gzip_ext', $override_google_gzip, $gym_config) ? '.gz' : '') : '';
		$sitemap_url = $gym_config['google_url'] . ($google_mod_rewrite ? 'sitemapindex.xml' . $google_gzip_ext : "sitemap.$phpEx");
		$links['setup']['google'] = array(
			'override_mod_rewrite' => $override_google_mod_rewrite,
			'mod_rewrite' => $google_mod_rewrite,
			'override_gzip' => $override_google_gzip,
			'link_main' => $display_google_main_links,
			'link_index' => $display_google_index_links,
			'link_cat' => $display_google_cat_links,
			'gzip' => $google_gzip,
			'gzip_ext' => $google_gzip_ext,
			'google_url' => $gym_config['google_url'],
			'threshold' => max(1, (int) $gym_config['google_threshold']),
			'l_google_sitemap' => $user->lang['GOOGLE_SITEMAP'],
			'l_google_sitemap_of' => $user->lang['GOOGLE_MAP_OF'],
		);
		// only publicly readable and not thresholded forums will be listed
		if (!empty($gym_config['google_forum_installed'])) {
			$google_forum_mod_rewrite = (boolean) get_gym_option('google', 'forum', 'modrewrite', $override_google_mod_rewrite, $gym_config);
			$google_auth_guest = array_diff_assoc($auth_guest_list['read_post'], set_exclude_list($gym_config['google_forum_exclude']), $auth_guest_list['thresholded']);
			$google_forum_exclude = set_exclude_list($gym_config['google_forum_exclude']) + $auth_guest_list['skip_all'];
			$links['setup']['google'] = array_merge( $links['setup']['google'],
				array(
					'forum_google' => true,
					'forum_cat_google' => $gym_config['google_url'] . ($google_forum_mod_rewrite && $_phpbb_seo ? "%1\$s.xml$google_gzip_ext" : "sitemap.$phpEx?forum=%2\$s"),
					'auth_guest' => $google_auth_guest,
					'forum_exclude' => $google_forum_exclude,
				)
			);
		}
		$links['main'] = array_merge( $links['main'],
			array(
				'GYM_GOOGLE_TITLE' => $user->lang['GOOGLE_SITEMAPINDEX'],
				'GYM_GOOGLE_URL' => $sitemap_url,
				'GYM_GOOGLE_LINK' => $display_google_main_links ? sprintf($gym_link_tpl, $sitemap_url, 'sitemap-icon.gif', $user->lang['GOOGLE_SITEMAPINDEX']) : '',
				'GYM_GOOGLE_THRESOLD' => (int) $links['setup']['google']['threshold'],
			)
		);
		$do_display_main = $display_google_main_links ? true : $do_display_main;
		$do_display_index = $display_google_index_links ? true : $do_display_index;
		$do_display_cat = $display_google_cat_links ? true : $do_display_cat;
	}
	// RSS
	if ($rss_active) {
		$display_rss_main_links = (boolean) get_gym_option('rss', 'gym', 'link_main', OVERRIDE_MODULE, $gym_config);
		$display_rss_index_links = (boolean) get_gym_option('rss', 'gym', 'link_index', OVERRIDE_MODULE, $gym_config);
		$display_rss_cat_links = (boolean) get_gym_option('rss', 'gym', 'link_cat', OVERRIDE_MODULE, $gym_config);
		$override_rss_mod_rewrite = get_override('rss', 'modrewrite', $gym_config);
		$rss_mod_rewrite = (boolean) get_gym_option('rss', 'gym', 'modrewrite', $override_rss_mod_rewrite, $gym_config);
		$rss_modrtype = max(0, (int) get_gym_option('rss', 'gym', 'modrtype', $override_rss_mod_rewrite, $gym_config));
		$override_rss_gzip = get_override('rss', 'gzip', $gym_config);
		$rss_gzip = (boolean) get_gym_option('rss', 'forum', 'gzip', $override_rss_gzip, $gym_config);
		$rss_gzip_ext = ($rss_gzip || $config['gzip_compress']) ? (get_gym_option('rss', 'forum', 'gzip_ext', $override_rss_gzip, $gym_config) ? '.gz' : '') : '';
		// Take car of linking type
		$link_type_sep = $rss_mod_rewrite ? '/' : '&amp;';
		$link_type_to_options = array('n' => 'news', 'nd' => 'news'. $link_type_sep . 'digest', 'r' => '', 'rd' => 'digest');
		$link_type_bit = isset($link_type_to_options[$gym_config['rss_linking_type']]) ? $link_type_to_options[$gym_config['rss_linking_type']] : '';
		$rss_main_url = $gym_config['rss_url'] . ($rss_mod_rewrite ? 'rss/' . ($link_type_bit ? $link_type_bit . '/' : '') . 'rss.xml' . $rss_gzip_ext : "gymrss.$phpEx" . ($link_type_bit ? '?' . $link_type_bit : ''));
		$rss_chan_url = $gym_config['rss_url'] . ($rss_mod_rewrite ? 'rss/' . ($link_type_bit ? $link_type_bit . '/' : '') : "gymrss.$phpEx?channels" . ($link_type_bit ? '&amp;' . $link_type_bit : ''));
		$links['setup']['rss'] = array(
			'display_alternate' => (int) $gym_config['rss_alternate'],
			'link_main' => $display_rss_main_links,
			'link_index' => $display_rss_index_links,
			'link_cat' => $display_rss_cat_links,
			'override_mod_rewrite' => $override_rss_mod_rewrite,
			'mod_rewrite' => $rss_mod_rewrite,
			'override_gzip' => $override_rss_gzip,
			'gzip' => $rss_gzip,
			'gzip_ext' => $rss_gzip_ext,
			'rss_url' => $gym_config['rss_url'],
			'l_rss_feed' => $user->lang['RSS_FEED'],
			'l_rss_feed_of' => $user->lang['RSS_FEED_OF'],
		);
		if (!empty($gym_config['rss_forum_installed'])) {
			$rss_forum_allow_auth = (boolean) get_gym_option('rss', 'forum', 'allow_auth', $gym_config['rss_override'], $gym_config);
			// only readable forums can be listed
			$rss_auth_guest = array_diff_assoc($auth_guest_list['read_post'], set_exclude_list($gym_config['rss_forum_exclude']), $auth_guest_list['empty']);
			$rss_forum_exclude = set_exclude_list($gym_config['rss_forum_exclude']) + $auth_guest_list['skip_all'] + $auth_guest_list['empty'];
			$rss_forum_mod_rewrite = (boolean) get_gym_option('rss', 'forum', 'modrewrite', $override_rss_mod_rewrite, $gym_config);
			$rss_forum_modrtype = max(0, (int) get_gym_option('rss', 'forum', 'modrtype', $override_rss_mod_rewrite, $gym_config));

			$links['setup']['rss'] = array_merge( $links['setup']['rss'],
				array(
					'display_forum_alternate' => (int) $gym_config['rss_forum_alternate'],
					'forum_rss' => !empty($gym_config['rss_forum_installed']),
					'forum_cat_rss' => $gym_config['rss_url'] . ($rss_forum_mod_rewrite && $_phpbb_seo ? ($rss_forum_modrtype > 1 ? "%1\$s/" . ($link_type_bit ? $link_type_bit . '/' : '') . "forum.xml$rss_gzip_ext" : "forum" . $phpbb_seo->seo_delim['forum'] . "%2\$s/" . ($link_type_bit ? $link_type_bit . '/' : '') . "forum.xml$rss_gzip_ext") : "gymrss.$phpEx?forum=%2\$s" . ($link_type_bit ? '&amp;' . $link_type_bit : '')),
					'auth_guest' => $rss_auth_guest,
					'forum_exclude' => $rss_forum_exclude,
					'forum_allow_auth' => $rss_forum_allow_auth,
				)
			);
		}
		$links['main'] = array_merge( $links['main'],
			array(
				'GYM_RSS_TITLE' => $user->lang['RSS_FEED'],
				'GYM_RSS_URL' => $rss_main_url,
				'GYM_RSS_LINK' => $display_rss_main_links ? sprintf($gym_link_tpl, $rss_main_url, 'feed-icon.png', $user->lang['RSS_FEED']) : '',
				'GYM_RSS_CHAN_TITLE' => $user->lang['RSS_CHAN_LIST_TITLE'],
				'GYM_RSS_CHAN_URL' => $rss_chan_url,
				'GYM_RSS_CHAN_LINK' => $display_rss_main_links ? sprintf($gym_link_tpl, $rss_chan_url, 'feed-icon.png', $user->lang['RSS_CHAN_LIST_TITLE']) : '',
			)
		);
		$do_display_main = $display_rss_main_links ? true : $do_display_main;
		$do_display_index = $display_rss_index_links ? true : $do_display_index;
		$do_display_cat = $display_rss_cat_links ? true : $do_display_cat;
	}
	// HTML
	if ($html_active) {
		$display_html_main_links = (boolean) get_gym_option('html', 'gym', 'link_main', OVERRIDE_MODULE, $gym_config);
		$display_html_index_links = (boolean) get_gym_option('html', 'gym', 'link_index', OVERRIDE_MODULE, $gym_config);
		$display_html_cat_links = (boolean) get_gym_option('html', 'gym', 'link_cat', OVERRIDE_MODULE, $gym_config);
		$override_html_mod_rewrite = get_override('html', 'modrewrite', $gym_config);
		$html_mod_rewrite = (boolean) get_gym_option('html', 'gym', 'modrewrite', $override_html_mod_rewrite, $gym_config);
		$html_allow_map = (boolean) $gym_config['html_allow_map'];
		$html_allow_cat_map = (boolean) $gym_config['html_allow_cat_map'];
		$html_allow_news = (boolean) $gym_config['html_allow_news'];
		$html_allow_cat_news = (boolean) $gym_config['html_allow_cat_news'];
		$html_map_url = $gym_config['html_allow_map'] ? $gym_config['html_url'] . ($html_mod_rewrite ? 'maps/' : "map.$phpEx") : '';
		$html_news_url = $gym_config['html_allow_news'] ? $gym_config['html_url'] . ($html_mod_rewrite ? 'news/' : "map.$phpEx?news") : '';
		$links['setup']['html'] = array(
			'link_main' => $display_html_main_links,
			'link_index' => $display_html_index_links,
			'link_cat' => $display_html_cat_links,
			'override_mod_rewrite' => $override_html_mod_rewrite,
			'mod_rewrite' => $html_mod_rewrite,
			'html_url' => $gym_config['html_url'],
			'allow_map' => $html_allow_map,
			'allow_news' => $html_allow_news,
			'allow_cat_map' => $html_allow_cat_map,
			'allow_cat_news' => $html_allow_cat_news,
			'l_html_news' => $user->lang['HTML_NEWS'],
			'l_html_map' => $user->lang['HTML_MAP'],
			'l_html_news_of' => $user->lang['HTML_NEWS_OF'],
			'l_html_map_of' => $user->lang['HTML_MAP_OF'],
		);
		if (!empty($gym_config['html_forum_installed'])) {
			$html_forum_mod_rewrite = (boolean) get_gym_option('html', 'forum', 'modrewrite', $override_html_mod_rewrite, $gym_config);
			$html_forum_allow_map = (boolean) get_gym_option('html', 'forum', 'allow_map', $gym_config['html_override'], $gym_config);
			$html_forum_allow_cat_map = (boolean) get_gym_option('html', 'forum', 'allow_cat_map', $gym_config['html_override'], $gym_config);
			$html_forum_allow_news = (boolean) get_gym_option('html', 'forum', 'allow_news', $gym_config['html_override'], $gym_config);
			$html_forum_allow_cat_news = (boolean) get_gym_option('html', 'forum', 'allow_cat_news', $gym_config['html_override'], $gym_config);
			$html_auth_guest = array_diff_assoc($auth_guest_list['list'], set_exclude_list($gym_config['html_forum_exclude']), $auth_guest_list['empty']);
			$html_forum_allow_auth = (boolean) get_gym_option('html', 'forum', 'allow_auth', $gym_config['html_override'], $gym_config);
			$html_forum_exclude = set_exclude_list($gym_config['html_forum_exclude']) + $auth_guest_list['skip_link'] + $auth_guest_list['empty'];
			$links['setup']['html'] = array_merge( $links['setup']['html'],
				array(
					'forum_allow_map' => $html_forum_allow_map,
					'forum_map_url' => $html_allow_map ? $gym_config['html_url'] . ($html_forum_mod_rewrite ? 'maps/forum/' : "map.$phpEx?forum") : '',
					'forum_allow_news' => $html_forum_allow_news,
					'forum_news_url' => $html_allow_news ? $gym_config['html_url'] . ($html_forum_mod_rewrite ? 'news/forum/' : "map.$phpEx?forum=news") : '',
					'forum_allow_cat_map' => $html_forum_allow_cat_map,
					'forum_cat_map' => $gym_config['html_url'] . ($html_forum_mod_rewrite && $_phpbb_seo ? 'maps/forum/%1$s/' : "map.$phpEx?forum=%2\$s"),
					'forum_allow_cat_news' => $html_forum_allow_cat_news,
					'forum_cat_news' => $gym_config['html_url'] . ($html_forum_mod_rewrite && $_phpbb_seo ? 'news/forum/%1$s/' : "map.$phpEx?forum=%2\$s&amp;news"),
					'auth_guest' => $html_auth_guest,
					'forum_exclude' => $html_forum_exclude,
					'forum_allow_auth' => $html_forum_allow_auth,
				)
			);
		}
		$links['main'] = array_merge( $links['main'],
			array(
				'GYM_HTML_NEWS_TITLE' => $user->lang['HTML_NEWS'],
				'GYM_HTML_NEWS_URL' => $html_news_url,
				'GYM_HTML_NEWS_LINK' => $display_html_main_links ? sprintf($gym_link_tpl, $html_news_url, 'html_news.gif', $user->lang['HTML_NEWS']) : '',
				'GYM_HTML_MAP_TITLE' => $user->lang['HTML_MAP'],
				'GYM_HTML_MAP_URL' => $html_map_url,
				'GYM_HTML_MAP_LINK' => $display_html_main_links ? sprintf($gym_link_tpl, $html_map_url, 'maps-icon.gif', $user->lang['HTML_MAP']) : '',
				'GYM_HTML_THEFORUM_NEWS_TITLE' => $user->lang['HTML_FORUM_NEWS'],
				'GYM_HTML_THEFORUM_NEWS_URL' => $links['setup']['html']['forum_news_url'],
				'GYM_HTML_THEFORUM_NEWS_LINK' => sprintf($gym_link_tpl, $links['setup']['html']['forum_news_url'], 'html_news.gif', $user->lang['HTML_FORUM_NEWS']),
				'GYM_HTML_THEFORUM_MAP_TITLE' => $user->lang['HTML_FORUM_MAP'],
				'GYM_HTML_THEFORUM_MAP_URL' => $links['setup']['html']['forum_map_url'],
				'GYM_HTML_THEFORUM_MAP_LINK' => sprintf($gym_link_tpl, $links['setup']['html']['forum_map_url'], 'maps-icon.gif', $user->lang['HTML_FORUM_MAP']),
			)
		);
		$do_display_main = $display_html_main_links ? true : $do_display_main;
		$do_display_index = $display_html_index_links ? true : $do_display_index;
		$do_display_cat = $display_html_cat_links ? true : $do_display_cat;
	}
	$links['setup']['main'] = array(
		'link_main' => ($gym_config['gym_link_main'] && $do_display_main) ? 1 : 0,
		'link_index' => ($gym_config['gym_link_index'] && $do_display_index) ? 1 : 0,
		'link_cat' => ($gym_config['gym_link_cat'] && $do_display_cat) ? 1 : 0,
		'f_public_read' => array_diff_assoc($auth_guest_list['read'], $auth_guest_list['skip_pass']) + array_intersect_assoc($auth_guest_list['skip_cat'], $auth_guest_list['list']),
	);
	$links['main'] = array_merge( $links['main'],
		array(
			'GYM_LINKS' => $links['setup']['main']['link_main'],
			'GYM_LINKS_CAT' => $links['setup']['main']['link_cat'],
		)
	);
	$links['alternate'] = array();
	if (!empty($links['setup']['rss']['display_alternate'])) {
		$links['alternate'] = array(
			array( 'TITLE' => $user->lang['RSS_FEED'],
				'URL' => $rss_main_url ),
			array( 'TITLE' => $user->lang['RSS_CHAN_LIST_TITLE'],
				'URL' => $rss_chan_url ),
		);
	}
	return $links;
}
/**
* get_feed_data($_params)
* returns the parsed feed.
* */
function get_feed_data($_params) {
	global $user, $config;
	$feed_data = array('items' => array(),
		'setup' => array('date' => false, 'author' => false, 'desclen' => 0, 'chantitle' => '', 'chanlink' => ''),
	);
	@ini_set('user_agent','GYM Sitemaps &amp; RSS / www.phpBB-SEO.com');
	@ini_set('default_socket_timeout', 5);
	$xml = @file_get_contents($_params['url']);
	if ($xml) {
		// Get encoding
		$encoding = get_match('`encoding=[\'"]([a-z0-9_-]+)[\'"]`Usi', $xml);
		$encoding = !empty($encoding) ? strtolower($encoding) : detect_encoding($xml);
		if(preg_match('`<item>(.*)</item>`si', $xml, $matches)){
			// Get chan info
			$feed_data['setup']['chantitle'] = get_match('`<title>(.*)</title>`Usi', $xml, $encoding);
			$feed_data['setup']['chanlink'] = get_match('`<link>(.*)</link>`Usi', $xml, $encoding);
			$xml = trim($matches[0]);
			unset($matches);
			preg_match_all('`<item>(.*)</item>`Usi', $xml, $matches);
			unset($matches[0]);
			if (!empty($matches[1]) && is_array($matches[1])) {
				$i = 1;
				foreach($matches[1] as $key => $item) {
					if ($i > $_params['limit']) {
						break;
					}
					if ($title = get_match('`<title>(.*)</title>`Usi', $item, $encoding)) {
						if ($link = get_match('`<link>(.*)</link>`Usi', $item, $encoding)) {
							$feed_data['items'][$i]['PUBDATE'] = false;
							if ($pubdate = get_match('`<pubDate>(.*)</pubDate>`Usi', $item, $encoding)) {
								if (($pubdate = strtotime($pubdate, $user->time_now)) > 0) {
									$feed_data['items'][$i]['PUBDATE'] = $user->format_date($pubdate, $config['default_dateformat']);
									$feed_data['setup']['date'] = true;
								}
							}
							$feed_data['items'][$i]['DESC'] = false;
							if ($_params['desc'] && $description = get_match('`<description>(.*)</description>`Usi', $item, $encoding)) {
								if (empty($_params['html'])) {
									$description = htmlspecialchars($_params['striptags'] ? strip_tags($description) : $description, ENT_COMPAT, 'UTF-8');
								}
								$feed_data['items'][$i]['DESC'] = str_replace(array("\r", "\n"), '<br />', $description);
								$feed_data['setup']['desclen'] += utf8_strlen($feed_data['items'][$i]['DESC']);
							}
							$feed_data['items'][$i]['SOURCE'] = false;
							if ($source = get_match('`<source[\s]+url="(.*)">(.*)</source>`Usi', $item, $encoding)) {
								$feed_data['items'][$i]['SOURCE'] = htmlspecialchars($source, ENT_COMPAT, 'UTF-8');
							}
							$feed_data['items'][$i]['TITLE'] = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');
							$feed_data['items'][$i]['LINK'] = htmlspecialchars($link, ENT_COMPAT, 'UTF-8');
							$feed_data['items'][$i]['IMG'] = $user->img('topic_read', $feed_data['items'][$i]['TITLE'], false, '', 'src');
							$i++;
						}
					}
					unset($matches[1][$key]);
				}
			}
			unset($matches);
		}
	}
	unset($xml);
	return $feed_data;
}
/**
* detect_encoding($string)
* Inspired from php.net : http://www.php.net/mb_detect_encoding
*/
function detect_encoding($string) {
	if (function_exists('mb_detect_encoding')) {
		if ($encoding = @mb_detect_encoding($string . 'a')) {
			return strtolower($encoding);
		}
	}
	// Else at least try to see if utf-8, otherwise fall back to iso-8859-1
	// non-overlong 2-byte|excluding overlongs|straight 3-byte|excluding surrogates|planes 1-3|planes 4-15|plane 16
	return preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF] |\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $string) ? 'utf-8' : 'iso-8859-1';
}
/**
* get_match($pattern, $string, $encoding)
* returns properly encoded match from feed
*/
function get_match($pattern, $string, $encoding = 'utf-8') {
	static $filters = array('<![CDATA['=>'', ']]>'=>'');
	$string = strtr($string, $filters);
	preg_match($pattern, $string, $out);
	if(!empty($out[1])) {
  		// cdata
  		$out[1] = strtr($out[1], $filters);
  		if( strtolower($encoding) != 'utf-8') {
			$out[1] = utf8_recode($out[1], $encoding);
		}
  		return html_entity_decode(trim($out[1]), ENT_COMPAT, 'UTF-8');
	} else {
		return '';
	}
}
/**
* set_exclude_list($id_list) will build up the public unauthed ids
*/
function set_exclude_list($id_list) {
	$exclude_list = empty($id_list) ? array() : explode(',', $id_list);
	$ret = array();
	foreach ($exclude_list as $value ) {
		$value = (int) trim($value);
		if (!empty($value)) {
			$ret[$value] = $value;
		}
	}
	return $ret;
}
?>