<?php
//require('begin_caching.php');
/*
*
* @name rss.php
* @package phpBB3
* @version $Id: rss.php,v 1.0 2006/11/27 22:29:16 angelside Exp $
* @copyright (c) Canver Software
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/
define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$cacheTTL = 3600;

// Begin Configuration Section
$CFG['exclude_forums'] = '';
$CFG['max_topics'] = '20';
// End Configuration Section


// If not set, set the output count to max_topics
$count = request_var('count', 0);
$count = ( $count == 0 ) ? $CFG['max_topics'] : $count;

$forumid = request_var('fid', '');
$topicid = request_var('tid', '');
$userid = request_var('uid', '');
$posttype = request_var('type', '');


$mode = request_var('mode', '');

// Zeichen
//$chars = request_var('chars', 200);
//if($chars<0 || $chars>500) $chars=500; //Maximum
//$type = request_var('type', 'latest');

// [+] define path
// Create main board url (some code borrowed from functions_post.php)
// We have to generate a full HTTP/1.1 header here since we can't guarantee to have any of the information
$script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
if (!$script_name)
{
	$script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
}

$script_path = "";

$server_name = trim($config['server_name']);
$server_protocol = ( $config['cookie_secure'] ) ? 'https://' : 'http://';
$server_port = ( $config['server_port'] <> 80 ) ? ':' . trim($config['server_port']) . '' : '';

$url = $server_protocol . $server_name . $server_port;
//$url .= ( $script_path != '' ) ? $script_path . '' : '';

$index_url = $server_protocol . $server_name . $server_port;

// [-] define path
//
// Strip all BBCodes and Smileys from the post
//
function strip_post($text, $uid)
{
	$text = preg_replace("#\[\/?[a-z0-9\*\+\-]+(?:=.*?)?(?::[a-z])?(\:?$uid)\]#", '', $text); // for BBCode
	$text = preg_replace('#<!\-\- s(.*?) \-\-><img src="\{SMILIES_PATH\}\/.*? \/><!\-\- s(.*?) \-\->#', '', $text); // for smileys
	$text = str_replace('&amp;#', '&#', htmlspecialchars($text, ENT_QUOTES)); // html format

	return $text;
}

// Exclude forums
$sql_where = '';
if ($CFG['exclude_forums'])
{
	$exclude_forums = explode(',', $CFG['exclude_forums']);
	foreach ($exclude_forums as $i => $id)
	{
		if ($id > 0)
		{
			$sql_where .= ' AND p.forum_id != ' . trim($id);
		}
	}
}
if ($forumid != '')
{
	$select_forums = explode(',', $forumid);
	$sql_where .= ( sizeof($select_forums)>0 ) ? ' AND gateway_topics.forum_id IN (' . (int) $db->sql_escape($forumid) . ')' : '';
}
if ($topicid != '')
{
	$select_topics = explode(',', $topicid);
	$sql_where .= ( sizeof($select_topics)>0 ) ? ' AND gateway_topics.topic_id = ' . (int) $db->sql_escape($topicid) . '' : '';
}
if ($userid != '')
{
	$select_users = explode(',', $userid);
	$sql_where .= ( sizeof($select_users)>0 ) ? ' AND gateway_topics.topic_poster IN (' . (int) $db->sql_escape($userid) . ')' : '';	
}
if ($posttype != '')
{
	$post_types = explode(',', $posttype);
	$sql_where .= ( sizeof($post_types)>0 ) ? 'AND (t.topic_id = p.topic_id) AND t.topic_type = (' . $db->sql_escape($posttype) . ')' : '';	
	
	$post_type_extra_sql = ', '.TOPICS_TABLE . ' as t';
} else {
	$post_type_extra_sql = "";
}

$output = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
$output .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' . "\n";
$output .= '<channel>' . "\n";
$output .= '<title>' . strip_tags($config['sitename']) . '</title>' . "\n";
$output .= '<link>' . $index_url . '</link>' . "\n";
$output .= '<description>' . strip_tags($config['site_desc']) . '</description>' . "\n";

switch ($mode)
{
	default:
		// SQL posts table
		/*
		// Commented this out to change RSS feed to show topics ~Eric M, 3/10/09
		
		$sql = 'SELECT p.poster_id, p.post_subject, p.post_text, p.bbcode_uid, p.bbcode_bitfield, p.topic_id, p.forum_id, p.post_time, p.post_id, f.forum_name, u.username
				FROM ' . POSTS_TABLE . ' as p,
					' . FORUMS_TABLE . ' as f,
					' . USERS_TABLE . ' as u' . $post_type_extra_sql .'
				WHERE (u.user_id = p.poster_id)
				AND p.post_approved = 1
				AND (f.forum_id = p.forum_id)
				' . $sql_where . '
				ORDER BY post_time DESC';
		// Changed to below $sql
		*/

		if (!$sql_where) {
			$sql_where = "";
		}
			
		$sql = 'SELECT gateway_topics.topic_id,gateway_topics.forum_id,topic_title,topic_time,username,post_text,username,bbcode_uid,bbcode_bitfield
					FROM gateway_topics
						INNER JOIN gateway_posts
							ON gateway_topics.topic_first_post_id = gateway_posts.post_id
						INNER JOIN gateway_users
							ON gateway_topics.topic_poster = gateway_users.user_id
					WHERE topic_approved = 1
					' . $sql_where . '
					ORDER BY topic_time DESC';

		$result = $db->sql_query_limit($sql, $count, null, $cacheTTL);
	break;
	
	case "comments":
	
		$sql = 'SELECT topic_first_post_id FROM gateway_topics WHERE topic_id = '.$db->sql_escape($topicid);
		$first_post_result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($first_post_result)) {
		
			$sql = 'SELECT post_id,post_subject,post_text,post_time,bbcode_uid,post_username
						FROM ' . POSTS_TABLE . '
						WHERE topic_id = '.(int) $db->sql_escape($topicid).'
							AND post_id <> "'.$row['topic_first_post_id'].'" ORDER BY post_time DESC';
			$result = $db->sql_query($sql);
			
		}
		
		$db->sql_freeresult($first_post_result);
	break;
	/*
	case "members":
		$sql = 'SELECT * FROM ' . USERS_TABLE . ' ORDER BY user_regdate DESC';
		$result = $db->sql_query_limit($sql, $count);
	break;
	*/
	
	case 'roleplays':
		$sql = 'SELECT url,title,description,introduction,username,user_id,created
					FROM rpg_roleplays r
						INNER JOIN gateway_users u
							ON r.owner = u.user_id
					WHERE r.status = "Open"
					ORDER BY created DESC';

		$result = $db->sql_query_limit($sql, $count);		
	break;

}

