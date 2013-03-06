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

$ajax_topicid =  request_var('topicid', 0);

// Select some userdata from DB
$sql = 'SELECT topic_title, topic_poster, topic_time, topic_views, topic_replies
	FROM ' . TOPICS_TABLE . ' 
	WHERE topic_id = '. (int) $ajax_topicid;
$result = $db->sql_query($sql, 600);
if($row = $db->sql_fetchrow($result))
{
	// Get the Avatar
	$phpbb_root_path = generate_board_url() . '/';
	$theme_path = "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme';	

	// Send XML File
	header('Content-Type: text/xml; charset=utf-8');
	echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
	echo '<topicdata>';
	echo '<topictitle><![CDATA[' . $row['topic_title'] . ']]></topictitle>';
	echo '<topicposter><![CDATA[' . $row['topic_poster'] . ']]></topicposter>';
	echo '<topictime><![CDATA[' . $row['topic_time'] . ']]></topictime>';
	echo '<topicviews><![CDATA[' . $row['topic_views'] . ']]></topicviews>';
	echo '<topicreplies><![CDATA[' . $row['topic_replies'] . ']]></topicreplies>';
	echo '</topicdata>';
}
else
{
	echo $user->lang['GENERAL_ERROR'];
}
$db->sql_freeresult($result);
exit;
?>