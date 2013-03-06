<?php
/** 
*
* memberlist [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: memberlist.php, v1.26 2009/12/16 16:36:00 Elglobo Exp $
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
   'ABOUT_USER'   => 'Profil',
   'ACTIVE_IN_FORUM'   => 'Forum le plus actif',
   'ACTIVE_IN_TOPIC'   => 'Sujet le plus actif',
   'ADD_FOE'   => 'Ajouter à ma liste d’ignorés',
   'ADD_FRIEND'   => 'Ajouter à ma liste d’amis',
   'AFTER'   => 'Après',
   'ALL'   => 'Tous',
   'BEFORE'   => 'Avant',
   'CC_EMAIL'   => 'S’envoyer une copie de cet e-mail.',
   'CONTACT_USER'   => 'Contacter',
   'DEST_LANG'   => 'Langue',
   'DEST_LANG_EXPLAIN'   => 'Choisissez une langue appropriée (si disponible) pour le destinataire de ce message.',
   'EMAIL_BODY_EXPLAIN'   => 'Ce message sera envoyé au format texte, ne pas inclure de code HTML ni de BBCode. L’adresse de réponse à ce message sera votre adresse e-mail.',
   'EMAIL_DISABLED'   => 'Désolé mais toutes les fonctions en rapport avec les e-mails ont été désactivées.',
   'EMAIL_SENT'   => 'L’e-mail a été envoyé.',
   'EMAIL_TOPIC_EXPLAIN'   => 'Ce message sera envoyé au format texte, ne pas inclure de code HTML ni de BBCode. Notez que les informations sur le sujet sont déjà incluses dans le message. L’adresse de réponse à ce message sera votre adresse e-mail.',
   'EMPTY_ADDRESS_EMAIL'   => 'Vous devez fournir une adresse e-mail valide pour le destinataire.',
   'EMPTY_MESSAGE_EMAIL'   => 'Vous devez écrire un message.',
   'EMPTY_MESSAGE_IM'		=> 'Vous devez entrer un message à envoyer.',
   'EMPTY_NAME_EMAIL'   => 'Vous devez entrer le nom réel du destinataire.',
   'EMPTY_SUBJECT_EMAIL'   => 'Vous devez indiquer un sujet pour l’e-mail.',
   'EQUAL_TO'   => 'Egal à',
   'FIND_USERNAME_EXPLAIN'   => 'Utilisez ce formulaire pour rechercher un membre. Vous n’avez pas besoin de compléter tous les champs. Pour effectuer une recherche partielle, utilisez un * comme joker. Utilisez le format de date <kbd>AAAA-MM-JJ</kbd>, par exemple: <samp>2004-02-29</samp>. Utilisez les cases à cocher pour sélectionner un ou plusieurs noms d’utilisateurs (plusieurs noms d’utilisateurs peuvent être acceptés selon le formulaire lui-même) puis cliquez sur “Valider la sélection” pour retourner au formulaire précédent.',
   'FLOOD_EMAIL_LIMIT'   => 'Vous ne pouvez pas envoyer un autre e-mail si rapidement. Réessayez à nouveau dans quelques instants.',
   'GROUP_LEADER'   => 'Modérateur du groupe',
   'HIDE_MEMBER_SEARCH'   => 'Cacher la recherche des membres',
   'IM_ADD_CONTACT'   => 'Ajouter le contact',
   'IM_AIM'   => 'Notez que pour utiliser cette fonction vous devez avoir installé AOL Instant Messenger.',
   'IM_AIM_EXPRESS'   => 'AIM Express',
   'IM_DOWNLOAD_APP'   => 'Télécharger l’application',
   'IM_ICQ'   => 'Notez que les membres ont pu choisir de ne pas recevoir de messages instantanés non sollicités.',
   'IM_JABBER'   => 'Notez que les membres ont pu choisir de ne pas recevoir de messages instantanés non sollicités.',
   'IM_JABBER_SUBJECT'   => 'Ceci est un message automatique, merci de ne pas y répondre! Message de l’utilisateur %1$s le %2$s.',
   'IM_MESSAGE'   => 'Votre message',
   'IM_MSNM'   => 'Notez que pour utiliser cette fonction vous devez avoir installé Windows Messenger.',
   'IM_MSNM_BROWSER'   => 'Votre navigateur ne supporte pas cela.',
   'IM_MSNM_CONNECT'   => 'Windows Messenger n’est pas connecté.\nVous devez vous connecter pour continuer.',
   'IM_NAME'   => 'Votre nom',
   'IM_NO_DATA'			=> 'Aucune information de contact pour cet utilisateur.',
   'IM_NO_JABBER'   => 'Désolé, la transmission de messages instantanés des utilisateurs Jabber n’est pas supportée sur ce forum. Votre devez avoir un client Jabber installé sur votre système pour contacter le destinataire ci-dessus.',
   'IM_RECIPIENT'   => 'Destinataire',
   'IM_SEND'   => 'Envoyer un message',
   'IM_SEND_MESSAGE'   => 'Envoyer un message',
   'IM_SENT_JABBER'   => 'Votre message vers %1$s a été envoyé.',
   'IM_USER'   => 'Envoyer un message instantané',
   'LAST_ACTIVE'   => 'Dernière visite',
   'LESS_THAN'   => 'Moins que',
   'LIST_USER'   => '1 utilisateur',
   'LIST_USERS'   => '%d utilisateurs',
   'LOGIN_EXPLAIN_LEADERS'   => 'L’administrateur exige que vous soyez enregistré et connecté pour voir la liste des membres de l’équipe.',
   'LOGIN_EXPLAIN_MEMBERLIST'   => 'L’administrateur exige que vous soyez enregistré et connecté pour voir la liste des membres.',
   'LOGIN_EXPLAIN_SEARCHUSER'   => 'L’administrateur exige que vous soyez enregistré et connecté pour rechercher des membres.',
   'LOGIN_EXPLAIN_VIEWPROFILE'   => 'L’administrateur exige que vous soyez enregistré et connecté pour voir les profils.',
   'MORE_THAN'   => 'Plus que',

   'NO_EMAIL'   => 'Vous ne pouvez pas envoyer d’e-mail à ce membre.',
   'NO_VIEW_USERS'   => 'Vous ne pouvez pas voir la liste des membres ou les profils.',
   'ORDER'   => 'Ordre',
   'OTHER'   => 'Autre',
   'POST_IP'   => 'Posté depuis IP/domaine',
   'RANK'   => 'Rang',
   'REAL_NAME'   => 'Nom du destinataire',
   'RECIPIENT'   => 'Destinataire',
   'REMOVE_FOE'   => 'Supprimer de ma liste d’ignorés',
   'REMOVE_FRIEND'   => 'Supprimer de ma liste d’amis',
   'SEARCH_USER_POSTS'   => 'Rechercher les messages de l’utilisateur',
   'SELECT_MARKED'   => 'Valider la sélection',
   'SELECT_SORT_METHOD'   => 'Choisir la méthode de tri',
   'SEND_AIM_MESSAGE'   => 'Envoyer un message AIM',
   'SEND_ICQ_MESSAGE'   => 'Envoyer un message ICQ',
   'SEND_IM'   => 'Messagerie instantanée',
   'SEND_JABBER_MESSAGE'   => 'Envoyer un message Jabber',
   'SEND_MESSAGE'   => 'Message',
   'SEND_MSNM_MESSAGE'   => 'Envoyer un message MSN/WLM',
   'SEND_YIM_MESSAGE'   => 'Envoyer un message YIM',
   'SORT_EMAIL'   => 'E-mail',
   'SORT_LAST_ACTIVE'   => 'Dernière visite',
   'SORT_POST_COUNT'   => 'Nombre de messages',
   'USERNAME_BEGINS_WITH'   => 'Noms commençant par',
   'USER_ADMIN'   => 'Administrer l’utilisateur',
   'USER_BAN' 		=> 'Bannissement',
   'USER_FORUM'   => 'Statistiques de l’utilisateur',
   'USER_LAST_REMINDED'	=> array(
		0		=> 'Aucun rappel envoyé actuellement',
		1		=> '%1$d rappel envoyé<br />» %2$s',
		2		=> '%1$d rappels envoyés<br />» %2$s',
	),
   'USER_ONLINE'   => 'En ligne',
   'USER_PRESENCE'   => 'Présence sur le forum',
   'VIEWING_PROFILE'   => 'Vue du profil - %s',
   'VISITED'   => 'Dernière visite',
   'WWW'   => 'Site Internet',
));

?>