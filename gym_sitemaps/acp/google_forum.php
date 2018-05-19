<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_forum.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* google_forum Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class google_forum {
	var $gym_master;
	var $dyn_select = array();
	/**
	* constuctor
	*/
	function google_forum(&$gym_master) {
		$this->gym_master = &$gym_master;
		if (isset($this->gym_master->dyn_select) ) {
			$this->dyn_select = & $this->gym_master->dyn_select;
			$this->gym_master->forum_select();
		}
	}
	/**
	* acp_module()
	* retunrs the acp config, display vars + default values and select options
	* @access private
	*/
	function acp_module() {
		return array(
			'cache' => array(
				'display_vars' => array(
					'title'	=> 'GYM_CACHE',
					'vars'	=> array(
						'legend1'	=> 'GYM_CACHE',
						'google_forum_cache_on'	=> array('lang' => 'GYM_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_forum_cache_force_gzip' => array('lang' => 'GYM_CACHE_FORCE_GZIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_forum_cache_max_age' => array('lang' => 'GYM_CACHE_MAX_AGE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'google_forum_showstats'	=> array('lang' => 'GYM_SHOWSTATS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_forum_cache_on' => 0,
					'google_forum_cache_force_gzip' => 0,
					'google_forum_cache_max_age' => 24,
					'google_forum_showstats' => 1,
				),
			),
			'modrewrite' => array(
				'display_vars' => array(
					'title'	=> 'GYM_MODREWRITE',
					'vars'	=> array(
						'legend1'	=> 'GYM_MODREWRITE',
						'google_forum_modrewrite'	=> array('lang' => 'GYM_MODREWRITE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_forum_modrtype' => array('lang' => 'GYM_MODRTYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_forum_modrewrite' => 0,
					'google_forum_modrtype' => 0,
				),
				'select' => array(
					'google_forum_modrtype' => @$this->dyn_select['modrtype'],
				),
			),
			'gzip' => array(
				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend1'	=> 'GYM_GZIP',
						'google_forum_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_forum_gzip_ext' => array('lang' => 'GYM_GZIP_EXT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_forum_gzip' => 0,
					'google_forum_gzip_ext' => 1,
				),
			),
			'limit' => array(
				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'GYM_LIMIT',
						'google_forum_url_limit' => array('lang' => 'GYM_URL_LIMIT', 'validate' => 'int:0:50000', 'type' => 'text:6:6', 'explain' => true, 'overriding' => true),
						'google_forum_sql_limit' => array('lang' => 'GYM_SQL_LIMIT', 'validate' => 'int:0:2500', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_forum_url_limit' => 2500,
					'google_forum_sql_limit' => 150,
				),
			),
			'sort' => array(
				'display_vars' => array(
					'title'	=> 'GYM_SORT',
					'vars'	=> array(
						'legend1'	=> 'GYM_SORT',
						'google_forum_sort' => array('lang' => 'GYM_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_forum_sort' => 'DESC',
				),
				'select' => array(
					'google_forum_sort' => @$this->dyn_select['sort'],
				),
			),
			'pagination' => array(
				'display_vars' => array(
					'title'	=> 'GYM_PAGINATION',
					'vars'	=> array(
						'legend1'	=> 'GYM_PAGINATION',
						'google_forum_pagination' => array('lang' => 'GYM_PAGINATION_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_forum_limitdown' => array('lang' => 'GYM_LIMITDOWN', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'google_forum_limitup' => array('lang' => 'GYM_LIMITUP', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_forum_pagination' => 1,
					'google_forum_limitdown' => 3,
					'google_forum_limitup' => 5,
				),
			),
			'main' => array(
				'display_vars' => array(
					'title'	=> 'GOOGLE_FORUM_SETTINGS',
					'vars'	=> array(
						'legend1' => 'GOOGLE_PRIORITIES',
						'google_forum_sticky_priority' => array('lang' => 'GOOGLE_FORUM_STICKY_PRIORITY', 'type' => 'custom', 'validate' => 'string', 'method' => 'validate_num', 'params' => array('{CONFIG_VALUE}', '{KEY}', 2, 0, 1),  'explain' => true,),
						'google_forum_announce_priority' => array('lang' => 'GOOGLE_FORUM_ANNOUCE_PRIORITY', 'type' => 'custom', 'validate' => 'string', 'method' => 'validate_num', 'params' => array('{CONFIG_VALUE}', '{KEY}', 2, 0, 1),  'explain' => true,),
						'google_forum_global_priority' => array('lang' => 'GOOGLE_FORUM_GLOBAL_PRIORITY', 'type' => 'custom', 'validate' => 'string', 'method' => 'validate_num', 'params' => array('{CONFIG_VALUE}', '{KEY}', 2, 0, 1),  'explain' => true,),
						'google_forum_default_priority' => array('lang' => 'GOOGLE_DEFAULT_PRIORITY', 'type' => 'custom', 'validate' => 'string', 'method' => 'validate_num', 'params' => array('{CONFIG_VALUE}', '{KEY}', 2, 0, 1),  'explain' => true, 'overriding' => true),
						'legend2' => 'GOOGLE_FORUM_EXCLUDE',
						'google_forum_exclude' => array('lang' => 'GOOGLE_FORUM_EXCLUDE', 'multiple_validate' => 'int', 'type' => 'custom', 'method' => 'select_multiple_string', 'explain' => true),
						'legend3' => 'GOOGLE_PING',
						'google_forum_ping' => array('lang' => 'GOOGLE_PING', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_forum_sticky_priority' => 0.75,
					'google_forum_announce_priority' => 0.75,
					'google_forum_global_priority' => 0.75,
					'google_forum_default_priority' => 1.0,
					'google_forum_exclude' => '',
					'google_forum_ping' => 0,
				),
				'select' => array(
					'google_forum_exclude' => @$this->dyn_select['forums'],
				),
			),
			'info' => array(
				'title_lang' => 'GOOGLE_FORUM',
				'lang_file' => 'google_forum',
				'actions' => array( 'main', 'cache', 'modrewrite', 'gzip', 'limit', 'sort', 'pagination',),
				'mode' => 'google',
				'module' => 'forum'
			),
		);
	}
}
?>