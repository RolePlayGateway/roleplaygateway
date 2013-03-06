<?php
/** 
*
* @package phpBB3
* @version $Id: index.php,v 1.175 2007/07/26 15:49:44 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// Figure out who sent them here
if (isset($_SERVER['HTTP_REFERER']))
{
	$referer = $_SERVER['HTTP_REFERER'];
} else {
	$referer = "someone";
}


// Assign index specific vars
$template->assign_vars(array(
	'URI'			=> $_SERVER['REQUEST_URI'],
	'REFERER'		=> $referer
	));

// Output page
page_header('404 Error: '.$_SERVER['REQUEST_URI'].' Not Found');

$template->set_filenames(array(
	'body' => '404_body.html',)
);

page_footer();

?>