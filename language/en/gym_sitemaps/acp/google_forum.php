<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_forum.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* google_forum [English]
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
	'GOOGLE_FORUM' => 'Forum sitemaps',
	'GOOGLE_FORUM_EXPLAIN' => 'These are the settings for the forum Google sitemap module.<br/> Some are overridden depending on the Google sitemaps and main override settings.',
	'GOOGLE_FORUM_SETTINGS' => 'Forum sitemaps settings',
	'GOOGLE_FORUM_SETTINGS_EXPLAIN' => 'The following settings are specific to the forum Google sitemap module.',
	'GOOGLE_FORUM_STICKY_PRIORITY' => 'Sticky Priority',
	'GOOGLE_FORUM_STICKY_PRIORITY_EXPLAIN' => 'Sticky Priority (must be a number between 0.0 &amp; 1.0 inclusive).',
	'GOOGLE_FORUM_ANNOUCE_PRIORITY' => 'Announcement Priority',
	'GOOGLE_FORUM_ANNOUCE_PRIORITY_EXPLAIN' => 'Announcement Priority (must be a number between 0.0 &amp; 1.0 inclusive).',
	'GOOGLE_FORUM_GLOBAL_PRIORITY' => 'Global Announcement Priority',
	'GOOGLE_FORUM_GLOBAL_PRIORITY_EXPLAIN' => 'Global Announcement Priority (must be a number between 0.0 &amp; 1.0 inclusive).',
	'GOOGLE_FORUM_EXCLUDE' => 'Forum Exclusions',
	'GOOGLE_FORUM_EXCLUDE_EXPLAIN' => 'You can here exclude one or several forum from the sitemap listing.<br /><u>Note :</u> If this field is left empty, all public forums will be listed.',
	// Reset settings
	'GOOGLE_FORUM_RESET' => 'Forum sitemap module',
	'GOOGLE_FORUM_RESET_EXPLAIN' => 'Reset all the forum sitemap module options to default values.',
	'GOOGLE_FORUM_MAIN_RESET' => 'Forums sitemaps main',
	'GOOGLE_FORUM_MAIN_RESET_EXPLAIN' => 'Reset to default all the options in the "Forums sitemaps" (main) tab of the forum sitemap module.',
	'GOOGLE_FORUM_CACHE_RESET' => 'Forums sitemaps cache',
	'GOOGLE_FORUM_CACHE_RESET_EXPLAIN' => 'Reset to default all the caching options of the forum sitemap module.',
	'GOOGLE_FORUM_MODREWRITE_RESET' => 'Forums sitemaps URL rewriting',
	'GOOGLE_FORUM_MODREWRITE_RESET_EXPLAIN' => 'Reset to default all the URL rewriting options of the forum sitemap module.',
	'GOOGLE_FORUM_GZIP_RESET' => 'Forums sitemaps gunzip',
	'GOOGLE_FORUM_GZIP_RESET_EXPLAIN' => 'Reset to default all the gunzip options of the forum sitemap module.',
	'GOOGLE_FORUM_LIMIT_RESET' => 'Forums sitemaps limits',
	'GOOGLE_FORUM_LIMIT_RESET_EXPLAIN' => 'Reset to default all the limit options of the forum sitemap module.',
	'GOOGLE_FORUM_SORT_RESET' => 'Forums sitemaps Sorting',
	'GOOGLE_FORUM_SORT_RESET_EXPLAIN' => 'Reset to default all the sorting options of the forum sitemap module.',
	'GOOGLE_FORUM_PAGINATION_RESET' => 'Forums sitemaps pagination',
	'GOOGLE_FORUM_PAGINATION_RESET_EXPLAIN' => 'Reset to default all the pagination options of the forum sitemap module.',
));
?>