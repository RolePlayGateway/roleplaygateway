<?php
/** 
*
* acp_attachments [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: attachments.php, v1.25 2010/02/09 19:04:00 Elglobo Exp $
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
	'ACP_ATTACHMENT_SETTINGS_EXPLAIN'	=> 'Vous pouvez configurer les paramètres principaux pour les fichiers joints et les catégories spéciales associées.',
	'ACP_EXTENSION_GROUPS_EXPLAIN'		=> 'Vous pouvez ajouter, supprimer, modifier ou désactiver vos groupes d’extensions. D’autres options incluent l’attribution d’une catégorie spéciale, la modification du mécanisme de téléchargement et la définition d’une icône de chargement qui sera affichée devant le fichier joint qui appartient au groupe.',
	'ACP_MANAGE_EXTENSIONS_EXPLAIN'		=> 'Vous pouvez gérer les extensions autorisées. Pour activer vos extensions, référez-vous au panneau de gestion des groupes d’extensions. Nous recommandons vivement de ne pas autoriser les extensions de scripts tel que <code>php</code>, <code>php3</code>, <code>php4</code>, <code>phtml</code>, <code>pl</code>, <code>cgi</code>, <code>py</code>, <code>rb</code>, <code>asp</code>, <code>aspx</code>, etc.',
	'ACP_ORPHAN_ATTACHMENTS_EXPLAIN'	=> 'Vous pouvez voir les fichiers orphelins. Cela se produit la plupart du temps quand les utilisateurs insèrent des fichiers mais n’envoient pas le message. Vous pouvez supprimer les fichiers ou les insérer à des messages existants. L’insertion aux messages requiert une ID de message valide, vous avez à déterminer cette ID de vous-même. Cela assignera le fichier joint déjà chargé au message portant l’ID que vous entrez.',
	'ADD_EXTENSION'						=> 'Ajouter une extension',
	'ADD_EXTENSION_GROUP'				=> 'Ajouter un groupe d’extensions',
	'ADMIN_UPLOAD_ERROR'				=> 'Erreur lors de l’envoi du fichier: “%s”.',
	'ALLOWED_FORUMS'					=> 'Forums autorisés',
	'ALLOWED_FORUMS_EXPLAIN'			=> 'Autorise à utiliser le groupe d’extensions sur les forums sélectionnés. (ou tous si sélectionné)',
	'ALLOWED_IN_PM_POST'				=> 'Autorisé',
	'ALLOW_ATTACHMENTS'					=> 'Autoriser les fichiers joints',
	'ALLOW_ALL_FORUMS'					=> 'Autoriser dans tous les forums',
	'ALLOW_IN_PM'						=> 'Autoriser dans la messagerie privée',
	'ALLOW_PM_ATTACHMENTS'				=> 'Autoriser les fichiers joints dans les messages privés',
	'ALLOW_SELECTED_FORUMS'				=> 'Seulement dans les forums sélectionnés ci-dessous',
	'ASSIGNED_EXTENSIONS'				=> 'Extensions assignées',
	'ASSIGNED_GROUP'					=> 'Groupe d’extensions assigné',
	'ATTACH_EXTENSIONS_URL'				=> 'Extensions',
	'ATTACH_EXT_GROUPS_URL'				=> 'Groupes d’extensions',
	'ATTACH_ID'							=> 'ID',
	'ATTACH_MAX_FILESIZE'				=> 'Taille maximale du fichier',
	'ATTACH_MAX_FILESIZE_EXPLAIN'		=> 'Taille maximale de chaque fichier, mettre “0” pour illimité.',
	'ATTACH_MAX_PM_FILESIZE'			=> 'Taille maximale des fichiers dans la messagerie privée',
	'ATTACH_MAX_PM_FILESIZE_EXPLAIN' 	=> 'Taille maximale de chaque fichier joint à un message privé, mettre “0” pour illimité.',
	'ATTACH_ORPHAN_URL'					=> 'Fichiers orphelins',
	'ATTACH_POST_ID'					=> 'ID du message',
	'ATTACH_QUOTA'						=> 'Quota total de fichiers joints',
	'ATTACH_QUOTA_EXPLAIN'				=> 'Espace disque maximum disponible pour les fichiers joints de tout le forum, mettre “0” pour illimité.',
	'ATTACH_TO_POST'					=> 'Joindre le fichier au message',

	'CAT_FLASH_FILES'			=> 'Fichiers Flash',
	'CAT_IMAGES'				=> 'Images',
	'CAT_QUICKTIME_FILES'		=> 'Fichiers Quicktime',
	'CAT_RM_FILES'				=> 'Fichiers RealMedia',
	'CAT_WM_FILES'				=> 'Fichier Windows Media',
	'CHECK_CONTENT' 			=> 'Vérifier les fichiers joints',
	'CHECK_CONTENT_EXPLAIN' 	=> 'Certains navigateurs peuvent se tromper en attribuant un type MIME incorrect aux fichiers chargés. Cette option permet de rejeter les fichiers qui risquent d’entraîner ce problème.',
	'CREATE_GROUP'				=> 'Créer un nouveau groupe',
	'CREATE_THUMBNAIL'			=> 'Créer une miniature',
	'CREATE_THUMBNAIL_EXPLAIN'	=> 'Créer une miniature dans tous les cas possibles.',

	'DEFINE_ALLOWED_IPS'			=> 'Définir les IPs/noms d’hôtes autorisés',
	'DEFINE_DISALLOWED_IPS'			=> 'Définir les IPs/noms d’hôtes interdits',
	'DOWNLOAD_ADD_IPS_EXPLAIN'		=> 'Pour indiquer plusieurs adresses IPs ou noms d’hôtes différents, entrez chacun d’eux sur une nouvelle ligne. Pour indiquer une plage d’adresses IPs, séparez le début et la fin par un tiret, et utilisez * comme caractère joker.',
	'DOWNLOAD_REMOVE_IPS_EXPLAIN'	=> 'Vous pouvez supprimer (ou ne plus exclure) plusieurs adresses IPs d’un coup en utilisant la combinaison de touches appropriée avec votre clavier et votre souris. Les adresses IPs exclues ont un fond bleu.',
	'DISPLAY_INLINED'				=> 'Afficher les images',
	'DISPLAY_INLINED_EXPLAIN'		=> 'Si “Non”, les images jointes seront affichées en tant que liens.',
	'DISPLAY_ORDER'					=> 'Ordre d’affichage des fichiers joints',
	'DISPLAY_ORDER_EXPLAIN'			=> 'Classer les fichiers joints par date.',
	
	'EDIT_EXTENSION_GROUP'			=> 'Modifier le groupe d’extensions',
	'EXCLUDE_ENTERED_IP'			=> 'Activez ceci pour exclure l’IP/nom d’hôte entré.',
	'EXCLUDE_FROM_ALLOWED_IP'		=> 'Exclure une IP des IP/noms d’hôtes autorisés',
	'EXCLUDE_FROM_DISALLOWED_IP'	=> 'Exclure une IP des IP/noms d’hôtes interdits',
	'EXTENSIONS_UPDATED'			=> 'Les extensions ont été mises à jour.',
	'EXTENSION_EXIST'				=> 'L’extension %s existe déjà.',
	'EXTENSION_GROUP'				=> 'Groupe d’extensions',
	'EXTENSION_GROUPS'				=> 'Groupes d’extensions',
	'EXTENSION_GROUP_DELETED'		=> 'Le groupe d’extension a été supprimé.',
	'EXTENSION_GROUP_EXIST'			=> 'Le groupe d’extension %s existe déjà.',

	'GO_TO_EXTENSIONS'		=> 'Aller à l’écran de gestion des extensions',
	'GROUP_NAME'			=> 'Nom du groupe',

	'IMAGE_LINK_SIZE'			=> 'Dimensions du lien de l’image',
	'IMAGE_LINK_SIZE_EXPLAIN'	=> 'Afficher le fichier image joint sous forme de lien texte, si l’image est plus grande que les dimensions saisies. Pour désactiver ce comportement, réglez les valeurs sur 0px par 0px.',
	'IMAGICK_PATH'				=> 'Chemin vers Imagemagick',
	'IMAGICK_PATH_EXPLAIN'		=> 'Chemin complet vers l’application imagemagick, Par exemple: <samp>/usr/bin/</samp>.',

	'MAX_ATTACHMENTS'				=> 'Nombre maximum de fichiers joints par message',
	'MAX_ATTACHMENTS_PM'			=> 'Nombre maximum de fichiers joints par message privé',
	'MAX_EXTGROUP_FILESIZE'			=> 'Taille maximale du fichier',
	'MAX_IMAGE_SIZE'				=> 'Dimensions maximales de l’image',
	'MAX_IMAGE_SIZE_EXPLAIN'		=> 'Taille maximale des images jointes. Réglez les deux valeurs sur 0px par 0px pour désactiver le contrôle des dimensions.',
	'MAX_THUMB_WIDTH'				=> 'Largeur maximale de la miniature générée',
	'MAX_THUMB_WIDTH_EXPLAIN'		=> 'La miniature générée n’excédera pas la largeur indiquée.',
	'MIN_THUMB_FILESIZE'			=> 'Taille minimale de la miniature',
	'MIN_THUMB_FILESIZE_EXPLAIN'	=> 'Ne pas créer de miniature pour les images ayant un poids inférieur à',
	'MODE_INLINE'					=> 'Intégré',
	'MODE_PHYSICAL'					=> 'Physique',

	'NOT_ALLOWED_IN_PM'			=> 'Non autorisé dans les messages privés',
	'NOT_ALLOWED_IN_PM_POST'	=> 'Non autorisé',
	'NOT_ASSIGNED'				=> 'Non assigné',
	'NO_EXT_GROUP'				=> 'Aucun',
	'NO_EXT_GROUP_NAME'			=> 'Vous n’avez indiqué aucun nom de groupe',
	'NO_EXT_GROUP_SPECIFIED'	=> 'Vous n’avez indiqué aucun groupe d’extension.',
	'NO_FILE_CAT'				=> 'Aucun',
	'NO_IMAGE'					=> 'Aucune image',
	'NO_THUMBNAIL_SUPPORT'		=> 'Le support des miniatures est désactivé. Pour que cela fonctionne correctement, la librairie GD doit être disponible ou Imagemagick doit être installé. Aucun des deux n’a été trouvé.',
	'NO_UPLOAD_DIR'				=> 'Le répertoire d’envoi indiqué n’existe pas.',
	'NO_WRITE_UPLOAD'			=> 'Vous ne possédez pas les droits en écriture sur le répertoire d’envoi indiqué. Modifiez les droits en écriture (CHMOD) de ce répertoire.',

	'ONLY_ALLOWED_IN_PM'	=> 'Seulement autorisé dans les messages privés',
	'ORDER_ALLOW_DENY'		=> 'Autorisé',
	'ORDER_DENY_ALLOW'		=> 'Refusé',

	'REMOVE_ALLOWED_IPS'		=> 'Supprimer ou ne plus exclure les IP/noms d’hôtes <em>autorisés</em>',
	'REMOVE_DISALLOWED_IPS'		=> 'Supprimer ou ne plus exclure les IP/noms d’hôtes <em>interdits</em>',

	'SEARCH_IMAGICK'				=> 'Rechercher l’application Imagemagick',
	'SECURE_ALLOW_DENY'				=> 'Liste des autorisations/refus',
	'SECURE_ALLOW_DENY_EXPLAIN'		=> 'Lorsque les téléchargements sécurisés sont activés, modifiez le comportement par défaut de la liste d’autorisations/refus à celle d’une <strong>liste blanche</strong> (Autorisé) ou une <strong>liste noire</strong> (Refusé).',
	'SECURE_DOWNLOADS'				=> 'Activer les téléchargements sécurisés',
	'SECURE_DOWNLOADS_EXPLAIN'		=> 'Si cette option est activée, les téléchargements sont limités aux IPs/noms d’hôtes définis.',
	'SECURE_DOWNLOAD_NOTICE'		=> 'Les téléchargements sécurisés ne sont pas activés. Les paramètres ci-dessus seront appliqués une fois les téléchargements sécurisés activés.',
	'SECURE_DOWNLOAD_UPDATE_SUCCESS'=> 'La liste des IPs a été mise à jour.',
	'SECURE_EMPTY_REFERRER'			=> 'Autoriser un référent vide',
	'SECURE_EMPTY_REFERRER_EXPLAIN'	=> 'Les téléchargements sécurisés sont basés sur les référents. Voulez-vous autoriser les téléchargements pour ceux qui omettent le référant?',
	'SETTINGS_CAT_IMAGES'			=> 'Paramètres des catégories d’image',
	'SPECIAL_CATEGORY'				=> 'Catégorie spéciale',
	'SPECIAL_CATEGORY_EXPLAIN'		=> 'Les catégories spéciales proposent un affichage particulier.',
	'SUCCESSFULLY_UPLOADED'			=> 'Le chargement est terminé.',
	'SUCCESS_EXTENSION_GROUP_ADD'	=> 'Le groupe d’extension a été créé.',
	'SUCCESS_EXTENSION_GROUP_EDIT'	=> 'Le groupe d’extension a été mis à jour.',

	'UPLOADING_FILES'				=> 'Chargement de fichiers',
	'UPLOADING_FILE_TO'				=> 'Le fichier “%1$s” a été chargé au message numéro %2$d.',
	'UPLOAD_DENIED_FORUM'			=> 'Vous n’avez pas la permission de transférer des fichiers sur le forum “%s”.',
	'UPLOAD_DIR'					=> 'Répertoire d’envoi',
	'UPLOAD_DIR_EXPLAIN'			=> 'Chemin de stockage pour les fichiers joints. Notez que si vous modifiez ce répertoire tout en ayant déjà transféré des fichiers joints, vous devrez copier manuellement les fichiers au nouvel emplacement.',
	'UPLOAD_ICON'					=> 'Icône d’envoi',
	'UPLOAD_NOT_DIR'				=> 'L’emplacement d’envoi que vous avez indiqué ne semble pas être un répertoire.',
));

?>