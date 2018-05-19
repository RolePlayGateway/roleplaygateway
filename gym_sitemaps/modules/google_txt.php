<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_txt.php 148 2009-11-07 14:50:54Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* google_txt Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class google_txt {
	var $url_settings = array();
	var $options = array();
	var $module_config = array();
	var $outputs = array();
	var $txt_files = array();
	/**
	* constuctor
	*/
	function google_txt(&$gym_master) {
		$this->gym_master = &$gym_master;
		$this->options = &$this->gym_master->actions;
		$this->outputs = &$this->gym_master->output_data;
		$this->url_settings = &$this->gym_master->url_config;
		$this->module_config = array_merge(
			// Global
			$this->gym_master->google_config,
			// Other stuff required here
			array(
				'google_sources' => $this->gym_master->path_config['gym_path'] . 'sources/',
				'google_randomize' => (boolean) $this->gym_master->gym_config['google_txt_randomize'],
				'google_unique' => (boolean) $this->gym_master->gym_config['google_txt_unique'],
				'google_check_robots' => (boolean) $this->gym_master->gym_config['google_txt_check_robots'],
				'google_force_lastmod' => (boolean) $this->gym_master->gym_config['google_txt_force_lastmod'],
			)
		);
		// Check cache
		$this->gym_master->gym_output->setup_cache(); // Will exit if the cache is sent
		// List available files
		$this->get_source_list();
		// Init url settngs
		$this->init_url_settings();
	}
	/**
	* Initialize mod rewrite to handle multiple URL standards.
	* Only one 'if' is required after this in THE loop to properly switch
	* between the four types (none, advanced, mixed and simple).
	* @access private
	*/
	function init_url_settings() {
		global $phpbb_seo;
		// vars will fell like rain in the code ;)
		$this->url_settings['google_txt_delim'] = !empty($phpbb_seo->seo_delim['google_txt']) ? $phpbb_seo->seo_delim['google_txt'] : '-';
		$this->url_settings['google_txt_static'] = !empty($phpbb_seo->seo_static['google_txt']) ? $phpbb_seo->seo_static['google_txt'] : 'txt';
		$this->url_settings['modrewrite'] = $this->module_config['google_modrewrite'];

		if ($this->url_settings['modrewrite']) { // Module links
			$this->url_settings['google_txt_tpl'] = $this->module_config['google_url'] . 'txt' . $this->url_settings['google_txt_delim'] . '%1$s.xml' . $this->url_settings['gzip_ext_out'];
		} else {
			$this->url_settings['google_txt_tpl'] = $this->module_config['google_url'] . $this->url_settings['google_default'] . '?txt=%1$s';
		}
		return;
	}
	/**
	* sitemap, builds the sitemap
	* @access private
	*/
	function sitemap() {
		global $cache, $phpEx, $config;
		if (!empty($this->txt_files[$this->options['module_sub']])) {
			// Check robots.txt ?
			if ($this->module_config['google_check_robots']) {
				$this->gym_master->obtain_robots_disallows();
			}
			$sitemap_txt_url = sprintf( $this->url_settings['google_txt_tpl'], $this->options['module_sub'] );
			$this->gym_master->seo_kill_dupes($sitemap_txt_url);
			$txt_file = $this->txt_files[$this->options['module_sub']];
			// Grab data
			if (($txt_data = @file($txt_file)) && is_array($txt_data)) {
				$last_mod = (int) @filemtime($txt_file);
				$url_count = count($txt_data);
				$this->outputs['last_mod_time'] = $last_mod > $config['board_startdate'] ? $last_mod : (time() - rand(500, 10000));
				// Randomize ?
				if ($this->module_config['google_randomize']) {
					shuffle($txt_data);
				}
				// Limit ?
				if ($this->module_config['google_url_limit'] > 0 && $this->module_config['google_url_limit'] < $url_count) {
					$txt_data = array_slice($txt_data, 0, $this->module_config['google_url_limit']);
				}
				// Force last mod  ?
				$last_mod = $this->module_config['google_force_lastmod'] ? $this->outputs['last_mod_time'] : 0;
				// Parse URLs
				$dt = rand(0, 3600);
				$url_check = array();
				foreach ($txt_data as $key => $url) {
					$url = trim($url);
					if (empty($url) || ($this->module_config['google_check_robots'] && $this->gym_master->is_robots_disallowed($url))) {
						continue;
					}
					// Check unique ?
					if ($this->module_config['google_unique']) {
						if (isset($url_check[$url])) {
							continue;
						}
						$url_check[$url] = 1;
					}
					if ($this->module_config['google_force_lastmod']) {
						$_last_mod = $last_mod - $dt;
						$priority = $this->gym_master->get_priority($_last_mod);
						$changefreq = $this->gym_master->get_changefreq($_last_mod);
					} else {
						$_last_mod = $priority = $changefreq = 0;
					}
					$this->gym_master->parse_item(utf8_htmlspecialchars($url), $priority, $changefreq, $_last_mod);
					$dt += rand(30, 3600*12);
					unset($txt_data[$key]);
				}
			} else {
				// Clear the cache to make sure the guilty url is not shown in the sitemapIndex
				$cache->destroy('_gym_config_google_txt');
				$this->gym_master->gym_error(404, '', __FILE__, __LINE__);
			}
		} else {
			$this->gym_master->gym_error(404, '', __FILE__, __LINE__);
		}
		return;
	}
	/**
	* sitemapindex, builds the sitemapindex
	* @access private
	*/
	function sitemapindex() {
		global $config;
		// It's global list call, add module sitemaps
		// Reset the local counting, since we are cycling through modules
		$this->outputs['url_sofar'] = 0;
		foreach ($this->txt_files as $txt_action => $source) {
			$sitemap_txt_url = sprintf( $this->url_settings['google_txt_tpl'], $txt_action );;
			$last_mod = (int) @filemtime($txt_file);
			$last_mod = ($last_mod > $config['board_startdate'] && !$this->module_config['google_force_lastmod']) ? $last_mod : (time() - rand(500, 10000));
			$this->gym_master->parse_sitemap($sitemap_txt_url, $last_mod);
		}
		// Add the local counting, since we are cycling through modules
		$this->outputs['url_sofar_total'] = $this->outputs['url_sofar_total'] + $this->outputs['url_sofar'];
		return;
	}
	/**
	* get_source_list, builds the available sitemap list
	* @access private
	*/
	function get_source_list() {
		global $cache;
		if (($this->txt_files = $cache->get('_gym_config_google_txt')) === false) {
			$this->txt_files = array();
			$RegEx = '`^google_([a-z0-9_-]+)\.txt`i';
			$txt_dir = @opendir( $this->module_config['google_sources'] );
			while( ($txt_file = @readdir($txt_dir)) !== false ) {
				if(preg_match($RegEx, $txt_file, $matches)) {
					if (!empty($matches[1])) {
						$this->txt_files[$matches[1]] = $this->module_config['google_sources'] . 'google_' . $matches[1] . '.txt';
					}
				}
			}
			@closedir($txt_dir);
			$cache->put('_gym_config_google_txt', $this->txt_files);
		}
		return;
	}
}
?>