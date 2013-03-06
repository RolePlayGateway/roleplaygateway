<?php
/**
*
* acp [English]
*
* @package notify_moderators
* @version $Id: 1.0.2
* @copyright (c) 2008 david63
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
	'CLOSE_NOTIFICATION' 		=> 'Report closed notification',
	'DELETE_NOTIFICATION' 		=> 'Report deleted notification',
	'EDIT_NOTIFICATION'			=> 'Post edit notification',
	'POST_APPROVE'		 		=> 'Post approval notification',
	'POST_DISAPPROVE'			=> 'Post disapproval notification',
	'POST_GLOBAL'				=> 'Global',
	'POST_NOTIFICATION' 		=> 'New post notification',
	'POST_NOTIFY_EDIT'			=> 'edited post',
	'POST_NOTIFY_NEW'			=> 'new post',	
	'POST_NOTIFY_REPLY'			=> 'reply',
	'POST_QUEUE'				=> 'Post queue',
	'POST_QUEUE_NOTIFICATION'	=> 'Queued post notification',
	'QUEUE_NOTIFICATION'		=> 'Queued reply notification',
	'QUEUED_POST'				=> 'queued post',
	'REPLY_NOTIFICATION'		=> 'Post reply notification',
	'REPORT_NOTIFICATION' 		=> 'New report notification',	
	'REPORTED'					=> 'Reported',
	'TOPIC_QUEUE'				=> 'Topic queue',	

	// Text for PM notifications
	'PM_POST'					=> "Hello %s\n\nYou are receiving this notification because you are either an Administrator or a moderator of the %s forum.\nThe following %s has been made in the topic %s\n\nAuthor : %s\nTopic URL : %s\nSubject : %s\nTopic type : %s\nQueued : %s\nMessage :\n\n%s\n\n%s",
	
	'PM_POST_APPROVE'			=> "Hello %s,\n\nYou are receiving this notification because you are either an Administrator or a moderator of the %s forum.\n The following queued post has been approved by %s in the topic %s\n\nAuthor : %s\nTopic URL : %s\nSubject : %s\nTopic type : %s\nQueued : %s\nMessage :\n\n%s\n\n%s",

	'PM_POST_DISAPPROVE'		=> "Hello %s,\n\nYou are receiving this notification because you are either an Administrator or a moderator of the %s forum.\n The following queued post has been disapproved by %s in the topic %s\n\nAuthor : %s\nReason : %s\nSubject : %s\nTopic type : %s\nTopic type : %s\nQueued : %s\nMessage :\n\n%s\n\n%s",

	'PM_REPORT'					=> "Hello %s,\n\nYou are receiving this notification because you are either an Administrator or a moderator of the %s forum.\n The following report has been made by %s in the topic %s\n\nTopic URL : %s\nReport URL : %s\nSubject : %s\nReport reason : %s\nReport message :\n\n%s\n\n%s",

	'PM_REPORT_CLOSE'			=> "Hello %s,\n\nYou are receiving this notification because you are either an Administrator or a moderator of the %s forum.\n The following report has been closed by %s in the topic %s\n\nTopic URL : %s\nReport URL : %s\nSubject : %s\nReport reason : %s\nReport message :\n\n%s\n\n%s",
));

?>