<?php
/** 
*
* @package acp
* @version $Id $
* @copyright (c) 2008 iWisdom
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
							
/**
* @package module_install
*/
class acp_reparse_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_reparse',
			'title'		=> 'ACP_REPARSE_BBCODE',
			'version'	=> '0.0.1',
			'modes'		=> array(
				'index'	=> array('title' => 'ACP_REPARSE_BBCODE', 'auth' => 'acl_a_reparse_bbcode', 'cat' => array('')),
			),
		);
	}
							
	function install()
	{
	}
								
	function uninstall()
	{
	}

}
?>
