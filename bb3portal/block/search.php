<?php
/*
*
* @name search.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: search.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
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

// Assign specific vars
$template->assign_vars(array(
	'S_SEARCH_ACTION'	=> "{$phpbb_root_path}search.$phpEx",
	)
);

?>