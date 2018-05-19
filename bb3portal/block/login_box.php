<?php
/*
*
* @name login_box.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: login_box.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

$s_display = true;

// Assign specific vars
$template->assign_vars(array(
	'U_PORTAL'				=> append_sid("{$phpbb_root_path}bb3portal.$phpEx"),
	'S_DISPLAY_FULL_LOGIN'	=> ($s_display) ? true : false,
	'S_AUTOLOGIN_ENABLED'	=> ($config['allow_autologin']) ? true : false,
	'S_LOGIN_ACTION'	=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
	)
);

?>