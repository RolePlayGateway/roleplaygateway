<?php
/**
*
* @package Ultimate SEO URL phpBB SEO
* @version $Id: phpbb_seo_class.php 236 2010-03-03 08:20:36Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl1.5.txt Reciprocal Public License 1.5
*
*/
/**
* @ignore
*/
if (!defined('IN_PHPBB')) {
	exit;
}
require($phpbb_root_path . "phpbb_seo/includes/setup_phpbb_seo.$phpEx");
/**
* phpBB_SEO Class
* www.phpBB-SEO.com
* @package Ultimate SEO URL phpBB SEO
*/
class phpbb_seo extends setup_phpbb_seo {
	var	$version = '0.6.4';
	var	$modrtype = 2; // We set it to mixed as a default value
	var	$seo_path = array();
	var	$seo_url = array( 'forum' =>  array(), 'topic' =>  array(), 'user' => array(), 'username' => array(), 'group' => array(), 'file' => array() );
	var	$phpbb_filter = array(
			'forum' => array('st' => 0, 'sk' => 't', 'sd' => 'd'),
			'topic' => array('st' => 0, 'sk' => 't', 'sd' => 'a', 'hilit' => ''),
			'search' => array('st' => 0, 'sk' => 't', 'sd' => 'd', 'ch' => ''),
		);
	var	$seo_stop_files = array('posting' => 1, 'faq' => 1, 'ucp' => 1, 'swatch' => 1, 'mcp' => 1, 'style' => 1, 'cron' => 1);
	var	$seo_stop_vars = array('view=', 'mark=', 'watch=', 'hash=');
	var	$seo_stop_dirs = array();
	var	$seo_delim = array( 'forum' => '-f', 'topic' => '-t', 'user' => '-u', 'group' => '-g', 'start' => '-', 'sr' => '-', 'file' => '/');
	var	$seo_ext = array( 'forum' => '.html', 'topic' => '.html', 'post' => '.html', 'user' => '.html', 'group' => '.html',  'index' => '', 'global_announce' => '/', 'leaders' => '.html', 'atopic' => '.html', 'utopic' => '.html', 'npost' => '.html', 'urpost' => '.html', 'pagination' => '.html', 'gz_ext' => '');
	var	$seo_static = array( 'forum' => 'forum', 'topic' => 'topic', 'post' => 'post', 'user' => 'member', 'group' => 'group', 'index' => '', 'global_announce' => 'announces', 'leaders' => 'the-team', 'atopic' => 'active-topics', 'utopic' => 'unanswered', 'npost' => 'newposts', 'urpost' => 'unreadposts', 'pagination' => 'page', 'gz_ext' => '.gz' );
	var	$file_hbase = array();
	var	$get_vars = array();
	var	$path = '';
	var	$start = '';
	var	$filename = '';
	var	$file = '';
	var	$url_in = '';
	var	$url = '';
	var	$page_url = '';
	var	$seo_opt = array( 'url_rewrite' => false, 'modrtype' => 2, 'sql_rewrite' => false, 'profile_inj' => false, 'profile_vfolder' => false, 'profile_noids' => false, 'rewrite_usermsg' => false, 'rewrite_files' => false, 'rem_sid' => false, 'rem_hilit' => true, 'rem_small_words' => false, 'virtual_folder' => false, 'virtual_root' => false, 'cache_layer' => true, 'rem_ids' => false, );
	var	$rewrite_method = array();
	var	$paginate_method = array();
	var	$seo_cache = array();
	var	$cache_config = array();
	var	$RegEx = array();
	var	$sftpl = array();
	var	$url_replace = array();
	/**
	* constuctor
	*/
	function phpbb_seo() {
		global $phpEx, $config, $phpbb_root_path;
		// fix for an interesting bug with parse_str http://bugs.php.net/bug.php?id=48697
		// and apparently, the bug is still here in php5.3
		@ini_set("mbstring.internal_encoding", 'UTF-8');
		// Nothing should be edited here, please do your custom settings in the
		// phpbb_seo/includes/phpbb_seo_modules.php instead to make your updates easier.
		// reset the rewrite_method for $phpbb_root_path
		$this->rewrite_method[$phpbb_root_path] = array();
		// phpBB files must be treated a bit differently
		$this->seo_static['file'] = array(ATTACHMENT_CATEGORY_NONE => 'file', ATTACHMENT_CATEGORY_IMAGE => 'image', ATTACHMENT_CATEGORY_WM => 'wm', ATTACHMENT_CATEGORY_RM => 'rm',  ATTACHMENT_CATEGORY_THUMB => 'image', ATTACHMENT_CATEGORY_FLASH => 'flash', ATTACHMENT_CATEGORY_QUICKTIME => 'qt');
		$this->seo_static['file_index'] = 'resources';
		$this->seo_static['thumb'] = 'thumb';
		// Options that may be bypassed by the cached settings.
		$this->cache_config['dynamic_options'] = array_keys($this->seo_opt); // Do not change
		// copyright notice, do not change
		$this->cache_config['dynamic_options']['copyrights'] = $this->seo_opt['copyrights'] = array('img' => true, 'txt' => '', 'title' => '');
		// Caching config
		$this->seo_opt['cache_folder'] = 'phpbb_seo/cache/'; // Folder where the cache file is stored
		define('SEO_CACHE_PATH', rtrim(phpbb_realpath($phpbb_root_path . $this->seo_opt['cache_folder']), '/') . '/'); // do not change
		$this->seo_opt['topic_type'] = array(); // do not change
		$this->seo_opt['topic_last_page'] = array(); // do not change
		$this->cache_config['cache_enable'] = true; // do not change
		$this->cache_config['rem_ids'] = $this->seo_opt['rem_ids']; // do not change, set up above
		$this->cache_config['files'] = array('forum' => 'phpbb_cache.' . $phpEx, 'htaccess' => '.htaccess');
		$this->cache_config['cached'] = false; // do not change
		$this->cache_config['forum'] = array(); // do not change
		$this->cache_config['topic'] = array(); // do not change
		$this->cache_config['settings'] = array(); // do not change
		// --> DOMAIN SETTING <-- //
		// Path Settings, only rely on DB
		$server_protocol = ($config['server_protocol']) ? $config['server_protocol'] : (($config['cookie_secure']) ? 'https://' : 'http://');
		$server_name = trim($config['server_name'], '/ ');
		$server_port = max(0, (int) $config['server_port']);
		$server_port = ($server_port && $server_port <> 80) ? ':' . $server_port : '';
		$script_path = trim($config['script_path'], './ ');
		$script_path = (empty($script_path) ) ? '' : $script_path . '/';
		$this->seo_path['root_url'] = strtolower($server_protocol . $server_name . $server_port . '/');
		$this->seo_path['phpbb_urlR'] = $this->seo_path['phpbb_url'] =  $this->seo_path['root_url'] . $script_path;
		$this->seo_path['phpbb_script'] = $script_path;
		$this->seo_path['phpbb_files'] = $this->seo_path['phpbb_url'] . 'download/';
		$this->seo_path['canonical'] = '';
		// magic quotes, do it like this in case phpbb_seo class is not started in common.php
		if (!defined('STRIP')) {
			if (version_compare(PHP_VERSION, '6.0.0-dev', '<') ) {
				if (get_magic_quotes_gpc()) {
					define('SEO_STRIP', true);
				}
			}
		} elseif (STRIP) {
			define('SEO_STRIP', true);
		}
		// File setting
		$this->seo_req_uri();
		$this->seo_opt['seo_base_href'] = $this->seo_opt['req_file'] = $this->seo_opt['req_self'] = '';
		if ($script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF')) {
			// From session.php
			// Replace backslashes and doubled slashes (could happen on some proxy setups)
			$this->seo_opt['req_self'] = str_replace(array('\\', '//'), '/', $script_name);
			// basenamed page name (for example: index)
			$this->seo_opt['req_file'] = urlencode(htmlspecialchars(str_replace(".$phpEx", '', basename($this->seo_opt['req_self']))));
		}
		// Load settings from phpbb_seo/includes/phpbb_seo_modules.php
		$this->init_phpbb_seo();
		$this->seo_path['phpbb_filesR'] = $this->seo_path['phpbb_urlR'] . $this->seo_static['file_index'] . $this->seo_delim['file'];
		// see if we have some custom replacement
		if (!empty($this->url_replace)) {
			$this->url_replace = array(
				'find' => array_keys($this->url_replace),
				'replace' => array_values($this->url_replace)
			);
		}
		$this->seo_opt['topic_per_page'] = ($config['posts_per_page'] <= 0) ? 1 : $config['posts_per_page']; // do not change
		// Array of the filenames that require the use of a base href tag.
		$this->file_hbase = array_merge(array('viewtopic' => $this->seo_path['phpbb_url'], 'viewforum' => $this->seo_path['phpbb_url'], 'memberlist' => $this->seo_path['phpbb_url'], 'search' => $this->seo_path['phpbb_url']), $this->file_hbase);
		// Stop dirs
		$this->seo_stop_dirs = array_merge(array($phpbb_root_path . 'adm/' => false), $this->seo_stop_dirs);
		// Rewrite functions array : array( 'path' => array('file_name' => 'function_name'));
		// Warning, this way of doing things is path aware, this implies path to be properly sent to append_sid()
		// Allow to add options without slowing down the URL rewriting process
		$this->rewrite_method[$phpbb_root_path] = array_merge(
			array(
				'viewtopic' => 'viewtopic',
				'viewforum' => 'viewforum',
				'index' => 'index',
				'memberlist' => 'memberlist',
				'search' => $this->seo_opt['rewrite_usermsg'] ? 'search' : '',
			),
			$this->rewrite_method[$phpbb_root_path]
		);
		$this->rewrite_method[$phpbb_root_path . 'download/']['file'] = $this->seo_opt['rewrite_files'] ? 'phpbb_files' : '';
		$this->paginate_method = array_merge(
			array(
				'topic' => $this->seo_ext['topic'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
				'forum' => $this->seo_ext['forum'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
				'group' => $this->seo_ext['group'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
				'user' => $this->seo_ext['user'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
				'atopic' => $this->seo_ext['atopic'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
				'utopic' => $this->seo_ext['utopic'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
				'npost' => $this->seo_ext['npost'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
				'urpost' => $this->seo_ext['urpost'] === '/' ? 'rewrite_pagination_page' : 'rewrite_pagination',
			),
			$this->paginate_method
		);
		$this->RegEx = array_merge(
			array(
				'topic' => array(
					'check' => '`^' . ($this->seo_opt['virtual_folder'] ? '%1$s/' : '') . '(' . $this->seo_static['topic'] . '|[a-z0-9_-]+' . $this->seo_delim['topic'] . ')$`i',
					'match' => '`^((([a-z0-9_-]+)(' . $this->seo_delim['forum'] . '([0-9]+))?/)?(' . $this->seo_static['topic'] . '(?!=' . $this->seo_delim['topic'] . ')|.+(?=' . $this->seo_delim['topic'] . '))(' . $this->seo_delim['topic'] . ')?)([0-9]+)$`i',
					'parent' => 2,
					'parent_id' => 5,
					'title' => 6,
					'id' => 8,
					'url' => 1,
				),
				'forum' => array(
					'check' => $this->modrtype >= 2 ? '`^[a-z0-9_-]+(' . $this->seo_delim['forum'] . '[0-9]+)?$`i' : '`^' . $this->seo_static['forum'] . '[0-9]+$`i',
					'match' => '`^((' . $this->seo_static['forum'] . '|.+)(' . $this->seo_delim['forum'] . '([0-9]+))?)$`i',
					'title' => '\2',
					'id' => '\4',
				),
			),
			$this->RegEx
		);
		// preg_replace() patterns for format_url()
		// One could want to add |th|horn after |slash, but I'm not sure that Þ should be replaced with t and Ð with e
		$this->RegEx['url_find'] = array('`&([a-z]+)(acute|grave|circ|cedil|tilde|uml|lig|ring|caron|slash);`i', '`&(amp;)?[^;]+;`i', '`[^a-z0-9]`i'); // Do not remove : deaccentuation, html/xml entities & non a-z chars
		$this->RegEx['url_replace'] = array('\1', '-', '-');
		if ($this->seo_opt['rem_small_words']) {
			$this->RegEx['url_find'][] = '`(^|-)[a-z0-9]{1,2}(?=-|$)`i';
			$this->RegEx['url_replace'][] = '-';
		}
		$this->RegEx['url_find'][] ='`[-]+`'; // Do not remove : multi hyphen reduction
		$this->RegEx['url_replace'][] = '-';
		// $1 parent : string/
		// $2 title / url : topic-title / forum-url-fxx
		// $3 id
		$this->sftpl = array_merge(
			array(
				'topic' => ($this->seo_opt['virtual_folder'] ? '%1$s/' : '') . '%2$s' . $this->seo_delim['topic'] . '%3$s',
				'topic_smpl' => ($this->seo_opt['virtual_folder'] ? '%1$s/' : '') . $this->seo_static['topic'] . '%3$s',
				'forum' => $this->modrtype >= 2 ? '%2$s' : $this->seo_static['forum'] . '%3$s',
				'group' => $this->seo_opt['profile_inj'] ? '%2$s' . $this->seo_delim['group'] . '%3$s' : $this->seo_static['group'] . '%3$s',
			),
			$this->sftpl
		);
		if ( $this->seo_opt['url_rewrite'] && !defined('ADMIN_START') && isset($this->file_hbase[$this->seo_opt['req_file']])) {
			$this->seo_opt['seo_base_href'] = '<base href="' . $this->file_hbase[$this->seo_opt['req_file']] . '"/>';
		}
		return;
	}
	/**
	* will make sure that configured options are consistent
	* @access private
	*/
	function check_config() {
		$this->modrtype = max(0, (int) $this->modrtype);
		// For profiles and user messages pages, if we do not inject, we do not get rid of ids
		$this->seo_opt['profile_noids'] = $this->seo_opt['profile_inj'] ? $this->seo_opt['profile_noids'] : false;
		// If profile noids ...
		if ($this->seo_opt['profile_noids']) {
			$this->seo_ext['user'] = '/';
		}
		// Profile ans user messages virtual folder
		if ($this->seo_opt['profile_vfolder']) {
			$this->seo_ext['user'] = '/';
		}
		$this->seo_delim['sr'] = $this->seo_ext['user'] == '/' ? '/' : $this->seo_delim['sr'];
		// If we use virtual folder, we need '/' at the end of the forum URLs
		if ($this->seo_opt['virtual_folder']) {
			$this->seo_ext['forum'] = $this->seo_ext['global_announce'] = '/';
		}
		// If the forum cache is not activated
		if (!$this->seo_opt['cache_layer']) {
			$this->seo_opt['rem_ids'] = false;
		}
		// virtual root option
		if ($this->seo_opt['virtual_root'] && $this->seo_path['phpbb_script']) {
			// virtual root is available and activated
			$this->seo_path['phpbb_urlR'] = $this->seo_path['root_url'];
			$this->file_hbase['index'] = $this->seo_path['phpbb_url'];
			$this->seo_static['index'] = empty($this->seo_static['index']) ? 'forum' : $this->seo_static['index'];
		} else {
			// virtual root is not used or usable
			$this->seo_opt['virtual_root'] = false;
		}
		$this->seo_ext['index'] = empty($this->seo_static['index']) ? '' : ( empty($this->seo_ext['index']) ? '.html' : $this->seo_ext['index']);
		// In case url rewriting is deactivated
		if (!$this->seo_opt['url_rewrite'] || $this->modrtype == 0) {
			$this->seo_opt['sql_rewrite'] = false;
			$this->seo_opt['zero_dupe']['on'] = false;
		}
	}
	// --> URL rewriting functions <--
	/**
	* format_url( $url, $type = 'topic' )
	* Prepare Titles for URL injection
	*/
	function format_url( $url, $type = 'topic' ) {
		$url = preg_replace('`\[.*\]`U','',$url);
		if (isset($this->url_replace['find'])) {
			$url = str_replace($this->url_replace['find'], $this->url_replace['replace'], $url);
		}
		$url = htmlentities($url, ENT_COMPAT, 'UTF-8');
		$url = preg_replace($this->RegEx['url_find'] , $this->RegEx['url_replace'], $url);
		$url = strtolower(trim($url, '-'));
		return empty($url) ? $type : $url;
	}
	/**
	* set_url( $url, $id = 0, $type = 'forum', $parent = '' )
	* Prepare url first part and checks cache
	*/
	function set_url( $url, $id = 0, $type = 'forum',  $parent = '') {
		if ( empty($this->seo_url[$type][$id]) ) {
			return ( $this->seo_url[$type][$id] = !empty($this->cache_config[$type][$id]) ? $this->cache_config[$type][$id] : sprintf($this->sftpl[$type], $parent, $this->format_url($url, $this->seo_static[$type]) . $this->seo_delim[$type] . $id, $id) );
		}
		return $this->seo_url[$type][$id];
	}
	/**
	* prepare_url( $type, $title, $id, $parent = '', $smpl = false )
	* Prepare url first part
	*/
	function prepare_url( $type, $title, $id, $parent = '', $smpl = false ) {
		return empty($this->seo_url[$type][$id]) ? ($this->seo_url[$type][$id] = sprintf($this->sftpl[$type . ($smpl ? '_smpl' : '')], $parent, !$smpl ? $this->format_url($title, $this->seo_static[$type]) : '', $id)) : $this->seo_url[$type][$id];
	}
	/**
	* set_title( $type, $title, $id, $parent = '' )
	* Set title for url injection
	*/
	function set_title( $type, $title, $id, $parent = '' ) {
		return empty($this->seo_url[$type][$id]) ? ($this->seo_url[$type][$id] = ($parent ? $parent . '/' : '') . $this->format_url($title, $this->seo_static[$type])) : $this->seo_url[$type][$id];
	}
	/**
	* get_url_info($type, $url, $info = 'title')
	* Get info from url (title, id, parent etc ...)
	*/
	function get_url_info($type, $url, $info = 'title') {
		$url = trim($url, '/ ');
		if (preg_match($this->RegEx[$type]['match'], $url, $matches)) {
			return !empty($matches[$this->RegEx[$type][$info]]) ? $matches[$this->RegEx[$type][$info]] : '';
		}
		return '';
	}
	/**
	* check_url( $type, $url, $parent = '')
	* Validate a prepared url
	*/
	function check_url( $type, $url, $parent = '') {
		if (empty($url)) {
			return false;
		}
		$parent = !empty($parent) ? (string) $parent : '[a-z0-9/_-]+';
		return !empty($this->RegEx[$type]['check']) ? preg_match(sprintf($this->RegEx[$type]['check'], $parent), $url) : false;
	}
	/**
	* prepare_iurl( $data, $type, $parent = '' )
	* Prepare url first part (not for forums) with SQL based URL rewriting
	*/
	function prepare_iurl( $data, $type, $parent = '' ) {
		$id = max(0, (int) $data[$type . '_id']);
		if ( empty($this->seo_url[$type][$id]) ) {
			if (!empty($data[$type . '_url'])) {
				return ($this->seo_url[$type][$id] = $data[$type . '_url'] . $id);
			} else {
				return ($this->seo_url[$type][$id] = sprintf($this->sftpl[$type . ($this->modrtype > 2 ? '' : '_smpl')], $parent, $this->modrtype > 2 ? $this->format_url($data[$type . '_title'], $this->seo_static[$type]) : '', $id));
			}
		}
		return $this->seo_url[$type][$id];
	}
	/**
	* drop_sid( $url )
	* drop the sid's in url
	*/
	function drop_sid( $url ) {
		return (strpos($url, 'sid=') !== false) ? trim(preg_replace(array('`&(amp;)?sid=[a-z0-9]*(&amp;|&)?`', '`(\?)sid=[a-z0-9]*`'), array('\2', '\1'), $url), '?') : $url;
	}
	/**
	* set_user_url( $username, $user_id = 0 )
	* Prepare profile url
	*/
	function set_user_url( $username, $user_id = 0 ) {
		if (empty($this->seo_url['user'][$user_id])) {
			$username = strip_tags($username);
			$this->seo_url['username'][$username] = $user_id;
			if ( $this->seo_opt['profile_inj'] ) {
				if ( $this->seo_opt['profile_noids'] ) {
					$this->seo_url['user'][$user_id] = $this->seo_static['user'] . '/' . $this->seo_url_encode($username);
				} else {
					$this->seo_url['user'][$user_id] = $this->format_url($username,  $this->seo_delim['user']) . $this->seo_delim['user'] . $user_id;
				}
			} else {
				$this->seo_url['user'][$user_id] = $this->seo_static['user'] . $user_id;
			}
		}
	}
	/**
	* seo_url_encode( $url )
	* custom urlencoding
	*/
	function seo_url_encode( $url ) {
		// can be faster to return $url directly if you do not allow more chars than
		// [a-zA-Z0-9_\.-] in your usernames
		// return $url;
		// Here we hanlde the "&", "/", "+" and "#" case proper ( http://www.php.net/urlencode => http://issues.apache.org/bugzilla/show_bug.cgi?id=34602 )
		static $find = array('&', '/', '#', '+');
		static $replace = array('%26', '%2F', '%23', '%2b');
		return rawurlencode(str_replace( $find, $replace, utf8_normalize_nfc(htmlspecialchars_decode(str_replace('&amp;amp;', '%26', rawurldecode($url))))));
	}
	/**
	* url_rewrite($url, $params = false, $is_amp = true, $session_id = false)
	* builds and Rewrite URLs.
	* Allow adding of many more cases than just the
	* regular phpBB URL rewritting without slowing down the process.
	* Mimics append_sid with some shortcuts related to how url are rewritten
	*/
	function url_rewrite($url, $params = false, $is_amp = true, $session_id = false) {
		global $phpEx, $user, $_SID, $_EXTRA_URL, $phpbb_root_path;
		$qs = $anchor = '';
		$this->get_vars = array();
		$amp_delim = ($is_amp) ? '&amp;' : '&';
		if (strpos($url, '#') !== false) {
			list($url, $anchor) = explode('#', $url, 2);
			$anchor = '#' . $anchor;
		}
		@list($this->path, $qs) = explode('?', $url, 2);
		if (is_array($params)) {
			if (!empty($params['#'])) {
				$anchor = '#' . $params['#'];
				unset($params['#']);
			}
			$qs .= ($qs ? $amp_delim : '') . $this->query_string($params, $amp_delim, '');
		} elseif ($params) {
			if (strpos($params, '#') !== false) {
				list($params, $anchor) = explode('#', $params, 2);
				$anchor = '#' . $anchor;
			}
			$qs .= ($qs ? $amp_delim : '') . $params;
		}
		// Appending custom url parameter?
		if (!empty($_EXTRA_URL)) {
			$qs .= ($qs ? $amp_delim : '') . implode($amp_delim, $_EXTRA_URL);
		}
		// Sid ?
		if ($session_id === false && !empty($_SID)) {
			$qs .= ($qs ? $amp_delim : '') . "sid=$_SID";
		} else if ($session_id) {
			$qs .= ($qs ? $amp_delim : '') . "sid=$session_id";
		}
		// Build vanilla URL
		if (preg_match("`\.[a-z0-9]+$`i", $this->path) ) {
			$this->file = basename($this->path);
			$this->path = ltrim(str_replace($this->file, '', $this->path), '/');
		} else {
			$this->file = '';
			$this->path = ltrim($this->path, '/');
		}
		$this->url_in = $this->file . ($qs ? '?' . $qs : '');
		$url = $this->path . $this->url_in . $anchor;
		if (isset($this->seo_cache[$url])) {
			return $this->seo_cache[$url];
		}
		if ( !$this->seo_opt['url_rewrite'] || defined('ADMIN_START') || isset($this->seo_stop_dirs[$this->path]) ) {
			return ($this->seo_cache[$url] = $url);
		}
		$this->filename = trim(str_replace(".$phpEx", '', $this->file));
		if ( isset($this->seo_stop_files[$this->filename]) ) {
			// add full url
			$url = $this->path == $phpbb_root_path ? $this->seo_path['phpbb_url'] . preg_replace('`^' . $phpbb_root_path . '`', '', $url) : $url;
			return ($this->seo_cache[$url] = $url);
		}
		parse_str(str_replace('&amp;', '&', $qs), $this->get_vars);
		// strp slashes if necessary
		if (defined('SEO_STRIP')) {
			$this->get_vars = array_map(array(&$this, 'stripslashes'), $this->get_vars);
		}
		if (empty($user->data['is_registered'])) {
			if ( $this->seo_opt['rem_sid'] ) {
				unset($this->get_vars['sid']);
			}
			if ( $this->seo_opt['rem_hilit'] ) {
				unset($this->get_vars['hilit']);
			}
		}
		$this->url = $this->file;

		if ( !empty($this->rewrite_method[$this->path][$this->filename]) ) {
			$this->{$this->rewrite_method[$this->path][$this->filename]}();
			return ($this->seo_cache[$url] = $this->path . $this->url . $this->query_string($this->get_vars, $amp_delim, '?') . $anchor);
		} else {
			return ($this->seo_cache[$url] = $url);
		}
	}
	/**
	* URL rewritting for viewtopic.php
	* With Virtual Folder Injection
	* @access private
	*/
	function viewtopic() {
		global $phpbb_root_path;
		global $user;
		$this->filter_url($this->seo_stop_vars);
		$this->path = $this->seo_path['phpbb_urlR'];
		if ( !empty($this->get_vars['p']) ) {
			$this->url = $this->seo_static['post'] . $this->get_vars['p'] . $this->seo_ext['post'];
			unset($this->get_vars['p'], $this->get_vars['f'], $this->get_vars['t'], $this->get_vars['start']);
			return;
		}

		if ( isset($this->get_vars['t']) && !empty($this->seo_url['topic'][$this->get_vars['t']]) ) {
			// Filter default params
			$this->filter_get_var($this->phpbb_filter['topic']);
			$this->{$this->paginate_method['topic']}($this->seo_ext['topic']);
			$this->url = $this->seo_url['topic'][$this->get_vars['t']] . $this->start;
			unset($this->get_vars['t'], $this->get_vars['f'], $this->get_vars['p']);
			return;
		} else if (!empty($this->get_vars['t'])) {
			// Filter default params
			$this->filter_get_var($this->phpbb_filter['topic']);
			$this->{$this->paginate_method['topic']}($this->seo_ext['topic']);
			$this->url = $this->seo_static['topic'] . '-t'. $this->get_vars['t'] . $this->start;
			unset($this->get_vars['t'], $this->get_vars['f'], $this->get_vars['p']);
			return;
		}

		$this->path = $this->seo_path['phpbb_url'];
		return;
	}
	/**
	* URL rewritting for viewforum.php
	* @access private
	*/
	function viewforum() {
		global $phpbb_root_path;
		$this->path = $this->seo_path['phpbb_urlR'];
		$this->filter_url($this->seo_stop_vars);
		if ( isset($this->get_vars['f']) && !empty($this->seo_url['forum'][$this->get_vars['f']]) ) {
			// Filter default params
			$this->filter_get_var($this->phpbb_filter['forum']);
			$this->{$this->paginate_method['forum']}($this->seo_ext['forum']);
			$this->url = $this->seo_url['forum'][$this->get_vars['f']] . $this->start;
			unset($this->get_vars['f']);
			return;
		} else if (!empty($this->get_vars['f'])) {
			// Filter default params
			$this->filter_get_var($this->phpbb_filter['forum']);
			$this->{$this->paginate_method['forum']}($this->seo_ext['forum']);
			$this->url = $this->seo_static['forum'] . $this->get_vars['f'] . $this->start;
			unset($this->get_vars['f']);
			return;
		}
		$this->path = $this->seo_path['phpbb_url'];
		return;
	}
	/**
	* URL rewritting for memberlist.php
	* with nicknames and group name injection
	* @access private
	*/
	function memberlist() {
		global $phpbb_root_path;
		$this->path = $this->seo_path['phpbb_urlR'];
		if ( @$this->get_vars['mode'] === 'viewprofile' && !@empty($this->seo_url['user'][$this->get_vars['u']]) ) {
			$this->url = $this->seo_url['user'][$this->get_vars['u']] . $this->seo_ext['user'];
			unset($this->get_vars['mode'], $this->get_vars['u']);
			return;
		} elseif ( @$this->get_vars['mode'] === 'group' && !@empty($this->seo_url['group'][$this->get_vars['g']]) ) {
			$this->{$this->paginate_method['group']}($this->seo_ext['group']);
			$this->url =  $this->seo_url['group'][$this->get_vars['g']] . $this->start;
			unset($this->get_vars['mode'], $this->get_vars['g']);
			return;
		} elseif (@$this->get_vars['mode'] === 'leaders') {
			$this->url =  $this->seo_static['leaders'] . $this->seo_ext['leaders'];
			unset($this->get_vars['mode']);
			return;
		}
		$this->path = $this->seo_path['phpbb_url'];
		return;
	}
	/**
	* URL rewritting for search.php
	* @access private
	*/
	function search() {
		global $phpbb_root_path;
		if (isset($this->get_vars['fid'])) {
			$this->get_vars = array();
			$this->url = $this->url_in;
			return;
		}
		$this->path = $this->seo_path['phpbb_urlR'];
		$user_id = !empty($this->get_vars['author_id']) ? $this->get_vars['author_id'] : ( isset($this->seo_url['username'][rawurldecode(@$this->get_vars['author'])]) ? $this->seo_url['username'][rawurldecode(@$this->get_vars['author'])] : 0);
		if ( $user_id && isset($this->seo_url['user'][$user_id]) ) {
			// Filter default params
			$this->filter_get_var($this->phpbb_filter['search']);
			$this->{$this->paginate_method['user']}($this->seo_ext['user']);
			$sr = (@$this->get_vars['sr'] == 'topics' ) ? 'topics' : 'posts';
			$this->url = $this->seo_url['user'][$user_id] . $this->seo_delim['sr'] . $sr . $this->start;
			unset($this->get_vars['author_id'], $this->get_vars['author'], $this->get_vars['sr']);
			return;
		} elseif ( $this->seo_opt['profile_noids'] && !empty($this->get_vars['author']) ) {
			// Filter default params
			$this->filter_get_var($this->phpbb_filter['search']);
			$this->rewrite_pagination_page();
			$sr = (@$this->get_vars['sr'] == 'topics' ) ? '/topics' : '/posts';
			$this->url = $this->seo_static['user'] . '/' . $this->seo_url_encode($this->get_vars['author']) . $sr . $this->start;
			unset($this->get_vars['author'], $this->get_vars['author_id'], $this->get_vars['sr']);
			return;
		} elseif (!empty($this->get_vars['search_id'])) {
			switch ($this->get_vars['search_id']) {
				case 'active_topics':
					$this->filter_get_var($this->phpbb_filter['search']);
					$this->{$this->paginate_method['atopic']}($this->seo_ext['atopic']);
					$this->url = $this->seo_static['atopic'] . $this->start;
					unset($this->get_vars['search_id'], $this->get_vars['sr']);
					if (@$this->get_vars['st'] == 7) {
						unset($this->get_vars['st']);
					}
					return;
				case 'unanswered':
					$this->filter_get_var($this->phpbb_filter['search']);
					$this->{$this->paginate_method['utopic']}($this->seo_ext['utopic']);
					$this->url = $this->seo_static['utopic'] . $this->start;
					unset($this->get_vars['search_id']);
					if (@$this->get_vars['sr'] == 'topics') {
						unset($this->get_vars['sr']);
					}
					return;
				case 'egosearch':
					global $user;
					$this->set_user_url($user->data['username'], $user->data['user_id']);
					$this->url = $this->seo_url['user'][$user->data['user_id']] . $this->seo_delim['sr'] . 'topics' . $this->seo_ext['user'];
					unset($this->get_vars['search_id']);
					return;
				case 'newposts':
					$this->filter_get_var($this->phpbb_filter['search']);
					$this->{$this->paginate_method['npost']}($this->seo_ext['npost']);
					$this->url = $this->seo_static['npost'] . $this->start;
					unset($this->get_vars['search_id']);
					if (@$this->get_vars['sr'] == 'topics') {
						unset($this->get_vars['sr']);
					}
					return;
				case 'unreadposts':
					$this->filter_get_var($this->phpbb_filter['search']);
					$this->{$this->paginate_method['urpost']}($this->seo_ext['urpost']);
					$this->url = $this->seo_static['urpost'] . $this->start;
					unset($this->get_vars['search_id']);
					if (@$this->get_vars['sr'] == 'topics') {
						unset($this->get_vars['sr']);
					}
					return;
			}
		}
		$this->path = $this->seo_path['phpbb_url'];
		return;
	}
	/**
	* URL rewritting for download/file.php
	* @access private
	*/
	function phpbb_files() {
		$this->filter_url($this->seo_stop_vars);
		$this->path = $this->seo_path['phpbb_filesR'];
		if (isset($this->get_vars['id']) && !empty($this->seo_url['file'][$this->get_vars['id']])) {
			$this->url = $this->seo_url['file'][$this->get_vars['id']];
			if (!empty($this->get_vars['t'])) {
				$this->url .= $this->seo_delim['file'] . $this->seo_static['thumb'];
			} /*else if (@$this->get_vars['mode'] == 'view') {
				$this->url .= $this->seo_delim['file'] . 'view';
			}*/
			$this->url .= $this->seo_delim['file'] . $this->get_vars['id'];
			unset($this->get_vars['id'], $this->get_vars['t'], $this->get_vars['mode']);
			return;
		}
		$this->path = $this->seo_path['phpbb_files'];
		return;
	}
	/**
	* URL rewritting for index.php
	* @access private
	*/
	function index() {
		$this->path = $this->seo_path['phpbb_urlR'];
		if ($this->filter_url($this->seo_stop_vars)) {
			$this->url = $this->seo_static['index'] . $this->seo_ext['index'];
			return;
		}
		$this->path = $this->seo_path['phpbb_url'];
		return;
	}
	/**
	* Returns true if the user can edit urls
	* @access public
	*/
	function url_can_edit($forum_id = 0) {
		global $user, $auth;
		if (empty($this->seo_opt['sql_rewrite']) || empty($user->data['is_registered'])) {
			return false;
		}
		if ($auth->acl_get('a_')) {
			return true;
		}
		// un comment to grant url edit perm to moderators in at least a forums
		/*if ($auth->acl_getf_global('m_')) {
			return true;
		}*/
		$forum_id = max(0, (int) $forum_id);
		if ($forum_id && $auth->acl_get('m_', $forum_id)) {
			return true;
		}
		return false;
	}
	/**
	* Will break if a $filter pattern is foundin $url.
	* Example $filter = array("view=", "mark=");
	* @access private
	*/
	function filter_url($filter = array()) {
		foreach ($filter as $patern ) {
			if ( strpos($this->url_in, $patern) !== false ) {
				$this->get_vars = array();
				$this->url = $this->url_in;
				return false;
			}
		}
		return true;
	}
	/**
	* Will unset all default var stored in $filter array.
	* Example $filter = array('st' => 0, 'sk' => 't', 'sd' => 'a', 'hilit' => '');
	* @access private
	*/
	function filter_get_var($filter = array()) {
		if ( !empty($this->get_vars) ) {
			foreach ($this->get_vars as $paramkey => $paramval) {
				if ( isset($filter[$paramkey]) ) {
					if ( $filter[$paramkey] ==  $this->get_vars[$paramkey] || !isset($this->get_vars[$paramkey])) {
						unset($this->get_vars[$paramkey]);
					}
				}
			}
		}
		return;
	}
	/**
	* Appends the GET vars in the query string
	* @access public
	*/
	function query_string($get_vars = array(), $amp_delim = '&amp;', $url_delim = '?') {
		if(empty($get_vars)) {
			return '';
		}
		$params = array();
		foreach($get_vars as $key => $value) {
			if (is_array($value)) {
				foreach($value as $k => $v) {
					$params[] = $key . '[' . $k . ']=' . $v;
				}
			} else {
				$params[] = $key . (!trim($value) ? '' : '=' . $value);
			}
		}
		return $url_delim . implode($amp_delim , $params);
	}
	/**
	* rewrite pagination, simple
	* -xx.html
	*/
	function rewrite_pagination($suffix) {
		$this->start = $this->seo_start( @$this->get_vars['start'] ) . $suffix;
		unset($this->get_vars['start']);
	}
	/**
	* rewrite pagination, virtual folder
	* /pagexx.html
	*/
	function rewrite_pagination_page() {
		$this->start = '/' . $this->seo_start_page( @$this->get_vars['start'] );
		unset($this->get_vars['start']);
	}
	/**
	* Returns usable start param
	* -xx
	*/
	function seo_start($start) {
		return ($start >= 1 ) ? $this->seo_delim['start'] . (int) $start : '';
	}
	/**
	* Returns usable start param
	* pagexx.html
	* Only used in virtual folder mode
	*/
	function seo_start_page($start) {
		return ($start >=1 ) ? $this->seo_static['pagination'] . (int) $start . $this->seo_ext['pagination'] : '';
	}
	/**
	* Returns the full REQUEST_URI
	*/
	function seo_req_uri() {
		if ( !empty($_SERVER['HTTP_X_REWRITE_URL']) ) { // IIS  isapi_rewrite
			$this->seo_path['uri'] = ltrim($_SERVER['HTTP_X_REWRITE_URL'], '/');
		} elseif ( !empty($_SERVER['REQUEST_URI']) ) { // Apache mod_rewrite
			$this->seo_path['uri'] = ltrim($_SERVER['REQUEST_URI'], '/');
		} else { // no mod rewrite
			$this->seo_path['uri'] =  ltrim($_SERVER['SCRIPT_NAME'], '/') . ( ( !empty($_SERVER['QUERY_STRING']) ) ? '?'.$_SERVER['QUERY_STRING'] : '' );
		}
		$this->seo_path['uri'] = str_replace( '%26', '&', rawurldecode($this->seo_path['uri']));
		// workaround for FF default iso encoding
		if (!$this->is_utf8($this->seo_path['uri'])) {
			$this->seo_path['uri'] = utf8_normalize_nfc(utf8_recode($this->seo_path['uri'], 'iso-8859-1'));
		}
		$this->seo_path['uri'] = $this->seo_path['root_url'] . $this->seo_path['uri'];
		return $this->seo_path['uri'];
	}
	/**
	* seo_end() : The last touch function
	* Note : This mod is going to help your site a lot in Search Engines
	* We request that you keep this copyright notice as specified in the licence.
	* If You really cannot put this link, you should at least provide us with one visible
	* (can be small but visible) link on your home page or your forum Index using this code for example :
	* <a href="http://www.phpbb-seo.com/" title="Search Engine Optimization">phpBB SEO</a>
	*/
	function seo_end($return = false) {
		return;

		global $user, $config;
		if (empty($this->seo_opt['copyrights']['title'])) {
			$this->seo_opt['copyrights']['title'] = strpos($config['default_lang'], 'fr') !== false  ?  'Optimisation du R&eacute;f&eacute;rencement' : 'Search Engine Optimization';
		}
		if (empty($this->seo_opt['copyrights']['txt'])) {
			$this->seo_opt['copyrights']['txt'] = 'phpBB SEO';
		}
		if ($this->seo_opt['copyrights']['img']) {
			$output = '<br /><a href="http://www.phpbb-seo.com/" title="' . $this->seo_opt['copyrights']['title'] . '"><img src="' . $this->seo_path['phpbb_url'] . 'images/phpbb-seo.png" alt="' . $this->seo_opt['copyrights']['txt'] . '"/></a>';
		} else {
			$output = '<br /><a href="http://www.phpbb-seo.com/" title="' . $this->seo_opt['copyrights']['title'] . '">' . $this->seo_opt['copyrights']['txt'] . '</a>';
		}
		if ($return) {
			return $output;
		} else {
			$user->lang['TRANSLATION_INFO'] .= $output;
		}
		return;
	}
	// -> Cache functions
	/**
	* forum_id(&$forum_id, $forum_uri = '')
	* will tell the forum id from the uri or the forum_uri GET var by checking the cache.
	*/
	function get_forum_id(&$forum_id, $forum_uri = '') {
		if (empty($forum_uri)) {
			$forum_uri = request_var('forum_uri', '');
			unset($_GET['forum_uri'], $_REQUEST['forum_uri']);
		}
		if (empty($forum_uri)) {
			return 0;
		}
		if ($id = @array_search($forum_uri, $this->cache_config['forum']) ) {
			$forum_id = max(0, (int) $id);
		} elseif ( $id = $this->get_url_info('forum', $forum_uri, 'id')) {
			$forum_id = max(0, (int) $id);
		}
		return $forum_id;
	}
	/**
	* check_cache() will tell if the required file exists.
	* @access private
	*/
	function check_cache( $type = 'forum', $from_bkp = false ) {
		$file = SEO_CACHE_PATH . @$this->cache_config['files'][$type];
		if( !$this->cache_config['cache_enable'] || !isset($this->cache_config['files'][$type]) || !file_exists($file) ) {
			$this->cache_config['cached'] = false;
			return false;
		}
		include($file);
		if (is_array($this->cache_config[$type]) ) {
			$this->cache_config['cached'] = true;
			return true;
		} else {
			if ( !$from_bkp ) {
				// Try the current backup
				@copy($file . '.current', $file);
				$this->check_cache( $type, true );
			}
			$this->cache_config['cached'] = false;
			return false;
		}
	}
	/**
	* is_utf8($string)
	* Borrowed from php.net : http://www.php.net/mb_detect_encoding (detectUTF8)
	*/
	function is_utf8($string) {
		// non-overlong 2-byte|excluding overlongs|straight 3-byte|excluding surrogates|planes 1-3|planes 4-15|plane 16
		return preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF] |\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $string);
	}
	/**
	* stripslashes($value)
	* Borrowed from php.net : http://www.php.net/stripslashes
	*/
	function stripslashes($value) {
		return is_array($value) ? array_map(array(&$this, 'stripslashes'), $value) : stripslashes($value);
	}
	// --> Add on Functions <--
	// --> Gen stats
	/**
	* Returns usable microtime
	* Borrowed from php.net
	*/
	function microtime_float() {
		return array_sum(explode(' ',microtime()));
	}
} // End of the phpbb_seo class
?>
