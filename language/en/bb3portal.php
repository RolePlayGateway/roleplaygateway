<?php
/** 
*
* bb3portal.php [English]
*
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: bb3portal.php,v 1.10 2007/04/14 02:05:17 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
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


$lang = array_merge($lang, array(
	'PORTAL'			=> 'RolePlayGateway',
	'ANNOUNCEMENTS'		=> 'Announcements',
	'NEWS'			    => 'News',
	'POLL'			    => 'Poll',
	'READ_FULL'			=> 'Read all',
	'NO_NEWS'			=> 'No news',
	'POSTED_BY'			=> 'Poster',
	'COMMENTS'			=> 'Comments',
	'VIEW_COMMENTS'		=> 'View comments',
	'POST_REPLY'		=> 'Write comments',
	'CLOCK'				=> 'Clock',

	// who is online
	'WIO_TOTAL'			=> 'Total',
	'WIO_REGISTERED'	=> 'Registered',
	'WIO_HIDDEN'		=> 'Hidden',
	'WIO_GUEST'			=> 'Guest',
	//'RECORD_ONLINE_USERS'=> 'View record: <strong>%1$s</strong><br />%2$s',

	// welcome
	'WELCOME'	=> 'Welcome',	
	'WELCOME_INTRO'	=> '
phpBB3 Portal is tryout version of phpBB3 Olympus. It is easy to use and improvable. 
<br /><br />
<strong>phpBB3 Portal</strong> which is based on <strong>phpBB3</strong> and improved by <a href="http://www.phpbbturkiye.net" target="_blank"><strong>phpBB Türkiye</strong></a> & <a href="http://www.canversoft.net" target="_blank"><strong>Canver Software</strong></a>
<br /><br />
XHTML and UTF8 characters sets are used in the files as in the phpBB3 version.
<br /><br />
You can change this message in <em>language/en/bb3portal.php</em> file.
<br />',

	// user menu
	'USER_MENU'			=> 'User menu',
	'UM_LOG_ME_IN'		=> 'remember me',
	'UM_HIDE_ME'		=> 'hide me',
	'UM_MAIN_SUBSCRIBED'=> 'Subscribed',
	'UM_BOOKMARKS'		=> 'Bookmarks',

	// statistic
	'ST_NEW'		=> 'New',
	'ST_NEW_POSTS'	=> 'New post',
	'ST_NEW_TOPICS'	=> 'New topic',
	'ST_NEW_ANNS'	=> 'New announcement',
	'ST_NEW_STICKYS'=> 'New sticky',
	'ST_TOP'		=> 'Total',
	'ST_TOP_ANNS'	=> 'Total announcement',
	'ST_TOP_STICKYS'=> 'Total sticky',

	// search
	'SH'		=> 'go',
	'SH_SITE'	=> 'forums',
	'SH_POSTS'	=> 'posts',
	'SH_AUTHOR'	=> 'author',
	'SH_ENGINE'	=> 'search engines',
	'SH_ADV'	=> 'advanced search',
	
	// recent
	'RECENT_TOPIC'		=> 'Recent topic',
	'RECENT_ANN' 		=> 'Recent announcement',
	'RECENT_HOT_TOPIC' 	=> 'Recent popular topic',

	// random member
	'RND_MEMBER'	=> 'Random member',
	'RND_JOIN'		=> 'Join',
	'RND_POSTS' 	=> 'Posts',
	'RND_OCC' 		=> 'Occupation',
	'RND_FROM' 		=> 'Location',
	'RND_WWW' 		=> 'Web page',

	// most poster
	'MOST_POSTER' => 'Top poster',

	// links
	'LINKS' => 'Links',

	// latest members
	'LATEST_MEMBERS' => 'Latest members',

	// make donation
	'DONATION' 		=> 'Make donation',
	'DONATION_TEXT' => 'is a formation suplying services with no intention of any revenue. Anyone who wants to support this formation can do it by donating so that the cost of server, the domain and etc. could be paid of.',
	'PAY_MSG'		=> 'After selecting the amount which you want to donate from the menu, you can go on by clicking on the picture of PayPal.',
	'PAY_ITEM' 		=> 'Make donation', // paypal item

	// main menu
	'M_MENU' 		=> 'Menu',
	'M_CONTENT' 	=> 'Content',
	'M_ACP' 		=> 'ACP',
	'M_HELP' 		=> 'Help',
	'M_BBCODE' 		=> 'BBCode FAQ',
	'M_TERMS' 		=> 'Term of use',
	'M_PRV' 		=> 'Privacy policy',

	// link us
	'LINK_US' 		=> 'Link to us',
	'LINK_US_TXT' 	=> 'Please feel free to link to <strong>%s</strong>. Use the following HTML:',

	// friends
	'FRIENDS'				=> 'Friends',
	'FRIENDS_OFFLINE'		=> 'Offline',
	'FRIENDS_ONLINE'		=> 'Online',
	'NO_FRIENDS'			=> 'No friends currently defined',
	'NO_FRIENDS_OFFLINE'	=> 'No friends offline',
	'NO_FRIENDS_ONLINE'		=> 'No friends online',
	
	// last bots
	'LAST_VISITED_BOTS'		=> 'Last %s visited bots',
	
	// team
	'NO_ADMINISTRATORS_P'	=> 'No administrators',
	'NO_MODERATORS_P'		=> 'No moderators',
	
	/**
	* DO NOT REMOVE or CHANGE
	*/
	'PORTAL_COPY' 	=> 'Portal by <a href="http://www.phpbb3portal.com" title="phpBB3 Portal" target="_blank">phpBB3 Portal</a> &copy; <a href="http://www.phpbbturkiye.net" target="_blank">phpBB Türkiye</a>',
	
	)
);

?>