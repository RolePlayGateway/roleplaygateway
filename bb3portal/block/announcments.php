<?php
/**
*
* @package bb3portal
* @version $Id: announcments.php,v 1.4 2007/04/12 06:19:39 angelside Exp $
* @author Sevdin Filiz destek@canversoft.com
* @copyright (c) 2007 Canver Software - www.canversoft.net
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
// Fetch Posts for announcments from bb3portal/includes/functions.php if we want to see the announcments
//
if($CFG['announcments'] == true)
{
	$fetch_announcments = phpbb_fetch_posts('', $CFG['number_of_announcments'], $CFG['announcments_length'], $CFG['announcments_day'], 'announcments');

	if ( (!intval($CFG['global_announcments_forum'])) && (count($fetch_announcments) > 0) )
	{
		$sql = 'SELECT forum_id FROM ' . FORUMS_TABLE . ' WHERE forum_type = 1';
		if(!($result = $db->sql_query_limit($sql, '1')))
		{
			die('Could not query forum information');
		}
		$row = $db->sql_fetchrow($result);		
		$CFG['global_announcment_forum'] =  $row['forum_id'];
	}
	
	for ($i = 0; $i < count($fetch_announcments); $i++)
	{
		$a_fid = (intval($fetch_announcments[$i]['forum_id'])) ? $fetch_announcments[$i]['forum_id'] : $CFG['global_announcments_forum'];
		$template->assign_block_vars('announcments_row', array(
			'ATTACH_ICON_IMG'	=> ($fetch_announcments[$i]['attachment']) ? $user->img('icon_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
            'TITLE'				=> $fetch_announcments[$i]['topic_title'],
            'POSTER'			=> $fetch_announcments[$i]['username'],
			'U_USER_PROFILE'	=> (($fetch_announcments[$i]['user_type'] == USER_NORMAL || $fetch_announcments[$i]['user_type'] == USER_FOUNDER) && $fetch_announcments[$i]['user_id'] != ANONYMOUS) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $fetch_announcments[$i]['user_id']) : '',
      		'TIME'				=> $fetch_announcments[$i]['topic_time'],
            'TEXT'				=> $fetch_announcments[$i]['post_text'],
            'REPLIES'			=> $fetch_announcments[$i]['topic_replies'],
            'U_VIEW_COMMENTS'	=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?t=' . $fetch_announcments[$i]['topic_id'] . '&amp;f=' . $a_fid),
            'U_POST_COMMENT'	=> append_sid($phpbb_root_path . 'posting.' . $phpEx . '?mode=reply&amp;t=' . $fetch_announcments[$i]['topic_id'] . '&amp;f=' . $a_fid),
            'S_NOT_LAST'		=> ($i < count($fetch_announcments) - 1) ? true : false,
			'S_POLL'			=> $fetch_announcments[$i]['poll'],
			'MINI_POST_IMG'		=> $user->img('icon_post_target', 'POST'),
			)
		);
	}

	// Assign specific vars
	$template->assign_vars(array(
		'S_DISPLAY_ANNOUNCMENTS_LIST'=> (count($fetch_announcments) == 0 || isset($HTTP_GET_VARS['article'])) ? false : true,
		'L_ANNOUNCMENTS'	=> $user->lang['ANNOUNCMENTS'],
		'L_POSTED_BY'		=> $user->lang['POSTED_BY'],
		'L_COMMENTS'		=> $user->lang['COMMENTS'],
		'L_VIEW_COMMENTS'	=> $user->lang['VIEW_COMMENTS'],
		'L_POST_REPLY'		=> $user->lang['POST_REPLY']
		)
	);
}

?>
