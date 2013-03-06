<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: rss_forum.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* rss_forum Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class rss_forum {
	var $gym_master;
	var $dyn_select = array();
	/**
	* constuctor
	*/
	function rss_forum(&$gym_master) {
		global $user;
		$this->gym_master = &$gym_master;
		if (isset($this->gym_master->dyn_select) ) {
			$this->dyn_select = & $this->gym_master->dyn_select;
			$this->gym_master->forum_select();
		}
		// Load the language iso 639-1 code - http://www.loc.gov/standards/iso639-2/php/French_list.php
		if ( !isset($user->lang['ISO_639_1'])) {
			$user->add_lang('gym_sitemaps/gym_iso639');
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
						'rss_forum_cache_on'	=> array('lang' => 'GYM_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_cache_force_gzip' => array('lang' => 'GYM_CACHE_FORCE_GZIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_cache_max_age' => array('lang' => 'GYM_CACHE_MAX_AGE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_showstats'	=> array('lang' => 'GYM_SHOWSTATS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_forum_cache_on'	=> 0,
					'rss_forum_cache_force_gzip' => 0,
					'rss_forum_cache_max_age' => 6,
					'rss_forum_showstats'	=> 1,
				),
			),
			'modrewrite' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_MODREWRITE',
					'vars'	=> array(
						'legend1'	=> 'GYM_MODREWRITE',
						'rss_forum_modrewrite'	=> array('lang' => 'GYM_MODREWRITE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_modrtype' => array('lang' => 'GYM_MODRTYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'rss_forum_modrewrite' => 0,
					'rss_forum_modrtype' => 0,
				),
				'select' => array(
					'rss_forum_modrtype' => @$this->dyn_select['modrtype'],
				),
			),
			'gzip' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend4'	=> 'GYM_GZIP',
						'rss_forum_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_gzip_ext' => array('lang' => 'GYM_GZIP_EXT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_forum_gzip' => 0,
					'rss_forum_gzip_ext' => 1,
				),
			),
			'limit' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'RSS_LIMIT_GEN',
						'rss_forum_url_limit' => array('lang' => 'GYM_URL_LIMIT', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_sql_limit' => array('lang' => 'GYM_SQL_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_time_limit' => array('lang' => 'GYM_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'legend2'	=> 'RSS_LIMIT_SPEC',
						'rss_forum_url_limit_long' => array('lang' => 'RSS_URL_LIMIT_LONG', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_sql_limit_long' => array('lang' => 'RSS_SQL_LIMIT_LONG', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_url_limit_short' => array('lang' => 'RSS_URL_LIMIT_SHORT', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_sql_limit_short' => array('lang' => 'RSS_SQL_LIMIT_SHORT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_url_limit_msg' => array('lang' => 'RSS_URL_LIMIT_MSG', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_sql_limit_msg' => array('lang' => 'RSS_SQL_LIMIT_MSG', 'validate' => 'int:0:500', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_forum_url_limit' => 50,
					'rss_forum_sql_limit' => 20,
					'rss_forum_time_limit' => 15,
					'rss_forum_url_limit_long' => 100,
					'rss_forum_sql_limit_long' => 25,
					'rss_forum_url_limit_short' => 10,
					'rss_forum_sql_limit_short' => 10,
					'rss_forum_url_limit_msg' => 15,
					'rss_forum_sql_limit_msg' => 15,
				),
			),
			'sort' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_SORT',
					'vars'	=> array(
						'legend1'	=> 'GYM_SORT',
						'rss_forum_sort' => array('lang' => 'GYM_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_forum_sort' => 'DESC',
				),
				'select' => array(
					'rss_forum_sort' => @$this->dyn_select['sort'],
				),
			),
			'pagination' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_PAGINATION',
					'vars'	=> array(
						'legend1'	=> 'GYM_PAGINATION',
						'rss_forum_pagination' => array('lang' => 'GYM_PAGINATION_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_limitdown' => array('lang' => 'GYM_LIMITDOWN', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_limitup' => array('lang' => 'GYM_LIMITUP', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'rss_forum_pagination' => 1,
					'rss_forum_limitdown' => 3,
					'rss_forum_limitup' => 5,
				),
			),
			'main' => array(
 				'display_vars' => array(
					'title'	=> 'RSS_MAIN',
					'vars'	=> array(
						// Settings
						'legend1'	=> 'RSS_SETTINGS',
						'rss_forum_c_info' => array('lang' => 'RSS_C_INFO', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true, 'overriding' => true),
						'rss_forum_sitename' => array('lang' => 'RSS_SITENAME', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true, 'overriding' => true),
						'rss_forum_site_desc' => array('lang' => 'RSS_SITE_DESC', 'validate' => 'string', 'type' => 'textarea:6:50', 'explain' => true, 'overriding' => true),
						'rss_forum_logo_url' => array('lang' => 'RSS_LOGO_URL', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true, 'overriding' => true),
						'rss_forum_image_url' => array('lang' => 'RSS_IMAGE_URL', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true, 'overriding' => true),
						'rss_forum_lang' => array('lang' => 'RSS_LANG', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'rss_forum_alternate'	=> array('lang' => 'RSS_FORUM_ALTERNATE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						// Auth settings
						'legend2'	=> 'RSS_AUTH_SETTINGS',
						'rss_forum_allow_auth' => array('lang' => 'RSS_ALLOW_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_cache_auth' => array('lang' => 'RSS_CACHE_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						// Notifications
						'legend3'	=> 'RSS_NOTIFY',
						'rss_forum_yahoo_notify' => array('lang' => 'RSS_YAHOO_NOTIFY', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						// Exclusions
						'legend4' => 'RSS_FORUM_EXCLUDE',
						'rss_forum_exclude' => array('lang' => 'RSS_FORUM_EXCLUDE', 'multiple_validate' => 'int', 'type' => 'custom', 'method' => 'select_multiple_string', 'explain' => true),
					),
				),
				'default' => array(
 					'rss_forum_c_info' => $config['sitename'],
					'rss_forum_sitename' => $config['sitename'],
					'rss_forum_site_desc' => $config['site_desc'],
					'rss_forum_logo_url' => 'logo.gif',
					'rss_forum_image_url' => 'rss_forum_big.gif',
					'rss_forum_lang' => $config['default_lang'],
					'rss_forum_alternate' => 1,
					'rss_forum_allow_auth' => 0,
					'rss_forum_cache_auth' => 1,
					// Exclusions
					'rss_forum_exclude' => '',
					// Notifications
					'rss_forum_yahoo_notify' => 0,
				),
				'select' => array(
					'rss_forum_lang' => $user->lang['ISO_639_1'],
					'rss_forum_exclude' => @$this->dyn_select['forums'],

				),
			),
			'content' => array(
 				'display_vars' => array(
					'title'	=> 'RSS_CONTENT',
					'vars'	=> array(
						// Content
						'legend1'	=> 'RSS_CONTENT',
						'rss_forum_allow_news' => array('lang' => 'RSS_ALLOW_NEWS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_short' => array('lang' => 'RSS_ALLOW_SHORT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_long' => array('lang' => 'RSS_ALLOW_LONG', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_content' => array('lang' => 'RSS_ALLOW_CONTENT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_profile' => array('lang' => 'RSS_ALLOW_PROFILE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_profile_links' => array('lang' => 'RSS_ALLOW_PROFILE_LINKS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_bbcode' => array('lang' => 'RSS_ALLOW_BBCODE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_strip_bbcode' => array('lang' => 'RSS_STRIP_BBCODE', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_links' =>  array('lang' => 'RSS_ALLOW_LINKS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_emails' => array('lang' => 'RSS_ALLOW_EMAILS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_allow_smilies' => array('lang' => 'RSS_ALLOW_SMILIES', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'rss_forum_sumarize' => array('lang' => 'RSS_SUMARIZE', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'rss_forum_sumarize_method' => array('lang' => 'RSS_SUMARIZE_METHOD', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'rss_forum_nohtml' => array('lang' => 'RSS_NOHTML', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						// form specific
						'legend2'	=> 'RSS_FORUM_CONTENT',
						'rss_forum_first' => array('lang' => 'RSS_FORUM_FIRST', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_forum_last' => array('lang' => 'RSS_FORUM_LAST', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'rss_forum_rules' => array('lang' => 'RSS_FORUM_RULES', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					// Content
					'rss_forum_allow_news' => 1,
					'rss_forum_allow_short' => 1,
					'rss_forum_allow_long' => 1,
					'rss_forum_allow_content' => 1,
					'rss_forum_allow_profile' => 1,
					'rss_forum_allow_profile_links' => 0,
					'rss_forum_allow_bbcode' => 1,
					'rss_forum_strip_bbcode' => '',
					'rss_forum_allow_links' =>  1,
					'rss_forum_allow_emails' => 0,
					'rss_forum_allow_smilies' => 1,
					'rss_forum_sumarize' => 25,
					'rss_forum_sumarize_method' => 'words',
					'rss_forum_nohtml' => 0,
					'rss_forum_first' =>  0,
					'rss_forum_last' => 1,
					'rss_forum_rules' => 0,
				),
				'select' => array(
					'rss_forum_sumarize_method' => @$this->dyn_select['sumarize_method'],
				),
			),
			'info' => array(
				'title_lang' => 'RSS_FORUM',
				'lang_file' => 'rss_forum',
				'actions' => array( 'main', 'content', 'cache', 'modrewrite', 'gzip', 'limit', 'sort', 'pagination',),
				'mode' => 'rss',
				'module' => 'forum'
			),
		);
	}
}
?>