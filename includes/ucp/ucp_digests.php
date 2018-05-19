<?php
/** 
*
* @package ucp
* @version $Id: v3_modules.xml 52 2007-12-09 19:45:45Z jelly_doughnut $
* @copyright (c) 2007 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
* Written by Mark D. Hamill, mhamill@computer.org, http://phpbbservices.com
* This software is designed to work with phpBB Version 3.0.3.

* This is the digest user interface for the Digests mod.
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
			
/**
* @package ucp
*/
class ucp_digests
{
	var $u_action;
					
	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpEx;
					
		// Attach the language file
		$user->add_lang('mods/ucp_digests');
							
		// Set up the page
		$this->tpl_name 	= 'ucp_digests';
		
		// There are four modes for the Digest user interface. By chunking the user interface into relatively
		// small screens it is not so intimidating.
		switch ($mode)
		{
			case 'basics':
				$this->page_title 	= $user->lang['UCP_DIGESTS_BASICS'];
				break;
			case 'posts_selection':
				$this->page_title 	= $user->lang['UCP_DIGESTS_POSTS_SELECTION'];
				break;
			case 'post_filters':
				$this->page_title 	= $user->lang['UCP_DIGESTS_POST_FILTERS'];
				break;
			case 'additional_criteria':
				$this->page_title 	= $user->lang['UCP_DIGESTS_ADDITIONAL_CRITERIA'];
				break;
			default:
				trigger_error(sprintf($user->lang['UCP_DIGESTS_MODE_ERROR'], $mode));
				break;
		}
							 
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
		
			// Handle the form processing by storing digest settings
			
			$sql = 'UPDATE ' . USERS_TABLE . ' SET %s WHERE user_id = ' . $user->data['user_id'];
			switch ($mode)
			{

				case 'basics':
				
					// Note: user_digest_send_hour_gmt is stored in UTC and translated to local time (as set in the profile). 
					// This is different than in phpBB 2, when all times were stored in server time.
					$local_send_hour = intval($_POST['send_hour']) - ($user->data['user_timezone'] + $user->data['user_dst']);
					if ($local_send_hour >= 24)
					{
						$local_send_hour = $local_send_hour - 24;
					}
					else if ($local_send_hour < 0)
					{
						$local_send_hour = $local_send_hour + 24;
					}
					$set_stmt = "user_digest_type = '" . htmlspecialchars($_POST['digest_type']) . "', user_digest_format = '" . htmlspecialchars($_POST['style']) . "', user_digest_send_hour_gmt = " . $local_send_hour;
					
				break;
					
				case 'posts_selection':
				
					$set_stmt = "user_digest_filter_type = '" . htmlspecialchars($_POST['filtertype']) . "'";
					
				break;

				case 'post_filters':
				
					if (isset($_POST['mark_read']))
					{
						$mark_read = (htmlspecialchars($_POST['mark_read']) == 'on') ? 1 : 0;
					}
					else
					{
						$mark_read = 0;
					}
					$set_stmt = 'user_digest_max_posts = ' . intval($_POST['count_limit']) . ', user_digest_min_words = ' . intval($_POST['min_word_size']) . ', user_digest_new_posts_only = ' . intval($_POST['new_posts']) . ', user_digest_show_mine = ' . intval($_POST['show_mine']) . ', user_digest_show_mine = ' . intval($_POST['show_mine']) . ', user_digest_remove_foes = ' . intval($_POST['filter_foes']) . ', user_digest_show_pms = ' . intval($_POST['pms']) . ', user_digest_pm_mark_read = ' . $mark_read;
				break;
					
				case 'additional_criteria':
				
					$set_stmt = "user_digest_sortby = '" . htmlspecialchars($_POST['sort_by']) . "', user_digest_max_display_words = " . intval($_POST['max_word_size']) . ', user_digest_send_on_no_posts = ' . intval($_POST['send_on_no_posts'])  . ', user_digest_reset_lastvisit = ' . intval($_POST['lastvisit']);
					
				break;
					
				default:
				
					trigger_error(sprintf($user->lang['UCP_DIGESTS_MODE_ERROR'], $mode));
					
				break;
					
			}
			
