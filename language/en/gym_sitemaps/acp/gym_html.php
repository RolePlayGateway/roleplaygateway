<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_html.php 204 2009-12-20 12:04:51Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* gym_html [English]
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
	'HTML_MAIN' => 'HTML Settings',
	'HTML_MAIN_EXPLAIN' => 'These are the main setting for the HTML module.<br/>They can be applied to all the HTML modules depending on your HTML override settings.',
	// Linking setup
	'HTML_LINKS_ACTIVATION' => 'Forum Linking',
	'HTML_LINKS_MAIN' => 'Main links',
	'HTML_LINKS_MAIN_EXPLAIN' => 'Display or not main news and maps links in footer.<br/>This feature requires that main links display is activated in the main configuration.',
	'HTML_LINKS_INDEX' => 'Links on index',
	'HTML_LINKS_INDEX_EXPLAIN' => 'Display or not links to the available news and maps for each forum on the forum index. These links are added below the forum descriptions.<br/>This feature requires that links on index display is activated in the main configuration.',
	'HTML_LINKS_CAT' => 'Links on forum page',
	'HTML_LINKS_CAT_EXPLAIN' => 'Display or not links to the news and maps of the current forum. These links are added below the forum title.<br/>This feature requires that links on forum page display is activated in the main configuration.',
	// Reset settings
	'HTML_ALL_RESET' => 'ALL HTML modules',
	// Limits
	'HTML_RSS_NEWS_LIMIT' => 'Mains news page limit',
	'HTML_RSS_NEWS_LIMIT_EXPLAIN' => 'Number of items displayed on the main news page, gathered from the configured RSS source for the main new page.',
	'HTML_MAP_TIME_LIMIT' => 'Time limit for module main maps',
	'HTML_MAP_TIME_LIMIT_EXPLAIN' => 'Limit in days. The maximum age of the items taken into account when building the module main map page. Can be very useful to lower the server load on large data bases. Enter 0 for no limit',
	'HTML_CAT_MAP_TIME_LIMIT' => 'Time limit for category maps',
	'HTML_CAT_MAP_TIME_LIMIT_EXPLAIN' => 'Limit in days. The maximum age of the items taken into account when building the module category map pages. Can be very useful to lower the server load on large data bases. Enter 0 for no limit',
	'HTML_NEWS_TIME_LIMIT' => 'Time limits for News',
	'HTML_NEWS_TIME_LIMIT_EXPLAIN' => 'Limit in days. The maximum age of the items taken into account when building the module news page. Can be very useful to lower the server load on large data bases. Enter 0 for no limit',
	'HTML_CAT_NEWS_TIME_LIMIT' => 'Time limit for category news',
	'HTML_CAT_NEWS_TIME_LIMIT_EXPLAIN' => 'Limit in days. The maximum age of the items taken into account when building the module category news pages. Can be very useful to lower the server load on large data bases. Enter 0 for no limit',
	// sort
	'HTML_MAP_SORT_TITLE' => 'Map sorting',
	'HTML_NEWS_SORT_TITLE' => 'News sorting',
	'HTML_CAT_SORT_TYPE' => 'Sorting for category maps',
	'HTML_CAT_SORT_TYPE_EXPLAIN' => 'Following the same principle as above, this one applies to the module category maps pages, e.g. a forum map for the HTML forum module.',
	'HTML_NEWS_SORT_TYPE' => 'Sorting for news page',
	'HTML_NEWS_SORT_TYPE_EXPLAIN' => 'Following the same principle as above, this one applies to the module news page, e.g. the forum news page for the HTML forum module.',
	'HTML_CAT_NEWS_SORT_TYPE' => 'Sorting for category news pages',
	'HTML_CAT_NEWS_SORT_TYPE_EXPLAIN' => 'Following the same principle as above, this one applies to the module category news pages, e.g. a forum news page for the HTML forum module.',
	'HTML_PAGINATION_GEN' => 'Main Pagination',
	'HTML_PAGINATION_SPEC' => 'Module Pagination',
	'HTML_PAGINATION' => 'Site map pagination',
	'HTML_PAGINATION_EXPLAIN' => 'Activate pagination on the site map pages. You can decide to use only one, or several pages for your site maps.',
	'HTML_PAGINATION_LIMIT' => 'Item per page',
	'HTML_PAGINATION_LIMIT_EXPLAIN' => 'When site map pagination is activated, you can choose how many item to display per page.',
	'HTML_NEWS_PAGINATION' => 'News Pagination',
	'HTML_NEWS_PAGINATION_EXPLAIN' => 'Activate pagination on the news pages. You can decide to use only one, or several pages for your news pages.',
	'HTML_NEWS_PAGINATION_LIMIT' => 'News per page',
	'HTML_NEWS_PAGINATION_LIMIT_EXPLAIN' => 'When news pagination is activated, you can choose how many news to display per page.',
	'HTML_ITEM_PAGINATION' => 'Item pagination',
	'HTML_ITEM_PAGINATION_EXPLAIN' => 'You can here decide to output paginated links (when available) for the listed items. For example, the module can additionally output links of the forum’s topic pages.',
	// Basic settings
	'HTML_SETTINGS' => 'Basic settings',
	'HTML_C_INFO' => 'Copyright information',
	'HTML_C_INFO_EXPLAIN' => 'Info to display in the copyright meta tag for site maps and news pages. Default is the phpBB site name. This info will only be used if you installed the phpBB SEO dynamic meta tag mod.',
	'HTML_SITENAME' => 'Site name',
	'HTML_SITENAME_EXPLAIN' => 'The Site name to show in the site maps and news pages. Default is the phpBB site name.',
	'HTML_SITE_DESC' => 'Site description',
	'HTML_SITE_DESC_EXPLAIN' => 'The Site description to show in the site maps and news pages. Default is the phpBB site description.',
	'HTML_LOGO_URL' => 'Site logo',
	'HTML_LOGO_URL_EXPLAIN' => 'The image file to use as the site logo in the RSS feeds, in the gym_sitemaps/images/ directory.',
	'HTML_URL' => 'HTML URL',
	'HTML_URL_EXPLAIN' => 'Enter the full URL to your map.php file, e.g. http://www.example.com/eventual_dir/ if map.php is installed in http://www.example.com/eventual_dir/.<br/>This option is useful when phpBB is not installed in the domain’s root and you would like put the map.php file in the root level.',
	'HTML_RSS_NEWS_URL' => 'Mains news page RSS source',
	'HTML_RSS_NEWS_URL_EXPLAIN' => 'Enter here the full url to the RSS feed you want to display on the main news page, example http://www.example.com/gymrss.php?news&amp;digest to display all news from all RSS modules installed on the main HTML news page.<br />You can use an RSS 2.0 feed as a source for this page.',
	'HTML_STATS_ON_NEWS' => 'Display forum stats on news pages',
	'HTML_STATS_ON_NEWS_EXPLAIN' => 'Display, or not, forum stats on news pages.',
	'HTML_STATS_ON_MAP' => 'Display forum stats maps',
	'HTML_STATS_ON_MAP_EXPLAIN' => 'Display, or not, forum stats maps pages.',
	'HTML_BIRTHDAYS_ON_NEWS' => 'Display birthdays on news pages',
	'HTML_BIRTHDAYS_ON_NEWS_EXPLAIN' => 'Display, or not, birthdays on news pages.',
	'HTML_BIRTHDAYS_ON_MAP' => 'Display birthdays on news pages',
	'HTML_BIRTHDAYS_ON_MAP_EXPLAIN' => 'Display, or not, birthdays on news pages.',
	'HTML_DISP_ONLINE' => 'Display user online',
	'HTML_DISP_ONLINE_EXPLAIN' => 'Display, or not, the user online list on the site map and news pages.',
	'HTML_DISP_TRACKING' => 'Activate tracking',
	'HTML_DISP_TRACKING_EXPLAIN' => 'Activate, or not, item tracking (read / unread).',
	'HTML_DISP_STATUS' => 'Activate status',
	'HTML_DISP_STATUS_EXPLAIN' => 'Activate, or not, the item status system (Announcement, Stickies, locked etc ... ).',
	// Cache
	'HTML_CACHE' => 'Cache',
	'HTML_CACHE_EXPLAIN' => 'You can here define various caching options for the HTML mode. HTML caching is separated from the other modes (Google and RSS). This module uses the standard phpBB’s cache.<br/>This options thus cannot be inherited from the main  level, and only publicly visible content will be cached. This settings though, may be transmitted to the HTML modules depending on your HTML override settings.<br/><br/>Cache is separated into two types, one for each column in the output : The main column, containing the maps and news, and the optional one, which for example can be used to add a last active topic listing in the HTML forum module.',
	'HTML_MAIN_CACHE_ON' => 'Activate main column caching',
	'HTML_MAIN_CACHE_ON_EXPLAIN' => 'You can here activate / deactivate the site maps and news column caching.',
	'HTML_OPT_CACHE_ON' => 'Activate optional column caching',
	'HTML_OPT_CACHE_ON_EXPLAIN' => 'You can here activate / deactivate the optional column caching.',
	'HTML_MAIN_CACHE_TTL' => 'Main cache duration',
	'HTML_MAIN_CACHE_TTL_EXPLAIN' => 'Maximum amount of hours the main column cached file will be used before it will be updated. Each cached file will be updated every time someone will browse it after this duration was exceeded.',
	'HTML_OPT_CACHE_TTL' => 'Optional column cache duration',
	'HTML_OPT_CACHE_TTL_EXPLAIN' => 'Maximum amount of hours the optional column cached file will be used before it will be updated. Each cached file will be updated every time someone will browse it after this duration was exceeded.',
	// Auth settings
	'HTML_AUTH_SETTINGS' => 'Authorization settings',
	'HTML_ALLOW_AUTH' => 'Authorizations',
	'HTML_ALLOW_AUTH_EXPLAIN' => 'Activate the authorization for site map and news pages. If activated, logged in users will be able to browse private content and to view items from private forums if they have the proper authorization.',
	'HTML_ALLOW_NEWS' => 'Activate news',
	'HTML_ALLOW_NEWS_EXPLAIN' => 'Each module can have a news page listing the last X active items with their content, which can be filtered. For the forum, the forum news page is generally a page displaying the 10 last topic first posts digest coming from a selection of public and / or private forums.',
	'HTML_ALLOW_CAT_NEWS' => 'Activate category news',
	'HTML_ALLOW_CAT_NEWS_EXPLAIN' => 'Following the same principles as the module news pages, each module category can have a news page.',
	// Content
	'HTML_NEWS' => 'News settings',
	'HTML_NEWS_EXPLAIN' => 'Here you can set up various content filtering / formatting options for the news. <br/>They can be applied to all the HTML modules depending on your HTML override settings.',
	'HTML_NEWS_CONTENT' => 'News content settings',
	'HTML_SUMARIZE' => 'Digest Items',
	'HTML_SUMARIZE_EXPLAIN' => 'You can summarize the message content put in the news pages.<br/> The limit sets the maximum amount of sentences, words or characters, according to the method selected below. Enter 0 to output all of it.',
	'HTML_SUMARIZE_METHOD' => 'Digest Method',
	'HTML_SUMARIZE_METHOD_EXPLAIN' => 'You can select between three different methods to limit the message content in feeds.<br/> By number of lines, by number of words and by number of characters. BBcode tags and words won’t be broken.',
	'HTML_ALLOW_PROFILE' => 'Show Profiles',
	'HTML_ALLOW_PROFILE_EXPLAIN' => 'Item author name can be added to the output if desired.',
	'HTML_ALLOW_PROFILE_LINKS' => 'Profile link',
	'HTML_ALLOW_PROFILE_LINKS_EXPLAIN' => 'If author name is included in the output, you can decide to link it or not to the corresponding phpBB profile page.',
	'HTML_ALLOW_BBCODE' => 'Allow BBcodes',
	'HTML_ALLOW_BBCODE_EXPLAIN' => 'You may choose here to either parse and output or omit the BBcode.',
	'HTML_STRIP_BBCODE' => 'Strip BBcodes',
	'HTML_STRIP_BBCODE_EXPLAIN' => 'You can here set up a list of BBcodes to exclude from parsing.<br/>The format is simple : <br/><ul><li> <u>Comma separated list of BBcodes :</u> Delete BBcode tags, keep the content. <br/><u>Example :</u> <b>img,b,quote</b> <br/> In this example img, bold and quote BBcode won’t be parsed, the BBcode tags themselves will be deleted and the content inside the BBcode tags kept.</li><li> <u>Comma separated list of BBcodes with colon option :</u> Delete BBcode tags and decide about their content. <br/><u>Example :</u> <b>img:1,b:0,quote,code:1</b> <br/> In this example, img BBcode and the img link will be deleted, bold won’t be processed, but the bold-ed text will be kept, quote won’t be parsed, but their content will be kept, code BBcode and their content will be deleted from the output.</ul>The filter will work even if BBcode is empty. Handy to delete code tags content and img links from output for example.<br/>The filtering occurs before summarizing.<br/> The Magic parameter "all" (can be all:0 or all:1 to strip BBcode tags content as well) will take care of all at once.',
	'HTML_ALLOW_LINKS' => 'Allow active links',
	'HTML_ALLOW_LINKS_EXPLAIN' => 'You may choose here to either activate or not links used in items content.<br/> If deactivated, links and emails will be included in the content but won’t be clickable.',
	'HTML_ALLOW_EMAILS' => 'Allow Emails',
	'HTML_ALLOW_EMAILS_EXPLAIN' => 'You chose here to output "email AT domain DOT com" instead of "email@domain.com" in the items content.',
	'HTML_ALLOW_SMILIES' => 'Allow Smilies',
	'HTML_ALLOW_SMILIES_EXPLAIN' => 'You may choose here to either parse or ignore the smilies in content.',
	'HTML_ALLOW_SIG' => 'Allow signatures',
	'HTML_ALLOW_SIG_EXPLAIN' => 'You may choose here to either display or not the users signatures in content.',
	'HTML_ALLOW_MAP' => 'Activate the module map',
	'HTML_ALLOW_MAP_EXPLAIN' => 'You can here activate / deactivate the module site map.',
	'HTML_ALLOW_CAT_MAP' => 'Activate module category maps',
	'HTML_ALLOW_CAT_MAP_EXPLAIN' => 'You can here activate / deactivate the module category maps.',
));
?>