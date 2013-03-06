<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_html.php 134 2009-11-02 11:13:45Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* gym_html [French]
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
	'HTML_MAIN' => 'Paramètres des plans html',
	'HTML_MAIN_EXPLAIN' => 'Il s’agit des paramètres principaux pour le module de pages html.<br />Ils peuvent être appliqués à l’ensemble des modules HTML selon vos paramètres d’héritage des options HTML.',
	// Linking setup
	'HTML_LINKS_ACTIVATION' => 'Affichage des liens sitemaps sur le forum',
	'HTML_LINKS_MAIN' => 'Liens principaux',
	'HTML_LINKS_MAIN_EXPLAIN' => 'Afficher ou non les liens vers les news plans en pied de page.<br/>Cette option nécéssite que l’affichage des liens principaux soit activé dans la configuration générale.',
	'HTML_LINKS_INDEX' => 'Liens sur l’index',
	'HTML_LINKS_INDEX_EXPLAIN' => 'Afficher ou non les liens vers les news et plans de chaque forum sur l’index du forum. Ces liens sont ajoutés sous la descriptions des forums.<br/>Cette option nécéssite que l’affichage des liens sur l’index soit activé dans la configuration générale.',
	'HTML_LINKS_CAT' => 'Liens des forums',
	'HTML_LINKS_CAT_EXPLAIN' => 'Afficher ou non les liens vers les news et plans du forum en cours. Ce liens est ajoutés sous le titre du forum.<br/>Cette option nécéssite que l’affichage des liens des forums soit activé dans la configuration générale.',
	// Reset settings
	'HTML_ALL_RESET' => 'Tous les modules HTML',
	// Limits
	'HTML_RSS_NEWS_LIMIT' => 'Limite du nombre d’éléments de la page news principale',
	'HTML_RSS_NEWS_LIMIT_EXPLAIN' => 'Limite le nombre d’éléments récupérés depuis le flux RSS configuré dans les paramètres des news.',
	'HTML_MAP_TIME_LIMIT' => 'Limite temporelle du plan principal',
	'HTML_MAP_TIME_LIMIT_EXPLAIN' => 'Permet de limiter en nombre de jours, l’ancienneté maximale des éléments pris en compte dans le plan principal des modules. Cette option vous permet par exemple de n’afficher que les sujets du mois en cours sur le plan du forum. Entrez 0 pour aucune limite.',
	'HTML_CAT_MAP_TIME_LIMIT' => 'Limite temporelle des catégories',
	'HTML_CAT_MAP_TIME_LIMIT_EXPLAIN' => 'Permet de limiter en nombre de jours, l’ancienneté maximale des éléments pris en compte dans les plan de catégorie des modules. Cette option vous permet par exemple de n’afficher que les sujets du mois en cours sur le plan d’un forum. Entrez 0 pour aucune limite.',
	'HTML_NEWS_TIME_LIMIT' => 'Limite temporelle de la page news',
	'HTML_NEWS_TIME_LIMIT_EXPLAIN' => 'Permet de limiter en nombre de jours, l’ancienneté maximale des éléments pris en compte dans la page de news des modules. Cette option vous permet par exemple de n’afficher que les news du mois en cours sur la page news. Entrez 0 pour aucune limite.',
	'HTML_CAT_NEWS_TIME_LIMIT' => 'Limite temporelle des news de catégories',
	'HTML_CAT_NEWS_TIME_LIMIT_EXPLAIN' => 'Permet de limiter en nombre de jours, l’ancienneté maximale des éléments pris en compte dans les pages de news de catégories des modules. Cette option vous permet par exemple de n’afficher que les news du mois en cours sur la page news d’un forum. Entrez 0 pour aucune limite.',
	// sort
	'HTML_MAP_SORT_TITLE' => 'Tri des plans',
	'HTML_NEWS_SORT_TITLE' => 'Tri des news',
	'HTML_CAT_SORT_TYPE' => 'Ordre de tri de plans de catégories',
	'HTML_CAT_SORT_TYPE_EXPLAIN' => 'Suivant le même principe que l’ordre défini ci-dessus, celui-ci s’applique au plan des catégories des modules, soit le plan d’un forum pour le cas du module forum HTML.',
	'HTML_NEWS_SORT_TYPE' => 'Ordre de tri de la page de news',
	'HTML_NEWS_SORT_TYPE_EXPLAIN' => 'Suivant le même principe que l’ordre défini ci-dessus, celui-ci s’applique à la page de news des modules, soit la page news du forum pour le cas du module forum HTML.',
	'HTML_CAT_NEWS_SORT_TYPE' => 'Ordre de tri des news de catégories',
	'HTML_CAT_NEWS_SORT_TYPE_EXPLAIN' => 'Suivant le même principe que l’ordre défini ci-dessus, celui-ci s’applique aux pages de news de catégories des modules, soit la page news d’un forum pour le cas du module forum HTML.',
	'HTML_PAGINATION_GEN' => 'Pagination globale',
	'HTML_PAGINATION_SPEC' => 'Pagination du module',
	'HTML_PAGINATION' => 'Pagination des plans de site',
	'HTML_PAGINATION_EXPLAIN' => 'Permet d’activer la pagination sur les plans de site. Activez l’option si vous souhaitez afficher plus d’une page pour lister tous les éléments d’un plan.',
	'HTML_PAGINATION_LIMIT' => 'Eléments par page',
	'HTML_PAGINATION_LIMIT_EXPLAIN' => 'Quand la pagination globale est activée, vous pouvez définir ici le nombre d’éléments par page.',
	'HTML_NEWS_PAGINATION' => 'Pagination des news',
	'HTML_NEWS_PAGINATION_EXPLAIN' => 'Permet d’activer la pagination sur la page de news principale du module. Activez l’option si vous souhaitez afficher plus d’une page de news.',
	'HTML_NEWS_PAGINATION_LIMIT' => 'News par page',
	'HTML_NEWS_PAGINATION_LIMIT_EXPLAIN' => 'Quand la pagination des news est activée, vous pouvez définir ici le nombre de news par page.',
	'HTML_ITEM_PAGINATION' => 'Pagination des éléments',
	'HTML_ITEM_PAGINATION_EXPLAIN' => 'Vous pouvez décider de produire des listes d’éléments avec pagination (si disponible). Pour un sujet de forum par exemple, cela se traduira par l’ajout de liens additionnels vers les différentes pages du sujet.',
	// Basic settings
	'HTML_SETTINGS' => 'Paramètres de base',
	'HTML_C_INFO' => 'Informations de copyright',
	'HTML_C_INFO_EXPLAIN' => 'Les informations à afficher dans la balise meta copyright des plans HTML et les news. Par défaut correspond au nom du site phpBB. Cette information ne sera utilisée que si vous installez / utilisez le mod Méta dynamiqes de phpBB SEO.',
	'HTML_SITENAME' => 'Nom du site',
	'HTML_SITENAME_EXPLAIN' => 'Le nom du site à utiliser dans les plans HTML et les news. Par défaut correspond au nom du site phpBB.',
	'HTML_SITE_DESC' => 'Description du site',
	'HTML_SITE_DESC_EXPLAIN' => 'La description du site à utiliser dans les plans HTML et les news. Par défaut correspond à la description du site phpBB.',
	'HTML_LOGO_URL' => 'Logo du site',
	'HTML_LOGO_URL_EXPLAIN' => 'Le fichier image à utiliser comme logo du site dans plans HTML et les news, à placer dans le dossier gym_sitemaps/images/.',
	'HTML_URL' => 'URL de la page du module HTML',
	'HTML_URL_EXPLAIN' => 'Entrez l’URL complète vers votre fichier map.php, par exemple http://www.example.com/eventual_dir/ si le fichier map.php est installé dans http://www.example.com/eventual_dir/.<br />Cette option est utile lorsque phpBB n’est pas installé à la racine de votre domaine et que vous désirez placer le fichier map.php à la racine.',
	'HTML_RSS_NEWS_URL' => 'URL de la source rss de la page news principale',
	'HTML_RSS_NEWS_URL_EXPLAIN' => 'Entrez l’URL complète vers le flux RSS de votre choix, par exemple http://www.example.com/gymrss.php?news&amp;digest pour afficher les news de tous les modules RSS installés sur la page de new principale.<br />Vous pouvez utiliser n’importe quelle flux RSS 2.0 comme source de cette page de news.',
	'HTML_STATS_ON_NEWS' => 'Afficher les stats du forum sur les news',
	'HTML_STATS_ON_NEWS_EXPLAIN' => 'Afficher, ou non, les stats du forum sur les pages de news.',
	'HTML_STATS_ON_MAP' => 'Afficher les stats du forum sur les plans',
	'HTML_STATS_ON_MAP_EXPLAIN' => 'Afficher, ou non, les stats du forum sur les plans de site.',
	'HTML_BIRTHDAYS_ON_NEWS' => 'Afficher les anniversaires sur les news',
	'HTML_BIRTHDAYS_ON_NEWS_EXPLAIN' => 'Afficher, ou non, les anniversaires sur les pages de news.',
	'HTML_BIRTHDAYS_ON_MAP' => 'Afficher les anniversaires sur les plans',
	'HTML_BIRTHDAYS_ON_MAP_EXPLAIN' => 'Afficher, ou non, les anniversaires sur les plans de site.',
	'HTML_DISP_ONLINE' => 'Afficher les utilisateurs en ligne',
	'HTML_DISP_ONLINE_EXPLAIN' => 'Afficher ou non la liste des utilisateurs en ligne sur les pages du module.',
	'HTML_DISP_TRACKING' => 'Activer le suivit des éléments',
	'HTML_DISP_TRACKING_EXPLAIN' => 'Activer le système de suivit des éléments (lus / non lus).',
	'HTML_DISP_STATUS' => 'Activer le status des éléments',
	'HTML_DISP_STATUS_EXPLAIN' => 'Activer le système de status des éléments (Annonces, Post-it, Vérouillés etc ... ).',
	// Cache
	'HTML_CACHE' => 'Cache',
	'HTML_CACHE_EXPLAIN' => 'Vous pouvez définir ici diverses options liées au cache du type de sortie HTML. La gestion du cache HTML est séparé de celle des autres types de sorties. Ce module utilise le cache standard de phpBB.<br/>Ces options ne peuvent donc pas être hérités du niveau pricipale. De plus, seul les pages contenant uniquement du contenu public seront mise en cache. Ces paramètres peuvent en revanche être tramis aux modules HTML en fonction de votre configuration des priorités de paramétrage.<br/><br/>Le cache est séparé en deux fichiers, un pour chaque colonne de la page de sortie : La colonne principale contenant le plan ou les news et la colonne optionnelle, qui permet par exemple d’ajouter une liste des dernier sujets actifs sur les pages du module forum HTML.',
	'HTML_MAIN_CACHE_ON' => 'Activer la mise en cache du contenu principal',
	'HTML_MAIN_CACHE_ON_EXPLAIN' => 'Vous pouvez activer / désactiver la mise en cache des news et des plans pour ce module.',
	'HTML_OPT_CACHE_ON' => 'Activer la mise en cache de la colonne optionelle',
	'HTML_OPT_CACHE_ON_EXPLAIN' => 'Vous pouvez activer / désactiver la mise en cache du contenu de la colonne optionelle pour ce module. Pour le module forum HTML, cette colonne optionnelle peut par exemple contenir une liste des dernier sujets actifs.',
	'HTML_MAIN_CACHE_TTL' => 'Durée de vie du cache du contenu principal',
	'HTML_MAIN_CACHE_TTL_EXPLAIN' => 'Nombre maximal d’heures pendant lesquelles un fichier en cache sera utilisé avant d’être mis à jour. Si cette durée est atteinte le fichier en cache sera rafraîchi lorsque quelqu’un y fera appel.',
	'HTML_OPT_CACHE_TTL' => 'Durée de vie du cache de la colonne optionelle',
	'HTML_OPT_CACHE_TTL_EXPLAIN' => 'Nombre maximal d’heures pendant lesquelles un fichier en cache sera utilisé avant d’être mis à jour. Si cette durée est atteinte le fichier en cache sera rafraîchi lorsque quelqu’un y fera appel.',
	// Auth settings
	'HTML_AUTH_SETTINGS' => 'Paramètres liés aux autorisations',
	'HTML_ALLOW_AUTH' => 'Autorisations',
	'HTML_ALLOW_AUTH_EXPLAIN' => 'Active les autorisations pour les pages HTML. Si activé, les utilisateurs connectés pourront voir les contenus privés et des éléments de forums privés s’ils ont les autorisations nécessaires.',
	'HTML_ALLOW_NEWS' => 'Activer les news',
	'HTML_ALLOW_NEWS_EXPLAIN' => 'La page de news de chaque module HTML est une page contenant les x derniers éléments avec tout ou partie de leur contenu. Pour un forum, il s’agit générallement d’une page contenant un résumé des 10 derniers messages provenant d’un ou plusieurs forums, publics et / ou privés.',
	'HTML_ALLOW_CAT_NEWS' => 'Activer les news de catégories',
	'HTML_ALLOW_CAT_NEWS_EXPLAIN' => 'Suivant le meme principe que la page de news des modules, chaque catégories (ou forum dans le cas du module HTML forum) peut avoir une page de news dédiée si vous activez l’option.',
	// Content
	'HTML_NEWS' => 'Paramètres des news',
	'HTML_NEWS_EXPLAIN' => 'Vous pouvez paramétrer ici diverses options de filtrage et mise en forme des news et de leur contenu.<br />Ces paramètres peuvent être appliqués à l’ensemble des modules HTML selon vos paramètres d’héritage des options HTML.',
	'HTML_NEWS_CONTENT' => 'Paramètres du contenu des news',
	'HTML_SUMARIZE' => 'Résumer les éléments',
	'HTML_SUMARIZE_EXPLAIN' => 'Vous pouvez résumer le contenu des messages affichés dans les pages news.<br /> Cette limite détermine le nombre maximum de phrases, mots ou caractères, selon la méthode sélectionnée ci-dessous. Entrez 0 pour aucune limite.',
	'HTML_SUMARIZE_METHOD' => 'Méthode de résumé',
	'HTML_SUMARIZE_METHOD_EXPLAIN' => 'Vous pouvez choisir parmi trois méthodes différentes pour limiter le contenu des messages affichés dans les pages news :<br />par nombre de lignes, par nombre de mots ou par nombre de caractères. Les balises BBCodes et les mots ne seront pas scindés.',
	'HTML_ALLOW_PROFILE' => 'Afficher les profils',
	'HTML_ALLOW_PROFILE_EXPLAIN' => 'Les profils peuvent être affichés ou pas dans le contenu.',
	'HTML_ALLOW_PROFILE_LINKS' => 'Lien vers les profils',
	'HTML_ALLOW_PROFILE_LINKS_EXPLAIN' => 'Si les profils sont affichés, vous pouvez décider d’en faire un lien menant vers la page correspondant à son profil phpBB.',
	'HTML_ALLOW_BBCODE' => 'Autoriser les BBCodes',
	'HTML_ALLOW_BBCODE_EXPLAIN' => 'Vous pouvez choisir d’inclure ou non les BBCodes dans le résultat.',
	'HTML_STRIP_BBCODE' => 'Filtres BBCodes',
	'HTML_STRIP_BBCODE_EXPLAIN' => 'Vous pouvez paramétrer ici une liste de BBCodes à exclure du traitement.<br />Le format est simple : <br /><ul><li><u>Une liste de BBCodes séparés par des virgules</u> : supprime les balises BBCodes mais en conserve le contenu.<br /><u>Exemple</u> : <b>img,b,quote</b><br />Dans cet exemple, les BBCodes img, b et quote ne seront pas interprétés, les balises BBCodes seront supprimées mais le contenu à l’intérieur des balises sera conservé.</li><li><u>Une liste de BBCodes séparés par des virgules avec l’option "double point" (":")</u> : Supprime les balises BBCodes et décide du contenu.<br /><u>Exemple</u> : <b>img:1,b:0,quote,code:1</b><br />Dans cet exemple, la balise BBCode img et le lien img seront supprimés, le gras (b) ne sera pas interprété mais le texte qui aurait du être en gras sera conservé, la balise quote ne sera pas interprétée mais son contenu sera conservé, les balises code et leur contenu seront retirés du résultat.</li></ul>Le filtre fonctionnera même si les BBCodes sont vides. Pratique pour effacer par exemple le contenu des balises code et les liens images de la mise en page.<br />Le filtrage est effectué avant le résumé.<br />Le paramètre magique "all" ( all:0 ou all:1 pour supprimer également tous les contenus des balises BBCcodes) va gérer tous les BBCodes en une fois.',
	'HTML_ALLOW_LINKS' => 'Autoriser les liens actifs',
	'HTML_ALLOW_LINKS_EXPLAIN' => 'Vous pouvez choisir ici d’activer ou non les liens dans le contenu des éléments.<br />En cas de non activation, les liens et les emails seront inclus dans le contenu mais ne seront pas cliquables.',
	'HTML_ALLOW_EMAILS' => 'Autoriser les emails',
	'HTML_ALLOW_EMAILS_EXPLAIN' => 'Vous choisissez ici d’avoir en sortie "email AT domain DOT com" au lieu de "email@domain.com" dans le contenu des éléments.',
	'HTML_ALLOW_SMILIES' => 'Autoriser les smileys',
	'HTML_ALLOW_SMILIES_EXPLAIN' => 'Vous pouvez choisir d’inclure ou ignorer les smileys dans le contenu.',
	'HTML_ALLOW_SIG' => 'Autoriser les signatures',
	'HTML_ALLOW_SIG_EXPLAIN' => 'Vous pouvez choisir d’inclure ou ignorer les signatures dans le contenu.',
	'HTML_ALLOW_MAP' => 'Activer le plan des modules',
	'HTML_ALLOW_MAP_EXPLAIN' => 'Activer ou non le plan pricipale des modules. Pour le forum, cela correspond au plans des forums.',
	'HTML_ALLOW_CAT_MAP' => 'Activer les plans de catégories des modules',
	'HTML_ALLOW_CAT_MAP_EXPLAIN' => 'Activer ou non les plans de catégories des modules. Pour le forum, cela correspond aux plans de chaque forums.',
));
?>