<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: rss_main.php 134 2009-11-02 11:13:45Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* rss_main Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class rss_main {
	var $gym_master;
	var $dyn_select = array();
	var $lang_iso639 = array();
	var $rss_override = array();
	/**
	* constuctor
	*/
	function rss_main(&$gym_master) {
		global $user;
		$this->gym_master = &$gym_master;
		// Load the language iso 639-1 code - http://www.loc.gov/standards/iso639-2/php/French_list.php
		if ( !isset($user->lang['ISO_639_1'])) {
			$user->add_lang('gym_sitemaps/gym_iso639');
		}
		if (isset($this->gym_master->dyn_select) ) {
			$this->rss_override = $this->gym_master->dyn_select['override'];
			unset($this->rss_override[OVERRIDE_GLOBAL]);
			$this->dyn_select = $this->gym_master->dyn_select;
		}
	}
	/**
	* acp_module()
	* retunrs the acp config
	* @access private
	*/
	function acp_module() {
		global $config, $phpbb_seo, $user;
		$config['sitename'] = utf8_normalize_nfc($config['sitename']);
		$config['site_desc'] = utf8_normalize_nfc($config['site_desc']);
		return array(
			'cache' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_CACHE',
					'vars'	=> array(
						'legend1'	=> 'GYM_CACHE',
						'rss_cache_on'	=> array('lang' => 'GYM_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_cache_force_gzip' => array('lang' => 'GYM_CACHE_FORCE_GZIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_cache_max_age' => array('lang' => 'GYM_CACHE_MAX_AGE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_cache_auto_regen' => array('lang' => 'GYM_CACHE_AUTO_REGEN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'rss_showstats'	=> array('lang' => 'GYM_SHOWSTATS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_cache_on'	=> 0,
					'rss_cache_force_gzip' => 0,
					'rss_cache_max_age' => 6,
					'rss_cache_auto_regen'	=> 1,
					'rss_showstats'	=> 1,
				),
			),
			'modrewrite' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_MODREWRITE',
					'vars'	=> array(
						'legend1'	=> 'GYM_MODREWRITE',
						'rss_modrewrite'	=> array('lang' => 'GYM_MODREWRITE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_modrtype' => array('lang' => 'GYM_MODRTYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'rss_1xredir' => array('lang' => 'RSS_1XREDIR', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					'rss_modrewrite' => 0,
					'rss_modrtype' => 0,
					'rss_1xredir' => 0,
				),
				'select' => array(
					'rss_modrtype' => @$this->dyn_select['modrtype'],
				),
			),
			'gzip' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend4'	=> 'GYM_GZIP',
						'rss_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_gzip_ext' => array('lang' => 'GYM_GZIP_EXT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_gzip' => 0,
					'rss_gzip_ext' => 1,
				),
			),
			'limit' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'RSS_LIMIT_GEN',
						'rss_url_limit' => array('lang' => 'GYM_URL_LIMIT', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_sql_limit' => array('lang' => 'GYM_SQL_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_time_limit' => array('lang' => 'GYM_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'legend2'	=> 'RSS_LIMIT_SPEC',
						'rss_url_limit_long' => array('lang' => 'RSS_URL_LIMIT_LONG', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true,),
						'rss_sql_limit_long' => array('lang' => 'RSS_SQL_LIMIT_LONG', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true,),
						'rss_url_limit_short' => array('lang' => 'RSS_URL_LIMIT_SHORT', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true,),
						'rss_sql_limit_short' => array('lang' => 'RSS_SQL_LIMIT_SHORT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true,),
						'rss_url_limit_msg' => array('lang' => 'RSS_URL_LIMIT_MSG', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true,),
						'rss_sql_limit_msg' => array('lang' => 'RSS_SQL_LIMIT_MSG', 'validate' => 'int:0:500', 'type' => 'text:4:4', 'explain' => true,),
					),
				),
				'default' => array(
 					'rss_url_limit' => 50,
					'rss_sql_limit' => 20,
					'rss_time_limit' => 15,
					'rss_url_limit_long' => 100,
					'rss_sql_limit_long' => 25,
					'rss_url_limit_short' => 10,
					'rss_sql_limit_short' => 10,
					'rss_url_limit_msg' => 15,
					'rss_sql_limit_msg' => 15,
				),
			),
			'sort' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_SORT',
					'vars'	=> array(
						'legend1'	=> 'GYM_SORT',
						'rss_sort' => array('lang' => 'GYM_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_sort' => 'DESC',
				),
				'select' => array(
					'rss_sort' => @$this->dyn_select['sort'],
				),
			),
			'pagination' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_PAGINATION',
					'vars'	=> array(
						'legend1'	=> 'GYM_PAGINATION',
						'rss_pagination' => array('lang' => 'GYM_PAGINATION_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_limitdown' => array('lang' => 'GYM_LIMITDOWN', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_limitup' => array('lang' => 'GYM_LIMITUP', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_pagination' => 1,
					'rss_limitdown' => 3,
					'rss_limitup' => 5,
				),
			),
			'override' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_OVERRIDE',
					'vars'	=> array(
						'legend1'	=> 'GYM_OVERRIDE',
						'rss_override'	=> array('lang' => 'GYM_OVERRIDE_MAIN', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'rss_override_cache'	=> array('lang' => 'GYM_OVERRIDE_CACHE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'rss_override_modrewrite'	=> array('lang' => 'GYM_OVERRIDE_MODREWRITE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'rss_override_gzip'	=> array('lang' => 'GYM_OVERRIDE_GZIP','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'rss_override_limit'	=> array('lang' => 'GYM_OVERRIDE_LIMIT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'rss_override_sort'	=> array('lang' => 'GYM_OVERRIDE_SORT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'rss_override_pagination'	=> array('lang' => 'GYM_OVERRIDE_PAGINATION','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
					),
				),
				'default' => array(
 					'rss_override'	=> OVERRIDE_MODULE,
					'rss_override_cache' => OVERRIDE_OTYPE,
					'rss_override_modrewrite' => OVERRIDE_OTYPE,
					'rss_override_gzip' => OVERRIDE_GLOBAL,
					'rss_override_limit' => OVERRIDE_OTYPE,
					'rss_override_sort' => OVERRIDE_MODULE,
					'rss_override_pagination' => OVERRIDE_OTYPE,
				),
				'select' => array(
					'rss_override' => $this->rss_override,
					'rss_override_cache' => @$this->dyn_select['override'],
					'rss_override_modrewrite' => @$this->dyn_select['override'],
					'rss_override_gzip' => @$this->dyn_select['override'],
					'rss_override_limit' => @$this->dyn_select['override'],
					'rss_override_sort' => @$this->dyn_select['override'],
					'rss_override_pagination' => @$this->dyn_select['override'],
				),
			),
			'main' => array(
 				'display_vars' => array(
					'title'	=> 'RSS_MAIN',
					'vars'	=> array(
						// URL Settings
						'legend1'	=> 'RSS_URL',
						'rss_url'	=> array('lang' => 'RSS_URL', 'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),
						// Link Settings
						'legend2'	=> 'RSS_LINKS_ACTIVATION',
						'rss_link_main'	=> array('lang' => 'RSS_LINKS_MAIN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'rss_link_index' => array('lang' => 'RSS_LINKS_INDEX', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'rss_link_cat' => array('lang' => 'RSS_LINKS_CAT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'rss_alternate'	=> array('lang' => 'RSS_ALTERNATE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_linking_type'	=> array('lang' => 'RSS_LINKING_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
						// Settings
						'legend3'	=> 'RSS_SETTINGS',
						'rss_c_info' => array('lang' => 'RSS_C_INFO', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true),
						'rss_sitename' => array('lang' => 'RSS_SITENAME', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true),
						'rss_site_desc' => array('lang' => 'RSS_SITE_DESC', 'validate' => 'string', 'type' => 'textarea:6:50', 'explain' => true),
						'rss_logo_url' => array('lang' => 'RSS_LOGO_URL', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true),
						'rss_image_url' => array('lang' => 'RSS_IMAGE_URL', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true),
						'rss_lang' => array('lang' => 'RSS_LANG', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						// Auth settings
						'legend4'	=> 'RSS_AUTH_SETTINGS',
						'rss_allow_auth' => array('lang' => 'RSS_ALLOW_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_cache_auth' => array('lang' => 'RSS_CACHE_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						// Notifications
						'legend5'	=> 'RSS_NOTIFY',
						'rss_yahoo_notify' => array('lang' => 'RSS_YAHOO_NOTIFY', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_yahoo_appid' => array('lang' => 'RSS_YAHOO_APPID', 'validate' => 'string',	'type' => 'text:25:200', 'explain' => true),
						// Style
						'legend6'	=> 'RSS_STYLE',
						'rss_xslt'	=> array('lang' => 'RSS_XSLT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_force_xslt' => array('lang' => 'RSS_FORCE_XSLT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_load_phpbb_css' => array('lang' => 'RSS_LOAD_PHPBB_CSS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					'rss_url' => $phpbb_seo->seo_path['phpbb_url'],
					'rss_link_main' => 1,
					'rss_link_index' => 1,
					'rss_link_cat' => 1,
					'rss_alternate' => 1,
					'rss_linking_type' => 'n',
 					'rss_c_info' => $config['sitename'],
					'rss_sitename' => $config['sitename'],
					'rss_site_desc' => $config['site_desc'],
					'rss_logo_url' => 'logo.gif',
					'rss_image_url' => 'rss_forum_big.gif',
					'rss_lang' => $config['default_lang'],
					'rss_allow_auth' => 0,
					'rss_cache_auth' => 1,
					// Notifications
					'rss_yahoo_notify' => 0,
					'rss_yahoo_appid' => '',
					// Style
					'rss_xslt' => 1,
					'rss_force_xslt' => 1,
					'rss_load_phpbb_css' => 0,
				),
				'select' => array(
					'rss_lang' => $user->lang['ISO_639_1'],
					'rss_linking_type' => @$this->dyn_select['rss_linking_types'],

				),
			),
			'content' => array(
 				'display_vars' => array(
					'title'	=> 'RSS_CONTENT',
					'vars'	=> array(
						// Content
						'legend1'	=> 'RSS_CONTENT',
						'rss_allow_news' => array('lang' => 'RSS_ALLOW_NEWS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_news_update' => array('lang' => 'RSS_NEWS_UPDATE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true),
						'rss_allow_short' => array('lang' => 'RSS_ALLOW_SHORT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_allow_long' => array('lang' => 'RSS_ALLOW_LONG', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_allow_content' => array('lang' => 'RSS_ALLOW_CONTENT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_allow_profile' => array('lang' => 'RSS_ALLOW_PROFILE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_allow_profile_links' => array('lang' => 'RSS_ALLOW_PROFILE_LINKS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_allow_bbcode' => array('lang' => 'RSS_ALLOW_BBCODE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_strip_bbcode' => array('lang' => 'RSS_STRIP_BBCODE', 'validate' => 'string', 'type' => 'text:30:200', 'explain' => true),
						'rss_allow_links' =>  array('lang' => 'RSS_ALLOW_LINKS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_allow_emails' => array('lang' => 'RSS_ALLOW_EMAILS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_allow_smilies' => array('lang' => 'RSS_ALLOW_SMILIES', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_sumarize' => array('lang' => 'RSS_SUMARIZE', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true,),
						'rss_sumarize_method' => array('lang' => 'RSS_SUMARIZE_METHOD', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'rss_nohtml' => array('lang' => 'RSS_NOHTML', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					// Content
					'rss_allow_news' => 1,
					'rss_news_update' => '',
					'rss_allow_short' => 1,
					'rss_allow_long' => 1,
					'rss_allow_content' => 1,
					'rss_allow_profile' => 1,
					'rss_allow_profile_links' => 0,
					'rss_allow_bbcode' => 1,
					'rss_strip_bbcode' => '',
					'rss_allow_links' =>  1,
					'rss_allow_emails' => 0,
					'rss_allow_smilies' => 1,
					'rss_sumarize' => 50,
					'rss_sumarize_method' => 'words',
					'rss_nohtml' => 0,
				),
				'select' => array(
					'rss_sumarize_method' => @$this->dyn_select['sumarize_method'],
				),
			),
			'info' => array(
				'title_lang' => 'GYM_RSS',
				'lang_file' => 'gym_rss',
				'actions' => array('main', 'content', 'cache', 'modrewrite', 'gzip', 'limit', 'sort', 'pagination', 'override',),
				'mode' => 'rss',
				'module' => 'main',
			),
		);
	}
}
?>