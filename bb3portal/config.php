<?php
/*
*
* @name config.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: config.php,v 1.6 2007/04/14 02:05:16 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

// announcements
$CFG['announcements']				= true;  // Show announcements?  true = show    false = no show
$CFG['number_of_announcements']		= '5';   // Number of announcements on Portal
$CFG['announcements_day']			= '0';   // Number of days (0 means infinite) to display the announcment
$CFG['announcements_length']		= '300'; // Max length (0 means infinite) of announcements. The length of one parsed smiley is huge (can easily be 100)... remember that when you set this...
$CFG['global_announcements_forum']	= '2';   // Global announcements forum ID

// news
$CFG['news']			= true;  // Show news?  true = show    false = no show
$CFG['show_all_news']	= false; // Show all of the articles in this forum, including Stickies, announcements, and Global announcements?
$CFG['number_of_news']	= '5';   // Number of news articles on Portal (0 means infinite)
$CFG['news_length']		= '200'; // Max length (0 means infinite) of news article. The length of one parsed smiley is huge (can easily be 100)... remember that when you set this...
$CFG['news_forum']		= '';   // News Forum ID (forum we pull the articles from, leave blank to pull from all forums) separate by comma for multi-forums, eg. '1,2,5'

// recents topic
// recent topic have auto auth but if you disable some forum id, use this settings
$CFG['exclude_forums']		= '';   // Exclude Forum ID (forum we pull the articles from, leave blank to pull from all forums) separate by comma for multi-forums, eg. '1,2,5'
$CFG['max_topics']			= '10';  // Limit of recent announcements/hot topics
$CFG['recent_title_limit']	= '30'; // character limit for recent topic

// paypal block
$CFG['pay_c_block']	= true; // show paypal center block
$CFG['pay_s_block']	= true;  // show paypal small block 
$CFG['pay_acc']		= 'admin@roleplaygateway.com'; // paypal account, eg. xxx@xxx.com

// last x bots
$CFG['load_last_visited_bots'] 		= true; // show last x bot blocks
$CFG['last_visited_bots_number']	= '5'; // how many bots show

// poll block
$CFG['poll_topic'] 		= true;  // show poll blocks
$CFG['poll_topic_id']	= '1308'; // poll topic id

// other
$CFG['max_last_member']		= '10';  // Limit of latest members
$CFG['max_most_poster']		= '10';  // Limit of most poster
$CFG['max_online_friends']	= '10';  // Limit of online friends


?>
