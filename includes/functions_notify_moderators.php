<?php
/** 
*
* @package notify_moderators
* @version $Id: 1.0.2
* @copyright (c) 2008 david63
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// Some of the code for this function has been "borrowed" from the phpbb3 function user_notification()
function notify_moderators($mode, $process_notify_id, $notify_user_id = 0, $disapprove_reason = '')
{
	global $db, $user, $config, $phpbb_root_path, $phpEx, $auth;

	// Do we need to notify anyone? - if not let's go back!
	if (!$config['notify_admin'] && !$config['notify_moderator'] && !$config['notify_group'])
	{
		return;
	}

	// Set some variables
	$process_notify = false;
	$is_global = false;
	$sql_notify = '';
	$forum_sql = '';

	// First thing we need to do is find out if we are dealing with a Global post
	switch ($mode)
	{
		case 'report':
		case 'close':
		case 'delete':
			$from_sql = 'FROM ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p, ' . REPORTS_TABLE . ' r ';
			$and_sql = ' AND r.post_id = p.post_id';
			$in_set_sql = $db->sql_in_set('r.report_id', $process_notify_id);
		break;

		default:
			$from_sql = 'FROM ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p ';
			$and_sql = '';
			$in_set_sql = $db->sql_in_set('p.post_id', $process_notify_id);
		break;
	}

	$sql = 'SELECT t.topic_type ' .
		$from_sql . '
		WHERE t.topic_id = p.topic_id ' .
			$and_sql . '
			AND ' . $in_set_sql;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);

	$is_global = ($row['topic_type'] == POST_GLOBAL) ? true : false;
	$db->sql_freeresult($result);

	if(!$is_global)
	{
		$forum_sql = 'AND p.forum_id = f.forum_id';
	}
	// End of Global

	// Now we can start
	switch ($mode)
	{
		// Get the report data
		case 'report':
		case 'close':
		case 'delete':
			if ($config['notify_report'] || $config['notify_close'])
			{
				$sql = 'SELECT r.report_id, r.user_id, r.report_closed, r.report_text, rr.reason_description, u.username, f.forum_id, f.forum_name, p.post_id, p.post_subject, p.post_approved, p.bbcode_bitfield, p.bbcode_uid, t.topic_title
					FROM ' . REPORTS_TABLE . ' r, ' . REPORTS_REASONS_TABLE . ' rr, ' . POSTS_TABLE . ' p, ' . FORUMS_TABLE . ' f, ' . TOPICS_TABLE . ' t, ' . USERS_TABLE . ' u
					WHERE ' . $db->sql_in_set('r.report_id', $process_notify_id) . '
						AND r.reason_id = rr.reason_id
						AND r.user_id = u.user_id
						AND r.post_id = p.post_id ' .
						$forum_sql . '
						AND p.topic_id = t.topic_id';
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);

				$notify_ary = array(
					'reason'			=> htmlspecialchars_decode($row['reason_description']),
					'report_id'			=> $row['report_id'],
					'report_text'		=> htmlspecialchars_decode($row['report_text']),
					'report_closed'		=> $row['report_closed'],
					'post_id'			=> $row['post_id'],
					'post_subject'		=> htmlspecialchars_decode($row['post_subject']),
					'bbcode_bitfield'	=> $row['bbcode_bitfield'],
					'bbcode_uid'		=> $row['bbcode_uid'],
					'topic_title'		=> htmlspecialchars_decode($row['topic_title']),
					'post_text'			=> '',
				);

				if($is_global)
				{
					$notify_ary['forum_id'] = 0;
					$notify_ary['forum_name'] = $user->lang['POST_GLOBAL'];
				}
				else
				{
					$notify_ary['forum_id'] = $row['forum_id'];
					$notify_ary['forum_name'] = htmlspecialchars_decode($row['forum_name']);
				}

				switch ($mode)
				{
					case 'report':
						if ($config['notify_report'])
						{
							$notify_ary['reporter_name'] = $row['username'];
							$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['REPORT_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($row['topic_title']);
							$notify_ary['notify_template'] = 'notify_moderators_report';
							$notify_ary['pm_text'] = $user->lang['PM_REPORT'];
							$notify_ary['mod_name'] = '';
							$notify_ary['author_id'] = $row['user_id'];

							$db->sql_freeresult($result);

							// Do we ignore the user notification setting for sending a report?
							$sql_notify = ($config['notify_overide_report']) ? '' : ' AND u.notify_moderator > ' . NOTIFY_MOD_NONE;
							$process_notify = true;
						}
					break;

					case 'close':
					case 'delete':
						if ($config['notify_close'])
						{
							// Get who closed the report
							$sql = 'SELECT user_id, username
								FROM ' . USERS_TABLE . '
								WHERE ' .  $db->sql_in_set('user_id', $notify_user_id);
							$result = $db->sql_query($sql);
							$row = $db->sql_fetchrow($result);

							$notify_ary['mod_name'] = $row['username'];
							$notify_ary['notify_template'] = 'notify_moderators_report_close';
							$notify_ary['pm_text'] = $user->lang['PM_REPORT_CLOSE'];
							$notify_ary['reporter_name'] = '';
							$notify_ary['author_id'] = $row['user_id'];

							if ($mode == 'close')
							{
								$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['CLOSE_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($notify_ary['topic_title']);
							}
							else
							{
								$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['DELETE_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($notify_ary['topic_title']);
							}
							$db->sql_freeresult($result);

							$sql_notify = ' AND u.notify_moderator > ' . NOTIFY_MOD_NONE;
							// We do not want to send a message when a closed report is deleted
							$process_notify = ($notify_ary['report_closed'] == false) ? true : false;
						}
					break;
				}				
			}
		break;

		default:
			if (($mode == 'post' && $config['notify_post']) || (($mode == 'reply' || $mode == 'quote') && $config['notify_reply']) || ($mode == 'edit' && $config['notify_edit']) || $config['notify_approve'])
			{
				// Get the post data
				$sql = 'SELECT p.post_id, p.post_subject, p.post_text, p.post_approved, p.post_username, p.bbcode_bitfield, p.bbcode_uid, f.forum_id, f.forum_name, t.topic_title, t.topic_type, u.user_id, u.username
					FROM ' . POSTS_TABLE . ' p, ' . FORUMS_TABLE . ' f, ' . TOPICS_TABLE . ' t, ' . USERS_TABLE . ' u
					WHERE p.topic_id = t.topic_id
						AND p.poster_id = u.user_id ' .
						$forum_sql . '
						AND ' . $db->sql_in_set('p.post_id', $process_notify_id);
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);

				$notify_ary = array(
					'post_text'			=> htmlspecialchars_decode($row['post_text']), 
					'bbcode_bitfield'	=> $row['bbcode_bitfield'],
					'bbcode_uid'		=> $row['bbcode_uid'],
					'post_id'			=> $row['post_id'],
					'post_subject'		=> htmlspecialchars_decode($row['post_subject']),
					'topic_title'		=> htmlspecialchars_decode($row['topic_title']),
					'author_name'		=> ($row['user_id'] == ANONYMOUS && $row['post_username'] != '') ? $row['username'] . ' (' . $row['post_username'] . ')' : $row['username'],
					'await_moderation'	=> ($row['post_approved'] == 0) ? $user->lang['YES'] : $user->lang['NO'],
					'report_id'			=> 0,
				);

				switch ($mode)
				{
					case 'approve':
						$notify_ary['mode_name'] = '';
						$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['POST_APPROVE'] . ' : ' . htmlspecialchars_decode($row['topic_title']);
						$notify_ary['notify_template'] = 'notify_moderators_approve';
						$notify_ary['pm_text'] = $user->lang['PM_POST_APPROVE'];
					break;

					case 'disapprove':
						$notify_ary['mode_name'] = '';
						$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['POST_DISAPPROVE'] . ' : ' . htmlspecialchars_decode($row['topic_title']);
						$notify_ary['notify_template'] = 'notify_moderators_disapprove';
						$notify_ary['pm_text'] = $user->lang['PM_POST_DISAPPROVE'];
					break;
					
					case 'reply':
					case 'quote':
						$notify_ary['mode_name'] = $user->lang['POST_NOTIFY_REPLY'];
						$notify_ary['topic_subject'] = ($row['post_approved']) ? '[ ' . $config['sitename'] . ' ] - ' . $user->lang['REPLY_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($row['topic_title']) : '[ ' . $config['sitename'] . ' ] - ' . $user->lang['QUEUE_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($row['topic_title']);
						$notify_ary['notify_template'] = 'notify_moderators_post';
						$notify_ary['pm_text'] = $user->lang['PM_POST'];
						$notify_ary['mod_name'] = '';
						$notify_ary['author_id'] = $row['user_id'];
					break;

					case 'edit':
						$notify_ary['mode_name'] = $user->lang['POST_NOTIFY_EDIT'];
						$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['EDIT_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($row['topic_title']);
						$notify_ary['notify_template'] = 'notify_moderators_post';
						$notify_ary['pm_text'] = $user->lang['PM_POST'];
						$notify_ary['mod_name'] = '';
						$notify_ary['author_id'] = $notify_user_id;
					break;

					default:
						if ($row['post_approved'])
						{
							$notify_ary['mode_name'] = $user->lang['POST_NOTIFY_NEW'];
							$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['POST_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($row['topic_title']);
						}
						else
						{
							$notify_ary['mode_name'] = $user->lang['QUEUED_POST'];
							$notify_ary['topic_subject'] = '[ ' . $config['sitename'] . ' ] - ' . $user->lang['POST_QUEUE_NOTIFICATION'] . ' : ' . htmlspecialchars_decode($row['topic_title']);
						}
						$notify_ary['notify_template'] = 'notify_moderators_post';
						$notify_ary['pm_text'] = $user->lang['PM_POST'];
						$notify_ary['mod_name'] = '';
						$notify_ary['author_id'] = $row['user_id'];
					break;
				}

				switch ($row['topic_type'])
				{
					case POST_STICKY:
						$notify_ary['topic_type'] = $user->lang['POST_STICKY'];
					break;

					case POST_ANNOUNCE:
						$notify_ary['topic_type'] = $user->lang['POST_ANNOUNCEMENT'];
					break;

					case POST_GLOBAL:
						$notify_ary['topic_type'] = $user->lang['POST_GLOBAL'];
					break;

					case POST_NORMAL:
						$notify_ary['topic_type'] = $user->lang['POST'];
					break;

					default:
						$notify_ary['topic_type'] = $user->lang['POST_QUEUE'];
					break;
				}

				if($is_global)
				{
					$notify_ary['forum_id'] = 0;
					$notify_ary['forum_name'] = $user->lang['POST_GLOBAL'];
				}
				else
				{
					$notify_ary['forum_id'] = $row['forum_id'];
					$notify_ary['forum_name'] = htmlspecialchars_decode($row['forum_name']);
				}
				$db->sql_freeresult($result);

				// Who approved/disapproved the post?
				if ($config['notify_approve'] && ($mode == 'approve' || $mode == 'disapprove'))
				{
					$sql = 'SELECT user_id, username
						FROM ' . USERS_TABLE . '
						WHERE ' .  $db->sql_in_set('user_id', $notify_user_id);
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);

					$notify_ary['mod_name'] = $row['username'];
					$notify_ary['author_id'] = $row['user_id'];

					$db->sql_freeresult($result);
				}

				// Does the user want the notification?
				$sql_notify = ' AND u.notify_moderator > ' . NOTIFY_MOD_NONE;
				$process_notify = true;
			}
	} // End switch

	// We only need to carry on if there is some data to process
	if ($process_notify)
	{
		// Get banned User ID's
		// Just in case we have a banned moderator!!!!!
		$sql = 'SELECT ban_userid
			FROM ' . BANLIST_TABLE . '
			WHERE ban_userid <> 0
				AND ban_exclude <> 1';
		$result = $db->sql_query($sql);

		$sql_ignore_users = ANONYMOUS;
		while ($row = $db->sql_fetchrow($result))
		{
			$sql_ignore_users .= ', ' . (int) $row['ban_userid'];
		}
		$db->sql_freeresult($result);

		// Get board admins
		if ($config['notify_admin'])
		{	
			foreach ($auth->acl_get_list(false, array('a_'), false) as $temp => $admin_ary)
			{
				foreach ($admin_ary as $temp => $admin_ary)
				{
					foreach ($admin_ary as $admin_ary)
					{
						$mod_notify_ary[] = $admin_ary;
					}
				}
			}
		}

		// Globals do not have forum moderators
		if (!$is_global)
		{
			// Get the individual moderators for the forum
			if ($config['notify_moderator'] && $notify_ary['forum_id'] != 0)
			{
				$sql = 'SELECT u.user_id, u.user_type, u.username, u.user_email, u.user_jabber, u.user_lang, u.user_notify_type, u.notify_moderator
					FROM ' . USERS_TABLE . ' u, ' . MODERATOR_CACHE_TABLE . ' mc
						WHERE ' . $db->sql_in_set('mc.forum_id', $notify_ary['forum_id']) . '
							AND u.user_id = mc.user_id
							AND ' . $db->sql_in_set('u.user_id', $sql_ignore_users, true) . 
							$sql_notify;
				$result = $db->sql_query($sql);
		
				while ($row = $db->sql_fetchrow($result))
				{
					if ($row['user_type'] == USER_NORMAL)
					{
						$mod_notify_ary[] = $row['user_id'];
					}
				}
				$db->sql_freeresult($result);
			}

			// Get group moderators for the forum
			if ($config['notify_group'])
			{		
				foreach ($auth->acl_get_list(false, array('m_'), $notify_ary['forum_id']) as $temp => $mod_ary)
				{
					foreach ($mod_ary as $temp => $mod_ary)
					{
						foreach ($mod_ary as $mod_ary)
						{
							$mod_notify_ary[] = $mod_ary;
						}
					}
				}
			}
		}

		// Do we have any admins/moderators to process?
		if (sizeof($mod_notify_ary))
		{
			// Get admin/moderator details
			$sql = 'SELECT u.user_id, u.user_type, u.username, u.user_email, u.user_jabber, u.user_lang, u.user_notify_type, u.notify_moderator
				FROM ' . USERS_TABLE . ' u 
					WHERE ' . $db->sql_in_set('u.user_id', array_unique($mod_notify_ary)) . '
						AND ' . $db->sql_in_set('u.user_id', $sql_ignore_users, true) . 
						$sql_notify;
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$process_notify_rows[$row['user_id']] = array(
					'user_id'			=> $row['user_id'],
					'username'			=> $row['username'],
					'user_email'		=> $row['user_email'],
					'user_jabber'		=> $row['user_jabber'], 
					'user_lang'			=> $row['user_lang'], 
					'notify_type'		=> 'notify',
					'method'			=> $row['user_notify_type'], 
					'notify_moderator'	=> $row['notify_moderator'],
					'template'			=> $notify_ary['notify_template'],
					'allowed'			=> false
				);
			}
			$db->sql_freeresult($result);

			// Make sure Moderators/Admins are allowed to read the forum
			// This should not be a problem - but you never know!
			foreach ($auth->acl_get_list(array_keys($process_notify_rows), 'f_read', $notify_ary['forum_id']) as $forum_id => $forum_ary)
			{
				foreach ($forum_ary as $auth_option => $user_ary)
				{
					foreach ($user_ary as $user_id)
					{
						$process_notify_rows[$user_id]['allowed'] = true;
					}
				}
			}

			$msg_users = array();
			foreach ($process_notify_rows as $user_id => $row)
			{
				$msg_users[] = $row;
			}
			unset($process_notify_rows);

			// Now, we are ready to send out the notifications
			foreach ($msg_users as $row)
			{
				// Check to see if the moderator/admin is the author and do we need to send the message to the author
				if ($config['notify_own'] || ($row['user_id'] != $notify_ary['author_id']))
				{	
					// Process the message
					$msg_list_ary = array(); 
					$pos = (!isset($msg_list_ary[$row['template']])) ? 0 : sizeof($msg_list_ary[$row['template']]);

					$msg_list_ary[$row['template']][$pos]['method'] = $row['method'];
					$msg_list_ary[$row['template']][$pos]['email'] = $row['user_email'];
					$msg_list_ary[$row['template']][$pos]['jabber'] = $row['user_jabber'];
					$msg_list_ary[$row['template']][$pos]['user_id'] = $row['user_id'];
					$msg_list_ary[$row['template']][$pos]['name'] = $row['username'];
					$msg_list_ary[$row['template']][$pos]['lang'] = $row['user_lang'];

					$topic_url = generate_board_url() . '/viewtopic.' . $phpEx . '?f=' . $notify_ary['forum_id'] . '&p=' . $notify_ary['post_id'];
				
					$report_url = ($mode == 'delete') ? '' : $report_url = generate_board_url() . '/mcp.' . $phpEx . '?i=reports&mode=report_details&f=' . $notify_ary['forum_id'] . '&r=' . $notify_ary['report_id'];
					
					if ($config['notify_pm'] == true && $row['notify_moderator'] == NOTIFY_MOD_PM)
					{
						$send_user_pm = true;
					}
					else
					{
						$send_user_pm = false;
						$notify_ary['post_text'] = process_text($notify_ary['post_text']);
					}

					// Do not change the order of the items in these arrays otherwise the reports will break
					switch ($mode)
					{
						case 'report':
							$pm_array = array(
								'MODERATOR_NAME'	=> $row['username'],
								'FORUM_NAME'		=> $notify_ary['forum_name'],
								'REPORTER'			=> $notify_ary['reporter_name'],
								'TOPIC_TITLE'		=> $notify_ary['topic_title'],
								'U_TOPIC_URL'		=> $topic_url,
								'U_REPORT_URL'		=> $report_url,
								'POST_SUBJECT'		=> $notify_ary['post_subject'],
								'REASON'			=> $notify_ary['reason'],
								'REPORT_TEXT'		=> $notify_ary['report_text'],
								'EMAIL_SIG'			=> $config['board_email_sig'],
							);
						break;

						case 'close':
						case 'delete':
							$pm_array = array(
								'MODERATOR_NAME'	=> $row['username'],
								'FORUM_NAME'		=> $notify_ary['forum_name'],
								'MOD_NAME'			=> $notify_ary['mod_name'],
								'TOPIC_TITLE'		=> $notify_ary['topic_title'],
								'U_TOPIC_URL'		=> $topic_url,
								'U_REPORT_URL'		=> $report_url,
								'POST_SUBJECT'		=> $notify_ary['post_subject'],
								'REASON'			=> $notify_ary['reason'],
								'REPORT_TEXT'		=> $notify_ary['report_text'],
								'EMAIL_SIG'			=> $config['board_email_sig'],
							);
						break;

						case 'approve':
							$pm_array = array(
								'MODERATOR_NAME'	=> $row['username'],
								'FORUM_NAME'		=> $notify_ary['forum_name'],
								'MOD_NAME'			=> $notify_ary['mod_name'],
								'TOPIC_TITLE'		=> $notify_ary['topic_title'],
								'AUTHOR_NAME'		=> $notify_ary['author_name'],
								'U_TOPIC_URL'		=> $topic_url,
								'POST_SUBJECT'		=> $notify_ary['post_subject'],
								'TOPIC_TYPE'		=> $notify_ary['topic_type'],
								'MODERATION'		=> $notify_ary['await_moderation'],
								'POST_TEXT'			=> $notify_ary['post_text'],
								'EMAIL_SIG'			=> $config['board_email_sig'],
							);
						break;

						case 'disapprove':
							$pm_array = array(
								'MODERATOR_NAME'	=> $row['username'],
								'FORUM_NAME'		=> $notify_ary['forum_name'],
								'MOD_NAME'			=> $notify_ary['mod_name'],								
								'TOPIC_TITLE'		=> $notify_ary['topic_title'],
								'AUTHOR_NAME'		=> $notify_ary['author_name'],
								'REASON'			=> htmlspecialchars_decode($disapprove_reason),
								'U_TOPIC_URL'		=> $topic_url,
								'POST_SUBJECT'		=> $notify_ary['post_subject'],
								'TOPIC_TYPE'		=> $notify_ary['topic_type'],
								'MODERATION'		=> $notify_ary['await_moderation'],
								'POST_TEXT'			=> $notify_ary['post_text'],
								'EMAIL_SIG'			=> $config['board_email_sig'],
							);
						break;

						default:
							$pm_array = array(
								'MODERATOR_NAME'	=> $row['username'],
								'FORUM_NAME'		=> $notify_ary['forum_name'],
								'POST_TYPE'			=> $notify_ary['mode_name'],
								'TOPIC_TITLE'		=> $notify_ary['topic_title'],
								'AUTHOR_NAME'		=> $notify_ary['author_name'],
								'U_TOPIC_URL'		=> $topic_url,
								'POST_SUBJECT'		=> $notify_ary['post_subject'],
								'TOPIC_TYPE'		=> $notify_ary['topic_type'],
								'MODERATION'		=> $notify_ary['await_moderation'],
								'POST_TEXT'			=> $notify_ary['post_text'],
								'EMAIL_SIG'			=> $config['board_email_sig'],
							);
					}

					foreach ($msg_list_ary as $email_template => $email_list)
					{
						if ($send_user_pm)
						{
							// Get the details of the Admin from whom the PM notifications are being sent.
							$sql = 'SELECT u.user_id, u.username, u.user_ip
								FROM ' . USERS_TABLE . ' u
									WHERE ' . $db->sql_in_set('u.user_id', $config['notify_pm_admin']);
							$result = $db->sql_query($sql);

							$row = $db->sql_fetchrow($result);

							if ($row)
							{						
								if (!function_exists('submit_pm'))
								{
									include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
								}
								if (!class_exists('parse_message'))
								{
									include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);
								}

								foreach ($email_list as $addr)
								{
									$message_parser = new parse_message();
									$message_parser->message = vsprintf($notify_ary['pm_text'], $pm_array);
									$message_parser->parse(true, true, true, false, false, true, true); 						
					
									// PM data array to be sent to the PM functions
									$pm_data = array(
										'from_user_id'			=> $config['notify_pm_admin'],
										'from_user_ip'			=> $row['user_ip'],
										'from_username'			=> $row['username'],
										'icon_id'				=> 0,
										'enable_sig'			=> false,
										'enable_bbcode'			=> true,
										'enable_smilies'		=> true,
										'enable_urls'			=> true,
										'bbcode_bitfield'		=> $notify_ary['bbcode_bitfield'],
										'bbcode_uid'			=> $notify_ary['bbcode_uid'],
										'message'				=> $message_parser->message,
										'address_list'			=> array('u' => array($addr['user_id'] => 'to'))
									);

									submit_pm('post', $notify_ary['topic_subject'], $pm_data, false, false);
								}
							}
							else
							{
								// If we cannot send a PM then we will send it the "normal" way
								$send_user_pm = false;
							}
						}

						if (!$send_user_pm)
						{
							if (!class_exists('messenger'))
							{
								include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
							}
							$messenger = new messenger();

							foreach ($email_list as $addr)
							{								
								$template_data = array(
									'TOPIC_SUBJECT'		=> $notify_ary['topic_subject'],
									'MODERATOR_NAME'	=> $addr['name'],
								);

								$messenger->template($email_template, $addr['lang']);
								$messenger->to($addr['email'], $addr['name']);
								$messenger->im($addr['jabber'], $addr['name']);
								$messenger->assign_vars(array_merge($pm_array, $template_data));					
								$messenger->send($addr['method']);
							}
							$messenger->save_queue();
						}					
					}
				}
			}
		}
	}
	return;
}

function process_text($text)
{
	// We need to remove any special bbcodes and format the text so that it is readable in plain text
	$str_from = array('&#91;', '&#93;', '&#46;', '&#58;', '<!-- m -->', '<!-- w -->', '</a>', '<a class="postlink');
	$str_to = array('[', ']', '.', ':', '', '', '', '');

	$text = str_replace($str_from, $str_to, $text);
	$text = smiley_text($text, true);
	$text = str_replace("<br />", "\n", $text);
	$text = preg_replace('/\:[0-9a-z\:]+\]/si', ']', $text);

	return $text;
}

function get_notify_counts($mod_id, &$report_count, &$topic_count, &$post_count)
{
	global $auth, $db;

	$report_count	= 0;
	$topic_count	= 0;
	$post_count		= 0;
	$sql_mod_forums	= '';

	// Is this user an admin?
	foreach ($auth->acl_get_list(false, array('a_'), false) as $temp => $admin_ary)

	$is_admin = (in_array($mod_id, $admin_ary['a_'])) ? true : false;

	if ($is_admin)
	{
		// We need this for Globals
		$sql_mod_forums .= '0, '; 
		
		$sql = 'SELECT forum_id
			FROM ' . FORUMS_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$sql_mod_forums .= $row['forum_id'] . ', '; 
		}
	}
	else
	{
		// Get individual moderator forums
		$sql = 'SELECT forum_id
			FROM ' . MODERATOR_CACHE_TABLE . '
			WHERE ' . $db->sql_in_set('user_id', $mod_id);
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{			
			if (isset($row['forum_id']))
			{
				$sql_mod_forums .= $row['forum_id'] . ', '; 
			}
		}
		$db->sql_freeresult($result);

		// Get user's moderator group forums
		$sql = 'SELECT m.forum_id, ug.user_id
			FROM ' . MODERATOR_CACHE_TABLE . ' m, ' . USER_GROUP_TABLE . ' ug
			WHERE m.group_id = ug.group_id';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['user_id'] == $mod_id)
			{
				$sql_mod_forums .= $row['forum_id'] . ', '; 
			}
		}
	}
	$sql_mod_forums = substr($sql_mod_forums, 0, -2);

	// Do we have any forum moderators?
	if ($sql_mod_forums)
	{
		$sql = 'SELECT COUNT(r.report_id) AS total_reports
			FROM ' . REPORTS_TABLE . ' r, ' . POSTS_TABLE . " p
			WHERE r.post_id = p.post_id
				AND r.report_closed = 0
				AND p.forum_id IN ($sql_mod_forums)";
		$result = $db->sql_query($sql);
		$report_count = (int) $db->sql_fetchfield('total_reports');

		$sql = 'SELECT COUNT(t.topic_id) AS total_queued_topics
			FROM ' . TOPICS_TABLE . " t
			WHERE t.topic_approved = 0
				AND t.forum_id IN ($sql_mod_forums)";
		$result = $db->sql_query($sql);
		$topic_count = (int) $db->sql_fetchfield('total_queued_topics');

		$sql = 'SELECT COUNT(p.post_id) AS total_queued_posts
			FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . " t
			WHERE p.topic_id = t.topic_id
				AND p.post_approved = 0
				AND t.topic_approved = 1
				AND p.forum_id IN ($sql_mod_forums)";
		$result = $db->sql_query($sql);
		$post_count = (int) $db->sql_fetchfield('total_queued_posts');

		$db->sql_freeresult($result);
	}
}

?>