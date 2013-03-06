<?php
/*
*
* @name functions.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: functions.php,v 1.4 2007/04/14 02:05:17 angelside Exp $
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

include_once($phpbb_root_path . 'includes/message_parser.'.$phpEx);

//
// Strip all BBCodes and Smileys from the post
//
/* Undefined variable: replacements
function strip_post($text, $uid, &$text_length)
{
	// for BBCode
	$text = preg_replace("#\[\/?[a-z0-9\*\+\-]+(?:=.*?)?(?::[a-z])?(\:?$uid)\]#", '', $text);
	
	// for smileys
	$text = preg_replace('#<!\-\- s(.*?) \-\-><img src="\{SMILIES_PATH\}\/.*? \/><!\-\- s(.*?) \-\->#', '', $text, -1, $replacements);

	$text_length += $replacements;

	return $text;	
}
*/

function phpbb_fetch_posts($forum_sql, $number_of_posts, $text_length, $time, $type)
{
	global $db, $user, $phpbb_root_path, $phpEx, $auth;
	
	$from_forum = ($forum_sql != '') ? 't.forum_id IN (' . $forum_sql . ') AND' : '';
	$post_time = ($time == 0) ? '' : 't.topic_time > ' . (time() - $time * 86400) . ' AND';

	if ($type == 'announcements')
	{
		$topic_type = '( t.topic_type = 2 OR t.topic_type = 3 ) AND';
	}
	else if ($type == 'news_all')
	{
		$topic_type = '';
	}
	else
	{
		$topic_type = 't.topic_type = 0 AND';
	}

	$sql = 'SELECT
			t.forum_id,
			t.topic_id,
			t.topic_time,
			t.topic_title,
			p.post_text,
			u.username,
			u.user_id,
			u.user_type,
			u.user_colour,
			t.topic_replies,
			p.bbcode_uid,
			t.forum_id,
			t.topic_poster,
			p.post_id,
			p.enable_smilies,
			p.enable_bbcode,
			p.enable_magic_url,
			p.bbcode_bitfield,
			p.bbcode_uid,
			t.topic_attachment,
			t.poll_title
		FROM
			' . TOPICS_TABLE . ' AS t,
			' . USERS_TABLE . ' AS u,
			' . POSTS_TABLE . ' AS p
		WHERE
			' . $topic_type . '
			' . $from_forum . '
			' . $post_time . '
			t.topic_poster = u.user_id AND
			t.topic_first_post_id = p.post_id AND
			t.topic_first_post_id = p.post_id AND
			t.topic_status <> 2 AND
			t.topic_approved = 1
		ORDER BY
			t.topic_time DESC';

	//
	// query the database
	//
	if(!($result = $db->sql_query_limit($sql, $number_of_posts)))
	{
		die('Could not query topic information');
	}

	//
	// fetch all postings
	//
	$posts = array();
	$i = 0;
	while ( ($row = $db->sql_fetchrow($result)) && ( ($i < $number_of_posts) || ($number_of_posts == '0') ) )
	{
		if ( ($auth->acl_get('f_read', $row['forum_id'])) || ($row['forum_id'] == '0') )
		{
			if ($row['user_id'] != ANONYMOUS && $row['user_colour'])
			{
				$row['username'] = '<b style="color:#' . $row['user_colour'] . '">' . $row['username'] . '</b>';
			}
		
			$posts[$i]['post_text'] = censor_text($row['post_text']);
			$posts[$i]['topic_id'] = $row['topic_id'];
			$posts[$i]['forum_id'] = $row['forum_id'];
			$posts[$i]['topic_replies'] = $row['topic_replies'];
			$posts[$i]['topic_time'] = $user->format_date($row['topic_time']);
			$posts[$i]['topic_title'] = $row['topic_title'];
			$posts[$i]['username'] = $row['username'];
			$posts[$i]['user_id'] = $row['user_id'];
			$posts[$i]['user_type'] = $row['user_type'];
			$posts[$i]['user_user_colour'] = $row['user_colour'];
			$posts[$i]['poll'] = ($row['poll_title'] != '') ? true : false;
			$posts[$i]['attachment'] = ($row['topic_attachment']) ? true : false;

			$len_check = $posts[$i]['post_text'];
			/* Undefined variable: replacements
			$len_check = strip_post($len_check, $row['bbcode_uid'], $text_length);*/

			if (($text_length != 0) && (strlen($len_check) > $text_length))
			{
				$posts[$i]['post_text'] = substr($len_check, 0, $text_length);
				$posts[$i]['post_text'] .= '...';
				$posts[$i]['striped'] = true;
			}

			include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
			$bbcode = new bbcode($row['bbcode_bitfield']);
			$posts[$i]['post_text'] = censor_text($posts[$i]['post_text']);

			$bbcode->bbcode_second_pass($posts[$i]['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield']);
			$posts[$i]['post_text'] = smiley_text($posts[$i]['post_text']);
			$posts[$i]['post_text'] = str_replace("\n", '<br />', $posts[$i]['post_text']);
			$i++;
		}
	}
	// return the result
	return $posts;
}

/**
* Censor title, return short title
*
* @param $title string title to censor
* @param $limit int short title character limit
*
*/
function character_limit(&$title, $limit = 0)
{
   $title = censor_text($title);
   if ($limit > 0)
   {
      return (strlen(utf8_decode($title)) > $limit + 3) ? truncate_string($title, $limit) . '...' : $title;
   }
   else
   {
      return $title;
   }
}

?>