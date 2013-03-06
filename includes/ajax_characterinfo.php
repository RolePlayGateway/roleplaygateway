<?php
/**
*
* @package AJAX userinfo
* @version $Id: ajax_user.php, V1.0.0 2008-09-14 23:08:23 tas2580 $
* @copyright (c) 2007 SEO phpBB http://www.phpbb-seo.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin(false);
$user->setup();

$ajax_charid =  request_var('charid', 0);
$mode =  request_var('mode', "");

$output = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
$output .= '<characterdata>';

// Select some userdata from DB
$sql = 'SELECT synopsis,health,healthMax,dexterity,strength,lastAction,speed
	FROM rpg_characters
		LEFT OUTER JOIN rpg_characters_stats ON rpg_characters.id = rpg_characters_stats.character_id
	WHERE rpg_characters.id = '. (int) $ajax_charid;
$result = $db->sql_query($sql);
if($row = $db->sql_fetchrow($result)) {

	$output .= '<synopsis><![CDATA[' . $row['synopsis'] . ']]></synopsis>';
	$output .= '<health>' . $row['health'] . '</health>';
	$output .= '<healthMax>' . $row['healthMax'] . '</healthMax>';
	$output .= '<dexterity>' . $row['dexterity'] . '</dexterity>';
	$output .= '<strength>' . $row['strength'] . '</strength>';
	$output .= '<lastAction>' . $row['lastAction'] . '</lastAction>';
	$output .= '<nextAction>' . ($row['lastAction'] + 5) . '</nextAction>';
	$output .= '<speed>' . $row['speed'] . '</speed>';

}

$output .= '</characterdata>';



// Send XML File
header('Content-Type: text/xml; charset=utf-8');
echo $output;

$db->sql_freeresult($result);
exit;
?>