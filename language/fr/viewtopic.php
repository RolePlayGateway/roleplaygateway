<?php
/** 
*
* viewtopic [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: viewtopic.php, v1.25 2009/10/16 15:13:00 Elglobo Exp $
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
	'ATTACHMENT'						=> 'Fichier(s) joint(s)',
	'ATTACHMENT_FUNCTIONALITY_DISABLED'	=> 'Les fichiers joints ont été désactivés.',

	'BOOKMARK_ADDED'		=> 'Le sujet a été ajouté aux favoris.',
	'BOOKMARK_ERR' 			=> 'Le sujet n’a pas pu être ajouté aux favoris. Essayez à nouveau.',
	'BOOKMARK_REMOVED'		=> 'Le sujet a été supprimé des favoris.',
	'BOOKMARK_TOPIC'		=> 'Ajouter ce sujet aux favoris',
	'BOOKMARK_TOPIC_REMOVE'	=> 'Supprimer ce sujet des favoris',
	'BUMPED_BY'				=> 'Sujet remonté par %1$s le %2$s.',
	'BUMP_TOPIC'			=> 'Remonter le sujet',

	'CODE'					=> 'Code',
	'COLLAPSE_QR'			=> 'Masquer la réponse rapide',

	'DELETE_TOPIC'			=> 'Supprimer le sujet',
	'DOWNLOAD_NOTICE'		=> 'Vous n’avez pas les permissions nécessaires pour voir les fichiers joints à ce message.',

	'EDITED_TIMES_TOTAL'	=> 'Dernière édition par %1$s le %2$s, édité %3$d fois.',
	'EDITED_TIME_TOTAL'		=> 'Dernière édition par %1$s le %2$s, édité %3$d fois.',
	'EMAIL_TOPIC'			=> 'Envoyer par e-mail à un ami',
	'ERROR_NO_ATTACHMENT'	=> 'Le fichier joint sélectionné n’est plus disponible.',

	'FILE_NOT_FOUND_404'	=> 'Le fichier <strong>%s</strong> n’existe pas.',
	'FORK_TOPIC'			=> 'Copier le sujet',
	'FULL_EDITOR'			=> 'Editeur complet',

	'LINKAGE_FORBIDDEN'		=> 'Vous ne pouvez pas voir, télécharger ou joindre des fichiers à partir de ce site.',
	'LOGIN_NOTIFY_TOPIC'	=> 'Vous avez demandé à surveiller ce sujet. Connectez-vous pour le voir.',
	'LOGIN_VIEWTOPIC'		=> 'Vous devez être enregistré et connecté pour voir ce sujet.',

	'MAKE_ANNOUNCE'				=> 'Mettre en “Annonce”',
	'MAKE_GLOBAL'				=> 'Mettre en “Annonce globale”',
	'MAKE_NORMAL'				=> 'Mettre en “Sujet standard”',
	'MAKE_STICKY'				=> 'Mettre en “Post-it”',
	'MAX_OPTIONS_SELECT'		=> 'Vous pouvez sélectionner jusqu’à <strong>%d</strong> options',
	'MAX_OPTION_SELECT'			=> 'Vous pouvez sélectionner <strong>1</strong> option',
	'MISSING_INLINE_ATTACHMENT'	=> 'Le fichier joint <strong>%s</strong> n’est plus disponible.',
	'MOVE_TOPIC'				=> 'Déplacer le sujet',

	'NO_ATTACHMENT_SELECTED'=> 'Vous n’avez pas sélectionné de fichier joint à voir ou à télécharger.',
	'NO_NEWER_TOPICS'		=> 'Aucun nouveau sujet dans ce forum.',
	'NO_OLDER_TOPICS'		=> 'Aucun ancien sujet dans ce forum.',
	'NO_UNREAD_POSTS'		=> 'Aucun nouveau message non-lu dans ce sujet.',
	'NO_VOTE_OPTION'		=> 'Vous devez choisir une option lorsque vous votez.',
	'NO_VOTES'				=> 'Aucun vote',

	'POLL_ENDED_AT'         => 'Le sondage s’est terminé le %s',
	'POLL_RUN_TILL'			=> 'Le sondage est actif jusqu’au %s',
	'POLL_VOTED_OPTION'		=> 'Vous avez voté pour cette option',
	'PRINT_TOPIC'			=> 'Imprimer le sujet',

	'QUICK_MOD'				=> 'Actions rapides de modération',
	'QUICKREPLY'			=> 'Réponse rapide',
	'QUOTE'					=> 'Citation',

	'REPLY_TO_TOPIC'		=> 'Répondre au sujet',
	'RETURN_POST'			=> '%sRevenir au message%s',
	'SHOW_QR'				=> 'Réponse rapide',

	'SUBMIT_VOTE'			=> 'Voter',

	'TOTAL_VOTES'			=> 'Nombre total de votes',

	'UNLOCK_TOPIC'			=> 'Déverrouiller le sujet',

	'VIEW_INFO'				=> 'Informations du message',
	'VIEW_NEXT_TOPIC'		=> 'Sujet suivant',
	'VIEW_PREVIOUS_TOPIC'	=> 'Sujet précédent',
	'VIEW_RESULTS'			=> 'Voir les résultats',
	'VIEW_TOPIC_POST'		=> '1 message',
	'VIEW_TOPIC_POSTS'		=> '%d messages',
	'VIEW_UNREAD_POST'		=> 'Voir le premier message non lu',
	'VISIT_WEBSITE'			=> 'Site Internet',
	'VOTE_SUBMITTED'		=> 'Votre vote a été pris en compte.',
	'VOTE_CONVERTED'      	=> 'La modification d’un vote n’est pas possible pour les sondages issus d’une conversion.',
	
));

?>