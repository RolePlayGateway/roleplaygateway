<?php
/**
*
* @package phpBB3
* @version $Id: index.php,v 1.176 2007/10/05 14:30:06 acydburn Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');


$result = $db->sql_query("SELECT VERSION();");
$row = $db->sql_fetchrow($result);
trigger_error(implode(", ",$row));

trigger_error($row[0]);
	


page_header('RolePlayGateway Video Chat!');

$template->set_filenames(array(
	'body' => 'video_chat.html')
);

page_footer();

?>

