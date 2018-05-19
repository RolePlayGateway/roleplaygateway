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
* google_forum [French]
* Translated By: Mathieu M. & gowap [ www.phpbb-seo.com ]
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
	'GOOGLE_FORUM' => 'Sitemaps Forum',
	'GOOGLE_FORUM_EXPLAIN' => 'Il s’agit des paramètres du module Forum des Sitemaps Google.<br /> Certains paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage au niveau du type de rendu des Sitemaps Google et au niveau global.',
	'GOOGLE_FORUM_SETTINGS' => 'Paramètres des Sitemaps Forum',
	'GOOGLE_FORUM_SETTINGS_EXPLAIN' => 'Les paramètres suivants sont spécifiques au module Sitemaps Forum.',
	'GOOGLE_FORUM_STICKY_PRIORITY' => 'Priorité des Post-It',
	'GOOGLE_FORUM_STICKY_PRIORITY_EXPLAIN' => 'Priorité des Post-It (le nombre doit être compris entre 0.0 &amp; 1.0 inclus).',
	'GOOGLE_FORUM_ANNOUCE_PRIORITY' => 'Priorité des annonces',
	'GOOGLE_FORUM_ANNOUCE_PRIORITY_EXPLAIN' => 'Priorité des annonces (le nombre doit être compris entre 0.0 & 1.0 inclus).',
	'GOOGLE_FORUM_GLOBAL_PRIORITY' => 'Priorité des annonces générales',
	'GOOGLE_FORUM_GLOBAL_PRIORITY_EXPLAIN' => 'Priorité des annonces générales (le nombre doit être compris entre 0.0 & 1.0 inclus).',
	'GOOGLE_FORUM_EXCLUDE' => 'Exclusion de forums',
	'GOOGLE_FORUM_EXCLUDE_EXPLAIN' => 'Sélectionnez les forums pour lesquels vous ne souhaitez pas de plan de sitemap<br /><u>Note</u> :<br />Si ce champ est laissé vide, tous les forums publics seront listés.',
	// Reset settings
	'GOOGLE_FORUM_RESET' => 'Module Sitemaps Forum',
	'GOOGLE_FORUM_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut du module Sitemaps Forum.',
	'GOOGLE_FORUM_MAIN_RESET' => 'Configuration générale Sitemaps Forum',
	'GOOGLE_FORUM_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut dans l’onglet "Paramètres Sitemaps Forum" du module Sitemaps Forum.',
	'GOOGLE_FORUM_CACHE_RESET' => 'Cache Sitemaps Forum',
	'GOOGLE_FORUM_CACHE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au cache du module Sitemaps Forum.',
	'GOOGLE_FORUM_MODREWRITE_RESET' => 'Réécriture d’URL Sitemaps Forum',
	'GOOGLE_FORUM_MODREWRITE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la réécriture d’URL du module Sitemaps Forum.',
	'GOOGLE_FORUM_GZIP_RESET' => 'Compression GZip Sitemaps Forum',
	'GOOGLE_FORUM_GZIP_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la la compression GZip du module Sitemaps Forum.',
	'GOOGLE_FORUM_LIMIT_RESET' => 'Limites Sitemaps Forum',
	'GOOGLE_FORUM_LIMIT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées aux limites du module Sitemaps Forum.',
	'GOOGLE_FORUM_SORT_RESET' => 'Tri Sitemaps Forum',
	'GOOGLE_FORUM_SORT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au tri du module Sitemaps Forum.',
	'GOOGLE_FORUM_PAGINATION_RESET' => 'Pagination Sitemaps Forum',
	'GOOGLE_FORUM_PAGINATION_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la pagination du module Sitemaps Forum.',
));
?>