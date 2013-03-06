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

$ajax_roleplayid =  request_var('roleplayid', 0);

$output = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
$output .= '<roleplay>';

// Select some userdata from DB
$sql = 'SELECT id,title,description,owner,type
	FROM rpg_roleplays
	WHERE id = '. (int) $ajax_roleplayid;
$result = $db->sql_query($sql);
if($row = $db->sql_fetchrow($result)) {



	$output .= '<title>' . $row['title'] . '</title>';
	$output .= '<owner>' . $row['owner'] . '</owner>';
	$output .= '<type>' . $row['type'] . '</type>';
	$output .= '<description><![CDATA[' . $row['description'] . ']]></description>';
	
	$sql = 'SELECT id,name
	FROM rpg_characters c
		INNER JOIN rpg_roleplay_players p
			ON c.id = p.character_id
	WHERE p.roleplay_id = '. (int) $ajax_roleplayid;
	$character_result = $db->sql_query($sql);
	
	while ($characters = $db->sql_fetchrow($character_result)) {
		$output .= '<character>';
		$output .= '<characterID>'.$characters['id'].'</characterID>';
		$output .= '<characterName>'.$characters['name'].'</characterName>';
		$output .= '</character>';
	}

}

$output .= '</roleplay>';



// Send XML File
header('Content-Type: text/xml; charset=utf-8');
echo $output;

$db->sql_freeresult($result);
exit;
?>