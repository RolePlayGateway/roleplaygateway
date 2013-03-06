<?php
/**
*
* info_acp_gym_sitemaps [English]
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: info_acp_gym_sitemaps.php 131 2009-10-25 12:03:44Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

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
	'ACP_GYM_SITEMAPS' => 'GYM Sitemaps &amp; RSS',
	'ACP_GYM_MAIN' => 'Main configuration',
	'ACP_GYM_GOOGLE_MAIN' => 'Google Sitemaps',
	'ACP_GYM_RSS_MAIN' => 'RSS Feeds',
	'ACP_GYM_YAHOO_MAIN' => 'Yahoo! url list',
	'ACP_GYM_HTML_MAIN' => 'HTML sitemaps',
	'GYM_LOG_CONFIG_MAIN' => '<strong>Altered GYM sitemaps &amp; RSS settings</strong><br/>&raquo; Main settings',
	'GYM_LOG_CONFIG_GOOGLE' => '<strong>Altered GYM sitemaps &amp; RSS settings</strong><br/>&raquo; Google sitemaps',
	'GYM_LOG_CONFIG_RSS' => '<strong>Altered GYM sitemaps &amp; RSS settings</strong><br/>&raquo; RSS Feeds',
	'GYM_LOG_CONFIG_HTML' => '<strong>Altered GYM sitemaps &amp; RSS settings</strong><br/>&raquo; HTML sitemaps',
	'GYM_LOG_CONFIG_YAHOO' => '<strong>Altered GYM sitemaps &amp; RSS settings</strong><br/>&raquo; Yahoo! URL lists',
	// Install Logs
	'SEO_LOG_INSTALL_GYM_SITEMAPS' => '<strong>GYM Sitemaps &amp; RSS V%s Installed</strong>',
	'SEO_LOG_INSTALL_GYM_SITEMAPS_FAIL' => '<strong>GYM Sitemaps &amp; RSS install attempt failed</strong><br/>%s',
	'SEO_LOG_UNINSTALL_GYM_SITEMAPS' => '<strong>GYM Sitemaps &amp; RSS V%s Un-installed</strong>',
	'SEO_LOG_UNINSTALL_GYM_SITEMAPS_FAIL' => '<strong>GYM Sitemaps &amp; RSS Un-install attempt failed</strong><br/>%s',
	'SEO_LOG_UPDATE_GYM_SITEMAPS' => '<strong>Updated GYM Sitemaps &amp; RSS to V%s</strong>',
	'SEO_LOG_UPDATE_GYM_SITEMAPS_FAIL' => '<strong>GYM Sitemaps &amp; RSS Update attempt failed</strong><br/>%s',
));

?>