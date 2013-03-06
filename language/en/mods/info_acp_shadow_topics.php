<?php
/**
*
* Temporary Shadow Topics[English]
*
* @package language
* @version $Id: info_acp_shadow_topics.php,v 1.0.0 2009/07/04 15:35:00 mtrs Exp $
* @copyright (c) 2009 mtrs
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

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'SHADOW_PRUNE_ENABLE'			=> 'Shadow topics prune frequency',
	'SHADOW_PRUNE_ENABLE_EXPLAIN'	=> 'Enter 0 to disable mod, otherwise set shadow topics prune frequency in hours',
	'SHADOW_PRUNE_TIME'				=> 'Next time (unix seconds) shadow topics to be pruned',
	'SHADOW_TOPICS_PRUNED' 			=> '<strong>Shadow topics pruned</strong>',
	'MCP_MAIN_SHADOW_FORUM_VIEW'	=> 'View shadow topics',
	'INSTALL_TEMP_SHADOW_TOPICS'	=> 'Install Temporary Shadow Topics MOD',
	'MOD_INSTALLED'					=> 'Mod installed successfully. Please remove the install_temp_shadow_topics.php script',
	
));

?>