<?php
/** 
*
* @package acp
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
							
/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class acp_phpbb_topic_tagging_info
{

	function module()
	{
	return array(
		'filename'	=> 'acp_phpbb_topic_tagging',
		'title'		=> 'PTT_ACP_MODULE_TITLE',
		'version'	=> '0.1.0',
		'modes'		=> array(
			'configure'		=> array('title' => 'PTT_ACP_CONFIGURE_TITLE',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
								 
			'manage'		=> array('title' => 'PTT_ACP_MANAGE_TITLE',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
								 
			'remove'		=> array('title' => 'PTT_ACP_REMOVE_TITLE',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
			'clear_orphans'	=> array('title' => 'PTT_ACP_CLEAR_ORPHANS',
								 'auth' => 'acl_a_user', 
								 'cat' => array('ACP_GENERAL')),
			'view_all'		=> array('title' => 'PTT_ACP_VIEW_ALL_TITLE',
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