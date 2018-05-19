<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: google_xml.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* google_xml [French]
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
	'GOOGLE_XML' => 'Sitemaps XML',
	'GOOGLE_XML_EXPLAIN' => 'Il s’agit des paramètres du module XML des Sitemaps Google. Il permet d’incorporer dans GYM Sitemaps des listes d’URLs à partir de fichiers texte (une URL par ligne) et ainsi leur faire partager toutes les fonctionnalités du MOD comme la gestion des styles XSL et le cache.<br />Certains paramètres peuvent être écrasés en fonction de votre configuration des priorités des paramétrage pour le type de rendu Google Sitemaps et pour le niveau principal.<br /><br />Chaque fichier texte ajouté dans le dossier gym_sitemaps/sources/ sera pris en compte une fois que vous aurez vidé le cache de configuration du module, via le lien maintenance ci-dessus.<br />Chaque liste d’URLs devra être composée d’une URL complète par ligne et suivre une règle simple pour les noms de fichiers : <b>google_</b>nom_de_fichier<b>.xml</b>.<br />Une entrée sera alors crée dans le SitemapIndex avec comme URL : <b>example.com/sitemap.php?xml=nom_de_fichier</b> ou <b>example.com/xml-nom_de_fichier.xml</b> en version réécrite.<br />Le nom des fichiers source ne peut comporter que des caractères alphanumériques (chiffres et lettres non accentuées) ainsi que les séparateurs "_" et "-".<p>Vous pouvez également utiliser l’url complète d’un sitemap qui serait généré par une autre application, en configurant le fichier gym_sitemaps/sources/xml_google_external.php (voir les commentaires du fichier pour plus de dètails).</p><u style="color:red;">Note</u> :<br /> Il est conseillé d’activer le cache pour ce module afin d’éviter des traitements inutiles sur des fichiers xml volumineux.',
	// Main
	'GOOGLE_XML_CONFIG' => 'Paramètres Sitemaps XML',
	'GOOGLE_XML_CONFIG_EXPLAIN' => 'Certains paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage au niveau du type de rendu des Sitemaps Google et au niveau global.',
	'GOOGLE_XML_RANDOMIZE' => 'Répartition aléatoire',
	'GOOGLE_XML_RANDOMIZE_EXPLAIN' => 'Vous pouvez répartir aléatoirement les URLs récupérées dans les fichiers xml. Changer régulièrement l’ordre des URLs peut légèrement améliorer l’indexation. Cette option est également pratique dans le cas où le fichier xml source comporte plus de liens que la limite de résultats en sortie souhaitée (ex : limite à 1000 et fichier xml source contenant 5000 URLs). Dans ce cas, la réparition aléatoire permet de faire cycler l’ensemble des URLs dans les Sitemaps.<br /><br /><u>Note</u> :<br />Cette option impose de traiter tout le fichier source élément par élément, il est conseiller de l’utiliser avec le cache activé.',
	'GOOGLE_XML_UNIQUE' => 'Supprimer les doublons',
	'GOOGLE_XML_UNIQUE_EXPLAIN' => 'Activez cette option pour s’assurer que si la même URL apparait plusieurs fois dans le fichier texte source, elle ne sera affichée qu’une seule dans le Sitemap.<br /><br /><u>Note</u> :<br />Cette option impose de traiter tout le fichier source élément par élément, il est conseiller de l’utiliser avec le cache activé.',
	'GOOGLE_XML_FORCE_LASTMOD' => 'Dernière modification',
	'GOOGLE_XML_FORCE_LASTMOD_EXPLAIN' => 'Le module peut imposer pour toutes les URLs du Sitemap une date de dernière modification basée sur le rythme de régénération du cache (même s’il n’est pas activé). Le module en profitera par ailleurs pour définir des valeurs pour les priorités et les fréquences de mise à jour en fonction de la date de dernière modification. Par défaut, le module reprendra les date fournies ou non par le fichier source.<br /><br /><u>Note</u> :<br />Cette option impose de traiter tout le fichier source élément par élément, il est conseiller de l’utiliser avec le cache activé.',
	'GOOGLE_XML_FORCE_LIMIT' => 'Forcer la limite',
	'GOOGLE_XML_FORCE_LIMIT_EXPLAIN' => 'Le module peut s’assurer qu’il n’y a pas plus de liens dans le fichier source que la limite imposée ci-dessus.<br /><br /><u>Note</u> :<br />Cette option impose de traiter tout le fichier source élément par élément, il est conseiller de l’utiliser avec le cache activé.',
	// Reset settings
	'GOOGLE_XML_RESET' => 'Module Sitemaps XML',
	'GOOGLE_XML_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut du module Sitemaps XML.',
	'GOOGLE_XML_MAIN_RESET' => 'Configuration générale du module Sitemaps XML',
	'GOOGLE_XML_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut dans l’onglet "Paramètres Sitemaps XML" du module Sitemaps XML.',
	'GOOGLE_XML_CACHE_RESET' => 'Cache Sitemaps XML',
	'GOOGLE_XML_CACHE_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées au cache du module Sitemaps XML.',
	'GOOGLE_XML_GZIP_RESET' => 'Compression GZip Sitemaps XML',
	'GOOGLE_XML_GZIP_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées à la la compression GZip du module Sitemaps XML.',
	'GOOGLE_XML_LIMIT_RESET' => 'Limites Sitemaps XML',
	'GOOGLE_XML_LIMIT_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut liées aux limites du module Sitemaps XML.',
));
?>