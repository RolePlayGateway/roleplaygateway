<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_txt.php 204 2009-12-20 12:04:51Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* google_txt [English]
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
	'GOOGLE_TXT' => 'TXT Sitemap',
	'GOOGLE_TXT_EXPLAIN' => 'These are the parameter for the TXT Google sitemap module. It can fully integrate URL list from a text file (on url per line) in GYM sitemaps and take advantage of all the module’s features such as XSLt styling and caching.<br/> Some settings can be overridden depending on the Google sitemaps and main override settings.<br/>Each texte file added in the gym_sitemaps/sources/ directory will be taken into account once you will have cleared the module ACP cache, using the maintenance link above.<br/> Each URL list texte file must be composed of one full URL per line and will have to follow a basic pattern for file naming : <b>google_</b>txt_file_name<b>.txt</b>.<br />An entry will be created in the SitemapIndex with URL<b>example.com/sitemap.php?txt=txt_file_name</b> and <b>example.com/txt-txt_file_name.xml</b> when url rewritten.<br/> The name of the source file must must use alphanumerical characters (0-9a-z) plus both separators "_" and "-".<br/><u style="color:red;">Note :</u><br/> It is advised to cache this module’s sitemaps to prevent useless parsing of potentially big text files.',
	// Main
	'GOOGLE_TXT_CONFIG' => 'TXT Sitemap settings',
	'GOOGLE_TXT_CONFIG_EXPLAIN' => 'Some settings can be overridden depending on the Google sitemaps and main override settings.',
	'GOOGLE_TXT_RANDOMIZE' => 'Randomize',
	'GOOGLE_TXT_RANDOMIZE_EXPLAIN' => 'You can randomize URLs grabbed from the text file. Changing the order on a regular basis may help for crawling a bit. This option is as well handy for example when you would limit the urls to 1000 for this module and use text source files with 5000 urls, in such cases all the 5000 URLs will be regularly  displayed on the corresponding sitemap.',
	'GOOGLE_TXT_UNIQUE' => 'Check duplicate',
	'GOOGLE_TXT_UNIQUE_EXPLAIN' => 'Activate to make sure that if some URL appear more than one time in the text source, it will only display once in the sitemap.',
	'GOOGLE_TXT_FORCE_LASTMOD' => 'Last modification',
	'GOOGLE_TXT_FORCE_LASTMOD_EXPLAIN' => 'You can force a last modification time based on the cache duration cycle (even if cache is not activated) for all URLs in the sitemap. The module will as well compute priorities and change frequencies based on this last mod time. By default, no lastmod tag is added.',
	// Reset settings
	'GOOGLE_TXT_RESET' => 'TXT Sitemaps Module',
	'GOOGLE_TXT_RESET_EXPLAIN' => 'Reset to default all the sorting options of the TXT Sitemaps module.',
	'GOOGLE_TXT_MAIN_RESET' => 'TXT Sitemap Settings',
	'GOOGLE_TXT_MAIN_RESET_EXPLAIN' => 'Reset to default all the options in the "TXT Sitemap settings" (main) tab of the TXT Sitemap module.',
	'GOOGLE_TXT_CACHE_RESET' => 'TXT Sitemap Cache',
	'GOOGLE_TXT_CACHE_RESET_EXPLAIN' => 'Reset to default all the caching options of the TXT Sitemap module.',
	'GOOGLE_TXT_GZIP_RESET' => 'TXT Sitemap Gunzip',
	'GOOGLE_TXT_GZIP_RESET_EXPLAIN' => 'Reset to default all the Gunzip options of the TXT Sitemap module.',
	'GOOGLE_TXT_LIMIT_RESET' => 'TXT Sitemap Limit',
	'GOOGLE_TXT_LIMIT_RESET_EXPLAIN' => 'Reset to default all the Limit options of the TXT Sitemap module.',
));
?>