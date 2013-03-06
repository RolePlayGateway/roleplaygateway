<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
// PRS
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

//ini_set('display_errors', true);
//error_reporting(E_ALL);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$mode = request_var('mode','');

$roleplay		= request_var('roleplay_id',0);
$location 		= request_var('location_id',0);
$contentID 		= request_var('content_id',0);
$lastID 		= request_var('lastID',0);
$format 		= request_var('format','html');

if ($roleplay == 0) {
	header('HTTP/1.0 503 Service Unavailable');
	die('Must specify roleplay.');
}

do_headers();

$start 		= @request_var('start',0);
$limit   	= @request_var('limit', 25);

$limit 		= ($limit > 10) ? 10 : $limit;

switch ($mode) {

	case 'activity':

		if ($location > 0) {
			$sql = '(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a
						LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
						LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 
					 WHERE a.id = '.(int) $contentID.' AND a.place_id = '.  (int) $location . ')
					 
					 
					 UNION DISTINCT
					 
					(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a USE INDEX (PRIMARY)
						LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
						LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 
					 WHERE a.id < '.(int) $contentID.' AND a.place_id = '.  (int) $location . ' ORDER BY a.written DESC LIMIT 10)
					 
					 UNION DISTINCT
					 
					(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a USE INDEX (PRIMARY)
						LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
						LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 
					 WHERE a.id > '.(int) $contentID.' AND a.place_id = '.  (int) $location . ' ORDER BY a.written ASC LIMIT 10)	

						ORDER BY written DESC;
					 
					 ';		
		} else {
			$sql = '(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a
						LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
						LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 
					 WHERE a.id = '.(int) $contentID.' AND a.roleplay_id = '.  (int) $roleplay . ')
					 
					 
					 UNION DISTINCT
					 
					(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a USE INDEX (PRIMARY) 
						LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
						LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 
					 WHERE a.id < '.(int) $contentID.' AND a.roleplay_id = '.  (int) $roleplay . ' ORDER BY a.written DESC LIMIT 10)
					 
					 UNION DISTINCT
					 
					(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a USE INDEX (PRIMARY)
						LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
						LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 
					 WHERE a.id > '.(int) $contentID.' AND a.roleplay_id = '.  (int) $roleplay . ' ORDER BY a.written ASC LIMIT 10)	

						ORDER BY written DESC;
					 
					 ';			

		}
		
	// trigger_error($sql);

	break;
	
	case 'stream':
	
		if ($lastID > 0) {
		
			$sql = 'SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a
							LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
							LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 WHERE a.id > '. (int) $lastID.' AND a.roleplay_id = '.  (int) $roleplay .' ORDER BY a.written DESC LIMIT 20';
						 
			if ($location > 0) {
				$sql = 'SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a
							LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
							LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 WHERE a.id > '. (int) $lastID.' AND a.roleplay_id = '.  (int) $roleplay .' AND a.place_id = '. (int) $location .' ORDER BY a.written DESC LIMIT 20';			
			}
		
		} else {
	
			$sql = 'SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a USE INDEX (roleplay_id)
							LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
							LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
						 WHERE a.roleplay_id = '.  (int) $roleplay .' ORDER BY a.written DESC LIMIT 20';

		}
		
	break;

}

$result = @$db->sql_query($sql);

while ($content_row = $db->sql_fetchrow($result)) {

	$lastID = ($lastID < $content_row['id']) ? $content_row['id'] : $lastID;

	if ($character_id = $content_row['character_id']) {

		$sql = 'SELECT id,name,url FROM rpg_characters WHERE id = '. (int) $character_id ;
		$character_result = $db->sql_query($sql);
		$character = $db->sql_fetchrow($character_result);
		$db->sql_freeresult($character_result);
	}

	$template->assign_block_vars('activity', array(
		'S_BBCODE_ALLOWED'		=> true,
		'S_SMILIES_ALLOWED'		=> true,
		'S_CAN_EDIT'			=> ((($auth->acl_get('m_')) || ($content_row['author_id'] == $user->data['user_id'])) && ($content_row['type'] != 'Dialogue')) ? true : false,
		'S_IS_DIALOGUE'			=> ($content_row['type'] == 'Dialogue') ? true : false,
		'ID'	 				=> $content_row['id'],
		'AUTHOR'	 			=> get_username_string('full', @$content_row['user_id'], @$content_row['username']),
		'PLAYER_ID' 			=> @$content_row['user_id'],
		'LOCATION' 				=> @$content_row['place'],
		'LOCATION_ID' 			=> @$content_row['place_id'],
		'LOCATION_URL' 			=> urlify(@$content_row['place']),
		'CONTENT'				=> generate_text_for_display($content_row['text'], $content_row['bbcode_uid'], $content_row['bbcode_bitfield'], 7),
		'TIME_AGO'				=> timeAgo(strtotime($content_row['written'])),
		'CHARACTER_NAME'		=> $character['name'],
		'CHARACTER_URL'			=> $character['url'],
		'TIME_ISO'				=> date('c',strtotime($content_row['written'])),
	));

		$sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $content_row['id'] . '';
		$tags_result = $db->sql_query($sql);
		while ($tags_row = $db->sql_fetchrow($tags_result)) {
			$template->assign_block_vars('activity.characters', array(
				'ID'		=> $tags_row['id'],
				'NAME'		=> $tags_row['name'],
				'URL'		=> $tags_row['url'],
				'SYNOPSIS'	=> $tags_row['synopsis'],
			));	
		}
		$db->sql_freeresult($tags_result); 


	$activity = ($location > 0) ? $row['place'] : "Activity";

	$location_description = ($location > 0) ? $row['description'] : "";
}
$db->sql_freeresult($result);

// get some last minute info
$sql = 'SELECT url FROM rpg_roleplays WHERE id = '.(int) $roleplay;
$result = $db->sql_query($sql);
$roleplay_url = $db->sql_fetchfield('url');
$db->sql_freeresult($result);

$template->assign_vars(array(
	'URL'		=>	$roleplay_url,
	'LAST_ID'	=>	$first_row['id']
));


page_header('Activity Stream');

$template->set_filenames(array(
	'body' => 'roleplay_stream_body.html')
);		
page_footer();

function do_headers() {

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
		header("Cache-Control: no-cache, must-revalidate" ); 
		header("Pragma: no-cache" );
		header("Content-Type: text/html; charset=utf-8");
		
}

?>
