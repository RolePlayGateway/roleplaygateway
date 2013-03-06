<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: main_main.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
* main_main Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class main_main {
	var $gym_master;
	var $dyn_select = array();
	/**
	* constuctor
	*/
	function main_main(&$gym_master) {
		$this->gym_master = &$gym_master;
		if (isset($this->gym_master->dyn_select) ) {
			$this->dyn_select = $this->gym_master->dyn_select;
		}
	}
	/**
	* acp_module()
	* returns the acp config
	* @access private
	*/
	function acp_module() {
		return array(
			'cache' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_CACHE',
					'vars'	=> array(
						'legend1' => 'GYM_CACHE',
						'gym_mod_since'	=> array('lang' => 'GYM_MOD_SINCE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'gym_cache_on'	=> array('lang' => 'GYM_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'gym_cript_cache' => array('lang' => 'GYM_CRITP_CACHE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'gym_cache_force_gzip' => array('lang' => 'GYM_CACHE_FORCE_GZIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'gym_cache_max_age' => array('lang' => 'GYM_CACHE_MAX_AGE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true,),
						'gym_cache_auto_regen' => array('lang' => 'GYM_CACHE_AUTO_REGEN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'gym_showstats' => array('lang' => 'GYM_SHOWSTATS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					'gym_mod_since'	=> 0,
					'gym_cache_on'	=> 0,
					'gym_cript_cache' => 1,
					'gym_cache_force_gzip' => 0,
					'gym_cache_max_age' => 24,
					'gym_cache_auto_regen' => 1,
					'gym_showstats' => 1,
				),
			),
			'modrewrite' => array(
			       	'display_vars' => array(
					'title'	=> 'GYM_MODREWRITE',
					'vars'	=> array(
						'legend1' => 'GYM_MODREWRITE',
						'gym_modrewrite' => array('lang' => 'GYM_MODREWRITE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'gym_modrtype' => array('lang' => 'GYM_MODRTYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'gym_zero_dupe' => array('lang' => 'GYM_ZERO_DUPE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					'gym_modrewrite' => 0,
					'gym_modrtype' => 0,
					'gym_zero_dupe' => 0,
				),
				'select' => array(
					'gym_modrtype' => @$this->dyn_select['modrtype'],
				),
			),
			'gzip' => array(
				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend1'	=> 'GYM_GZIP',
						'gym_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'gym_gzip_level' => array('lang' => 'GYM_GZIP_LEVEL', 'validate' => 'int:1:9', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'gym_gzip_ext' => array('lang' => 'GYM_GZIP_EXT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
					),
				),
				'default' => array(
					'gym_gzip' => 0,
					'gym_gzip_level' => 6,
					'gym_gzip_ext' => 1,
				),
				'select' => array(
					'gym_gzip_level' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9),
				),
			),
			'limit' => array(
				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'GYM_LIMIT',
						'gym_url_limit' => array('lang' => 'GYM_URL_LIMIT', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true,),
						'gym_sql_limit' => array('lang' => 'GYM_SQL_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true,),
						'gym_time_limit' => array('lang' => 'GYM_TIME_LIMIT', 'validate' => 'int:0:1000', 'type' => 'text:4:4', 'explain' => true,),
					),
				),
				'default' => array(
					'gym_url_limit' => 150,
					'gym_sql_limit' => 25,
					'gym_time_limit' => 60,
				),
			),
			'sort' => array(
				'display_vars' => array(
					'title'	=> 'GYM_SORT',
					'vars'	=> array(
						'legend1'	=> 'GYM_SORT',
						'gym_sort' => array('lang' => 'GYM_SORT_TYPE', 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
					),
				),
				'default' => array(
					'gym_sort' => 'DESC',
				),
				'select' => array(
					'gym_sort' => @$this->dyn_select['sort'],
				),
			),
			'pagination' => array(
				'display_vars' => array(
					'title'	=> 'GYM_PAGINATION',
					'vars'	=> array(
						'legend1'	=> 'GYM_PAGINATION',
						'gym_pagination' => array('lang' => 'GYM_PAGINATION_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'gym_limitdown' => array('lang' => 'GYM_LIMITDOWN', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true,),
						'gym_limitup' => array('lang' => 'GYM_LIMITUP', 'validate' => 'int:0', 'type' => 'text:4:4', 'explain' => true,),
					),
				),
				'default' => array(
					'gym_pagination' => 1,
					'gym_limitdown' => 3,
					'gym_limitup' => 5,
				),
			),
			'override' => array(
				'display_vars' => array(
					'title'	=> 'GYM_OVERRIDE',
					'vars'	=> array(
						'legend'	=> 'GYM_OVERRIDE',
						'gym_override'	=> array('lang' => 'GYM_OVERRIDE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'gym_override_cache'	=> array('lang' => 'GYM_OVERRIDE_CACHE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'gym_override_modrewrite'	=> array('lang' => 'GYM_OVERRIDE_MODREWRITE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'gym_override_gzip'	=> array('lang' => 'GYM_OVERRIDE_GZIP','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'gym_override_limit'	=> array('lang' => 'GYM_OVERRIDE_LIMIT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'gym_override_sort'	=> array('lang' => 'GYM_OVERRIDE_SORT','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
						'gym_override_pagination'	=> array('lang' => 'GYM_OVERRIDE_PAGINATION','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
					),
				),
				'default' => array(
					'gym_override' => 1,
					'gym_override_cache' => OVERRIDE_OTYPE,
					'gym_override_modrewrite' => OVERRIDE_GLOBAL,
					'gym_override_gzip' => OVERRIDE_GLOBAL,
					'gym_override_limit' => OVERRIDE_OTYPE,
					'gym_override_sort' => OVERRIDE_OTYPE,
					'gym_override_pagination' => OVERRIDE_OTYPE,
				),
				'select' => array(
					'gym_override_cache' => @$this->dyn_select['override'],
					'gym_override_modrewrite' => @$this->dyn_select['override'],
					'gym_override_gzip' => @$this->dyn_select['override'],
					'gym_override_limit' => @$this->dyn_select['override'],
					'gym_override_sort' => @$this->dyn_select['override'],
					'gym_override_pagination' => @$this->dyn_select['override'],
				),
			),
			'main' => array(
				'display_vars' => array(
					'title'	=> 'MAIN_MAIN',
					'vars'	=> array(
						'legend'	=> 'GYM_LINKS_ACTIVATION',
						'gym_link_main'	=> array('lang' => 'GYM_LINKS_MAIN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'gym_link_index' => array('lang' => 'GYM_LINKS_INDEX', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'gym_link_cat' => array('lang' => 'GYM_LINKS_CAT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
					),
				),
				'default' => array(
					'gym_link_main' => 1,
					'gym_link_index' => 1,
					'gym_link_cat' => 1,
				),
			),
			'info' => array(
				'title_lang' => 'GYM_MAIN',
				'lang_file' => '',
				'actions' => array('main', 'cache', 'modrewrite', 'gzip', 'limit', 'sort', 'pagination', 'override'),
				'mode' => 'main',
				'module' => 'main',
				'select' => array(
					'gym_override_cache' => @$this->dyn_select['override'],
					'gym_override_modrewrite' => @$this->dyn_select['override'],
					'gym_override_gzip' => @$this->dyn_select['override'],
					'gym_override_limit' => @$this->dyn_select['override'],
					'gym_override_sort' => @$this->dyn_select['override'],
					'gym_override_pagination' => @$this->dyn_select['override'],
					'gym_sort' => @$this->dyn_select['sort'],
					'gym_modrtype' => @$this->dyn_select['modrtype'],
					'gym_gzip_level' => @$this->dyn_select['gzip_level'],
				),
			),
		);
	}
}
?>