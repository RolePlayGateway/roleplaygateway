<?php
define('IN_PHPBB', true);
define('PHPBB_ROOT_PATH','/var/www/vhosts/roleplaygateway.com/httpdocs/');
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

error_reporting(E_ALL);
ini_set('display_errors',true);


	$sql = 'SELECT * FROM ajax_chat_messages a WHERE userID = 2147483647 AND text LIKE "/transcriptBegin%";';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {
		$sql = 'SELECT id as end_id FROM ajax_chat_messages WHERE userID = 2147483647 AND channel = '.$row['channel']. ' AND id > '.$row['id'].' AND text LIKE "/transcriptEnd%" ORDER BY id ASC LIMIT 1';
		$end_result = $db->sql_query($sql);
		if ($end_log_id = $db->sql_fetchfield('end_id')) {
			
			echo("\n\n".$sql);
			
			$sql = 'INSERT IGNORE INTO rpg_transcript_queue (place_id,start_msg_id,end_msg_id) VALUES ('.$row['channel'].','.$row['id'].','.$end_log_id.')';
			echo("\n".$sql);
			if (!$db->sql_query($sql)) {
				echo "\ncouldn't insert queue message...";
			}
		}
	}

	// Get all the posts that have been edited or posted since the threshold.
	$sql = "SELECT * FROM rpg_transcript_queue WHERE content_id IS NULL";
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {
	
	
	
		echo "\nWorking on transcript beginning at chat id #".$row['start_msg_id']." and ending at ".$row['end_msg_id']."...";
	
		$sql = 'SELECT id,userID,userName,text,dateTime FROM ajax_chat_messages WHERE id >= '.$row['start_msg_id']. ' AND id <= '.$row['end_msg_id']. ' AND channel = '.$row['place_id']. ' AND userID <> 2147483647 AND text NOT LIKE "/ooc%" LIMIT 100';
		echo ("\n\n$sql");
		
		$transcript_result = $db->sql_query($sql);
		while ($message = $db->sql_fetchrow($transcript_result)) {
			if ($message['userID'] 		== 	2147483647) 							continue;
			if ($message['userName'] 	== 	getUsernameFromID($message['userID'])) 	continue;
			//strip_bbcode($text);
			
			$real_messages[$message['id']]['character'] = $message['userName'];
			$real_messages[$message['id']]['text'] 		= $message['text'];
			$real_messages[$message['id']]['dateTime'] 	= $message['dateTime'];			
			$real_messages[$message['id']]['type'] 		= ((stripos($message['text'],'/me')) === false) ? 'speech' : 'action';
			
			$characters[] = $message['userName'];
			
		}
		
		$row['roleplay_id'] = getRoleplayFromPlace($row['place_id']);
		
		
		foreach($real_messages as $message) {
			$content .= formatDialogue($message,$row['roleplay_id']);		
		}
		
		$sql = 'INSERT INTO rpg_content (roleplay_id,place_id,type,text,written,bbcode_bitfield,bbcode_uid)
					VALUES ('.$row['roleplay_id'].','.$row['place_id'].',"Dialogue","'.$db->sql_escape($content).'",NOW(),"","")';
		if ($db->sql_query($sql)) {
		
			$content_id = $db->sql_nextid();
		
			foreach ($characters as $character) {
				$sql = 'INSERT INTO rpg_content_tags (content_id,character_id) VALUES ('.$content_id.','.$character.')';
				$db->sql_query($sql);
			}
		
			$sql = 'UPDATE rpg_transcript_queue SET content_id = '.$content_id. ' WHERE start_msg_id = '.$row['start_msg_id']. ' AND end_msg_id = '.$row['end_msg_id'];
			$db->sql_query($sql);
					
			echo "\n All done!";

		}
		
		
		unset($real_messages);
		unset($content);
		unset($characters);
		
	}
	
	echo "\nNothing good happened. :[";


function formatDialogue($message,$roleplay) {
	
	$output = '<div class="'.$message['type'].'">';
	
	if ($message['type'] == 'speech') {
		$output .= getCharacterPortrait($message['character'],$roleplay);
	}
	
	if ($message['type'] == 'action') {
		$message['text'] = substr($message['text'],4);
	}
	
	$message['text'] = preg_replace("/\[color=(.*)](.*)\[\/color]/","$2",$message['text']);
	
	$output .= '<div>';
	
	if ($message['type'] == 'speech') {
		$output .= getCiteLink($message['character'],$roleplay).' said <q>'.$message['text'].'</q>';
	} else if ($message['type'] == 'action') {
		//$message['text'] = substr($message['text'],4);
	
		$output .= '<em>'.getCiteLink($message['character'],$roleplay).' '.$message['text'].'</em>';
	}
	$output .= '</div>';
	$output .= '</div>';
	
	return $output;
}

function getCiteLink($character,$roleplay) {
	return '<cite><a href="http://www.roleplaygateway.com/roleplay/'.getRoleplayURL($roleplay).'/characters/'.urlify($character).'/">'.$character.'</a></cite>';
}

function getCharacterPortrait($character,$roleplay) {
	return '<a href="http://www.roleplaygateway.com/roleplay/'.getRoleplayURL($roleplay).'/characters/'.urlify($character).'/"><img src="http://www.roleplaygateway.com/images/character_avatar.php?character_name='.urlify($character).'" alt="'.$character.'\'s Portrait" /></a>';
}
	
function getUsernameFromID($id) {
	global $db;
	
	$sql = 'SELECT username FROM gateway_users WHERE user_id = '.$id;
	$result = $db->sql_query($sql);
	
	return $db->sql_fetchfield('username');
}

