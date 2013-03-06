<?php
/**
*
* acp_board [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: board.php, v1.29 2010/03/01 00:34:00 Elglobo Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

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

// Board Settings
$lang = array_merge($lang, array(
	'ACP_BOARD_SETTINGS_EXPLAIN'	=> 'Vous pouvez modifier les paramètres de base de votre forum, depuis le nom du site jusqu’à la validation de l’inscription par message privé.',
	'CUSTOM_DATEFORMAT'				=> 'Personnalisée',
	'DEFAULT_DATE_FORMAT'			=> 'Format de la date',
	'DEFAULT_DATE_FORMAT_EXPLAIN'	=> 'Le format de la date est le même que la fonction <code>date</code> de PHP',
	'DEFAULT_LANGUAGE'				=> 'Langue par défaut',
	'DEFAULT_STYLE'					=> 'Style par défaut',
	'DISABLE_BOARD'					=> 'Désactiver le forum',
	'DISABLE_BOARD_EXPLAIN'			=> 'Ceci va rendre le forum inaccessible aux utilisateurs. Vous pouvez aussi rentrer un message court (255 caractères) pour leur en expliquer la raison.',
	'OVERRIDE_STYLE'				=> 'Annuler le style de l’utilisateur',
	'OVERRIDE_STYLE_EXPLAIN'		=> 'Remplace le style de l’utilisateur par le style par défaut.',
	'SITE_DESC'						=> 'Description du site',
	'SITE_NAME'						=> 'Nom du site',
	'SYSTEM_DST'					=> 'Activer l’heure d’été',
	'SYSTEM_TIMEZONE'				=> 'Fuseau horaire',
	'WARNINGS_EXPIRE'				=> 'Durée de l’avertissement',
	'WARNINGS_EXPIRE_EXPLAIN'		=> 'Nombre de jours qui s’écoulera avant que l’avertissement expire automatiquement.',
));

// Board Features
$lang = array_merge($lang, array(
	'ACP_BOARD_FEATURES_EXPLAIN'	=> 'Vous pouvez activer/désactiver plusieurs fonctionnalités du forum.',

	'ALLOW_ATTACHMENTS'			=> 'Autoriser les fichiers joints',
	'ALLOW_BIRTHDAYS'			=> 'Autoriser les anniversaires',
	'ALLOW_BIRTHDAYS_EXPLAIN'	=> 'Autorise la saisie des dates anniversaires et l’affichage de l’âge dans les profils. Notez que l’affichage des anniversaires sur l’index du forum est contrôlé par un paramètre de charge différent.',
	'ALLOW_BOOKMARKS'			=> 'Autoriser la mise en favoris des sujets',
	'ALLOW_BOOKMARKS_EXPLAIN'	=> 'L’utilisateur est autorisé à mettre des sujets en favoris.',
	'ALLOW_BBCODE'				=> 'Autoriser les BBCodes',
	'ALLOW_FORUM_NOTIFY'		=> 'Autoriser la surveillance des forums',
	'ALLOW_NAME_CHANGE'			=> 'Autoriser les changements de nom d’utilisateur',
	'ALLOW_NO_CENSORS'			=> 'Autoriser la désactivation de la censure',
	'ALLOW_NO_CENSORS_EXPLAIN'	=> 'Les utilisateurs peuvent choisir de désactiver la censure automatique des messages ou messages privés.',
	'ALLOW_PM_ATTACHMENTS'		=> 'Autoriser les fichiers joints dans les messages privés',
	'ALLOW_PM_REPORT'			=> 'Autoriser les utilisateurs à rapporter les messages privés',
	'ALLOW_PM_REPORT_EXPLAIN'	=> 'Si cette option est activée, les utilisateurs ont la possibilité de rapporter aux modérateurs du forum un message privé qu’ils ont reçu ou envoyé. Ces messages privés seront alors visibles dans le panneau de modération.',
	'ALLOW_QUICK_REPLY'			=> 'Autoriser la réponse rapide',
	'ALLOW_QUICK_REPLY_EXPLAIN'	=> 'Cette option vous permet de désactiver le module de réponse rapide sur l’ensemble du forum. Si activé, les paramètres spécifiques de forum seront utilisés pour déterminer si la réponse rapide est affichée pour chacun des forums.',
	'ALLOW_QUICK_REPLY_BUTTON'	=> 'Soumettre et activer la réponse rapide dans tous les forums',
	'ALLOW_SIG'					=> 'Autoriser les signatures',
	'ALLOW_SIG_BBCODE'			=> 'Autoriser les BBCodes dans les signatures d’utilisateur',
	'ALLOW_SIG_FLASH'			=> 'Autoriser l’utilisation du BBCode <code>[FLASH]</code> dans la signature',
	'ALLOW_SIG_IMG'				=> 'Autoriser l’utilisation du BBCode <code>[IMG]</code> dans la signature',
	'ALLOW_SIG_LINKS'			=> 'Autoriser les liens dans les signatures d’utilisateur',
	'ALLOW_SIG_LINKS_EXPLAIN'	=> 'Si désactivé, le BBCode <code>[URL]</code> et la transformation automatique des textes en liens seront désactivés.',
	'ALLOW_SIG_SMILIES'			=> 'Autoriser les smileys dans les signatures d’utilisateur',
	'ALLOW_SMILIES'				=> 'Autoriser les smileys',
	'ALLOW_TOPIC_NOTIFY'		=> 'Autoriser la surveillance des sujets',
	'BOARD_PM'					=> 'Messagerie privée',
	'BOARD_PM_EXPLAIN'			=> 'Activer ou désactiver la messagerie privée pour tous les utilisateurs.',
));

// Avatar Settings
$lang = array_merge($lang, array(
	'ACP_AVATAR_SETTINGS_EXPLAIN'	=> 'Les avatars sont généralement de petites images uniques qu’un utilisateur choisit pour le représenter. Selon le style, ils sont normalement affichés sous le nom d’utilisateur lors de la visualisation de sujets. Vous pouvez choisir quelle méthode l’utilisateur peut utiliser pour choisir son avatar. Dans le cas où vous autorisez l’envoi d’avatar, vous devez indiquer ci-dessous le nom du répertoire en question et vous assurer des droits en écriture de ce répertoire. Notez également que les limitations de taille ne sont imposées qu’aux avatars chargés et ne concernent pas les avatars dont on aura fourni un lien externe.',
	'ALLOW_AVATARS'					=> 'Activer les avatars',
	'ALLOW_AVATARS_EXPLAIN'			=> 'Autorise l’utilisation générale des avatars;<br />Si vous désactivez l’utilisation générale des avatars ou les avatars affichés selon une méthode particulière, les avatars désactivés ne seront plus affichés sur le forum, mais les utilisateurs seront toujours capables de télécharger leur propre avatar dans leur panneau d’utilisateur.',

	'ALLOW_LOCAL'					=> 'Activer la galerie d’avatars',
	'ALLOW_REMOTE'					=> 'Autoriser les avatars distants',
	'ALLOW_REMOTE_EXPLAIN'			=> 'Avatars liés depuis un autre site',
	'ALLOW_REMOTE_UPLOAD'			=> 'Autoriser le chargement distant d’avatar',
	'ALLOW_REMOTE_UPLOAD_EXPLAIN'	=> 'Autorise le chargement d’avatars d’un autre site Internet.',
	'ALLOW_UPLOAD'					=> 'Autoriser le chargement d’avatar',
	'AVATAR_GALLERY_PATH'			=> 'Répertoire de la galerie d’avatars',
	'AVATAR_GALLERY_PATH_EXPLAIN'	=> 'Chemin d’accès depuis le répertoire racine de phpBB vers les images préchargées, exemple: <samp>images/avatars/gallery</samp>.',
	'AVATAR_STORAGE_PATH'			=> 'Dossier de stockage des avatars',
	'AVATAR_STORAGE_PATH_EXPLAIN'	=> 'Chemin d’accès depuis le répertoire racine de phpBB, exemple: <samp>images/avatars/upload</samp>.',
	'MAX_AVATAR_SIZE'				=> 'Dimensions maximales d’un avatar',
	'MAX_AVATAR_SIZE_EXPLAIN'		=> 'Largeur x Hauteur en pixels.',
	'MAX_FILESIZE'					=> 'Taille maximale d’un avatar',
	'MAX_FILESIZE_EXPLAIN'			=> 'Pour les avatars envoyés.',
	'MIN_AVATAR_SIZE'				=> 'Dimensions minimales d’un avatar',
	'MIN_AVATAR_SIZE_EXPLAIN'		=> 'Largeur x Hauteur en pixels.',
));

// Message Settings
$lang = array_merge($lang, array(
	'ACP_MESSAGE_SETTINGS_EXPLAIN'		=> 'Vous pouvez modifier tous les paramètres de la messagerie privée.',

	'ALLOW_BBCODE_PM'			=> 'Autoriser les BBCodes dans les messages privés',
	'ALLOW_FLASH_PM'			=> 'Autoriser l’utilisation du BBCode <code>[FLASH]</code>',
	'ALLOW_FLASH_PM_EXPLAIN'	=> 'Notez que l’utilisation du Flash dans les messages privés, si activé ici, dépend également des permissions.',
	'ALLOW_FORWARD_PM'			=> 'Autoriser le transfert des messages privés',
	'ALLOW_IMG_PM'				=> 'Autoriser l’utilisation du BBCode <code>[IMG]</code>',
	'ALLOW_MASS_PM'				=> 'Autoriser l’envoi de messages privés à plusieurs utilisateurs et groupes',
	'ALLOW_MASS_PM_EXPLAIN' 	=> 'L’envoi aux groupes peut être ajusté par groupe dans l’écran de réglage du groupe.',
	'ALLOW_PRINT_PM'			=> 'Autoriser la visualisation de l’impression dans la messagerie privée',
	'ALLOW_QUOTE_PM'			=> 'Autoriser les citations dans les messages privés',
	'ALLOW_SIG_PM'				=> 'Autoriser les signatures dans les messages privés',
	'ALLOW_SMILIES_PM'			=> 'Autoriser les smileys dans les messages privés',
	'BOXES_LIMIT'				=> 'Nombre de messages privés maximum par dossier',
	'BOXES_LIMIT_EXPLAIN'		=> 'Les utilisateurs ne peuvent pas recevoir plus que ce nombre de messages dans chacun de leurs dossiers de message privé. Mettre “0” pour permettre un nombre de message illimité.',
	'BOXES_MAX'					=> 'Nombre maximum de dossiers',
	'BOXES_MAX_EXPLAIN'			=> 'Les utilisateurs peuvent créer ce nombre de dossiers pour leurs messages privés.',
	'ENABLE_PM_ICONS'			=> 'Autoriser les icônes de sujet dans les messages privés',
	'FULL_FOLDER_ACTION'		=> 'Action par défaut lorsqu’un dossier est plein',
	'FULL_FOLDER_ACTION_EXPLAIN'=> 'Action par défaut à effectuer lorsque le dossier d’un utilisateur est plein, dans le cas où l’action indiquée par l’utilisateur n’est pas applicable. La seule exception s’applique au dossier des “Messages envoyés” où l’action par défaut est de toujours supprimer les anciens messages.',
	'HOLD_NEW_MESSAGES'			=> 'Rejeter les nouveaux messages',
	'PM_EDIT_TIME'				=> 'Temps limite d’édition',
	'PM_EDIT_TIME_EXPLAIN'		=> 'Temps après lequel on ne peut plus éditer un message privé quand il n’a pas encore été délivré. Mettre “0” pour illimité.',
	'PM_MAX_RECIPIENTS'			=> 'Nombre maximum autorisé de destinataires',
	'PM_MAX_RECIPIENTS_EXPLAIN' => 'Le nombre maximum autorisé de destinataires d’un message privé. Une valeur à “0” indique un nombre illimité de destinataires. Ce paramètre peut être ajusté pour chaque groupe dans l’écran de réglage du groupe.',
));

// Post Settings
$lang = array_merge($lang, array(
	'ACP_POST_SETTINGS_EXPLAIN'			=> 'Vous pouvez définir tous les paramètres par défaut pour les messages.',
	'ALLOW_POST_LINKS'					=> 'Autoriser les liens dans les messages et messages privés',
	'ALLOW_POST_LINKS_EXPLAIN'			=> 'Si désactivé, le BBCode <code>[URL]</code> et la transformation automatique des textes en liens seront désactivés.',
	'ALLOW_POST_FLASH'					=> 'Autoriser l’utilisation du BBCode <code>[FLASH]</code> dans les messages',
	'ALLOW_POST_FLASH_EXPLAIN'			=> 'Si désactivé, le BBCode <code>[FLASH]</code> sera désactivé. Autrement, le système de permission déterminera les membres pouvant utiliser le BBCode <code>[FLASH]</code>.',

	'BUMP_INTERVAL'					=> 'Intervalle de remontée de sujet',
	'BUMP_INTERVAL_EXPLAIN'			=> 'Nombre des minutes, d’heures, ou de jours entre la date du dernier message et la possibilité de remonter le sujet. Mettre “0” pour illimité.',
	'CHAR_LIMIT'					=> 'Nombre maximum de caractères par message',
	'CHAR_LIMIT_EXPLAIN'			=> 'Le nombre de caractères autorisés dans un message. Mettre “0” pour illimité.',
	'DELETE_TIME'					=> 'Limiter le temps de suppression',
	'DELETE_TIME_EXPLAIN'			=> 'Limite le temps disponible pour effacer un nouveau message. Mettre “0” pour désactiver ce comportement.',
	'DISPLAY_LAST_EDITED'			=> 'Afficher la raison de la dernière édition',
	'DISPLAY_LAST_EDITED_EXPLAIN'	=> 'Choisissez si l’information sur la date de la dernière édition doit être affichée ou non dans les messages.',
	'EDIT_TIME'						=> 'Temps limite d’édition',
	'EDIT_TIME_EXPLAIN'				=> 'Durée d’autorisation d’édition du message après l’avoir posté.',
	'FLOOD_INTERVAL'				=> 'Intervalle de flood',
	'FLOOD_INTERVAL_EXPLAIN'		=> 'Nombre de secondes qu’un utilisateur doit patienter entre la publication de nouveaux messages. Pour autoriser les utilisateurs à ignorer cela, modifiez leurs permissions.',
	'HOT_THRESHOLD'					=> 'Seuil de popularité des sujets',
	'HOT_THRESHOLD_EXPLAIN'			=> 'Nombre de messages requis afin qu’un sujet soit affiché comme étant populaire. Mettre “0” pour désactiver les sujets populaires.',
	'MAX_POLL_OPTIONS'				=> 'Nombre maximum d’options de vote',
	'MAX_POST_FONT_SIZE'			=> 'Taille maximale de la police',
	'MAX_POST_FONT_SIZE_EXPLAIN'	=> 'Taille maximale de la police dans un message. Mettre “0” pour illimité.',
	'MAX_POST_IMG_HEIGHT'			=> 'Hauteur maximale d’une image',
	'MAX_POST_IMG_HEIGHT_EXPLAIN'	=> 'Hauteur maximale d’un fichier image ou flash dans un message. Mettre “0” pour illimité.',
	'MAX_POST_IMG_WIDTH'			=> 'Largeur maximale d’une image',
	'MAX_POST_IMG_WIDTH_EXPLAIN'	=> 'Largeur maximale d’un fichier image ou flash dans un message. Mettre “0” pour illimité.',
	'MAX_POST_URLS'					=> 'Nombre maximum de liens',
	'MAX_POST_URLS_EXPLAIN'			=> 'Nombre maximum de liens dans un message. Mettre “0” pour illimité.',
	'MIN_CHAR_LIMIT'				=> 'Nombre minimum de caractères par message',
	'MIN_CHAR_LIMIT_EXPLAIN'		=> 'Nombre minimum de caractères qu’un utilisateur a besoin de taper dans un message/message privé',
	'POSTING'						=> 'Publication',
	'POSTS_PER_PAGE'				=> 'Messages par page',
	'QUOTE_DEPTH_LIMIT'				=> 'Nombre maximum de citations imbriquées',
	'QUOTE_DEPTH_LIMIT_EXPLAIN'		=> 'Nombre maximum de citations imbriquées dans un message. Mettre “0” pour illimité.',
	'SMILIES_LIMIT'					=> 'Nombre maximum de smileys par message',
	'SMILIES_LIMIT_EXPLAIN'			=> 'Nombre maximum de smileys dans un message. Mettre “0” pour illimité.',
	'SMILIES_PER_PAGE'				=> 'Smileys par page',
	'TOPICS_PER_PAGE'				=> 'Sujets par page',
));

// Signature Settings
$lang = array_merge($lang, array(
	'ACP_SIGNATURE_SETTINGS_EXPLAIN'	=> 'Vous pouvez modifier les paramètres pour les signatures.',

	'MAX_SIG_FONT_SIZE'				=> 'Taille maximale de la police dans les signatures',
	'MAX_SIG_FONT_SIZE_EXPLAIN'		=> 'Taille de police maximale autorisée dans les signatures d’utilisateur. Mettre “0” pour illimité.',
	'MAX_SIG_IMG_HEIGHT'			=> 'Hauteur maximale d’une image dans les signatures',
	'MAX_SIG_IMG_HEIGHT_EXPLAIN'	=> 'Hauteur maximale d’un fichier image/flash dans les signatures d’utilisateur. Mettre “0” pour illimité.',
	'MAX_SIG_IMG_WIDTH'				=> 'Largeur maximale d’une image dans les signatures',
	'MAX_SIG_IMG_WIDTH_EXPLAIN'		=> 'Largeur maximale d’un fichier image/flash dans les signatures d’utilisateur. Mettre “0” pour illimité.',
	'MAX_SIG_LENGTH'				=> 'Longueur maximale de la signature',
	'MAX_SIG_LENGTH_EXPLAIN'		=> 'Nombre de caractères maximum dans les signatures d’utilisateur.',
	'MAX_SIG_SMILIES'				=> 'Nombre maximum de smileys par signature',
	'MAX_SIG_SMILIES_EXPLAIN'		=> 'Nombre maximum de smileys dans les signatures d’utilisateur. Mettre “0” pour illimité.',
	'MAX_SIG_URLS'					=> 'Nombre maximum de liens dans les signatures',
	'MAX_SIG_URLS_EXPLAIN'			=> 'Nombre maximum de liens dans la signature d’utilisateur. Mettre “0” pour illimité.',
));

// Registration Settings
$lang = array_merge($lang, array(
	'ACP_REGISTER_SETTINGS_EXPLAIN'		=> 'Vous pouvez modifier les paramètres relatifs à l’inscription et aux profils d’utilisateurs.',

	'ACC_ACTIVATION'			=> 'Activation de compte',
	'ACC_ACTIVATION_EXPLAIN'	=> 'Cela détermine si les utilisateurs ont accès au forum immédiatement ou si une confirmation est requise. Vous pouvez également désactiver complètement les nouvelles inscriptions.',
	'NEW_MEMBER_POST_LIMIT'			=> 'Limite de message d’un nouveau membre',
	'NEW_MEMBER_POST_LIMIT_EXPLAIN'	=> 'Les nouveaux membres resteront dans le groupe <em>Nouveaux utilisateurs enregistrés</em> jusqu’à qu’ils atteignent ce nombre de messages. Vous pouvez utiliser ce groupe pour éviter qu’ils utilisent le système de messagerie privé ou la révision de leurs messages. <strong>Mettre “0” pour désactiver cette fonctionnalité.</strong>',
	'NEW_MEMBER_GROUP_DEFAULT'		=> 'Mettre le groupe des nouveaux utilisateurs enregistrés par défaut',
	'NEW_MEMBER_GROUP_DEFAULT_EXPLAIN'	=> 'Si ce paramètre est activé et qu’une limite de message pour les nouveaux membres est indiquée, les nouveaux utilisateurs enregistrés ne seront pas simplement placés dans le groupe <em>Nouveaux utilisateurs enregistrés</em>, mais ce groupe deviendra également leur groupe par défaut. Cela peut s’avérer pratique si vous voulez assigner un rang et/ou un avatar de groupe par défaut afin que les utilisateurs en héritent.',
	'ACC_ADMIN'					=> 'Par l’administrateur',
	'ACC_DISABLE'				=> 'Désactiver',
	'ACC_NONE'					=> 'Aucun',
	'ACC_USER'					=> 'Par l’utilisateur',
//	'ACC_USER_ADMIN'			=> 'User + Admin',
	'ALLOW_EMAIL_REUSE'			=> 'Autoriser les adresses e-mail à être réutilisées',
	'ALLOW_EMAIL_REUSE_EXPLAIN'	=> 'Plusieurs utilisateurs peuvent s’enregistrer avec la même adresse e-mail.',
	'COPPA'						=> 'COPPA',
	'COPPA_FAX'					=> 'Numéro de fax COPPA',
	'COPPA_MAIL'				=> 'Adresse e-mail COPPA',
	'COPPA_MAIL_EXPLAIN'		=> 'Ceci est l’adresse e-mail où les parents enverront les formulaires d’inscription COPPA.',
	'ENABLE_COPPA'				=> 'Activer la COPPA',
	'ENABLE_COPPA_EXPLAIN'		=> 'Cela oblige les utilisateurs à déclarer qu’ils ont 13 ans ou plus afin d’être en conformité avec la COPPA. Si cela est désactivé, le groupe spécial COPPA ne sera plus affiché.',
	'MAX_CHARS'					=> 'Max',
	'MIN_CHARS'					=> 'Min',
	'NO_AUTH_PLUGIN'			=> 'Aucun module d’authentification trouvé.',
	'PASSWORD_LENGTH'			=> 'Longueur du mot de passe',
	'PASSWORD_LENGTH_EXPLAIN'	=> 'Nombre de caractères minimum et maximum dans les mots de passe.',
	'REG_LIMIT'					=> 'Tentatives d’inscription',
	'REG_LIMIT_EXPLAIN'			=> 'Nombre de tentatives que les utilisateurs pourront faire dans la saisie du code de confirmation avant que leur session n’expire.',
	'USERNAME_ALPHA_ONLY'		=> 'Alphanumériques seulement',
	'USERNAME_ALPHA_SPACERS'	=> 'Alphanumériques et espaces',
	'USERNAME_ASCII'			=> 'ASCII (aucun caractère unicode international)',
	'USERNAME_LETTER_NUM'		=> 'Tous chiffres et lettres',
	'USERNAME_LETTER_NUM_SPACERS'	=> 'Tous chiffres, lettres et espaces',
	'USERNAME_CHARS'			=> 'Limite des caractéres du nom d’utilisateur',
	'USERNAME_CHARS_ANY'		=> 'N’importe quel caractère',
	'USERNAME_CHARS_EXPLAIN'	=> 'Restreint du type de caractères qui peut être utilisé dans les noms d’utilisateur, les espaces comprennent: espace, -, +, _, [ et ].',
	'USERNAME_LENGTH'			=> 'Longueur du nom d’utilisateur',
	'USERNAME_LENGTH_EXPLAIN'	=> 'Nombre de caractères minimum et maximum dans les noms d’utilisateur.',
));

// Feeds
$lang = array_merge($lang, array(
	'ACP_FEED_MANAGEMENT'				=> 'Paramètres généraux de publication des flux',
	'ACP_FEED_MANAGEMENT_EXPLAIN'		=> 'Ce module rend disponible différents flux ATOM, en parsant les BBCodes dans les messages pour les rendre lisible dans des flux extérieurs.',
	 
	'ACP_FEED_GENERAL'					=> 'Paramètres de flux général',
 	'ACP_FEED_POST_BASED'				=> 'Paramètres de flux de message',
	'ACP_FEED_TOPIC_BASED'				=> 'Paramètres de flux de sujet',
 	'ACP_FEED_SETTINGS_OTHER'			=> 'Autres flux et paramétrages',
	 
	'ACP_FEED_ENABLE'					=> 'Activer les flux',
	'ACP_FEED_ENABLE_EXPLAIN'			=> 'Active ou non, les flux ATOM pour le forum entier.<br />En désactivant les flux, peu importe la manière dont sont réglées les options ci-dessous.',
	'ACP_FEED_LIMIT'					=> 'Nombre d’articles',
	'ACP_FEED_LIMIT_EXPLAIN'			=> 'Le nombre maximum d’articles de flux à afficher.',
	 
	'ACP_FEED_OVERALL'					=> 'Activer les flux sur l’ensemble du forum',
	'ACP_FEED_OVERALL_EXPLAIN'			=> 'Permet de suivre les nouveaux messages sur l’ensemble du forum.',
	'ACP_FEED_FORUM'					=> 'Activer les flux par forum',
	'ACP_FEED_FORUM_EXPLAIN'			=> 'Permet de suivre les nouveaux messages d’un forum et ses sous-forums.',
	'ACP_FEED_TOPIC'					=> 'Activer les flux par sujet',
	'ACP_FEED_TOPIC_EXPLAIN'			=> 'Permet de suivre les nouveaux messages d’un sujet en particulier.',
	 
	'ACP_FEED_TOPICS_NEW'				=> 'Activer le flux des nouveaux sujets',
	'ACP_FEED_TOPICS_NEW_EXPLAIN'		=> 'Active le flux des “nouveaux sujets”, qui affiche les derniers sujets créés, y compris le premier message.',
	'ACP_FEED_TOPICS_ACTIVE'			=> 'Activer le flux des sujets actifs',
	'ACP_FEED_TOPICS_ACTIVE_EXPLAIN'	=> 'Active le flux des “sujet actifs”, qui affiche les derniers sujets actifs, y compris le dernier message.',
	'ACP_FEED_NEWS'						=> 'Flux des nouvelles',
	'ACP_FEED_NEWS_EXPLAIN'				=> 'Sélectionne le premier message depuis ces forums. Ne sélectionnez aucun forum pour désactiver le flux des nouvelles.<br />Sélectionner plusieurs forums en maintenant la touche <samp>CTRL</samp> et en cliquant.',
	  
	'ACP_FEED_OVERALL_FORUMS'			=> 'Activer le flux des forums',
	'ACP_FEED_OVERALL_FORUMS_EXPLAIN'	=> 'Active le flux de “tous les forums”, ce qui affiche une liste des forums.',
	 
	'ACP_FEED_HTTP_AUTH'				=> 'Autoriser l’authentification HTTP',
	'ACP_FEED_HTTP_AUTH_EXPLAIN'		=> 'Active l’authentification HTTP, ce qui autorise les utilisateurs à recevoir le contenu qui est masqué aux invités en ajoutant le paramètre <samp>auth=http</samp> à l’URL du flux. Notez que certaines installations de PHP nécessite d’effectuer des modifications additionnelles sur le fichier .htaccess. Toutes les instructions sont contenues dans ce fichier.',
	'ACP_FEED_ITEM_STATISTICS'			=> 'Statistiques de l’article',
	'ACP_FEED_ITEM_STATISTICS_EXPLAIN'	=> 'Affiche les statistiques indivuelles sous les articles de flux<br />(Exemple: Posté par, date et heure, Réponses, Vues)',
	'ACP_FEED_EXCLUDE_ID'				=> 'Exclure ces forums',
	'ACP_FEED_EXCLUDE_ID_EXPLAIN'		=> 'Le contenu de ces forums <strong>se sera pas inclus dans les flux</strong>. Ne sélectionnez aucun forum pour lire les données de tous les forums.<br />Sélectionner plusieurs forums en maintenant la touche <samp>CTRL</samp> et en cliquant.',
));

 // Visual Confirmation Settings
$lang = array_merge($lang, array(
	'ACP_VC_SETTINGS_EXPLAIN'				=> 'Vous pouvez sélectionner et configurer les plugins CAPTCHA, qui utilisent différents moyens pour rejeter les tentatives d’inscription des robots.',
	'AVAILABLE_CAPTCHAS'					=> 'Plugins disponibles',
	'CAPTCHA_UNAVAILABLE'					=> 'Le CAPTCHA ne peut pas être sélectionné car les prérequis ne sont pas remplis.',
	'CAPTCHA_GD'							=> 'GD CAPTCHA',
	'CAPTCHA_GD_3D'							=> 'GD Captcha 3D',
	'CAPTCHA_GD_FOREGROUND_NOISE'			=> 'GD CAPTCHA avec bruit de fond',
	'CAPTCHA_GD_EXPLAIN'					=> 'Utilise GD pour un CAPTCHA plus avancé.',
	'CAPTCHA_GD_FOREGROUND_NOISE_EXPLAIN'	=> 'Utiliser un bruit de fond pour faire un CAPTCHA plus difficile à déchiffrer par les robots.',
	'CAPTCHA_GD_X_GRID'						=> 'GD CAPTCHA avec bruit de fond x-axis',
	'CAPTCHA_GD_X_GRID_EXPLAIN'				=> 'Utiliser le paramètre ci-dessous pour rendre la confirmation visuelle plus difficile à déchiffrer. Mettre “0” pour désactiver le bruit de fond x-axis.',
	'CAPTCHA_GD_Y_GRID'						=> 'GD CAPTCHA avec bruit de fond y-axis',
	'CAPTCHA_GD_Y_GRID_EXPLAIN'				=> 'Utiliser le paramètre ci-dessous pour rendre la confirmation visuelle plus difficile à déchiffrer. Mettre “0” pour désactiver le bruit de fond y-axis.',
	'CAPTCHA_GD_WAVE'						=> 'Distorsion ondulatoire du GD CAPTCHA',
	'CAPTCHA_GD_WAVE_EXPLAIN'				=> 'Cela appliquera une distorsion ondulatoire au CAPTCHA.',
 	'CAPTCHA_GD_3D_NOISE'					=> 'Ajouter des objets de bruit en 3D',
	'CAPTCHA_GD_3D_NOISE_EXPLAIN'			=> 'Cela ajoutera des objets supplémentaires au CAPTCHA, par-dessus les lettres.',
	'CAPTCHA_GD_FONTS'						=> 'Utiliser différentes polices',
	'CAPTCHA_GD_FONTS_EXPLAIN'				=> 'Ce paramètre contrôle le nombre différent de formes de lettres qui sont utilisées. Vous pouvez seulement utiliser les formes par défaut ou introduire des lettres modifiées. L’ajout de lettres en minuscule est également possible.',
	'CAPTCHA_FONT_DEFAULT'					=> 'Défaut',
	'CAPTCHA_FONT_NEW'						=> 'Nouvelles formes',
	'CAPTCHA_FONT_LOWER'					=> 'Utiliser également des minuscules',

	'CAPTCHA_NO_GD'							=> 'CAPTCHA sans GD',
	'CAPTCHA_PREVIEW_MSG'					=> 'Vos modifications pour les paramètres de la confirmation visuelle n’ont pas été sauvegardées. Ceci est juste un aperçu.',
	'CAPTCHA_PREVIEW_EXPLAIN'				=> 'Voici le CAPTCHA tel qu’il apparaîtrait avec vos paramètres actuels.',

	'CAPTCHA_SELECT'						=> 'Plugins CAPTCHA installés',
	'CAPTCHA_SELECT_EXPLAIN'				=> 'La liste déroulante affiche les plugins CAPTCHA reconnus par le forum. Les plugins grisés ne sont pas disponibles immédiatement et peuvent nécessiter au préalable une configuration pour être utilisés.',
	'CAPTCHA_CONFIGURE'						=> 'Configurer les CAPTCHAs',
	'CAPTCHA_CONFIGURE_EXPLAIN'				=> 'Change les paramètres pour le CAPTCHA sélectionné.',
	'CONFIGURE'								=> 'Configurer',
	'CAPTCHA_NO_OPTIONS'					=> 'Ce CAPTCHA n’a pas d’options de configuration.',

	'VISUAL_CONFIRM_POST'					=> 'Activer la confirmation visuelle pour les visiteurs',
	'VISUAL_CONFIRM_POST_EXPLAIN'			=> 'Oblige les invités à saisir un code aléatoire correspondant à une image afin d’empêcher la publication de messages en masse.',
	'VISUAL_CONFIRM_REG'					=> 'Activer la confirmation visuelle pour les inscriptions',
	'VISUAL_CONFIRM_REG_EXPLAIN'			=> 'Oblige les nouveaux utilisateurs à saisir un code aléatoire correspondant à une image afin d’empêcher les inscriptions en masse.',
	'VISUAL_CONFIRM_REFRESH'				=> 'Autoriser les utilisateurs à rafraîchir l’image de confirmation',
	'VISUAL_CONFIRM_REFRESH_EXPLAIN'		=> 'Autorise les utilisateurs à demander de nouveaux codes de confirmation s’ils sont incapables de déchiffrer la confirmation visuelle durant l’inscription. Certains plugins peuvent ne pas supporter cette option.',
));

// Cookie Settings
$lang = array_merge($lang, array(
	'ACP_COOKIE_SETTINGS_EXPLAIN'		=> 'Ces informations définissent les données utilisées pour envoyer les cookies aux navigateurs de vos utilisateurs. Dans la majorité des cas, les valeurs par défaut pour les paramètres de cookie suffisent. Si vous avez besoin de les modifier, faîtes-le avec soin car des paramètres incorrects peuvent empêcher les utilisateurs de se connecter.',

	'COOKIE_DOMAIN'				=> 'Domaine du cookie',
	'COOKIE_NAME'				=> 'Nom du cookie',
	'COOKIE_PATH'				=> 'Chemin du cookie',
	'COOKIE_SECURE'				=> 'Cookie sécurisé',
	'COOKIE_SECURE_EXPLAIN'		=> 'Si votre serveur fonctionne par l’intermédiaire du protocole SSL, activez cette option sinon laissez désactivé. Si vous activez cette option alors que votre serveur n’est pas sous le protocole SSL, des erreurs se produiront lors des redirections.',
	'ONLINE_LENGTH'				=> 'Durée d’apparition dans la liste des utilisateurs en ligne',
	'ONLINE_LENGTH_EXPLAIN'		=> 'Nombre de minutes après lequel les utilisateurs inactifs n’apparaîtont plus dans la liste des utilisateurs en ligne. Plus cette valeur est élevée, plus le traitement requis pour générer la liste sera long.',
	'SESSION_LENGTH'			=> 'Durée de la session',
	'SESSION_LENGTH_EXPLAIN'	=> 'Les sessions expireront après cette durée, en secondes.',
));

// Load Settings
$lang = array_merge($lang, array(
	'ACP_LOAD_SETTINGS_EXPLAIN'	=> 'Vous pouvez activer et désactiver certaines fonctions du forum pour réduire la quantité de traitement requise. Sur la plupart des serveurs, il n’est pas nécessaire de désactiver ces fonctionnalités. Cependant, sur certains systèmes ou hébergements mutualisés, il peut être préférable de désactiver certaines possibilités dont vous n’avez pas réellement besoin. Vous pouvez également indiquer des limites pour la charge du système et les sessions actives au delà desquelles le forum sera hors-ligne.',

	'CUSTOM_PROFILE_FIELDS'			=> 'Champs de profil personnalisés',
	'LIMIT_LOAD'					=> 'Limiter la charge système',
	'LIMIT_LOAD_EXPLAIN'			=> 'Si la charge du système dépasse cette valeur durant une minute, le forum sera automatiquement indisponible. Une valeur à 1.0 équivaut à environ 100% d’utilisation d’un processeur. Cela ne fonctionne que sur les serveurs basés sous UNIX et où cette information est accessible. Cette valeur se réinitialise à 0 si phpBB n’arrive pas à obtenir la valeur de la charge du système.',
	'LIMIT_SESSIONS'				=> 'Nombre de sessions',
	'LIMIT_SESSIONS_EXPLAIN'		=> 'Si le nombre de sessions dépasse cette valeur durant une minute, le forum sera indisponible. Mettre “0” pour illimité.',
	'LOAD_CPF_MEMBERLIST'			=> 'Autoriser les styles à afficher les champs personnalisés dans la liste des membres',
	'LOAD_CPF_VIEWPROFILE'			=> 'Afficher les champs personnalisés dans les profils d’utilisateur',
	'LOAD_CPF_VIEWTOPIC'			=> 'Afficher les champs personnalisés dans les pages de sujet',
	'LOAD_USER_ACTIVITY'			=> 'Afficher l’activité des utilisateurs',
	'LOAD_USER_ACTIVITY_EXPLAIN'	=> 'Affiche les sujets/forums actifs dans les profils d’utilisateur et dans le panneau de l’utilisateur. Il est recommandé de désactiver cette option pour les forums de plus d’un million de messages.',
	'RECOMPILE_STYLES'				=> 'Recompiler les différents éléments du style',
	'RECOMPILE_STYLES_EXPLAIN'		=> 'Cherche les nouvelles mises à jour du style dans le système de fichiers et les recompile.',
	'YES_ANON_READ_MARKING'			=> 'Activer l’indicateur de lecture pour les visiteurs',
	'YES_ANON_READ_MARKING_EXPLAIN'	=> 'Enregistre l’état lu/non lu pour les visiteurs. Si désactivé, les messages sont toujours considérés comme lus pour les visiteurs.',
	'YES_BIRTHDAYS'					=> 'Activer l’affichage de la liste des anniversaires',
	'YES_BIRTHDAYS_EXPLAIN'			=> 'Si désactivé, la liste des anniversaires ne sera plus affichée. Ce paramètre n’est pris en compte que si la fonctionnalité des anniversaires est également activée.',
	'YES_JUMPBOX'					=> 'Activer l’affichage de l’accès rapide aux forums',
	'YES_MODERATORS'				=> 'Activer l’affichage des modérateurs',
	'YES_ONLINE'					=> 'Activer l’affichage de la liste des utilisateurs en ligne',
	'YES_ONLINE_EXPLAIN'			=> 'Affiche ces informations sur l’accueil, dans les forums et sujets.',
	'YES_ONLINE_GUESTS'				=> 'Activer l’affichage des visiteurs dans “Qui est en ligne”',
	'YES_ONLINE_GUESTS_EXPLAIN'		=> 'Affiche les informations concernant les visiteurs dans “Qui est en ligne”.',
	'YES_ONLINE_TRACK'				=> 'Activer l’affichage de l’état de connexion',
	'YES_ONLINE_TRACK_EXPLAIN'		=> 'Affiche dans le profil public et les sujets le statut de l’utilisateur.',
	'YES_POST_MARKING'				=> 'Activer les sujets pointés',
	'YES_POST_MARKING_EXPLAIN'		=> 'Indique si le membre a participé au sujet.',
	'YES_READ_MARKING'				=> 'Activer l’indicateur de lecture par le serveur',
	'YES_READ_MARKING_EXPLAIN'		=> 'Enregistre l’état lu/non lu dans la base plutôt que dans un cookie.',));

// Auth settings
$lang = array_merge($lang, array(
	'ACP_AUTH_SETTINGS_EXPLAIN'	=> 'phpBB supporte les plugins d’authentification ou modules. Ceux-ci vous permettent de déterminer de quelle manière les utilisateurs sont authentifiés lorsqu’ils se connectent au forum. Par défaut, trois plugins sont fournis; DB, LDAP et Apache. Toutes les méthodes ne nécessitent pas d’informations complémentaires, remplissez uniquement les champs s’ils sont appropriés à la méthode sélectionnée.',

	'AUTH_METHOD'				=> 'Sélectionnez une méthode d’authentification',

	'APACHE_SETUP_BEFORE_USE'	=> 'Vous devez configurer l’authentification apache avant de passer phpBB à cette méthode d’authentification. Gardez en tête que le nom d’utilisateur utilisé pour l’authentification apache doit être identique à votre nom d’utilisateur phpBB. L’authentification Apache peut seulement être utilisée avec <var>mod_php</var> (pas avec une version CGI) et <var>safe_mode</var> doit être désactivé.',

	'LDAP_DN'						=> 'Base LDAP vers <var>DN</var>',
	'LDAP_DN_EXPLAIN'				=> 'Ceci est le “Distinguished Name”, situant toutes les informations utilisateurs, exemple: <samp>o=Mon entreprise, c=FR</samp>.',
	'LDAP_EMAIL'					=> 'Attribut LDAP des adresses e-mail',
	'LDAP_EMAIL_EXPLAIN'			=> 'Ceci est le nom de l’attribut de l’e-mail de vos utilisateurs (s’il existe) afin de régler automatiquement l’adresse e-mail des nouveaux utilisateurs. Laissez cette case vide pour que l’adresse e-mail résultante soit vide pour les utilisateurs qui se connectent pour la première fois.',
	'LDAP_INCORRECT_USER_PASSWORD'	=> 'La connexion au serveur LDAP a échoué avec les nom d’utilisateur et mot de passe indiqués.',
	'LDAP_NO_EMAIL'					=> 'Cet attribut d’adresse e-mail n’existe pas.',
	'LDAP_NO_IDENTITY'				=> 'Impossible de trouver un identifiant de connexion pour %s',
	'LDAP_PASSWORD'					=> 'Mot de passe LDAP',
	'LDAP_PASSWORD_EXPLAIN'			=> 'Laissez cette case vide pour utiliser une connexion anonyme. Sinon, indiquez le mot de passe pour l’utilisateur indiqué ci-dessus. Ceci est obligatoire pour les serveurs possédant un Active Directory.<br /><em><strong>ATTENTION:</strong> Ce mot de passe sera stocké en clair dans votre de base de données et sera visible par n’importe qui ayant accès à votre base de données ou à cette page de configuration.</em>',
	'LDAP_PORT'						=> 'Port du serveur LDAP',
	'LDAP_PORT_EXPLAIN'				=> 'Si vous le souhaitez, vous pouvez indiquer un port qui devrait être employé pour se connecter au serveur LDAP au lieu du port par défaut 389.',
	'LDAP_SERVER'					=> 'Nom du serveur LDAP',
	'LDAP_SERVER_EXPLAIN'			=> 'Si vous utilisez LDAP, ceci est le nom d’hôte ou l’adresse IP du serveur LDAP. Sinon, vous pouvez préciser une URL comme ldap://hostname:port/',
	'LDAP_UID'						=> 'Clé <var>uid</var> LDAP',
	'LDAP_UID_EXPLAIN'				=> 'Ceci est la clé utilisée pour la recherche d’un identifiant de connexion, exemple: <var>uid</var>, <var>sn</var>, etc.',
	'LDAP_USER'						=> 'Utilisateur <var>dn</var> LDAP',
	'LDAP_USER_EXPLAIN'				=> 'Laissez cette case vide pour utiliser une connexion anonyme. Si cela est renseigné dans phpBB, utilisez le “Distinguished Name” que vous avez indiqué lors des tentatives de connexion afin de trouver l’utilisateur correct, comme <samp>uid=Nom,ou=MonUnité,o=MaCompagnie,c=FR</samp>. Requis pour les serveurs possédant un Active Directory.',
	'LDAP_USER_FILTER'				=> 'Filtre de l’utilisateur LDAP',
	'LDAP_USER_FILTER_EXPLAIN'		=> 'Si vous le souhaitez, vous pouvez en plus limiter les objets recherchés avec des filtres additionnels. Par exemple <samp>objectClass=posixGroup</samp> deviendrait lors de l’utilisation <samp>(&amp;(uid=$username)(objectClass=posixGroup))</samp>',
));

// Server Settings
$lang = array_merge($lang, array(
	'ACP_SERVER_SETTINGS_EXPLAIN'	=> 'Vous pouvez définir les paramètres du serveur et du domaine. Vérifiez que les données saisies soient précises, afin d’éviter que vos e-mails ne contiennent des données erronées. Lorsque vous saisissez le nom de domaine, n’oubliez pas qu’il doit contenir http:// ou un autre protocole. Ne modifiez le numéro de port que si vous savez que votre serveur utilise une valeur différente, le port 80 est correct dans la majorité des cas.',

	'ENABLE_GZIP'				=> 'Activer la compression GZip',
	'ENABLE_GZIP_EXPLAIN'		=> 'Le contenu généré sera compressé avant d’être envoyé à l’utilisateur. Cela peut réduire le trafic mais également augmenter l’utilisation du CPU à la fois du côté serveur et client. Cela nécessite que l’extension PHP zlib soit chargée.',
	'FORCE_SERVER_VARS'			=> 'Forcer les paramètres URL du serveur',
	'FORCE_SERVER_VARS_EXPLAIN'	=> 'Si “Oui” les paramètres définis ici seront utilisés à la place des valeurs déterminées automatiquement.',
	'ICONS_PATH'				=> 'Emplacement des icônes de message',
	'ICONS_PATH_EXPLAIN'		=> 'Chemin depuis le répertoire racine de phpBB, exemple: <samp>images/icons</samp>',
	'PATH_SETTINGS'				=> 'Chemins d’accès',
	'RANKS_PATH'				=> 'Emplacement des images de rang',
	'RANKS_PATH_EXPLAIN'		=> 'Chemin depuis le répertoire racine de phpBB, exemple: <samp>images/ranks</samp>',
	'SCRIPT_PATH'				=> 'Chemin du script',
	'SCRIPT_PATH_EXPLAIN'		=> 'Chemin d’accès où sont situés les fichiers phpBB depuis le nom de domaine. exemple: <samp>/phpBB3</samp>',
	'SERVER_NAME'				=> 'Nom de domaine',
	'SERVER_NAME_EXPLAIN'		=> 'Nom de domaine du serveur exécutant phpBB. (par exemple: <samp>www.exemple.com</samp>)',
	'SERVER_PORT'				=> 'Port du serveur',
	'SERVER_PORT_EXPLAIN'		=> 'Port utilisé par le serveur, normalement 80, changez seulement si différent.',
	'SERVER_PROTOCOL'			=> 'Protocole du serveur',
	'SERVER_PROTOCOL_EXPLAIN'	=> 'Utilisé comme protocole du serveur si ces paramètres sont forcés. Si vide ou non forcé, le protocole est déterminé par les paramètres de cookie sécurisé. (<samp>http://</samp> ou <samp>https://</samp>)',
	'SERVER_URL_SETTINGS'		=> 'Paramètres des URLs du serveur',
	'SMILIES_PATH'				=> 'Emplacement des smileys',
	'SMILIES_PATH_EXPLAIN'		=> 'Chemin depuis le répertoire racine de phpBB, exemple: <samp>images/smilies</samp>',
	'UPLOAD_ICONS_PATH'			=> 'Emplacement des icônes de groupes d’extensions',
	'UPLOAD_ICONS_PATH_EXPLAIN'	=> 'Chemin depuis le répertoire racine de phpBB, exemple: <samp>images/upload_icons</samp>',
	));

// Security Settings
$lang = array_merge($lang, array(
	'ACP_SECURITY_SETTINGS_EXPLAIN'		=> 'Vous pouvez définir les paramètres relatifs à l’identification et à la session.',

	'ALL'							=> 'Tous',
	'ALLOW_AUTOLOGIN'				=> 'Autoriser les connexions automatiques',
	'ALLOW_AUTOLOGIN_EXPLAIN'		=> 'Détermine si les utilisateurs peuvent être connectés automatiquement quand ils visitent le forum.',
	'AUTOLOGIN_LENGTH'				=> 'Expiration des clés de connexion automatique (en jours)',
	'AUTOLOGIN_LENGTH_EXPLAIN'		=> 'Nombre de jours après lequel les clés de connexions automatiques sont supprimées ou “0” pour désactiver.',
	'BROWSER_VALID'					=> 'Valider le navigateur',
	'BROWSER_VALID_EXPLAIN'			=> 'Active la validation du navigateur pour chaque session, ce qui améliore la sécurité.',
	'CHECK_DNSBL'					=> 'Comparer l’IP avec la liste noire DNS',
	'CHECK_DNSBL_EXPLAIN'			=> 'Si activé, l’adresse IP de l’utilisateur est vérifiée par les services DNSBL à l’inscription et à la publication de messages: <a href="http://spamcop.net">spamcop.net</a> et <a href="http://www.spamhaus.org">www.spamhaus.org</a>. Cette vérification peut prendre un moment, selon la configuration du serveur. Si vous remarquez des ralentissements ou de mauvaises appréciations, il est recommandé de désactiver cette vérification.',
	'CLASS_B'						=> 'A.B',
	'CLASS_C'						=> 'A.B.C',
	'EMAIL_CHECK_MX'				=> 'Vérifier l’e-mail pour un enregistrement MX valide',
	'EMAIL_CHECK_MX_EXPLAIN'		=> 'Si activé, le domaine de l’e-mail fourni lors de l’inscription et des modifications de profil est contrôlé, pour s’assurer qu’il possède un enregistrement MX valide.',
	'FORCE_PASS_CHANGE'				=> 'Forcer la modification du mot de passe',
	'FORCE_PASS_CHANGE_EXPLAIN'		=> 'Oblige l’utilisateur à modifier son mot de passe après un certain nombre de jours. Mettre “0” pour désactiver ce comportement.',
	'FORM_TIME_MAX'					=> 'Temps maximum lors de l’envoi des formulaires',
	'FORM_TIME_MAX_EXPLAIN'			=> 'Détermine le temps dont un utilisateur dispose pour envoyer un formulaire. Mettre “-1” pour désactiver. Notez qu’un formulaire peut devenir invalide si la session expire, et cela indépendamment de ce paramètre.',
	'FORM_SID_GUESTS'				=> 'Lier les formulaires aux sessions des invités',
	'FORM_SID_GUESTS_EXPLAIN'		=> 'Si activé, les formulaires émis aux invités seront exclusifs à leur session. Cela peut entraîner quelques problèmes avec certains fournisseurs d’accès.',
	'FORWARDED_FOR_VALID'			=> 'Entête <var>X_FORWARDED_FOR</var> validée',
	'FORWARDED_FOR_VALID_EXPLAIN'	=> 'Les sessions seront seulement continuées si l’entête <var> X_FORWARDED_FOR </var> envoyée est égale à celle envoyée avec la requête précédente. L’en-tête <var>X_FORWARDED_FOR</var> vérifiera également si les adresses IP n’ont pas été bannies.',
	'IP_VALID'						=> 'Validation de session IP',
	'IP_VALID_EXPLAIN'				=> 'Détermine quelle partie de l’adresse IP des utilisateurs sera utilisée pour valider une session : <samp>Tous</samp> compare l’adresse complète, <samp>A.B.C</samp> les premiers x.x.x, <samp>A.B</samp> les premiers x.x, <samp>Aucune</samp> désactive la vérification. Pour les adresses IPv6, <samp>A.B.C</samp> compare les 4 premiers blocs et <samp>A.B</samp> les 3 premiers blocs.',
	'MAX_LOGIN_ATTEMPTS'			=> 'Nombre maximal de tentatives de connexion',
	'MAX_LOGIN_ATTEMPTS_EXPLAIN'	=> 'Après ce nombre d’échecs de connexion, l’utilisateur devra également confirmer sa connexion visuellement (confirmation visuelle).',
	'NO_IP_VALIDATION'				=> 'Aucune',
	'NO_REF_VALIDATION' 			=> 'Aucune',
	'PASSWORD_TYPE'					=> 'Complexité du mot de passe',
	'PASSWORD_TYPE_EXPLAIN'			=> 'Détermine la complexité requise pour définir ou modifier un mot de passe, les options suivantes incluent les précédentes.',
	'PASS_TYPE_ALPHA'				=> 'Doit contenir des lettres et des chiffres',
	'PASS_TYPE_ANY'					=> 'Aucune condition',
	'PASS_TYPE_CASE'				=> 'Doit contenir des minuscules et majuscules',
	'PASS_TYPE_SYMBOL'				=> 'Doit contenir des symboles',
	'REF_HOST' 						=> 'Valider uniquement l’hôte',
	'REF_PATH' 						=> 'Valider également le chemin',
	'REFERER_VALID' 				=> 'Valider le référant',
	'REFERER_VALID_EXPLAIN'  => 'Si activé, le référant des requêtes POST sera comparé au paramétrage effectué pour le chemin de l’hôte ou du script. Ceci peut entraîner certains problèmes avec les forums utilisant plusieurs domaines ou des connexions externes.',
	'TPL_ALLOW_PHP'					=> 'Autoriser le PHP dans les templates',
	'TPL_ALLOW_PHP_EXPLAIN'			=> 'Si cette option est activée, les instructions <code>PHP</code> et <code>INCLUDEPHP</code> seront reconnues et analysées dans les templates.',
));

// Email Settings
$lang = array_merge($lang, array(
	'ACP_EMAIL_SETTINGS_EXPLAIN'	=> 'Ces informations sont utilisées lors de l’envoi d’e-mails à vos utilisateurs. Assurez-vous que l’adresse e-mail indiquée soit valide, les messages refusés ou indélivrables seront probablement retournés à cette adresse. Si votre fournisseur d’hébergement ne fournit pas nativement un service de messagerie électronique (basé sur PHP) vous pouvez envoyer directement les messages en utilisant SMTP. Cela nécessite l’adresse d’un serveur approprié (contactez votre fournisseur d’hébergement si besoin). Si le serveur requiert une authentification (et seulement dans ce cas) entrez le nom d’utilisateur, le mot de passe et la méthode d’authentification nécessaire.',

	'ADMIN_EMAIL'					=> 'Adresse e-mail de retour',
	'ADMIN_EMAIL_EXPLAIN'			=> 'Cette adresse sera utilisée comme adresse de retour dans tous les e-mails, l’adresse e-mail du contact technique. Elle sera toujours utilisée comme l’adresse du <samp>Return-Path</samp> et de l’<samp>expéditeur</samp> dans les e-mails.',
	'BOARD_EMAIL_FORM'				=> 'Les utilisateurs envoient des e-mails via le forum',
	'BOARD_EMAIL_FORM_EXPLAIN'		=> 'Au lieu de montrer publiquement les adresses e-mails des utilisateurs, les utilisateurs peuvent envoyer des e-mails via le forum.',
	'BOARD_HIDE_EMAILS'				=> 'Masquer les adresses e-mails',
	'BOARD_HIDE_EMAILS_EXPLAIN'		=> 'Cette fonction préserve les adresses e-mails complètement privées.',
	'CONTACT_EMAIL'					=> 'E-mail de contact',
	'CONTACT_EMAIL_EXPLAIN'			=> 'Cette adresse sera utilisée lorsqu’un contact particulier est nécessaire, exemple: spam, erreur survenue, etc. Elle sera toujours utilisée comme l’adresse de l’<samp>expéditeur</samp> et <samp>adresse de réponse</samp> dans les e-mails.',
	'EMAIL_FUNCTION_NAME'			=> 'Nom de la fonction e-mail',
	'EMAIL_FUNCTION_NAME_EXPLAIN'	=> 'La fonction e-mail est utilisée pour envoyer des e-mails via PHP.',
	'EMAIL_PACKAGE_SIZE'			=> 'Taille des paquets d’e-mails',
	'EMAIL_PACKAGE_SIZE_EXPLAIN'	=> 'Ceci est le nombre d’e-mails envoyés dans un paquet. Cette option est appliquée à la file d’attente des messages; Réglez cette option à “0” si vous rencontrez des problèmes avec des notifications de messages non délivrés.',
	'EMAIL_SIG'						=> 'Signature de l’e-mail',
	'EMAIL_SIG_EXPLAIN'				=> 'Ce texte sera inséré à la fin de tous les e-mails envoyés par le forum.',
	'ENABLE_EMAIL'					=> 'Autoriser l’envoi d’e-mail via le forum',
	'ENABLE_EMAIL_EXPLAIN'			=> 'Si désactivé, aucun e-mail ne sera envoyé par le forum. <em>Notez que les paramètres d’activation de compte “par l’utilisateur” et “par l’administrateur” nécessite que ce réglage soit activé. Si vous utilisez actuellement l’un de ces deux paramètres dans les paramètres généraux d’activation, en désactivant ce réglage, les nouvelles inscriptions ne nécessiteront plus aucune activation.</em>',
	'SMTP_AUTH_METHOD'				=> 'Méthode d’authentification SMTP',
	'SMTP_AUTH_METHOD_EXPLAIN'		=> 'Seulement utilisé si un nom d’utilisateur et un mot de passe a été renseigné. Contactez votre fournisseur d’accès si vous n’êtes pas sûr de la méthode à utiliser.',
	'SMTP_CRAM_MD5'					=> 'CRAM-MD5',
	'SMTP_DIGEST_MD5'				=> 'DIGEST-MD5',
	'SMTP_LOGIN'					=> 'LOGIN',
	'SMTP_PASSWORD'					=> 'Mot de passe SMTP',
	'SMTP_PASSWORD_EXPLAIN'			=> 'Saisissez un mot de passe uniquement si votre serveur SMTP en requiert un.<br /><em><strong>Attention:</strong> ce mot de passe sera stocké en clair dans la base de données, visible de toute personne ayant accès à votre base de données ou à cette page de configuration.</em>',
	'SMTP_PLAIN'					=> 'PLAIN',
	'SMTP_POP_BEFORE_SMTP'			=> 'POP-AVANT-SMTP',
	'SMTP_PORT'						=> 'Port du serveur SMTP',
	'SMTP_PORT_EXPLAIN'				=> 'Modifiez cela uniquement si vous savez que votre serveur SMTP utilise un port différent.',
	'SMTP_SERVER'					=> 'Adresse du serveur SMTP',
	'SMTP_SETTINGS'					=> 'Paramètres SMTP',
	'SMTP_USERNAME'					=> 'Nom d’utilisateur SMTP',
	'SMTP_USERNAME_EXPLAIN'			=> 'Saisissez un nom d’utilisateur uniquement si votre serveur SMTP en requiert un.',
	'USE_SMTP'						=> 'Utiliser un serveur SMTP pour l’envoi d’e-mails',
	'USE_SMTP_EXPLAIN'				=> 'Sélectionnez “Oui” si vous voulez ou devez envoyer les e-mails par l’intermédiaire d’un serveur au lieu d’utiliser la fonction e-mail locale.',
));

// Jabber settings
$lang = array_merge($lang, array(
	'ACP_JABBER_SETTINGS_EXPLAIN'	=> 'Vous pouvez activer et contrôler l’utilisation de Jabber pour la messagerie instantanée et les notifications du forum. Jabber est un protocole open-source et donc librement utilisable. Certains serveurs Jabber contiennent des passerelles qui vous permettent de contacter des utilisateurs sur d’autres réseaux. Tous les serveurs n’offrent pas cette possibilité. Assurez-vous de renseigner les informations d’un compte déjà inscrit - phpBB utilisera les informations indiquées telles quelles.',

	'JAB_ENABLE'				=> 'Activer Jabber',
	'JAB_ENABLE_EXPLAIN'		=> 'Active l’utilisation de Jabber pour l’envoi de messages et de notifications.',
	'JAB_GTALK_NOTE'			=> 'Notez que GTalk ne marchera pas car la fonction <samp>dns_get_record</samp> est introuvable. Cette fonction n’est pas disponible dans PHP4 et elle n’est pas implémentée sur les environnements Windows. Cela ne fonctionne pas non plus sur les système basés sous BSD, y compris Mac OS.',
	'JAB_PACKAGE_SIZE'			=> 'Taille des paquets Jabber',
	'JAB_PACKAGE_SIZE_EXPLAIN'	=> 'Nombre de messages envoyés dans un paquet. Si mis à “0”, le message est envoyé immédiatement et ne sera pas placé en file d’attente.',
	'JAB_PASSWORD'				=> 'Mot de passe Jabber',
	'JAB_PASSWORD_EXPLAIN'		=> '<em><strong>Attention:</strong> ce mot de passe sera stocké en clair dans la base de données, visible de toute personne ayant accès à votre base de données ou à cette page de configuration.</em>',
	'JAB_PORT'					=> 'Port Jabber',
	'JAB_PORT_EXPLAIN'			=> 'Laissez cette case vide à moins que vous sachiez qu’il ne s’agisse pas du port 5222.',
	'JAB_SERVER'				=> 'Serveur Jabber',
	'JAB_SERVER_EXPLAIN'		=> 'Consultez %sjabber.org%s pour la liste des serveurs.',
	'JAB_SETTINGS_CHANGED'		=> 'Les paramètres Jabber ont été modifiés.',
	'JAB_USE_SSL'				=> 'Utiliser SSL pour se connecter',
	'JAB_USE_SSL_EXPLAIN'		=> 'Si activé, une connexion sécurisée tentera d’être établie. Le port de Jabber sera modifié en 5223, si le port 5222 est utilisé.',
	'JAB_USERNAME'				=> 'Nom d’utilisateur Jabber ou JID',
	'JAB_USERNAME_EXPLAIN'		=> 'Indiquez un nom d’utilisateur inscrit ou un JID valide. La validité du nom d’utilisateur ne sera pas vérifiée. Si vous ne spécifiez qu’un nom d’utilisateur, votre JID sera calculé à partir de ce nom et de celui du serveur spécifié ci-dessus. Sinon, spécifiez un JID valide, par exemple utilisateur@jabber.org.',
));

?>