<?php
/**
*
* @package tracker
* @version $Id: tracker_constants.php 114 2008-05-05 20:07:38Z evil3 $
* @copyright (c) 2008 http://www.jeffrusso.net
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

global $table_prefix;

define('TRACKER_CONFIG_TABLE',			$table_prefix . 'tracker_config');
define('TRACKER_ATTACHMENTS_TABLE',		$table_prefix . 'tracker_attachments');
define('TRACKER_PROJECT_TABLE',			$table_prefix . 'tracker_project');
define('TRACKER_PROJECT_CATS_TABLE',	$table_prefix . 'tracker_project_categories');
define('TRACKER_TICKETS_TABLE',			$table_prefix . 'tracker_tickets');
define('TRACKER_POSTS_TABLE',			$table_prefix . 'tracker_posts');
define('TRACKER_COMPONENTS_TABLE',		$table_prefix . 'tracker_components');
define('TRACKER_HISTORY_TABLE', 		$table_prefix . 'tracker_history');
define('TRACKER_VERSION_TABLE', 		$table_prefix . 'tracker_version');
define('TRACKER_PROJECT_WATCH_TABLE', 	$table_prefix . 'tracker_project_watch');
define('TRACKER_TICKETS_WATCH_TABLE', 	$table_prefix . 'tracker_tickets_watch');

// Used when returning allowed attachment extension
// This will allow use to find all extensions that are
// allowed board wide
define('TRACKER_EXTENSION_ID', 9999);

define('TRACKER_ASSIGNED_TO_GROUP', -1);
define('TRACKER_PRIORITY_DEFAULT', 4);
define('TRACKER_SEVERITY_DEFAULT', 3);

define('TRACKER_TICKET_UNLOCKED', 0);
define('TRACKER_TICKET_LOCKED', 1);
define('TRACKER_TICKET_UNHIDDEN', 0);
define('TRACKER_TICKET_HIDDEN', 1);
define('TRACKER_TICKET_UNSECURITY', 0);
define('TRACKER_TICKET_SECURITY', 1);

define('TRACKER_PROJECT_DISABLED', 0);
define('TRACKER_PROJECT_ENABLED', 1);

define('TRACKER_HISTORY_ASSIGNED_TO', 1);
define('TRACKER_HISTORY_STATUS_CHANGED', 2);
define('TRACKER_HISTORY_PRIORITY_CHANGED', 3);
define('TRACKER_HISTORY_SEVERITY_CHANGED', 4);

define('TRACKER_EMAIL_NOTIFY', 1);
define('TRACKER_EMAIL_NOTIFY_COMMENT', 2);
define('TRACKER_EMAIL_NOTIFY_STATUS_SINGLE', 3);
define('TRACKER_EMAIL_NOTIFY_STATUS_DOUBLE', 4);

define('TRACKER_SUBJECT_LENGTH', 30);

define('TRACKER_ALL', 0);
define('TRACKER_ALL_OPENED', 1);
define('TRACKER_ALL_CLOSED', 2);
define('TRACKER_NEW_STATUS', 3);
?>