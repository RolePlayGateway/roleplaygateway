<?php
/**
*
* @package ucp
* @version $Id: ucp_advertisements.php,v 1.0 2008/11/23 14:36:33 Eric Martindale Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class ucp_advertisements_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_advertisements',
			'title'		=> 'UCP_ADVERTISEMENTS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'advertisements'	=> array('title' => 'UCP_MAIN_ATTACHMENTS', 'auth' => 'acl_u_attach', 'cat' => array('UCP_MAIN')),
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