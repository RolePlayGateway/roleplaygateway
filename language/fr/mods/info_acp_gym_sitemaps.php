<?php
/**
*
* info_acp_gym_sitemaps [French]
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
	'ACP_GYM_MAIN' => 'Configuration générale',
	'ACP_GYM_GOOGLE_MAIN' => 'Sitemaps Google',
	'ACP_GYM_RSS_MAIN' => 'Flux RSS',
	'ACP_GYM_YAHOO_MAIN' => 'Yahoo! urllist.txt',
	'ACP_GYM_HTML_MAIN' => 'Pages HTML',
	'GYM_LOG_CONFIG_MAIN' => '<strong>Modification des paramètres de GYM Sitemaps &amp; RSS</strong><br/>&raquo; Configuration générale',
	'GYM_LOG_CONFIG_GOOGLE' => '<strong>Modification des paramètres de GYM Sitemaps &amp; RSS</strong><br/>&raquo; Sitemaps Google',
	'GYM_LOG_CONFIG_RSS' => '<strong>Modification des paramètres de GYM Sitemaps &amp; RSS</strong><br/>&raquo; Flux RSS',
	'GYM_LOG_CONFIG_HTML' => '<strong>Modification des paramètres de GYM Sitemaps &amp; RSS</strong><br/>&raquo; Plans de site HTML',
	'GYM_LOG_CONFIG_YAHOO' => '<strong>Modification des paramètres de GYM Sitemaps &amp; RSS</strong><br/>&raquo; Liste d’URLs Yahoo!',
	// Install Logs
	'SEO_LOG_INSTALL_GYM_SITEMAPS' => '<strong>Installation de GYM Sitemaps &amp; RSS V%s</strong>',
	'SEO_LOG_INSTALL_GYM_SITEMAPS_FAIL' => '<strong>Echec de l’installation de GYM Sitemaps &amp; RSS</strong><br/>%s',
	'SEO_LOG_UNINSTALL_GYM_SITEMAPS' => '<strong>Désinstallation de GYM Sitemaps &amp; RSS V%s</strong>',
	'SEO_LOG_UNINSTALL_GYM_SITEMAPS_FAIL' => '<strong>Echec de la désinstallation de GYM Sitemaps &amp; RSS</strong><br/>%s',
	'SEO_LOG_UPDATE_GYM_SITEMAPS' => '<strong>Mise à jour de GYM Sitemaps &amp; RSS vers V%s</strong>',
	'SEO_LOG_UPDATE_GYM_SITEMAPS_FAIL' => '<strong>Echec de la mise à jour de GYM Sitemaps &amp; RSS</strong><br/>%s',
));

?>