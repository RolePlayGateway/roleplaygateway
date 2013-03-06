<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_common.php 185 2009-11-26 09:37:00Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
global $table_prefix;
// Table
define('GYM_CONFIG_TABLE', $table_prefix . 'gym_config');
// Overrides (must be numbers, and OVERRIDE_GLOBAL > OVERRIDE_OTYPE > OVERRIDE_MODULE > 0)
define('OVERRIDE_GLOBAL', 3);
define('OVERRIDE_OTYPE', 2);
define('OVERRIDE_MODULE', 1);

// Some set up
$_action_types = array('google' => 'google', 'rss' => 'rss', 'html' => 'html', /*'yahoo' => 'yahoo'*/);
if (defined('ADMIN_START') || defined('IN_INSTALL')) {
	$_action_types['main'] = 'main';
}
$_override_types = array('cache', 'gzip', 'modrewrite', 'limit', 'pagination', 'sort');

/**
* obtain_gym_config ($mode, &$cfg_array).
* Get the required config datas
*/
function obtain_gym_config($mode, &$cfg_array) {
	global $db, $cache;
	$sql_config = '';
	$cache_file = '_gym_config';
	$mode = empty($mode) ? 'main' : $mode;
	if ($mode != 'main') {
		$sql_config = 	"WHERE config_type = 'main' OR config_type = '" . $db->sql_escape($mode). "'";
		$cache_file .= '_' . $mode;
	} else {
		$cache_file .= '_main';
	}
	if (($cfg_array = $cache->get($cache_file)) === false) {
		$cfg_array = array();
		$sql = "SELECT *
			FROM " . GYM_CONFIG_TABLE . "
			$sql_config";
		$db->sql_return_on_error(true);
		$result = $db->sql_query($sql);
		$db->sql_return_on_error(false);
		while ($row = $db->sql_fetchrow($result)) {
			$cfg_array[$row['config_name']] = $row['config_value'];
		}
		$db->sql_freeresult($result);
		$cache->put($cache_file, $cfg_array);
	}
	return;
}
/**
* set_gym_config($config_name, $config_value, $mode, &$cfg_array)
* Set gym_config value. Creates missing config entry if needed.
*/
function set_gym_config($config_name, $config_value, $mode, &$cfg_array) {
	global $db, $_action_types;
	if (!in_array($mode, $_action_types) ) {
		trigger_error('GYM_ERROR_MODULE_TYPE', E_USER_ERROR);
	}
	$sql = 'UPDATE ' . GYM_CONFIG_TABLE . "
		SET config_value = '" . $db->sql_escape($config_value) . "'
		WHERE config_name = '" . $db->sql_escape($config_name) . "'";
	$db->sql_query($sql);
	if (!$db->sql_affectedrows() && !isset($cfg_array[$config_name])) {
		$sql = 'INSERT INTO ' . GYM_CONFIG_TABLE . ' ';
		$sql .= $db->sql_build_array('INSERT', array(
			'config_name'	=> (string) $config_name,
			'config_value'	=> (string) $config_value,
			'config_type'	=> (string) $mode,
			)
		);
		$db->sql_query($sql);
	}
	$cfg_array[$config_name] = $config_value;
	return;
}
/**
* rem_gym_config($config_name, &$cfg_array)
* Delete rem_gym_config value.
*/
function rem_gym_config($config_name, &$cfg_array) {
	global $db;
	$sql = 'DELETE FROM ' . GYM_CONFIG_TABLE . "
		WHERE config_name = '" . $db->sql_escape($config_name) . "'";
	$db->sql_query($sql);
	unset($cfg_array[$config_name]);
	return;
}
/**
* obtain_gym_links().
* Builds the rss and sitemaps links
*/
function obtain_gym_links($gym_links = array()) {
	global $phpbb_root_path, $template, $cache, $config, $phpEx, $user, $phpbb_seo, $auth;
	if (empty($config['gym_installed'])) {
		return;
	}
	$_phpbb_seo = !empty($phpbb_seo);
	$board_url = $_phpbb_seo ? $phpbb_seo->seo_path['phpbb_url'] : generate_board_url() . '/';
	$gym_config = array();
	$cache_file = '_gym_links_' . $user->data['user_lang'];
	$gym_link_tpl = '<a href="%1$s" title="%3$s" class="gym"><img src="' . $board_url . 'gym_sitemaps/images/%2$s" alt="%3$s"/>&nbsp;%4$s</a>&nbsp;';
	if (($links = $cache->get($cache_file)) === false) {
		obtain_gym_config('main', $gym_config);
		$user->add_lang('gym_sitemaps/gym_common');
		if (!defined('GYM_RSS_FUNC_INC')) {
			require($phpbb_root_path . 'gym_sitemaps/includes/gym_rss_functions.' . $phpEx);
		}
		$links = get_gym_links($gym_config);
		$cache->put($cache_file, $links);
	}
	// In case one would want to manually fil the array in some file, like viewforum
	// Would be passed here from page_header() where $gym_links is global
	if (!empty($gym_links['main'])) {
		$links = array_merge($links['main'], $gym_links['main']);
	}
	// A bit dirty but lazy way to add forum maps and news pages everywhere ;-)
	$html_setup = & $links['setup']['html'];
	$rss_setup = & $links['setup']['rss'];
	$google_setup = & $links['setup']['google'];
	if (!empty($html_setup['forum_allow_cat_news']) || !empty($html_setup['forum_allow_cat_map']) || !empty($rss_setup['forum_rss']) || !empty($google_setup['forum_google'])) {
		$_f_sep = $_phpbb_seo ? $phpbb_seo->seo_delim['forum'] : '';
		$display_main_index = !empty($links['setup']['main']['link_index']);
		if ($display_main_index && !empty($template->_tpldata['forumrow'])) {
			foreach ($template->_tpldata['forumrow'] as $k => $v) {
				$num_topics = !empty($v['TOPICS']) ? max(0, (int) $v['TOPICS']) : 0;
				if ($num_topics && empty($v['S_IS_LINK']) && empty($v['S_IS_CAT'])) {
					$link = '';
					$forum_id = (int) $v['FORUM_ID'];
					$forum_name = $v['FORUM_NAME'];
					if (!empty($html_setup['link_index']) && (isset($html_setup['auth_guest'][$forum_id]) || (!empty($html_setup['forum_allow_auth']) && !isset($html_setup['forum_exclude'][$forum_id])))) {
						if ($html_setup['forum_allow_cat_news']) {
							$url = sprintf($html_setup['forum_cat_news'], $_phpbb_seo ? $phpbb_seo->seo_url['forum'][$forum_id] : '', $forum_id );
							$link .= sprintf($gym_link_tpl, $url, 'html_news.gif', sprintf($html_setup['l_html_news_of'], $forum_name), $html_setup['l_html_news']);
						}
						if ($html_setup['forum_allow_cat_map']) {
							$url = sprintf($html_setup['forum_cat_map'], $_phpbb_seo ? $phpbb_seo->seo_url['forum'][$forum_id] : '', $forum_id );
							$link .= ' ' . sprintf($gym_link_tpl, $url, 'maps-icon.gif', sprintf($html_setup['l_html_map_of'], $forum_name), $html_setup['l_html_map']);
						}
					}
					if (!empty($rss_setup['link_index']) && (isset($rss_setup['auth_guest'][$forum_id]) || ($rss_setup['forum_allow_auth'] && !isset($rss_setup['forum_exclude'][$forum_id])))) {
						$url = sprintf($rss_setup['forum_cat_rss'], $_phpbb_seo ? $phpbb_seo->seo_url['forum'][$forum_id] : '', $forum_id );
						$link .= ' ' . sprintf($gym_link_tpl, $url, 'feed-icon.png', sprintf($rss_setup['l_rss_feed_of'], $forum_name), $rss_setup['l_rss_feed']);
					}
					if (!empty($google_setup['link_index']) && isset($google_setup['auth_guest'][$forum_id]) && ($num_topics >= $google_setup['threshold'])) {
							$url = sprintf($google_setup['forum_cat_google'], $_phpbb_seo ? str_replace($_f_sep . $forum_id, '', $phpbb_seo->seo_url['forum'][$forum_id]) . $_f_sep . $forum_id : '', $forum_id );
							$link .= ' ' . sprintf($gym_link_tpl, $url, 'sitemap-icon.gif', sprintf($google_setup['l_google_sitemap_of'], $forum_name), $google_setup['l_google_sitemap']);
					}
					if ($link) {
						$template->_tpldata['forumrow'][$k]['FORUM_DESC'] .= "<br/>$link";
					}
				}
			}
		}
		$display_main_cat = !empty($links['setup']['main']['link_cat']);
		if ($display_main_cat && !empty($template->_rootref['FORUM_NAME']) && !empty($template->_rootref['FORUM_ID'])) {
			$forum_id = (int) $template->_rootref['FORUM_ID'];
			$forum_name = $template->_rootref['FORUM_NAME'];
			$do_display = false;
			if (!empty($template->_tpldata['navlinks'])) {
				$forum_data = $template->_tpldata['navlinks'][count($template->_tpldata['navlinks']) - 1];
			}
			if (!empty($forum_data) ) {
				if ($_phpbb_seo && empty($phpbb_seo->seo_url['forum'][$forum_id])) {
					$phpbb_seo->seo_url['forum'][$forum_id] = $phpbb_seo->set_url($forum_name, $forum_id, $phpbb_seo->seo_static['forum']);
				}
				if (!empty($html_setup['link_cat']) && (isset($html_setup['auth_guest'][$forum_id]) || (!empty($html_setup['forum_allow_auth']) && !isset($html_setup['forum_exclude'][$forum_id]))) ) {
					if ($html_setup['forum_allow_cat_news']) {
						$url = sprintf($html_setup['forum_cat_news'], $_phpbb_seo ? $phpbb_seo->seo_url['forum'][$forum_id] : '', $forum_id);
						$title = sprintf($html_setup['l_html_news_of'], $forum_name);
						$links['main']['GYM_HTML_FORUM_NEWS_LINK'] = sprintf($gym_link_tpl, $url, 'html_news.gif', $title, $html_setup['l_html_news']);
						$do_display = true;
					}
					if ($html_setup['forum_allow_cat_map']) {
						$url = sprintf($html_setup['forum_cat_map'], $_phpbb_seo ? $phpbb_seo->seo_url['forum'][$forum_id] : '', $forum_id );
						$title = sprintf($html_setup['l_html_map_of'], $forum_name);
						$links['main']['GYM_HTML_FORUM_MAP_LINK'] = sprintf($gym_link_tpl, $url, 'maps-icon.gif', $title, $html_setup['l_html_map']);
						$do_display = true;
					}
				}
			}
			if (!empty($forum_data['S_IS_POST'])) {
				if (!empty($rss_setup['link_cat']) && (isset($rss_setup['auth_guest'][$forum_id]) || ($rss_setup['forum_allow_auth'] && !isset($rss_setup['forum_exclude'][$forum_id])) )) {
					$url = sprintf($rss_setup['forum_cat_rss'], $_phpbb_seo ? $phpbb_seo->seo_url['forum'][$forum_id] : '', $forum_id );
					$title = sprintf($rss_setup['l_rss_feed_of'], $forum_name);
					$links['main']['GYM_RSS_FORUM_LINK'] = sprintf($gym_link_tpl, $url, 'feed-icon.png', $title, $rss_setup['l_rss_feed']);
					if (!empty($links['setup']['rss']['display_forum_alternate'])) {
						$links['alternate'][] = array(
							'TITLE' => $title,
							'URL' => $url
						);
					}
					$do_display = true;
				}
				if (!empty($google_setup['link_cat']) && isset($google_setup['auth_guest'][$forum_id])) {
					$url = sprintf($google_setup['forum_cat_google'], $_phpbb_seo ? str_replace($_f_sep . $forum_id, '', $phpbb_seo->seo_url['forum'][$forum_id]) . $_f_sep . $forum_id : '', $forum_id );
					$title = sprintf($google_setup['l_google_sitemap_of'], $forum_name);
					$links['main']['GYM_GOOGLE_FORUM_LINK'] = sprintf($gym_link_tpl, $url, 'sitemap-icon.gif', $title, $google_setup['l_google_sitemap']);
					$do_display = true;
				}
			}
			$links['main']['GYM_LINKS_CAT'] = $do_display;
		}
	}
	if (!empty($links['main'])) {
		$template->assign_vars($links['main']);
	}
	if (!empty($links['alternate'])) {
		foreach ($links['alternate'] as $alternate) {
			$template->assign_block_vars('gym_rsslinks', $alternate);
		}
	}
	return $links['setup'];
}
/**
* display_feed($params, $tpl_prefix = '')
* $params : array of params or string feed URL for defaults
* tpl_prefix is for using different link blocks on one page
* Use display_feed('http://www.example.com/rss/rss.xml') to use default settings.
* */
function display_feed($params, $tpl_prefix = '') {
	global $cache, $user, $config, $template, $phpbb_root_path, $phpEx;
	if (is_string($params)) {
		$params = array('url' => $params);
	}
	$_params = array(
		'url' => trim(str_replace('&amp;', '&', $params['url'])),
		'slide' => !empty($params['slide']),
		'speed' => !empty($params['speed']) ? max((int) $params['speed'], 1) : 30,
		'ttl' => !empty($params['ttl']) ? max((int) $params['ttl'], 0) : 3600,
		'limit' => !empty($params['limit']) ? max((int) $params['limit'], 1) : 5,
		'desc' => !empty($params['desc']),
		'html' => !empty($params['html']),
		'striptags' => !empty($params['striptags']),
	);
	if (empty($_params['url'])) {
		return;
	}
	$cache_file = '_gym_links_' . md5($user->data['user_lang'] . $_params['url']);
	if (($feed_data = $cache->get($cache_file)) === false) {
		if (!defined('GYM_RSS_FUNC_INC')) {
			require($phpbb_root_path . 'gym_sitemaps/includes/gym_rss_functions.' . $phpEx);
		}
		$feed_data = get_feed_data($_params);
		$cache->put($cache_file, $feed_data, $_params['ttl']);
	}
	if (!empty($feed_data['items'])) {
		$template->assign_vars(array($tpl_prefix . 'GYM_RSS_AGREGATED' => true,
			$tpl_prefix . 'GYM_CHAN_TITLE' => $feed_data['setup']['chantitle'],
			$tpl_prefix . 'GYM_CHAN_LINK' => $feed_data['setup']['chanlink'],
			$tpl_prefix . 'GYM_CHAN_SOURCE' => $_params['url'],
			$tpl_prefix . 'GYM_RSS_AUTHOR' => false,
			$tpl_prefix . 'GYM_RSS_DATE' => $feed_data['setup']['date'],
			$tpl_prefix . 'GYM_RSS_DESC' => $_params['desc'],
			$tpl_prefix . 'GYM_RSS_SLIDE' => $_params['slide'],
			$tpl_prefix . 'GYM_RSS_SLIDE_SP' => 'height:' . ($_params['desc'] ? $_params['limit']*45: $_params['limit']*20) . 'px;',
			$tpl_prefix . 'GYM_RSS_SLIDE_SP_JS' => $_params['desc'] ? $_params['limit']*45 : $_params['limit']*20,
			$tpl_prefix . 'GYM_RSS_SLIDE_EP' => $_params['desc'] ? (int) ($_params['limit']*$feed_data['setup']['desclen']/(count($feed_data['items'])*2.67)) : $_params['limit']*45,
			$tpl_prefix . 'GYM_RSS_CSSID' => $tpl_prefix . 'gnews',
			$tpl_prefix . 'GYM_RSS_SCRSPEED' => $_params['speed'],
		));
		$i = 1;
		foreach ($feed_data['items'] as $item) {
			if ($i > $_params['limit']) {
				break;
			}
			$template->assign_block_vars(strtolower($tpl_prefix) . 'gym_link_list', $item);
			$i++;
		}
		unset($feed_data);
	}
}
/**
* get_override($mode, $key, $gym_config)
*
*/
function get_override($mode, $key, $gym_config) {
	return $gym_config['gym_override_' . $key] != OVERRIDE_GLOBAL ? ($gym_config[$mode . '_override_' . $key] != OVERRIDE_GLOBAL ? $gym_config[$mode . '_override_' . $key] : $gym_config['gym_override_' . $key] ) : OVERRIDE_GLOBAL;
}
/**
* get_gym_option($mode, $type, $gym_config)
* Same effect as gym_sitemaps::set_module_option() but with all params and usable outisde the class
*/
function get_gym_option($mode, $module, $key, $override, &$gym_config) {
	return ($override == OVERRIDE_MODULE && @isset($gym_config[$mode . '_' . $module . '_' . $key])) ? $gym_config[$mode . '_' . $module . '_' . $key] : ( ($override != OVERRIDE_GLOBAL && @isset($gym_config[$mode . '_' . $key])) ? $gym_config[$mode . '_' . $key] : ( isset($gym_config['gym_' . $key]) ? $gym_config['gym_' . $key] : ( @isset($gym_config[$mode . '_' . $key]) ? $gym_config[$mode . '_' . $key] : ( @isset($gym_config[$mode . '_' . $module . '_' . $key]) ? $gym_config[$mode . '_' . $module . '_' . $key] : null ) ) ) );
}
/**
* get_date_from_header($response_header_array)
*/
function get_date_from_header($response_header_array) {
	if (is_array($response_header_array)) {
		foreach ($response_header_array as $header_line) {
			if (preg_match('`Date:(.+)`i', $header_line, $match)) {
				return (int) strtotime(trim($match[1]));
			}
		}
	}
	return 0;
}
/**
* numeric_entify_utf8()
* borrowed from php.net : http://www.php.net/utf8_decode
*/
function numeric_entify_utf8($utf8_string) {
	$out = "";
	$ns = strlen ($utf8_string);
	for ($nn = 0; $nn < $ns; $nn ++) {
		$ch = $utf8_string[$nn];
		$ii = ord ($ch);
		if ($ii < 128) { //1 7 0bbbbbbb (127)
			$out .= $ch;
		} elseif ($ii >> 5 == 6) { //2 11 110bbbbb 10bbbbbb (2047)
			$b1 = ($ii & 31);
			$nn ++;
			$ch = $utf8_string[$nn];
			$ii = ord ($ch);
			$b2 = ($ii & 63);
			$ii = ($b1 * 64) + $b2;
			$ent = sprintf ("&#%d;", $ii);
			$out .= $ent;
		} elseif ($ii >> 4 == 14) { //3 16 1110bbbb 10bbbbbb 10bbbbbb
			$b1 = ($ii & 31);
			$nn ++;
			$ch = $utf8_string[$nn];
			$ii = ord ($ch);
			$b2 = ($ii & 63);
			$nn ++;
			$ch = $utf8_string[$nn];
			$ii = ord ($ch);
			$b3 = ($ii & 63);
			$ii = ((($b1 * 64) + $b2) * 64) + $b3;
			$ent = sprintf ("&#%d;", $ii);
			$out .= $ent;
		} elseif ($ii >> 3 == 30) { //4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
			$b1 = ($ii & 31);
			$nn ++;
			$ch = $utf8_string[$nn];
			$ii = ord ($ch);
			$b2 = ($ii & 63);
			$nn ++;
			$ch = $utf8_string[$nn];
			$ii = ord ($ch);
			$b3 = ($ii & 63);
			$nn ++;
			$ch = $utf8_string[$nn];
			$ii = ord ($ch);
			$b4 = ($ii & 63);
			$ii = ((((($b1 * 64) + $b2) * 64) + $b3) * 64) + $b4;
			$ent = sprintf ("&#%d;", $ii);
			$out .= $ent;
		}
	}
	return $out;
}
?>