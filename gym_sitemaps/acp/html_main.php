<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: html_main.php 134 2009-11-02 11:13:45Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* html_main Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class html_main {
	var $gym_master;
	var $dyn_select = array();
	var $lang_iso639 = array();
	var $html_override = array();
	/**
	* constuctor
	*/
	function html_main(&$gym_master) {
		global $user;
		$this->gym_master = &$gym_master;
		if (isset($this->gym_master->dyn_select) ) {
			$this->html_override = $this->gym_master->dyn_select['override'];
			unset($this->html_override[OVERRIDE_GLOBAL]);
			$this->dyn_select = $this->gym_master->dyn_select;
		}
	}
	/**
	* acp_module()
	* retunrs the acp config
	* @access private
	*/
	function acp_module() {
		global $config, $phpbb_seo, $user, $phpEx;
		$config['sitename'] = utf8_normalize_nfc($config['sitename']);
		$config['site_desc'] = utf8_normalize_nfc($config['site_desc']);
		return array(
			'cache' => array(
 				'display_vars' => array(
					'title'	=> 'HTML_CACHE',
					'vars'	=> array(
						'legend1'	=> 'HTML_CACHE',
						'html_main_cache_on'	=> array('lang' => 'HTML_MAIN_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_main_cache_ttl' => array('lang' => 'HTML_MAIN_CACHE_TTL', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true),
						'html_opt_cache_on'	=> array('lang' => 'HTML_OPT_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_opt_cache_ttl' => array('lang' => 'HTML_OPT_CACHE_TTL', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true),
					),
				),
				'default' => array(
					'html_main_cache_on'	=> 0,
					'html_opt_cache_on'	=> 0,
					'html_opt_cache_ttl' => 6,
					'html_main_cache_ttl'	=> 6,
				),
			),
			'modrewrite' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_MODREWRITE',
					'vars'	=> array(
						'legend1'	=> 'GYM_MODREWRITE',
						'html_modrewrite'	=> array('lang' => 'GYM_MODREWRITE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_modrtype' => array('lang' => 'GYM_MODRTYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'html_modrewrite' => 0,
					'html_modrtype' => 0,
				),
				'select' => array(
					'html_modrtype' => @$this->dyn_select['modrtype'],
				),
			),
			'gzip' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend4'	=> 'GYM_GZIP',
						'html_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'html_gzip' => 0,
				),
			),
			'limit' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'GYM_URL_LIMIT',
						'html_rss_news_limit' => array('lang' => 'HTML_RSS_NEWS_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true),
						'legend2'	=> 'GYM_TIME_LIMIT',
						'html_map_time_limit' => array('lang' => 'HTML_MAP_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true),
						'html_cat_time_limit' => array('lang' => 'HTML_CAT_MAP_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true),
						'html_news_time_limit' => array('lang' => 'HTML_NEWS_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true),
						'html_cat_news_time_limit' => array('lang' => 'HTML_CAT_NEWS_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true),
					),
				),
				'default' => array(
					'html_rss_news_limit' => 10,
					'html_map_time_limit' => 0,
					'html_cat_time_limit' => 0,
					'html_news_time_limit' => 0,
					'html_cat_news_time_limit' => 0,
				),
			),
			'sort' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_SORT',
					'vars'	=> array(
						'legend1'	=> 'GYM_SORT',
						'html_sort' => array('lang' => 'GYM_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_cat_sort' => array('lang' => 'HTML_CAT_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_news_sort' => array('lang' => 'HTML_NEWS_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_cat_news_sort' => array('lang' => 'HTML_CAT_NEWS_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'html_sort' => 'DESC',
					'html_cat_sort' => 'DESC',
					'html_news_sort' => 'DESC',
					'html_cat_news_sort' => 'DESC',
				),
				'select' => array(
					'html_sort' => @$this->dyn_select['sort'],
					'html_cat_sort' => @$this->dyn_select['sort'],
					'html_news_sort' => @$this->dyn_select['sort'],
					'html_cat_news_sort' => @$this->dyn_select['sort'],
				),
			),
			'pagination' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_PAGINATION',
					'vars'	=> array(
						'legend1'	=> 'HTML_PAGINATION_GEN',
						'html_pagination' => array('lang' => 'HTML_PAGINATION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'html_pagination_limit' => array('lang' => 'HTML_PAGINATION_LIMIT', 'validate' => 'int:0:100', 'type' => 'text:4:4', 'explain' => true, ),
						'html_news_pagination' => array('lang' => 'HTML_NEWS_PAGINATION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'html_news_pagination_limit' => array('lang' => 'HTML_NEWS_PAGINATION_LIMIT', 'validate' => 'int:0:50', 'type' => 'text:4:4', 'explain' => true,),
						'legend2' => 'HTML_PAGINATION_SPEC',
						'html_item_pagination' => array('lang' => 'HTML_ITEM_PAGINATION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
					),
				),
				'default' => array(
					'html_pagination' => 1,
					'html_pagination_limit' => 25,
					'html_news_pagination' => 1,
					'html_news_pagination_limit' => 10,
					'html_item_pagination' => 0,
				),
			),
			'override' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_OVERRIDE',
					'vars'	=> array(
						'legend1'	=> 'GYM_OVERRIDE',
						'html_override'	=> array('lang' => 'GYM_OVERRIDE_MAIN', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_override_cache'	=> array('lang' => 'GYM_OVERRIDE_CACHE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_override_modrewrite'	=> array('lang' => 'GYM_OVERRIDE_MODREWRITE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_override_gzip'	=> array('lang' => 'GYM_OVERRIDE_GZIP','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_override_limit'	=> array('lang' => 'GYM_OVERRIDE_LIMIT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_override_sort'	=> array('lang' => 'GYM_OVERRIDE_SORT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_override_pagination'	=> array('lang' => 'GYM_OVERRIDE_PAGINATION','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
					),
				),
				'default' => array(
 					'html_override'	=> OVERRIDE_MODULE,
					'html_override_cache' => OVERRIDE_OTYPE,
					'html_override_modrewrite' => OVERRIDE_OTYPE,
					'html_override_gzip' => OVERRIDE_GLOBAL,
					'html_override_limit' => OVERRIDE_OTYPE,
					'html_override_sort' => OVERRIDE_MODULE,
					'html_override_pagination' => OVERRIDE_OTYPE,
				),
				'select' => array(
					'html_override' => $this->html_override,
					'html_override_cache' => $this->html_override,
					'html_override_modrewrite' => @$this->dyn_select['override'],
					'html_override_gzip' => @$this->dyn_select['override'],
					'html_override_limit' => $this->html_override,
					'html_override_sort' => @$this->dyn_select['override'],
					'html_override_pagination' => $this->html_override,
				),
			),
			'main' => array(
 				'display_vars' => array(
					'title'	=> 'HTML_MAIN',
					'vars'	=> array(
						// URL Settings
						'legend1'	=> 'HTML_URL',
						'html_url'	=> array('lang' => 'HTML_URL', 'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),
						// Link Settings
						'legend2'	=> 'HTML_LINKS_ACTIVATION',
						'html_link_main'	=> array('lang' => 'HTML_LINKS_MAIN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'html_link_index' => array('lang' => 'HTML_LINKS_INDEX', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'html_link_cat' => array('lang' => 'HTML_LINKS_CAT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						// Settings
						'legend3'	=> 'HTML_SETTINGS',
						'html_allow_map' => array('lang' => 'HTML_ALLOW_MAP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_allow_cat_map' => array('lang' => 'HTML_ALLOW_CAT_MAP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_sitename' => array('lang' => 'HTML_SITENAME', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true),
						'html_site_desc' => array('lang' => 'HTML_SITE_DESC', 'validate' => 'string', 'type' => 'textarea:6:50', 'explain' => true),
						'html_c_info' => array('lang' => 'HTML_C_INFO', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true),
						'html_logo_url' => array('lang' => 'HTML_LOGO_URL', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true),
						'html_stats_on_news' => array('lang' => 'HTML_STATS_ON_NEWS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_stats_on_map' => array('lang' => 'HTML_STATS_ON_MAP', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_birthdays_on_news' => array('lang' => 'HTML_BIRTHDAYS_ON_NEWS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_birthdays_on_map' => array('lang' => 'HTML_BIRTHDAYS_ON_MAP', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_disp_online' => array('lang' => 'HTML_DISP_ONLINE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
						'html_disp_tracking' => array('lang' => 'HTML_DISP_TRACKING', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
						'html_disp_status' => array('lang' => 'HTML_DISP_STATUS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
						'html_allow_profile' => array('lang' => 'HTML_ALLOW_PROFILE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_allow_profile_links' => array('lang' => 'HTML_ALLOW_PROFILE_LINKS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						// Auth settings
						'legend4'	=> 'HTML_AUTH_SETTINGS',
						'html_allow_auth' => array('lang' => 'HTML_ALLOW_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					'html_url' => $phpbb_seo->seo_path['phpbb_url'],
					'html_link_main' => 1,
					'html_link_index' => 1,
					'html_link_cat' => 1,
					'html_allow_map' => 1,
					'html_allow_cat_map' => 1,
 					'html_c_info' => $config['sitename'],
					'html_sitename' => $config['sitename'],
					'html_site_desc' => $config['site_desc'],
					'html_logo_url' => '',
					'html_disp_online' => 'globalmod',
					'html_disp_tracking' => 'reg',
					'html_disp_status' => 'reg',
					'html_allow_profile' => 'none',
					'html_allow_profile_links' => 'reg',
					'html_allow_auth' => 1,
					'html_stats_on_news' => 'all',
					'html_stats_on_map' => 'all',
					'html_birthdays_on_news' => 'reg',
					'html_birthdays_on_map' => 'reg',
				),
				'select' => array(
					'html_disp_online' => @$this->dyn_select['gym_auth'],
					'html_disp_tracking' => @$this->dyn_select['gym_auth'],
					'html_disp_status' => @$this->dyn_select['gym_auth'],
					'html_allow_profile' => @$this->dyn_select['gym_auth'],
					'html_allow_profile_links' => @$this->dyn_select['gym_auth'],
					'html_stats_on_news' => @$this->dyn_select['gym_auth'],
					'html_stats_on_map' => @$this->dyn_select['gym_auth'],
					'html_birthdays_on_news' => @$this->dyn_select['gym_auth'],
					'html_birthdays_on_map' => @$this->dyn_select['gym_auth'],
				),
			),
			'content' => array(
 				'display_vars' => array(
					'title'	=> 'HTML_NEWS',
					'vars'	=> array(
						// News
						'legend1'	=> 'HTML_NEWS',
						'html_allow_news' => array('lang' => 'HTML_ALLOW_NEWS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_allow_cat_news' => array('lang' => 'HTML_ALLOW_CAT_NEWS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_rss_news_url'	=> array('lang' => 'HTML_RSS_NEWS_URL', 'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),
						// News content
						'legend2'	=> 'HTML_NEWS_CONTENT',
						'html_allow_bbcode' => array('lang' => 'HTML_ALLOW_BBCODE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_strip_bbcode' => array('lang' => 'HTML_STRIP_BBCODE', 'validate' => 'string', 'type' => 'text:30:200', 'explain' => true),
						'html_allow_links' =>  array('lang' => 'HTML_ALLOW_LINKS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_allow_emails' => array('lang' => 'HTML_ALLOW_EMAILS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_allow_smilies' => array('lang' => 'HTML_ALLOW_SMILIES', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_allow_sig' => array('lang' => 'HTML_ALLOW_SIG', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'html_sumarize' => array('lang' => 'HTML_SUMARIZE', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true,),
						'html_sumarize_method' => array('lang' => 'HTML_SUMARIZE_METHOD', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
					),
				),
				'default' => array(
					// Content
					'html_allow_news' => 1,
					'html_allow_cat_news' => 1,
					'html_rss_news_url' => (!empty($this->gym_master->gym_config['rss_url']) ? $this->gym_master->gym_config['rss_url'] : $phpbb_seo->seo_path['phpbb_url']) . "gymrss.$phpEx?news&amp;digest",
					'html_allow_bbcode' => 'all',
					'html_strip_bbcode' => '',
					'html_allow_links' =>  'all',
					'html_allow_emails' => 'none',
					'html_allow_smilies' => 'all',
					'html_allow_sig' => 'reg',
					'html_sumarize' => 75,
					'html_sumarize_method' => 'words',
				),
				'select' => array(
					'html_sumarize_method' => @$this->dyn_select['sumarize_method'],
					'html_allow_bbcode' => @$this->dyn_select['gym_auth'],
					'html_allow_links' =>  @$this->dyn_select['gym_auth'],
					'html_allow_emails' => @$this->dyn_select['gym_auth'],
					'html_allow_smilies' => @$this->dyn_select['gym_auth'],
					'html_allow_sig' => @$this->dyn_select['gym_auth'],
				),
			),
			'info' => array(
				'title_lang' => 'GYM_HTML',
				'lang_file' => 'gym_html',
				'actions' => array('main', 'content', 'cache', 'modrewrite', 'gzip', 'limit', 'sort', 'pagination', 'override',),
				'mode' => 'rss',
				'module' => 'main',
			),
		);
	}
}
?>