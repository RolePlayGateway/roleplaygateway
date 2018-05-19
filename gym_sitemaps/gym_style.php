<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_style.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/


define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

// Report all errors, except notices
error_reporting(E_ALL ^ E_NOTICE);

if (version_compare(PHP_VERSION, '6.0.0-dev', '<')) {
	@set_magic_quotes_runtime(0);
}
// Load Extensions
if (!empty($load_extensions)) {
	$load_extensions = explode(',', $load_extensions);
	foreach ($load_extensions as $extension) {
		@dl(trim($extension));
	}
}
// Option, strip the white spaces in the output, saves a bit of bandwidth.
$strip_spaces = true;
// Option, grabb phpBB stylesheet if using prosilver, will adapt the styling
$load_phpbb_css = false;
// Will automatically update the cache in case the original files are modified.
// Rss or google output
$action_expected = array('rss', 'google');
// CSS or XSLT stylsheet
$type_expected = array('css', 'xsl');

// Language
$language = (isset($_GET['lang']) && !is_array($_GET['lang'])) ? htmlspecialchars($_GET['lang']) : '';
$action = isset($_GET['action']) && in_array($_GET['action'], $action_expected) ? trim($_GET['action']) : '';
$type = isset($_GET['type']) && in_array($_GET['type'], $type_expected) ? trim($_GET['type']) : '';
$theme_id = isset($_GET['theme_id']) ? intval($_GET['theme_id']) : '';

