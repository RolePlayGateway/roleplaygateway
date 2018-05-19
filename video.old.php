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

if ($user->data['user_id'] == 25647) {
	trigger_error('You have been banned from video chat.');
}

if (($user->data['user_id'] == 661) || ($user->data['user_id'] == 23511) || ($user->data['session_ip'] == "98.238.123.40")) {
	die();
}

/* if (!$user->data['is_registered']) {
	login_box(null,'You must log in to use video chat.');
} */

page_header('RolePlayGateway Video Chat!');

$template->set_filenames(array(
	'body' => 'video_chat.html')
);

page_footer();

?>
