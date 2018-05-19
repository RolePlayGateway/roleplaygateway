<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: rss_forum.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* rss_forum [French]
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
	'RSS_FORUM' => 'Module RSS Forum',
	'RSS_FORUM_EXPLAIN' => 'Il s’agit des paramètres du module Forum des flux RSS.<br /> Certains paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage au niveau du type de rendu des flux RSS et au niveau global.',
	'RSS_FORUM_ALTERNATE' => 'Liens alternate RSS',
	'RSS_FORUM_ALTERNATE_EXPLAIN' => 'Afficher ou nons les liens alternate RSS des forums dans la barre de navigation des navigateurs',
	'RSS_FORUM_EXCLUDE' => 'Exclusion de forums',
	'RSS_FORUM_EXCLUDE_EXPLAIN' => 'Sélécttionnez les forums pour lesquels vous ne souhaitez pas de flux RSS<br /><u>Note</u> :<br />Si ce champ est laissé vide, tous les forums accessibles seront pris en compte.',
	// Content
	'RSS_FORUM_CONTENT' => 'Paramètres du contenu module RSS Forum',
	'RSS_FORUM_FIRST' => 'Premier message',
	'RSS_FORUM_FIRST_EXPLAIN' => 'Affiche ou non l’URL du premier message de chaque sujet listé dans les flux RSS.<br /> Par défaut seul le dernier message de chaque sujet est listé. Afficher également le premier message implique une charge serveur plus importante.',
	'RSS_FORUM_LAST' => 'Dernier message',
	'RSS_FORUM_LAST_EXPLAIN' => 'Affiche ou non le dernier message de chaque sujet listé dans les flux RSS.<br /> Par défaut seul le dernier message de chaque sujet est listé. Cette option est utile si vous souhaitez uniquement lister l’URL du premier message dans les flux RSS.<br />Veuillez noter que paramétrer "Premier message" sur OUI et "Dernier message" sur NON revient au même que de créer un flux de type Actualités.',
	'RSS_FORUM_RULES' => 'Afficher le règlement du forum',
	'RSS_FORUM_RULES_EXPLAIN' => 'Affiche ou non le règlement du forum dans les flux RSS.',
	// Reset settings
	'RSS_FORUM_RESET' => 'Module RSS Forum',
	'RSS_FORUM_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut du module RSS Forum.',
	'RSS_FORUM_MAIN_RESET' => 'Configuration générale RSS Forum',
	'RSS_FORUM_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut dans l’onglet "Paramètres des flux RSS" du module RSS Forum.',
	'RSS_FORUM_CONTENT_RESET' => 'Paramètres du contenu module RSS forum',
	'RSS_FORUM_CONTENT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut des paramètres du contenu du module RSS Forum.',
	'RSS_FORUM_CACHE_RESET' => 'Cache RSS Forum',
	'RSS_FORUM_CACHE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au cache du module RSS Forum.',
	'RSS_FORUM_MODREWRITE_RESET' => 'Réécriture d’URL RSS Forum',
	'RSS_FORUM_MODREWRITE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la réécriture d’URL du module RSS Forum.',
	'RSS_FORUM_GZIP_RESET' => 'Compression GZip RSS Forum',
	'RSS_FORUM_GZIP_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la compression GZip du module RSS Forum.',
	'RSS_FORUM_LIMIT_RESET' => 'Limites RSS Forum',
	'RSS_FORUM_LIMIT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées aux limites du module RSS Forum.',
	'RSS_FORUM_SORT_RESET' => 'Tri RSS Forum',
	'RSS_FORUM_SORT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au tri du module RSS Forum.',
	'RSS_FORUM_PAGINATION_RESET' => 'Pagination RSS Forum',
	'RSS_FORUM_PAGINATION_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la pagination du module RSS Forum.',
));
?>