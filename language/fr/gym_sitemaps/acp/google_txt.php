<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_txt.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* google_txt [French]
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
	'GOOGLE_TXT' => 'Sitemaps TXT',
	'GOOGLE_TXT_EXPLAIN' => 'Il s’agit des paramètres du module TXT des Sitemaps Google. Il permet d’incorporer dans GYM Sitemaps des listes d’URLs à partir de fichiers texte (une URL par ligne) et ainsi leur faire partager toutes les fonctionnalités du MOD comme la gestion des styles XSL et le cache.<br />Certains paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage pour le type de rendu Google Sitemaps et pour le niveau principal.<br /><br />Chaque fichier texte ajouté dans le dossier gym_sitemaps/sources/ sera pris en compte une fois que vous aurez vidé le cache de configuration du module, via le lien maintenance ci-dessus.<br />Chaque liste d’URLs devra être composée d’une URL complète par ligne et suivre une règle simple pour les noms de fichiers : <b>google_</b>nom_de_fichier<b>.txt</b>.<br />Une entrée sera alors créée dans le SitemapIndex avec comme URL : <b>example.com/sitemap.php?txt=nom_de_fichier</b> ou <b>example.com/txt-nom_de_fichier.txt</b> en version réécrite.<br />Le nom des fichiers source ne peut comporter que des caractères alphanumériques (chiffres et lettres non accentuées) ainsi que les séparateurs "_" et "-".<br /><u style="color:red;">Note</u> :<br /> Il est conseillé d’activer le cache pour ce module afin d’éviter des traitements inutiles sur des fichiers texte volumineux.',
	// Main
	'GOOGLE_TXT_CONFIG' => 'Paramètres Sitemaps TXT',
	'GOOGLE_TXT_CONFIG_EXPLAIN' => 'Certains paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage au niveau du type de rendu des Sitemaps Google et au niveau global.',
	'GOOGLE_TXT_RANDOMIZE' => 'Répartition aléatoire',
	'GOOGLE_TXT_RANDOMIZE_EXPLAIN' => 'Vous pouvez répartir aléatoirement les URLs récupérées dans les fichiers texte. Changer régulièrement l’ordre des URLs peut légèrement améliorer l’indexation. Cette option est également pratique dans le cas où le fichier texte source comporte plus de liens que la limite de résultats en sortie souhaitée (ex : limite à 1000 et fichier texte source contenant 5000 URLs). Dans ce cas, la répartition aléatoire permet de faire cycler l’ensemble des URLs dans les Sitemaps.',
	'GOOGLE_TXT_UNIQUE' => 'Supprimer les doublons',
	'GOOGLE_TXT_UNIQUE_EXPLAIN' => 'Activez cette option pour s’assurer que si la même URL apparait plusieurs fois dans le fichier texte source, elle ne sera affichée qu’une seule fois dans le Sitemap.',
	'GOOGLE_TXT_FORCE_LASTMOD' => 'Dernière modification',
	'GOOGLE_TXT_FORCE_LASTMOD_EXPLAIN' => 'Le module peut imposer pour toutes les URLs du Sitemap une date de dernière modification basée sur le rythme de régénération du cache (même s’il n’est pas activé). Le module en profitera par ailleurs pour définir des valeurs pour les priorités et les fréquences de mise à jour en fonction de la date de dernière modification. Par défaut, aucune balise <lastmod>, <priority> et <changefreq> ne seront ajoutées au Sitemap.',
	// Reset settings
	'GOOGLE_TXT_RESET' => 'Module Sitemaps TXT',
	'GOOGLE_TXT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut du module Sitemaps TXT.',
	'GOOGLE_TXT_MAIN_RESET' => 'Configuration générale du module Sitemaps TXT',
	'GOOGLE_TXT_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut dans l’onglet "Paramètres Sitemaps TXT" du module Sitemaps TXT.',
	'GOOGLE_TXT_CACHE_RESET' => 'Cache Sitemaps TXT',
	'GOOGLE_TXT_CACHE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au cache du module Sitemaps TXT.',
	'GOOGLE_TXT_GZIP_RESET' => 'Compression GZip Sitemaps TXT',
	'GOOGLE_TXT_GZIP_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la la compression GZip du module Sitemaps TXT.',
	'GOOGLE_TXT_LIMIT_RESET' => 'Limites Sitemaps TXT',
	'GOOGLE_TXT_LIMIT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées aux limites du module Sitemaps TXT.',
));
?>