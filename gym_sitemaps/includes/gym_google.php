<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_google.php 112 2009-09-30 17:21:34Z dcz $
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
* gym_google Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class gym_google extends gym_sitemaps {
	var	$google_config = array();
	// changefreq values, set to null to deactivate a value
	var	$freq_values = array('always' => 1, 'hourly' => 1, 'daily' => 1, 'weekly' => 1, 'monthly' => 1, 'yearly' => 1, 'never'  => 1);
	/**
	* constuctor
	*/
	function gym_google() {
		global $phpbb_seo, $phpEx, $config;
		global $db, $auth, $user;
		$this->gym_sitemaps('google');
		// init output
		$this->output_data['showstats'] = $this->gym_config['gym_showstats'] && $this->gym_config['google_showstats'];
		// Check the main vars
		$this->init_get_vars();
		if (empty($this->actions['action_modules'])) {
			$this->gym_error(404, '', __FILE__, __LINE__);
		}
		// Set last mod time from DB, will only be used as his for sitempaindex
		// put it into phpbb config for the dynamic property.
		$config_name = $this->actions['action_type'] . '_' . (!empty($this->actions['module_main']) ? $this->actions['module_main'] . '_' : '') . 'last_mod_time';
		if (@$config[$config_name] < $config['board_startdate']) {
			set_config($config_name, $this->output_data['time'], 1);
		}
		$this->output_data['last_mod_time'] = intval($config[$config_name]);
		// Init the output class
		$this->gym_init_output();
		// Setup the output
		$this->cache_config = array_merge(
			// Global
			$this->cache_config,
			// Other stuff required here
			array(
				'cache_enable' => (boolean) $this->set_module_option('cache_on', $this->override['cache']),
				'cache_auto_regen' => (boolean) $this->set_module_option('cache_auto_regen', $this->override['cache']),
				'cache_force_gzip' => (boolean) $this->set_module_option('cache_force_gzip', $this->override['cache']),
				'cache_born' => $this->output_data['last_mod_time'],
				'cache_max_age' => round($this->set_module_option('cache_max_age', $this->override['cache']),2) * 3600,
				'cache_file_ext' => ( $this->gym_output->gzip_config['gzip'] || $this->set_module_option('cache_force_gzip', $this->override['cache']) ) ? '.xml.gz' : '.xml',
			)
		);
		// Can you believe it, sprintf is faster than straight parsing.
		$this->style_config	= array('Sitemap_tpl' => "\n\t" . '<url>' . "\n\t\t" . '<loc>%1$s</loc>%2$s%3$s%4$s' . "\n\t" . '</url>',
			'SitmIndex_tpl' => "\n\t" . '<sitemap>' . "\n\t\t" . '<loc>%s</loc>%s' . "\n\t" . '</sitemap>',
			'lastmod_tpl' => "\n\t\t" . '<lastmod>%s</lastmod>',
			'changefreq_tpl' => "\n\t\t" . '<changefreq>%s</changefreq>',
			'priority_tpl' => "\n\t\t" . '<priority>%.1f</priority>',
			'xslt_style' => '',
			'stats_genlist'	=> "\n" . '<!-- URL list generated in  %s s %s - %s sql - %s URLs listed -->',
			'stats_start' => "\n" . '<!--  Output started from cache after %s s - %s sql -->',
			'stats_nocache'	=> "\n" . '<!--  Output ended after %s s %s -->',
			'stats_end' => "\n" . '<!--  Output from cache ended up after %s s - %s sql -->',
		);
		$this->google_config = array(
			'google_default_priority' => $this->set_module_option('default_priority', $this->gym_config['google_override']),
			'google_url' => $this->gym_config['google_url'],
			// module specific settings we should often need in module
			'google_modrewrite' => (int) $this->set_module_option('modrewrite', $this->override['modrewrite']),
			'google_modrtype' => (int) $this->set_module_option('modrtype', $this->override['modrewrite']),
			'google_pagination' => $this->set_module_option('pagination', $this->override['pagination']),
			'google_limitdown' => (int) $this->set_module_option('limitdown', $this->override['pagination']),
			'google_limitup'=> (int) $this->set_module_option('limitup', $this->override['pagination']),
			'google_sql_limit' => (int) $this->set_module_option('sql_limit', $this->override['limit']),
			'google_url_limit' => (int) $this->set_module_option('url_limit', $this->override['limit']),
			'google_sort' => ($this->set_module_option('sort', $this->override['sort']) === 'DESC') ? 'DESC' : 'ASC',
			'google_ping' => $this->set_module_option('ping', $this->gym_config['google_override']),
			// display threshold
			'google_threshold' => max(1, (int) $this->gym_config['google_threshold']),
			'google_allow_auth' => (int) $this->set_module_option('allow_auth', $this->gym_config['google_override']),
			'google_cache_auth' => (int) $this->set_module_option('cache_auth', $this->gym_config['google_override']),
		);
		$this->google_config['google_auth_guest'] = ($this->google_config['google_allow_auth'] && ($user->data['is_bot'] || $user->data['is_registered'])) ? false : true;
		$this->cache_config['do_cache'] = $this->google_config['google_auth_guest'] ? true :  $this->google_config['google_cache_auth'];
		if ($this->gym_config['google_xslt']) {
			$this->style_config['xslt_style'] = "\n" . '<?xml-stylesheet type="text/xsl" href="' . $phpbb_seo->seo_path['phpbb_url'] . 'gym_sitemaps/gym_style.' . $phpEx . '?action-google,type-xsl,lang-' . $config['default_lang'] . ',theme_id-' . $config['default_style'] . '" ?'.'>';
		}
		// Take care about module categorie urls, assuming that they are of the proper form
		// title-sepXX.xml
		// assuming that phpbb_seo seo_delim array is properly set.
		if (empty($this->actions['module_main']) && empty($this->actions['module_sub']) && !empty($_REQUEST['module_sep']) && !empty($_REQUEST['module_sub'])) {
			if ($module = @array_search('-' . $_REQUEST['module_sep'], $phpbb_seo->seo_delim)) {
				$this->actions['module_main'] = $module;
				$this->actions['module_sub'] = (int) $_REQUEST['module_sub'];
			}
		}
		// Are we going to explain ?
		$do_explain = false;
		if (!empty($_REQUEST['explain']) && $auth->acl_get('a_') && defined('DEBUG_EXTRA') && method_exists($db, 'sql_report')) {
			$do_explain = true;
			$this->cache_config['do_cache'] = false;
		}
		if ( empty($this->actions['module_main']) ) { // SitemapIndex
			$this->google_sitemapindex();
		} else { // Sitemap
			$this->google_sitemap();
		}
		if ($do_explain) {
			$db->sql_report('display');
		} else {
			$this->gym_output->do_output();
		}
		return;
	}
	/**
	* GGs_sitemapindex() will build our sitemapIndex
	* Listing all available sitemaps
	* @access private
	*/
	function google_sitemapindex() {
		global $phpEx, $phpbb_seo, $db;
		$sitemapindex_url = $this->gym_config['google_url'] . ( $this->google_config['google_modrewrite'] ? 'sitemapindex.xml' . $this->url_config['gzip_ext_out'] : 'sitemap.'.$phpEx);
		$this->seo_kill_dupes($sitemapindex_url);
		$this->output_data['data'] = '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' . $this->style_config['xslt_style'] . "\n" . '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n\t" . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n\t" . 'http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"' . "\n\t" . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" . '<!-- Generated by Google Yahoo MSN Sitemaps and RSS ' . $this->gym_config['gym_version'] . ' - © 2006, 2007 www.phpBB-SEO.com -->' . "\n";
		// start the modules
		$this->load_modules('sitemapindex');
		// Grabb the total
		$this->output_data['url_sofar'] = $this->output_data['url_sofar_total'];
		if ( empty($this->output_data['url_sofar']) ) {
			$this->gym_error(404, 'GYM_TOO_FEW_ITEMS', __FILE__, __LINE__);
		}
		$this->output_data['data'] .= "\n" . '</sitemapindex>';
		if ( $this->google_config['google_ping'] && ($this->output_data['time'] >= ($this->cache_config['cache_born'] + $this->cache_config['cache_max_age'])) ) {
			$this->google_ping($sitemapindex_url);
		}
		return;
	}
	/**
	* GGs_sitemap() will build the actual Google sitemaps, all cases
	* @access private
	*/
	function google_sitemap() {
		global $phpEx, $phpbb_seo, $db;
		// Initialize SQL cycling : do not query for more than required
		$this->gym_config['google_sql_limit'] = ($this->gym_config['google_sql_limit'] > $this->gym_config['google_url_limit']) ? $this->gym_config['google_url_limit'] : $this->gym_config['google_sql_limit'];
		$this->output_data['data'] = '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' . $this->style_config['xslt_style'] . "\n" . '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n\t" . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n\t" . 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"' . "\n\t" . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" . '<!-- Generated by Google Yahoo MSN Sitemaps and RSS ' . $this->gym_config['gym_version'] . ' - © 2006, 2007, 2008 www.phpBB-SEO.com -->' . "\n";
		// start the module
		$module_class = $this->actions['action_type'] . '_' . $this->actions['module_main'];
		$this->load_module($module_class, 'sitemap');
		if (empty($this->output_data['url_sofar']) ) {
			$this->gym_error(404, 'GYM_TOO_FEW_ITEMS', __FILE__, __LINE__);
		}
		$this->output_data['data'] .= "\n" . '</urlset>';
		if ( $this->google_config['google_ping'] && ($this->output_data['time'] >= ($this->cache_config['cache_born'] + $this->cache_config['cache_max_age'])) ) {
			$this->google_ping();
		}
		return;
	}
	/**
	* parse_sitemap($url, $lastmodtime = 0)
	* adds the module sitemaps to the sitemapindex
	*/
	function parse_sitemap($url, $lastmodtime = 0) {
		global $config, $user;
		if ($lastmodtime > $config['board_startdate']) {
			$lastmodtime = sprintf($this->style_config['lastmod_tpl'], gmdate('Y-m-d\TH:i:s'.'+00:00', intval($lastmodtime)));
		} else {
			$lastmodtime = '';
		}
		$this->output_data['data'] .= sprintf($this->style_config['SitmIndex_tpl'], $url, $lastmodtime);
		$this->output_data['url_sofar']++;
	}
	/**
	* parse_item() adds the item info to the output
	*/
	function parse_item($url, $priority = 1.0, $changefreq = 'always', $lastmodtime = 0) {
		global $config, $user;
		$changefreq = isset($this->freq_values[$changefreq]) ? sprintf($this->style_config['changefreq_tpl'], $changefreq) : '';
		$priority = $priority <= 1 && $priority > 0 ? str_replace(',', '.', sprintf($this->style_config['priority_tpl'], $priority)) : '';
		$lastmodtime = $lastmodtime > $config['board_startdate'] ? sprintf($this->style_config['lastmod_tpl'], gmdate('Y-m-d\TH:i:s'.'+00:00', intval($lastmodtime))) : '';
		$this->output_data['data'] .= sprintf($this->style_config['Sitemap_tpl'], $url, $lastmodtime, $changefreq, $priority);
		$this->output_data['url_sofar']++;
	}
	/**
	* get_priority() computes the priority, bases on last mod time and page number
	* Freshest items with most pages gets the highest priority
	* 42 is the answer to the most important question in the universe ;-)
	*/
	function get_priority($lastmodtime, $pages = 1) {
		global $user;
		return $user->time_now / ($user->time_now + ((($user->time_now - $lastmodtime)* 42) / $pages));
	}
	/**
	* get_changefreq() computes the changefreq, based on lastmodtime
	*/
	function get_changefreq($lastmodtime) {
		global $user;
		$dt = $user->time_now - $lastmodtime;
		// 	42 weeks ~ 10 month		| 8 weeks 			| 15 days			| 2 days		| 12 hours
		return $dt > 25401600 ? 'yearly' : ( $dt > 4838400 ? 'monthly' : ( $dt > 1296000 ? 'weekly' : ( $dt > 172800 ? 'daily' : ( $dt > 43200 ? 'hourly' : 'always' ) ) ) );
	}
	/**
	* parse_sitemap($url, $lastmodtime = 0)
	* adds the module sitemaps to the sitemapindex
	*/
	function google_ping($url = '') {
		global $config;
		$url = !empty($url) ? str_replace('&amp;', '&', $url) : (!empty($this->url_config['current']) ? $this->url_config['current'] : '');
		$url = trim($url);
		if (empty($url)) {
			return;
		}
		// No more than 200 pings a day!
		if (@$config['gym_pinged_today'] > 200) {
			// @TODO add logs about this ?
			return;
		}
		$skip = array('http://localhost', 'http://127.0.0.1', 'http://192.168.');
		foreach ($skip as $_skip) {
			if (utf8_strpos($url, $_skip) !== false) {
				// @TODO add logs about this ?
				return;
			}
		}
		$se_urls = array('http://www.google.com/', /*'http://www.yahoo.com/', 'http://www.live.com/'*/);
		$timout = 3;
		$time = time();
		$pinged = 0;
		foreach ($se_urls as $se_url) {
			if (time() - $time >= $timout) {
				return;
			}
			$request = $se_url . 'ping?sitemap=' . urlencode($url);
			if ( function_exists('file_get_contents') ) {
				// Make the request
				@ini_set('user_agent','GYM Sitemaps &amp; RSS / www.phpBB-SEO.com');
				@ini_set('default_socket_timeout', $timout);
				$status_code = false;
				if (@file_get_contents($request)) {
					// Retrieve HTTP status code
					@list($version,$status_code,$msg) = @explode(' ',$http_response_header[0], 3);
				}
				if (@$status_code != 200) {
					// @TODO add logs about this ?
				} else {
					$pinged++;
					$this->style_config['stats_genlist'] .= "\n<!--  Pinged $se_url - $url -->";
				}
			} else if (function_exists('curl_exec')) {
				// Initialize the session
				$session = curl_init($request);
				// Set curl options
				curl_setopt($session, CURLOPT_HEADER, false);
				curl_setopt($session, CURLOPT_USERAGENT, 'GYM Sitemaps &amp; RSS / www.phpBB-SEO.com');
				curl_setopt($session, CURLOPT_TIMEOUT, $timout);
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				// Make the request
				$response = curl_exec($session);
				// Close the curl session
				curl_close($session);
				// Get HTTP Status code from the response
				$status_codes = array();
				preg_match('/\d\d\d/', $response, $status_code);
				$status_code = @$status_codes[0];
				// Get the the response, bypassing the header
				if ($status_code != 200) {
					// @TODO add logs about this ?
				} else {
					$pinged++;
					$this->style_config['stats_genlist'] .= "\n<!--  Pinged $se_url - $url -->";
				}
			} else {
				// @TODO add logs about this ?
			}
		}
		if ($pinged) {
			set_config('gym_pinged_today', @$config['gym_pinged_today'] + $pinged, 1);
		}
		return;
	}
}
?>