<?php
/**
*
* @FILENAME  : includes\acp\info\acp_phpBBTags.php
* @DATE      : 19 Jan 2009
* @VERSION   : 1.0
* @COPYRIGHT : (c) 2009 phpbb3-mods
* @Website   : http://www.phpbb3-mods.com/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*/
							
/**
* @package module_install
*/
class acp_phpBBTags_info
{

	function module()
	{
	return array(
		'filename'	=> 'acp_phpBBTags',
		'title'		=> 'ACP_PHPBBTAGS',
		'version'	=> '0.1.0',
		'modes'		=> array(
			'configure'		=> array('title' => 'PBT_CONFIGURE',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
								 
			'manage'		=> array('title' => 'PBT_MANAGE',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
								 
			'remove'		=> array('title' => 'PBT_REMOVE',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
			'clear_orphans'	=> array('title' => 'PBT_CLEAR_ORPHANS',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
			'view_all'		=> array('title' => 'PBT_VIEW_ALL',
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