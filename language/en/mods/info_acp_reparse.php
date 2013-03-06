<?php
/**
*
* info_acp_reparse [English]
*
* @package language
* @version $Id $
* @copyright (c) 2008 iWisdom
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_REPARSE_BBCODE'			=> 'Reparse BBCode',
	
	'ADDED_PERMISSIONS'				=> 'The permissions needed to run Admin Reparse BBCode have been added.',
	
	'BBCODE_REPARSE_COMPLETE'		=> 'BBCodes have been reparsed.',
	'BBCODE_REPARSE_CONFIRM'		=> 'Are you sure you want to reparse all BBCodes? This may take some time.',
	'BBCODE_REPARSE_PROGRESS'		=> 'Step %1$d completed. Moving on to step %2$d in a moment...',
	
	'LOG_BBCODE_REPARSED'			=> '<strong>Reparsed BBCode</strong>',
	
	'REMOVE_INSTALL'				=> 'Please delete the install folder immediately.',
));
?>