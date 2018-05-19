<?php
/**
*
* acp [English]
*
* @package notify_moderators
* @version $Id: 1.1.0
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
	'ACP_NOTIFY_MODERATORS'			=> 'Notify Moderators',
	'ACP_NOTIFY_SETTINGS_EXPLAIN'	=> 'Here you can set the options for sending copies of forum posts and/or reported posts to the Admin and/or Forum Moderators.',
	'ACP_COPY_ADMIN'				=> 'Notify Admin(s)',
	'ACP_COPY_MODERATOR'			=> 'Notify Moderator(s)',
	'ACP_COPY_GROUP'				=> 'Notify Group Moderators',
	'ACP_COPY_GROUP_EXPLAIN'		=> 'Only required if you have moderator groups.',
	'ACP_MODERATOR_OWN'				=> 'Send own messages',
	'ACP_MODERATOR_OWN_EXPLAIN'		=> 'Send the Admin/Moderator a copy of their own messages, when they approve/disapprove a post or make or action a report.',
	'ACP_MODERATOR_POST'			=> 'Notify posts',
	'ACP_MODERATOR_POST_EXPLAIN'	=> 'Send the Admin/Moderator a copy of new topics.',
	'ACP_MODERATOR_REPLY'			=> 'Notify replies',
	'ACP_MODERATOR_REPLY_EXPLAIN'	=> 'Send the Admin/Moderator a copy of replied to topics.',
	'ACP_MODERATOR_EDIT'			=> 'Notify edited messages',
	'ACP_MODERATOR_EDIT_EXPLAIN'	=> 'Send the Admin/Moderator a copy of edited posts.',
	'ACP_MODERATOR_REPORT'			=> 'Notify reported posts',
	'ACP_MODERATOR_REPORT_EXPLAIN'	=> 'Send the Admin/Moderator a copy of a reported post.<br />If this is set to <b>Yes</b> then Admin(s)/Moderators will always be notified of reports - even if they have selected not to receive notifications.',
	'ACP_MODERATOR_CLOSE'			=> 'Notify closed reports',
	'ACP_MODERATOR_CLOSE_EXPLAIN'	=> 'Send the Admin/Moderator a message when a reported post has been closed or deleted.',
	'ACP_MODERATOR_APPROVE'			=> 'Notify approved/disapproved posts',
	'ACP_MODERATOR_APPROVE_EXPLAIN'	=> 'Send the Admin/Moderator a message when a post has been approved or disapproved.',
	'ACP_MODERATOR_QUEUE'			=> 'Notify moderator queued posts',
	'ACP_MODERATOR_QUEUE_EXPLAIN'	=> 'Send the Admin/Moderator a copy of a queued topic/post.',
	'ACP_NOTIFY_PM'					=> 'Allow users to be notified by PM',
	'ACP_NOTIFY_PM_EXPLAIN'			=> 'Allow Admin(s)/Moderators to have their notifications sent via a PM instead of by their normal notification method.',
	'ACP_OVERIDE_REPORT'			=> 'Overide Moderator’s setting',
	'ACP_OVERIDE_REPORT_EXPLAIN'	=> 'Send the Admin/Moderator a notification of a reported post even if they have requested not to have any notifications.',
	'APC_PM_ADMIN'					=> 'Select the Admin to send notifications by PM',
	'APC_PM_ADMIN_EXPLAIN'			=> 'This is the Admin who will send the PM.',
	'ACP_REPORT_COUNT'				=> 'Show report count on index',
	'ACP_QUEUE_COUNT'				=> 'Show queue counts on index',

	'ACP_NOTIFY_SETTINGS'			=> 'Notify moderator settings',
	'ACP_NOTIFY_LIST'				=> 'List moderators',

	'ACP_NOTIFY_PM_SETTINGS'		=> 'Notify PM settings',
	'ACP_NOTIFY_WHO'				=> 'Notify who',
	'ACP_NOTIFY_WHAT'				=> 'Notify what',
	'ACP_NOTIFY_COUNTS'				=> 'Show counts',

	'NORMAL'						=> 'Normal',
	'NONE'							=> 'None',

	'INSTALL_NOT_DELETED'			=> 'The install file for this mod has not been deleted',

	'LOG_CONFIG_NOTIFY'				=> '<b>Altered notify moderator settings</b>',

	'NOTIFY_MODERATOR'				=> 'Notify me of new posts',
	'NOTIFY_MODERATOR_EXPLAIN'		=> 'Notify me of any new posts made in the forums that I am a Moderator of.',

	'LIST_MODERATORS_TITLE'			=> 'Moderator List',
	'LIST_MODERATORS_TITLE_EXPLAIN'	=> 'This is a list of the board moderators together with their notification method and the forums that they moderate.',
	'NOTIFY_METHOD'					=> 'Notify method',
	'EMAIL_JABBER'					=> 'Email & Jabber',
));

// Install
$lang = array_merge($lang, array(
	'COMPLETE'						=> 'Install complete ...',
	'INSTALL_NOTIFY_MODERATORS'		=> 'Installing Notify Moderators Mod',
	'NO_FOUNDER'					=> 'You are not authorised to install this mod - you need Founder status.',
	'CAT_ERROR'						=> 'Category module %s cannot be found.',
));

?>