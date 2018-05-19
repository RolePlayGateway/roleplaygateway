<?php
/** 
*
* search [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: search.php, v1.25 2009/10/16 15:03:00 Elglobo Exp $
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
	'ALL_AVAILABLE'   => 'Tous disponibles',
	'ALL_RESULTS'   => 'Tous les résultats',

	'DISPLAY_RESULTS'   => 'Afficher les résultats sous forme de',

	'FOUND_SEARCH_MATCH'   => '%d résultat trouvé',
    'FOUND_SEARCH_MATCHES'   => '%d résultats trouvés',
	'FOUND_MORE_SEARCH_MATCHES'   => 'La recherche a trouvé plus de %d résultats',

	'GLOBAL'   => 'Annonce globale',

	'IGNORED_TERMS'   => 'ignoré',
	'IGNORED_TERMS_EXPLAIN'   => 'Les mots suivants de votre recherche ont été ignorés parce qu’ils sont trop communs: <strong>%s</strong>.',

	'JUMP_TO_POST' => 'Aller au message',

	'LOGIN_EXPLAIN_EGOSEARCH'			=> 'Vous devez être enregistré et connecté afin de voir vos propres messages.',
	'LOGIN_EXPLAIN_UNREADSEARCH'=> 'Vous devez être enregistré et connecté pour voir les messages non lus.',
	'MAX_NUM_SEARCH_KEYWORDS_REFINE'	=> 'Vous avez spécifié un nombre de mots trop important à rechercher. N’entrez pas plus de %1$d mots.',
	
	'NO_KEYWORDS'   => 'Vous devez indiquer au moins un mot pour effectuer une recherche. Chaque mot doit se composer d’au moins %d caractères et ne doit pas en contenir plus de %d en excluant les jokers.',
	'NO_RECENT_SEARCHES'   => 'Aucune recherche n’a été effectuée récemment.',
	'NO_SEARCH'   => 'Désolé mais vous n’êtes pas autorisé à utiliser le système de recherche.',
	'NO_SEARCH_RESULTS'   => 'Aucun sujet ou message ne correspond à vos critères de recherche.',
	'NO_SEARCH_TIME'   => 'Désolé mais vous ne pouvez pas utiliser la fonction recherche actuellement. Merci de réessayer dans quelques instants.',
	'WORD_IN_NO_POST'   => 'Aucun résultat trouvé pour le mot <strong>%s</strong>.',
	'WORDS_IN_NO_POST'   => 'Aucun résultat trouvé pour les mots <strong>%s</strong>.',

	'POST_CHARACTERS'   => 'premiers caractères des messages',

	'RECENT_SEARCHES'   => 'Recherches récentes',
	'RESULT_DAYS'   => 'Rechercher depuis',
	'RESULT_SORT'   => 'Classer les résultats par',
	'RETURN_FIRST'   => 'Renvoyer les',
	'RETURN_TO_SEARCH_ADV'   => 'Revenir à la recherche avancée',

	'SEARCHED_FOR'   => 'Rechercher les termes utilisés',
    'SEARCHED_TOPIC'   => 'Sujet recherché',
    'SEARCH_ALL_TERMS'   => 'Rechercher tous les termes',
    'SEARCH_ANY_TERMS'   => 'Rechercher n’importe lequel de ces termes',
    'SEARCH_AUTHOR'   => 'Rechercher par auteur',
	'SEARCH_AUTHOR_EXPLAIN'   => 'Utilisez un * comme joker pour des recherches partielles.',
    'SEARCH_FIRST_POST'   => 'Premier message des sujets uniquement',
    'SEARCH_FORUMS'   => 'Rechercher dans les forums',
    'SEARCH_FORUMS_EXPLAIN'   => 'Choisissez le forum ou les forums dans le(s)quel(s) vous souhaitez effectuer une recherche. Les sous-forums sont automatiquement inclus si vous ne désactivez pas l’option ci-dessous “Rechercher dans les sous-forums”.',
    'SEARCH_IN_RESULTS'   => 'Rechercher dans ces résultats',
    'SEARCH_KEYWORDS_EXPLAIN'   => 'Placez un <strong>+</strong> devant un mot qui doit être trouvé et un <strong>-</strong> devant un mot qui doit être exclu. Tapez une suite de mots séparés par des <strong>|</strong> entre crochets si uniquement un des mots doit être trouvé. Utilisez un * comme joker pour des recherches partielles.',
	'SEARCH_MSG_ONLY'   => 'Messages uniquement',
	'SEARCH_OPTIONS'   => 'Options de recherche',
	'SEARCH_QUERY'   => 'Rechercher',
	'SEARCH_SUBFORUMS'   => 'Rechercher dans les sous-forums',
	'SEARCH_TITLE_MSG'   => 'Titres et messages',
	'SEARCH_TITLE_ONLY'   => 'Titres uniquement',
	'SEARCH_WITHIN'   => 'Rechercher dans',
	'SORT_ASCENDING'   => 'Croissant',
    'SORT_AUTHOR'   => 'Auteur',
	'SORT_DESCENDING'   => 'Décroissant',
    'SORT_FORUM'   => 'Forum',
    'SORT_POST_SUBJECT'   => 'Sujet du message',
    'SORT_TIME'   => 'Date',

	'TOO_FEW_AUTHOR_CHARS'   => 'Vous devez indiquer au moins %d caractères du nom de l’auteur.',
));

?>