while( ($row = $db->sql_fetchrow($result)) )
{

	switch ($mode)
	{
		default:
			if (!$auth->acl_get('f_list', $row['forum_id']))
			{
				if ($row['forum_id'] != 0) {


					// if the user does not have permissions to list this forum, skip everything until next branch
					continue;
				}
			}
			$topic_id = $row['topic_id'];
			$forum_id = $row['forum_id'];
			$title = $row['topic_title'];
			$time = date('r', $row['topic_time']);
			$viewtopic = "$url/viewtopic.$phpEx?f=" . $row['forum_id'] . '&amp;t=' . $row['topic_id'];
			$link = $viewtopic;
			$creator = $row['username'];
			
			$commentrss = "$url/rss.php?mode=comments&amp;tid=".$row['topic_id'];
			
			$description = $row['post_text'];
			// $description = censor_text($description);
			// $description = str_replace("\n", '<br />', $description);
			
			
			$description = generate_text_for_display($description, $row['bbcode_uid'], $row['bbcode_bitfield'], 7);

			
			
			// $description = strip_post($description, $row['bbcode_uid']);

		break;
		
		case 'roleplays':
			$title 			= $row['title'];
			$time 			= date('r', strtotime($row['created']));
			$creator 		= $row['username'];
			$description 	= $row['description'];
			
			$link 			= 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/';
		break;
		
		
		case "comments":
		
			$topic_id = $row['post_id'];
			$forum_id = "";
			$title = $row['post_subject'];
			$time = date('r', $row['post_time']);
			$viewtopic = "$url/viewtopic.$phpEx?p=" . $row['post_id'] . "#p" .$row['post_id'];
			$link = $viewtopic;
			$creator = $row['post_username'];

			$commentrss = "";
			
			$description = $row['post_text'];
			$description = generate_text_for_display($description, $row['bbcode_uid'], $row['bbcode_bitfield'], 7);
/* 			$description = censor_text($description);
			$description = str_replace("\n", '<br />', $description);
			$description = strip_post($description, $row['bbcode_uid']);		 */
			
		
		break;
		
		/*
		case "members":
			$title = $row['username'];
			$time = date('r', $row['user_regdate']);
			$link = $url . "/member".$row['user_id'].".html";
			$creator = $row['user_email'];
			
			
			// optional fields
			$messengers = (empty($row['user_icq'])) ? "" : $row['username'] ." has ICQ! You can reach them at ICQ #".$row['user_icq']."\n";
			$messengers .= (empty($row['user_aim'])) ? "" : $row['username'] ." has AIM! You can reach them at: ".$row['user_aim']."\n";
			$messengers .= (empty($row['user_msnm'])) ? "" : $row['username'] ." has MSN! You can reach them at: ".$row['user_msnm']."\n";
			$messengers .= (empty($row['user_yim'])) ? "" : $row['username'] ." has Yahoo! You can reach them at: ".$row['user_yim']."\n";
			$messengers .= (empty($row['user_jabber'])) ? "" : $row['username'] ." has Jabber or Google Talk! You can reach them at: ".$row['user_jabber']."\n";
			$messengers .= (empty($row['user_website'])) ? "" : $row['username'] ." listed their website as: ".$row['user_website']."\n";
			
			$contact_info = ($messengers == "") ? $row['username'] ." hasn't listed any contact information, but you can still reach them by email. at ".$row['user_email'] : $messengers;
			
			$description = "<img src=\"".$url."/download.php?avatar=".$row['user_id'].".jpg\" alt=\"".$row['username']."\" style=\"float:left;\" />
				".$contact_info;
			$description = censor_text($description);
			$description = str_replace("\n", '<br />', $description);
		break;
		*/
	}
	
	
	$output .= "<item>\n";
	$output .= "\t<title>$title</title>\n";
	$output .= "\t<description>" . $description . "</description>\n";
	$output .= "\t<link>$link</link>\n";
	$output .= "\t<guid isPermaLink=\"false\">$link</guid>\n";
	$output .= "\t<dc:creator>".$creator."</dc:creator>\n";
	$output .= "\t<pubDate>$time</pubDate>\n";
	$output .= (strlen($commentrss) >= 1) ? "\t<wfw:commentRss>$commentrss</wfw:commentRss>\n" : "";
	$output .= "</item>\n\n";
}

$db->sql_freeresult($result);

$output .= "</channel>\n</rss>";

header('Content-Type: text/xml; charset=utf-8');
echo $output;
//require('end_caching.php');
?>
