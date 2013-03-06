<?php
/** 
*
* groups [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: groups.php, v1.24 2008/07/03 12:58:20 Elglobo Exp $
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
   'ALREADY_DEFAULT_GROUP'   => 'Le groupe sélectionné est déjà votre groupe par défaut.',
   'ALREADY_IN_GROUP' => 'Vous êtes déjà membre du groupe sélectionné.',
   'ALREADY_IN_GROUP_PENDING'   => 'Vous avez déjà demandé à rejoindre le groupe choisi.',
   
   'CANNOT_JOIN_GROUP'		=> 'Vous n’êtes pas autorisé à rejoindre ce groupe. Vous ne pouvez rejoindre que les groupes ouverts.',
   'CANNOT_RESIGN_GROUP'	=> 'Vous n’êtes pas autorisé à vous retirer de ce groupe. Vous ne pouvez vous retirer que des groupes ouverts.',
   'CHANGED_DEFAULT_GROUP'	=> 'Le groupe par défaut a été modifié.',
   
   'GROUP_AVATAR' => 'Avatar du groupe',
   'GROUP_CHANGE_DEFAULT' => 'Êtes-vous sûr de vouloir modifier votre groupe par défaut pour “%s”?',
   'GROUP_CLOSED' => 'Fermé',
   'GROUP_DESC' => 'Description du groupe',
   'GROUP_HIDDEN' => 'Invisible',
   'GROUP_INFORMATION' => 'Information sur le groupe',
   'GROUP_IS_CLOSED' => 'C’est un groupe fermé, aucun nouveau membre ne peut être ajouté.',
   'GROUP_IS_FREE' => 'C’est un groupe libre et ouvert, chaque utilisateur peut en devenir membre.',
   'GROUP_IS_HIDDEN' => 'C’est un groupe caché, seuls les membres de ce groupe peuvent en voir les informations.',
   'GROUP_IS_OPEN' => 'C’est un groupe ouvert, mais l’utilisateur doit faire une demande pour en devenir membre.',
   'GROUP_IS_SPECIAL' => 'C’est un groupe spécial, les groupes spéciaux sont gérés par les administrateurs.',
   'GROUP_JOIN' => 'Rejoindre le groupe',
   'GROUP_JOIN_CONFIRM' => 'Êtes-vous sûr de vouloir rejoindre le groupe sélectionné?',
   'GROUP_JOIN_PENDING' => 'Demander à rejoindre un groupe',
   'GROUP_JOIN_PENDING_CONFIRM' => 'Êtes-vous sûr de vouloir demander à rejoindre le groupe sélectionné?',
   'GROUP_JOINED' => 'Vous êtes désormais membre de ce groupe.',
   'GROUP_JOINED_PENDING' => 'Votre demande d’adhésion a été prise en compte. Vous devez attendre que le modérateur du groupe approuve votre demande.',
   'GROUP_LIST' => 'Gérer les membres',
   'GROUP_MEMBERS' => 'Membres du groupe',
   'GROUP_NAME' => 'Nom du groupe',
   'GROUP_OPEN' => 'Ouvrir',
   'GROUP_RANK' => 'Rang du groupe',
   'GROUP_RESIGN_MEMBERSHIP' => 'Se désinscrire du groupe',
   'GROUP_RESIGN_MEMBERSHIP_CONFIRM' => 'Êtes-vous sûr de vouloir vous désinscrire du groupe sélectionné?',
   'GROUP_RESIGN_PENDING' => 'Annuler une demande d’adhésion',
   'GROUP_RESIGN_PENDING_CONFIRM' => 'Êtes-vous sûr de vouloir annuler votre demande d’adhésion au groupe sélectionné?',
   'GROUP_RESIGNED_MEMBERSHIP' => 'Vous avez été désinscrit du groupe sélectionné.',
   'GROUP_RESIGNED_PENDING' => 'Votre demande d’adhésion pour le groupe sélectionné a été annulée.',
   'GROUP_TYPE' => 'Type du groupe',
   'GROUP_UNDISCLOSED' => 'Groupe invisible',
   'FORUM_UNDISCLOSED'   => 'Modérateur des forums cachés',

   'LOGIN_EXPLAIN_GROUP' => 'Vous devez vous connecter pour voir les détails de ce groupe.',

   'NO_LEADERS' => 'Vous n’êtes pas modérateur d’un groupe.',
   'NOT_LEADER_OF_GROUP' => 'L’opération demandée ne peut aboutir car vous n’êtes pas modérateur du groupe sélectionné.',
   'NOT_MEMBER_OF_GROUP' => 'L’opération demandée ne peut aboutir car vous n’êtes pas membre du groupe sélectionné.',
   'NOT_RESIGN_FROM_DEFAULT_GROUP'   => 'Vous ne pouvez pas être retiré de votre groupe par défaut.',
   
   'PRIMARY_GROUP' => 'Groupe par défaut',

   'REMOVE_SELECTED' => 'Supprimer la sélection',

   'USER_GROUP_CHANGE' => 'De “%1$s” vers “%2$s”',
   'USER_GROUP_DEMOTE' => 'Rétrograder le chef de groupe.',
   'USER_GROUP_DEMOTE_CONFIRM'   => 'Êtes-vous sûr de vouloir rétrograder le chef de groupe sélectionné?',
   'USER_GROUP_DEMOTED' => 'Le chef de groupe a été rétrogradé en simple membre.',
));

?>