if (empty($language) && empty($action) && empty($type) && empty($theme_id)) {
	// grabb vars like this because browser are not aggreeing on how to handle & in xml. FF only accpet & where IE and opera only accept &amp;
	$qs = !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '';
	preg_match('`action-(rss|google),type-(xsl),lang-([a-z_]+),theme_id-([0-9]+)`i', $qs, $matches );
	$language = !empty($matches[3])  ? htmlspecialchars($matches[3]) : '';
	$action = !empty($matches[1]) && in_array($matches[1], $action_expected) ? trim($matches[1]) : '';
	$type = !empty($matches[2]) && in_array($matches[2], $type_expected) ? trim($matches[2]) : '';
	$theme_id = !empty($matches[4])  ? intval($matches[4]) : '';
}
$content_type = $type == 'css' ? 'text/css' : 'text/xml';
// Expire time of seven days if not recached
$cache_ttl = 7*86400;
$recache = false;
$theme = false;
// Let's go
if (!empty($action) && !empty($type) && !empty($language) && !empty($theme_id)) {
	// build cache file name
	$file = "{$phpbb_root_path}gym_sitemaps/cache/style_{$action}_{$language}_$theme_id.$type";
	if (file_exists($file)) {
		$cached_time = filemtime($file);
		$expire_time = $cached_time + $cache_ttl;
		$recache = $expire_time < time() ? true : /*(filemtime($style_file) > $cached_time ? true :*/ false/*)*/;
	} else {
		$recache = true;
		$expire_time = time() + $cache_ttl;
	}
	if (!$recache) {
		header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $expire_time));
		header('Content-type: ' . $content_type . '; charset=UTF-8');
		readfile($file);
		// We are done with this call
		exit;
	} else {
		// Include files
		require($phpbb_root_path . 'config.' . $phpEx);
		if (empty($acm_type) || empty($dbms)) {
			exit;
		}
		require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
		require($phpbb_root_path . 'includes/cache.' . $phpEx);
		require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
		require($phpbb_root_path . 'includes/constants.' . $phpEx);
		require_once($phpbb_root_path . 'gym_sitemaps/includes/gym_common.' . $phpEx);
		$db = new $sql_db();
		$cache = new cache();
		// Connect to DB
		if (!@$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false)) {
			exit;
		}
		unset($dbhost, $dbuser, $dbpasswd, $dbname, $dbport);
		$config = $cache->obtain_config();
		$gym_config = array();
		obtain_gym_config($action, $gym_config);
		// Do we load phpbb css ?
		$load_phpbb_css = isset($gym_config[$action .  '_load_phpbb_css']) ? $gym_config[$action .  '_load_phpbb_css'] : $load_phpbb_css;

		// Check if requested style does exists
		if ($theme_id > 0) {
			$sql = 'SELECT s.style_id, c.theme_path, c.theme_name, t.template_path
				FROM ' . STYLES_TABLE . ' s, ' . STYLES_TEMPLATE_TABLE . ' t, ' . STYLES_THEME_TABLE . ' c
				WHERE s.style_id = ' . $theme_id . '
					AND t.template_id = s.template_id
					AND c.theme_id = s.theme_id';
			$result = $db->sql_query($sql, 300);
			$theme = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		// Grabb the default one data instead
		if (!$theme) {
			// grabb the first available one
			$theme_id = (int) $config['default_style'];
			$sql = 'SELECT s.style_id, c.theme_path, c.theme_name, t.template_path
				FROM ' . STYLES_TABLE . ' s, ' . STYLES_TEMPLATE_TABLE . ' t, ' . STYLES_THEME_TABLE . ' c
				WHERE s.style_id = ' . $theme_id . '
					AND t.template_id = s.template_id
					AND c.theme_id = s.theme_id';
			$result = $db->sql_query($sql, 300);
			$theme = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		}
		// Determine style file name
		$tpath = $type == 'xsl' ? $theme['template_path'] . '/template/gym_sitemaps' : $theme['theme_path'] . '/theme';
		$style_file = $phpbb_root_path . "styles/$tpath/gym_{$action}.$type";
		if (!file_exists($style_file)) {
			// Degrade to default styling
			$style_file = $phpbb_root_path . "gym_sitemaps/style/gym_{$action}.$type";
			$load_phpbb_css = false;
		}
		$db->sql_close();
		if (!empty($cache)) {
			$cache->unload();
		}
		// No available style
		if (!$theme) {
			exit;
		}
		// Load the language file
		if (file_exists($phpbb_root_path . 'language/' . $language . '/gym_sitemaps/gym_common.' . $phpEx)) {
			require($phpbb_root_path . 'language/' . $language . '/gym_sitemaps/gym_common.' . $phpEx);
			require($phpbb_root_path . 'language/' . $language . '/common.' . $phpEx);
		} else { // Try with the default language
			$language = $config['default_lang'];
			require($phpbb_root_path . 'language/' . $language . '/gym_sitemaps/gym_common.' . $phpEx);
			require($phpbb_root_path . 'language/' . $language . '/common.' . $phpEx);
		}
		// Do not recache is up to date, recompile if the stylesheet was updated
		$file = "{$phpbb_root_path}gym_sitemaps/cache/style_{$action}_{$language}_$theme_id.$type";
		if (file_exists($file)) {
			$cached_time = filemtime($file);
			$expire_time = $cached_time + $cache_ttl;
			$recache = $expire_time < time() ? true : (filemtime($style_file) > $cached_time ? true : false);
		} else {
			$recache = true;
			$expire_time = time() + $cache_ttl;
		}
		if (!$recache) {
			header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $expire_time));
			header('Content-type: ' . $content_type . '; charset=UTF-8');
			readfile($file);
			exit;
		}
		// Path Settings
		$server_protocol = ($config['server_protocol']) ? $config['server_protocol'] : (($config['cookie_secure']) ? 'https://' : 'http://');
		$server_name = trim($config['server_name'], '/ ');
		$server_port = max(0, (int) $config['server_port']);
		$server_port = ($server_port && $server_port <> 80) ? ':' . $server_port . '/' : '/';
		$script_path = trim($config['script_path'], '/ ');
		$script_path = (empty($script_path) ) ? '' : $script_path . '/';
		$root_url = strtolower($server_protocol . $server_name . $server_port);
		// First grabb the online style
		$phpbb_url = $root_url . $script_path;
		// Parse Theme Data
		$replace = array(
			'{T_IMAGE_PATH}'	=> "{$phpbb_url}gym_sitemaps/images/",
			'{T_STYLE_PATH}'	=> "{$phpbb_url}gym_sitemaps/style/",
			'{S_CONTENT_DIRECTION}'	=> $lang['DIRECTION'],
			'{S_USER_LANG}'		=> $language
		);
		if ($type == 'xsl') {
			$replace = array_merge($replace, array(
				'{T_CSS_PATH}'		=> "{$phpbb_url}gym_sitemaps/gym_style.$phpEx?action=$action&amp;type=css&amp;lang={$language}&amp;theme_id={$theme_id}",
				'{L_HOME}'		=> $lang['GYM_HOME'],
				'{L_FORUM_INDEX}'	=> $lang['GYM_FORUM_INDEX'],
				'{L_LINK}'		=> $lang['GYM_LINK'],
				'{L_LASTMOD_DATE}'	=> $lang['GYM_LASTMOD_DATE'],
				'{ROOT_URL}'		=> $root_url,
				'{PHPBB_URL}'		=> $phpbb_url,
				// Do not remove !
				'{L_COPY}'		=>  '<a href="http://www.phpbb-seo.com/" title="GYM Sitemaps &amp; RSS &#169; 2006, 2007, 2008 phpBB SEO" class="copyright"><img src="' . $phpbb_url . 'gym_sitemaps/images/phpbb-seo.png" alt="' . $lang['GYM_SEO'] . '"/></a>',
				'{L_SEARCH_ADV_EXPLAIN}' => $lang['SEARCH_ADV_EXPLAIN'],
				'{L_CHANGE_FONT_SIZE}'  => $lang['CHANGE_FONT_SIZE'],
				'{L_SEARCH_ADV}' 	=> $lang['SEARCH_ADV'],
				'{L_SEARCH}' 		=> $lang['SEARCH'],
				'{L_BACK_TO_TOP}' 	=> $lang['BACK_TO_TOP'],
				'{L_FAQ}' 		=> $lang['FAQ'],
				'{L_FAQ_EXPLAIN}' 	=> $lang['FAQ_EXPLAIN'],
				'{L_REGISTER}' 		=> $lang['REGISTER'],
				'{L_SKIP}' 		=> $lang['SKIP'],
				'{L_BOOKMARK_THIS}' 	=> $lang['GYM_BOOKMARK_THIS'],
				'{SITENAME}' 		=> htmlspecialchars($config['sitename']),
				'{SITE_DESCRIPTION}' 	=> $config['site_desc'],

			));
			if ($action == 'google') {
				$replace = array_merge($replace, array(
					'{L_SITEMAP}'		=> $lang['GOOGLE_SITEMAP'],
					'{L_SITEMAP_OF}'	=> $lang['GOOGLE_SITEMAP_OF'],
					'{L_SITEMAPINDEX}'	=> $lang['GOOGLE_SITEMAPINDEX'],
					'{L_NUMBER_OF_SITEMAP}'	=> $lang['GOOGLE_NUMBER_OF_SITEMAP'],
					'{L_SITEMAP_URL}'	=> $lang['GOOGLE_SITEMAP_URL'],
					'{L_NUMBER_OF_URL}'	=> $lang['GOOGLE_NUMBER_OF_URL'],
					'{L_CHANGEFREQ}'	=> $lang['GOOGLE_CHANGEFREQ'],
					'{L_PRIORITY}'		=> $lang['GOOGLE_PRIORITY'],
				));
			} elseif ($action == 'rss') {
				$replace = array_merge($replace, array(
					'{L_UPDATE}'		=> $lang['RSS_UPDATE'],
					'{L_LAST_UPDATE}'	=> $lang['RSS_LAST_UPDATE'],
					'{L_MINUTES}'		=> $lang['GYM_MINUTES'],
					'{L_SOURCE}'		=> $lang['GYM_SOURCE'],
					'{L_SUBSCRIBE_POD}'	=> $lang['RSS_SUBSCRIBE_POD'],
					'{L_SUBSCRIBE}'		=> $lang['RSS_SUBSCRIBE'],
					'{L_2_LINK}'		=> $lang['RSS_2_LINK'],
					'{L_FEED}'		=> $lang['RSS_FEED'],
					'{L_ITEM_LISTED}'	=> $lang['RSS_ITEM_LISTED'],
					'{L_ITEMS_LISTED}'	=> $lang['RSS_ITEMS_LISTED'],
					'{L_RSS_VALID}'		=> $lang['RSS_VALID'],
				));
			}
		}
		// Load the required stylsheet template
		if ( $load_phpbb_css && $type == 'css' ) {
			@ini_set('user_agent','GYM Sitemaps &amp; RSS / www.phpBB-SEO.com');
			@ini_set('default_socket_timeout', 10);
			$phpbb_css = @file_get_contents("{$phpbb_url}style.php?id={$theme_id}&lang={$language}");
			if ($phpbb_css) {
				$output = str_replace('./styles/', "{$phpbb_url}styles/", $phpbb_css);
			} else {
				$style_tpl = @file_get_contents($style_file);
				$output = str_replace(array_keys($replace), array_values($replace), $style_tpl);
			}
			unset($phpbb_css);
		} else {
			$style_tpl = @file_get_contents($style_file);
			$output = str_replace(array_keys($replace), array_map('numeric_entify_utf8', array_values($replace)), $style_tpl);
		}
		if ($strip_spaces) {
			if ($type === 'xsl') {
				$output = preg_replace(array('`<\!--.*-->`Us', '`[\s]+`'), ' ', $output);
			} else {
				$output = preg_replace(array('`/\*.*\*/`Us', '`[\s]+`'), ' ', $output);
			}
		}
		$handle = @fopen($file, 'wb');
		@flock($handle, LOCK_EX);
		@fwrite($handle, $output);
		@flock($handle, LOCK_UN);
		@fclose ($handle);
		@chmod($file, 0666);

		header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $expire_time));
		header('Content-type: ' . $content_type . '; charset=UTF-8');
		echo $output;
		exit;
	}
}
exit;
?>