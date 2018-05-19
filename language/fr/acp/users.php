<?php
/**
*
* acp_users [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: users.php, v1.25 2009/10/15 12:35:00 Elglobo Exp $
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
	'ADMIN_SIG_PREVIEW'		=> 'Aperçu de la signature',
	'AT_LEAST_ONE_FOUNDER'	=> 'Vous n’êtes pas autorisé à passer ce fondateur en utilisateur normal. Il est nécessaire d’avoir au moins un fondateur sur le forum. Si vous voulez modifier le statut de cet utilisateur, vous devez tout d’abord promouvoir un autre utilisateur en tant que fondateur.',

	'BAN_ALREADY_ENTERED'	=> 'Ce bannissement a déjà été effectué. Aucune mise à jour n’a été effectuée.',
	'BAN_SUCCESSFUL'		=> 'Le bannissement a été ajouté.',

	'CANNOT_BAN_FOUNDER'			=> 'Vous n’êtes pas autorisé à bannir les comptes des administrateurs fondateurs.',
	'CANNOT_BAN_YOURSELF'			=> 'Vous n’êtes pas autorisé à vous bannir.',
	'CANNOT_DEACTIVATE_BOT'			=> 'Vous n’êtes pas autorisé à désactiver les comptes de robots. Désactivez plutôt le robot dans la page des robots.',
	'CANNOT_DEACTIVATE_FOUNDER'		=> 'Vous n’êtes pas autorisé à désactiver les comptes des administrateurs fondateurs.',
	'CANNOT_DEACTIVATE_YOURSELF'	=> 'Vous n’êtes pas autorisé à désactiver votre propre compte.',
	'CANNOT_FORCE_REACT_BOT'		=> 'Vous n’êtes pas autorisé à forcer la réactivation sur les comptes de robots. Réactivez plutôt le robot dans la page des robots.',
	'CANNOT_FORCE_REACT_FOUNDER'	=> 'Vous n’êtes pas autorisé à forcer la réactivation sur les comptes des administrateurs fondateurs.',
	'CANNOT_FORCE_REACT_YOURSELF'	=> 'Vous n’êtes pas autorisé à forcer la réactivation de votre propre compte.',
	'CANNOT_REMOVE_ANONYMOUS'		=> 'Vous n’êtes pas autorisé à supprimer le compte de l’utilisateur invité.',
	'CANNOT_REMOVE_YOURSELF'		=> 'Vous n’êtes pas autorisé à supprimer votre propre compte.',
	'CANNOT_SET_FOUNDER_IGNORED'	=> 'Vous ne pouvez pas promouvoir des utilisateurs ignorés en fondateurs.',
	'CANNOT_SET_FOUNDER_INACTIVE'	=> 'Vous devez activer les utilisateurs avant de les promouvoir au statut d’administrateurs fondateurs, seuls les utilisateurs activés peuvent être promus.',
	'CONFIRM_EMAIL_EXPLAIN'			=> 'Vous êtes seulement obligé de renseigner cette case si vous modifiez l’adresse e-mail de cet utilisateur.',

	'DELETE_POSTS'			=> 'Supprimer ses messages',
	'DELETE_USER'			=> 'Supprimer cet utilisateur',
	'DELETE_USER_EXPLAIN'	=> 'Merci de noter que la suppression d’un utilisateur est une action irréversible.',

	'FORCE_REACTIVATION_SUCCESS'	=> 'La réactivation a été forcée.',
	'FOUNDER'						=> 'Fondateur',
	'FOUNDER_EXPLAIN'				=> 'Les fondateurs ont toutes les permissions et ne peuvent jamais être bannis, supprimés ou modifiés par des utilisateurs non fondateurs.',

	'GROUP_APPROVE'					=> 'Accepter le membre',
	'GROUP_DEFAULT'					=> 'Groupe par défaut',
	'GROUP_DELETE'					=> 'Supprimer le membre du groupe',
	'GROUP_DEMOTE'					=> 'Rétrograder le chef de groupe',
	'GROUP_PROMOTE'					=> 'Promouvoir en chef de groupe',

	'IP_WHOIS_FOR'			=> 'IP whois pour %s',

	'LAST_ACTIVE'			=> 'Dernière visite le',

	'MOVE_POSTS_EXPLAIN'	=> 'Merci de sélectionner le forum où vous désirez déplacer tous les messages de cet utilisateur.',

	'NO_SPECIAL_RANK'		=> 'Aucun rang spécial sélectionné',
	'NO_WARNINGS'			=> 'Aucun avertissement.',
	'NOT_MANAGE_FOUNDER'	=> 'Vous avez essayé de gérer un utilisateur ayant le statut de fondateur. Seuls les fondateurs peuvent gérer d’autres fondateurs.',

	'QUICK_TOOLS'			=> 'Outils rapides',

	'REGISTERED'			=> 'Inscrit le',
	'REGISTERED_IP'			=> 'Adresse IP lors de son inscription',
	'RETAIN_POSTS'			=> 'Conserver ses messages',

	'SELECT_FORM'			=> 'Sélectionner un formulaire',
	'SELECT_USER'			=> 'Sélectionner un utilisateur',

	'USER_ADMIN'					=> 'Administration de l’utilisateur',
	'USER_ADMIN_ACTIVATE'			=> 'Activer son compte',
	'USER_ADMIN_ACTIVATED'			=> 'Le compte a été activé.',
	'USER_ADMIN_AVATAR_REMOVED'		=> 'L’avatar de cet utilisateur a été supprimé.',
	'USER_ADMIN_BAN_EMAIL'			=> 'Bannir par son e-mail',
	'USER_ADMIN_BAN_EMAIL_REASON'	=> 'L’e-mail a été banni via l’administration de l’utilisateur',
	'USER_ADMIN_BAN_IP'				=> 'Bannir par son adresse IP',
	'USER_ADMIN_BAN_IP_REASON'		=> 'L’adresse IP a été bannie via l’administration de l’utilisateur',
	'USER_ADMIN_BAN_NAME_REASON'	=> 'Le nom d’utilisateur a été banni via l’administration de l’utilisateur',
	'USER_ADMIN_BAN_USER'			=> 'Bannir par son nom d’utilisateur',
	'USER_ADMIN_DEACTIVATE'			=> 'Désactiver son compte',
	'USER_ADMIN_DEACTIVED'			=> 'Le compte a été désactivé.',
	'USER_ADMIN_DEL_ATTACH'			=> 'Supprimer ses fichiers joints',
	'USER_ADMIN_DEL_AVATAR'			=> 'Supprimer son avatar',
	'USER_ADMIN_DEL_OUTBOX'			=> 'Vider la boîte d’envoi',
	'USER_ADMIN_DEL_POSTS'			=> 'Supprimer ses messages',
	'USER_ADMIN_DEL_SIG'			=> 'Supprimer sa signature',
	'USER_ADMIN_EXPLAIN'			=> 'Vous pouvez modifier les informations d’un utilisateur et certaines options particulières.',
	'USER_ADMIN_FORCE'				=> 'Forcer la réactivation',
	'USER_ADMIN_LEAVE_NR'			=> 'Supprimer des nouveaux inscrits',
	'USER_ADMIN_MOVE_POSTS'			=> 'Déplacer ses messages',
	'USER_ADMIN_SIG_REMOVED'		=> 'La signature de cet utilisateur a été supprimée.',
	'USER_ATTACHMENTS_REMOVED'		=> 'Les fichiers joints de cet utilisateur ont été supprimés.',
	'USER_AVATAR_NOT_ALLOWED'		=> 'L’avatar ne peut pas être affiché car les avatars ont été désactivés.',
	'USER_AVATAR_UPDATED'			=> 'Les informations avatar de cet utilisateur ont été mis à jour.',
	'USER_AVATAR_TYPE_NOT_ALLOWED'	=> 'L’avatar actuel ne peut pas être affiché car ce type d’avatar a été désactivté.',
	'USER_CUSTOM_PROFILE_FIELDS'	=> 'Champs de profil personnalisés',
	'USER_DELETED'					=> 'Cet utilisateur a été supprimé.',
	'USER_GROUP_ADD'				=> 'Ajouter cet utilisateur au groupe',
	'USER_GROUP_NORMAL'				=> 'L’utilisateur est membre des groupes définis',
	'USER_GROUP_PENDING'			=> 'En attente d’acceptation dans les groupes',
	'USER_GROUP_SPECIAL'			=> 'L’utilisateur est membre des groupes prédéfinis',
	'USER_LIFTED_NR'				=> 'Le status de nouvel inscrit a été supprimé.',
	'USER_NO_ATTACHMENTS'			=> 'Aucun fichier joint à afficher.',
	'USER_OUTBOX_EMPTIED'			=> 'La boîte d’envoi de l’utilisateur a été vidé.',
	'USER_OUTBOX_EMPTY'				=> 'La boîte d’envoi de l’utilisateur était déjà vide.',
	'USER_OVERVIEW_UPDATED'			=> 'Les informations de cet utilisateur ont été mises à jour.',
	'USER_POSTS_DELETED'			=> 'Tous les messages de cet utilisateur ont été supprimés.',
	'USER_POSTS_MOVED'				=> 'Tous les messages de cet utilisateur ont été déplacés vers le forum cible.',
	'USER_PREFS_UPDATED'			=> 'Les préférences de cet utilisateur ont été mises à jour.',
	'USER_PROFILE'					=> 'Profil utilisateur',
	'USER_PROFILE_UPDATED'			=> 'Le profil de cet utilisateur a été mis à jour.',
	'USER_RANK'						=> 'Rang de l’utilisateur',
	'USER_RANK_UPDATED'				=> 'Le rang de cet utilisateur a été mis à jour.',
	'USER_SIG_UPDATED'				=> 'La signature de cet utilisateur a été mise à jour.',
	'USER_WARNING_LOG_DELETED'		=> 'Aucune information disponible. La liste d’entrées a probablement été supprimée.',
	'USER_TOOLS'					=> 'Outils de base',
));

?>