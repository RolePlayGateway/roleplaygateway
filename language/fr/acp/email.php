<?php
/** 
*
* acp_email [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: email.php, v1.24 2008/07/03 17:07:24 Elglobo Exp $
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

	'ACP_MASS_EMAIL_EXPLAIN'		=> 'Vous pouvez envoyer un e-mail à tous vos utilisateurs ou à tous les membres d’un groupe particulier. Pour cela, un e-mail sera envoyé via l’adresse administrative, avec tous les destinataires en copie cachée. Si vous envoyez le message à un grand groupe de personnes, merci de patienter après avoir validé et de ne pas arrêter la page lors du traitement. Il est normal qu’un envoi de masse prenne du temps, vous aurez une notification quand le script aura terminé.',
	'ALL_USERS'						=> 'Tous les utilisateurs',
	
	'COMPOSE'				=> 'Ecrire',

	'EMAIL_SEND_ERROR'		=> 'Il y a eu une erreur lors de l’envoi de l’e-mail. Merci de consulter le %sJournal d’erreurs%s pour un message plus détaillé.',
	'EMAIL_SENT'			=> 'Votre message a été envoyé.',
	'EMAIL_SENT_QUEUE'		=> 'Votre message a été mis en attente pour l’envoi.',

	'LOG_SESSION'			=> 'Enregistre la session mail dans les journaux d’erreurs critiques',

	'SEND_IMMEDIATELY'		=> 'Envoyer immédiatement',
	'SEND_TO_GROUP'			=> 'Envoyer au groupe',
	'SEND_TO_USERS'			=> 'Envoyer aux utilisateurs',
	'SEND_TO_USERS_EXPLAIN'	=> 'Entrer des noms ici écrasera tout groupe sélectionné ci-dessus. Entrez chaque nom d’utilisateur sur une ligne différente.',
	
	'MAIL_HIGH_PRIORITY'	=> 'Haute',
	'MAIL_LOW_PRIORITY'		=> 'Basse',
	'MAIL_NORMAL_PRIORITY'	=> 'Normale',
	'MAIL_PRIORITY'			=> 'Priorité du mail',
	'MASS_MESSAGE'			=> 'Votre message',
	'MASS_MESSAGE_EXPLAIN'	=> 'Notez que vous ne pouvez mettre que du texte brut. Toutes les balises seront supprimées avant l’envoi.',
	
	'NO_EMAIL_MESSAGE'		=> 'Vous devez entrer un message.',
	'NO_EMAIL_SUBJECT'		=> 'Vous devez indiquer un sujet pour votre message.',
));

?>