function getRoleplayURL($id) {
	global $db;
	
	$sql = 'SELECT url FROM rpg_roleplays WHERE id = '.$id;
	$result = $db->sql_query($sql);
	
	return $db->sql_fetchfield('url');
}	

function getRoleplayFromPlace($id) {
	global $db;
	
	$sql = 'SELECT roleplay_id FROM rpg_places WHERE id = '.$id;
	$result = $db->sql_query($sql);
	
	return $db->sql_fetchfield('roleplay_id');
}

function calculate_flesch($text) {
	return (206.835 - (1.015 * average_words_sentence($text)) - (84.6 * average_syllables_word($text)));
}

function calculate_flesch_grade($text) {
	
	//echo "\nFormula: ((.39 * ".average_words_sentence($text).") + (11.8 * ".average_syllables_word($text).") - 15.59)";

	return ((.39 * average_words_sentence($text)) + (11.8 * average_syllables_word($text)) - 15.59);
}

function average_words_sentence($text) {
	//$sentences = strlen(preg_replace('/[^\.!?]/', '', $text));
	$sentences = strlen(preg_replace('/[^\.!?\n]/', '', $text));
	$words = strlen(preg_replace('/[^ ]/', '', $text));
	
	//echo "\nSentences: $sentences";
	//echo "\nWords: $words";
	
	if ($sentences < 1) { $sentences = 1; }
	
	$words_sentence = ($words/$sentences);
	
	if ($words_sentence > 50) {
		$words_sentence = 15;
	}
	
	return $words_sentence;
}

function average_syllables_word($text) {
	$syllables = 0;
	
	$text = preg_replace("/\.{4,}/","... ",$text);
	$text = preg_replace("/\n/",". ",$text);
	
	$words = explode(' ', $text);
	
	// If this is a one-word sentence,  do not waste time counting syllables, the average syllables per word will be too high.
	if (count($words) <= 2) {
		return 1;
	}
	
	for ($i = 0; $i < count($words); $i++) {
		$syllables = $syllables + count_syllables($words[$i]);
		//echo "\nThis Word: ".$words[$i];
		//echo "\nSyllables: ".$syllables;
	}
	
	return ($syllables/count($words));
}

function count_syllables($word) {
	  
	$subsyl = Array(
		'cial'
		,'tia'
		,'cius'
		,'cious'
		,'giu'
		,'ion'
		,'iou'
		,'sia$'
		,'.ely$'
	);
					 
	$addsyl = Array(
		'ia'
		,'riet'
		,'dien'
		,'iu'
		,'io'
		,'ii'
		,'[aeiouym]bl$'
		,'[aeiou]{3}'
		,'^mc'
		,'ism$'
		,'([^aeiouy])\1l$'
		,'[^l]lien'
		,'^coa[dglx].'
		,'[^gq]ua[^auieo]'
		,'dnt$'
	);
					  
	// Based on Greg Fast's Perl module Lingua::EN::Syllables
	$word = preg_replace('/[^a-z]/is', '', strtolower($word));
	$word_parts = preg_split('/[^aeiouy]+/', $word);
	foreach ($word_parts as $key => $value) {
		if ($value <> '') {
			$valid_word_parts[] = $value;
		}
	}
 
	$syllables = 0;
	// Thanks to Joe Kovar for correcting a bug in the following lines
	foreach ($subsyl as $syl) {
		$syllables -= preg_match('~'.$syl.'~', $word);
	}
	foreach ($addsyl as $syl) {
		$syllables += preg_match('~'.$syl.'~', $word);
	}
	if (strlen($word) == 1) {
		$syllables++;
	}
	$syllables += @count($valid_word_parts);
	$syllables = ($syllables == 0) ? 1 : $syllables;
	
	if ($syllables > 10) {
		$syllables = 5;
	}

	return $syllables;
}

function gunning_fog_score($text) {
	return ((average_words_sentence($text) + percentage_number_words_three_syllables($text)) * 0.4);
}

function percentage_number_words_three_syllables($text) {
	$syllables = 0;
	$words = explode(' ', $text);
	for ($i = 0; $i < count($words); $i++) {
		if (count_syllables($words[$i]) > 2) {
			$syllables ++;
		}
	}
	 
	$score = number_format((($syllables / count($words)) * 100));
	  
	return ($score);
}

function get_real_content($text) {

	if (!$uid)
	{
		$uid = '[0-9a-z]{5,}';
	}
	
	// Hoorah. 
	$text = preg_replace("/\[quote(?:=&quot;(.*?)&quot;)?(\:$uid)\](.*)\[\/quote?(\:$uid)\]/ie", ' ', $text);
	$text = preg_replace("/\[img(:$uid)?](.*)\[\/img(:$uid)?]/iUe", ' ', $text);
	$text = preg_replace("/\[code(:$uid)?](.*)\[\/code(:$uid)?]/iUe", ' ', $text);
	$text = preg_replace("/\[color=(\w)](.*)\[\/color]/iUe", '$2', $text);
	$text = preg_replace("/\[url(:$uid)?\](.*)\[\/url(:$uid)?\]/iUe", " ", $text);
	$text = preg_replace("/\[url(=(.*))?(:$uid)?\](.*)\[\/url(:$uid)?\]/iUe", ' ', $text);
	$text = preg_replace("/^(http:\/\/)/i", ' ', $text);
	
	// Use internal parsing to go ahead and convert URLs (because this is complicated. no need to do it twice)
	$text = make_clickable($text);
	
	// Now strip all HTML-enclosed content from the post
	$text = preg_replace('/\<a (.*)\>(.*)\<\/a\>/', ' ', $text);
	
	return $text;
}

?>