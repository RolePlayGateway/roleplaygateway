<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_rss.php 134 2009-11-02 11:13:45Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* gym_rss [French]
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
	'RSS_MAIN' => 'Paramètres des flux RSS',
	'RSS_MAIN_EXPLAIN' => 'Il s’agit des paramètres principaux pour le module de flux RSS.<br />Ils peuvent être appliqués à l’ensemble des modules RSS selon vos paramètres d’héritage des options RSS.',
	// Linking setup
	'RSS_LINKS_ACTIVATION' => 'Affichage des liens RSS sur le forum',
	'RSS_LINKS_MAIN' => 'Liens principaux',
	'RSS_LINKS_MAIN_EXPLAIN' => 'Afficher ou non les liens vers le flux principal et la liste des flux en pied de page.<br/>Cette option nécéssite que l’affichage des liens principaux soit activé dans la configuration générale.',
	'RSS_LINKS_INDEX' => 'Liens sur l’index',
	'RSS_LINKS_INDEX_EXPLAIN' => 'Afficher ou non les liens vers les flux de chaque forum sur l’index du forum. Ces liens sont ajoutés sous la descriptions des forums.<br/>Cette option nécéssite que l’affichage des liens sur l’index soit activé dans la configuration générale.',
	'RSS_LINKS_CAT' => 'Liens des forums',
	'RSS_LINKS_CAT_EXPLAIN' => 'Afficher ou non les liens vers le flux rss du forum en cours. Ce liens est ajoutés sous le titre du forum.<br/>Cette option nécéssite que l’affichage des liens des forums soit activé dans la configuration générale.',
	// Reset settings
	'RSS_ALL_RESET' => 'Tous les modules RSS',
	// Limits
	'RSS_LIMIT_GEN' => 'Limites principales',
	'RSS_LIMIT_SPEC' => 'Limites RSS',
	'RSS_URL_LIMIT_LONG' => 'Limites des flux longs',
	'RSS_URL_LIMIT_LONG_EXPLAIN' => 'Nombre d’éléments affichés dans un flux long sans contenu, nécessite que l’option "Autoriser les flux longs" soit activée.',
	'RSS_SQL_LIMIT_LONG' => 'Cycles SQL flux longs',
	'RSS_SQL_LIMIT_LONG_EXPLAIN' => 'Nombre d’éléments requêtés en un cycle SQL pour les flux longs sans contenu.',
	'RSS_URL_LIMIT_SHORT' => 'Limites des flux courts',
	'RSS_URL_LIMIT_SHORT_EXPLAIN' => 'Nombre d’éléments affichés dans un flux court sans contenu, nécessite que l’option "Autoriser les flux courts soit activée".',
	'RSS_SQL_LIMIT_SHORT' => 'Cycles SQL flux courts',
	'RSS_SQL_LIMIT_SHORT_EXPLAIN' => 'Nombre d’éléments requêtés en un cycle SQL pour les flux courts sans contenu.',
	'RSS_URL_LIMIT_MSG' => 'Limite par défaut pour les flux avec contenu',
	'RSS_URL_LIMIT_MSG_EXPLAIN' => 'Nombre d’éléments affichés par défaut dans les flux avec contenu, nécessite que l’option "Autoriser le contenu des éléments" soit activée.',
	'RSS_SQL_LIMIT_MSG' => 'Cycles SQL flux avec contenu',
	'RSS_SQL_LIMIT_MSG_EXPLAIN' => 'Nombre d’éléments requêtés en un cycle SQL pour les flux avec contenu.',
	// Basic settings
	'RSS_SETTINGS' => 'Paramètres de base',
	'RSS_C_INFO' => 'Informations de copyright',
	'RSS_C_INFO_EXPLAIN' => 'Les informations à afficher dans la balise &lt;copyright&gt; des flux RSS. Par défaut correspond au nom du site phpBB.',
	'RSS_SITENAME' => 'Nom du site',
	'RSS_SITENAME_EXPLAIN' => 'Le nom du site à afficher dans les flux RSS. Par défaut correspond au nom du site phpBB.',
	'RSS_SITE_DESC' => 'Description du site',
	'RSS_SITE_DESC_EXPLAIN' => 'La description du site à afficher dans les flux RSS. Par défaut correspond à la description du site phpBB.',
	'RSS_LOGO_URL' => 'Logo du site',
	'RSS_LOGO_URL_EXPLAIN' => 'Le fichier image à utiliser comme logo du site dans les flux RSS, à placer dans le dossier gym_sitemaps/images/.',
	'RSS_IMAGE_URL' => 'Logo RSS',
	'RSS_IMAGE_URL_EXPLAIN' => 'Le fichier image à utiliser comme logo RSS dans les flux RSS, à placer dans le dossier gym_sitemaps/images/.',
	'RSS_LANG' => 'Langue du flux RSS',
	'RSS_LANG_EXPLAIN' => 'La langue déclarée comme langue principale dans les flux RSS. Par défaut il s’agit de la langue par défaut de phpBB.',
	'RSS_URL' => 'URL du flux RSS',
	'RSS_URL_EXPLAIN' => 'Entrez l’URL complète vers votre fichier gymrss.php, par exemple http://www.example.com/eventual_dir/ si le fichier gymrss.php est installé dans http://www.example.com/eventual_dir/.<br />Cette option est utile lorsque phpBB n’est pas installé à la racine de votre domaine et que vous désirez placer le fichier gymrss.php à la racine.',
	// Auth settings
	'RSS_AUTH_SETTINGS' => 'Paramètres liés aux autorisations',
	'RSS_ALLOW_AUTH' => 'Autorisations',
	'RSS_ALLOW_AUTH_EXPLAIN' => 'Active les autorisations pour les flux RSS. Si activé, les utilisateurs connectés pourront voir les flux privés et des éléments de forums privés dans les flux généraux s’ils ont les autorisations nécessaires.',
	'RSS_CACHE_AUTH' => 'Cache des flux privés',
	'RSS_CACHE_AUTH_EXPLAIN' => 'Vous pouvez désactiver le cache pour les flux privés si les autorisations sont activées.<br /> Mettre en cache les flux privés augmentera le nombre de fichiers en cache, ce qui ne devrait pas poser de problème en général. Cependant cette option vous permet de décider de ne mettre en cache que les flux publics.',
	'RSS_ALLOW_NEWS' => 'Autoriser les flux Actualités',
	'RSS_ALLOW_NEWS_EXPLAIN' => 'Les flux Actualités sont un type de flux personnalisé qui affichera uniquement le premier élément sans tenir compte d’éventuelles réponses. C’est un flux supplémentaire qui n’interfère pas avec les autres. Ce type de flux est utile si vous souhaitez par exemple soumettre vos flux de forum à un système comme Google News. Ainsi chaque sujet correspondra à une entrée dans le flux RSS qui ne varie pas quand une réponse est ajoutée.',
	'RSS_NEWS_UPDATE' => 'Mise à jour des flux Actualités',
	'RSS_NEWS_UPDATE_EXPLAIN' => 'Quand les flux Actualités sont activés, vous pouvez définir ici une durée de vie spécifique pour ce type de flux. Mettez 0 ou laissez vide pour désactiver ; la durée standard sera alors utilisée pour la mise à jour.',
	'RSS_ALLOW_SHORT' => 'Autoriser les flux courts',
	'RSS_ALLOW_SHORT_EXPLAIN' => 'Autorise ou non l’utilisation des flux RSS courts.',
	'RSS_ALLOW_LONG' => 'Autoriser les flux longs',
	'RSS_ALLOW_LONG_EXPLAIN' => 'Autorise ou non l’utilisation des flux RSS longs.',
	// Notifications
	'RSS_NOTIFY' => 'Notifications',
	'RSS_YAHOO_NOTIFY' => 'Notifications Yahoo!',
	'RSS_YAHOO_NOTIFY_EXPLAIN' => 'Active les notifications Yahoo! pour les flux RSS.<br /> Ceci ne concerne pas les flux généraux (RSS.xml).<br />Chaque fois que le cache d’un flux est mis à jour, une notification sera envoyée à Yahoo!<br /><br /><u>Note</u> :<br /> vous DEVEZ entrer votre AppID Yahoo! ci-dessous pour que les notifications soient envoyées.',
	'RSS_YAHOO_APPID' => 'AppID Yahoo! ',
	'RSS_YAHOO_APPID_EXPLAIN' => 'Entrez votre AppID Yahoo!. Si vous n’en avez pas encore, visitez <a href="http://api.search.yahoo.com/webservices/register_application">cette page</a>.<br /><br /><u>Note</u> :<br /> vous devrez créer un compte Yahoo! avant de pouvoir obtenir une AppID Yahoo!.',
	// Styling
	'RSS_STYLE' => 'Style des flux RSS',
	'RSS_XSLT' => 'Style XSL',
	'RSS_XSLT_EXPLAIN' => 'Les flux RSS peuvent être personnalisés grâce à une feuille de style <a href="http://www.w3schools.com/xsl/xsl_transformation.asp">XSL</a>.',
	'RSS_FORCE_XSLT' => 'Forcer le style',
	'RSS_FORCE_XSLT_EXPLAIN' => 'Il est nécessaire de biaiser les navigateurs pour permettre l’usage du style XSL. Ceci est fait en ajoutant des espaces au début du code XML.<br />FF 2 et IE7 regardent seulement les 500 premiers caractères pour décider s’il s’agit ou non d’un flux RSS auquel ils imposent leur propre mise en page.',
	'RSS_LOAD_PHPBB_CSS' => 'Charger les CSS de phpBB',
	'RSS_LOAD_PHPBB_CSS_EXPLAIN' => 'Le MOD GYM Sitemaps utilise le système de styles de phpBB3. Les feuilles de style XSL utilisées pour produire le fichier html en sortie sont compatibles avec le système de styles de phpBB3.<br />Avec ce paramétrage, plutôt que d’utiliser le style par défaut, vous pouvez appliquer la feuille de style de phpBB à la feuille de style XSL. De cette façon, toutes vos personnalisations de thème telles que le fond, les couleurs de polices ou encore les images seront utilisées en sortie pour l’affichage des flux RSS.<br />Ceci ne prendra effet que lorsque vous aurez vidé le cache des flux RSS depuis le menu "Maintenance".<br />Si les fichiers de style des flux RSS ne sont pas disponibles sur le style utilisé, le style par défaut (toujours disponible et basé sur Prosilver) sera utilisé.<br />N’essayez pas d’utiliser les templates de Prosilver avec un autre style, les CSS ne correspondront certainement pas.',
	// Content
	'RSS_CONTENT' => 'Paramètres du contenu',
	'RSS_CONTENT_EXPLAIN' => 'Vous pouvez paramétrer ici diverses options de filtrage et mise en forme du contenu.<br />Ces paramètres peuvent être appliqués à l’ensemble des modules RSS selon vos paramètres d’héritage des options RSS.',
	'RSS_ALLOW_CONTENT' => 'Autoriser le contenu des éléments',
	'RSS_ALLOW_CONTENT_EXPLAIN' => 'Vous pouvez décider ici d’autoriser l’affichage partiel ou complet du contenu des messages dans les flux RSS.<br /><br /><u>Note</u> :<br /> Cette option augmente la charge du serveur. Les limites des flux avec contenu devraient être inférieures à celles des flux sans contenu.',
	'RSS_SUMARIZE' => 'Résumer les éléments',
	'RSS_SUMARIZE_EXPLAIN' => 'Vous pouvez résumer le contenu des messages affichés dans les flux.<br /> Cette limite détermine le nombre maximum de phrases, mots ou caractères, selon la méthode sélectionnée ci-dessous. Entrez 0 pour aucune limite.',
	'RSS_SUMARIZE_METHOD' => 'Méthode de résumé',
	'RSS_SUMARIZE_METHOD_EXPLAIN' => 'Vous pouvez choisir parmi trois méthodes différentes pour limiter le contenu des messages affichés dans les flux :<br />par nombre de phrases, par nombre de mots ou par nombre de caractères. Les balises BBCodes et les mots ne seront pas scindés.',
	'RSS_ALLOW_PROFILE' => 'Afficher le nom de l’auteur.',
	'RSS_ALLOW_PROFILE_EXPLAIN' => 'Le nom de l’auteur de l’élément peut être ajouté aux flux RSS si vous le désirez.',
	'RSS_ALLOW_PROFILE_LINKS' => 'Lien vers le profil',
	'RSS_ALLOW_PROFILE_LINKS_EXPLAIN' => 'Si le nom de l’auteur est affiché, vous pouvez décider d’en faire un lien menant vers la page correspondant à son profil phpBB.',
	'RSS_ALLOW_BBCODE' => 'Autoriser les BBCodes',
	'RSS_ALLOW_BBCODE_EXPLAIN' => 'Vous pouvez choisir d’inclure ou non les BBCodes dans le résultat.',
	'RSS_STRIP_BBCODE' => 'Filtres BBCodes',
	'RSS_STRIP_BBCODE_EXPLAIN' => 'Vous pouvez paramétrer ici une liste de BBCodes à exclure du traitement.<br />Le format est simple : <br /><ul><li><u>Une liste de BBCodes séparés par des virgules</u> : supprime les balises BBCodes mais en conserve le contenu.<br /><u>Exemple</u> : <b>img,b,quote</b><br />Dans cet exemple, les BBCodes img, b et quote ne seront pas interprétés, les balises BBCodes seront supprimées mais le contenu à l’intérieur des balises sera conservé.</li><li><u>Une liste de BBCodes séparés par des virgules avec l’option "double point" (":")</u> : Supprime les balises BBCodes et décide du contenu.<br /><u>Exemple</u> : <b>img:1,b:0,quote,code:1</b><br />Dans cet exemple, la balise BBCode img et le lien img seront supprimés, le gras (b) ne sera pas interprété mais le texte qui aurait du être en gras sera conservé, la balise quote ne sera pas interprétée mais son contenu sera conservé, les balises code et leur contenu seront retirés du résultat.</li></ul>Le filtre fonctionnera même si les BBCodes sont vides. Pratique pour effacer par exemple le contenu des balises code et les liens images de la mise en page.<br />Le filtrage est effectué avant le résumé.<br />Le paramètre magique "all" ( all:0 ou all:1 pour supprimer également tous les contenus des balises BBCcodes) va gérer tous les BBCodes en une fois.',
	'RSS_ALLOW_LINKS' => 'Autoriser les liens actifs',
	'RSS_ALLOW_LINKS_EXPLAIN' => 'Vous pouvez choisir ici d’activer ou non les liens dans le contenu des éléments.<br />En cas de non activation, les liens et les emails seront inclus dans le contenu mais ne seront pas cliquables.',
	'RSS_ALLOW_EMAILS' => 'Autoriser les emails',
	'RSS_ALLOW_EMAILS_EXPLAIN' => 'Vous choisissez ici d’avoir en sortie "email AT domain DOT com" au lieu de "email@domain.com" dans le contenu des éléments.',
	'RSS_ALLOW_SMILIES' => 'Autoriser les smileys',
	'RSS_ALLOW_SMILIES_EXPLAIN' => 'Vous pouvez choisir d’inclure ou ignorer les smileys dans le contenu.',
	'RSS_NOHTML' => 'Filtre HTML',
	'RSS_NOHTML_EXPLAIN' => 'Filtrer ou non le html des flux. Si vous activez l’option, les flux ne contiendront que du texte brut.',
	// Old URL handling
	'RSS_1XREDIR' => 'Gestion des URLs réécrites de GYM 1.x',
	'RSS_1XREDIR_EXPLAIN' => 'Active la détection des URLs réécrites au format GYM 1.x. Le module affichera un flux spécial fournissant la nouvelle URL du flux demandé.<br /><br /><u>Note</u> :<br /><br />Cette option nécessite la mise en place des rewriterules de compatibilité comme expliqué dans le fichier d’installation.',
));
?>