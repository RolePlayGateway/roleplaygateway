<?php
/*
*
* @name announcements.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: announcements.php,v 1.1 2007/04/14 02:18:10 angelside Exp $
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
// Fetch Posts for announcements from bb3portal/includes/functions.php if we want to see the announcements
//
if($CFG['announcements'] == true)
{
	$fetch_announcements = phpbb_fetch_posts('', $CFG['number_of_announcements'], $CFG['announcements_length'], $CFG['announcements_day'], 'announcements');

	if ( (!intval($CFG['global_announcements_forum'])) && (count($fetch_announcements) > 0) )
	{
		$sql = 'SELECT forum_id FROM ' . FORUMS_TABLE . ' WHERE forum_type = 1';
		if(!($result = $db->sql_query_limit($sql, '1')))
		{
			die('Could not query forum information');
		}
		$row = $db->sql_fetchrow($result);		
		$CFG['global_announcment_forum'] =  $row['forum_id'];
	}
	
	for ($i = 0; $i < count($fetch_announcements); $i++)
	{
		$a_fid = (intval($fetch_announcements[$i]['forum_id'])) ? $fetch_announcements[$i]['forum_id'] : $CFG['global_announcements_forum'];
		$template->assign_block_vars('announcements_row', array(
			'ATTACH_ICON_IMG'	=> ($fetch_announcements[$i]['attachment']) ? $user->img('icon_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
            'TITLE'				=> $fetch_announcements[$i]['topic_title'],
            'POSTER'			=> $fetch_announcements[$i]['username'],
			'U_USER_PROFILE'	=> (($fetch_announcements[$i]['user_type'] == USER_NORMAL || $fetch_announcements[$i]['user_type'] == USER_FOUNDER) && $fetch_announcements[$i]['user_id'] != ANONYMOUS) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $fetch_announcements[$i]['user_id']) : '',
      		'TIME'				=> $fetch_announcements[$i]['topic_time'],
            'TEXT'				=> $fetch_announcements[$i]['post_text'],
            'REPLIES'			=> $fetch_announcements[$i]['topic_replies'],
            'U_VIEW_COMMENTS'	=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?t=' . $fetch_announcements[$i]['topic_id'] . '&amp;f=' . $a_fid),
            'U_POST_COMMENT'	=> append_sid($phpbb_root_path . 'posting.' . $phpEx . '?mode=reply&amp;t=' . $fetch_announcements[$i]['topic_id'] . '&amp;f=' . $a_fid),
            'S_NOT_LAST'		=> ($i < count($fetch_announcements) - 1) ? true : false,
			'S_POLL'			=> $fetch_announcements[$i]['poll'],
			'MINI_POST_IMG'		=> $user->img('icon_post_target', 'POST'),
			)
		);
	}

	// Assign specific vars
	$template->assign_vars(array(
		'S_DISPLAY_ANNOUNCEMENTS_LIST'=> (count($fetch_announcements) == 0 || isset($HTTP_GET_VARS['article'])) ? false : true,
		'L_ANNOUNCEMENTS'	=> $user->lang['ANNOUNCEMENTS'],
		'L_POSTED_BY'		=> $user->lang['POSTED_BY'],
		'L_COMMENTS'		=> $user->lang['COMMENTS'],
		'L_VIEW_COMMENTS'	=> $user->lang['VIEW_COMMENTS'],
		'L_POST_REPLY'		=> $user->lang['POST_REPLY']
		)
	);
}

?>
