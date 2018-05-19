<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_common.php 204 2009-12-20 12:04:51Z dcz $
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
	// Main
	'ALL' => 'All',
	'MAIN' => 'GYM Sitemaps',
	'MAIN_MAIN_RESET' => 'GYM sitemaps main options',
	'MAIN_MAIN_RESET_EXPLAIN' => 'Reset all the GYM main options to default values.',
	// Linking setup
	'GYM_LINKS_ACTIVATION' => 'Forum Linking',
	'GYM_LINKS_MAIN' => 'Main links',
	'GYM_LINKS_MAIN_EXPLAIN' => 'Display or not links to main GYM page in footer : SitemapIndex, main RSS feed and feed list page, main map and new page.',
	'GYM_LINKS_INDEX' => 'Links on index',
	'GYM_LINKS_INDEX_EXPLAIN' => 'Display or not links to the available GYM pages for each forum on the forum index. These links are added below the forum descriptions.',
	'GYM_LINKS_CAT' => 'Links on forum page',
	'GYM_LINKS_CAT_EXPLAIN' => 'Display or not links to the available GYM pages on a forum page. These links are added below the forum title.',
	// Google sitemaps
	'GOOGLE' => 'Google',
	// Reset settings
	'GOOGLE_MAIN_RESET' => 'Google Sitemap main options',
	'GOOGLE_MAIN_RESET_EXPLAIN' => 'Reset all the Google Sitemap main options to default values.',
	// RSS feeds
	'RSS' => 'RSS',
	'RSS_ALTERNATE' => 'RSS alternate links',
	'RSS_ALTERNATE_EXPLAIN' => 'Display or not RSS alternate links in browsers navigation bar',
	'RSS_LINKING_TYPE' => 'RSS Linking Type',
	'RSS_LINKING_TYPE_EXPLAIN' => 'The type of feed to display among the forum pages.<br/>Can be set to :<br/><b>&bull; News Feeds with or without content</b><br/>Items are displayed in the creation date order, with or without content,<br/><b>&bull; Regular Feeds with or without content</b><br/>Items are displayed in the last activity date order, with or without content.<br/>This only affects the link displayed, not the feeds actually available.',
	'RSS_LINKING_NEWS' => 'News Feeds',
	'RSS_LINKING_NEWS_DIGEST' => 'News Feeds with content',
	'RSS_LINKING_REGULAR' => 'Regular Feeds',
	'RSS_LINKING_REGULAR_DIGEST' => 'Regular Feeds with content',
	// Reset settings
	'RSS_MAIN_RESET' => 'RSS main options',
	'RSS_MAIN_RESET_EXPLAIN' => 'Reset all the RSS main options to default values.',
	'YAHOO' => 'Yahoo',
	// HTML
	'HTML_MAIN_RESET' => 'Global HTML options',
	'HTML_MAIN_RESET_EXPLAIN' => 'Reset all the HTML maps and news main options to default values.',
	'HTML' => 'Html',

	// GYM authorisation array
	'GYM_AUTH_ADMIN' => 'Admin',
	'GYM_AUTH_GLOBALMOD' => 'Global moderators',
	'GYM_AUTH_REG' => 'Logged in',
	'GYM_AUTH_GUEST' => 'Guests',
	'GYM_AUTH_ALL' => 'All',
	'GYM_AUTH_NONE' => 'None',
	// XSLT
	'GYM_STYLE' => 'Styling',

	// Cache status
	'SEO_CACHE_FILE_TITLE' => 'Cache Status',
	'SEO_CACHE_STATUS' => 'File cache is configured at: <b>%s</b>',
	'SEO_CACHE_FOUND' => 'File cache found.',
	'SEO_CACHE_NOT_FOUND' => 'File cache was not found.',
	'SEO_CACHE_WRITABLE' => 'File cache is writeable.',
	'SEO_CACHE_UNWRITABLE' => 'File cache is <b>not</b> writeable.  Please CHMOD the cache folder to 0777.',

	// Mod Rewrite type
	'ACP_SEO_SIMPLE' => 'Simple',
	'ACP_SEO_MIXED' => 'Intermediate',
	'ACP_SEO_ADVANCED' => 'Advanced',
	'ACP_PHPBB_SEO_VERSION' => 'Version',
	'ACP_SEO_SUPPORT_FORUM' => 'Support Forum',
	'ACP_SEO_RELEASE_THREAD' => 'Subject to availability',
	'ACP_SEO_REGISTER_TITLE' => 'Register',
	'ACP_SEO_REGISTER_UPDATE' => 'notified about updates',
	'ACP_SEO_REGISTER_MSG' => 'You may want to %1$s to be %2$s',

	// Maintenance
	'GYM_MAINTENANCE' => 'Maintenance',
	'GYM_MODULE_MAINTENANCE' => '%1$s maintenance',
	'GYM_MODULE_MAINTENANCE_EXPLAIN' => 'Here you can manage the cached files used by the %1$s modules.<br/> There are two types: the one used to store the data outputted on the public pages, and the ones used to build each module’s ACP. You can delete the module’s ACP cache if you check the clear cache option; default is clearing the content cache for the selected modules.',
	'GYM_CLEAR_CACHE' => 'Clear %1$s cache',
	'GYM_CLEAR_CACHE_EXPLAIN' => 'You can here clear the cached files for the %1$s module. These cached files contains the data used to build the %1$s .<br/>It can be useful if you want to force the cache update.',
	'GYM_CLEAR_ACP_CACHE' => 'Clear %1$s ACP',
	'GYM_CLEAR_ACP_CACHE_EXPLAIN' => 'You can choose to clear the %1$s ACP cached setup instead. These cached files contains the data used to build the %1$s ACP.<br/>It can be useful to activate new options that may have been added to the modules of this  type.',
	'GYM_CACHE_CLEARED' => 'Clear cache success in : ',
	'GYM_CACHE_NOT_CLEARED' => 'An error occurred while clearing the cache, please check the folder permissions (CHMOD 0666 or 0777).<br/>The folder currently set up for caching is : ',
	'GYM_FILE_CLEARED' => 'File(s) erased: ',
	'GYM_CACHE_ACCESSED' => 'The caching folder was properly accessed, but no files were deleted: ',
	'MODULE_CACHE_CLEARED' => 'ACP module cache cleared with success, if you just uploaded a module, it’s ACP will show up now.',

	// set defaults
	'GYM_SETTINGS' => 'Settings',
	'GYM_RESET_ALL' => 'Reset All',
	'GYM_RESET_ALL_EXPLAIN' => 'If you check the option, all the above options sets will be reset to default.',
	'GYM_RESET' => 'Reset %1$s config',
	'GYM_RESET_EXPLAIN' => 'Below you can reset %1$s modules config, either a whole module at once or only a given set of module config.',

	'GYM_INSTALL' => 'Install',
	'GYM_MODULE_INSTALL' => 'Install %1$s module',
	'GYM_MODULE_INSTALL_EXPLAIN' => 'Below you can activate / deactivate the %1$s module.<br/>If you just uploaded a module, you need to activate it before you will be able to use it.<br/>If you cannot see new module, try clearing the ACP module’s cache in the maintenance page.',

	// Titles
	'GYM_MAIN' => 'GYM Sitemaps Settings',
	'GYM_MAIN_EXPLAIN' => 'These are the setting common to all type of output and to all modules.<br/> They can be applied to all type of outputs (html, RSS, Google sitemaps, Yahoo! url list) and/or to all modules depending on your override settings.',
	'MAIN_MAIN' => 'GYM Sitemaps Overview',
	'MAIN_MAIN_EXPLAIN' => 'GYM sitemaps is a very flexible and Search Engine Optimized phpBB module. It will allow you to build Google sitemaps, RSS 2.0 feeds, Yahoo! URL lists and html sitemaps for your forum as well as for any part of your website thanks to its modularity.<br/><br/> Each  type (Google, RSS, html & Yahoo) can grab items to list from several applications installed on your site (forum, album etc ...) using a dedicated module.<br/>You can activate / deactivate modules using the install link in each  type ACP, each module has its own configuration pages.<br/><br/>Make sure you check the %1$s, support is provided in the %2$s.<br/>General SEO support and discussion is as well provided on %3$s<br/>%4$s<br/>Enjoy ;-)',

	'GYM_GOOGLE' => 'Google Sitemaps',
	'GYM_GOOGLE_EXPLAIN' => 'These are the setting common to all Google sitemaps modules (forum, custom etc ...).<br/> They can be applied to all Google sitemaps modules depending on your override settings for this type of output and the main ones.',
	'GYM_RSS' => 'RSS feeds',
	'GYM_RSS_EXPLAIN' => 'These are the setting common to all RSS feeds modules (forum, custom etc ...).<br/> They can be applied to all RSS feeds modules depending on your override settings for this type of output and the main ones.',
	'GYM_HTML' => 'HTML Pages',
	'GYM_HTML_EXPLAIN' => 'These are the setting common to all HTML modules (forum, custom etc ...).<br/> They can be applied to all HTML modules depending on your override settings for this type of output and the main ones.',
	'GYM_MODULES_INSTALLED' => 'Active module(s)',
	'GYM_MODULES_UNINSTALLED' => 'Non active module(s)',

	// Overrides
	'GYM_OVERRIDE_GLOBAL' => 'Global',
	'GYM_OVERRIDE_OTYPE' => 'Output Type',
	'GYM_OVERRIDE_MODULE' => 'Module',

	// override messages
	'GYM_OVERRIDED_GLOBAL' => 'This option is currently overridden at the top level (Main configuration)',
	'GYM_OVERRIDED_OTYPE' => 'This option is currently overridden at the  type level',
	'GYM_OVERRIDED_MODULE' => 'This option is currently overridden at the module level',
	'GYM_OVERRIDED_VALUE' => 'The value currently taken into account is : ',
	'GYM_OVERRIDED_VALUE_NOTHING' => 'nothing',
	'GYM_COULD_OVERRIDE' => 'This option could be overridden but currently isn’t.',

	// Overridable / common options
	'GYM_CACHE' => 'Cache',
	'GYM_CACHE_EXPLAIN' => 'Here you can set up various caching options. Remember that these settings may be overridden depending on your override settings.',
	'GYM_MOD_SINCE' => 'Activate Modified Since',
	'GYM_MOD_SINCE_EXPLAIN' => 'The module will ask the browser if it already has an up-to-date version of the page in its cache before resending the content.<br /><u>NOTE :</u> This option will concern all types of output.',
	'GYM_CACHE_ON' => 'Activate Caching',
	'GYM_CACHE_ON_EXPLAIN' => 'You can activate / deactivate caching for this module.',
	'GYM_CACHE_FORCE_GZIP' => 'Force Cache compression',
	'GYM_CACHE_FORCE_GZIP_EXPLAIN' => 'Allow you to force gunzip compression for cached files despite the use of gunzip. This can help out a little if you miss web space, but will be a bit more work for the server to uncompress the file before it is sent to the browser.',
	'GYM_CACHE_MAX_AGE' => 'Cache duration',
	'GYM_CACHE_MAX_AGE_EXPLAIN' => 'Maximum amount of hours a cached file will be used before it will be updated. Each cached file will be updated every time someone will browse it after this duration was exceeded when auto regen is on. If not, the cache will only be updated upon demand in ACP.',
	'GYM_CACHE_AUTO_REGEN' => 'Cache auto update',
	'GYM_CACHE_AUTO_REGEN_EXPLAIN' => 'If you activate the cache auto update, outputted lists will be updated once the cache will have expired, if not, you will have to manually clear your cached files in the maintenance link above to see new URLs in your lists.',
	'GYM_SHOWSTATS' => 'Cache Statistics',
	'GYM_SHOWSTATS_EXPLAIN' => 'Output or not the generation statistics in the source code.<br /><u>NOTE :</u> The duration is the time needed to build the page. This step is not repeated when writing from cache.',
	'GYM_CRITP_CACHE' => 'Encode cache filenames',
	'GYM_CRITP_CACHE_EXPLAIN' => 'Encrypt the cache file names or not. It is safer to keep the cache filenames encrypted, but it can be handy to check the unencrypted filenames for debugging.<br /><u>NOTE :</u> This option will concern all type of cached files.',

	'GYM_MODREWRITE' => 'URL rewriting',
	'GYM_MODREWRITE_EXPLAIN' => 'Here you can set up various URL rewriting options. Remember that these settings may be overridden depending on your override settings.',
	'GYM_MODREWRITE_ON' => 'Activate URL rewriting',
	'GYM_MODREWRITE_ON_EXPLAIN' => 'This activates URL rewriting for the module links.<br /><u>NOTE :</u> You MUST use an Apache server with the mod_rewrite module loaded or an IIS server running the isapi_rewrite module AND to properly set up the module’s rewrite rules in your .htaccess (or httpd.ini with IIS ).',
	'GYM_ZERO_DUPE_ON' => 'Activate the Zero Duplicate',
	'GYM_ZERO_DUPE_ON_EXPLAIN' => 'This activates the Zero Duplicate for the module links.<br /><u>NOTE :</u> Redirections will only occur when (re)generating the cache in this version.',
	'GYM_MODRTYPE' => 'URL rewriting type',
	'GYM_MODRTYPE_EXPLAIN' => 'These options are overridden by the use of the phpBB SEO mod rewrite (auto detection ).<br/>Four levels of url rewriting can be set up here: None, Simple, Mixed and Advanced :<br/><ul><li><b>None :</b> No URL rewriting;<br></li><li><b>Simple :</b>Static URL rewriting for all links, no title injection;<br></li><li><b>Mixed :</b> Forum and category titles are injected in URLs, but topic titles remain statically rewritten;<br></li><li><b>Advanced :</b> All titles are injected in URLs;</li></ul>',

	'GYM_GZIP' => 'GUNZIP',
	'GYM_GZIP_EXPLAIN' => 'Here you can set up various gunzip options. Remember that these settings may be overridden depending on your override settings.%1$s',
	'GYM_GZIP_FORCED' => '<br/><b style="color:red;">NOTE :</b> Gun-zip compressions is activated in phpBB config. It is thus forced in the module.',
	'GYM_GZIP_CONFIGURABLE' => '<br/><b style="color:red;">NOTE :</b> Gun-zip compressions is not activated in phpBB config. You can set the below options as you wish.',
	'GYM_GZIP_ON' => 'Activate gunzip',
	'GYM_GZIP_ON_EXPLAIN' => 'This activates gunzip compression on the output. This can significantly lower the amount of data transmitted to the browser and thus the time required to transmit the content.',
	'GYM_GZIP_EXT' => 'Gunzip suffix',
	'GYM_GZIP_EXT_EXPLAIN' => 'You can here chose to use or not the .gz suffix in the module URLs. This only applies when gunzip and URL rewriting are activated.',
	'GYM_GZIP_LEVEL' => 'Gunzip Compression level',
	'GYM_GZIP_LEVEL_EXPLAIN' => 'Integer between 1 and 9, 9 being the most compression. It’s usually not worth it to go over 6.<br /><u>NOTE :</u> This option will concern all types of output.',

	'GYM_LIMIT' => 'Limits',
	'GYM_LIMIT_EXPLAIN' => 'Here you can set the limit to apply when building the output : number of url outputted, SQL cycling (amount of item queried at once) and age of items listed.<br/>Remember that these settings may be overridden depending on your override settings.',
	'GYM_URL_LIMIT' => 'Item Limits',
	'GYM_URL_LIMIT_EXPLAIN' => 'The maximum amount of item to output.',
	'GYM_SQL_LIMIT' => 'SQL cycling',
	'GYM_SQL_LIMIT_EXPLAIN' => 'For all types of output, except html, SQL queries are split into several to be able to list large amount of items without running too heavy queries.<br/>Define here the amount of item to query at once. The number of SQL queries will be the number of item listed divided by this cycle.',
	'GYM_TIME_LIMIT' => 'Time Limits',
	'GYM_TIME_LIMIT_EXPLAIN' => 'Limit in days. The maximum age of the items taken into account when building the  lists. Can be very useful to lower the server load on large data bases. Enter 0 for no limit',

	'GYM_SORT' => 'Sorting',
	'GYM_SORT_EXPLAIN' => 'Here you can chose how to sort the output.<br/>Remember that these settings may be overridden depending on your override settings.',
	'GYM_SORT_TYPE' => 'Default Sorting',
	'GYM_SORT_TYPE_EXPLAIN' => 'All outputted links are sorted by default by last activity (Descending). <br /> You can set this to Ascending for example if you wish to make it easier for Search engines to find links to old content.<br/>Remember that these settings may be overridden depending on your override settings.',

	'GYM_PAGINATION' => 'Pagination',
	'GYM_PAGINATION_EXPLAIN' => 'Here you can set up various pagination options. Remember that these settings may be overridden depending on your override settings.',
	'GYM_PAGINATION_ON' => 'Activate Pagination',
	'GYM_PAGINATION_ON_EXPLAIN' => 'You can here decide to output paginated links (when available) for the listed items. For example, the module can additionally output links of the forum’s topic pages.',
	'GYM_LIMITDOWN' => 'Pagination: Lower Limit',
	'GYM_LIMITDOWN_EXPLAIN' => 'Enter here how many paginated pages, starting from the first page, to output.',
	'GYM_LIMITUP' => 'Pagination: Upper Limit',
	'GYM_LIMITUP_EXPLAIN' => 'Enter here how many paginated pages, starting from the last one, to output.',

	'GYM_OVERRIDE' => 'Override',
	'GYM_OVERRIDE_EXPLAIN' => 'GYM sitemaps is fully modular. Each output type (Google, RSS ...) uses its own output modules corresponding to the type of item to list. For example, the first module for all types of output is the forum module, listing items from the forum.<br/> Many options, such as URL rewriting, caching, gunzip compression etc ..., are repeated on several levels of the GYM sitemaps ACP. This allow you to use different settings for the same option depending on the type of output and the output module. But it can occur that you would prefer to, for example, activate URL rewriting on all the GYM sitemaps module at once (all outputs types and all modules).<br/> That’s what the Override setting will allow you to do for many types of settings. <br/>The inheritance process goes from the highest level of settings (Main configuration) to the output type level (Google, RSS ...) and ends at the lowest level : the output modules (forum, album ...)<br/>Overrinding settings can take three values :<br/><ul><li><b>Global :</b> The Main settings will be used;<br></li><li><b>Output Type :</b> The output type settings will be used for its modules;<br></li><li><b>Module :</b> The lowest available setting will be used, e.g., the module’s one first, and if not set, the output type one and so on up to the global setting if available.</li></ul>',
	'GYM_OVERRIDE_ON' => 'Activate Main Override',
	'GYM_OVERRIDE_ON_EXPLAIN' => 'You can here Activate / Deactivate the main overrinding. Deactivating is the same as setting all overrides to "module", letting the output type’s override settings to set the module override.',
	'GYM_OVERRIDE_MAIN' => 'Default Overriding',
	'GYM_OVERRIDE_MAIN_EXPLAIN' => 'Set override level for the other types of settings a module could use.',
	'GYM_OVERRIDE_CACHE' => 'Cache Overriding',
	'GYM_OVERRIDE_CACHE_EXPLAIN' => 'What level of overriding to set for the caching options.',
	'GYM_OVERRIDE_GZIP' => 'Gunzip Overriding',
	'GYM_OVERRIDE_GZIP_EXPLAIN' => 'What level of overriding to set for the gunzip options.',
	'GYM_OVERRIDE_MODREWRITE' => 'URL Rewriting Overriding',
	'GYM_OVERRIDE_MODREWRITE_EXPLAIN' => 'What level of overriding to set for the URL rewriting options.',
	'GYM_OVERRIDE_LIMIT' => 'Limit Overriding',
	'GYM_OVERRIDE_LIMIT_EXPLAIN' => 'What level of overriding to set for the limit options.',
	'GYM_OVERRIDE_PAGINATION' => 'Pagination Overriding',
	'GYM_OVERRIDE_PAGINATION_EXPLAIN' => 'What level of overriding to set for the pagination options.',
	'GYM_OVERRIDE_SORT' => 'Sorting Overriding',
	'GYM_OVERRIDE_SORT_EXPLAIN' => 'What level of overriding to set for the sorting options.',

	// Mod rewrite
	'GYM_MODREWRITE_ADVANCED' => 'Advanced',
	'GYM_MODREWRITE_MIXED' => 'Mixed',
	'GYM_MODREWRITE_SIMPLE' => 'Simple',
	'GYM_MODREWRITE_NONE' => 'None',

	// Sorting
	'GYM_ASC' => 'Ascending',
	'GYM_DESC' => 'Descending',

	// Other
	// robots.txt
	'GYM_CHECK_ROBOTS' => 'CHeck robots.txt disallows',
	'GYM_CHECK_ROBOTS_EXPLAIN' => 'Check and apply robots.txt rules (if any) to the URL list. The module will automatically acknowledge the robots.txt updates.<br />This option is very handy for XML and TXT import, when we cannot be sure about the URL list consistency.<br/><br /><u>Note</u> :<br />This option will imply more work on the source file, it is recommended to use it when caching is activated.',
	// summarize method
	'GYM_METHOD_CHARS' => 'By characters',
	'GYM_METHOD_WORDS' => 'By words',
	'GYM_METHOD_LINES' => 'By lines',
));
?>
