<?php
/**
*
* acp_ban [English]
*
* @package language
* @version $Id: info_acp_donation_mod.php 3 2008-04-04 20:41:37Z Highway of Life $
* @copyright (c) 2005 phpBB Group
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

/**
* DO NOT CHANGE
*/
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

$lang = array_merge($lang, array(
	'ACP_DONATION_MANAGEMENT'		=> 'PayPal Donation MOD Management',
	'ACP_DONATION_MOD'				=> 'Donation MOD Configuration',
	'ACP_DONATE_SETTINGS'			=> 'Settings',
	'ACP_DONATE_REWARDS'			=> 'Rewards',
	'ACP_DONATE_GOALS'				=> 'Goals',
	'ACP_DONATE_DONATIONS'			=> 'Donations',
	'ACP_DONATE_SUPPORTERS'			=> 'Supporters', 
));

?>