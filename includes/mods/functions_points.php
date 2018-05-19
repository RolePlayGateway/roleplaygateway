<?php
/** 
*
* @package phpBB3
* @version $Id: functions_points.php,v 1.0 2007/04/22 23:46:26 Ganon_Master Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/*
* Some small functions. 
*/

if(!defined('IN_PHPBB'))
{
	exit;
}

function add_points($user_id, $amount)
{
	global $db;
	
	$sql="UPDATE ".USERS_TABLE." 
		SET user_points = user_points + $amount
		WHERE user_id = $user_id";
	$db->sql_query($sql);
	
	return;
}

function substract_points($user_id, $amount)
{
	global $db;

	$sql="UPDATE ".USERS_TABLE." 
		SET user_points = user_points - $amount
		WHERE user_id = $user_id";
	$db->sql_query($sql);
	
	return;
}

?>