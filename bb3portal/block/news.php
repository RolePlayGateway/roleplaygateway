<?php
/*
*
* @name news.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: news.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

//
// Fetch Posts for news from bb3portal/includes/functions.php
//
if( (!isset($HTTP_GET_VARS['article'])) && ($CFG['news'] == true) )
{
	$fetch_news = phpbb_fetch_posts($CFG['news_forum'], $CFG['number_of_news'], $CFG['news_length'], 0, ($CFG['show_all_news']) ? 'news_all' : 'news');
	
	if (count($fetch_news) == 0)
	{
		$template->assign_block_vars('news_row', array(
			'S_NO_TOPICS'	=> true,
			'S_NOT_LAST'	=> false
			)
        );
	}
	else
	{
		for ($i = 0; $i < count($fetch_news); $i++)
		{
	      	if( isset($fetch_news[$i]['striped']) && $fetch_news[$i]['striped'] == true )
	      	{
				$open_bracket = '[ ';
				$close_bracket = ' ]';
				$read_full = $user->lang['READ_FULL'];
			}
			else
			{
	      	      $open_bracket = '';
	      	      $close_bracket = '';
	      	      $read_full = '';
			}
			
			$template->assign_block_vars('news_row', array(
				'ATTACH_ICON_IMG'	=> ($fetch_news[$i]['attachment']) ? $user->img('icon_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
				'TITLE'				=> $fetch_news[$i]['topic_title'],
				'POSTER'			=> $fetch_news[$i]['username'],
				'U_USER_PROFILE'	=> (($fetch_news[$i]['user_type'] == USER_NORMAL || $fetch_news[$i]['user_type'] == USER_FOUNDER) && $fetch_news[$i]['user_id'] != ANONYMOUS) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $fetch_news[$i]['user_id']) : '',
				'TIME'				=> $fetch_news[$i]['topic_time'],
				'TEXT'				=> $fetch_news[$i]['post_text'],
				'REPLIES'			=> $fetch_news[$i]['topic_replies'],
				'U_VIEW_COMMENTS'	=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?t=' . $fetch_news[$i]['topic_id'] . '&amp;f=' . $fetch_news[$i]['forum_id']),
				'U_POST_COMMENT'	=> append_sid($phpbb_root_path . 'posting.' . $phpEx . '?mode=reply&amp;t=' . $fetch_news[$i]['topic_id'] . '&amp;f=' . $fetch_news[$i]['forum_id']),
				'U_READ_FULL'		=> append_sid($_SERVER['PHP_SELF'] . '?article=' . $i),
				'L_READ_FULL'		=> $read_full,
				'OPEN'				=> $open_bracket,
				'CLOSE'				=> $close_bracket,
				'S_NOT_LAST'		=> ($i < count($fetch_news) - 1) ? true : false,
				'S_POLL'			=> $fetch_news[$i]['poll'],
				'MINI_POST_IMG'		=> $user->img('icon_post_target', 'POST'),
				)
        	);
		}
	}

	$template->assign_vars(array(
		'S_DISPLAY_NEWS_LIST'	=> true
		)
	);
}
else if ($CFG['news'] == true)
{
	$fetch_news = phpbb_fetch_posts($CFG['news_forum'], $CFG['number_of_news'], 0, 0, ($CFG['show_all_news']) ? 'news_all' : 'news');

	$i = intval($HTTP_GET_VARS['article']);

	$template->assign_block_vars('news_row', array(
		'ATTACH_ICON_IMG'	=> ($fetch_news[$i]['attachment']) ? $user->img('icon_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
		'TITLE'				=> $fetch_news[$i]['topic_title'],
		'POSTER'			=> $fetch_news[$i]['username'],
		'TIME'				=> $fetch_news[$i]['topic_time'],
		'TEXT'				=> $fetch_news[$i]['post_text'],
		'REPLIES'			=> $fetch_news[$i]['topic_replies'],
		'U_VIEW_COMMENTS'	=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?t=' . $fetch_news[$i]['topic_id']),
		'U_POST_COMMENT'	=> append_sid($phpbb_root_path . 'posting.' . $phpEx . '?mode=reply&amp;t=' . $fetch_news[$i]['topic_id'] . '&amp;f=' . $fetch_news[$i]['forum_id']),
		'S_POLL'			=> $fetch_news[$i]['poll']
		)
	);

	$template->assign_vars(array(
		'S_DISPLAY_NEWS_LIST'	=> true
		)
	);
}

// Assign specific vars
$template->assign_vars(array(
	'L_NEWS'			=> $user->lang['NEWS'],
	'L_NO_NEWS'			=> $user->lang['NO_NEWS'],
	
	'L_POSTED_BY'		=> $user->lang['POSTED_BY'],
	'L_COMMENTS'		=> $user->lang['COMMENTS'],
	'L_VIEW_COMMENTS'	=> $user->lang['VIEW_COMMENTS'],
	'L_POST_REPLY'		=> $user->lang['POST_REPLY']
	)
);

?>
