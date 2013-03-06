<?php

define('IN_PHPBB', true);
define('IN_GAMES_MOD', true);

$phpbb_root_path = '/var/www/vhosts/thegrandtournament.com/httpdocs/';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
include($phpbb_root_path . 'includes/functions_post.'.$phpEx);


$challenger = mysql_real_escape_string($_REQUEST['challenger']);
$opponent = mysql_real_escape_string($_REQUEST['opponent']);

$sql = "SELECT user_id,username FROM grand_users WHERE user_email = '".$challenger."'";
if ($result = $db->sql_query($sql)) {
	while ($row = $db->sql_fetchrow($result)) {
		$challenger_id = $row['user_id'];
		$challenger_name = $row['username'];
	}
} else {
	$to_email = $challenger;
	$from_email = $opponent;
}
$db->sql_freeresult($result);

$sql = "SELECT user_id,username FROM grand_users WHERE user_email = '".$opponent."'";
if ($result = $db->sql_query($sql)) {
	while ($row = $db->sql_fetchrow($result)) {
		$opponent_id = $row['user_id'];
		$opponent_name = $row['username'];
	}
} else {
	$to_email = $opponent;
	$from_email = $challenger;
}
$db->sql_freeresult($result);

if ($challenger_id >= 1 && $opponent_id >= 1) {

	echo "We have a match! $challenger_name and $opponent_name";

	//
	// Set some vars
	//
	$topic_id = '';
	$post_id = '';
	$poll_id = '';
	$bbcode_on = 1;
	$bbcode_uid = 0;
	$html_on = 0;
	$smilies_on = 0;
	$error_msg = '';
	$notify_user = TRUE;
	$attach_sig = 0;
	$post_data = array();
	$post_data['first_post'] = true;
	$post_data['last_post'] = false;
	$post_data['has_poll'] = false;
	$post_data['edit_poll'] = false;
	$post_data['last_topic'] = true;
		
		
	//
	// Submit the new game (newtopic)
	//
	$return_message = '';
	$return_meta = '';
	
	$username = get_games_username($challenger_id);

	$challenger_points = get_games_user_points($challenger_id);
	$opponent_points = get_games_user_points($opponent_id);

	$subject = $lang['Games_Subject_Prefix_Running'] . " ";
	$subject .= $lang['Games_Subject_part1'];
	$subject .= get_next_game_id();
	$subject .= ': ';
	$subject .= $username;
	$subject .= $lang['Games_Subject_part2'];
	$subject .= $opponent_name;

	$game_description = ( !empty($HTTP_POST_VARS['message']) ) ? $HTTP_POST_VARS['message'] : '';
	$message = "\n\r\n\r[color=green][u]" . $lang['Games_Description'] . "[/u][/color]\n\r" . $game_description . "\n\r\n\r[color=green][u]" . $lang['Games_Points_Before_Match'] . "[/u][/color]\n\r" . $username . ": " . $challenger_points . " points, " . $opponent_name . ": " . $opponent_points . " points.";
	$message .= "\n\nSubmitted from ".$_SERVER['REFERING_URL'];
	
	$topic_type = POST_NORMAL;

	$poll_title = $lang['Games_Poll_Question'];
	$poll_length = '';
	$poll_option_text = array();
	$poll_option_text[] = htmlspecialchars(trim(stripslashes($username)));
	$poll_option_text[] = htmlspecialchars(trim(stripslashes($opponent_name)));
	$poll_options = array();
	while( list($option_id, $option_text) = @each($poll_option_text) )
	{
		$poll_options[$option_id] = htmlspecialchars(trim(stripslashes($option_text)));
	}
	prepare_new_game($post_data, $bbcode_on, $html_on, $smilies_on, $error_msg, $username, $opponent_name, $opponent_id, $bbcode_uid, $subject, $message, $poll_title, $poll_options, $poll_length);
	if ( $error_msg == '' )
	{
		submit_new_game($post_data, $return_message, $return_meta, $forum_id, $topic_id, $post_id, $poll_id, $topic_type, $bbcode_on, $html_on, $smilies_on, $attach_sig, $bbcode_uid, str_replace("\'", "''", $username), $challenger, $challenger_points, str_replace("\'", "''", $opponent_name), $opponent_id, $opponent_points, str_replace("\'", "''", $subject), str_replace("\'", "''", $message), str_replace("\'", "''", $poll_title), $poll_options, $poll_length);
		make_post_autom($post_id);
		// Notify opponent of new challenge by PM and EMAIL him of this new PM
		$user_id = $challenger;
		notify_opponent($opponent_name);
		notify_opponent($username);
		$temp_mode = "newtopic";
		update_post_stats($temp_mode, $post_data, $forum_id, $topic_id, $post_id, $user_id);
		user_notification($temp_mode, $post_data, $post_info['topic_title'], $forum_id, $topic_id, $post_id, $notify_user);
		//force subscription to thread
		//force subscription to thread
		$sql = "INSERT INTO `grand_topics_watch`
			( `topic_id` , `user_id` , `notify_status` )
			VALUES ('".$topic_id."', '".$user_id."', '1')";
		if ( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_MESSAGE, "Couldn't subscribe to topic.  Notify the admins. Include the following:<br /><code>".mysql_error()."</code>" );
		}
		$db->sql_freeresult($result);
		$sql = "INSERT INTO `grand_topics_watch`
			( `topic_id` , `user_id` , `notify_status` )
			VALUES ('".$topic_id."', '".$opponent_id."', '1')";
		if ( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_MESSAGE, "Couldn't subscribe to topic.  Notify the admins. Include the following:<br /><code>".mysql_error()."</code>" );
		}
		$db->sql_freeresult($result);
		
		
		// Change cookies
		$tracking_topics = ( !empty($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_t']) ) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_t']) : array();
		$tracking_forums = ( !empty($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f']) ) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f']) : array();
		if ( count($tracking_topics) + count($tracking_forums) == 100 && empty($tracking_topics[$topic_id]) )
		{
			asort($tracking_topics);
			unset($tracking_topics[key($tracking_topics)]);
		}
		$tracking_topics[$topic_id] = time();
		setcookie($board_config['cookie_name'] . '_t', serialize($tracking_topics), 0, $board_config['cookie_path'], $board_config['cookie_domain'], $board_config['cookie_secure']);
		// Show message with link to topic after posting new game
		$template->assign_vars(array(
			'META' => $return_meta
			)
		);
		message_die(GENERAL_MESSAGE, $return_message);
	}
	else if ( $error_msg != '' )
	{
		// Show error page if there's an error
		$template->set_filenames(array(
			'reg_header' => 'error_body.tpl'
			)
		);
		$template->assign_vars(array(
			'ERROR_MESSAGE' => $error_msg
			)
		);
		$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
	}
} else {
	echo "No match!  We couldn't find one of the email addresses that you listed.";
	
	$subject = "Someone wants to record a fight in the Hall of Records!";
	$body = "Hey! I want to record a fight we've had using the GT League Hall of Records ( http://www.thegrandtournament.com ).  Can you please register so we can both track our fighting records using this official system?";
	$header = "From: ". $from_email . "\r\n";
	
	mail($to_email, $subject, $body, $header);
	
}
?>