<?php
/**
*
* @package Ultimate SEO URL phpBB SEO
* @version $Id: setup_phpbb_seo.php 222 2010-02-27 13:08:48Z dcz $
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
/**
* setup_phpbb_seo Class
* www.phpBB-SEO.com
* @package Ultimate SEO URL phpBB SEO
*/
class setup_phpbb_seo {
	/**
	* Do the init
	*/
	function init_phpbb_seo() {
		global $phpEx, $config, $phpbb_root_path;
		// --> No Dupe
		$this->seo_opt['no_dupe']['on'] = $this->cache_config['dynamic_options']['no_dupe']['on'] = false;
		// <-- No Dupe
		// --> Zero Dupe
		$this->seo_opt['zero_dupe'] = array( 'on' => false, // Activate or not the redirections : true / false
			'strict' => false, // strict compare, == VS strpos() : true / false
			'post_redir' => 'guest', // Redirect post urls if not valid ? : guest / all / post / off
		);
		$this->cache_config['dynamic_options']['zero_dupe'] = $this->seo_opt['zero_dupe']; // Do not change
		$this->seo_opt['zero_dupe']['do_redir'] = false; // do not change
		$this->seo_opt['zero_dupe']['go_redir'] = true; // do not change
		$this->seo_opt['zero_dupe']['do_redir_post'] = false; // do not change
		$this->seo_opt['zero_dupe']['start'] = 0; // do not change
		$this->seo_opt['zero_dupe']['redir_def'] = array(); // do not change
		// <-- Zero Dupe
		// Let's load config and forum urls, mods adding options in the cache file must do it before
		if ($this->check_cache()) {
			foreach($this->cache_config['dynamic_options'] as $optionname => $optionvalue ) {
				if (@is_array($this->cache_config['settings'][$optionname])) {
					$this->seo_opt[$optionname] = array_merge($this->seo_opt[$optionname], $this->cache_config['settings'][$optionname]);
				} elseif ( @isset($this->cache_config['settings'][$optionvalue]) ) {
					$this->seo_opt[$optionvalue] = $this->cache_config['settings'][$optionvalue];
				}
			}
			$this->modrtype = @isset($this->seo_opt['modrtype']) ? $this->seo_opt['modrtype'] : $this->modrtype;
			if ( $this->modrtype > 1 ) { // Load cached URLs
				$this->seo_url['forum'] =& $this->cache_config['forum'];
			}
		}
		// ====> here starts the add-on and custom set up <====

		// ===> Custom url replacements <===
		// Here you can set up custom replacements to be used in title injection.
		// Example : array( 'find' => 'replace')
		//	$this->url_replace = array(
		//		// Purely cosmetic replace
		//		'$' => 'dollar', '€' => 'euro',
		//		'\'s' => 's', // it's => its / mary's => marys ...
		//		// Language specific replace (German example)
		//		'ß' => 'ss',
		//		'Ä' => 'Ae', 'ä' => 'ae',
		//		'Ö' => 'Oe', 'ö' => 'oe',
		//		'Ü' => 'Ue', 'ü' => 'ue',
		//	);

		// ===> Custom values Delimiters, Static parts and Suffixes <===
		// ==> Delimiters <==
		// Can be overridden, requires .htaccess update <=
		// Example :
		//	$this->seo_delim['forum'] = '-mydelim'; // instead of the default "-f"

		// ==> Static parts <==
		// Can be overridden, requires .htaccess update.
		// Example :
		//	$this->seo_static['post'] = 'message'; // instead of the default "post"
		// !! phpBB files must be treated a bit differently !!
		// Example :
		//	$this->seo_static['file'][ATTACHMENT_CATEGORY_QUICKTIME] = 'quicktime'; // instead of the default "qt"
		//	$this->seo_static['file_index'] = 'my_files_virtual_dir'; // instead of the default "resources"

		// ==> Suffixes <==
		// Can be overridden, requires .htaccess update <=
		// Example :
		// 	$this->seo_ext['topic'] = '/'; // instead of the default ".html"

		// ==> Special for lazy French, others may delete this part
		if ( strpos($config['default_lang'], 'fr') !== false ) {
			$this->seo_static['user'] = 'membre';
			$this->seo_static['group'] = 'groupe';
			$this->seo_static['global_announce'] = 'annonces';
			$this->seo_static['leaders'] = 'equipe';
			$this->seo_static['atopic'] = 'sujets-actifs';
			$this->seo_static['utopic'] = 'sans-reponses';
			$this->seo_static['npost'] = 'nouveaux-messages';
			$this->seo_static['urpost'] = 'non-lu';
			$this->seo_static['file_index'] = 'ressources';
		}
		// <== Special for lazy French, others may delete this part

		// Let's make sure that settings are consistent
		$this->check_config();
	}
	// Here start the add-on methods
	// --> Zero Duplicate
	/**
	* Custom HTTP 301 redirections.
	* To kill duplicates
	*/
	function seo_redirect($url, $header = '301 Moved Permanently', $code = 301, $replace = true) {
		global $db;
		if (!$this->seo_opt['zero_dupe']['on'] || @headers_sent()) {
			return false;
		}
		garbage_collection();
		$url = str_replace('&amp;', '&', $url);
		// Behave as redirect() for checks to provide with the same level of protection
		// Make sure no linebreaks are there... to prevent http response splitting for PHP < 4.4.2
		if (strpos(urldecode($url), "\n") !== false || strpos(urldecode($url), "\r") !== false || strpos($url, ';') !== false) {
			trigger_error('Tried to redirect to potentially insecure url.', E_USER_ERROR);
		}
		// Now, also check the protocol and for a valid url the last time...
		$allowed_protocols = array('http', 'https'/*, 'ftp', 'ftps'*/);
		$url_parts = parse_url($url);
		if ($url_parts === false || empty($url_parts['scheme']) || !in_array($url_parts['scheme'], $allowed_protocols)) {
			trigger_error('Tried to redirect to potentially insecure url.', E_USER_ERROR);
		}
		$http = 'HTTP/1.1 ';
		header($http . $header, $replace, $code);
		header('Location: ' . $url);
		exit_handler();
	}
	/**
	* Set the do_redir_post option right
	*/
	function set_do_redir_post() {
		global $user;
		switch ($this->seo_opt['zero_dupe']['post_redir']) {
			case 'guest':
				if ( empty($user->data['is_registered']) ) {
					$this->seo_opt['zero_dupe']['do_redir_post'] = true;
				}
				break;
			case 'all':
				$this->seo_opt['zero_dupe']['do_redir_post'] = true;
				break;
			case 'off': // Do not redirect
				$this->seo_opt['zero_dupe']['do_redir'] = false;
				$this->seo_opt['zero_dupe']['go_redir'] = false;
				$this->seo_opt['zero_dupe']['do_redir_post'] = false;
				break;
			default:
				$this->seo_opt['zero_dupe']['do_redir_post'] = false;
				break;
		}
		return $this->seo_opt['zero_dupe']['do_redir_post'];
	}
	/**
	* Redirects if the uri sent does not match (fully) the
	* attended url
	*/
	function seo_chk_dupe($url = '', $uri = '', $path = '') {
		global $auth, $user, $_SID, $phpbb_root_path, $config;
		if (empty($this->seo_opt['req_file']) || (!$this->seo_opt['rewrite_usermsg'] && $this->seo_opt['req_file'] == 'search') ) {
			return false;
		}
		if (!empty($_REQUEST['explain']) && (boolean) ($auth->acl_get('a_') && defined('DEBUG_EXTRA'))) {
			if ($_REQUEST['explain'] == 1) {
				return true;
			}
		}
		$path = empty($path) ? $phpbb_root_path : $path;
		$uri = !empty($uri) ? $uri : $this->seo_path['uri'];
		$reg = !empty($user->data['is_registered']) ? true : false;
		$url = empty($url) ? $this->expected_url($path) : str_replace('&amp;', '&', append_sid($url, false, true, 0));
		$url = $this->drop_sid($url);
		// Only add sid if user is registered and needs it to keep session
		if (!empty($_GET['sid']) && !empty($_SID) && ($reg || !$this->seo_opt['rem_sid']) ) {
			if ($_GET['sid'] == $user->session_id) {
				$url .=  (utf8_strpos( $url, '?' ) !== false ? '&' : '?') . 'sid=' . $user->session_id;
			}
		}
		$url = str_replace( '%26', '&', urldecode($url));
		if ($this->seo_opt['zero_dupe']['do_redir']) {
			$this->seo_redirect($url);
		} else {
			$url_check = $url;
			// we remove url hash for comparison, but keep it for redirect
			if (strpos($url, '#') !== false) {
				list($url_check, $hash) = explode('#', $url, 2);
			}
			if ($this->seo_opt['zero_dupe']['strict']) {
				return $this->seo_opt['zero_dupe']['go_redir'] && ( ($uri != $url_check) ? $this->seo_redirect($url) : false );
			} else {
				return $this->seo_opt['zero_dupe']['go_redir'] && ( (utf8_strpos( $uri, $url_check ) === false) ? $this->seo_redirect($url) : false );
			}
		}
	}
	/**
	* expected_url($path = '')
	* build expected url
	*/
	function expected_url($path = '') {
		global $phpbb_root_path, $phpEx;
		$path = empty($path) ? $phpbb_root_path : $path;
		$params = array();
		foreach ($this->seo_opt['zero_dupe']['redir_def'] as $get => $def) {
			if ((isset($_GET[$get]) && $def['keep']) || !empty($def['force'])) {
				$params[$get] = $def['val'];
				if (!empty($def['hash'])) {
					$params['#'] = $def['hash'];
				}
			}
		}
		$this->page_url = append_sid($path . $this->seo_opt['req_file'] . ".$phpEx", $params, false, 0);
		return $this->page_url;
	}
	/**
	* set_cond($bool, $type = 'bool_redir', $or = true)
	* Helps out grabbing boolean vars
	*/
	function set_cond($bool, $type = 'do_redir', $or = true) {
		if ( $or ) {
			$this->seo_opt['zero_dupe'][$type] = (boolean) ($bool || $this->seo_opt['zero_dupe'][$type]);
		} else {
			$this->seo_opt['zero_dupe'][$type] = (boolean) ($bool && $this->seo_opt['zero_dupe'][$type]);
		}
		return;
	}
	/**
	* check start var consistency
	* Returns our best guess for $start, eg the first valid page
	*/
	function seo_chk_start($start = 0, $limit = 0) {
		if ($limit > 0) {
			$start = is_int($start/$limit) ? $start : intval($start/$limit)*$limit;
		}
		if ( $start >= 1 ) {
			$this->start = $this->seo_delim['start'] . (int) $start;
			return (int) $start;
		}
		$this->start = '';
		return 0;
	}
	// <-- Zero Duplicate
}
?>
