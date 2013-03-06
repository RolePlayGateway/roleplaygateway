<?php
/**
*
* @package acp
* @version $Id: info_acp_user_reminder.php 92 2008-06-29 22:35:49Z lefty74 $
* @copyright (c) 2008 lefty74 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// Create the lang array if it does not already exist
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Merge language entries into the common lang array
$lang = array_merge($lang, array(
	'USER_REMINDER' 						=> 'User Reminder',
	'ACP_USER_REMINDER_CONFIGURATION' 		=> 'Configuration',
	'ACP_USER_REMINDER_ZERO_POSTER'			=> 'Zero Posters',
	'ACP_USER_REMINDER_INACTIVE' 			=> 'Inactive Users',
	'ACP_USER_REMINDER_NOT_LOGGED_IN'		=> 'Not logged in',
	'ACP_USER_REMINDER_INACTIVE_STILL'		=> '2nd Reminder',
	'ACP_USER_REMINDER_PROTECTED_USERS'		=> 'Protected Users',
	
	//Admin logs
	'LOG_USER_REMINDER_ZERO_POSTER'			=> '<strong>Sent reminder e-mails to zero posters</strong><br />» %s',
	'LOG_USER_REMINDER_ZERO_POSTER_CLEAR'	=> '<strong>Reminders to zero posters cleared</strong><br />» %s',
	'LOG_USER_REMINDER_INACTIVE'			=> '<strong>Sent reminder e-mails to inactive users</strong><br />» %s',
	'LOG_USER_REMINDER_INACTIVE_CLEAR'		=> '<strong>Reminders to inactive users cleared</strong><br />» %s',
	'LOG_USER_REMINDER_NOT_LOGGED_IN'		=> '<strong>Sent reminder e-mails to users never logged in</strong><br />» %s',
	'LOG_USER_REMINDER_NOT_LOGGED_IN_CLEAR'	=> '<strong>Reminders to never logged in users cleared</strong><br />» %s',
	'LOG_USER_REMINDER_INACTIVE_STILL'		=> '<strong>Sent 2nd user reminder to users not reacted to previous ones</strong><br />» %s',
	'LOG_USER_REMINDER_INACTIVE_STILL_CLEAR'	=> '<strong>2nd Reminders to users cleared</strong><br />» %s',
	'LOG_USER_REMINDER_CLEARED'				=> '<strong>Reminders cleared of protected users</strong><br />» %s',
	'LOG_USER_PROTECTED'						=> '<strong>Users protected from receiving reminders</strong><br />» %s',
	'LOG_USER_UNPROTECTED'						=> '<strong>Users removed from reminders protection</strong><br />» %s',

	
	'LOG_USER_REMINDER_CONFIG_UPDATED'		=> '<strong>User Reminder configuration updated</strong>',

	
	));
?>