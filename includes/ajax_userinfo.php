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
// PRS
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

// Start session management
$user->session_begin(false);
$user->setup();

$ajax_userid =  request_var('userid', "");

if (!is_int($ajax_userid)) {

	// Do magic to convert HTML-escaped spaces to regular spaces
	$ajax_userid = preg_replace("/%20/"," ",$ajax_userid);

	$sql = "SELECT user_id FROM gateway_users WHERE username_clean = '".$db->sql_escape($ajax_userid)."'";
	$result = $db->sql_query($sql);
	if($row = $db->sql_fetchrow($result))
	{
		$ajax_userid = $row['user_id'];
	}
}

// Select some userdata from DB
$sql = 'SELECT user_id,username, username_clean, user_regdate, user_posts, user_from, user_lastvisit, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_colour, user_website, user_rank
	FROM ' . USERS_TABLE . ' 
	WHERE user_id = '. $db->sql_escape($ajax_userid);
$result = $db->sql_query($sql, 600);
if($row = $db->sql_fetchrow($result))
{
	// Get the Avatar
	$phpbb_root_path = generate_board_url() . '/';
	$theme_path = "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme';
	$avatar = get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']);

	// Get rank
	$rank_title = $rank_img = $rank_img_src = '';
	get_user_rank($row['user_rank'], $row['user_posts'], $rank_title, $rank_img, $rank_img_src);
	
	$userID = $row['user_id'];

	// Get username with usercolor
	$displayName = get_username_string('full', $ajax_userid, $row['username'], $row['user_colour'], $row['username_clean']);
	$username = $row['username'];
	
	$sql = 'SELECT prs_reputation,total_words,average_words
		FROM gateway_user_stats
		WHERE user_id = '. (int) $ajax_userid;
	$result = $db->sql_query($sql, 3600);
	if($user_stats = $db->sql_fetchrow($result))
	{
		//$reputation = $user_stats['prs_reputation'];
		$reputation = 3;
		$words_written = $user_stats['total_words'];
		$words_per_post = $user_stats['average_words'];
	}	

	// Send XML File
	header('Content-Type: text/xml; charset=utf-8');
	echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
	echo '<userdata>';
	echo '<userID>'.$userID.'</userID>';
	echo '<username>' . $username . '</username>';
	echo '<displayName><![CDATA[' . $displayName . ']]></displayName>';
	echo '<reputation><![CDATA[' . $reputation . ']]></reputation>';
	echo '<wordswritten><![CDATA[' . $words_written . ']]></wordswritten>';
	echo '<wordsperpost><![CDATA[' . $words_per_post . ']]></wordsperpost>';
	echo '<regdate><![CDATA[' . $user->format_date($row['user_regdate']) . ']]></regdate>';
	echo '<posts><![CDATA[' . $row['user_posts'] . ']]></posts>';
	
	echo '<from><![CDATA[' . (!empty($row['user_from']) ? $row['user_from'] : $user->lang['NA']) . ']]></from>';
	echo '<lastvisit><![CDATA[' . (!empty($row['user_lastvisit']) ? $user->format_date($row['user_lastvisit']) : $user->lang['NA']) . ']]></lastvisit>';
	echo '<website><![CDATA[' . (!empty($row['user_website']) ? $row['user_website'] : $user->lang['NA']) . ']]></website>';
	echo '<avatar><![CDATA[' . (!empty($avatar) ? $avatar : '<img src="http://www.roleplaygateway.com/images/no_avatar.png" alt="No Avatar" />') . ']]></avatar>';
	echo '<rank><![CDATA[' . (!empty($rank_title) ? $rank_title : "") . ']]></rank>';

	$sql = 'SELECT id,title FROM rpg_roleplays WHERE owner = '.(int) $ajax_userid;
	$result = $db->sql_query($sql, 3600);
	echo "<roleplays>";
	while ($user_roleplays = $db->sql_fetchrow($result)) {
		echo "<roleplay>";
		echo "<roleplayID>".$user_roleplays['id']."</roleplayID>";
		echo "<roleplayName>".$user_roleplays['title']."</roleplayName>";
		echo "</roleplay>";
	}
	echo "</roleplays>";
	
	$sql = 'SELECT id,name FROM rpg_characters WHERE owner = '.(int) $ajax_userid;
	$result = $db->sql_query($sql, 3600);
	echo "<characters>";
	while ($user_characters = $db->sql_fetchrow($result)) {
		echo "<character>";
		echo "<characterID>".$user_characters['id']."</characterID>";
		echo "<characterName>".$user_characters['name']."</characterName>";
		echo "</character>";
	}
	echo "</characters>";
	
	echo '</userdata>';
}
else
{
	echo $user->lang['GENERAL_ERROR'];
}
$db->sql_freeresult($result);
exit;
?>
