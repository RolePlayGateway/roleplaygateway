<?php
/**
*
* @package notify_moderators
* @version $Id: 1.1.0
* @copyright (c) 2008 david63
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/

class acp_notify_moderators_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_notify_moderators',
			'title'		=> 'ACP_NOTIFY_MODERATORS',
			'version'	=> '1.1.0',
			'modes'		=> array(
				'settings'	=> array('title' => 'ACP_NOTIFY_SETTINGS', 'auth' => 'acl_a_notify_moderators', 'cat' => array('ACP_CAT_DOT_MODS')),
				'list'		=> array('title' => 'ACP_NOTIFY_LIST', 'auth' => 'acl_a_notify_moderators', 'cat' => array('ACP_CAT_DOT_MODS')),
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