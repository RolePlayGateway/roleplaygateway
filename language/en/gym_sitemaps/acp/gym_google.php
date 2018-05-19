<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_google.php 204 2009-12-20 12:04:51Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* gym_google [English]
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
	'GOOGLE_MAIN' => 'Google Sitemaps Settings',
	'GOOGLE_MAIN_EXPLAIN' => 'Main settings for the Google sitemap module.<br/>They will applied to all the Google sitemaps modules by default.',
	// Linking setup
	'GOOGLE_LINKS_ACTIVATION' => 'Forum Linking',
	'GOOGLE_LINKS_MAIN' => 'Main links',
	'GOOGLE_LINKS_MAIN_EXPLAIN' => 'Display or not sitemapindex link in footer.<br/>This feature requires that main links display is activated in the main configuration.',
	'GOOGLE_LINKS_INDEX' => 'Links on index',
	'GOOGLE_LINKS_INDEX_EXPLAIN' => 'Display or not links to the available sitemaps for each forum on the forum index. These links are added below the forum descriptions.<br/>This feature requires that links on index display is activated in the main configuration.',
	'GOOGLE_LINKS_CAT' => 'Links on forum page',
	'GOOGLE_LINKS_CAT_EXPLAIN' => 'Display or not links to the sitemap of the current forum. These links are added below the forum title.<br/>This feature requires that links on forum page display is activated in the main configuration.',
	// Reset settings
	'GOOGLE_ALL_RESET' => '<b>All</b> Google sitemaps modules',
	'GOOGLE_URL' => 'Google Sitemaps URL',
	'GOOGLE_URL_EXPLAIN' => 'Enter the full URL to your sitemapIndex eg http://www.example.com/eventual_dir/ if sitemap.php is installed in http://www.example.com/eventual_dir/.<br/>This option is useful when phpBB is not installed in the domain\'s root and you would like to list URLs from the domain’s root level in your Google sitemaps.',
	'GOOGLE_PING' => 'Google Ping',
	'GOOGLE_PING_EXPLAIN' => 'Pings Google each time a sitemap gets refreshed.',
	'GOOGLE_THRESHOLD' => 'Sitemaps threshold',
	'GOOGLE_THRESHOLD_EXPLAIN' => 'Minimum amount of items to display a sitemap. For the forum, this means that only forum with more than this threshold topic will have a sitemap.',
	'GOOGLE_PRIORITIES' => 'Priority settings',
	'GOOGLE_DEFAULT_PRIORITY' => 'Default Priority',
	'GOOGLE_DEFAULT_PRIORITY_EXPLAIN' => 'The default priority for URLs listed in all the sitemaps; will be used unless additional options are made possible by  module (must be a number between 0.0 &amp; 1.0 inclusive)',
	'GOOGLE_XSLT' => 'XSLT Styling',
	'GOOGLE_XSLT_EXPLAIN' => 'Activates the XSL style-sheet to output user-friendly Google sitemaps with clickable links and more. This will only be effective after you will have cleared the Google sitemaps cache using the Maintenance link above.',
	'GOOGLE_LOAD_PHPBB_CSS' => 'Load phpBB CSS',
	'GOOGLE_LOAD_PHPBB_CSS_EXPLAIN' => 'The GYM sitemap module uses the phpBB3 templating system. The XSL stylesheets used to build the html output are compatible with phpBB3 styling.<br/>With this, you can apply the phpBB CSS on the XSL stylesheet instead of the default one. This way, all your theme personalizations such as background and font color or even images will be used in the Google sitemap styled output.<br/>This will only have effect after you will have cleared the RSS cache in the "Maintenance" menu.<br/>If the Google sitemaps style file are not present in the current style, the default style (always available, based on prosilver) will be used.<br/>Do not try to use prosilver templates with another style, the CSS most likely won\'t match.',
	// Auth settings
	'GOOGLE_AUTH_SETTINGS' => 'Authorization settings',
	'GOOGLE_ALLOW_AUTH' => 'Authorizations',
	'GOOGLE_ALLOW_AUTH_EXPLAIN' => 'Activate the authorization for Sitemaps. If activated, logged in users and bots will be able to browse private forum sitemaps if they have the proper authorization.',
	'GOOGLE_CACHE_AUTH' => 'Cache private sitemaps',
	'GOOGLE_CACHE_AUTH_EXPLAIN' => 'You can disable cache for non public sitemaps when allowed.<br/> Caching private sitemaps will increase the number of file cached. It should not be a problem, but you can decide to only cache public sitemaps here.',
));
?>