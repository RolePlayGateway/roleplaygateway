<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_xml.php 148 2009-11-07 14:50:54Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* google_xml Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class google_xml {
	var $url_settings = array();
	var $options = array();
	var $module_config = array();
	var $outputs = array();
	var $xml_files = array();
	/**
	* constuctor
	*/
	function google_xml(&$gym_master) {
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
				'google_randomize' => (boolean) $this->gym_master->gym_config['google_xml_randomize'],
				'google_unique' => (boolean) $this->gym_master->gym_config['google_xml_unique'],
				'google_check_robots' => (boolean) $this->gym_master->gym_config['google_xml_check_robots'],
				'google_force_limit' => (boolean) $this->gym_master->gym_config['google_xml_force_limit'],
				'google_force_lastmod' => (boolean) $this->gym_master->gym_config['google_xml_force_lastmod'],
			)
		);
		$this->module_config['xml_parse'] = (boolean) ($this->module_config['google_randomize'] || $this->module_config['google_unique'] || $this->module_config['google_force_limit'] || $this->module_config['google_force_lastmod']|| $this->module_config['google_check_robots']);
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
		$this->url_settings['google_xml_delim'] = !empty($phpbb_seo->seo_delim['google_xml']) ? $phpbb_seo->seo_delim['google_xml'] : '-';
		$this->url_settings['google_xml_static'] = !empty($phpbb_seo->seo_static['google_xml']) ? $phpbb_seo->seo_static['google_xml'] : 'xml';
		$this->url_settings['modrewrite'] = $this->module_config['google_modrewrite'];

		if ($this->url_settings['modrewrite']) { // Module links
			$this->url_settings['google_xml_tpl'] = $this->module_config['google_url'] . 'xml' . $this->url_settings['google_xml_delim'] . '%1$s.xml' . $this->url_settings['gzip_ext_out'];
		} else {
			$this->url_settings['google_xml_tpl'] = $this->module_config['google_url'] . $this->url_settings['google_default'] . '?xml=%1$s';
		}
		return;
	}
	/**
	* sitemap, builds the sitemap
	* @access private
	*/
	function sitemap() {
		global $cache, $phpEx, $config, $user;
		if (!empty($this->xml_files[$this->options['module_sub']])) {
			// Check robots.txt ?
			if ($this->module_config['google_check_robots']) {
				$this->gym_master->obtain_robots_disallows();
			}
			$sitemap_xml_url = sprintf( $this->url_settings['google_xml_tpl'], $this->options['module_sub'] );
			$this->gym_master->seo_kill_dupes($sitemap_xml_url);
			$xml_file = $this->xml_files[$this->options['module_sub']];
			// Grab data
			if (strpos($xml_file, 'http://') !== false) {
				@ini_set('user_agent','GYM Sitemaps &amp; RSS / www.phpBB-SEO.com');
				// You may want to use a higher value for the timout in case you use slow external sitemaps
				@ini_set('default_socket_timeout', 5);
			}
			if ($xml_data = @file_get_contents($xml_file)) {
				if (!empty($http_response_header)) {
					$_last_mod = get_date_from_header($http_response_header);
				} else {
					$_last_mod = (int) @filemtime($xml_file);
				}
				$this->outputs['last_mod_time'] = $_last_mod > $config['board_startdate'] ? $_last_mod : ($user->time_now - rand(500, 10000));
				if (($url_tag_pos = utf8_strpos($xml_data, '<url>')) === false) {
					// this basic test failed
					// @TODO add loggs about this ?
					$this->gym_master->gym_error(404, '', __FILE__, __LINE__);
				}
				if (!$this->module_config['xml_parse']) {
					// use our hown headers
					$xml_data = str_replace('</urlset>', '', trim($xml_data) );
					// Add to the output variable
					$this->outputs['data'] .= substr($xml_data, $url_tag_pos);
					// Link count
					$this->outputs['url_sofar'] = preg_match_all('`\<loc\>[^<>]+\</loc\>`Ui', $xml_data, $matches);
					// free memory
					unset($xml_data, $matches);
				} else {
					$total_matches = preg_match_all('`\<url\>.+\</url\>`Usi', $xml_data, $matches, PREG_SET_ORDER);
					// free memory
					unset($xml_data);
					if (!empty($matches)) {
						// Randomize ?
						if ($this->module_config['google_randomize']) {
							shuffle($matches);
						}
						// Limit ?
						if ($this->module_config['google_url_limit'] > 0 && $this->module_config['google_url_limit'] < $total_matches) {
							$matches = array_slice($matches, 0, $this->module_config['google_url_limit']);
						}
						// Force last mod  ?
						$_last_mod = $this->module_config['google_force_lastmod'] ? $this->outputs['last_mod_time'] : 0;
						// Parse URLs
						$dt = rand(0, 3600);
						$url_check = array();
						foreach ($matches as $key => $data) {
							preg_match_all('`\<(loc|lastmod|changefreq|priority)\>([^<>]+)\</\1\>`Ui', $data[0], $url_tags, PREG_SET_ORDER);
							$loc = $priority = $changefreq = $lastmod = '';
							foreach ($url_tags as $url_tag) {
								if (empty($url_tag[1]) || empty($url_tag[2])) {
									continue;
								}
								$url_tag[1] = strtolower($url_tag[1]);
								${$url_tag[1]} = trim($url_tag[2]);
							}
							if (empty($loc)) {
								continue;
							}
							// Check unique ?
							if ($this->module_config['google_unique']) {
								if (isset($url_check[$loc])) {
									continue;
								}
								$url_check[$loc] = 1;
							}
							if ($this->module_config['google_check_robots'] && $this->gym_master->is_robots_disallowed($loc)) {
								continue;
							}
							if ($this->module_config['google_force_lastmod']) {
								$last_mod = $_last_mod - $dt;
								$priority = $this->gym_master->get_priority($last_mod);
								$changefreq = $this->gym_master->get_changefreq($last_mod);
								$lastmod = gmdate('Y-m-d\TH:i:s'.'+00:00', $last_mod);
							} else {
								$lastmod = !empty($lastmod) ? $lastmod : 0;
								$priority = !empty($priority) ? $priority : 0;
								$changefreq = !empty($changefreq) ? $changefreq : 0;
							}
							$this->parse_item($loc, $priority, $changefreq, $lastmod);
							unset($matches[$key]);
							$dt += rand(30, 3600*12);
						}
						unset($url_check);
					} else {
						// Clear the cache to make sure the guilty url is not shown in the sitemapIndex
						$cache->destroy('_gym_config_google_xml');
						$this->gym_master->gym_error(500, '', __FILE__, __LINE__);
					}

				}
			} else {
				// Clear the cache to make sure the guilty url is not shown in the sitemapIndex
				$cache->destroy('_gym_config_google_xml');
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
		foreach ($this->xml_files as $xml_action => $source) {
			$sitemap_xml_url = sprintf( $this->url_settings['google_xml_tpl'], $xml_action );
			$last_mod = (int) @filemtime($xml_file);
			$last_mod = ($last_mod > $config['board_startdate'] && !$this->module_config['google_force_lastmod']) ? $last_mod : (time() - rand(500, 10000));
			$this->gym_master->parse_sitemap($sitemap_xml_url, $last_mod);
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
		global $cache, $phpEx;
		if (($this->xml_files = $cache->get('_gym_config_google_xml')) === false) {
			$this->xml_files = array();
			// Check the eventual external url config
			if (file_exists($this->module_config['google_sources'] . "xml_google_external.$phpEx")) {
				include($this->module_config['google_sources'] . "xml_google_external.$phpEx");
				// Duplicated keys will be overriden bellow
				$this->xml_files = array_merge($this->xml_files, $external_setup);
			}
			$RegEx = '`^google_([a-z0-9_-]+)\.xml$`i';
			$xml_dir = @opendir( $this->module_config['google_sources'] );
			while( ($xml_file = @readdir($xml_dir)) !== false ) {
				if(preg_match($RegEx, $xml_file, $matches)) {
					if (!empty($matches[1])) {
						$this->xml_files[$matches[1]] = $this->module_config['google_sources'] . 'google_' . $matches[1] . '.xml';
					}
				}
			}
			@closedir($xml_dir);
			$cache->put('_gym_config_google_xml', $this->xml_files);
		}
		return;
	}
	/**
	* parse_item() adds the item info to the output
	*/
	function parse_item($url, $priority = 1.0, $changefreq = 'always', $lastmodtime = 0) {
		global $config, $user;
		$changefreq = isset($this->gym_master->freq_values[$changefreq]) ? sprintf($this->gym_master->style_config['changefreq_tpl'], $changefreq) : '';
		$priority = $priority <= 1 && $priority > 0 ? sprintf($this->gym_master->style_config['priority_tpl'], $priority) : '';
		$lastmodtime = $lastmodtime > $config['board_startdate'] ? sprintf($this->gym_master->style_config['lastmod_tpl'], $lastmodtime) : '';
		$this->gym_master->output_data['data'] .= sprintf($this->gym_master->style_config['Sitemap_tpl'], $url, $lastmodtime, $changefreq, $priority);
		$this->gym_master->output_data['url_sofar']++;
	}
}
?>