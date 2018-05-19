<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_common.php 112 2009-09-30 17:21:34Z dcz $
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
	'RSS_AUTH_SOME_USER' => '<b><u>Avertissement :</u></b>Cette liste d’éléments est personalisée selon les autorisations de <b>%s</b>.<br/>Certains éléments ne seront pas visibles par les invités.',
	'RSS_AUTH_THIS_USER' => '<b><u>Avertissement :</u></b>Cet élément est personalisé selon les autorisations de <b>%s</b>.<br/>Il ne sera pas visible par les invités.',
	'RSS_AUTH_SOME' => '<b><u>Avertissement :</u></b>Cette liste d’éléments n’est pas publique.<br/>Certains éléments ne seront pas visibles par les invités.',
	'RSS_AUTH_THIS' => '<b><u>Avertissement :</u></b>Cet élément n’est pas public.<br/>Il ne sera pas visible par les invités.',
	'RSS_CHAN_LIST_TITLE' => 'Liste des flux',
	'RSS_CHAN_LIST_DESC' => 'Ceci est une liste de tous les flux RSS disponibles.',
	'RSS_CHAN_LIST_DESC_MODULE' => 'Ceci est une liste de tous les flux RSS disponibles pour : %s.',
	'RSS_ANNOUCES_DESC' => 'Ce flux liste toutes les annonces globales de : %s',
	'RSS_ANNOUNCES_TITLE' => 'Annonces de : %s',
	'GYM_LAST_POST_BY' => 'Dernier message par ',
	'GYM_FIRST_POST_BY' => 'Message de ',
	'GYM_LINK' => 'Lien',
	'GYM_SOURCE' => 'Source',
	'GYM_RSS_SOURCE' => 'Source',
	'RSS_MORE' => 'plus',
	'RSS_CHANNELS' => 'Canaux',
	'RSS_CONTENT' => 'Résumé',
	'RSS_SHORT' => 'Liste courte',
	'RSS_LONG' => 'Liste longue',
	'RSS_NEWS' => 'Actualités',
	'RSS_NEWS_DESC' => 'Dernières actualités de',
	'RSS_REPORTED_UNAPPROVED' => 'Ce sujet est en attente d’approbation.',

	'GYM_HOME' => 'Page principale',
	'GYM_FORUM_INDEX' => 'Index du forum',
	'GYM_LASTMOD_DATE' => 'Dernière modification',
	'GYM_SEO' => 'Optimisation du référencement',
	'GYM_MINUTES' => 'minute(s)',
	'GYM_SQLEXPLAIN' => 'Rapport SQL',
	'GYM_SQLEXPLAIN_MSG' => 'Connecté en tant qu’admin, vous pouvez vérifier le %s de cette page.',
	'GYM_BOOKMARK_THIS' => 'Ajouter aux favoris',
	// Errors
	'GYM_ERROR_404' => 'Cette page n’existe pas ou n’est pas activée.',
	'GYM_ERROR_404_EXPLAIN' => 'Le serveur n’a trouvé aucune page correspondant à l’url que vous avez utilisé.',
	'GYM_ERROR_401' => 'Vous n’êtes pas autorisé à voir cette page.',
	'GYM_ERROR_401_EXPLAIN' => 'Cette page est uniquement accessible aux utilisateurs enregistrés possédant les autorisations nécéssaires.',
	'GYM_LOGIN' => 'Vous n’êtes pas autorisé à voir cette page.',
	'GYM_LOGIN_EXPLAIN' => 'Vous devez être enregistré et connecté pour voir cette page.',
	'GYM_TOO_FEW_ITEMS' => 'Page Indisponible',
	'GYM_TOO_FEW_ITEMS_EXPLAIN' => 'Cette page ne contient pas assez d’éléments pour être affichée.',
	'GYM_TOO_FEW_ITEMS_EXPLAIN_ADMIN' => 'La source de cette page ne contient aucun éléments ou un nombre d’éléments inférieur au seuil défini dans l’ACP pour être affichée.<br/> Un Header 404 Not Found est par ailleurs utilisé pour indiquer aux moteurs de recherche de ne pas utiliser ce lien.',

	'GOOGLE_SITEMAP' => 'Sitemap',
	'GOOGLE_SITEMAP_OF' => 'Sitemap de',
	'GOOGLE_MAP_OF' => 'Sitemap de %1$s',
	'GOOGLE_SITEMAPINDEX' => 'SitemapIndex',
	'GOOGLE_NUMBER_OF_SITEMAP' => 'Nombre de Sitemaps dans ce SitemapIndex Google',
	'GOOGLE_NUMBER_OF_URL' => 'Nombre d’URLs dans ce Sitemap Google',
	'GOOGLE_SITEMAP_URL' => 'URL du Sitemap',
	'GOOGLE_CHANGEFREQ' => 'Fréquence de Màj',
	'GOOGLE_PRIORITY' => 'Priorité',

	'RSS_FEED' => 'Flux RSS',
	'RSS_FEED_OF' => 'Flux RSS de %1$s',
	'RSS_2_LINK' => 'Lien du flux RSS 2.0',
	'RSS_UPDATE' => 'Mise à jour',
	'RSS_LAST_UPDATE' => 'Dernière Màj',
	'RSS_SUBSCRIBE_POD' => '<h2>S’abonner à ce flux!</h2>Avec votre service préféré.',
	'RSS_SUBSCRIBE' => 'Pour s’abonner manuellement à ce flux, utilisez l’URL suivante :',
	'RSS_ITEM_LISTED' => 'Un élément listé.',
	'RSS_ITEMS_LISTED' => 'éléments listés.',
	'RSS_VALID' => 'Flux RSS 2.0 valide',

	// Old URL handling
	'RSS_1XREDIR' => 'Ce flux RSS a été déplacé',
	'RSS_1XREDIR_MSG' => 'Ce flux RSS a été déplacé, il se trouve désormais à cette adresse',
	// HTML sitemaps
	'HTML_MAP' => 'Plan de site',
	'HTML_MAP_OF' => 'Plan de %1$s',
	'HTML_MAP_NONE' => 'Aucun plan de site',
	'HTML_NO_ITEMS' => 'Aucun élément',
	'HTML_NEWS' => 'News',
	'HTML_NEWS_OF' => 'News de %1$s',
	'HTML_NEWS_NONE' => 'Aucune news',
	'HTML_PAGE' => 'Page',
	'HTML_MORE' => 'Lire la suite',
	// Forum
	'HTML_FORUM_MAP' => 'Plan des Forums',
	'HTML_FORUM_NEWS' => 'News des Forums',
	'HTML_FORUM_GLOBAL_MAP' => 'Liste des Annonces Globales',
	'HTML_FORUM_GLOBAL_NEWS' => 'Annonces Globales',
	'HTML_FORUM_ANNOUNCE_MAP' => 'Liste des Annonces',
	'HTML_FORUM_ANNOUNCE_NEWS' => 'Annonces',
	'HTML_FORUM_STICKY_MAP' => 'Liste des Post its',
	'HTML_FORUM_STICKY_NEWS' => 'Post its',
	'HTML_LASTX_TOPICS_TITLE' => '%1$s derniers sujets actifs',
));
?>