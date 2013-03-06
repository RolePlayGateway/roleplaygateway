<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_google.php 134 2009-11-02 11:13:45Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* gym_common [French]
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
	'GOOGLE_MAIN' => 'Paramètres des Sitemaps Google',
	'GOOGLE_MAIN_EXPLAIN' => 'Paramètres principaux du type de rendu Sitemaps Google.<br />Par défaut, ils s’appliqueront à tous les modules du type de rendu Sitemaps Google.',
	// Linking setup
	'GOOGLE_LINKS_ACTIVATION' => 'Affichage des liens sitemaps sur le forum',
	'GOOGLE_LINKS_MAIN' => 'Liens principaux',
	'GOOGLE_LINKS_MAIN_EXPLAIN' => 'Afficher ou non les liens vers le sitemapindex en pied de page.<br/>Cette option nécéssite que l’affichage des liens principaux soit activé dans la configuration générale.',
	'GOOGLE_LINKS_INDEX' => 'Liens sur l’index',
	'GOOGLE_LINKS_INDEX_EXPLAIN' => 'Afficher ou non les liens vers les sitemaps de chaque forum sur l’index du forum. Ces liens sont ajoutés sous la descriptions des forums.<br/>Cette option nécéssite que l’affichage des liens sur l’index soit activé dans la configuration générale.',
	'GOOGLE_LINKS_CAT' => 'Liens des forums',
	'GOOGLE_LINKS_CAT_EXPLAIN' => 'Afficher ou non les liens vers le sitemaps du forum en cours. Ce liens est ajoutés sous le titre du forum.<br/>Cette option nécéssite que l’affichage des liens des forums soit activé dans la configuration générale.',
	// Reset settings
	'GOOGLE_ALL_RESET' => '<b>Tous<b> les modules Sitemaps Google',
	'GOOGLE_URL' => 'URL du Sitemap Google',
	'GOOGLE_URL_EXPLAIN' => 'Entrez l’URL complète de votre SitemapIndex, par exemple http://www.example.com/eventual_dir/ si le fichier sitemap.php est installé dans http://www.example.com/eventual_dir/.<br />Cette option est utile lorsque phpBB n’est pas installé à la racine de votre domaine et que vous désirez lister dans vos Sitemaps Google des URLs situées à la racine du domaine.',
	'GOOGLE_PING' => 'Ping Google',
	'GOOGLE_PING_EXPLAIN' => 'Prévient Google (ping) chaque fois qu’un Sitemap est rafraîchi.',
	'GOOGLE_THRESHOLD' => 'Limite d’activation des sitemaps',
	'GOOGLE_THRESHOLD_EXPLAIN' => 'Nombre d’éléments minimum contenu dans un sitemap. Dans le cas du forum, seul les forums ayant plus de sujets que cette limite auront un sitemap.',
	'GOOGLE_PRIORITIES' => 'Priorité',
	'GOOGLE_DEFAULT_PRIORITY' => 'Priorité par défaut',
	'GOOGLE_DEFAULT_PRIORITY_EXPLAIN' => 'Cette priorité par défaut sera utilisée pour les URLs listées dans les Sitemaps Google sauf si des options supplémentaires sont disponibles au niveau des modules (le nombre doit être compris entre 0.0 &amp; 1.0 inclus)',
	'GOOGLE_XSLT' => 'Style XSL',
	'GOOGLE_XSLT_EXPLAIN' => 'Active la feuille de style XSL pour obtenir un affichage visuellement agréable avec entre autres des liens cliquables. Ceci ne prendra effet que lorsque vous aurez vidé le cache des Sitemaps Google depuis le menu "Maintenance".',
	'GOOGLE_LOAD_PHPBB_CSS' => 'Charger les CSS de phpBB',
	'GOOGLE_LOAD_PHPBB_CSS_EXPLAIN' => 'Le MOD GYM Sitemaps utilise le système de styles de phpBB3. Les feuilles de style XSL utilisées pour produire le fichier html en sortie sont compatibles avec le système de styles de phpBB3.<br />Avec ce paramétrage, plutôt que d’utiliser le style par défaut, vous pouvez appliquer la feuille de style de phpBB à la feuille de style XSL. De cette façon, toutes vos personnalisations de thème telles que le fond, les couleurs de polices ou encore les images seront utilisées en sortie pour l’affichage des Sitemaps Google.<br />Ceci ne prendra effet que lorsque vous aurez vidé le cache des Sitemaps Google depuis le menu "Maintenance".<br />Si les fichiers de style des Sitemaps Google ne sont pas disponibles sur le style utilisé, le style par défaut (toujours disponible et basé sur Prosilver) sera utilisé.<br />N’essayez pas d’utiliser les templates de Prosilver avec un autre style, les CSS ne correspondront certainement pas.',
	// Auth settings
	'GOOGLE_AUTH_SETTINGS' => 'Paramètres liés aux autorisations',
	'GOOGLE_ALLOW_AUTH' => 'Autorisations',
	'GOOGLE_ALLOW_AUTH_EXPLAIN' => 'Active les autorisations pour les sitemaps. Si activé, les utilisateurs connectés et les bots pourront voir les sitemaps des forum privés s’ils ont les autorisations nécessaires.',
	'GOOGLE_CACHE_AUTH' => 'Cache des sitemaps privés',
	'GOOGLE_CACHE_AUTH_EXPLAIN' => 'Vous pouvez désactiver le cache pour les sitemaps privés si les autorisations sont activées.<br /> Mettre en cache les sitemaps privés augmentera le nombre de fichiers en cache, ce qui ne devrait pas poser de problème en général. Cependant cette option vous permet de décider de ne mettre en cache que les sitemaps publics.',
));
?>