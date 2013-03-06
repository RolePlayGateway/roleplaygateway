<?php
/** 
*
* @package ucp
* @version $Id: v3_modules.xml 52 2007-12-09 19:45:45Z jelly_doughnut $
* @copyright (c) 2007 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
							
/**
* @package module_install
*/
class ucp_digests_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_digests',
			'title'		=> 'UCP_DIGESTS',
			'version'	=> '2.1.1',
			'modes'		=> array(
				'basics'				=> array('title' => 'UCP_DIGESTS_BASICS', 'auth' => '', 'cat' => array('UCP_MAIN')),
				'posts_selection'		=> array('title' => 'UCP_DIGESTS_POSTS_SELECTION', 'auth' => '', 'cat' => array('UCP_MAIN')),
				'post_filters'			=> array('title' => 'UCP_DIGESTS_POST_FILTERS', 'auth' => '', 'cat' => array('UCP_MAIN')),
				'additional_criteria'	=> array('title' => 'UCP_DIGESTS_ADDITIONAL_CRITERIA', 'auth' => '', 'cat' => array('UCP_MAIN')),
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