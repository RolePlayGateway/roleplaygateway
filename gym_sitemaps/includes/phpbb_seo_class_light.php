<?php
/**
*
* @package Ultimate SEO URL phpBB SEO
* @version $Id: phpbb_seo_class_light.php 172 2009-11-20 10:13:45Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl1.5.txt Reciprocal Public License 1.5
*
*/
/**
* @ignore
*/
if (!defined('IN_PHPBB')) {
	exit;
}
/**
* phpBB_SEO Class lite
* For Compatibility with the phpBB SEO mod rewrites
* www.phpBB-SEO.com
* @package phpBB SEO
*/
//

class phpbb_seo {
	var	$modrtype = -1;
	var	$seo_path = array();
	var	$seo_url = array( 'forum' =>  array(), 'topic' =>  array(), 'user' => array(), 'username' => array(), 'group' => array(), 'file' => array() );
	var	$seo_delim = array( 'forum' => '-f', 'topic' => '-t', 'user' => '-u', 'group' => '-g', 'start' => '-', 'sr' => '-', 'file' => '/');
	var	$seo_ext = array( 'forum' => '.html', 'topic' => '.html', 'post' => '.html', 'user' => '.html', 'group' => '.html',  'index' => '', 'global_announce' => '/', 'leaders' => '.html', 'atopic' => '.html', 'utopic' => '.html', 'npost' => '.html', 'pagination' => '.html', 'gz_ext' => '');
	var	$seo_static = array( 'forum' => 'forum', 'topic' => 'topic', 'post' => 'post', 'user' => 'member', 'group' => 'group', 'index' => '', 'global_announce' => 'announces', 'leaders' => 'the-team', 'atopic' => 'active-topics', 'utopic' => 'unanswered', 'npost' => 'newposts', 'pagination' => 'page', 'gz_ext' => '.gz' );
	var	$seo_opt = array();
	var	$seo_cache = array();
	var	$RegEx = array();
	var	$sftpl = array();
	var	$url_replace = array();
	var	$light = true;
	/**
	* constuctor
	*/
	function phpbb_seo() {
		global $config, $phpEx;
		// fix for an interesting bug with parse_str http://bugs.php.net/bug.php?id=48697
		// and apparently, the bug is still here in php5.3
		@ini_set("mbstring.internal_encoding", 'UTF-8');
		// URL Settings
		$this->seo_opt = array(
			'profile_inj' => false,
			'rem_small_words' => false,
			'virtual_folder' => false,
			'virtual_root' => false,
		);
		// --> DOMAIN SETTING <-- //
		// Path Settings, only rely on DB
		$server_protocol = ($config['server_protocol']) ? $config['server_protocol'] : (($config['cookie_secure']) ? 'https://' : 'http://');
		$server_name = trim($config['server_name'], '/ ');
		$server_port = max(0, (int) $config['server_port']);
		$server_port = ($server_port && $server_port <> 80) ? ':' . $server_port : '';
		$script_path = trim($config['script_path'], '/ ');
		$script_path = (empty($script_path) ) ? '' : $script_path . '/';
		$this->seo_path['root_url'] = strtolower($server_protocol . $server_name . $server_port . '/');
		$this->seo_path['phpbb_urlR'] = $this->seo_path['phpbb_url'] =  $this->seo_path['root_url'] . $script_path;
		$this->seo_path['phpbb_script'] = $script_path;
		$this->seo_path['canonical'] = '';
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
		// see if we have some custom replacement
		if (!empty($this->url_replace)) {
			$this->url_replace = array(
				'find' => array_keys($this->url_replace),
				'replace' => array_values($this->url_replace)
			);
		}
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
		$this->sftpl = array(
			'topic' => ($this->seo_opt['virtual_folder'] ? '%1$s/' : '') . '%2$s' . $this->seo_delim['topic'] . '%3$s',
			'topic_smpl' => ($this->seo_opt['virtual_folder'] ? '%1$s/' : '') . $this->seo_static['topic'] . '%3$s',
			'forum' => $this->modrtype >= 2 ? '%2$s' : $this->seo_static['forum'] . '%3$s',
			'group' => $this->seo_opt['profile_inj'] ? '%2$s' . $this->seo_delim['group'] . '%3$s' : $this->seo_static['group'] . '%3$s',
		);
		return;
	}
	// --> Gen stats
	/**
	* Returns microtime
	* Borrowed from php.net
	*/
	function microtime_float() {
		return array_sum(explode(' ', microtime()));
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
	* is_utf8($string)
	* Borrowed from php.net : http://www.php.net/mb_detect_encoding (detectUTF8)
	*/
	function is_utf8($string) {
		// non-overlong 2-byte|excluding overlongs|straight 3-byte|excluding surrogates|planes 1-3|planes 4-15|plane 16
		return preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF] |\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $string);
	}
}
?>