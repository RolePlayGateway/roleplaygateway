<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: html_forum.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* html_forum Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class html_forum {
	var $gym_master;
	var $dyn_select = array();
	/**
	* constuctor
	*/
	function html_forum(&$gym_master) {
		global $user;
		$this->gym_master = &$gym_master;
		if (isset($this->gym_master->dyn_select) ) {
			$this->dyn_select = &$this->gym_master->dyn_select;
			$this->gym_master->forum_select();
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
					'title'	=> 'HTML_CACHE',
					'vars'	=> array(
						'legend1'	=> 'HTML_CACHE',
						'html_forum_main_cache_on'	=> array('lang' => 'HTML_MAIN_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_main_cache_ttl' => array('lang' => 'HTML_MAIN_CACHE_TTL', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'html_forum_opt_cache_on'	=> array('lang' => 'HTML_OPT_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_opt_cache_ttl' => array('lang' => 'HTML_OPT_CACHE_TTL', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'html_forum_main_cache_on' => 0,
					'html_forum_opt_cache_on' => 0,
					'html_forum_main_cache_ttl' => 6,
					'html_forum_opt_cache_ttl' => 6,
				),
			),
			'modrewrite' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_MODREWRITE',
					'vars'	=> array(
						'legend1'	=> 'GYM_MODREWRITE',
						'html_forum_modrewrite'	=> array('lang' => 'GYM_MODREWRITE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_modrtype' => array('lang' => 'GYM_MODRTYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'html_forum_modrewrite' => 0,
					'html_forum_modrtype' => 0,
				),
				'select' => array(
					'html_forum_modrtype' => @$this->dyn_select['modrtype'],
				),
			),
			'gzip' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend4'	=> 'GYM_GZIP',
						'html_forum_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
 					'html_forum_gzip' => 0,
				),
			),
			'limit' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'GYM_TIME_LIMIT',
						'html_forum_map_time_limit' => array('lang' => 'HTML_MAP_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'html_forum_cat_time_limit' => array('lang' => 'HTML_CAT_MAP_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'html_forum_news_time_limit' => array('lang' => 'HTML_NEWS_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'html_forum_cat_news_time_limit' => array('lang' => 'HTML_CAT_NEWS_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'html_forum_map_time_limit' => 0,
					'html_forum_cat_time_limit' => 0,
					'html_forum_news_time_limit' => 0,
					'html_forum_cat_news_time_limit' => 0,
				),
			),
			'sort' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_SORT',
					'vars'	=> array(
						'legend1'	=> 'HTML_MAP_SORT_TITLE',
						'html_forum_first' => array('lang' => 'HTML_FORUM_FIRST', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_forum_sort' => array('lang' => 'GYM_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_cat_sort' => array('lang' => 'HTML_CAT_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'legend2'	=> 'HTML_NEWS_SORT_TITLE',
						'html_forum_news_first' => array('lang' => 'HTML_FORUM_NEWS_FIRST', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'html_forum_news_sort' => array('lang' => 'HTML_NEWS_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_cat_news_sort' => array('lang' => 'HTML_CAT_NEWS_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'html_forum_sort' => 'DESC',
					'html_forum_cat_sort' => 'DESC',
					'html_forum_news_sort' => 'DESC',
					'html_forum_cat_news_sort' => 'DESC',
					'html_forum_first' => 0,
					'html_forum_news_first' => 1,
				),
				'select' => array(
					'html_forum_sort' => @$this->dyn_select['sort'],
					'html_forum_cat_sort' => @$this->dyn_select['sort'],
					'html_forum_news_sort' => @$this->dyn_select['sort'],
					'html_forum_cat_news_sort' => @$this->dyn_select['sort'],
				),
			),
			'pagination' => array(
 				'display_vars' => array(
					'title'	=> 'GYM_PAGINATION',
					'vars'	=> array(
						'legend1'	=> 'HTML_PAGINATION_GEN',
						'html_forum_pagination' => array('lang' => 'HTML_FORUM_PAGINATION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_pagination_limit' => array('lang' => 'HTML_FORUM_PAGINATION_LIMIT', 'validate' => 'int:0:100', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'html_forum_news_pagination' => array('lang' => 'HTML_NEWS_PAGINATION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_news_pagination_limit' => array('lang' => 'HTML_NEWS_PAGINATION_LIMIT', 'validate' => 'int:0:50', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'legend2'	=> 'HTML_PAGINATION_SPEC',
						'html_forum_item_pagination' => array('lang' => 'HTML_ITEM_PAGINATION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_ltopic_pagination' => array('lang' => 'HTML_FORUM_LTOPIC_PAGINATION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
					),
				),
				'default' => array(
					'html_forum_pagination' => 1,
					'html_forum_pagination_limit' => 50,
					'html_forum_news_pagination' => 1,
					'html_forum_news_pagination_limit' => 10,
					'html_forum_item_pagination' => 0,
					'html_forum_ltopic_pagination' => 0,
				),
			),
			'main' => array(
 				'display_vars' => array(
					'title'	=> 'HTML_MAIN',
					'vars'	=> array(
						// Settings
						'legend1'	=> 'HTML_SETTINGS',
						'html_forum_allow_map' => array('lang' => 'HTML_ALLOW_MAP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_allow_cat_map' => array('lang' => 'HTML_ALLOW_CAT_MAP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_sitename' => array('lang' => 'HTML_SITENAME', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true, 'overriding' => true),
						'html_forum_site_desc' => array('lang' => 'HTML_SITE_DESC', 'validate' => 'string', 'type' => 'textarea:6:50', 'explain' => true, 'overriding' => true),
						'html_forum_c_info' => array('lang' => 'HTML_C_INFO', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true, 'overriding' => true),
						'html_forum_logo_url' => array('lang' => 'HTML_LOGO_URL', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true, 'overriding' => true),
						'html_forum_stats_on_news' => array('lang' => 'HTML_STATS_ON_NEWS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_stats_on_map' => array('lang' => 'HTML_STATS_ON_MAP', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_birthdays_on_news' => array('lang' => 'HTML_BIRTHDAYS_ON_NEWS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_birthdays_on_map' => array('lang' => 'HTML_BIRTHDAYS_ON_MAP', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_disp_online' => array('lang' => 'HTML_DISP_ONLINE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_disp_tracking' => array('lang' => 'HTML_DISP_TRACKING', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_disp_status' => array('lang' => 'HTML_DISP_STATUS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_allow_profile' => array('lang' => 'HTML_ALLOW_PROFILE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_allow_profile_links' => array('lang' => 'HTML_ALLOW_PROFILE_LINKS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_exclude' => array('lang' => 'HTML_FORUM_EXCLUDE', 'multiple_validate' => 'int', 'type' => 'custom', 'method' => 'select_multiple_string', 'explain' => true),
						// Auth settings
						'legend2'	=> 'HTML_AUTH_SETTINGS',
						'html_forum_allow_auth' => array('lang' => 'HTML_ALLOW_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						// Last topic list
						'legend3'	=> 'HTML_FORUM_LTOPIC',
						'html_forum_ltopic' => array('lang' => 'HTML_FORUM_INDEX_LTOPIC', 'validate' => 'int:0:100', 'type' => 'text:4:4', 'explain' => true),
						'html_forum_cat_ltopic' => array('lang' => 'HTML_FORUM_CAT_LTOPIC', 'validate' => 'int:0:100', 'type' => 'text:4:4', 'explain' => true),
						'html_forum_news_ltopic' => array('lang' => 'HTML_FORUM_NEWS_LTOPIC', 'validate' => 'int:0:100', 'type' => 'text:4:4', 'explain' => true),
						'html_forum_cat_news_ltopic' => array('lang' => 'HTML_FORUM_CAT_NEWS_LTOPIC', 'validate' => 'int:0:100', 'type' => 'text:4:4', 'explain' => true),
						'html_forum_ltopic_exclude' => array('lang' => 'HTML_FORUM_LTOPIC_EXCLUDE', 'multiple_validate' => 'int', 'type' => 'custom', 'method' => 'select_multiple_string', 'explain' => true),
						// form specific
						'legend4'	=> 'HTML_FORUM_CONTENT',
						'html_forum_last_post' => array('lang' => 'HTML_FORUM_LAST_POST', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
						'html_forum_rules' => array('lang' => 'HTML_FORUM_RULES', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
						'html_forum_desc' => array('lang' => 'HTML_FORUM_DESC', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
					),
				),
				'default' => array(
					'html_forum_allow_map' => 1,
					'html_forum_allow_cat_map' => 1,
 					'html_forum_c_info' => $config['sitename'],
					'html_forum_sitename' => $config['sitename'],
					'html_forum_site_desc' => $config['site_desc'],
					'html_forum_logo_url' => '',
					'html_forum_url' => $phpbb_seo->seo_path['phpbb_url'],
					'html_forum_disp_online' => 'globalmod',
					'html_forum_disp_tracking' => 'reg',
					'html_forum_disp_status' => 'reg',
					'html_forum_allow_auth' => 1,
					'html_forum_ltopic' => 15,
					'html_forum_cat_ltopic' => 15,
					'html_forum_news_ltopic' => 10,
					'html_forum_cat_news_ltopic' => 10,
					'html_forum_allow_profile' => 'none',
					'html_forum_allow_profile_links' => 'reg',
					'html_forum_last_post' => 'reg',
					'html_forum_rules' => 'all',
					'html_forum_desc' => 'all',
					'html_forum_stats_on_news' => 'all',
					'html_forum_stats_on_map' => 'all',
					'html_forum_birthdays_on_news' => 'reg',
					'html_forum_birthdays_on_map' => 'reg',
					// Exclusions
					'html_forum_exclude' => '',
					'html_forum_ltopic_exclude' => '',
				),
				'select' => array(
					'html_forum_exclude' => @$this->dyn_select['forums'],
					'html_forum_disp_online' => @$this->dyn_select['gym_auth'],
					'html_forum_disp_tracking' => @$this->dyn_select['gym_auth'],
					'html_forum_disp_status' => @$this->dyn_select['gym_auth'],
					'html_forum_ltopic_exclude' => @$this->dyn_select['forums'],
					'html_forum_allow_profile' => @$this->dyn_select['gym_auth'],
					'html_forum_allow_profile_links' => @$this->dyn_select['gym_auth'],
					'html_forum_last_post' => @$this->dyn_select['gym_auth'],
					'html_forum_rules' => @$this->dyn_select['gym_auth'],
					'html_forum_desc' => @$this->dyn_select['gym_auth'],
					'html_forum_stats_on_news' => @$this->dyn_select['gym_auth'],
					'html_forum_stats_on_map' => @$this->dyn_select['gym_auth'],
					'html_forum_birthdays_on_news' => @$this->dyn_select['gym_auth'],
					'html_forum_birthdays_on_map' => @$this->dyn_select['gym_auth'],
				),
			),
			'content' => array(
 				'display_vars' => array(
					'title'	=> 'HTML_NEWS',
					'vars'	=> array(
						// news
						'legend1'	=> 'HTML_NEWS',
						'html_forum_allow_news' => array('lang' => 'HTML_FORUM_ALLOW_NEWS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_allow_cat_news' => array('lang' => 'HTML_FORUM_ALLOW_CAT_NEWS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'html_forum_news_ids' => array('lang' => 'HTML_FORUM_NEWS_IDS', 'multiple_validate' => 'int', 'type' => 'custom', 'method' => 'select_multiple_string', 'explain' => true),
						// News content
						'legend2'	=> 'HTML_NEWS_CONTENT',
						'html_forum_post_buttons' => array('lang' => 'HTML_FORUM_POST_BUTTONS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true),
						'html_forum_allow_bbcode' => array('lang' => 'HTML_ALLOW_BBCODE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_strip_bbcode' => array('lang' => 'HTML_STRIP_BBCODE', 'validate' => 'string', 'type' => 'text:25:200', 'explain' => true, 'overriding' => true),
						'html_forum_allow_links' =>  array('lang' => 'HTML_ALLOW_LINKS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_allow_emails' => array('lang' => 'HTML_ALLOW_EMAILS', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_allow_smilies' => array('lang' => 'HTML_ALLOW_SMILIES', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_allow_sig' => array('lang' => 'HTML_ALLOW_SIG', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
						'html_forum_sumarize' => array('lang' => 'HTML_SUMARIZE', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'html_forum_sumarize_method' => array('lang' => 'HTML_SUMARIZE_METHOD', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					// Content
					'html_forum_allow_news' => 1,
					'html_forum_news_ids' => '',
					'html_forum_allow_cat_news' => 1,
					'html_forum_post_buttons' => 'none',
					'html_forum_allow_bbcode' => 'all',
					'html_forum_strip_bbcode' => '',
					'html_forum_allow_links' =>  'all',
					'html_forum_allow_emails' => 'none',
					'html_forum_allow_smilies' => 'none',
					'html_forum_allow_sig'  => 'none',
					'html_forum_sumarize' => 75,
					'html_forum_sumarize_method' => 'words',
				),
				'select' => array(
					'html_forum_sumarize_method' => @$this->dyn_select['sumarize_method'],
					'html_forum_post_buttons' => @$this->dyn_select['gym_auth'],
					'html_forum_allow_bbcode' => @$this->dyn_select['gym_auth'],
					'html_forum_allow_links' =>  @$this->dyn_select['gym_auth'],
					'html_forum_allow_emails' => @$this->dyn_select['gym_auth'],
					'html_forum_allow_smilies' => @$this->dyn_select['gym_auth'],
					'html_forum_allow_sig' => @$this->dyn_select['gym_auth'],
					'html_forum_news_ids' => @$this->dyn_select['forums'],
				),
			),
			'info' => array(
				'title_lang' => 'HTML_FORUM',
				'lang_file' => 'html_forum',
				'actions' => array( 'main', 'content', 'cache', 'modrewrite', 'gzip', 'limit', 'sort', 'pagination',),
				'mode' => 'rss',
				'module' => 'forum'
			),
		);
	}
}
?>