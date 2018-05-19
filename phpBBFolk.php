<?php
/** 
*
* @package phpBBFolk
* @version $Id: bbatgs.php,v 1.506 2007/09/24 15:26:38 nanothree Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$mode = request_var('mode', '');

//error_reporting(0);

switch($mode){


	case 'add_tag':
		if($user->data['user_id'] == ANONYMOUS){
			login_box('', $user->lang['ADD_TAGS_NOT_LOGGED']);
		}
		
		
		// Initial var setup
		$forum_id		= request_var('f', 0);
		$topic_id		= request_var('t', 0);
		$roleplay_id	= request_var('r', 0);
		$tags 			= utf8_normalize_nfc(request_var('tags', '', true));
		
		//get user input
		if(trim($tags) != '')
		{
			$tag_outcome = insert_tags($tags, $topic_id);
			
			//echo '<pre>';
			//var_dump($tag_outcome);
			
			$meta_info = append_sid("http://www.roleplaygateway.com/viewtopic.$phpEx", 'f=' . $forum_id . '&t=' . $topic_id);
			$message = sprintf($user->lang['PBF_ADD_TAGS_DONE'], $tag_outcome['added'], $tag_outcome['dups']) . '<br /><br />' . sprintf($user->lang['RETURN_TOPIC'], '<a href="' . $meta_info . '">', '</a>');
			meta_refresh(3, $meta_info);
		}
		else
		{
			$meta_info = append_sid("http://www.roleplaygateway.com/viewtopic.$phpEx", 'f=' . $forum_id . '&t=' . $topic_id .'#phpBBFolk_input');
			$message = $user->lang['PBF_ADD_TAGS_NO_TAGS'] . '<br /><br />' . sprintf($user->lang['RETURN_TOPIC'], '<a href="' . $meta_info . '">', '</a>');
		}
		
		trigger_error($message);
	break;
	case 'search':
	default:
	
		//error_reporting(0);
	
		$search_tag = utf8_normalize_nfc(request_var('tag', '', true));
		
		//trigger_error($search_tag);
		
		if($search_tag == '')
		{
		
			$config['pbf_max_font'] = 50;
		
			$template->assign_vars(array(
				'S_TAG_SEARCH_ACTION'		=> append_sid("http://www.roleplaygateway.com/tag/", 'mode=search'),
				'S_SEARCH_STRING'			=> $search_tag,
				'BIG_TAG_CLOUD'					=> get_tag_cloud($config['pbf_min_font'], $config['pbf_max_font'], $config['pbf_colour1'], $config['pbf_colour2'], 1000),
				)
			);		
			page_header($user->lang['PBF_SEARCH_PAGE_TITLE']);
					
			$template->set_filenames(array(
				'body' => 'phpBBFolk_search.html')
			);	
				
			page_footer();			
		}
		else
		{
			$start = request_var('start', 0);
			
			$result_set = search_tags($search_tag, $start);

			//echo '<pre>';
			//echo var_dump($result_set);

			$topics_count = get_num_rows($search_tag);
			
			
			$sql = "SELECT count(*) as count FROM gateway_tags WHERE tag = '".$search_tag."'";
			$result = $db->sql_query($sql);
			$roleplay_count = $db->sql_fetchfield('count');
	
			if (($topics_count > 0) || ($roleplay_count > 0))
			{	
				foreach ($result_set as $row)
				{		
					$topic_id 		= $row['topic_id'];
					$forum_id 		= $row['forum_id'];
					$roleplay_id 	= $row['roleplay_id'];
					
					if ($roleplay_id > 0) {
						$view_topic_url = "http://www.roleplaygateway.com/roleplay/".$roleplay_url."/";
					
						$sql = 'SELECT count(*) as posts FROM rpg_content WHERE roleplay_id = '.$row['id'];
						$db->sql_query($sql);
						$row['posts'] = $db->sql_fetchfield('posts');	
						
						$sql = 'SELECT count(*) as characters FROM rpg_characters WHERE roleplay_id = '.$row['id'];
						$db->sql_query($sql);
						$row['characters'] = $db->sql_fetchfield('characters');	
						
						$sql = 'SELECT average_words FROM rpg_roleplay_stats WHERE roleplay_id = '.$row['id'];
						$db->sql_query($sql);
						$row['words'] = $db->sql_fetchfield('average_words');	
						
						$sql = 'SELECT written FROM rpg_content WHERE roleplay_id = '.$row['id'].' ORDER BY written DESC LIMIT 1';
						$db->sql_query($sql);
						$row['last_activity'] = strtotime($db->sql_fetchfield('written'));
					
					
						$template->assign_block_vars('roleplayrow', array(
							'ID'						=> $row['id'],
							'URL'						=> 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/',
							'TITLE'						=> censor_text($row['title']),
							'TAGS'						=> @display_roleplay_tags(@get_roleplay_tags($row['id'])),
							'OWNER_USERNAME'			=> get_username_string('full', $row['owner'], $row['username']),
							'DESCRIPTION'				=> $row['description'],
							'POSTS'						=> $row['posts'],
							'CHARACTERS'				=> $row['characters'],
							'WORDS'						=> $row['words'],
							'LAST_ACTIVITY'				=> $user->format_date($row['last_activity']),
							)
						);
					
					} else {
						
						// This will allow the style designer to output a different header
						// or even separate the list of announcements from sticky and normal topics
						$s_type_switch_test = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;
				
						// Replies
						$replies = ($auth->acl_get('m_approve', $forum_id)) ? $row['topic_replies_real'] : $row['topic_replies'];
				
						if ($row['topic_status'] == ITEM_MOVED)
						{
							$topic_id = $row['topic_moved_id'];
							$unread_topic = false;
						}
						else
						{
							$unread_topic = (isset($topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
						}
				
						// Get folder img, topic status/type related information
						$folder_img = $folder_alt = $topic_type = '';
						topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);
				
						// Generate all the URIs ...
						$view_topic_url = append_sid("http://www.roleplaygateway.com/viewtopic.$phpEx", 'f=' . (($row['forum_id']) ? $row['forum_id'] : $forum_id) . '&amp;t=' . $topic_id);
				
						$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
						$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
						$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("http://www.roleplaygateway.com/mcp.$phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $user->session_id) : '';					
						//echo '<pre>';
						//var_dump($row);
						//exit();
						$template->assign_block_vars('topicrow', array(
							'FORUM_ID'					=> $forum_id,
							'TOPIC_ID'					=> $topic_id,
							'ROLEPLAY_ID'				=> $topic_id,
							'TOPIC_AUTHOR'				=> get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], @$row['topic_first_poster_colour']),
							'TOPIC_AUTHOR_COLOUR'		=> get_username_string('colour', $row['topic_poster'], $row['topic_first_poster_name'], @$row['topic_first_poster_colour']),
							'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], @$row['topic_first_poster_colour']),
							'FIRST_POST_TIME'			=> $user->format_date($row['topic_time']),
							'LAST_POST_SUBJECT'			=> censor_text($row['topic_last_post_subject']),
							'LAST_POST_TIME'			=> $user->format_date($row['topic_last_post_time']),
							'LAST_VIEW_TIME'			=> $user->format_date($row['topic_last_view_time']),
							'LAST_POST_AUTHOR'			=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
							'LAST_POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
							'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				
							'PAGINATION'		=> topic_generate_pagination($replies, $view_topic_url),
							'REPLIES'			=> $replies,
							'VIEWS'				=> $row['topic_views'],
							'TOPIC_TITLE'		=> censor_text($row['topic_title']),
							'TOPIC_TYPE'		=> $topic_type,
				
							'TOPIC_FOLDER_IMG'		=> $user->img($folder_img, $folder_alt),
							'TOPIC_FOLDER_IMG_SRC'	=> $user->img($folder_img, $folder_alt, false, '', 'src'),
							'TOPIC_FOLDER_IMG_ALT'	=> $user->lang[$folder_alt],
							'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
							'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
							'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
							'ATTACH_ICON_IMG'		=> ($auth->acl_get('u_download') && $auth->acl_get('f_download', $forum_id) && $row['topic_attachment']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
							'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',
				
							'S_TOPIC_TYPE'			=> $row['topic_type'],
							'S_USER_POSTED'			=> (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
							'S_UNREAD_TOPIC'		=> $unread_topic,
							'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $auth->acl_get('m_report', $forum_id)) ? true : false,
							'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
							'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
							'S_HAS_POLL'			=> (@$row['poll_start']) ? true : false,
							'S_POST_ANNOUNCE'		=> ($row['topic_type'] == POST_ANNOUNCE) ? true : false,
							'S_POST_GLOBAL'			=> ($row['topic_type'] == POST_GLOBAL) ? true : false,
							'S_POST_STICKY'			=> ($row['topic_type'] == POST_STICKY) ? true : false,
							'S_TOPIC_LOCKED'		=> ($row['topic_status'] == ITEM_LOCKED) ? true : false,
							'S_TOPIC_MOVED'			=> ($row['topic_status'] == ITEM_MOVED) ? true : false,
				
							'U_NEWEST_POST'			=> $view_topic_url . '&amp;view=unread#unread',
							'U_LAST_POST'			=> $view_topic_url . '&amp;p=' . $row['topic_last_post_id'] . '#p' . $row['topic_last_post_id'],
							'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
							'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], @$row['topic_first_poster_colour']),
							'U_VIEW_TOPIC'			=> append_sid("http://www.roleplaygateway.com/viewtopic.$phpEx", 'f=' . $forum_id . '&t=' . $topic_id),
							'U_MCP_REPORT'			=> append_sid("http://www.roleplaygateway.com/mcp.$phpEx", 'i=reports&amp;mode=reports&amp;f=' . $forum_id . '&amp;t=' . $topic_id, true, $user->session_id),
							'U_MCP_QUEUE'			=> $u_mcp_queue,
				
							'S_TOPIC_TYPE_SWITCH'	=> (@$s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test,
							'TAG_LIST'				=> get_tag_list($topic_id, 20))

						);
					}
				}
				$template->assign_vars(array(
					'PAGINATION'	=> generate_pagination(append_sid("http://www.roleplaygateway.com/phpBBFolk.$phpEx","tag=$search_tag"), $topics_count, $config['topics_per_page'], $start),
					'PAGE_NUMBER'	=> on_page($topics_count, $config['topics_per_page'], $start),
					'TOTAL_TOPICS'	=> (@$s_display_active) ? false : (($topics_count == 1) ? $user->lang['PBF_NUM_TOPIC'] : sprintf($user->lang['PBF_NUM_TOPICS'], $topics_count)),
					'S_DISPLAY_SEARCHBOX'		=> true,
					'S_TAG_SEARCH_ACTION'		=> append_sid("http://www.roleplaygateway.com/tag/", 'mode=search'),
					'S_SEARCH_STRING'			=> $search_tag,
					//'TAG_CLOUD'					=> get_tag_cloud()
					)
				);		
				page_header($search_tag. " | RolePlayGateway");
					
				$template->set_filenames(array(
					'body' => 'phpBBFolk_search_results.html')
				);	
				
				page_footer();			
			}else{
			
				$meta_info = append_sid("http://www.roleplaygateway.com/phpBBFolk.$phpEx");
				$message = sprintf($user->lang['PBF_NO_RESULTS'], $search_tag) . '<br /><br />' . sprintf($user->lang['PBF_RETURN_TO_SEARCH'], '<a href="' . $meta_info . '">', '</a>');
	
				trigger_error($message);		
			}
		}		
	break;
	case 'get_suggestions':
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
		header("Cache-Control: no-cache, must-revalidate" ); 
		header("Pragma: no-cache" );
		header("Content-Type: text/html; charset=utf-8");
		
			
		if (isset($_GET['tag']) && $_GET['tag'] != '') {
			//Get every page title for the site.
			$tag = utf8_normalize_nfc(request_var('tag', '', true));
						
			$sql_array =  array('SELECT'	 => 'tag',
								'FROM'		=> array(TAGS_TABLE => ''),
								'WHERE'		=> 'tag '. $db->sql_like_expression($tag.$db->any_char),
								'ORDER_BY' 	=> 'tag');
			
			$sql = $db->sql_build_query('SELECT_DISTINCT', $sql_array);
			
			$result 	= $db->sql_query($sql,3600);
			$res_set	= $db->sql_fetchrowset($result);
			//echo $sql;
			//echo '<pre>';
			//var_dump($res_set);
			$i = 0;
			$out = '';
			if(count($res_set) > 0){
				foreach($res_set as $suggestion){
					//Return each page title seperated by a newline.
					$out .= $suggestion['tag'] . "\n";
				}
						
				$out = substr($out, 0, strlen($out) - 1);
			}else{
				$out = 'No Suggestions';
			}
			echo $out;
		}
			
	break;
	case 'filter':
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
		header("Cache-Control: no-cache, must-revalidate" ); 
		header("Pragma: no-cache" );
		header("Content-Type: text/html; charset=utf-8");
		
		if ($user->data['is_bot'] || $user->data['user_id'] == ANONYMOUS) {
				header('HTTP/1.0 403 Forbidden');
				die('You are not permitted to use filters.');
		}
		
		switch ($_REQUEST['action']) {
			case 'ignore': $type = 'Ignored'; break;
			case "highlight": case 'favorite': $type = 'Favorite'; break;
			case 'delete': case 'remove': $type = 'Remove'; break;
			
			default:
				header('HTTP/1.0 503 Service Unavailable');
				die();
			break;
		
		}
		
		if (!$_REQUEST['tag']) {
			header('HTTP/1.0 503 Service Unavailable');
			die('Must specify tag.');
		}

		if ($type == 'Remove') {
			$sql = 'DELETE FROM rpg_user_filters WHERE tag = "'.$db->sql_escape(request_var('tag','')).'" AND user_id = '.$user->data['user_id'];
		} else {
			$sql = 'INSERT INTO rpg_user_filters (user_id,tag,type) 
								VALUES ('.$user->data['user_id'].', "'.$db->sql_escape(request_var('tag','')).'","'.$type.'")
						ON DUPLICATE KEY UPDATE type = "'.$type.'";';
		}
		$db->sql_query($sql);
		
		
		echo '1';
	break;
	case 'add':
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
		header("Cache-Control: no-cache, must-revalidate" ); 
		header("Pragma: no-cache" );
		header("Content-Type: text/html; charset=utf-8");
		
		if (!$_REQUEST['tag']) {
			header('HTTP/1.0 503 Service Unavailable');
			die('Must specify tag.');
		}		
		if (!$_REQUEST['roleplay_id']) {
			header('HTTP/1.0 503 Service Unavailable');
			die('Must specify roleplay.');
		}
		
		$sql = 'INSERT INTO gateway_tags (tag,roleplay_id) VALUES ("'.$db->sql_escape(request_var('tag','')).'",'.(int) request_var('roleplay_id',0).');';
		$db->sql_query($sql);
		
		echo '1';
		
	break;
}

function get_roleplay_tags($id) {

	global $db, $auth;

	$sql = "SELECT tag FROM gateway_tags WHERE roleplay_id = ".$id;

	$result     = $db->sql_query($sql);

	while( $row = $db->sql_fetchrow($result) )
	{
		$tags[] = '<a href="http://www.roleplaygateway.com/tag/'.$row['tag'].'">'.$row['tag'].'</a>';
	}
	
	$db->sql_freeresult($result);
	
	
	return $tags;

}

function display_roleplay_tags($tags) {

	$output = @implode(", ",$tags);

	return $output;
}

?>