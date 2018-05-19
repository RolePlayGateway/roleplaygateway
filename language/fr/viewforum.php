<?php
/** 
*
* viewforum [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: viewforum.php, v1.25 2009/12/16 16:36:00 Elglobo Exp $
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
   'ACTIVE_TOPICS'   => 'Sujets actifs',
   'ANNOUNCEMENTS'   => 'Annonces',

   'FORUM_PERMISSIONS' => 'Permissions du forum',

   'ICON_ANNOUNCEMENT'   => 'Annonce',
   'ICON_STICKY' => 'Post-it',

   'LOGIN_NOTIFY_FORUM' => 'Vous avez été averti de la présence d’un nouveau message dans ce forum, connectez-vous pour y accéder.',

   'MARK_TOPICS_READ' => 'Marquer tous les sujets comme lus',

   'NEW_POSTS_HOT' => 'Nouveaux messages [ Populaires ]',
   'NEW_POSTS_LOCKED' => 'Nouveaux messages [ Verrouillés ]',
   'NO_NEW_POSTS_HOT' => 'Pas de nouveaux messages [ Populaires ]',
   'NO_NEW_POSTS_LOCKED' => 'Pas de nouveaux messages [ Verrouillés ]',
   'NO_READ_ACCESS'      => 'Vous n’avez pas les permissions requises pour lire les sujets de ce forum.',


   'POST_FORUM_LOCKED' => 'Le forum est verrouillé',

   'TOPICS_MARKED' => 'Les sujets de ce forum ont été marqués comme lus.',

   'VIEW_FORUM' => 'Voir le forum',
   'VIEW_FORUM_TOPIC' => '1 sujet',
   'VIEW_FORUM_TOPICS' => '%d sujets',
));

?>