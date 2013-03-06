<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: rss_forum.php 204 2009-12-20 12:04:51Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* rss_forum [English]
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
	'RSS_FORUM' => 'Forum RSS module',
	'RSS_FORUM_EXPLAIN' => 'These are the setting for the forum RSS feeds module.<br/> Some of them can be overridden depending on the RSS and Main override settings.',
	'RSS_FORUM_ALTERNATE' => 'RSS alternate links',
	'RSS_FORUM_ALTERNATE_EXPLAIN' => 'Display or not forum RSS alternate links in browsers navigation bar',
	'RSS_FORUM_EXCLUDE' => 'Forum Exclusions',
	'RSS_FORUM_EXCLUDE_EXPLAIN' => 'You can here exclude one or several forum from the RSS listing.<br /><u>Note :</u> If this field is left empty, all readable forums will be listed.',
	// Content
	'RSS_FORUM_CONTENT' => 'Forum Content settings',
	'RSS_FORUM_FIRST' => 'First message',
	'RSS_FORUM_FIRST_EXPLAIN' => 'Display or not the first post’s URL for all topics listed in the RSS feeds.<br/> By default, only the last post of each thread is listed. Displaying the first one as well means a bit more work for the server.',
	'RSS_FORUM_LAST' => 'Last message',
	'RSS_FORUM_LAST_EXPLAIN' => 'Display or not the last message for all topics listed in the RSS feeds.<br/>  By default, only the last post of each thread is listed. This option is useful if you want to only list the first post URL in RSS feeds.<br/>Please note: Setting First message to YES and last message to NO is the same as building a news feed.',
	'RSS_FORUM_RULES' => 'Display Forum Rules',
	'RSS_FORUM_RULES_EXPLAIN' => 'Display or not the Forum Rules in the RSS feeds.',
	// Reset settings
	'RSS_FORUM_RESET' => 'Forum RSS module',
	'RSS_FORUM_RESET_EXPLAIN' => 'Reset all the Forum RSS module options to default values.',
	'RSS_FORUM_MAIN_RESET' => 'Forums RSS Main',
	'RSS_FORUM_MAIN_RESET_EXPLAIN' => 'Reset to default all the options in the "RSS Feeds Settings" (main) tab of the forum RSS module.',
	'RSS_FORUM_CONTENT_RESET' => 'Forums RSS Content',
	'RSS_FORUM_CONTENT_RESET_EXPLAIN' => 'Reset to default all the Content options of the forum RSS module.',
	'RSS_FORUM_CACHE_RESET' => 'Forums RSS Cache',
	'RSS_FORUM_CACHE_RESET_EXPLAIN' => 'Reset to default all the caching options of the forum RSS module.',
	'RSS_FORUM_MODREWRITE_RESET' => 'Forums RSS URL rewriting',
	'RSS_FORUM_MODREWRITE_RESET_EXPLAIN' => 'Reset to default all the URL rewriting options of the forum RSS module.',
	'RSS_FORUM_GZIP_RESET' => 'Forums RSS Gunzip',
	'RSS_FORUM_GZIP_RESET_EXPLAIN' => 'Reset to default all the Gunzip options of the forum RSS module.',
	'RSS_FORUM_LIMIT_RESET' => 'Forums RSS Limits',
	'RSS_FORUM_LIMIT_RESET_EXPLAIN' => 'Reset to default all the Limits options of the forum RSS module.',
	'RSS_FORUM_SORT_RESET' => 'Forums RSS Sorting',
	'RSS_FORUM_SORT_RESET_EXPLAIN' => 'Reset to default all the Sorting options of the forum RSS module.',
	'RSS_FORUM_PAGINATION_RESET' => 'Forums RSS Pagination',
	'RSS_FORUM_PAGINATION_RESET_EXPLAIN' => 'Reset to default all the Pagination options of the forum RSS module.',
));
?>