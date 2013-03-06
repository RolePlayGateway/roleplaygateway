<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_output.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
* gym_sitemaps Class
* www.phpBB-SEO.com
* @package GYM Sitemaps & RSS
*/
class gym_output {
	var $options = array();
	var $outputs = array();
	var $cache = array();
	var $gzip_config = array();
	/**
	* constuctor
	*/
	function gym_output(&$gym_master) {
		$this->gym_master = &$gym_master;
		$this->options = &$this->gym_master->actions;
		$this->outputs = &$this->gym_master->output_data;
		$this->cache = &$this->gym_master->cache_config;
		// Set up and check gzip handling
		$this->init_gzip();
		if ($this->outputs['showstats']) {
			if ( @function_exists('memory_get_usage') ) {
				$this->outputs['mem_usage'] = memory_get_usage();
			}
		}
		// used to skip cache when auth items are not cached
		// initialized after thanks to the &
		$this->cache['do_cache'] = true;
	}
	/**
	* do_output() is called when no cache file is available for the request
	* Will filter cases, call function to build output and cache on the fly
	* @access private
	*/
	function do_output() {
		global $config, $phpEx, $phpbb_seo, $db;
		if ($this->outputs['showstats']) {
			$this->outputs['gen_data'] = sprintf('%.5f', $phpbb_seo->microtime_float() - $this->outputs['microtime']);
			$mem_stats = $this->mem_usage();
			$this->outputs['data'] .= "\n" . sprintf($this->gym_master->style_config['stats_genlist'], $this->outputs['gen_data'], $mem_stats, $db->sql_num_queries(), $this->outputs['url_sofar']);
		}
		if ($this->cache['cache_enable']) {
			$this->write_cache();
			if ($this->check_cache($this->cache['file'])) {
				$this->cache_output();
			} else {
				$this->otf_output();
			}
		} else {
			$this->otf_output();
		}
		return;
	}
	/**
	* cache_output() is called to output from a cached file
	* Build the last stats, send the header and output the cached file
	* @access private
	*/
	function cache_output() {
		global $phpbb_seo, $db;
		if ($this->outputs['showstats']) {
			$this->outputs['gen_out'] = sprintf('%.5f', $phpbb_seo->microtime_float() - $this->outputs['microtime']);
			$genstats = sprintf($this->gym_master->style_config['stats_start'], $this->outputs['gen_out'], $db->sql_num_queries());
		} else {
			$genstats = '';
		}
		if ($this->gzip_config['gzip']) {
			$this->send_header();
			readfile($this->cache['file']);
		} else {
			$this->send_header();
			if ($this->cache['cache_force_gzip']) {
				readgzfile($this->cache['file']);
				if ($this->outputs['showstats']) {
					$this->outputs['gen_out'] = sprintf('%.5f', $phpbb_seo->microtime_float() - $this->outputs['microtime']);
					$genstat2 = sprintf($this->gym_master->style_config['stats_end'], $this->outputs['gen_out'], $db->sql_num_queries());
					echo $genstats . $genstat2;
				}
			} else {
				readfile($this->cache['file']);
				if ($this->outputs['showstats']) {
					$this->outputs['gen_out'] = sprintf('%.5f', $phpbb_seo->microtime_float() - $this->outputs['microtime']);
					$genstat2 = sprintf($this->gym_master->style_config['stats_end'], $this->outputs['gen_out'], $db->sql_num_queries());
					echo $genstats . $genstat2;
				}
			}
		}
		$this->gym_master->safe_exit();
		return;
	}
	/**
	* otf_output() will do the output on the fly
	* when cache disabled or caching failed
	* @access private
	*/
	function otf_output() {
		global $lang,$phpbb_seo;
		// Unset lang array before output
		unset($lang);
		if ($this->gzip_config['gzip']) {
			$this->outputs['data'] = gzencode($this->outputs['data'], $this->gym_master->gzip_config['gzip_level']);
			$this->send_header();
			echo $this->outputs['data'];
			unset($this->outputs['data']);

		} else {
			$this->send_header();
			echo $this->outputs['data'];
			if ($this->outputs['showstats']) {
				$mem_stats = $this->mem_usage();
				$this->outputs['gen_out'] = sprintf('%.5f', $phpbb_seo->microtime_float() - $this->outputs['microtime']);
				$genstats = sprintf($this->gym_master->style_config['stats_nocache'], $this->outputs['gen_out'], $mem_stats);
				echo $genstats;
			}
			unset($this->outputs['data']);
		}
		$this->gym_master->safe_exit();
		return;
	}
	/**
	* send_header() takes care about headers
	* @access private
	*/
	function send_header() {
		global $user;
		if (!empty($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'], 'Apache/2')) {
			header ('Cache-Control: no-cache, pre-check=0, post-check=0, max-age=0');
		} else {
			header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
		}
		$content_types = array('rss' => 'application/rss+xml', 'google' => 'text/xml', 'yahoo' => 'text/plain');
		if (stripos($user->browser, 'YahooFeedSeeker') === false) {
			if ($this->gym_master->actions['action_type'] === 'rss' && ( $this->gym_master->gym_config['rss_xslt'] && $this->gym_master->gym_config['rss_force_xslt'] ) ) {
				$content_types['rss'] = 'text/xml';
			}
		}
		header('Expires: '. $this->outputs['expires_time']);
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $this->outputs['last_mod_time']));
		if (!empty($this->outputs['etag'])) {
			header('Etag: ' . $this->outputs['etag']);
		}
		header('Accept-Ranges: bytes');
		header('Content-type: ' . $content_types[$this->gym_master->actions['action_type']] . '; charset=UTF-8');
		if ($this->gzip_config['gzip']) {
			header('Content-Encoding: ' . $this->check_gzip_type());
		}
		return;
	}

	// --> RSS functions <--

	// --> Cache function <--
	/**
	* setup_cache Check and cache set up
	* @access private
	*/
	function setup_cache() {
		// For module inclusion in ACP : No cache
		if ( defined('ADMIN_START') ) {
			return;
		}
		// build cache file name
		$file_name = ( !empty($this->options['module_sub']) ?  $this->options['module_sub'] : '' ) . trim(str_replace(array('&amp;', '/'), '-', $this->options['extra_params_full']),'-') . '-a' . $this->options['auth_param'];
		if ($this->gym_master->gym_config['gym_cript_cache']) {
			$file_name = md5( $file_name );
		}
		$file_name = $this->gym_master->actions['action_type'] . '_' . ( ( !empty($this->options['module_main']) ) ?  $this->options['module_main'] . '_' : 'main_' ) . $file_name . $this->cache['cache_file_ext'];
		$this->cache['file'] = $this->gym_master->path_config['gym_path'] . 'cache/' . $file_name;
		// Output, first check cache
		if ($this->cache['do_cache'] && $this->check_cache($this->cache['file'])) {
			// Check expiration
			$this->cache['cache_born'] = filemtime($this->cache['file']);
			$this->cache['cache_too_old'] = ($this->cache['cache_born'] + $this->cache['cache_max_age']) <= $this->outputs['time'] ? true : false;
			if ($this->cache['cache_too_old'] && $this->cache['cache_auto_regen']) {
				@unlink($this->cache['file']);
				$this->cache['cached'] = false;
			}
		}
		// Expiration time & Etags
		if (!$this->cache['cached'] || !$this->cache['do_cache']) {
			// Take care about lastmod when not cached
			if (($this->outputs['last_mod_time'] + $this->cache['cache_max_age']) <= $this->outputs['time']) {
				$this->outputs['last_mod_time'] = $this->outputs['time'];
				$this->outputs['expires_time'] = gmdate('D, d M Y H:i:s \G\M\T', ($this->outputs['time'] + $this->cache['cache_max_age']));
				$this->outputs['etag'] = md5($this->outputs['expires_time'] . $this->cache['file']);
				$this->update_lastmod();
			} else {
				$this->outputs['expires_time'] = gmdate('D, d M Y H:i:s \G\M\T', ($this->outputs['last_mod_time'] + $this->cache['cache_max_age']));
				$this->outputs['etag'] = md5($this->outputs['expires_time'] . $this->cache['file']);
			}
			$this->check_mod_since();
		} else {
			$this->outputs['last_mod_time'] = $this->cache['cache_born'];
			$this->outputs['expires_time'] = gmdate('D, d M Y H:i:s \G\M\T', ($this->outputs['last_mod_time'] + $this->cache['cache_max_age']));
			$this->outputs['etag'] = md5($this->outputs['expires_time'] . $this->cache['file']);
			$this->check_mod_since();
			$this->cache_output();
		}

		return;
	}
	/**
	* update_lastmod Update the lastmod date, used when cache is not actvated.
	* @access private
	*/
	function update_lastmod() {
		global $config;
		$config_name = $this->options['action_type'] . '_' . (!empty($this->options['module_main']) ? $this->options['module_main'] . '_' : '') . 'last_mod_time';
		$config_value = $this->outputs['last_mod_time'] > $config['board_startdate'] ? $this->outputs['last_mod_time'] : $this->outputs['time'];
		set_config($config_name, $config_value, 1);

		return;
	}
	/**
	* mem_usage()
	* @access private
	*/
	function mem_usage() {
		if (function_exists('memory_get_usage')) {
			if ($memory_usage = memory_get_usage()) {
				$memory_usage -= $this->outputs['mem_usage'];
				$memory_usage = ($memory_usage >= 1048576) ? round((round($memory_usage / 1048576 * 100) / 100), 2) . ' MB' : (($memory_usage >= 1024) ? round((round($memory_usage / 1024 * 100) / 100), 2) . ' Kb' : $memory_usage . ' b');
				return "( Mem Usage : $memory_usage )";
			}
		}
		return '';
	}
	/**
	* check_mod_since() will exit with 304 Not Modified header
	* and exit upon HTTP_IF_MODIFIED_SINCE or HTTP_IF_NONE_MATCH Checks
	* @access private
	*/
	function check_mod_since() {
		if ($this->cache['mod_since']) {
			$http = 'HTTP/1.1 ';
			if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
				if ($_SERVER['HTTP_IF_NONE_MATCH'] == $this->outputs['etag']) {
					header($http . ' 304 Not Modified');
					$this->gym_master->safe_exit();
				}
			}
			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
				if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $this->outputs['last_mod_time']) {
					header($http . ' 304 Not Modified');
					$this->gym_master->safe_exit();
				}
			}
		}
		return;
	}
	/**
	* check_cache($file) will tell if the required file exists.
	* @access private
	*/
	function check_cache($file) {
		if(!$this->cache['cache_enable']) {
			$this->cache['cached'] = false;
			return false;
		}
		if(!@file_exists($file)) {
			$this->cache['cached'] = false;
			return false;
		}
		$this->cache['cached'] = true;
		return true;
	}
	/**
	* write_cache( $action, $id = 0 ) will write the cached file.
	* @access private
	*/
	function write_cache() {
		if(!$this->cache['cache_enable'] or !$this->cache['do_cache']) {
			return false;
		}
		$file = $this->cache['file'];
		if ($this->gzip_config['gzip'] || $this->cache['cache_force_gzip']) {
			$handle = @gzopen($file, 'wb');
			@flock($handle, LOCK_EX);
			@gzwrite($handle, $this->outputs['data']);
			@flock($handle, LOCK_UN);
			@gzclose ($handle);
		} else {
			$handle = @fopen($file, 'wb');
			@flock($handle, LOCK_EX);
			@fwrite($handle, $this->outputs['data']);
			@flock($handle, LOCK_UN);
			@fclose ($handle);
		}
		$this->update_lastmod();
		@umask(0000);
		@chmod($file, 0666);
		return true;
	}
	// --> Gun-Zip handeling <--
	/**
	* init_gzip ().
	* Check if gzip is available and set proper values for this event
	* if phpBB gun-zip is acvtivated, then it must be in the module
	* @access private
	*/
	function init_gzip() {
		global $config;
		$this->gzip_config['gzip'] = $config['gzip_compress'] ? 1 : intval($this->gym_master->set_module_option('gzip', $this->gym_master->override['gzip']));
		if (!$this->check_gzip() && $this->gzip_config['gzip']) {
			$this->gzip_config['gzip'] = false;
		}
		$this->gym_master->url_config['gzip_ext_out'] = $this->gzip_config['gzip'] ? '.gz' : '';
		$this->gym_master->url_config['gzip_ext_out'] = (intval($this->gym_master->set_module_option('gzip_ext', $this->gym_master->override['gzip'])) > 0) ? $this->gym_master->url_config['gzip_ext_out'] : '';
		$this->check_requested_ext();
		return;
	}
	/**
	* check_gzip() tells is Gun-Zip is available
	* @access private
	*/
	function check_gzip() {
		if (headers_sent()) {
			return false;
		}
		if (!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && ( (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !==false ) || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !==false ) ) {
			return true;
		} else {
			return false;
		}
		return;
	}
	/**
	* check_gzip_type() return the user's Gun-Zip type
	* @access private
	*/
	function check_gzip_type() {
		if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !==false ) {
			return 'gzip';
		} elseif (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !==false ) {
			return 'x-gzip';
		}
		return 'gzip';
	}
	/**
	* check_requested_ext($uri) will force the .gz extention if required
	* @access private
	*/
	function check_requested_ext() {
		global $phpbb_seo, $phpEx;
		return;
		$uri = $phpbb_seo->seo_path['uri'];
		if ( ( strpos($phpbb_seo->seo_path['uri'], '.gz') !== false ) && ($this->ext_config['gzip_ext_out'] == '') && !strpos($uri, $phpEx)) {
			$uri = str_replace ('.gz', "", $uri);
			$url= $this->path_config['root_url'] . ltrim($uri, '/');
			$this->gym_master->gym_redirect($url);
		} elseif ( ( strpos($uri, '.gz') === false ) && ($this->ext_config['gzip_ext_out'] != '') && !strpos($uri, $phpEx)) {
			$uri = $uri . '.gz';
			$url= $this->path_config['root_url'] . ltrim($uri, '/');
			$this->gym_master->gym_redirect($url);
		}
		return;
	}
}
?>