			// Update the user's digest settings
			$sql = sprintf($sql, $set_stmt);
			$result = $db->sql_query($sql);
			
			// If no subscription is desired, remove any individual forum subscriptions and save some disk space!
			if (($mode == 'basics') && (htmlspecialchars($_POST['digest_type']) == DIGEST_NONE_VALUE))
			{
				$sql = 'DELETE FROM ' . DIGESTS_SUBSCRIBED_FORUMS_TABLE . ' 
						WHERE user_id = ' . $user->data['user_id'];
				$result = $db->sql_query($sql);
			}
			
			if ($mode == 'posts_selection')
			{
				// If there are any individual forum subscriptions, remove the old ones and create the new ones
				$sql = 'DELETE FROM ' . DIGESTS_SUBSCRIBED_FORUMS_TABLE . ' 
						WHERE user_id = ' . $user->data['user_id'];
				$result = $db->sql_query($sql);

				// Note that if "all_forums" is unchecked and bookmarks are disabled, there are individual forum subscriptions, so they must be saved.
				$all_forums = isset($_POST['all_forums']) ? htmlspecialchars($_POST['all_forums']) : '';
				$digest_type = isset($_POST['digest_type']) ? htmlspecialchars($_POST['digest_type']) : '';
				if (($all_forums !== 'on') && (trim($digest_type) !== DIGEST_BOOKMARKS)) 
				{
					foreach ($_POST as $key => $value) 
					{
						if (substr(htmlspecialchars($key), 0, 4) == 'elt_') 
						{
							$forum_id = substr(htmlspecialchars($key), 4, strpos($key, '_', 4) - 4);
							$sql = 'INSERT INTO ' . DIGESTS_SUBSCRIBED_FORUMS_TABLE . ' (user_id, forum_id) 
									VALUES (' .	intval($user->data['user_id']) . ', ' . intval($forum_id) . ')'; 
							$result = $db->sql_query($sql);
						}
					}
				}
			}
					
