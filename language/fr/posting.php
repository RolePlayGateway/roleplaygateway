<?php
/** 
*
* posting [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: posting.php, v1.26 2010/02/09 19:46:00 Elglobo Exp $
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

$lang = array_merge($lang, array(
	'ADD_ATTACHMENT'			=> 'Ajouter des fichiers joints',
	'ADD_ATTACHMENT_EXPLAIN'	=> 'Si vous souhaitez joindre un ou plusieurs fichiers, complétez les indications suivantes.',
	'ADD_FILE'					=> 'Ajouter le fichier',
	'ADD_POLL'					=> 'Ajouter un sondage',
	'ADD_POLL_EXPLAIN'			=> 'Si vous ne souhaitez pas ajouter de sondage à votre sujet, laissez ces champs vides.',
	'ALREADY_DELETED'			=> 'Désolé, ce message a déjà été supprimé.',
	'ATTACH_QUOTA_REACHED'		=> 'Désolé, le quota de fichiers joints a été atteint.',
	'ATTACH_SIG'				=> 'Attacher ma signature (les signatures peuvent être modifiées dans le panneau de l’utilisateur)',

	'BBCODE_A_HELP'				=> 'Fichier joint chargé en ligne: [attachment=]nom_du_fichier.ext[/attachment]',
	'BBCODE_B_HELP'				=> 'Texte gras: [b]texte[/b]',
	'BBCODE_C_HELP'				=> 'Code: [code]code[/code]',
	'BBCODE_E_HELP'				=> 'Liste: Ajouter une liste d’éléments',
	'BBCODE_F_HELP'				=> 'Taille de la police: [size=85]petit texte[/size]',
	'BBCODE_IS_OFF'				=> 'Les %sBBCodes%s sont <em>désactivés</em>',
	'BBCODE_IS_ON'				=> 'Les %sBBCodes%s sont <em>activés</em>',
	'BBCODE_I_HELP'				=> 'Texte italique: [i]texte[/i]',
	'BBCODE_L_HELP'				=> 'Liste: [list]texte[/list]',
	'BBCODE_LISTITEM_HELP'		=> 'Elément de liste: [*]texte[/*]',
	'BBCODE_O_HELP'				=> 'Liste ordonnée: [list=]texte[/list]',
	'BBCODE_P_HELP'				=> 'Insérer une image: [img]http://image_url[/img]',
	'BBCODE_Q_HELP'				=> 'Citation: [quote]texte[/quote]',
	'BBCODE_S_HELP'				=> 'Couleur de la police: [color=red]texte[/color] Astuce : vous pouvez également utiliser color=#FF0000',
	'BBCODE_U_HELP'				=> 'Texte souligné: [u]texte[/u]',
	'BBCODE_W_HELP'				=> 'Insérer un lien: [url]http://url[/url] ou [url=http://url]texte descriptif[/url]',
	'BBCODE_D_HELP'				=> 'Flash: [flash=largeur,hauteur]http://flash_url[/flash]',
	'BUMP_ERROR'				=> 'Vous ne pouvez pas faire remonter ce sujet aussitôt après l’ajout du dernier message.',

	'CANNOT_DELETE_REPLIED'		=> 'Désolé, vous ne pouvez supprimer que les messages n’ayant reçu aucune réponse.',
	'CANNOT_EDIT_POST_LOCKED'	=> 'Ce message a été verrouillé. Vous ne pouvez plus l’éditer.',
	'CANNOT_EDIT_TIME'			=> 'Vous ne pouvez plus éditer ou supprimer ce message.',
	'CANNOT_POST_ANNOUNCE'		=> 'Désolé, vous ne pouvez pas poster d’annonces.',
	'CANNOT_POST_STICKY'		=> 'Désolé, vous ne pouvez pas créer de nouveaux post-it.',
	'CHANGE_TOPIC_TO'			=> 'Changer le statut du sujet en',
	'CLOSE_TAGS'				=> 'Fermer les balises',
	'CURRENT_TOPIC'				=> 'Sujet actuel',

	'DELETE_FILE'				=> 'Supprimer le fichier',
	'DELETE_MESSAGE'			=> 'Supprimer le message',
	'DELETE_MESSAGE_CONFIRM'	=> 'Êtes-vous sûr de vouloir supprimer ce message?',
	'DELETE_OWN_POSTS'			=> 'Désolé, vous ne pouvez supprimer que vos propres messages.',
	'DELETE_POST_CONFIRM'		=> 'Êtes-vous sûr de vouloir supprimer ce message?',
	'DISALLOWED_CONTENT' 		=> 'Le chargement a été rejeté car le fichier envoyé a été identifié comme un éventuel vecteur d’attaque.',
	'DELETE_POST_WARN'			=> 'Une fois supprimé, le message ne pourra pas être récupéré',
	'DISABLE_BBCODE'			=> 'Désactiver les BBCodes',
	'DISABLE_MAGIC_URL'			=> 'Désactiver les liens',
	'DISABLE_SMILIES'			=> 'Désactiver les smileys',
	'DISALLOWED_EXTENSION'		=> 'L’extension %s n’est pas autorisée.',
	'DRAFT_LOADED'				=> 'Brouillon chargé dans la zone de rédaction de message, vous pouvez finir votre message maintenant.<br />Le brouillon sera supprimé dès que vous aurez posté votre message.',
	'DRAFT_LOADED_PM'			=> 'Brouillon chargé dans la zone de rédaction de message privé, vous pouvez finir votre message maintenant.<br />Le brouillon sera supprimé dès que vous aurez envoyé votre message privé.',
	'DRAFT_SAVED'				=> 'Le brouillon a été sauvegardé.',
	'DRAFT_TITLE'				=> 'Titre du brouillon',

	'EDIT_REASON'				=> 'Raison de l’édition du message',
	'EMPTY_FILEUPLOAD'			=> 'Le fichier chargé est vide ou n’existe pas.',
	'EMPTY_MESSAGE'				=> 'Votre message est vide!',
	'EMPTY_REMOTE_DATA'			=> 'Le fichier n’a pas pu être chargé, essayez de le charger manuellement.',

	'FLASH_IS_OFF'				=> '[flash] est <em>désactivé</em>',
	'FLASH_IS_ON'				=> '[flash] est <em>activé</em>',
	'FLOOD_ERROR'				=> 'Vous ne pouvez pas poster un nouveau message, si tôt après le dernier.',
	'FONT_COLOR'				=> 'Couleur de la police',
	'FONT_COLOR_HIDE'			=> 'Masquer les couleurs de la police',
	'FONT_HUGE'					=> 'Très grande',
	'FONT_LARGE'				=> 'Grande',
	'FONT_NORMAL'				=> 'Normale',
	'FONT_SIZE'					=> 'Taille de la police',
	'FONT_SMALL'				=> 'Petite',
	'FONT_TINY'					=> 'Très petite',

	'GENERAL_UPLOAD_ERROR'		=> 'Impossible de charger le fichier joint de %s.',

	'IMAGES_ARE_OFF'			=> '[img] est <em>désactivé</em>',
	'IMAGES_ARE_ON'				=> '[img] est <em>activé</em>',
	'INVALID_FILENAME'			=> '%s est un nom de fichier invalide.',

	'LOAD'						=> 'Charger',
	'LOAD_DRAFT'				=> 'Charger un brouillon',
	'LOAD_DRAFT_EXPLAIN'		=> 'Vous pouvez charger le brouillon que vous souhaitez finir. Votre message actuel sera annulé, tout le contenu de votre message actuel sera supprimé. Vous pouvez voir, éditer et supprimer vos brouillons dans le panneau de l’utilisateur.',
	'LOGIN_EXPLAIN_BUMP'		=> 'Vous devez être connecté pour remonter un sujet de ce forum.',
	'LOGIN_EXPLAIN_DELETE'		=> 'Vous devez être connecté pour supprimer des messages dans ce forum.',
	'LOGIN_EXPLAIN_POST'		=> 'Vous devez être connecté pour poster dans ce forum.',
	'LOGIN_EXPLAIN_QUOTE'		=> 'Vous devez être connecté pour citer des messages dans ce forum.',
	'LOGIN_EXPLAIN_REPLY'		=> 'Vous devez être connecté pour répondre aux sujets de ce forum.',

	'MAX_FONT_SIZE_EXCEEDED'	=> 'Vous pouvez seulement employer des polices dont la taille maximum est de %1$d.',
	'MAX_FLASH_HEIGHT_EXCEEDED'	=> 'Vos animations flash doivent être de %1$d pixels de haut maximum.',
	'MAX_FLASH_WIDTH_EXCEEDED'	=> 'Vos animations flash doivent être de %1$d pixels de large maximum.',
	'MAX_IMG_HEIGHT_EXCEEDED'	=> 'Vos images doivent être de %1$d pixels de haut maximum.',
	'MAX_IMG_WIDTH_EXCEEDED'	=> 'Vos images doivent être de %1$d pixels de large maximum.',

	'MESSAGE_BODY_EXPLAIN'		=> 'Entrez votre message ici, il ne doit pas contenir plus de <strong>%d</strong> caractères.',
	'MESSAGE_DELETED'			=> 'Votre message a été supprimé.',
	'MORE_SMILIES'				=> 'Voir plus de smileys',

	'NOTIFY_REPLY'				=> 'M’avertir lorsqu’une réponse est postée.',
	'NOT_UPLOADED'				=> 'Le fichier ne peut pas être chargé.',
	'NO_DELETE_POLL_OPTIONS'	=> 'Vous ne pouvez pas supprimer les options du sondage existantes.',
	'NO_PM_ICON'				=> 'Aucune',
	'NO_POLL_TITLE'				=> 'Vous devez entrer un titre de sondage.',
	'NO_POST'					=> 'Le message demandé n’existe pas.',
	'NO_POST_MODE'				=> 'Aucun type de message n’est indiqué.',

	'PARTIAL_UPLOAD'			=> 'Le fichier n’a été que partiellement chargé.',
	'PHP_SIZE_NA'				=> 'La taille du fichier joint est trop grande.<br />Impossible de déterminer la taille maximale définie par PHP dans php.ini.',
	'PHP_SIZE_OVERRUN'			=> 'La taille du fichier joint est trop grande, la taille maximale de chargement est de %1$d %2$s.<br />Notez que ce paramètre se trouve dans php.ini et ne peut pas être outrepassé.',
	'PLACE_INLINE'				=> 'Insérer dans le message',
	'POLL_DELETE'				=> 'Supprimer le sondage',
	'POLL_FOR'					=> 'Durée du sondage',
	'POLL_FOR_EXPLAIN'			=> 'Mettre “0” ou laissez vide pour ne jamais terminer le sondage.',
	'POLL_MAX_OPTIONS'			=> 'Option(s) par utilisateur',
	'POLL_MAX_OPTIONS_EXPLAIN'	=> 'Ceci est le nombre d’options que chaque utilisateur peut choisir quand il vote.',
	'POLL_OPTIONS'				=> 'Options du sondage',
	'POLL_OPTIONS_EXPLAIN'		=> 'Placez chaque option sur une ligne différente. Vous pouvez entrer jusqu’à <strong>%d</strong> options.',
	'POLL_OPTIONS_EDIT_EXPLAIN'	=> 'Placez chaque option sur une ligne différente. Vous pouvez entrer jusqu’à <strong>%d</strong> options. Si vous supprimez ou ajoutez des options, tous les votes précédents seront remis à zéro.',
	'POLL_QUESTION'				=> 'Question du sondage',
	'POLL_TITLE_TOO_LONG'		=> 'Le titre du sondage doit contenir moins de 100 caractères.',
	'POLL_TITLE_COMP_TOO_LONG'	=> 'La taille du titre du sondage est trop importante, essayez de retirer les BBCodes et/ou les smileys.',
	'POLL_VOTE_CHANGE'			=> 'Permettre de voter à nouveau',
	'POLL_VOTE_CHANGE_EXPLAIN'	=> 'Si activé, les utilisateurs peuvent changer leur vote.',
	'POSTED_ATTACHMENTS'		=> 'Fichiers joints postés',
	'POST_APPROVAL_NOTIFY'		=> 'Vous serez averti lorsque votre message sera approuvé.',
	'POST_CONFIRMATION'			=> 'Confirmation du message',
	'POST_CONFIRM_EXPLAIN'		=> 'Afin de lutter contre le spam de messages instantanés, l’administrateur souhaite que vous entriez un code de confirmation. Le code apparaît dans l’image que vous devriez voir ci-dessous. Si vous êtes déficient visuel ou si vous ne pouvez pas lire ce code, contactez %sl’administrateur du forum%s.',
	'POST_DELETED'				=> 'Le message a été supprimé.',
	'POST_EDITED'				=> 'Votre message a été édité.',
	'POST_EDITED_MOD'			=> 'Votre message a été édité, mais requiert l’approbation d’un modérateur avant d’être rendu visible publiquement.',
	'POST_GLOBAL'				=> 'Annonce globale',
	'POST_ICON'					=> 'Icône de message',
	'POST_NORMAL'				=> 'Normal',
	'POST_REVIEW'				=> 'Revue du sujet',
	'POST_REVIEW_EDIT'			=> 'Revue du sujet',
	'POST_REVIEW_EDIT_EXPLAIN'	=> 'Ce message a été modifié par un autre utilisateur pendant que vous étiez entrain de l’éditer. Vous pouvez revoir la version actuelle de ce message et ajuster votre édition.',
	'POST_REVIEW_EXPLAIN'		=> 'Au moins un nouveau message a été ajouté à ce sujet entre-temps. Vous pouvez revoir votre message en conséquence.',
	'POST_STORED'				=> 'Votre message a été posté.',
	'POST_STORED_MOD'			=> 'Votre message a été posté, mais requiert l’approbation d’un modérateur avant d’être rendu visible publiquement.',
	'POST_TOPIC_AS'				=> 'Poster le sujet en tant que',
	'PROGRESS_BAR'				=> 'Barre de progression',

	'QUOTE_DEPTH_EXCEEDED'		=> 'Vous pouvez utiliser seulement %1$d citation(s).',

	'SAVE'						=> 'Sauvegarder',
	'SAVE_DATE'					=> 'Sauvegardé le',
	'SAVE_DRAFT'				=> 'Sauvegarder le brouillon',
	'SAVE_DRAFT_CONFIRM'		=> 'Notez que les brouillons sauvegardés ne contiennent que le titre et le message, tout autre élément sera supprimé. Souhaitez-vous sauvegarder votre brouillon maintenant?',
	'SMILIES'					=> 'Smileys',
	'SMILIES_ARE_OFF'			=> 'Les smileys sont <em>désactivés</em>',
	'SMILIES_ARE_ON'			=> 'Les smileys sont <em>activés</em>',
	'STICKY_ANNOUNCE_TIME_LIMIT'=> 'Durée du post-it ou de l’annonce',
	'STICK_TOPIC_FOR'			=> 'Epingler pendant',
	'STICK_TOPIC_FOR_EXPLAIN'	=> 'Mettre “0” ou laissez cette case vide pour une durée illimitée des post-it/annonces. Notez que ce nombre est relatif à la date du message.',
	'STYLES_TIP'				=> 'Astuce: les mises en forme peuvent être appliquées rapidement en sélectionnant le texte.',

	'TOO_FEW_CHARS'				=> 'Votre message contient trop peu de caractères.',
	'TOO_FEW_CHARS_LIMIT'		=> 'Votre message contient %1$d caractères. Le nombre minimum de caractères à taper est de %2$d.',
	'TOO_FEW_POLL_OPTIONS'		=> 'Vous devez entrer au moins deux options possibles au sondage.',
	'TOO_MANY_ATTACHMENTS'		=> 'Impossible d’ajouter un nouveau fichier joint, %d est le maximum autorisé.',
	'TOO_MANY_CHARS'			=> 'Votre message contient trop de caractères.',
	'TOO_MANY_CHARS_POST'		=> 'Votre message contient %1$d caractères. Le nombre maximal de caractères autorisé est %2$d.',
	'TOO_MANY_CHARS_SIG'		=> 'Votre signature contient %1$d caractères. Le nombre maximal de caractères autorisé est %2$d.',
	'TOO_MANY_POLL_OPTIONS'		=> 'Vous avez dépassé le nombre d’options de sondage possible.',
	'TOO_MANY_SMILIES'			=> 'Votre message contient trop de smileys. Un maximum de %d smiley(s) est autorisé.',
	'TOO_MANY_URLS'				=> 'Votre message contient trop de liens. Un maximum de %d lien(s) est autorisé.',
	'TOO_MANY_USER_OPTIONS'		=> 'Vous ne pouvez pas indiquer un nombre d’options par utilisateur supérieur au nombre d’options du sondage.',
	'TOPIC_BUMPED'				=> 'Le sujet a été remonté.',

	'UNAUTHORISED_BBCODE'		=> 'Vous ne pouvez pas utiliser certains BBCodes: %s.',
	'UNGLOBALISE_EXPLAIN'		=> 'Pour changer cette annonce globale en sujet normal, vous devez sélectionner le forum où vous souhaitez voir apparaître le sujet.',
	'UPDATE_COMMENT'			=> 'Mettre à jour le commentaire',
	'URL_INVALID'				=> 'Le lien indiqué est invalide.',
	'URL_NOT_FOUND'				=> 'Le fichier indiqué n’a pas été trouvé.',
	'URL_IS_OFF'				=> '[url] est <em>désactivé</em>',
	'URL_IS_ON'					=> '[url] est <em>activé</em>',
	'USER_CANNOT_BUMP'			=> 'Vous ne pouvez pas remonter de sujets dans ce forum.',
	'USER_CANNOT_DELETE'		=> 'Vous ne pouvez pas supprimer de messages dans ce forum.',
	'USER_CANNOT_EDIT'			=> 'Vous ne pouvez pas éditer de messages dans ce forum.',
	'USER_CANNOT_REPLY'			=> 'Vous ne pouvez pas répondre à un sujet dans ce forum.',
	'USER_CANNOT_FORUM_POST'	=> 'Vous ne pouvez pas effectuer d’opérations sur ce forum car ce type de forum ne le permet pas.',

	'VIEW_MESSAGE'				=> '%sVoir le message envoyé%s',
	'VIEW_PRIVATE_MESSAGE'		=> '%sVoir le message privé envoyé%s',

	'WRONG_FILESIZE'			=> 'Le fichier est trop gros, la taille maximum autorisée est %1d %2s.',
	'WRONG_SIZE'				=> 'L’image doit faire au moins %1$d pixels de large, %2$d pixels de haut et au plus %3$d pixels de large et %4$d pixels de haut. L’image actuelle fait %5$d pixels de large et %6$d pixels de haut.',
));

?>