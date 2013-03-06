<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_common.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* gym_common [English]
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
	'RSS_AUTH_SOME_USER' => '<b><u>Warning :</u></b>This item list is personalized according to <b>%s</b>\'s authorizations.<br/>Some of the item listed may not be viewable when not logged in.',
	'RSS_AUTH_THIS_USER' => '<b><u>Warning :</u></b>This item is personalized according to <b>%s</b>\'s authorizations.<br/>It will not be viewable when not logged in.',
	'RSS_AUTH_SOME' => '<b><u>Warning :</u></b>This item list is not public.<br/>Some of the item listed may not be viewable when not logged in.',
	'RSS_AUTH_THIS' => '<b><u>Warning :</u></b>This item is not public.<br/>It will not be viewable when not logged in.',
	'RSS_CHAN_LIST_TITLE' => 'Channel list',
	'RSS_CHAN_LIST_DESC' => 'This channel list is listing the available RSS feeds.',
	'RSS_CHAN_LIST_DESC_MODULE' => 'This channel list is listing the RSS feeds available for : %s.',
	'RSS_ANNOUCES_DESC' => 'This feeds is listing all the global announcements of : %s',
	'RSS_ANNOUNCES_TITLE' => 'Announces from  : %s',
	'GYM_LAST_POST_BY' => 'Last post by ',
	'GYM_FIRST_POST_BY' => 'Post by ',
	'GYM_LINK' => 'Link',
	'GYM_SOURCE' => 'Source',
	'GYM_RSS_SOURCE' => 'Source',
	'RSS_MORE' => 'more',
	'RSS_CHANNELS' => 'Channels',
	'RSS_CONTENT' => 'Digest',
	'RSS_SHORT' => 'Short list',
	'RSS_LONG' => 'Long list',
	'RSS_NEWS' => 'News',
	'RSS_NEWS_DESC' => 'Latest news from',
	'RSS_REPORTED_UNAPPROVED' => 'This item is currently waiting for approval.',

	'GYM_HOME' => 'Home Page',
	'GYM_FORUM_INDEX' => 'Forum Index',
	'GYM_LASTMOD_DATE' => 'Last modification date',
	'GYM_SEO' => 'Search Engine Optimization',
	'GYM_MINUTES' => 'minute(s)',
	'GYM_SQLEXPLAIN' => 'SQL Explain report',
	'GYM_SQLEXPLAIN_MSG' => 'Logged in as admin, you can check the %s for this page.',
	'GYM_BOOKMARK_THIS' => 'Bookmark this',
	// Errors
	'GYM_ERROR_404' => 'This page does not exist or is not activated',
	'GYM_ERROR_404_EXPLAIN' => 'The server did not find any page corresponding to the URL you have used.',
	'GYM_ERROR_401' => 'You are not allowed to view this page.',
	'GYM_ERROR_401_EXPLAIN' => 'This page is only accessible to logged in users granted with the required authorisations.',
	'GYM_LOGIN' => 'You are not allowed to view this page.',
	'GYM_LOGIN_EXPLAIN' => 'You must be registered and logged in to view this page.',
	'GYM_TOO_FEW_ITEMS' => 'Page Unavailable',
	'GYM_TOO_FEW_ITEMS_EXPLAIN' => 'This page does not contain enough item to be displayed.',
	'GYM_TOO_FEW_ITEMS_EXPLAIN_ADMIN' => 'This page source is either empty or does not contain enough items (less than the configured threshold in ACP) to be displayed.<br/>A 404 Not Found header was sent to properly inform Search Engines to discard this link.',

	'GOOGLE_SITEMAP' => 'Sitemap',
	'GOOGLE_SITEMAP_OF' => 'Sitemap of',
	'GOOGLE_MAP_OF' => 'Sitemap of %1$s',
	'GOOGLE_SITEMAPINDEX' => 'SitemapIndex',
	'GOOGLE_NUMBER_OF_SITEMAP' => 'Number of Sitemaps in this Google SitemapIndex',
	'GOOGLE_NUMBER_OF_URL' => 'Number of URLs in this Google Sitemap',
	'GOOGLE_SITEMAP_URL' => 'Sitemap URL',
	'GOOGLE_CHANGEFREQ' => 'Change freq.',
	'GOOGLE_PRIORITY' => 'priority',

	'RSS_FEED' => 'RSS Feed',
	'RSS_FEED_OF' => 'RSS Feed of %1$s',
	'RSS_2_LINK' => 'RSS 2.0 feed link',
	'RSS_UPDATE' => 'Update',
	'RSS_LAST_UPDATE' => 'Last Update',
	'RSS_SUBSCRIBE_POD' => '<h2>Bookmark this feed Now!</h2>With your preferred service.',
	'RSS_SUBSCRIBE' => 'To subscribe to this RSS feed manually, please use the following URL :',
	'RSS_ITEM_LISTED' => 'One item listed.',
	'RSS_ITEMS_LISTED' => 'items listed.',
	'RSS_VALID' => 'RSS 2.0 Valid feed',

	// Old URL handling
	'RSS_1XREDIR' => 'This RSS Feed has been moved',
	'RSS_1XREDIR_MSG' => 'This RSS Feed has been moved, you can now find it here ',
	// HTML sitemaps
	'HTML_MAP' => 'Site map',
	'HTML_MAP_OF' => 'Site map of %1$s',
	'HTML_MAP_NONE' => 'No site map',
	'HTML_NO_ITEMS' => 'No item',
	'HTML_NEWS' => 'News',
	'HTML_NEWS_OF' => 'News of %1$s',
	'HTML_NEWS_NONE' => 'No news',
	'HTML_PAGE' => 'Page',
	'HTML_MORE' => 'Read more',
	// Forum
	'HTML_FORUM_MAP' => 'Forum site map',
	'HTML_FORUM_NEWS' => 'Forums news',
	'HTML_FORUM_GLOBAL_MAP' => 'Global announcement list',
	'HTML_FORUM_GLOBAL_NEWS' => 'Global announcements',
	'HTML_FORUM_ANNOUNCE_MAP' => 'Announcements list',
	'HTML_FORUM_ANNOUNCE_NEWS' => 'Announcements',
	'HTML_FORUM_STICKY_MAP' => 'Sticky list',
	'HTML_FORUM_STICKY_NEWS' => 'Stickies',
	'HTML_LASTX_TOPICS_TITLE' => 'Last %1$s active topics',
));
?>
