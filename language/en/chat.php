<?php
/**
*
* groups [English]
*
* @package language
* @version $Id: chat.php 380 2007-03-13 01:48:04Z roadydude $
* @copyright (c) 2006 StarTrekGuide Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'DETAILS'		=> '<a href="http://startrekguide.com/forum/f27t1250-ajax_chat.html" style="font-weight: bold;">AJAX Chat</a> by <a href="http://startrekguide.com/forum/memberlist.php?mode=viewprofile&un=Handyman" style="color: rgb(170, 0, 0); font-weight: bold;">Handyman</a>',
	'MESSAGE'		=> 'Message',
	'ONLINE_LIST'	=> 'Online List',
	'PAGE_TITLE'	=> 'Forum Chat',
	'UNIT'			=> 'Seconds',
	'UPDATES'		=> 'Updates every',
));
?>