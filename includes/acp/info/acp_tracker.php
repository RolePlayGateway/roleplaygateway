<?php
/**
*
* @package acp
* @version $Id: acp_tracker.php 114 2008-05-05 20:07:38Z evil3 $
* @copyright (c) 2008 http://www.jeffrusso.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_tracker_info
{
	public function module()
	{
		return array(
			'filename'	=> 'acp_tracker',
			'title'		=> 'ACP_TRACKER',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'		=> array('title' => 'ACP_TRACKER_SETTINGS', 		'auth' => 'acl_a_tracker', 'cat' => array('ACP_TRACKER')),
				'attachments'	=> array('title' => 'ACP_TRACKER_ATTACHMENTS', 		'auth' => 'acl_a_tracker', 'cat' => array('ACP_TRACKER')),
				'project'		=> array('title' => 'ACP_TRACKER_PROJECT', 			'auth' => 'acl_a_tracker', 'cat' => array('ACP_TRACKER')),
				'component'		=> array('title' => 'ACP_TRACKER_COMPONENT', 		'auth' => 'acl_a_tracker', 'cat' => array('ACP_TRACKER')),
				'version'		=> array('title' => 'ACP_TRACKER_VERSION', 			'auth' => 'acl_a_tracker', 'cat' => array('ACP_TRACKER')),
			),
		);
	}

	public function install()
	{
	}

	public function uninstall()
	{
	}
}

?>