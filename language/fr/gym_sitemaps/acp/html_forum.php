<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: html_forum.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* html_forum [French]
* Translated By: dcz [ www.phpbb-seo.com ]
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
	'HTML_FORUM' => 'Module HTML Forum',
	'HTML_FORUM_EXPLAIN' => 'Il s’agit des paramètres du module Forum HTML.<br /> Certains paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage au niveau du type de rendu des pages HTML et au niveau global.',
	'HTML_FORUM_EXCLUDE' => 'Exclusion de forums',
	'HTML_FORUM_EXCLUDE_EXPLAIN' => 'Sélécttionnez les forums pour lesquels vous ne souhaitez pas de plan de site ni de news<br /><u>Note</u> :<br />Si ce champ est laissé vide, tous les forums accessibles seront pris en compte.',
	'HTML_FORUM_ALLOW_NEWS' => 'News du forum',
	'HTML_FORUM_ALLOW_NEWS_EXPLAIN' => 'La page de news du forum est une page regroupant un ou plusieurs sujets, résumés ou non, provenant d’un ou plusieurs forums que vous pouvez séléctionner ci-dessous.',
	'HTML_FORUM_ALLOW_CAT_NEWS' => 'Activer les news des forums',
	'HTML_FORUM_ALLOW_CAT_NEWS_EXPLAIN' => 'Active une page de news pour chaque forum non exclus.',
	'HTML_FORUM_NEWS_IDS' => 'Forum source des news',
	'HTML_FORUM_NEWS_IDS_EXPLAIN' => 'Vous pouvez définire un ou plusieurs forums, même privés, comme cource de votre page news.<br />Sélécttionnez le ou les forums source.<br /><u>Note</u> :<br />Si ce champ est laissé vide, tous les forums accessibles seront pris en compte.',
	'HTML_FORUM_LTOPIC' => 'Liste des derniers sujets optionnelle',
	'HTML_FORUM_INDEX_LTOPIC' => 'Affichage sur le plan du forum',
	'HTML_FORUM_INDEX_LTOPIC_EXPLAIN' => 'Vous pouvez activer ou non l’affichage de la liste des derniers sujets actifs du forums ou des forums séléctionés plus bas dans le plan du forum.<br/>Entrez le nombre de derniers sujets à afficher, 0 pour désactiver totallement.',
	'HTML_FORUM_CAT_LTOPIC' => 'Affichage sur le plan de chaque forum',
	'HTML_FORUM_CAT_LTOPIC_EXPLAIN' => 'Vous pouvez activer ou non l’affichage de la liste des derniers sujets actifs du forums ou des forums séléctionés plus bas dans le plan de chaque forum.<br/>Entrez le nombre de derniers sujets à afficher, 0 pour désactiver totallement.',
	'HTML_FORUM_NEWS_LTOPIC' => 'Affichage sur la page news du forum',
	'HTML_FORUM_NEWS_LTOPIC_EXPLAIN' => 'Vous pouvez activer ou non l’affichage de la liste des derniers sujets actifs du forums ou des forums séléctionés plus bas dans la page news du forum.<br/>Entrez le nombre de derniers sujets à afficher, 0 pour désactiver totallement.',
	'HTML_FORUM_CAT_NEWS_LTOPIC' => 'Affichage sur la page news de chaque forums',
	'HTML_FORUM_CAT_NEWS_LTOPIC_EXPLAIN' => 'Vous pouvez activer ou non l’affichage de la liste des derniers sujets actifs du forums ou des forums séléctionés plus bas dans la page news de chaque forum.<br/>Entrez le nombre de derniers sujets à afficher, 0 pour désactiver totallement.',
	'HTML_FORUM_LTOPIC_PAGINATION' => 'Pagination des derniers sujets',
	'HTML_FORUM_LTOPIC_PAGINATION_EXPLAIN' => 'Afficher ou non la pagination des sujets dans la liste optionnelle des derniers sujets.',
	'HTML_FORUM_LTOPIC_EXCLUDE' => 'Exclusion de la liste des derniers sujets optionnelle',
	'HTML_FORUM_LTOPIC_EXCLUDE_EXPLAIN' => 'Vous pouvez exclure certains forums de la liste optionnelle des derniers sujets.<br />Sélécttionnez les forums que vous ne souhaitez pas voir apparaitre dans la liste optinelle des derniers sujets.<br /><u>Note</u> :<br />Si ce champ est laissé vide, tous les forums accessibles seront pris en compte.',
	// Pagination
	'HTML_FORUM_PAGINATION' => 'Pagination des plans de forums',
	'HTML_FORUM_PAGINATION_EXPLAIN' => 'Permet d’activer la pagination sur les plans de forums. Activez l’option si vous souhaitez afficher plus d’une page et lister tous les éléments d’un forum.',
	'HTML_FORUM_PAGINATION_LIMIT' => 'Sujets par page',
	'HTML_FORUM_PAGINATION_LIMIT_EXPLAIN' => 'Quand la pagination des plans de forums est activée, vous pouvez définir ici le nombre de sujets par page.',
	// Content
	'HTML_FORUM_CONTENT' => 'Paramètres du contenu module Forum HTML',
	'HTML_FORUM_FIRST' => 'Classement des plans',
	'HTML_FORUM_FIRST_EXPLAIN' => 'Le classement des plans des forums peut être réalisé en se basant sur la date de parution ou sur la date de la dernière réponse. Choisissez oui pour classer par ordre de parution des sujets, non pour classer par ordre de parutions de la dernière réponse des sujets.',
	'HTML_FORUM_NEWS_FIRST' => 'Classement des news',
	'HTML_FORUM_NEWS_FIRST_EXPLAIN' => 'Le classement des news des forums peut être réalisé en se basant sur la date de parution ou sur la date de la dernière réponse. Choisissez oui pour classer par ordre de parution des sujets, non pour classer par ordre de parutions de la dernière réponse des sujets.',
	'HTML_FORUM_LAST_POST' => 'Afficher le dernier message',
	'HTML_FORUM_LAST_POST_EXPLAIN' => 'Afficher ou non les information sur le dernier message des sujets listés.',
	'HTML_FORUM_POST_BUTTONS' => 'Afficher les boutons de gestion',
	'HTML_FORUM_POST_BUTTONS_EXPLAIN' => 'Afficher ou non les boutons de gestion des news tel que répondre, éditer, etc ...',
	'HTML_FORUM_RULES' => 'Afficher le règlement du forum',
	'HTML_FORUM_RULES_EXPLAIN' => 'Affiche ou non le règlement du forum dans les pages news et / ou les plans des forums.',
	'HTML_FORUM_DESC' => 'Afficher la description du forum',
	'HTML_FORUM_DESC_EXPLAIN' => 'Affiche ou non la description du forum dans pages les news et / ou les plans des forums.',
	// Reset settings
	'HTML_FORUM_RESET' => 'Module HTML Forum',
	'HTML_FORUM_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut du module HTML Forum.',
	'HTML_FORUM_MAIN_RESET' => 'Configuration générale HTML Forum',
	'HTML_FORUM_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut dans l’onglet "Paramètres du module HTML Forum.',
	'HTML_FORUM_CONTENT_RESET' => 'Paramètres des news du module HTML forum',
	'HTML_FORUM_CONTENT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut des paramètres du contenu du module HTML Forum.',
	'HTML_FORUM_CACHE_RESET' => 'Cache HTML Forum',
	'HTML_FORUM_CACHE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au cache du module HTML Forum.',
	'HTML_FORUM_MODREWRITE_RESET' => 'Réécriture d’URL HTML Forum',
	'HTML_FORUM_MODREWRITE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la réécriture d’URL du module HTML Forum.',
	'HTML_FORUM_GZIP_RESET' => 'Compression GZip HTML Forum',
	'HTML_FORUM_GZIP_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la compression GZip du module HTML Forum.',
	'HTML_FORUM_LIMIT_RESET' => 'Limites HTML Forum',
	'HTML_FORUM_LIMIT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées aux limites du module HTML Forum.',
	'HTML_FORUM_SORT_RESET' => 'Tri HTML Forum',
	'HTML_FORUM_SORT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au tri du module HTML Forum.',
	'HTML_FORUM_PAGINATION_RESET' => 'Pagination HTML Forum',
	'HTML_FORUM_PAGINATION_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la pagination du module HTML Forum.',
));
?>