<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_xml.php 112 2009-09-30 17:21:34Z dcz $
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
	var $gym_master;
	var $dyn_select = array();
	/**
	* constuctor
	*/
	function google_xml(&$gym_master) {
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
						'google_xml_cache_on'	=> array('lang' => 'GYM_CACHE_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_xml_cache_force_gzip' => array('lang' => 'GYM_CACHE_FORCE_GZIP', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_xml_cache_max_age' => array('lang' => 'GYM_CACHE_MAX_AGE', 'validate' => 'string', 'type' => 'text:4:4', 'explain' => true, 'overriding' => true),
						'google_xml_showstats'	=> array('lang' => 'GYM_SHOWSTATS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_xml_cache_on' => 1,
					'google_xml_cache_force_gzip' => 0,
					'google_xml_cache_max_age' => 24,
					'google_xml_showstats' => 1,
				),
			),
			'gzip' => array(
				'display_vars' => array(
					'title'	=> 'GYM_GZIP',
					'vars'	=> array(
						'legend1'	=> 'GYM_GZIP',
						'google_xml_gzip' => array('lang' => 'GYM_GZIP_ON', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
						'google_xml_gzip_ext' => array('lang' => 'GYM_GZIP_EXT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_xml_gzip' => 0,
					'google_xml_gzip_ext' => 1,
				),
			),
			'limit' => array(
				'display_vars' => array(
					'title'	=> 'GYM_LIMIT',
					'vars'	=> array(
						'legend1'	=> 'GYM_LIMIT',
						'google_xml_url_limit' => array('lang' => 'GYM_URL_LIMIT', 'validate' => 'int:0:10000', 'type' => 'text:8:8', 'explain' => true, 'overriding' => true),
						'google_xml_force_limit' => array('lang' => 'GOOGLE_XML_FORCE_LIMIT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
					),
				),
				'default' => array(
					'google_xml_url_limit' => 750,
					'google_xml_force_limit' => 0,
				),
			),
			// We always need a main
			'main' => array(
				'display_vars' => array(
					'title'	=> 'GOOGLE_XML_CONFIG',
					'vars'	=> array(
						'legend1' => 'GOOGLE_XML_CONFIG',
						'google_xml_randomize' => array('lang' => 'GOOGLE_XML_RANDOMIZE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_xml_unique' => array('lang' => 'GOOGLE_XML_UNIQUE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_xml_check_robots' => array('lang' => 'GYM_CHECK_ROBOTS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'google_xml_force_lastmod' => array('lang' => 'GOOGLE_XML_FORCE_LASTMOD', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,),
						'legend2' => 'GOOGLE_PRIORITIES',
						'google_xml_default_priority' => array('lang' => 'GOOGLE_DEFAULT_PRIORITY', 'type' => 'custom', 'validate' => 'string', 'method' => 'validate_num', 'params' => array('{CONFIG_VALUE}', '{KEY}', 2, 0, 1),  'explain' => true, 'overriding' => true),
						'legend3' => 'GOOGLE_PING',
						'google_xml_ping' => array('lang' => 'GOOGLE_PING', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'overriding' => true),
					),
				),
				'default' => array(
					'google_xml_randomize' => 0,
					'google_xml_unique' => 0,
					'google_xml_check_robots' => 0,
					'google_xml_force_lastmod' => 0,
					'google_xml_default_priority' => 1.0,
					'google_xml_ping' => 0,
				),
			),
			'info' => array(
				'title_lang' => 'GOOGLE_XML',
				'lang_file' => 'google_xml',
				'actions' => array( 'main', 'cache', 'gzip', 'limit'),
				'mode' => 'google',
				'module' => 'xml'
			),
		);
	}
}
?>