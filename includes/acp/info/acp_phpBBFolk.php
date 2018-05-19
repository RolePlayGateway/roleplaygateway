<?php
/** 
*
* @package acp
* @version $Id: v3_modules.xml,v 1.4 2007/03/18 18:16:40 kellanved Exp $
* @copyright (c) 2007 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
							
/**
* @package module_install
*/
class acp_phpBBFolk_info
{

	function module()
	{
	return array(
		'filename'	=> 'acp_phpBBFolk',
		'title'		=> 'ACP_PHPBBFOLK',
		'version'	=> '0.1.0',
		'modes'		=> array(
			'configure'		=> array('title' => 'PBF_CONFIGURE', 
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
								 
			'manage'		=> array('title' => 'PBF_MANAGE', 
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
								 
			'remove'		=> array('title' => 'PBF_REMOVE',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
			'clear_orphans'	=> array('title' => 'PBF_CLEAR_ORPHANS',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
			'view_all'		=> array('title' => 'PBF_VIEW_ALL',
					 			 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
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