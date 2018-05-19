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
	// Main
	'ALL' => 'Tout',
	'MAIN' => 'GYM Sitemaps',
	'MAIN_MAIN_RESET' => 'Options globales GYM Sitemaps',
	'MAIN_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut du module GYM Sitemaps.',
	// Linking setup
	'GYM_LINKS_ACTIVATION' => 'Affichage des liens sur le forum',
	'GYM_LINKS_MAIN' => 'Liens principaux',
	'GYM_LINKS_MAIN_EXPLAIN' => 'Afficher ou non les liens vers les pages principales de GYM en pied de page : SitemapIndex, Flux RSS principal et liste des flux, index des plans et pages de news principale.',
	'GYM_LINKS_INDEX' => 'Liens sur l’index',
	'GYM_LINKS_INDEX_EXPLAIN' => 'Afficher ou non les liens vers les différent plans, flux, news et sitemaps de chaque forum sur l’index du forum. Ces liens sont ajoutés sous la descriptions des forums.',
	'GYM_LINKS_CAT' => 'Liens des forums',
	'GYM_LINKS_CAT_EXPLAIN' => 'Afficher ou non les liens vers les différent plans, flux, news et sitemaps du forum en cours. Ces liens sont ajoutés sous le titre du forum.',
	// Google sitemaps
	'GOOGLE' => 'Sitemaps Google',
	// Reset settings
	'GOOGLE_MAIN_RESET' => 'Options globales Sitemaps Google',
	'GOOGLE_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut des Sitemaps Google.',
	// RSS feeds
	'RSS' => 'Flux RSS',
	'RSS_ALTERNATE' => 'Liens alternate RSS',
	'RSS_ALTERNATE_EXPLAIN' => 'Afficher ou nons les liens alternate RSS dans la barre de navigation des navigateurs',
	'RSS_LINKING_TYPE' => 'Type de Liens RSS',
	'RSS_LINKING_TYPE_EXPLAIN' => 'Le type de flux dont les liens seront affichés sur les pages du forum.<br/>Peut prendre les valeurs :<br/><b>&bull; Flux News avec ou sans contenu</b><br/>Les éléments sont affichés dans l’ordre de parution, avec ou sans contenu,<br/><b>&bull; Flux Standards avec ou sans contenu</b><br/>Les éléments sont affichés dans l’ordre modification, avec ou sans contenu.<br/>Cette option n’affecte que les liens affichés automatiquement, pas les flux effectivement disponibles.',
	'RSS_LINKING_NEWS' => 'Flux News',
	'RSS_LINKING_NEWS_DIGEST' => 'Flux News avec contenu',
	'RSS_LINKING_REGULAR' => 'Flux Standards',
	'RSS_LINKING_REGULAR_DIGEST' => 'Flux Standards avec contenu',
	// Reset settings
	'RSS_MAIN_RESET' => 'Options globales Flux RSS',
	'RSS_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut des flux RSS.',
	'YAHOO' => 'Yahoo',
	// HTML
	'HTML_MAIN_RESET' => 'Options globales HTML',
	'HTML_MAIN_RESET_EXPLAIN' => 'Rétablir toutes les options par défaut des plan et news HTML',
	'HTML' => 'Html',

	// GYM authorisation array
	'GYM_AUTH_ADMIN' => 'Admin',
	'GYM_AUTH_GLOBALMOD' => 'Modérateurs Globaux',
	'GYM_AUTH_REG' => 'Connéctés',
	'GYM_AUTH_GUEST' => 'Invités',
	'GYM_AUTH_ALL' => 'Tous',
	'GYM_AUTH_NONE' => 'Aucun',
	// XSLT
	'GYM_STYLE' => 'Style',

	// Cache status
	'SEO_CACHE_FILE_TITLE' => 'Statut du cache',
	'SEO_CACHE_STATUS' => 'Le dossier configuré pour le cache est : <b>%s</b>',
	'SEO_CACHE_FOUND' => 'Le dossier du cache à bien été trouvé.',
	'SEO_CACHE_NOT_FOUND' => 'Le dossier du cache n’à pas été trouvé.',
	'SEO_CACHE_WRITABLE' => 'Le dossier du cache dispose des permissions en écriture.',
	'SEO_CACHE_UNWRITABLE' => 'Le dossier du cache ne dispose <b>pas</b> des permissions en écriture. Vous devez appliquer un CHMOD 0777 sur le dossier.',

	// Mod Rewrite
	'ACP_SEO_SIMPLE' => 'Simple',
	'ACP_SEO_MIXED' => 'Intermédiaire',
	'ACP_SEO_ADVANCED' => 'Avancé',
	'ACP_PHPBB_SEO_VERSION' => 'Version',
	'ACP_SEO_SUPPORT_FORUM' => 'Forum de support',
	'ACP_SEO_RELEASE_THREAD' => 'Sujet de mise à disposition',
	'ACP_SEO_REGISTER_TITLE' => 'enregistré',
	'ACP_SEO_REGISTER_UPDATE' => 'tenu informé des mises à jour.',
	'ACP_SEO_REGISTER_MSG' => 'Vous devez être %1$s pour être %2$s',

	// Maintenance
	'GYM_MAINTENANCE' => 'Maintenance',
	'GYM_MODULE_MAINTENANCE' => '%1$s maintenance',
	'GYM_MODULE_MAINTENANCE_EXPLAIN' => 'Vous pouvez gérer le cache utilisé par les modules %1$s.<br />Il y a deux sortes de cache : celui utilisé pour stocker les données affichées sur les pages publiques et celui utilisé pour la configuration des modules. Pour l’effacement du cache, par défaut c’est le cache de contenu qui est effacé pour les modules sélectionnés. Vous pouvez effacer le cache de configuration du/des module(s) sélectionnés en positionnant l’option "effacer le cache de configuration du module" sur Oui.',
	'GYM_CLEAR_CACHE' => 'Effacer le cache du module %1$s',
	'GYM_CLEAR_CACHE_EXPLAIN' => 'Vous pouvez effacer les fichiers du cache de contenu du module %1$s. Ces fichiers contiennent les données utilisées pour construire l’affichage du module %1$s.<br />Il peut être utile de les effacer pour forcer manuellement la mise à jour du cache.',
	'GYM_CLEAR_ACP_CACHE' => 'Effacer le cache de configuration du module %1$s',
	'GYM_CLEAR_ACP_CACHE_EXPLAIN' => 'Plutôt que d’effacer le cache de contenu, vous pouvez choisir d’effacer les fichiers du cache de configuration du module %1$s. Ces fichiers contiennent les données utilisées pour construire le panneau d’administration du module %1$s.<br />Il peut être utile de les effacer pour faire apparaitre de nouvelles options, introduites par exemple par une mise à jour du module.',
	'GYM_CACHE_CLEARED' => 'Vidage du cache réussie dans : ',
	'GYM_CACHE_NOT_CLEARED' => 'Un problème est survenu pendant le vidage du cache, veuillez vérifier les permissions du dossier (CHMOD 0666 ou 0777).<br />Le dossier configuré actuellement pour le cache est :',
	'GYM_FILE_CLEARED' => 'Fichier(s) effacé(s) : ',
	'GYM_CACHE_ACCESSED' => 'Le cache à vider ne contenait aucun élement, aucun fichier n’a donc été effacé dans : ',
	'MODULE_CACHE_CLEARED' => 'Vidage du cache de configuration du module effectué, si vous venez d’uploader un module, son panneau de configuration sera désormais visible.',

	// set defaults
	'GYM_SETTINGS' => 'Paramètres',
	'GYM_RESET_ALL' => 'Tout réinitialiser',
	'GYM_RESET_ALL_EXPLAIN' => 'Si vous cochez cette option, tous les jeux d’options ci-dessus seront rétablis à leurs valeurs par défaut.',
	'GYM_RESET' => 'Réinitialiser la configuration du module %1$s',
	'GYM_RESET_EXPLAIN' => 'Vous pouvez ci-dessous réinitialiser les options du module %1$s, soit du module complet, soit d’une sélection de paramètres.',

	'GYM_INSTALL' => 'Installation',
	'GYM_MODULE_INSTALL' => 'Installer le module %1$s',
	'GYM_MODULE_INSTALL_EXPLAIN' => 'Vous pouvez activer / désactiver le module %1$s.<br />Si vous venez d’uploader un module, vous devez l’activer avent de pouvoir l’utiliser.<br />Si malgré tout vous ne le voyez pas, essayer d’effacer son cache de configuration dans la partie maintenance.',

	// Titles
	'GYM_MAIN' => 'Paramètres du MOD GYM Sitemaps',
	'GYM_MAIN_EXPLAIN' => 'Il s’agit des paramètres communs à tous les types de rendu et tous les modules.<br /> Ils peuvent être appliqués à tous les types de rendu (Sitemaps Google, flux RSS ...) et/ou tous les modules en fonction de vos priorités de paramétrage.',
	'MAIN_MAIN' => 'Vue d’ensemble du MOD GYM Sitemaps',
	'MAIN_MAIN_EXPLAIN' => 'Le MOD GYM Sitemaps est un MOD phpBB très flexible et optimisé pour le référencement. Il vous permettra de construire des Sitemaps Google et des flux RSS 2.0 pour votre forum comme pour toute autre partie de votre site grâce à sa modularité.<br /><br /> Chaque type de rendu (Sitemaps Google, flux RSS ...) peut récupérer des éléments de plusieurs applications installées sur votre site (forum, album, etc.) en utilisant un module dédié.<br />Vous pouvez activer / désactiver chaque module en utilisant le panneau d’administration ; chaque module possède ses propres pages de configuration.<br /><br />Assurez vous d’avoir vérifié le %1$s, le support est assuré sur notre %2$s.<br />Le support et les discussions sur le référencement se font sur %3$s.<br />%4$s<br />Amusez vous bien ;-)',

	'GYM_GOOGLE' => 'Sitemaps Google',
	'GYM_GOOGLE_EXPLAIN' => 'Il s’agit des paramètres communs à tous les modules de type Sitemaps Google (forum, personnalisé, etc.).<br /> Ils peuvent être appliqués à tous les modules de type Sitemaps Google en fonction de votre configuration des priorités de paramétrage pour ce type de rendu et pour le niveau principal.',
	'GYM_RSS' => 'Flux RSS',
	'GYM_RSS_EXPLAIN' => 'Il s’agit des paramètres communs à l’ensemble des modules de type flux RSS (forum, personnalisé, etc.).<br /> Ils peuvent être appliqués à tous les modules de type flux RSS en fonction de votre configuration des priorités des paramétrage pour ce type de rendu et pour le niveau principal.',
	'GYM_HTML' => 'Pages HTML',
	'GYM_HTML_EXPLAIN' => 'Il s’agit des paramètres communs à l’ensemble des modules de type HTML (forum, personnalisé, etc.).<br /> Ils peuvent être appliqués à tous les modules de type HTML en fonction de votre configuration des priorités des paramétrage pour ce type de rendu et pour le niveau principal.',
	'GYM_MODULES_INSTALLED' => 'Module(s) actif(s)',
	'GYM_MODULES_UNINSTALLED' => 'Module(s) inactif(s)',

	// Overrides
	'GYM_OVERRIDE_GLOBAL' => 'Global',
	'GYM_OVERRIDE_OTYPE' => 'Type de Rendu',
	'GYM_OVERRIDE_MODULE' => 'Module',

	// override messages
	'GYM_OVERRIDED_GLOBAL' => 'Cette option est actuellement écrasée par un paramétrage prioritaire au niveau global (Configuration générale)',
	'GYM_OVERRIDED_OTYPE' => 'Cette option est actuellement écrasée par un paramétrage prioritaire au niveau du type de rendu',
	'GYM_OVERRIDED_MODULE' => 'Cette option est actuellement écrasée par un paramétrage prioritaire au niveau du module',
	'GYM_OVERRIDED_VALUE' => 'La valeur prise en compte actuellement est : ',
	'GYM_OVERRIDED_VALUE_NOTHING' => 'rien',
	'GYM_COULD_OVERRIDE' => 'Cette option peut être écrasée via les priorités mais ne l’est pas actuellement.',

	// Overridable / common options
	'GYM_CACHE' => 'Cache',
	'GYM_CACHE_EXPLAIN' => 'Vous pouvez définir ici diverses options liées au cache. Rappelez vous que ces paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage.',
	'GYM_MOD_SINCE' => 'Modifié depuis',
	'GYM_MOD_SINCE_EXPLAIN' => 'Permet de vérifier si le navigateur n’aurait pas une version à jour de la page demandée dans son cache pour le cas échéant lui demander de s’en servir plutôt que de solliciter le serveur inutilement.<br /><br /><u>Note</u> :<br /> Cette option concerne tous les types de rendu.',
	'GYM_CACHE_ON' => 'Activer la mise en cache',
	'GYM_CACHE_ON_EXPLAIN' => 'Vous pouvez activer / désactiver la mise en cache pour ce module.',
	'GYM_CACHE_FORCE_GZIP' => 'Forcer la compression du cache',
	'GYM_CACHE_FORCE_GZIP_EXPLAIN' => 'Vous permet de forcer la compression des fichiers en cache via compression GZip. Cela peut vous aider si vous manquez d’espace sur votre serveur, mais cela augmentera la charge de travail du serveur qui devra décompresser les fichiers avant de les envoyer aux navigateurs ne supportant pas la compression GZip.',
	'GYM_CACHE_MAX_AGE' => 'Durée de vie du cache',
	'GYM_CACHE_MAX_AGE_EXPLAIN' => 'Nombre maximal d’heures pendant lesquelles un fichier en cache sera utilisé avant d’être mis à jour. Si cette durée est atteinte et que la régénération automatique du cache est activée, tout fichier en cache sera rafraîchi lorsque quelqu’un y fera appel. Dans le cas contraire le cache sera uniquement remis à jour sur demande dans le panneau d’administration.',
	'GYM_CACHE_AUTO_REGEN' => 'Régénération automatique du cache',
	'GYM_CACHE_AUTO_REGEN_EXPLAIN' => 'Si vous activez la régénération automatique du cache, les listes générées seront remises à jour dès expiration du cache. Sinon, il vous faudra manuellement vider le cache à partir du menu Maintenance pour que les URLs récentes apparaissent dans vos listes.',
	'GYM_SHOWSTATS' => 'Statistiques du cache',
	'GYM_SHOWSTATS_EXPLAIN' => 'Afficher ou non les statistiques des durées de génération dans le code source.<br /><br /><u>Note</u> :<br />La durée de génération correspond au temps nécessaire pour générer la page. Cette étape n’est pas répétée lors d’un affichage depuis le cache',
	'GYM_CRITP_CACHE' => 'Crypter le nom des fichiers en cache',
	'GYM_CRITP_CACHE_EXPLAIN' => 'Il est plus sécurisé de crypter le nom des fichiers en cache, mais il peut être pratique de vérifier leurs noms en clair pour débugger.<br /><br /><u>Note</u> :<br /> Cette option concerne tous les types de fichiers en cache.',

	'GYM_MODREWRITE' => 'Réécriture d’URL',
	'GYM_MODREWRITE_EXPLAIN' => 'Vous pouvez définir ici plusieurs options de réécriture d’URL. Rappelez vous que ces paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage.',
	'GYM_MODREWRITE_ON' => 'Activer la réécriture d’URL',
	'GYM_MODREWRITE_ON_EXPLAIN' => 'Ceci active la réécriture d’URL pour les liens du module.<br /><br /><u>Note</u> :<br />Vous DEVEZ utiliser un serveur Apache avec le module mod_rewrite activé ou un serveur IIS avec le module isapi_rewrite ET configurer correctement les règles de rééecriture du module dans votre fichier .htaccess (ou httpd.ini avec IIS ).',
	'GYM_ZERO_DUPE_ON' => 'Activer le Zéro Duplicate',
	'GYM_ZERO_DUPE_ON_EXPLAIN' => 'Ceci active le Zéro Duplicate pour les liens du module.<br /><br /><u>Note</u> :<br /> les redirections ne seront fonctionnelles qu’après (re)génération du cache.',
	'GYM_MODRTYPE' => 'Type de réécriture d’URL',
	'GYM_MODRTYPE_EXPLAIN' => 'Si vous utilisez le MOD Rewrite de phpBB SEO, ces options seront écrasées par des valeurs détectées automatiquement.<br />Quatre niveaux de réécriture d’URL peuvent être utilisés : Aucun, Simple, Intermédiaire et Avancé :<br /><ul><li><b>Aucun :</b> pas de réécriture d’URL.<br /></li><li><b>Simple :</b> réécriture d’URL statique pour tous les liens, pas d’injection de titre.<br /></li><li><b>Intermédiaire :</b> les titres de forums et de catégories sont injectés dans les URLs, mais les titres de sujets restent réécrits statiquement.<br /></li><li><b>Avancé :</b> tous les titres sont injectés dans les URLs.</li></ul>',

	'GYM_GZIP' => 'GZip',
	'GYM_GZIP_EXPLAIN' => 'Vous pouvez définir ici plusieurs options pour la compression GZip. Rappelez vous que ces paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage.%1$s',
	'GYM_GZIP_FORCED' => '<br /><b style="color:red;">NOTE :</b> La compression GZip est activée dans la configuration de phpBB. Elle sera donc forcée dans le module.',
	'GYM_GZIP_CONFIGURABLE' => '<br /><b style="color:red;">NOTE :</b> La compression GZip n’est pas activée dans la configuration de phpBB. Vous pouvez configurer les options ci-dessous à votre convenance.',
	'GYM_GZIP_ON' => 'Activer la compression GZip',
	'GYM_GZIP_ON_EXPLAIN' => 'Activer la compression GZip permet de diminuer substantiellement la taille des fichiers transmis au navigateur et donc de réduire la durée de transmission.',
	'GYM_GZIP_EXT' => 'Suffixe GZip',
	'GYM_GZIP_EXT_EXPLAIN' => 'Vous pouvez choisir d’utiliser ou non l’extension .gz dans les URLs du module. Cela s’applique uniquement si la compression GZip et la réécriture d’URL sont activées.',
	'GYM_GZIP_LEVEL' => 'Niveau de compression GZip',
	'GYM_GZIP_LEVEL_EXPLAIN' => 'Nombre entier entre 1 et 9, 9 étant la compression la plus élevée. Il n’est généralement pas utile d’aller au delà de 6.<br /><br /><u>Note</u> :<br /> Cette option concerne tous les types de rendu.',

	'GYM_LIMIT' => 'Limites',
	'GYM_LIMIT_EXPLAIN' => 'Vous pouvez définir ici les limites à appliquer lors de la génération des différents résultats : nombre d’URLs, cycles SQL (nombre d’éléments par requête SQL) et âge des éléments listés.<br />Rappelez vous que ces paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage.',
	'GYM_URL_LIMIT' => 'Limite du nombre d’éléments',
	'GYM_URL_LIMIT_EXPLAIN' => 'Le nombre maximum d’éléments à générer.',
	'GYM_SQL_LIMIT' => 'Cycles SQL',
	'GYM_SQL_LIMIT_EXPLAIN' => 'Pour tous les types de rendu (hormis les plans html), les requêtes SQL sont divisées en plusieurs cycles afin de pouvoir lister un grand nombre d’éléments sans lancer de requêtes trop consommatrices de ressources.<br />Définissez ici le nombre maximum d’éléments à requêter lors d’un cycle. Le nombre de requêtes SQL nécessaires sera égal au nombre d’éléments listés divisé par le nombre d’élements par cycle.',
	'GYM_TIME_LIMIT' => 'Limite temporelle',
	'GYM_TIME_LIMIT_EXPLAIN' => 'Permet de limiter en nombre de jours, l’ancienneté maximale des éléments pris en compte dans les listes. Peut s’avérer utile pour limiter la charge serveur avec des bases de données volumineuses. Entrez 0 pour aucune limite.',

	'GYM_SORT' => 'Tri',
	'GYM_SORT_EXPLAIN' => 'Vous pouvez choisir ici la façon de trier les résultats.<br />Rappelez vous que ces paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage.',
	'GYM_SORT_TYPE' => 'Ordre de tri par défaut',
	'GYM_SORT_TYPE_EXPLAIN' => 'Par défaut les liens sont tous triés par date de dernière activité (tri descendant). <br /> Vous pouvez basculer en tri ascendant pour par exemple permettre aux moteurs de recherche de trouver facilement des liens vers du contenu ancien.<br />Rappelez vous que ces paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage.',

	'GYM_PAGINATION' => 'Pagination',
	'GYM_PAGINATION_EXPLAIN' => 'Vous pouvez définir ici des options variées concernant la pagination. Rappelez vous que ces paramètres peuvent être écrasés en fonction de votre configuration des priorités de paramétrage.',
	'GYM_PAGINATION_ON' => 'Activer la pagination',
	'GYM_PAGINATION_ON_EXPLAIN' => 'Vous pouvez décider de produire des listes d’élements avec pagination (si disponible). Pour un sujet de forum par exemple, cela se traduira par l’ajout de liens additionnels vers les différentes pages du sujet.',
	'GYM_LIMITDOWN' => 'Pagination: limite basse',
	'GYM_LIMITDOWN_EXPLAIN' => 'Entrez le nombre de pages (en partant de la première) à intégrer dans le résultat.',
	'GYM_LIMITUP' => 'Pagination: limite haute',
	'GYM_LIMITUP_EXPLAIN' => 'Entrez le nombre de pages (en partant de la dernière) à intégrer dans le résultat.',

	'GYM_OVERRIDE' => 'Priorités',
	'GYM_OVERRIDE_EXPLAIN' => 'GYM Sitemaps est un composant avec une approche flexible basée sur les modules. Chaque type de rendu (Sitemaps Google, flux RSS, etc.) utilise ses propres modules pour produire différents types de listes d’éléments. Le premier module disponible pour chaque type de rendu est par exemple le module forum, listant les éléments du forum.<br /><br />Plusieurs options telles que la réécriture d’URL, le cache, la compression GZip, etc. sont répétées à différents niveaux du panneau d’administration du MOD GYM Sitemaps. Cela permet d’utiliser des paramétrages différents pour la même option en fonction du type de rendu et du module générant les résultats. Cependant, vous pourriez préférer activer par exemple la réécriture d’URL sur tous les modules en une seule fois (tous les types de rendu et tous les modules).<br /> C’est à cela que sert le paramétrage de l’héritage des options.<br /><br />Le processus d’héritage des options part du niveau le plus élevé, la configuration générale pour passer ensuite au niveau des types de rendu (Sitemaps Google, flux RSS, etc.) pour finir au plus bas niveau, les modules individuels (forum, album, etc.).<br />L’héritage des paramètres peut prendre trois valeurs distinctes :<br /><ul><li><b>Global :</b> Les paramètres de la configuration générale sont utilisés<br /></li><li><b>Type de rendu :</b> Les paramètres définis au niveau du type de rendu (Sitemaps Google, flux RSS, etc.) seront utilisés pour chacun des modules de ce type de rendu.<br /></li><li><b>Module :</b> Le niveau de priorité le plus faible sera utilisé en premier. Exemple : utilisation du paramètre défini au niveau du module; s’il n’est pas défini, utilisation du paramètre défini au niveau du type de rendu; si celui ci n’est pas défini non plus, utilisation du paramètre de la configuration générale.</li></ul>',
	'GYM_OVERRIDE_ON' => 'Héritage à partir de la configuration générale',
	'GYM_OVERRIDE_ON_EXPLAIN' => 'Vous pouvez activer ou désactiver l’héritage à partir de la configuration générale. Désactiver cette option revient à paramétrer les différents types d’héritage suivants sur la valeur "Type de rendu", laissant les priorités définies au niveau du type de rendu définir  celles au niveau module.',
	'GYM_OVERRIDE_MAIN' => 'Héritage par défaut',
	'GYM_OVERRIDE_MAIN_EXPLAIN' => 'Défini de quel niveau seront héritées les options qui n’appartiennent pas à l’un des types d’options prédéfinis ci-dessous.',
	'GYM_OVERRIDE_CACHE' => 'Héritage des options du cache',
	'GYM_OVERRIDE_CACHE_EXPLAIN' => 'Défini d’où sont héritées les options liées au cache.',
	'GYM_OVERRIDE_GZIP' => 'Héritage des options de compression GZip',
	'GYM_OVERRIDE_GZIP_EXPLAIN' => 'Défini d’où sont héritées les options liés à la compression GZip.',
	'GYM_OVERRIDE_MODREWRITE' => 'Héritage des options de réécriture d’URL',
	'GYM_OVERRIDE_MODREWRITE_EXPLAIN' => 'Défini d’où sont héritées les options liées à la réécriture d’URL.',
	'GYM_OVERRIDE_LIMIT' => 'Héritage des options sur les limites',
	'GYM_OVERRIDE_LIMIT_EXPLAIN' => 'Défini d’où sont héritées les options liées aux limites.',
	'GYM_OVERRIDE_PAGINATION' => 'Héritage des options de pagination',
	'GYM_OVERRIDE_PAGINATION_EXPLAIN' => 'Défini d’où sont héritées les options liées à la pagination.',
	'GYM_OVERRIDE_SORT' => 'Héritage des options de tri',
	'GYM_OVERRIDE_SORT_EXPLAIN' => 'Défini d’où sont héritées les options liées au tri.',

	// Mod rewrite
	'GYM_MODREWRITE_ADVANCED' => 'Avancé',
	'GYM_MODREWRITE_MIXED' => 'Intermédiaire',
	'GYM_MODREWRITE_SIMPLE' => 'Simple',
	'GYM_MODREWRITE_NONE' => 'Aucun',

	// Sorting
	'GYM_ASC' => 'Ascendant',
	'GYM_DESC' => 'Descendant',

	// Other
	// robots.txt
	'GYM_CHECK_ROBOTS' => 'Vérifier les exclusions du fichier robots.txt',
	'GYM_CHECK_ROBOTS_EXPLAIN' => 'Vérifier et appliquer aux listes d’URLs les exclusions du fichier robots.txt s’il existe. Le MOD prend en compte automatiquement les mises à jour du fichier robots.txt.<br />Cette option est particulièrement pratique pour les imports TXT et XML, quand on n’est pas certain que les listes d’URLs importées ne contiennent aucune URL interdite.<br/><br /><u>Note</u> :<br />Cette option impose plus de travail sur le fichier source, il est conseillé de l’utiliser avec le cache activé.',
	// summarize method
	'GYM_METHOD_CHARS' => 'Par nombre de lettres',
	'GYM_METHOD_WORDS' => 'Par nombre de mots',
	'GYM_METHOD_LINES' => 'Par nombre de lignes',
));
?>
