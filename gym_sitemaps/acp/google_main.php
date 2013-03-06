<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_main.php 134 2009-11-02 11:13:45Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* google_main Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class google_main {
	var $gym_master;
	var $dyn_select = array();
	var $google_override = array();
	/**
	* constuctor
	*/
	function google_main(&$gym_master) {
		$this->gym_master = &$gym_master;
		if (isset($this->gym_master->dyn_select) ) {
			$this->google_override = $this->gym_master->dyn_select['override'];
			unset($this->google_override[OVERRIDE_GLOBAL]);
			$this->dyn_select = $this->gym_master->dyn_select;
		}
	}
	/**
	* acp_module()
	* retunrs the acp config
	* @access private
	*/
	function acp_module() {
		global $phpbb_seo;
		return array(
			'cache' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_CACHE',
					'vars'	=> array(
						'legend1'	=> 'GYM_CACHE',
						'google_cache_on'	=> array('lang' => 'GYM_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_cache_force_gzip' => array('lang' => 'GYM_CACHE_FORCE_GZIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_cache_max_age' => array('lang' => 'GYM_CACHE_MAX_AGE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'google_cache_auto_regen' => array('lang' => 'GYM_CACHE_AUTO_REGEN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_showstats'	=> array('lang' => 'GYM_SHOWSTATS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_cache_on' => 0,
					'google_cache_force_gzip' => 0,
					'google_cache_max_age' => 24,
					'google_cache_auto_regen' => 1,
					'google_showstats' => 1,
				),
			),
			'modrewrite' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_MODREWRITE',
					'vars'	=> array(
						'legend1'	=> 'GYM_MODREWRITE',
						'google_modrewrite'	=> array('lang' => 'GYM_MODREWRITE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_modrtype' => array('lang' => 'GYM_MODRTYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_modrewrite' => 0,
					'google_modrtype' => 0,
				),
				'select' => array(
					'google_modrtype' => @$this->dyn_select['modrtype'],
				),
			),
			'gzip' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend1'	=> 'GYM_GZIP',
						'google_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_gzip_ext' => array('lang' => 'GYM_GZIP_EXT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_gzip' => 0,
					'google_gzip_ext' => 1,
				),
			),
			'limit' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'GYM_LIMIT',
						'google_url_limit' => array('lang' => 'GYM_URL_LIMIT', 'validate' => 'int:0:50000', 'type' => 'text:6:6', 'explain' => true, 'overriding' => true),
						'google_sql_limit' => array('lang' => 'GYM_SQL_LIMIT', 'validate' => 'int:0:2500', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_url_limit' => 2500,
					'google_sql_limit' => 150,
				),
			),
			'sort' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_SORT',
					'vars'	=> array(
						'legend1'	=> 'GYM_SORT',
						'google_sort' => array('lang' => 'GYM_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_sort' => 'DESC',
				),
				'select' => array(
					'google_sort' => @$this->dyn_select['sort'],
				),
			),
			'pagination' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_PAGINATION',
					'vars'	=> array(
						'legend1'	=> 'GYM_PAGINATION',
						'google_pagination' => array('lang' => 'GYM_PAGINATION_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_limitdown' => array('lang' => 'GYM_LIMITDOWN', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'google_limitup' => array('lang' => 'GYM_LIMITUP', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_pagination' => 1,
					'google_limitdown' => 3,
					'google_limitup' => 5,
				),
			),
			'override' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_OVERRIDE',
					'vars'	=> array(
						'legend1'	=> 'GYM_OVERRIDE',
						'google_override'	=> array('lang' => 'GYM_OVERRIDE_MAIN', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'google_override_cache'	=> array('lang' => 'GYM_OVERRIDE_CACHE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'google_override_modrewrite'	=> array('lang' => 'GYM_OVERRIDE_MODREWRITE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'google_override_gzip'	=> array('lang' => 'GYM_OVERRIDE_GZIP','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'google_override_limit'	=> array('lang' => 'GYM_OVERRIDE_LIMIT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'google_override_sort'	=> array('lang' => 'GYM_OVERRIDE_SORT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'google_override_pagination'	=> array('lang' => 'GYM_OVERRIDE_PAGINATION','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),

					),
				),
				'default' => array(
					'google_override' => OVERRIDE_MODULE,
					'google_override_cache'	=> OVERRIDE_OTYPE,
					'google_override_modrewrite' => OVERRIDE_OTYPE,
					'google_override_gzip'	=> OVERRIDE_GLOBAL,
					'google_override_limit'	=> OVERRIDE_OTYPE,
					'google_override_sort'	=> OVERRIDE_MODULE,
					'google_override_pagination' => OVERRIDE_OTYPE,
				),
				'select' => array(
					'google_override' => $this->google_override,
					'google_override_cache' => @$this->dyn_select['override'],
					'google_override_modrewrite' => @$this->dyn_select['override'],
					'google_override_gzip' => @$this->dyn_select['override'],
					'google_override_limit' => @$this->dyn_select['override'],
					'google_override_sort' => @$this->dyn_select['override'],
					'google_override_pagination' => @$this->dyn_select['override'],
				),
			),
			'main' => array(
			       	'display_vars' => array(
					'title'	=> 'GOOGLE_MAIN',
					'vars'	=> array(
						// URL Settings
						'legend1'	=> 'GOOGLE_URL',
						'google_url'	=> array('lang' => 'GOOGLE_URL', 'validate' => 'string', 'type' => 'text:35:200', 'explain' => true),
						// Link Settings
						'legend2'	=> 'GOOGLE_LINKS_ACTIVATION',
						'google_link_main'	=> array('lang' => 'GOOGLE_LINKS_MAIN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_link_index' => array('lang' => 'GOOGLE_LINKS_INDEX', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_link_cat' => array('lang' => 'GOOGLE_LINKS_CAT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						// Auth settings
						'legend3'	=> 'GOOGLE_AUTH_SETTINGS',
						'google_allow_auth' => array('lang' => 'GOOGLE_ALLOW_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'google_cache_auth' => array('lang' => 'GOOGLE_CACHE_AUTH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						// Ping
						'legend4' => 'GOOGLE_PING',
						'google_ping' => array('lang' => 'GOOGLE_PING', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						// Styling
						'legend5' => 'GYM_STYLE',
						'google_xslt'	=> array('lang' => 'GOOGLE_XSLT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'google_load_phpbb_css' => array('lang' => 'GOOGLE_LOAD_PHPBB_CSS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						// Threshold
						'legend6' => 'GOOGLE_THRESHOLD',
						'google_threshold' => array('lang' => 'GOOGLE_THRESHOLD', 'validate' => 'int:1:100', 'type' => 'text:4:4', 'explain' => true),
						// Priorities
						'legend7' => 'GOOGLE_PRIORITIES',
						'google_default_priority' => array('lang' => 'GOOGLE_DEFAULT_PRIORITY', 'type' => 'custom', 'validate' => 'string', 'method' => 'validate_num', 'params' => array('{CONFIG_VALUE}', '{KEY}', 2, 0, 1),  'explain' => true,),
					)
				),
				'default' => array(
					'google_url' => $phpbb_seo->seo_path['phpbb_url'],
					'google_link_main' => 1,
					'google_link_index' => 1,
					'google_link_cat' => 1,
					'google_allow_auth' => 0,
					'google_cache_auth' => 1,
					'google_ping' => 0,
					'google_xslt'	=> 1,
					'google_load_phpbb_css'	=> 0,
					'google_threshold' => 10,
					'google_default_priority' => 1.0,
				),
			),
			'info' => array(
				'title_lang' => 'GYM_GOOGLE',
				'lang_file' => 'gym_google',
				'actions' => array( 'main', 'cache', 'modrewrite', 'gzip', 'limit', 'sort', 'pagination', 'override',),
				'mode' => 'google',
				'module' => 'main'
			),
		);
	}
}
?>