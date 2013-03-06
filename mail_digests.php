<?php
/** 
*
* @package phpBB3
* @version $Id: mail_digests.php, v 2.0.0 Production 2008/07/06 mark@phpbbservices.com Exp $
* @copyright (c) Mark D. Hamill (mhamill@computer.org, http://phpbbservices.com) 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/

// Written by Mark D. Hamill, mhamill@computer.org, http://phpbbservices.com
// This software is designed to work with phpBB Version 3.0.4.

// This is the e-mailing software for the Digests mod. It sends out daily or weekly
// digests based on settings created by users in the user control panel and stored in the
// phpbb_users table. It is typically called hourly by the operating system.

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("memory_limit", -1);

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx); // Used to send emails

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/ucp_digests');

// Set the log file end of line delimiter
switch ($config['digests_log_eol'])
{
	case DIGEST_HTML_VALUE:
		$eol = "<br />\n";
		break;
	case DIGEST_UNIX_VALUE:
		$eol = "\n";
		break;
	case DIGEST_WINDOWS_VALUE:
		$eol = "\r\n";
		break;
	case DIGEST_MAC_VALUE:
		$eol = "\r";
		break;
	default:
		echo sprintf($user->lang['DIGEST_BAD_EOL'], $config['digests_log_eol']);
		exit;		
}

// If the board is currently disabled, digests should also be disabled, don't ya think?
if ($config['board_disable'])
{
	write_log_entry($user->lang['DIGEST_BOARD_DISABLED'], true);
	exit;
}

// If the key parameter is enabled, validate the key parameter. If the parameter value does not match its required key, exit with an error.
if ($config['digests_require_key'])
{
	$supplied_key = (isset($_GET['key'])) ? htmlspecialchars($_GET['key']) : '';
	if (trim($supplied_key) != trim($config['digests_key_value']))
	{
		write_log_entry(sprintf($user->lang['DIGEST_BAD_KEY_VALUE'], $supplied_key, date($config['default_dateformat'], time())), true);
		exit;
	}
}

// Display a digest mail start processing message. It may get captured in a log
write_log_entry(sprintf($user->lang['DIGEST_START'],date($config['default_dateformat'])));
 
// Need a board URL since URLs pointing to the board need to be absolute URLs
$board_url = generate_board_url() . '/';

// Is today the day to run the weekly digest?
$weekday_gmt = gmdate('w'); // 0 = Sunday, 6 = Saturday
$current_hour_gmt = gmdate('G'); // 0 thru 23
$current_hour_gmt_plus_30 = gmdate('G') + .5; // 0 thru 23
if ($current_hour_gmt_plus_30 == 24)
{
	$current_hour_gmt_plus_30 = 0;	// A very unlikely situation
}

$weekly_digest_sql = ($weekday_gmt == $config['digests_weekly_digest_day']) ? " OR (user_digest_type = '" . DIGEST_WEEKLY_VALUE . "' AND (user_digest_send_hour_gmt = $current_hour_gmt OR user_digest_send_hour_gmt = $current_hour_gmt_plus_30))" : '';

// We need to know which auth_option_id corresponds to the forum read privilege (f_read), so let's do it here just once.
$sql = 'SELECT auth_option_id
		FROM ' . ACL_OPTIONS_TABLE . "
		WHERE auth_option = 'f_read'";
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$read_id = $row['auth_option_id'];

// We also need to know which auth_option_id corresponds to the forum list privilege (f_list), so let's do it here just once.
$sql = 'SELECT auth_option_id
		FROM ' . ACL_OPTIONS_TABLE . "
		WHERE auth_option = 'f_list'";
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$list_id = $row['auth_option_id'];

// Get users requesting digests for the current hour
$sql = 'SELECT *
	FROM ' . USERS_TABLE . "
	WHERE ((user_digest_type = '" . DIGEST_DAILY_VALUE . "' AND (user_digest_send_hour_gmt = $current_hour_gmt OR user_digest_send_hour_gmt = $current_hour_gmt_plus_30))" . 
	$weekly_digest_sql . ") AND user_inactive_reason = 0 AND user_digest_type <> '" . DIGEST_NONE_VALUE . "' ORDER BY user_lang";
	
$result = $db->sql_query($sql);

$messenger = new messenger(false); // Send out digests one at a time, not in batch
	
while ($row = $db->sql_fetchrow($result))
{
	// Each traverse through the loop sends out exactly one digest
	
	$digest_type = ($row['user_digest_type'] == DIGEST_DAILY_VALUE) ? $user->lang['DIGEST_DAILY'] : $user->lang['DIGEST_WEEKLY'];
	$email_subject = sprintf($user->lang['DIGEST_SUBJECT_TITLE'], $config['sitename'], $digest_type);

	// Change the digest format into something human readable
	switch($row['user_digest_format'])
	{
		case(DIGEST_TEXT_VALUE):
			$format = $user->lang['DIGEST_FORMAT_TEXT'];
			$messenger->template('digests_text'); // Change based on whether text, plain HTML or expanded HTML
			$is_html = false;
			$disclaimer = strip_tags(sprintf($user->lang['DIGEST_DISCLAIMER'], $board_url, $config['sitename'], $board_url, $phpEx, $config['board_contact'], $config['sitename']));
			$powered_by = $config['digests_digests_title'];
			$use_classic_template = false;
			break;
		case(DIGEST_PLAIN_VALUE):
			$format = $user->lang['DIGEST_FORMAT_PLAIN'];
			$messenger->template('digests_plain_html'); // Change based on whether text, plain HTML or expanded HTML
			$is_html = true;
			$disclaimer = sprintf($user->lang['DIGEST_DISCLAIMER'], $board_url, $config['sitename'], $board_url, $phpEx, $config['board_contact'], $config['sitename']);
			$powered_by = sprintf("<a href=\"%s\">%s</a>",$config['digests_page_url'],$config['digests_digests_title']);
			$use_classic_template = false;
			break;
		case(DIGEST_PLAIN_CLASSIC_VALUE):
			$format = $user->lang['DIGEST_FORMAT_PLAIN_CLASSIC'];
			$messenger->template('digests_plain_html'); // Change based on whether text, plain HTML or expanded HTML
			$is_html = true;
			$disclaimer = sprintf($user->lang['DIGEST_DISCLAIMER'], $board_url, $config['sitename'], $board_url, $phpEx, $config['board_contact'], $config['sitename']);
			$powered_by = sprintf("<a href=\"%s\">%s</a>",$config['digests_page_url'],$config['digests_digests_title']);
			$use_classic_template = true;
			break;
		case(DIGEST_HTML_VALUE):
			$format = $user->lang['DIGEST_FORMAT_HTML'];
			$messenger->template('digests_html'); // Change based on whether text, plain HTML or expanded HTML
			$is_html = true;
			$disclaimer = sprintf($user->lang['DIGEST_DISCLAIMER'], $board_url, $config['sitename'], $board_url, $phpEx, $config['board_contact'], $config['sitename']);
			$powered_by = sprintf("<a href=\"%s\">%s</a>",$config['digests_page_url'],$config['digests_digests_title']);
			$use_classic_template = false;
			break;
		case(DIGEST_HTML_CLASSIC_VALUE):
			$format = $user->lang['DIGEST_FORMAT_HTML_CLASSIC'];
			$messenger->template('digests_html'); // Change based on whether text, plain HTML or expanded HTML
			$is_html = true;
			$disclaimer = sprintf($user->lang['DIGEST_DISCLAIMER'], $board_url, $config['sitename'], $board_url, $phpEx, $config['board_contact'], $config['sitename']);
			$powered_by = sprintf("<a href=\"%s\">%s</a>",$config['digests_page_url'],$config['digests_digests_title']);
			$use_classic_template = true;
			break;
		default:
			trigger_error(sprintf($user->lang['DIGEST_FORMAT_ERROR'], $row['user_digest_format']));
			break;
	}
	
	// Set email header information
	$messenger->to($row['user_email']);
	$messenger->from($config['sitename'] . ' ' . $user->lang['DIGEST_ROBOT'] . ' <' . $config['board_email'] . '>');
	$messenger->subject($email_subject);
	
	// Transform user_digest_send_hour_gmt to local time
	$local_send_hour = $row['user_digest_send_hour_gmt'] + ($row['user_timezone'] + $row['user_dst']);
	if ($local_send_hour >= 24)
	{
		$local_send_hour = $local_send_hour - 24;
	}
	else if ($local_send_hour < 0)
	{
		$local_send_hour = $local_send_hour + 24;
	}
	
	if (($local_send_hour >= 24) || ($local_send_hour < 0))
	{
		trigger_error(sprintf($user->lang['DIGEST_TIME_ERROR'], $row['user_digest_filter_type']));
		exit;
	}
	
	// Change the filter type into something human readable
	switch($row['user_digest_filter_type'])
	{
		case(DIGEST_ALL):
			$post_types = $user->lang['DIGEST_POSTS_TYPE_ANY'];
			break;
		case(DIGEST_FIRST):
			$post_types = $user->lang['DIGEST_POSTS_TYPE_FIRST'];
			break;
		case(DIGEST_BOOKMARKS):
			$post_types = $user->lang['DIGEST_USE_BOOKMARKS'];
			break;
		default:
			trigger_error(sprintf($user->lang['DIGESTS_FILTER_ERROR'], $row['user_digest_filter_type']));
			exit;
	}
	
	// Change the sort by into something human readable
	switch ($row['user_digest_sortby'])
	{
		case(DIGEST_SORTBY_BOARD):
			$sort_by = $user->lang['DIGEST_SORT_USER_ORDER'];
			break;
		case(DIGEST_SORTBY_STANDARD):
			$sort_by = $user->lang['DIGEST_SORT_FORUM_TOPIC'];
			break;
		case(DIGEST_SORTBY_STANDARD_DESC):
			$sort_by = $user->lang['DIGEST_SORT_FORUM_TOPIC_DESC'];
			break;
		case(DIGEST_SORTBY_POSTDATE):
			$sort_by = $user->lang['DIGEST_SORT_POST_DATE'];
			break;
		case(DIGEST_SORTBY_POSTDATE_DESC):
			$sort_by = $user->lang['DIGEST_SORT_POST_DATE_DESC'];
			break;
		default:
			trigger_error(sprintf($user->lang['DIGESTS_SORT_BY_ERROR'], $row['user_digest_sortby']));
			exit;
	}
	
	// Send a proper content-language to the output
	$user_lang = $row['user_lang'];
	if (strpos($user_lang, '-x-') !== false)
	{
		$user_lang = substr($user_lang, 0, strpos($user_lang, '-x-'));
	}
	
	// Create proper message for indicating number of posts allowed in digest
	if (($row['user_digest_max_posts'] == 0) || ($config['digests_max_items'] == 0))
	{
		$max_posts_msg = $user->lang['DIGEST_NO_LIMIT'];
	}
	else if ($config['digests_max_items'] < $row['user_digest_max_posts'])
	{
		$max_posts_msg = sprintf($user->lang['DIGEST_BOARD_LIMIT'], $config['digests_max_items']);
	}
	else
	{
		$max_posts_msg = $row['user_digest_max_posts'];
	}

	$server_timezone = floatval(date('O')/100);
	// Convert server time into GMT time
	$gmt_time = time() - ($server_timezone * 60 * 60);
	// Now adjust GMT time to digest recipient's local time
	$recipient_time = $gmt_time + (($row['user_timezone'] + $row['user_dst']) * 60 * 60);

	// Print the non-post information in the digest
	$messenger->assign_vars(array(
		'DIGEST_DISCLAIMER'				=> $disclaimer,
		'DIGEST_FORMAT'					=> $format,
		'DIGEST_FREQUENCY'				=> $digest_type,
		'DIGEST_MAX_MSG_SIZE'			=> ($row['user_digest_max_display_words'] == 0) ? $user->lang['DIGEST_NO_LIMIT'] : $row['user_digest_max_display_words'],
		'DIGEST_MAX_POSTS_IN_DIGESTS'	=> $max_posts_msg,
		'DIGEST_MIN_WORDS_IN_DIGEST'	=> ($row['user_digest_min_words'] == 0) ? $user->lang['DIGEST_NO_CONSTRAINT'] : $row['user_digest_min_words'],
		'DIGEST_POST_TYPES'				=> $post_types,
		'DIGEST_POWERED_BY'				=> $powered_by,
		'DIGEST_REMOVE_FOES'			=> ($row['user_digest_remove_foes'] == 0) ? $user->lang['NO'] : $user->lang['YES'],
		'DIGEST_RESET_LAST_VISIT'		=> ($row['user_digest_reset_lastvisit'] == 0) ? $user->lang['NO'] : $user->lang['YES'],
		'DIGEST_SALUTATION'				=> $row['username'],
		'DIGEST_SEND_HOUR'				=> $user->lang['DIGEST_HOUR'][$local_send_hour],
		'DIGEST_SEND_IF_NO_NEW_MESSAGES'=> ($row['user_digest_send_on_no_posts'] == 0) ? $user->lang['NO'] : $user->lang['YES'],
		'DIGEST_SHOW_MY_MESSAGES'		=> ($row['user_digest_show_mine'] == 0) ? $user->lang['YES'] : $user->lang['NO'],
		'DIGEST_SHOW_NEW_POSTS_ONLY'	=> ($row['user_digest_new_posts_only'] == 1) ? $user->lang['YES'] : $user->lang['NO'],
		'DIGEST_SHOW_PMS'				=> ($row['user_digest_show_pms'] == 0) ? $user->lang['NO'] : $user->lang['YES'],
		'DIGEST_SORT_ORDER'				=> $sort_by,
		'DIGEST_VERSION'				=> $config['digests_version'],
		'L_DIGEST_FORMAT'				=> $user->lang['DIGEST_FORMAT_FOOTER'],
		'L_DIGEST_FREQUENCY'			=> $user->lang['DIGEST_MAIL_FREQUENCY'],
		'L_DIGEST_INTRODUCTION'			=> sprintf($user->lang['DIGEST_INTRODUCTION'],$config['sitename']),
		'L_DIGEST_MAX_MSG_SIZE'			=> $user->lang['DIGEST_MAX_SIZE'],
		'L_DIGEST_MAX_POSTS_IN_DIGESTS'	=> $user->lang['DIGEST_COUNT_LIMIT'],
		'L_DIGEST_MIN_WORDS_IN_DIGEST'	=> $user->lang['DIGEST_MIN_SIZE'],
		'L_DIGEST_OPTIONS'				=> $user->lang['DIGEST_YOUR_DIGEST_OPTIONS'],
		'L_DIGEST_POST_TYPES'			=> $user->lang['DIGEST_FILTER_TYPE'],
		'L_DIGEST_POWERED_BY'			=> $user->lang['DIGEST_POWERED_BY'],
		'L_DIGEST_PUBLISH_DATE'			=> sprintf($user->lang['DIGEST_PUBLISH_DATE'], date($row['user_dateformat'], $recipient_time)),
		'L_DIGEST_REMOVE_FOES'			=> $user->lang['DIGEST_FILTER_FOES'],
		'L_DIGEST_RESET_LAST_VISIT'		=> $user->lang['DIGEST_LASTVISIT_RESET'],
		'L_DIGEST_SALUTATION'			=> $user->lang['DIGEST_SALUTATION'],
		'L_DIGEST_SEND_HOUR'			=> $user->lang['DIGEST_SEND_HOUR'],
		'L_DIGEST_SEND_IF_NO_NEW_MESSAGES'	=> $user->lang['DIGEST_SEND_IF_NO_NEW_MESSAGES'],
		'L_DIGEST_SHOW_MY_MESSAGES'		=> $user->lang['DIGEST_REMOVE_YOURS'],
		'L_DIGEST_SHOW_NEW_POSTS_ONLY'	=> $user->lang['DIGEST_SHOW_NEW_POSTS_ONLY'],
		'L_DIGEST_SHOW_PMS'				=> $user->lang['DIGEST_SHOW_PMS'],
		'L_DIGEST_SORT_ORDER'			=> $user->lang['DIGEST_SORT_BY'],
		'L_DIGEST_TITLE'				=> $email_subject,
		'L_SITENAME'					=> $config['sitename'],
		'S_CONTENT_DIRECTION'			=> $user->lang['DIRECTION'],
		'S_USER_LANG'					=> $user_lang,
		// Opportunity for improvement: use user's stylesheet, not board default, which is what will happen in this context
		'T_STYLESHEET_LINK'				=> ($config['digests_enable_custom_stylesheets']) ? "{$board_url}styles/" . $config['digests_custom_stylesheet_path'] : "{$board_url}styles/" . $user->theme['theme_path'] . '/theme/stylesheet.css',
		'T_THEME_PATH'					=> "{$board_url}styles/" . $user->theme['theme_path'] . '/theme',
	));
	
	// Create SQL stubs
	
	$digest_exception = false;	// Used to indicate if the SQL should not be called to fetch posts due to conditions like no bookmarked topics
	
	// Determine how far back one day or one week is from the current time. Beyond this time no posts will be fetched
	if(trim($row['user_digest_type']) == DIGEST_DAILY_VALUE) 
	{
		$date_limit = time() - (24 * 60 * 60);
	}
	else // Must be a weekly digest, since NONE has already been filtered out
	{
		$date_limit = time() - (7 * 24 * 60 * 60);
	}
	
	// If requested to fetch new posts since the user's last visit, we need to examine the user_lastvisit date from the database.
	// However, in no event do we want to go back more than either one week or day, depending on the type of digest requested.
	if ($row['user_digest_new_posts_only'])
	{
		$date_limit = max($date_limit, $row['user_lastvisit']);
	}
	$date_limit_sql = ' AND p.post_time > ' . $date_limit;

	if ($row['user_digest_filter_type'] == DIGEST_BOOKMARKS) // Bookmarked topics only
	{
	
		// When selecting bookmarked topics only, we can safely ignore the logic constraining the user to read only 
		// from certain forums. Instead we will create the SQL to get the bookmarked topics only.
		
		unset($bookmarked_topics);
		$bookmarked_topics = array();
		$sql2 = 'SELECT t.topic_id
			FROM ' . USERS_TABLE . ' u, ' . BOOKMARKS_TABLE . ' b, ' . TOPICS_TABLE . " t
			WHERE u.user_id = b.user_id AND b.topic_id = t.topic_id 
				AND t.topic_last_post_time > $date_limit
				AND b.user_id = " . $row['user_id'];
		$result2 = $db->sql_query($sql2);
		while ($row2 = $db->sql_fetchrow($result2))
		{
			$bookmarked_topics[] = intval($row2['topic_id']);
		}
		$db->sql_freeresult($result2);
		if (sizeof($bookmarked_topics) > 0)
		{
			$fetched_forums_sql = ' AND ' . $db->sql_in_set('t.topic_id', $bookmarked_topics);
		}
		else
		{
			// Logically, if there are no bookmarked topics for this user_id then there will be nothing in the digest.
			$digest_exception = true;
			$digest_exception_type = $user->lang['DIGEST_NO_BOOKMARKS'];
		}
	}
	
	else
	
	{
	
		// This logic creates some qualifying SQL to retrieve posts only from the correct forums

		// Get forum read permissions for this user. They are also usually stored in the user_permissions column, but sometimes the field is empty. This always works.
		unset($allowed_forums);
		$allowed_forums = array();
		
		$forum_array = $auth->acl_raw_data_single_user($row['user_id']);
		foreach ($forum_array as $key => $value)
		{
			foreach ($value as $auth_option_id => $auth_setting)
			{
				if ($auth_option_id == $read_id)
				{
					if (($auth_setting == 1) && check_all_parents($key))
					{
						$allowed_forums[] = $key;
					}
				}
			}
		}
		
		if (sizeof($allowed_forums) == 0)
		{
			// If this user cannot retrieve ANY forums, no digest can be produced.
			$digest_exception = true;
			$digest_exception_type = $user->lang['DIGEST_NO_ALLOWED_FORUMS'];
		}
		else
		{
			$allowed_forums[] = 0;	// Add in global announcements forum
		}
		
		// Get the requested forums. If none are specified in the phpbb_digests_subscribed_forums table, then all allowed forums are assumed
		unset($requested_forums);
		$requested_forums = array();
		$sql2 = 'SELECT * FROM ' . DIGESTS_SUBSCRIBED_FORUMS_TABLE . '
				WHERE user_id = ' . $row['user_id'];
				
		$result2 = $db->sql_query($sql2);
		while ($row2 = $db->sql_fetchrow($result2))
		{
				$requested_forums[] = $row2['forum_id'];
		}
		$db->sql_freeresult($result2);
		
		// To capture global announcements when forums are specified, we have to add the pseudo-forum with a forum_id = 0.
		if (sizeof($requested_forums) > 0)
		{
			$requested_forums[] = 0;
		}
		
		// Ensure there are no duplicates
		$requested_forums = array_unique($requested_forums);
		
		// The forums that will be fetched is the array intersection of the requested and allowed forums. 
		$fetched_forums = (sizeof($requested_forums) > 0) ? array_intersect($allowed_forums, $requested_forums): $allowed_forums;
		asort($fetched_forums);
		if (sizeof($fetched_forums) == 0)
		{
			$digest_exception = true;
			$digest_exception_type = $user->lang['DIGEST_NO_ALLOWED_FORUMS'];
			$fetched_forums_sql = '';
		}
		else
		{
			$fetched_forums_sql = ' AND ' . $db->sql_in_set('p.forum_id', $fetched_forums);
		}

	}

	// Create the SQL stub for the sort order
	switch($row['user_digest_sortby'])
	{
		case DIGEST_SORTBY_BOARD:
			$topic_asc_desc = ($row['user_topic_sortby_dir'] == 'd') ? 'DESC' : '';
			switch($row['user_topic_sortby_type'])
			{
				case 'a':
					$order_by_sql = "f.left_id, f.right_id, t.topic_first_poster_name $topic_asc_desc, ";
					break;
				case 't':
					$order_by_sql = "f.left_id, f.right_id, t.topic_last_post_time $topic_asc_desc, ";
					break;
				case 'r':
					$order_by_sql = "f.left_id, f.right_id, t.topic_replies $topic_asc_desc, ";
					break;
				case 's':
					$order_by_sql = "f.left_id, f.right_id, t.topic_title $topic_asc_desc, " ; 
					break;
				case 'v':
					$order_by_sql = "f.left_id, f.right_id, t.topic_views $topic_asc_desc, ";
					break;
			}
			$post_asc_desc = ($row['user_post_sortby_dir'] == 'd') ? 'DESC' : '';
			switch($row['user_post_sortby_type'])
			{
				case 'a':
					$order_by_sql .= "u.username_clean $post_asc_desc";
					break;
				case 't':
					$order_by_sql .= "p.post_time $post_asc_desc";
					break;
				case 's':
					$order_by_sql .= "p.post_subject $post_asc_desc" ; 
					break;
			}
			break;
		case DIGEST_SORTBY_STANDARD:
			$order_by_sql = 'f.left_id, f.right_id, t.topic_last_post_time, p.post_time';
			break;
		case DIGEST_SORTBY_STANDARD_DESC:
			$order_by_sql = 'f.left_id, f.right_id, t.topic_last_post_time, p.post_time DESC';
			break;
		case DIGEST_SORTBY_POSTDATE:
			$order_by_sql = 'f.left_id, f.right_id, p.post_time';
			break;
		case DIGEST_SORTBY_POSTDATE_DESC:
			$order_by_sql = 'f.left_id, f.right_id, p.post_time DESC';
			break;
	}

	$new_topics_sql = '';
	$topics_posts_join_sql = 'f.forum_id = t.forum_id AND p.topic_id = t.topic_id ';
	
	// Create the first_post_only SQL stubs
	if ($row['user_digest_filter_type'] == DIGEST_FIRST)
	{
		$new_topics_sql = " AND t.topic_time > $date_limit ";
		$topics_posts_join_sql = ' t.topic_first_post_id = p.post_id AND t.forum_id = f.forum_id';
	}

	// Create SQL to remove your posts from the feed
	$remove_mine_sql = ($row['user_digest_show_mine'] == 0) ? ' AND p.poster_id <> ' . $row['user_id'] : '';

	// Create SQL to remove your foes from the feed
	$filter_foes_sql = '';
	unset($foes);
	$foes = array();
	if ($row['user_digest_remove_foes'] == 1)
	{
	
		// Fetch your foes
		$sql2 = 'SELECT zebra_id 
				FROM ' . ZEBRA_TABLE . '
				WHERE user_id = ' . $row['user_id'] . ' AND foe = 1';
		$result2 = $db->sql_query($sql2);
		while ($row2 = $db->sql_fetchrow($result2))
		{
			$foes[] = (int) $row2['zebra_id'];
		}
		$db->sql_freeresult($result2);
	
		if (sizeof($foes) > 0)
		{
			$filter_foes_sql = ' AND ' . $db->sql_in_set('p.poster_id', $foes, true);
		}
		
	}
	
	// At last, construct the SQL to return the relevant posts
	if (!$digest_exception)
	{
	
		$sql_array = array(
			'SELECT'	=> 'f.*, t.*, p.*, u.*',
		
			'FROM'		=> array(
				POSTS_TABLE => 'p',
				USERS_TABLE => 'u',
				TOPICS_TABLE => 't'	),
		
			'WHERE'		=> "$topics_posts_join_sql
						AND p.poster_id = u.user_id
						$date_limit_sql
						$fetched_forums_sql
						$new_topics_sql
						$remove_mine_sql
						$filter_foes_sql
						AND p.post_approved = 1",
		
			'ORDER_BY'	=> $order_by_sql
		);
		
		$sql_array['LEFT_JOIN'] = array(
			array(
				'FROM'	=> array(FORUMS_TABLE => 'f', TOPICS_TABLE => 't'),
				'ON'	=> 't.forum_id = f.forum_id'
			)
		);
		
		$sql_posts = $db->sql_build_query('SELECT', $sql_array);
		
		if (defined(DEBUG))
		{
			// In debug mode, write out the SQL used to retrieve posts
			write_log_entry(sprintf($user->lang['DIGEST_SQL_POSTS'], $row['username'], $sql_posts));
		}
		
		// Execute the SQL to retrieve the relevant posts. Note, if $row['user_digest_max_posts'] == 0 and $config['digests_max_items'] == 0 then there is no limit on the rows returned
		$result_posts = $db->sql_query_limit($sql_posts, min($row['user_digest_max_posts'],$config['digests_max_items'])); 
		$rowset_posts = $db->sql_fetchrowset($result_posts); // Get all the posts as a set
	
	}
	else
	{
		$result_posts = NULL;
		$rowset_posts = NULL;
	}
	
	if (!$digest_exception)
	{
		if ($row['user_digest_show_pms'])
		{
		
			// If there are any unread private messages, they are fetched separately and passed as a rowset to publish_feed.
			$pm_sql = 	'SELECT *
						FROM ' . PRIVMSGS_TO_TABLE . ' pt, ' . PRIVMSGS_TABLE . ' pm, ' . USERS_TABLE . ' u
						WHERE pt.msg_id = pm.msg_id
							AND pt.author_id = u.user_id
							AND pt.user_id = ' . $row['user_id'] . '
							AND (pm_unread = 1 OR pm_new = 1)
						ORDER BY message_time';
			if (defined(DEBUG))
			{
				// In debug mode, write out the SQL used to retrieve posts
				write_log_entry(sprintf($user->lang['DIGEST_SQL_PMS'], $row['username'], $pm_sql));
			}
			$pm_result = $db->sql_query($pm_sql);
			$pm_rowset = $db->sql_fetchrowset($pm_result);
			
		}
		else
		{
			$pm_result = NULL;
			$pm_rowset = NULL;
			if (defined(DEBUG))
			{
				write_log_entry(sprintf($user->lang['DIGEST_SQL_PMS_NONE'], $row['username']));
			}
		}
	}
	
	// Construct the body of the digest. We use the templating system because of the advanced features missing in the 
	// email templating system, e.g. loops and switches
	if (!$digest_exception)
	{
		$digest_content = create_content($rowset_posts, $pm_rowset, $row);
		
		$messenger->assign_vars(array(
			'DIGEST_CONTENT'		=> $digest_content,	
		));
		
		// Mark private messages in the digest as read, if so instructed
		if ((sizeof($pm_rowset) != 0) && ($row['user_digest_show_pms'] == 1) && ($row['user_digest_pm_mark_read'] == 1))
		{
			$pm_read_sql = 'UPDATE ' . PRIVMSGS_TO_TABLE . '
				SET pm_new = 0, pm_unread = 0 
				WHERE user_id = ' . $row['user_id'] . '
					AND (pm_unread = 1 OR pm_new = 1)';
			$pm_read_sql_result = $db->sql_query($pm_read_sql);
			$db->sql_freeresult($pm_read_sql_result);
		}
	}
	else
	{
		write_log_entry(sprintf($digest_exception_type, $row['username']));
	}
		 
	$db->sql_freeresult($result_posts);
	$db->sql_freeresult($pm_result);

	// Send the digest out if there are new qualifying posts or the user requests a digest to be sent if there are no posts OR
	// if there are unread private messages, the user wants to see private messages in the digest
	if (!$digest_exception)
	{
		if ( $row['user_digest_send_on_no_posts'] || (sizeof($rowset_posts) > 0) || ((sizeof($pm_rowset) > 0) && $row['user_digest_show_pms']))
		{
			$mail_sent = $messenger->send(NOTIFY_EMAIL, false, $is_html, true);
			if (!$mail_sent)
			{
				write_log_entry(sprintf($user->lang['DIGEST_LOG_ENTRY_BAD'], $row['username'], $row['user_email'], date($config['default_dateformat'])));
			}
			else
			{
				write_log_entry(sprintf($user->lang['DIGEST_LOG_ENTRY_GOOD'], $row['username'], $row['user_email'], sizeof($rowset_posts), sizeof($pm_rowset), date($config['default_dateformat'])));
			}
		}
		else
		{
			// log & reset messenger, bug fix provided by robdocmagic
			write_log_entry(sprintf($user->lang['DIGEST_LOG_ENTRY_NONE'], $row['username'], $row['user_email'], date($config['default_dateformat'])));
			$messenger->reset();
		}
	}

	// Reset the user's last visit date on the forum, if so requested
	if ($row['user_digest_reset_lastvisit'])
	{
		$sql2 = 'UPDATE ' . USERS_TABLE . '
					SET user_lastvisit = ' . time() . ' 
					WHERE user_id = ' . $row['user_id'];
		$result2 = $db->sql_query($sql2);
	}

}

// Kill the session we consumed. We don't want to use session_kill() because it updates user_lastvisit,
// which we don't necessarily want to do. 

$sql = 'DELETE FROM ' . SESSIONS_TABLE . "
		WHERE session_id = '" . $db->sql_escape($user->session_id) . "'
			AND session_user_id = " . (int) $user->data['user_id'];
$db->sql_query($sql);

// Allow connecting logout with external auth method logout
$method = basename(trim($config['auth_method']));
include_once($phpbb_root_path . 'includes/auth/auth_' . $method . '.' . $phpEx);

$method = 'logout_' . $method;
if (function_exists($method))
{
	$method($user->data, $new_session);
}

// Display a digest mail end processing message. It may get captured in a log
write_log_entry(sprintf($user->lang['DIGEST_END'],date($config['default_dateformat'])),true);
 
exit;

function create_content ($rowset, $pm_rowset, $user_row)
{

	// This function creates the bulk of one digest, by marking up private messages and posts
	// as appropriate and passing it back to the calling program.
	
	global $user, $template, $board_url, $phpEx, $config, $row, $is_html, $server_timezone, $use_classic_template;

	// Load the right template
	$mail_template = ($is_html) ? 'mail_digests_html.html' : 'mail_digests_text.html';
			
	$template->set_filenames(array(
	   'mail_digests'      => $mail_template,
	));
	
	// General template variables are set here
	$template->assign_vars(array(
		'DIGEST_TOTAL_PMS'				=> sizeof($pm_rowset),
		'DIGEST_TOTAL_POSTS'			=> sizeof($rowset),
		'L_AUTHOR'						=> $user->lang['DIGEST_AUTHOR'],
		'L_DATE'						=> $user->lang['DIGEST_DATE'],
		'L_DIGEST_LINK'					=> $user->lang['DIGEST_DIGEST_LINK'],
		'L_DIGEST_POST_TEXT'			=> $user->lang['DIGEST_POST_TEXT'],
		'L_DIGEST_POST_TIME'			=> $user->lang['DIGEST_POST_TIME'],
		'L_DIGEST_TOTAL_POSTS'			=> $user->lang['DIGEST_TOTAL_POSTS'],
		'L_DIGEST_TOTAL_PMS'			=> $user->lang['DIGEST_TOTAL_UNREAD_PRIVATE_MESSAGES'],
		'L_FROM'						=> ucwords($user->lang['FROM']),
		'L_MESSAGE_SUBJECT'				=> $user->lang['DIGEST_SUBJECT_LABEL'],
		'L_NO_PMS'						=> $user->lang['DIGEST_NO_PRIVATE_MESSAGES'] . "\n",
		'L_NO_POSTS'					=> $user->lang['DIGEST_NO_POSTS'] . "\n",
		'L_ON'							=> $user->lang['DIGEST_ON'],
		'L_PRIVATE_MESSAGE'				=> strtolower($user->lang['PRIVATE_MESSAGE']) . "\n",
		'L_PRIVATE_MESSAGE_2'			=> ucwords($user->lang['PRIVATE_MESSAGE']) . "\n",
		'L_YOU_HAVE_PRIVATE_MESSAGES'	=> $user->lang['DIGEST_YOU_HAVE_PRIVATE_MESSAGES'] . "\n",
		'S_SHOW_TOTAL_PMS'				=> ($user_row['user_digest_show_pms'] == 1) ? 'Y' : 'N',
		'S_USE_CLASSIC_TEMPLATE'		=> ($use_classic_template) ? 'Y' : 'N',
	));
	
	// Process private messages, if any, first
	
	if ((sizeof($pm_rowset) != 0) && ($row['user_digest_show_pms'] == 1))
	{
	
		$template->assign_vars(array(
			'S_SHOW_PMS'	=> 'Y',
		));
		
		foreach ($pm_rowset as $pm_row)
		{
		
			// Now adjust post time to digest recipient's local time
			$recipient_time = $pm_row['message_time'] - ($server_timezone * 60 * 60) + (($row['user_timezone'] + $row['user_dst']) * 60 * 60);

			$flags = (($pm_row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
				(($pm_row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
				(($pm_row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
				
			$pm_text = generate_text_for_display($pm_row['message_text'], $pm_row['bbcode_uid'], $pm_row['bbcode_bitfield'], $flags);
			
			// User signature wanted?
			$user_sig = ( $pm_row['enable_sig'] && $pm_row['user_sig'] != '' && $config['allow_sig'] ) ? $pm_row['user_sig'] : '';
			if ($user_sig != '')
			{
				// Format the signature for display
				$user_sig = generate_text_for_display($user_sig, $pm_row['user_sig_bbcode_uid'], $pm_row['user_sig_bbcode_bitfield'], $flags);
			}
		
			// Add signature to bottom of post
			$pm_text = ($user_sig != '') ? $pm_text . "\n" . $user->lang['DIGEST_POST_SIGNATURE_DELIMITER'] . "\n" . $user_sig : $pm_text . "\n";

			// If a text digest is desired, this is a good point to strip tags, after first replacing <br /> with \n
			if (!$is_html)
			{
				$pm_text = str_replace('<br />', "\n", $pm_text);
				$pm_text = strip_tags($pm_text);
			}
			else
			{
				// Board URLs must be absolute in the digests, so substitute board URL for relative URL
				$pm_text = str_replace('<img src="./', '<img src="' . $board_url, $pm_text);
			} 

			$template->assign_block_vars('pm', array(
				'NEW_UNREAD'	=> ($pm_row['pm_new'] == 1) ? $user->lang['DIGEST_NEW'] . ' ' : $user->lang['DIGEST_UNREAD'] . ' ',
				'PRIVATE_MESSAGE_LINK'		=> ($is_html) ? sprintf('<a href="%s?i=pm&mode=view&f=0&p=%s">%s</a>', $board_url . 'ucp.' . $phpEx, $pm_row['msg_id'], $pm_row['msg_id']) . "\n" : $pm_row['message_subject'] . "\n",
				'PRIVATE_MESSAGE_SUBJECT'	=> ($is_html) ? sprintf('<a href="%s?i=pm&mode=view&f=0&p=%s">%s</a>', $board_url . 'ucp.' . $phpEx, $pm_row['msg_id'], $pm_row['message_subject']) . "\n" : $pm_row['message_subject'] . "\n",
				'FROM'			=> ($is_html) ? sprintf('<a href="%s?mode=viewprofile&amp;u=%s">%s</a>', $board_url . 'memberlist.' . $phpEx, $pm_row['author_id'], $pm_row['username']) : $pm_row['username'],
				'DATE'			=> date($pm_row['user_dateformat'], $recipient_time) . "\n",
				'CONTENT'		=> $pm_text . "\n",
			));
		}

	}
	else
	{
		// Turn off switch that would indicate there are private messages
		$template->assign_vars(array(
			'S_SHOW_PMS'	=> 'N',
		));
	}
	
	// Process posts next
	
	$last_forum_id = -1;
	$last_topic_id = -1;

	if (sizeof($rowset) != 0)
	{
	
		foreach ($rowset as $post_row)
		{
		
			// Skip if post has less than minimum words wanted.
			$show_in_digest = true;
			if ($row['user_digest_min_words'] > 0)
			{
				$show_in_digest = (truncate_words($post_row['post_text'], $row['user_digest_min_words'], true) < $row['user_digest_min_words']) ? false : true;
			}
			
			if ($show_in_digest)
			{
			
				// Now adjust post time to digest recipient's local time
				$recipient_time = $post_row['post_time'] - ($server_timezone * 60 * 60) + (($row['user_timezone'] + $row['user_dst']) * 60 * 60);
			
				// Need BBCode flags to translate BBCode
				$flags = (($post_row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
					(($post_row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
					(($post_row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
					
				$post_text = generate_text_for_display($post_row['post_text'], $post_row['bbcode_uid'], $post_row['bbcode_bitfield'], $flags);
				
				// User signature wanted?
				$user_sig = ( $post_row['enable_sig'] && $post_row['user_sig'] != '' && $config['allow_sig'] ) ? $post_row['user_sig'] : '';
				if ($user_sig != '')
				{
					// Format the signature for display
					$user_sig = generate_text_for_display($user_sig, $post_row['user_sig_bbcode_uid'], $post_row['user_sig_bbcode_bitfield'], $flags);
				}
			
				// Add signature to bottom of post
				$post_text = ($user_sig != '') ? $post_text . "\n" . $user->lang['DIGEST_POST_SIGNATURE_DELIMITER'] . "\n" . $user_sig : $post_text . "\n";
	
				// If a text digest is desired, this is a good point to strip tags
				if (!$is_html)
				{
					$post_text = str_replace('<br />', "\n", $post_text);
					$post_text = strip_tags($post_text);
				}
				else
				{
					// Board URLs must be absolute in the digests, so substitute board URL for relative URL
					$post_text = str_replace('<img src="./', '<img src="' . $board_url, $post_text);
				} 
		
				if ($last_forum_id != (int) $post_row['forum_id'])
				{
					// Process a forum break
					$template->assign_block_vars('forum', array(
						'FORUM'			=> ($is_html) ? sprintf('<a href="%sviewforum.%s?f=%s">%s</a>', $board_url, $phpEx, $post_row['forum_id'], $post_row['forum_name']) : $post_row['forum_name'] . "\n",
					));
					$last_forum_id = (int) $post_row['forum_id'];
				}
						
				if ($last_topic_id != (int) $post_row['topic_id'])
				{
					// Process a topic break
					$template->assign_block_vars('forum.topic', array(
						'TOPIC'			=> ($is_html) ? sprintf('<a href="%sviewtopic.%s?f=%s&amp;t=%s">%s</a>', $board_url, $phpEx, $post_row['forum_id'], $post_row['topic_id'], $post_row['topic_title']) : $post_row['topic_title'] . "\n",
					));
					$last_topic_id = (int) $post_row['topic_id'];
				}
				
				// Handle max display words logic
				if ($row['user_digest_max_display_words'] > 0)
				{
					$post_text = truncate_words($post_text, $row['user_digest_max_display_words']);
				}
				
				$template->assign_block_vars('forum.topic.post', array(
					'S_FIRST_POST' 	=> ($post_row['topic_first_post_id'] == $post_row['post_id']) ? 'Y' : 'N', // Hide subject if first post, as it is the same as topic title
					'SUBJECT'		=> ($is_html) ? sprintf('<a href="%sviewtopic.php?f=%s&amp;t=%s#p%s">%s</a>%s', $board_url, $post_row['forum_id'], $post_row['topic_id'], $post_row['post_id'], $post_row['post_subject'], "\n") : $post_row['post_subject'] . "\n" ,
					'POST_LINK'		=> ($is_html) ? sprintf('<a href="%sviewtopic.php?f=%s&amp;t=%s#p%s">%s</a>%s', $board_url, $post_row['forum_id'], $post_row['topic_id'], $post_row['post_id'], $post_row['post_id'], "\n") : $post_row['post_id'] . "\n" ,
					'FROM'			=> ($is_html) ? sprintf('<a href="%s?mode=viewprofile&amp;u=%s">%s</a>%s', $board_url . 'memberlist.' . $phpEx, $post_row['user_id'], $post_row['username'], "\n") : $post_row['username'] . "\n",
					'DATE'			=> date($row['user_dateformat'], $recipient_time) . "\n",
					'CONTENT'		=> $post_text . "\n",
				));
			
			}
			
		}
	
	}
	
	$digest_body = $template->assign_display('mail_digests');
	$template->destroy(); // If you don't destroy the template subsequent users will receive duplicate posts
	return $digest_body;
	
}

function truncate_words($text, $max_words, $just_count_words = false)
{

	// This function returns the first $max_words from the supplied $text. If $just_count_words === true, a word count is returned. Note:
	// for consistency, HTML is stripped. This can be annoying, but otherwise HTML rendered in the digest may not be valid.
	
	global $user;
	
	if ($just_count_words)
	{
		return str_word_count(strip_tags($text));
	}
	
	$word_array = preg_split("/[\s]+/", $text);
	
	if (sizeof($word_array) <= $max_words)
	{
		return rtrim($text);
	}
	else
	{
		$truncated_text = '';
		for ($i=0; $i < $max_words; $i++) 
		{
			$truncated_text .= $word_array[$i] . ' ';
		}
		return rtrim($truncated_text) . $user->lang['DIGEST_MAX_WORDS_NOTIFIER'];
	}
}

function write_log_entry ($log_entry, $close = false)
{
	// This function writes a log entry. It could be to STDOUT, or it could be to a file, depending on value of $resource
	
	global $eol, $config, $phpbb_root_path, $phpEx;
	
	static $is_open = false;
	static $resource = NULL;	// If NULL, write to STDOUT
	
	if (!$config['digests_show_output'])
	{
		return;	// Don't write any log entries if not requested
	}

	if (!$is_open)
	{
		if (trim($config['digests_log_path']) != '')
		{
			// Attempt to open the file relative to the phpBB root directory shown in $config['digests_log_path'] write mode
			$mode = ($config['digests_reset_log']) ? 'w+' : 'a+';
			if ($config['digests_reset_log'])
			{
				// If asked to reset the digest log, record the date and time the log was last truncated
				set_config('digests_reset_log_date', time());
				// Then reset the digests_reset_log configuration variable to false
				set_config('digests_reset_log', 0);
			}
			$path = $phpbb_root_path . trim($config['digests_log_path']);
			$resource = fopen($path, $mode);
			if (!$resource)
			{
				echo sprintf($user->lang['DIGEST_LOG_WRITE_ERROR'], $path) . $eol; 
				exit;
			}
		}
		$is_open = true;
	}
	
	if ($resource == NULL)
	{
		echo $log_entry . $eol;
	}
	else
	{
		fwrite ($resource, $log_entry . $eol);
		if ($close)
		{
			fclose($resource);
		}
	}
	
	return;
}

function check_all_parents($forum_id)
{

	// This function checks all parents for a given forum_id. If any of them do not have the f_list permission
	// the function returns false, meaning the forum should not be displayed because it has a parent that should
	// not be listed. Otherwise it returns true, indicating the forum can be listed.
	
	global $db, $forum_array, $list_id;
	
	$there_are_parents = true;
	$current_forum_id = $forum_id;
	$include_this_forum = true;
	
	static $parents_loaded = false;
	static $parent_array = array();
	
	if (!$parents_loaded)
	{
		// Get a list of parent_ids for each forum and put them in an array.
		$sql = 'SELECT forum_id, parent_id 
			FROM ' . FORUMS_TABLE . '
			ORDER BY 1';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$parent_array[$row['forum_id']] = $row['parent_id'];
		}
		$parents_loaded = true;
	}
	
	while ($there_are_parents)
	{
	
		if ($parent_array[$current_forum_id] == 0) 	// No parent
		{
			$there_are_parents = false;
		}
		else
		{
			if ($forum_array[$parent_array[$current_forum_id]][$list_id] == 1)
			{
				// So far so good
				$current_forum_id = $parent_array[$current_forum_id];
			}
			else
			{
				// Danger Will Robinson! No list permission exists for a parent of the requested forum, so this forum should not be shown
				$there_are_parents = false;
				$include_this_forum = false;
			}
		}
		
	}
	
	return $include_this_forum;
	
}

?>