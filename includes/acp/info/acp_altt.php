<?php

/** 
*
* @package phpbb3 - NV advanced last topics title
* @version $Id: acp_altt.php 25 2007-12-02 12:15:08Z nickvergessen $
* @copyright (c) 2005 phpBB Group; 2006 phpBB.de; 2007 nickvergessen ( http://mods.flying-bits.org/ )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_altt_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_altt',
			'title'		=> 'ALTT_TITLE',
			'version'	=> '1.2.1',
			'modes'		=> array(
				'config_altt'	=> array(
					'title'			=> 'ALTT_CONFIG',
					'auth'			=> 'acl_a_board',
					'cat'			=> array('ACP_BOARD_CONFIGURATION'),
				),
			),
		);
	}
}

?>