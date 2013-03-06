<?php
/** 
*
* ucp_digests.php [English]
*
* @package language
* @version $Id: v3_modules.xml 52 2007-12-09 19:45:45Z jelly_doughnut $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
					
/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}
						
// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
		
global $config;
				
$lang = array_merge($lang, array(
	'DIGEST_ALL_FORUMS'					=> 'All',
	'DIGEST_AUTHOR'						=> 'Author',
	'DIGEST_BAD_EOL'					=> 'The end of line value of %s is invalid.', 
	'DIGEST_BAD_KEY_VALUE'				=> "The parameter \"key\" was expected but had an invalid value of \"%s\" when running mail_digests.$phpEx. Program terminated. %s",
	'DIGEST_BOARD_DISABLED'				=> 'Digests are temporarily disabled because the board is diabled.',
	'DIGEST_BOARD_LIMIT'				=> '%s (Board limit)',
	'DIGEST_BY'							=> 'By',
	'DIGEST_CONNECT_SOCKET_ERROR'		=> 'Unable to open connection to phpBB Smartfeed site, reported error is:<br />%s',
	'DIGEST_COUNT_LIMIT'				=> 'Maximum number of posts in the digest',
	'DIGEST_COUNT_LIMIT_EXPLAIN'		=> 'Enter a number greater than zero if you want to limit the number of posts in the digest.',
	'DIGEST_CURRENT_VERSION_INFO'		=> 'You are running version <strong>%s</strong>.',
	'DIGEST_DAILY'						=> 'Daily',
	'DIGEST_DATE'						=> 'Date',
	'DIGEST_DISABLED_MESSAGE'			=> 'To enable fields, select Basics and select a digest type',
	'DIGEST_DISCLAIMER'					=> 'This digest is being sent to registered members of <a href="%s">%s</a> forums. You can change or delete your subscription from the <a href="%sucp.%s">User Control Panel</a>. If you have questions or feedback on the format of this digest please send it to the <a href="mailto:%s">%s Webmaster</a>.',
	'DIGEST_END'						=> "Ending mail_digests.$phpEx at %s",
	'DIGEST_EXPLANATION'				=> 'Digests are email summaries of messages posted here that are sent to you periodically. Digests can be sent daily or weekly at an hour of the day you select. You can specify those particular forums for which you want message summaries (select Posts Selection), or by default you can elect to receive all messages for all forums for which you are allowed access. You can, of course, cancel your digest subscription at any time by simply coming back to this page. Most users find digests to be very useful. We encourage you to give it a try!',
	'DIGEST_FILTER_ERROR'				=> "mail_digests.$phpEx was called with an invalid user_digest_filter_type = %s",
	'DIGEST_FILTER_FOES'				=> 'Remove posts from my foes',
	'DIGEST_FILTER_TYPE'				=> 'Types of posts in digest',
	'DIGEST_FORMAT_ERROR'				=> "mail_digests.$phpEx was called with an invalid user_digest_format of %s",
	'DIGEST_FORMAT_FOOTER' 				=> 'Digest Format:',
	'DIGEST_FORMAT_HTML'				=> 'HTML',
	'DIGEST_FORMAT_HTML_EXPLAIN'		=> 'HTML will provide formatting, BBCode and signatures (if allowed). Stylesheets are applied if your email program allows.',
	'DIGEST_FORMAT_HTML_CLASSIC'		=> 'HTML Classic',
	'DIGEST_FORMAT_HTML_CLASSIC_EXPLAIN'	=> 'Similar to HTML except topic posts are listed inside of tables',
	'DIGEST_FORMAT_PLAIN'				=> 'Plain HTML',
	'DIGEST_FORMAT_PLAIN_EXPLAIN'		=> 'Plain HTML does not apply styles or colors',
	'DIGEST_FORMAT_PLAIN_CLASSIC'		=> 'Plain HTML Classic',
	'DIGEST_FORMAT_PLAIN_CLASSIC_EXPLAIN'	=> 'Similar to Plain HTML except topic posts are listed inside of tables',
	'DIGEST_FORMAT_STYLING'				=> 'Digest styling',
	'DIGEST_FORMAT_STYLING_EXPLAIN'		=> 'Please note that the styling actually rendered depends on the capabilities of your email program. Move your cursor over the styling type to learn more about each style.',
	'DIGEST_FORMAT_TEXT'				=> 'Text',
	'DIGEST_FORMAT_TEXT_EXPLAIN'		=> 'No HTML will appear in the digest. Only text will be shown.',
	'DIGEST_FREQUENCY'					=> 'Type of digest wanted',
	'DIGEST_FREQUENCY_EXPLAIN'			=> "Weekly digests are sent on %s. Universal Time is used for determining the day of the week.",
	'DIGEST_HOUR'	=> array(
		0	=> '12 AM',
		1	=> '1 AM',
		2	=> '2 AM',
		3	=> '3 AM',
		4	=> '4 AM',
		5	=> '5 AM',
		6	=> '6 AM',
		7	=> '7 AM',
		8	=> '8 AM',
		9	=> '9 AM',
		10	=> '10 AM',
		11	=> '11 AM',
		12	=> '12 PM',
		13	=> '1 PM',
		14	=> '2 PM',
		15	=> '3 PM',
		16	=> '4 PM',
		17	=> '5 PM',
		18	=> '6 PM',
		19	=> '7 PM',
		20	=> '8 PM',
		21	=> '9 PM',
		22	=> '10 PM',
		23	=> '11 PM'),
	'DIGEST_INTRODUCTION' 				=> 'Here is the latest digest of messages posted on %s forums. Please come and join the discussion!',
	'DIGEST_LASTVISIT_RESET'			=> 'Reset my last visit date when I am sent a digest',
	'DIGEST_LATEST_VERSION_INFO'		=> 'The latest available version is <strong>%s</strong>.',
	'DIGEST_LINK'						=> 'Link',
	'DIGEST_LOG_ENTRY_BAD'				=> 'Unable to send a digest to %s (%s) at %s.',
	'DIGEST_LOG_ENTRY_GOOD'				=> 'A digest was sent to %s (%s) containing %s posts and %s private messages at %s.',
	'DIGEST_LOG_ENTRY_NONE'				=> 'A digest was NOT sent to %s (%s) because user filters and preferences meant there was nothing to send. %s.',
	'DIGEST_LOG_WRITE_ERROR'			=> 'Unable to write to log with path, path = %s. This is frequently caused by the lack of public write permissions on this file.',
	'DIGEST_MAIL_FREQUENCY' 			=> 'Digest Frequency',
	'DIGEST_MARK_READ'					=> 'Mark as read when they appear in the digest',
	'DIGEST_MAX_SIZE'					=> 'Maximum words to display in a post',
	'DIGEST_MAX_SIZE_EXPLAIN'			=> 'Notice: To ensure consistent rendering, if a post must be truncated, the HTML will be removed from the post.',
	'DIGEST_MAX_WORDS_NOTIFIER'			=> '... ',
	'DIGEST_MIN_SIZE'					=> 'Minimum words required in post for the post to appear in a digest',
	'DIGEST_MIN_SIZE_EXPLAIN'			=> 'If you leave this blank, posts with text of any number of words are included',
	'DIGEST_NEW'						=> 'New',
	'DIGEST_NEW_POSTS_ONLY'				=> 'Show new posts only',
	'DIGEST_NEW_POSTS_ONLY_EXPLAIN'		=> 'This will filter out any posts posted prior to the date and time you last visited this board. If you visit the board frequently and read most of the posts, this will keep redundant posts from appearing in your digest. It may also mean that you will miss some posts in forums that you did not read.',
	'DIGEST_NO_ALLOWED_FORUMS'			=> 'Digest exception: %s cannot access any forums so no digest can be created.',
	'DIGEST_NO_BOOKMARKS'				=> 'Digest exception: %s requested a digest for bookmarked topics only but has no bookmarked topics.',
	'DIGEST_NO_CONSTRAINT'				=> 'No constraint',
	'DIGEST_NO_FORUMS_CHECKED' 			=> 'At least one forum must be checked',
	'DIGEST_NO_LIMIT'					=> 'No limit',
	'DIGEST_NO_POSTS'					=> 'There are no new posts.',
	'DIGEST_NO_PRIVATE_MESSAGES'		=> 'You have no new or unread private messages.',
	'DIGEST_NONE'						=> 'None (unsubscribe)',
	'DIGEST_ON'							=> 'on',
	'DIGEST_POST_TEXT'					=> 'Post Text', 
	'DIGEST_POST_TIME'					=> 'Post Time', 
	'DIGEST_POST_SIGNATURE_DELIMITER'	=> '<br />____________________<br />', // Place here whatever code (make sure it is valid XHTML) you want to use to distinguish the end of a post from the beginning of the signature line
	'DIGEST_POSTS_TYPE_ANY'				=> 'All posts',
	'DIGEST_POSTS_TYPE_FIRST'			=> 'First posts of topics only',
	'DIGEST_POWERED_BY'					=> 'Powered by',
	'DIGEST_PRIVATE_MESSAGES_IN_DIGEST'	=> 'Add my unread private messages',
	'DIGEST_PUBLISH_DATE'				=> 'The digest was published specifically for you on %s',
	'DIGEST_REMOVE_YOURS'				=> 'Remove my posts',
	'DIGEST_ROBOT'						=> 'Robot',
	'DIGEST_SALUTATION' 				=> 'Dear',
	'DIGEST_SELECT_FORUMS'				=> 'Include posts for these forums',
	'DIGEST_SELECT_FORUMS_EXPLAIN'		=> 'Please note the categories and forums shown are for those you are allowed to read only. Forum selection is disabled when you select bookmarked topics only.',
	'DIGEST_SEND_HOUR' 					=> 'Hour sent',
	'DIGEST_SEND_HOUR_EXPLAIN'			=> 'The digest arrival time is the time based on the time zone and daylight savings/summer time you set in your board preferences.',
	'DIGEST_SEND_IF_NO_NEW_MESSAGES'	=> 'Send digest if no new messages:',
	'DIGEST_SEND_ON_NO_POSTS'	 		=> 'Send a digest if there are no new posts',
	'DIGEST_SHOW_MY_MESSAGES' 			=> 'Show my posts in the digest:',
	'DIGEST_SHOW_NEW_POSTS_ONLY' 		=> 'Show new posts only',
	'DIGEST_SHOW_PMS' 					=> 'Show my private messages',
	'DIGEST_SIZE_ERROR'					=> 'This field is a required field. You must enter a positive whole number, less than or equal to the maximum allowed by the Forum Administrator. The maximum allowed is %s. If this value is zero, there is no limit.',
	'DIGEST_SIZE_ERROR_MIN'				=> 'You must enter a positive value or leave the field blank',
	'DIGEST_SOCKET_FUNCTIONS_DISABLED'	=> 'Socket functions are currently disabled.',
	'DIGEST_SORT_BY'					=> 'Post sort order',
	'DIGEST_SORT_BY_ERROR'				=> "mail_digests.$phpEx was called with an invalid user_digest_sortby = %s",
	'DIGEST_SORT_BY_EXPLAIN'			=> 'All digests are sorted by category and then by forum, like they are shown on the main index. Sort options apply to how posts are arranged within topics. Traditional Order is the default order used by phpBB 2, which is last topic post time (descending) then by post time within the topic.',
	'DIGEST_SORT_FORUM_TOPIC'			=> 'Traditional Order',
	'DIGEST_SORT_FORUM_TOPIC_DESC'		=> 'Traditional Order, Latest Posts First',
	'DIGEST_SORT_POST_DATE'				=> 'From oldest to newest',
	'DIGEST_SORT_POST_DATE_DESC'		=> 'From newest to oldest',
	'DIGEST_SORT_USER_ORDER'			=> 'Use my board display preferences',
	'DIGEST_SQL_PMS'					=> 'SQL used for private messages for %s: %s',
	'DIGEST_SQL_PMS_NONE'				=> 'No SQL issued for private_messages for %s because the user opted not to show private messages in the digest.',
	'DIGEST_SQL_POSTS'					=> 'SQL used for posts for %s: %s',
	'DIGEST_START'						=> "Starting mail_digests.$phpEx at %s",
	'DIGEST_SUBJECT_LABEL'				=> 'Subject',
	'DIGEST_SUBJECT_TITLE'				=> '%s %s Digest',
	'DIGEST_TIME_ERROR'					=> "mail_digests.$phpEx calculated a bad digest send hour of %s",
	'DIGEST_TOTAL_POSTS'				=> 'Total posts in this digest:',
	'DIGEST_TOTAL_UNREAD_PRIVATE_MESSAGES'	=> 'Total unread private messages:',
	'DIGEST_UNREAD'						=> 'Unread',
	'DIGEST_UPDATED'					=> 'Your digest settings were saved',
	'DIGEST_USE_BOOKMARKS'				=> 'Bookmarked topics only',
	'DIGEST_VERSION_NOT_UP_TO_DATE'		=> 'Administrator Notice: this version of phpBB Digests is not current. Updates are available on the <a href="%s">Digests website</a>.',
	'DIGEST_VERSION_UP_TO_DATE'			=> 'This version of phpBB Digests is up-to-date, no update available.',
	'DIGEST_WEEKDAY' => array(
		0	=> 'Sunday',
		1 	=> 'Monday',
		2	=> 'Tuesday',
		3	=> 'Wednesday',
		4	=> 'Thursday',
		5	=> 'Friday',
		6	=> 'Saturday'),
	'DIGEST_WEEKLY'						=> 'Weekly',
	'DIGEST_YOU_HAVE_PRIVATE_MESSAGES' 	=>	'You have private messages',
	'DIGEST_YOUR_DIGEST_OPTIONS' 		=> 'Your digest options:',
	'S_DIGEST_TITLE'					=> $config['digests_digests_title'],
	'UCP_DIGESTS_MODE_ERROR'			=> 'Digests was called with an invalid mode of %s',
				
));
			
?>