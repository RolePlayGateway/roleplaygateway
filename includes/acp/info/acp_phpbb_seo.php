<?php
/**
*
* @package Ultimate SEO URL phpBB SEO
* @version $Id: acp_phpbb_seo.php 131 2009-10-25 12:03:44Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl1.5.txt Reciprocal Public License 1.5
*
*/

/**
* @package module_install
*/
class acp_phpbb_seo_info {
	function module() {
		return array(
			'filename'	=> 'phpbb_seo',
			'title'		=> 'ACP_CAT_PHPBB_SEO',
			'version'	=> '0.6.0',
			'modes'		=> array(
				'settings'		=> array('title' => 'ACP_PHPBB_SEO_CLASS', 'auth' => 'acl_a_board', 'cat' => array('ACP_MOD_REWRITE')),
				'forum_url'		=> array('title' => 'ACP_FORUM_URL', 'auth' => 'acl_a_board', 'cat' => array('ACP_MOD_REWRITE')),
				'htaccess'		=> array('title' => 'ACP_HTACCESS', 'auth' => 'acl_a_board', 'cat' => array('ACP_MOD_REWRITE')),
				'extended'		=> array('title' => 'ACP_SEO_EXTENDED', 'auth' => 'acl_a_board', 'cat' => array('ACP_MOD_REWRITE')),
			));
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>