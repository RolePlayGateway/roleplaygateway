<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_txt.php 112 2009-09-30 17:21:34Z dcz $
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
	var $gym_master;
	var $dyn_select = array();
	/**
	* constuctor
	*/
	function google_txt(&$gym_master) {
		$this->gym_master = &$gym_master;
		if (isset($this->gym_master->dyn_select) ) {
			$this->dyn_select = $this->gym_master->dyn_select;
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
						'google_txt_cache_on'	=> array('lang' => 'GYM_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_txt_cache_force_gzip' => array('lang' => 'GYM_CACHE_FORCE_GZIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_txt_cache_max_age' => array('lang' => 'GYM_CACHE_MAX_AGE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'google_txt_showstats'	=> array('lang' => 'GYM_SHOWSTATS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_txt_cache_on' => 1,
					'google_txt_cache_force_gzip' => 0,
					'google_txt_cache_max_age' => 24,
					'google_txt_showstats' => 1,
				),
			),
			'gzip' => array(
				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend1' => 'GYM_GZIP',
						'google_txt_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_txt_gzip_ext' => array('lang' => 'GYM_GZIP_EXT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_txt_gzip' => 0,
					'google_txt_gzip_ext' => 1,
				),
			),
			'limit' => array(
				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'GYM_LIMIT',
						'google_txt_url_limit' => array('lang' => 'GYM_URL_LIMIT', 'validate' => 'int:0:5000', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_txt_url_limit' => 750,
				),
			),
			// We always need a main
			'main' => array(
				'display_vars' => array(
					'title'	=> 'GOOGLE_TXT_CONFIG',
					'vars'	=> array(
						'legend1' => 'GOOGLE_TXT_CONFIG',
						'google_txt_randomize' => array('lang' => 'GOOGLE_TXT_RANDOMIZE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_txt_unique' => array('lang' => 'GOOGLE_TXT_UNIQUE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_txt_check_robots' => array('lang' => 'GYM_CHECK_ROBOTS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_txt_force_lastmod' => array('lang' => 'GOOGLE_TXT_FORCE_LASTMOD', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'legend2' => 'GOOGLE_PRIORITIES',
						'google_txt_default_priority' => array('lang' => 'GOOGLE_DEFAULT_PRIORITY', 'type' => 'custom', 'validate' => 'string', 'method' => 'validate_num', 'params' => array('{CONFIG_VALUE}', '{KEY}', 2, 0, 1),  'explain' => true, 'overriding' => true),
						'legend3' => 'GOOGLE_PING',
						'google_txt_ping' => array('lang' => 'GOOGLE_PING', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_txt_randomize' => 0,
					'google_txt_unique' => 0,
					'google_txt_check_robots' => 0,
					'google_txt_force_lastmod' => 0,
					'google_txt_default_priority' => 1.0,
					'google_txt_ping' => 0,
				),
			),
			'info' => array(
				'title_lang' => 'GOOGLE_TXT',
				'lang_file' => 'google_txt',
				'actions' => array( 'main', 'cache', 'gzip', 'limit'),
				'mode' => 'google',
				'module' => 'txt'
			),
		);
	}
}
?>