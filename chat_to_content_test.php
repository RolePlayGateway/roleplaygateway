<?php
define('IN_PHPBB', true);
define('PHPBB_ROOT_PATH','/var/www/html/');
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
// PRS
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors',true);


// We need to get the roleplay id as well so we know what context to pull the character from

$sql = 'SELECT m.id,m.channel,m.userID,m.userName,m.dateTime,m.text FROM ajax_chat_messages m
			LEFT OUTER JOIN rpg_content c
				ON c.old_chat_id = m.id
			WHERE m.id > (SELECT MAX(old_chat_id) FROM rpg_content) AND m.channel <> 0 and m.userID <> 2147483647
				AND m.channel < 500000000
				AND c.id IS NULL
				AND m.text NOT LIKE "/ooc%"
			ORDER BY m.id DESC';
$chat_result = $db->sql_query($sql);
while ($message = $db->sql_fetchrow($chat_result)) {

	echo "\nBeginning work on chat message ID #".$message['id'].'...';
	
	$sql = 'SELECT id,old_chat_id FROM rpg_content WHERE old_chat_id = '.$message['id'];
	$content_result = $db->sql_query($sql);
	if ($content = $db->sql_fetchrow($content_result)) {
		
		echo "...found content: ".$content['id'];
	
		$db->sql_freeresult($content_result);
		continue;
	}
	

	$sql = 'SELECT id,roleplay_id FROM rpg_places WHERE id = '.$message['channel'];
	$places_result = $db->sql_query($sql);
	while ($place = $db->sql_fetchrow($places_result)) {

		echo "\n\tFound place ID #".$place['id'].'...';

		$sql = 'SELECT username FROM gateway_users WHERE user_id = '.$message['userID'] .' LIMIT 1';
		$user_result = $db->sql_query($sql);
		$user = $db->sql_fetchrow($user_result);
		$db->sql_freeresult($user_result);

		echo 'User is '.$user['username'].', ';
		if ($message['userName'] == $user['username']) {
		
			echo '...username was equal to username.';
			$db->sql_freeresult($places_result);
			continue;
		}
	
		$sql = 'SELECT id,name,url FROM rpg_characters WHERE roleplay_id = '.$place['roleplay_id'].' AND name = "'.$db->sql_escape($message['userName']).'"';
		$character_result = $db->sql_query($sql);
		$character = $db->sql_fetchrow($character_result);
		$db->sql_freeresult($character_result);
		
		echo 'Character is '.$character['id'].', ';
		if ($character['id']) {
			$textParts = explode(' ', $message['text']);
			
			switch ($textParts[0]) {
				case '/me':
					// TODO: Add a link to character name (maybe? character tagging meets this need, I think.)
					$message['text'] = $character['name'] . substr($message['text'],3);
					$message['type'] = 'Action';
				break;
				default:
					$message['type'] = 'Dialogue';
				break;
			}	
		
			// Have I ever told you how much I hate phpBB's text parsing engnie?
			$text = utf8_normalize_nfc($message['text']);
			$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
			$allow_bbcode = $allow_urls = $allow_smilies = true;
			generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			
			$sql_ary = array(
				'roleplay_id'       => $place['roleplay_id'],
				'character_id'      => $character['id'],
				'author_id'      	=> $message['userID'],
				'type'      		=> $message['type'],
				'place_id'      	=> $place['id'],
				'text'              => $text,
				'written'           => $message['dateTime'],
				'bbcode_uid'        => $uid,
				'bbcode_bitfield'   => $bitfield,
				'old_chat_id'   	=> $message['id'],
			);
		
			$sql = 'INSERT INTO rpg_content ' . $db->sql_build_array('INSERT', $sql_ary);
			if (!$db->sql_query($sql)) {
				echo "Error inserting content.";
			}
			$content_id = $db->sql_nextid();
			
			echo "\n\t\tInserted as $content_id.";
			
		
		// Attempt to tag characters in this post.
		
			$sql_ary = array(
				'content_id'		=> $content_id,
				'character_id'      => $character['id'],
			);		
			
			$sql = 'INSERT INTO rpg_content_tags ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
		}

		
		// Tag extra characters 
		find_characters($message['text']);
	
	}
	$db->sql_freeresult($places_result);
}
$db->sql_freeresult($chat_result);

function find_characters($text) {

	global $db, $user;

	preg_match('/\[b](.*)\[\/b]/',$text,$matches);
	
	print_r($matches);
	
	die();

}

?>