			// Generate confirmation page. It will redirect back to the calling page
			meta_refresh(3, $this->u_action);
			$message = $user->lang['DIGEST_UPDATED'] . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
			trigger_error($message);
		}
		else
		{

			// Don't show submit or reset buttons if there is no digest subscription
			$show_buttons = ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? false : true;
			if ($mode == 'basics')
			{
				$show_buttons = true; // Buttons must appear in basics mode otherwise there is no way to resubscribe
			}
		
			// These template variables are used on all the pages
			$template->assign_vars(array(
				'SHOW_BUTTONS'	=> ($show_buttons) ? 'T' : 'F',
				'S_DIGEST_HOME'			=> $config['digests_digests_title'],
				'S_DIGEST_PAGE_URL'		=> $config['digests_page_url'],
				'S_DIGEST_VERSION'		=> $config['digests_version'],
				// When the form is submitted it needs to redirect back to this program
				'U_ACTION'  => $user->page['page'],
			));
			
			// If the user is an administrator, show latest version information at the bottom of the page
			if ($user->data['user_type'] == USER_FOUNDER)
			{
			
				// Check for new version
				include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
				$latest_version = get_remote_file($config['digests_host'], '/digests/updatecheck', 'version_phpBB3.txt', $errstr, $errno);
			
				if ($latest_version === false)
				{
					if ($errstr)
					{
						$version_info = '<span style="color:red">' . sprintf($user->lang['DIGEST_CONNECT_SOCKET_ERROR'], $errstr) . '</span>';
					}
					else
					{
						$version_info = '<span>' . $user->lang['DIGEST_SOCKET_FUNCTIONS_DISABLED'] . '</span>';
					}
				}
				else
				{
					$latest_version = str_replace("\n",'.',$latest_version);
					$version_info = version_compare($latest_version, $config['digests_version']);
					if ($version_info)
					{
						$version_info = '<span style="color:red">' . sprintf($user->lang['DIGEST_VERSION_NOT_UP_TO_DATE'],$config['digests_page_url']);
						$version_info .= ' ' . sprintf($user->lang['DIGEST_LATEST_VERSION_INFO'], $latest_version) . ' ' . sprintf($user->lang['DIGEST_CURRENT_VERSION_INFO'], $config['digests_version']) . '</span>';
					}
					else
					{
						$version_info = '<span style="color:green">' . $user->lang['DIGEST_VERSION_UP_TO_DATE'] . '</span>';
					}
				}
			
				$template->assign_vars(array(
					'S_VERSION_INFO' => $version_info,
					'IS_ADMIN' => 'T'));
			}
			
			switch ($mode)
			{
			
				case 'basics':
					$this->page_title 	= $user->lang['UCP_DIGESTS_BASICS'];
					
					if ($user->data['user_digest_type'] == DIGEST_NONE_VALUE)
					{
						if ($config['digests_user_digest_send_hour_gmt'] == -1)
						{
							// Pick a random hour, since this is a new digest and the administrator requested this to even out digest server processing
							$local_send_hour = rand(0,23);
						}
						else
						{
							$local_send_hour = $config['digests_user_digest_send_hour_gmt'];
						}
					}
					else
					{
						// Transform user_digest_send_hour_gmt to local time
						$local_send_hour = $user->data['user_digest_send_hour_gmt'] + ($user->data['user_timezone'] + $user->data['user_dst']);
					}
					
					// Adjust time if outside of hour range
					if ($local_send_hour >= 24)
					{
						$local_send_hour = $local_send_hour - 24;
					}
					else if ($local_send_hour < 0)
					{
						$local_send_hour = $local_send_hour + 24;
					}
					
					// Set other form fields using board defaults if necessary, otherwise pull from the user's settings
					// Note, setting an administator configured default for digest type is a bad idea because
					// the user might think they have a digest subscription when they do not.
					
					if ($user->data['user_digest_type'] == DIGEST_NONE_VALUE)
					{
						$styling_html = ($config['digests_user_digest_format'] == DIGEST_HTML_VALUE) ? 'checked="checked"' : '';
						$styling_html_classic = ($config['digests_user_digest_format'] == DIGEST_HTML_CLASSIC_VALUE) ? 'checked="checked"' : '';
						$styling_plain = ($config['digests_user_digest_format'] == DIGEST_PLAIN_VALUE) ? 'checked="checked"' : '';
						$styling_plain_classic = ($config['digests_user_digest_format'] == DIGEST_PLAIN_CLASSIC_VALUE) ? 'checked="checked"' : '';
						$styling_text = ($config['digests_user_digest_format'] == DIGEST_TEXT_VALUE) ? 'checked="checked"' : '';
					}
					else
					{
						$styling_html = ($user->data['user_digest_format'] == DIGEST_HTML_VALUE) ? 'checked="checked"' : '';
						$styling_html_classic = ($user->data['user_digest_format'] == DIGEST_HTML_CLASSIC_VALUE) ? 'checked="checked"' : '';
						$styling_plain = ($user->data['user_digest_format'] == DIGEST_PLAIN_VALUE) ? 'checked="checked"' : '';
						$styling_plain_classic = ($user->data['user_digest_format'] == DIGEST_PLAIN_CLASSIC_VALUE) ? 'checked="checked"' : '';
						$styling_text = ($user->data['user_digest_format'] == DIGEST_TEXT_VALUE) ? 'checked="checked"' : '';
					}
					
					$template->assign_vars(array(
						'0_SELECTED'  					=> ($local_send_hour == 0) ? 'selected="selected"' : '',
						'1_SELECTED'  					=> ($local_send_hour == 1) ? 'selected="selected"' : '',
						'2_SELECTED'  					=> ($local_send_hour == 2) ? 'selected="selected"' : '',
						'3_SELECTED'  					=> ($local_send_hour == 3) ? 'selected="selected"' : '',
						'4_SELECTED'  					=> ($local_send_hour == 4) ? 'selected="selected"' : '',
						'5_SELECTED'  					=> ($local_send_hour == 5) ? 'selected="selected"' : '',
						'6_SELECTED'  					=> ($local_send_hour == 6) ? 'selected="selected"' : '',
						'7_SELECTED'  					=> ($local_send_hour == 7) ? 'selected="selected"' : '',
						'8_SELECTED'  					=> ($local_send_hour == 8) ? 'selected="selected"' : '',
						'9_SELECTED'  					=> ($local_send_hour == 9) ? 'selected="selected"' : '',
						'10_SELECTED'  					=> ($local_send_hour == 10) ? 'selected="selected"' : '',
						'11_SELECTED'  					=> ($local_send_hour == 11) ? 'selected="selected"' : '',
						'12_SELECTED'  					=> ($local_send_hour == 12) ? 'selected="selected"' : '',
						'13_SELECTED'  					=> ($local_send_hour == 13) ? 'selected="selected"' : '',
						'14_SELECTED'  					=> ($local_send_hour == 14) ? 'selected="selected"' : '',
						'15_SELECTED'  					=> ($local_send_hour == 15) ? 'selected="selected"' : '',
						'16_SELECTED'  					=> ($local_send_hour == 16) ? 'selected="selected"' : '',
						'17_SELECTED'  					=> ($local_send_hour == 17) ? 'selected="selected"' : '',
						'18_SELECTED'  					=> ($local_send_hour == 18) ? 'selected="selected"' : '',
						'19_SELECTED'  					=> ($local_send_hour == 19) ? 'selected="selected"' : '',
						'20_SELECTED'  					=> ($local_send_hour == 20) ? 'selected="selected"' : '',
						'21_SELECTED'  					=> ($local_send_hour == 21) ? 'selected="selected"' : '',
						'22_SELECTED'  					=> ($local_send_hour == 22) ? 'selected="selected"' : '',
						'23_SELECTED'  					=> ($local_send_hour == 23) ? 'selected="selected"' : '',
						'BASICS'						=> 'T',
						'DIGEST_DAY_CHECKED' 			=> ($user->data['user_digest_type'] == DIGEST_DAILY_VALUE) ? 'checked="checked"' : '',
						'DIGEST_HTML_CHECKED' 			=> $styling_html,
						'DIGEST_HTML_CLASSIC_CHECKED' 	=> $styling_html_classic,
						'DIGEST_NONE_CHECKED' 			=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'checked="checked"' : '',
						'DIGEST_PLAIN_CHECKED' 			=> $styling_plain,
						'DIGEST_PLAIN_CLASSIC_CHECKED' 	=> $styling_plain_classic,
						'DIGEST_TEXT_CHECKED' 			=> $styling_text,
						'DIGEST_WEEK_CHECKED' 			=> ($user->data['user_digest_type'] == DIGEST_WEEKLY_VALUE) ? 'checked="checked"' : '',
						'L_DIGEST_12AM'					=> $user->lang['DIGEST_HOUR'][0],
						'L_DIGEST_1AM'					=> $user->lang['DIGEST_HOUR'][1],
						'L_DIGEST_2AM'					=> $user->lang['DIGEST_HOUR'][2],
						'L_DIGEST_3AM'					=> $user->lang['DIGEST_HOUR'][3],
						'L_DIGEST_4AM'					=> $user->lang['DIGEST_HOUR'][4],
						'L_DIGEST_5AM'					=> $user->lang['DIGEST_HOUR'][5],
						'L_DIGEST_6AM'					=> $user->lang['DIGEST_HOUR'][6],
						'L_DIGEST_7AM'					=> $user->lang['DIGEST_HOUR'][7],
						'L_DIGEST_8AM'					=> $user->lang['DIGEST_HOUR'][8],
						'L_DIGEST_9AM'					=> $user->lang['DIGEST_HOUR'][9],
						'L_DIGEST_10AM'					=> $user->lang['DIGEST_HOUR'][10],
						'L_DIGEST_11AM'					=> $user->lang['DIGEST_HOUR'][11],
						'L_DIGEST_12PM'					=> $user->lang['DIGEST_HOUR'][12],
						'L_DIGEST_1PM'					=> $user->lang['DIGEST_HOUR'][13],
						'L_DIGEST_2PM'					=> $user->lang['DIGEST_HOUR'][14],
						'L_DIGEST_3PM'					=> $user->lang['DIGEST_HOUR'][15],
						'L_DIGEST_4PM'					=> $user->lang['DIGEST_HOUR'][16],
						'L_DIGEST_5PM'					=> $user->lang['DIGEST_HOUR'][17],
						'L_DIGEST_6PM'					=> $user->lang['DIGEST_HOUR'][18],
						'L_DIGEST_7PM'					=> $user->lang['DIGEST_HOUR'][19],
						'L_DIGEST_8PM'					=> $user->lang['DIGEST_HOUR'][20],
						'L_DIGEST_9PM'					=> $user->lang['DIGEST_HOUR'][21],
						'L_DIGEST_10PM'					=> $user->lang['DIGEST_HOUR'][22],
						'L_DIGEST_11PM'					=> $user->lang['DIGEST_HOUR'][23],
						'L_DIGEST_FREQUENCY_EXPLAIN'	=> sprintf($user->lang['DIGEST_FREQUENCY_EXPLAIN'], $user->lang['DIGEST_WEEKDAY'][$config['digests_weekly_digest_day']]),
						'L_DIGEST_HTML_VALUE'			=> DIGEST_HTML_VALUE,
						'L_DIGEST_HTML_CLASSIC_VALUE'	=> DIGEST_HTML_CLASSIC_VALUE,
						'L_DIGEST_PLAIN_VALUE'			=> DIGEST_PLAIN_VALUE,
						'L_DIGEST_PLAIN_CLASSIC_VALUE'	=> DIGEST_PLAIN_CLASSIC_VALUE,
						'L_DIGEST_TEXT_VALUE'			=> DIGEST_TEXT_VALUE,
						'S_DIGEST_TITLE'				=> $user->lang['UCP_DIGESTS_BASICS'],
						)
					);
					
				break;
					
				case 'posts_selection':
				
					$this->page_title 	= $user->lang['UCP_DIGESTS_POSTS_SELECTION'];

					// Individual forum checkboxes should be disabled if bookmarks are requested/expected
					if ((($user->data['user_digest_type'] == DIGEST_NONE_VALUE) && ($config['digests_user_digest_filter_type'] == DIGEST_BOOKMARKS)) ||
						(($user->data['user_digest_type'] != DIGEST_NONE_VALUE) && ($user->data['user_digest_filter_type'] == DIGEST_BOOKMARKS)))
					{
						$disabled = true;
					}
					else
					{
						$disabled = false;
					}

					// Get current subscribed forums for this user, if any. If none, all allowed forums are assumed
					$rowset = array();
					$sql = 'SELECT forum_id 
							FROM ' . DIGESTS_SUBSCRIBED_FORUMS_TABLE . ' 
							WHERE user_id = ' . $user->data['user_id'];
					$result = $db->sql_query($sql);
					$rowset = $db->sql_fetchrowset($result);

					$all_by_default = (sizeof($rowset) == 0) ? true : false;

					$forum_read_ary = array();
					$allowed_forums = array();
					
					$forum_read_ary = $auth->acl_getf('f_read');
					
					// Get a list of parent_ids for each forum and put them in an array.
					$parent_array = array();
					$sql = 'SELECT forum_id, parent_id 
						FROM ' . FORUMS_TABLE . '
						ORDER BY 1';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
					{
						$parent_array[$row['forum_id']] = $row['parent_id'];
					}

					foreach ($forum_read_ary as $forum_id => $allowed)
					{
						if ($allowed['f_read'])
						{
							// Since this user has read access to this forum, add it to the $allowed_forums array
							$allowed_forums[] = (int) $forum_id;
							
							// Also add to $allowed_forums the parents, if any, of this forum. Actually we have to find the parent's parents, etc., going up as far as necesary because 
							// $auth->act_getf does not return the parents for which the user has access, yet parents must be shown are on the interface
							$there_are_parents = true;
							$this_forum_id = (int) $forum_id;
							
							while ($there_are_parents)
							{
								if ($parent_array[$this_forum_id] == 0)
								{
									$there_are_parents = false;
								}
								else
								{
									// Do not add this parent to the list of allowed forums if it is already in the array
									if (!in_array((int) $parent_array[$this_forum_id], $allowed_forums))
									{
										$allowed_forums[] = (int) $parent_array[$this_forum_id];
									} 
									$this_forum_id = (int) $parent_array[$this_forum_id];	// Keep looping...
								}
							}
						}
					}
					
					// Get a list of forums as they appear on the main index for this user. For presentation purposes indent them so they show the natural phpBB3 hierarchy.
					// Indenting is cleverly handled by nesting <div> tags inside of other <div> tags, and the template defines the relative offset (20 pixels).
					
					if (sizeof($allowed_forums) > 0)
					
					{
					
						// Set a flag in case no forums should be checked
						$uncheck = ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) && ($config['digests_user_check_all_forums'] == '0');
					
						$sql = 'SELECT forum_name, forum_id, parent_id, forum_type
								FROM ' . FORUMS_TABLE . ' 
								WHERE ' . $db->sql_in_set('forum_id', $allowed_forums) . ' AND forum_type != ' . FORUM_LINK . '
								ORDER BY left_id ASC';
						$result = $db->sql_query($sql);
						
						$template->assign_block_vars('show_forums', array());
						
						$current_level = 0;			// How deeply nested are we at the moment
						$parent_stack = array();	// Holds a stack showing the current parent_id of the forum
						$parent_stack[] = 0;		// 0, the first value in the stack, represents the <div_0> element, a container holding all the categories and forums in the template
						
						while ($row = $db->sql_fetchrow($result))
						{
						
							if ((int) $row['parent_id'] != (int) end($parent_stack) || (end($parent_stack) == 0))
							{
								if (in_array($row['parent_id'],$parent_stack))
								{
									// If parent is in the stack, then pop the stack until the parent is found, otherwise push stack adding the current parent. This creates a </div>
									while ((int) $row['parent_id'] != (int) end($parent_stack))
									{
										array_pop($parent_stack);
										$current_level--;
										// Need to close a category level here
										$template->assign_block_vars('forums', array( 
											'DIV_OPEN_CLOSE' 	=> 'C',
											'PRINT' 			=> '0'));
									}
								}
								else
								{
									// If the parent is not in the stack, then push the parent_id on the stack. This is also a trigger to indent the block. This creates a <div>
									array_push($parent_stack, (int) $row['parent_id']);
									$current_level++;
									// Need to add a category level here
									$template->assign_block_vars('forums', array( 
										'DIV_OPEN_CLOSE' 	=> 'O',
										'CAT_ID' 			=> 'div_' . $row['parent_id'],
										'PRINT' 			=> '0'));
								}
							}
							
							// This code prints the forum or category, which will exist inside the previously created <div> block
							
							// Check this forum's checkbox? Only if they have forum subscriptions
							if (!$all_by_default)
							{
								$check = false;
								foreach($rowset as $this_row)
								{
									if ($this_row['forum_id'] == $row['forum_id'])
									{
										$check = true;
										break;
									}
								}
							}
							else
							{
								$check = true;
							}
							
							// Let's make the check logic more complicated. If "All Forums" is unchecked and there is no digest subscription
							// then we must make sure every forum is also unchecked. Also need to uncheck if bookmarks are turned on
							if ($check || $all_by_default)
							{
								$check = true;
							}
								
							$template->assign_block_vars('forums', array( 
								'DIV_OPEN_CLOSE' 	=> 'X',
								'FORUM_NAME' 		=> 'elt_' . (int) $row['forum_id'] . '_' . (int) $row['parent_id'],
								'FORUM_LABEL' 		=> $row['forum_name'],
								'PRINT' 			=> '1',
								'FORUM_SUBSCRIBED' 	=> ($check) ? 'checked="checked"' : '',
								'FORUM_DISABLED' 	=> ($disabled || $user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
								'IS_FORUM' 			=> ($row['forum_type'] == FORUM_CAT) ? '0' : '1'));								;
							
						}
					
						$db->sql_freeresult($result);
						
						// Now out of the loop, it is important to remember to close any open <div> tags. Typically there is at least one.
						while ((int) $row['parent_id'] != (int) end($parent_stack))
						{
							array_pop($parent_stack);
							$current_level--;
							// Need to close the <div> tag
							$template->assign_block_vars('forums', array( 
								'DIV_OPEN_CLOSE' 	=> 'C',
								'PRINT' 			=> '0'));
						}
						
						$template->assign_vars(array(
							'ALL_BY_DEFAULT'			=> ($all_by_default) ? 'checked="checked"' : '',
							'DIGEST_NO_FORUMS_CHECKED'	=> $user->lang['DIGEST_NO_FORUMS_CHECKED'],
							'DIGEST_POST_ANY'			=> ($user->data['user_digest_filter_type'] == DIGEST_ALL) ? 'checked="checked"' : '',
							'DIGEST_POST_BM'			=> ($user->data['user_digest_filter_type'] == DIGEST_BOOKMARKS) ? 'checked="checked"' : '',
							'DIGEST_POST_FIRST'			=> ($user->data['user_digest_filter_type'] == DIGEST_FIRST) ? 'checked="checked"' : '',
							'DISABLED'					=> ($disabled || $user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
							'NO_FORUMS' 				=> 'F', 
							'POSTS_SELECTION'			=> 'T',
							'S_DIGEST_TITLE'			=> $user->lang['UCP_DIGESTS_POSTS_SELECTION'],
							'CONTROL_DISABLED' 			=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
							'DISABLED_MESSAGE' 			=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? '<i>' . $user->lang['DIGEST_DISABLED_MESSAGE'] . '</i>' : ''
							)
						);
					}
						
					else
						
					{
						// No forums to show!
						$template->assign_vars(array(
							'POSTS_SELECTION'		=> 'T',
							'NO_FORUMS' 			=> 'T', 
							'L_NO_FORUMS_MESSAGE' 	=>  $user->lang['DIGEST_NO_FORUMS_AVAILABLE']));
					}				
				
				break;
					
				case 'post_filters':

					if ($config['digests_max_items'] > 0)
					{
						$max_posts = min($user->data['user_digest_max_posts'], $config['digests_max_items']);
					}
					else
					{
						$max_posts = $user->data['user_digest_max_posts'];
					}
					$this->page_title 	= $user->lang['UCP_DIGESTS_POST_FILTERS'];
					$template->assign_vars(array(
						'COUNT_LIMIT_DISABLED' 						=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
						'DIGEST_MARK_READ_CHECKED' 					=> ($user->data['user_digest_pm_mark_read'] == 1) ? 'checked="checked"' : '',
						'DIGEST_MARK_READ_DISABLED' 				=> ($user->data['user_digest_show_pms'] == 0 || $user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
						'DIGEST_MIN_SIZE_DISABLED' 					=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
						'DIGEST_NEW_POSTS_ONLY_CHECKED_NO' 			=> ($user->data['user_digest_new_posts_only'] == 0) ? 'checked="checked"' : '',
						'DIGEST_NEW_POSTS_ONLY_CHECKED_YES' 		=> ($user->data['user_digest_new_posts_only'] == 1) ? 'checked="checked"' : '',
						'DIGEST_FILTER_FOES_CHECKED_NO' 			=> ($user->data['user_digest_remove_foes'] == 0) ? 'checked="checked"' : '',
						'DIGEST_FILTER_FOES_CHECKED_YES' 			=> ($user->data['user_digest_remove_foes'] == 1) ? 'checked="checked"' : '',
						'DIGEST_PRIVATE_MESSAGES_IN_DIGEST_NO' 		=> ($user->data['user_digest_show_pms'] == 0) ? 'checked="checked"' : '',
						'DIGEST_PRIVATE_MESSAGES_IN_DIGEST_YES' 	=> ($user->data['user_digest_show_pms'] == 1) ? 'checked="checked"' : '',
						'DIGEST_REMOVE_YOURS_CHECKED_NO' 			=> ($user->data['user_digest_show_mine'] == 1) ? 'checked="checked"' : '',
						'DIGEST_REMOVE_YOURS_CHECKED_YES' 			=> ($user->data['user_digest_show_mine'] == 0) ? 'checked="checked"' : '',
						'DISABLED'									=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
						'DISABLED_MESSAGE' 							=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? '<i>' . $user->lang['DIGEST_DISABLED_MESSAGE'] . '</i>' : '',
						'L_DIGEST_COUNT_LIMIT_EXPLAIN'				=> sprintf($user->lang['DIGEST_SIZE_ERROR'],$config['digests_max_items']),
						'POST_FILTERS'								=> 'T',
						'S_DIGEST_MAX_ITEMS' 						=> $max_posts,
						'S_DIGEST_MIN_SIZE' 						=> ($user->data['user_digest_min_words'] == 0) ? '' : $user->data['user_digest_min_words'],
						'S_DIGEST_TITLE'							=> $user->lang['UCP_DIGESTS_POST_FILTERS'],
						)
					);
					
				break;
					
				case 'additional_criteria':
					$this->page_title 	= $user->lang['UCP_DIGESTS_ADDITIONAL_CRITERIA'];
					$template->assign_vars(array(
						'ADDITIONAL_CRITERIA'					=> 'T',
						'BOARD_SELECTED' 						=> ($user->data['user_digest_sortby'] == DIGEST_SORTBY_BOARD) ? 'selected="selected"' : '',
						'DIGEST_MAX_SIZE' 						=> ($user->data['user_digest_max_display_words'] == 0) ? '' : $user->data['user_digest_max_display_words'],
						'DIGEST_SEND_ON_NO_POSTS_NO_CHECKED' 	=> ($user->data['user_digest_send_on_no_posts'] == 0) ? 'checked="checked"' : '',
						'DIGEST_SEND_ON_NO_POSTS_YES_CHECKED' 	=> ($user->data['user_digest_send_on_no_posts'] == 1) ? 'checked="checked"' : '',
						'DISABLED'								=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? 'disabled="disabled"' : '',
						'DISABLED_MESSAGE' 						=> ($user->data['user_digest_type'] == DIGEST_NONE_VALUE) ? '<i>' . $user->lang['DIGEST_DISABLED_MESSAGE'] . '</i>' : '',
						'LASTVISIT_NO_CHECKED' 					=> ($user->data['user_digest_reset_lastvisit'] == 0) ? 'checked="checked"' : '',
						'LASTVISIT_YES_CHECKED' 				=> ($user->data['user_digest_reset_lastvisit'] == 1) ? 'checked="checked"' : '',
						'POSTDATE_DESC_SELECTED' 				=> ($user->data['user_digest_sortby'] == DIGEST_SORTBY_POSTDATE_DESC) ? 'selected="selected"' : '',
						'POSTDATE_SELECTED' 					=> ($user->data['user_digest_sortby'] == DIGEST_SORTBY_POSTDATE) ? 'selected="selected"' : '',
						'STANDARD_DESC_SELECTED' 				=> ($user->data['user_digest_sortby'] == DIGEST_SORTBY_STANDARD_DESC) ? 'selected="selected"' : '',
						'STANDARD_SELECTED' 					=> ($user->data['user_digest_sortby'] == DIGEST_SORTBY_STANDARD) ? 'selected="selected"' : '',
						'S_DIGEST_TITLE'						=> $user->lang['UCP_DIGESTS_ADDITIONAL_CRITERIA'],
						)
					);
					
				break;
					
				default:
					trigger_error(sprintf($user->lang['UCP_DIGESTS_MODE_ERROR'], $mode));
				break;
					
			}
			
		}
	}
}
			
?>