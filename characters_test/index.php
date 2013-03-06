<?php
// We start with this, to define that phpBB is to be used. If we don't, we'll get a security error throughout the rest of the code.
define('IN_PHPBB', true);
// Defines where we get the phpBB files from.  This script is served from a subdirectory ( /characters/ ), so it's "../" (up)
$phpbb_root_path = '../';
// Blah blah, php extension - automatically detected from this file.
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// And this is where we say, "Go get shit!" - it includes phpBB's basic libraries
include($phpbb_root_path . 'common.' . $phpEx);
// Start session management - d'dee dee.
$user->session_begin();
$auth->acl($user->data);
$user->setup();  
$character_name = request_var('name', ''); 
if ($character_name) { // yay, we have a character name that isn't null.
	// check to see if it's using the old numeric format for links...
	if (is_numeric($character_name)) {
		$sql = "SELECT url FROM rpg_characters WHERE id = '".$db->sql_escape($character_name)."'";
		$result = $db->sql_query_limit($sql,1,null,3600);
		while($row = $db->sql_fetchrow($result)) {
			// Wow, this is sheer magic!  
			redirect("http://www.roleplaygateway.com/roleplay/the-multiverse/characters/".$row['url'].'/');
		}
	} else {
		//$character_name = preg_replace("/_/","/ /",$character_name);
		//$character_name = preg_replace("/ /","/_/",$character_name);
		// Yes, I know SELECT * is lazy. ~_~ - Eric M
		$sql = "SELECT * FROM rpg_characters WHERE name = '".$db->sql_escape($character_name)."'";
		// Note that I've wrapped the $character_name inside an object->function called "sql_escape" - which does most of the heavy lifting for us
		$result = $db->sql_query_limit($sql,1);
	}
	if (count($result) == 1) {
	
		while($row = $db->sql_fetchrow($result)) {
		
			redirect("http://www.roleplaygateway.com/roleplay/the-multiverse/characters/".$row['url'].'/');
		
			// TODO: integrate this into the former SQL query using proper JOIN statements... Eric M, 5/4/09
			$sql = "SELECT username FROM gateway_users WHERE user_id = ".$row['owner'];
			$owner_result = $db->sql_query_limit($sql,1);
			while($owner_row = $db->sql_fetchrow($owner_result)) {                     
				$owner_username = $owner_row['username'];                                                                
			}
			$db->sql_freeresult($owner_result); // save some memory somewhere
			$template->assign_vars(array(
				'OWNER_USERNAME'              => get_username_string('full', $row['owner'], $owner_username), // don't forget the comma.  we're building an array here.
				'CHARACTER_NAME'              => $row['name'],
				'CHARACTER_ID'              => $row['id'],
				'CHARACTER_URL'              => $row['url'],
				'CHARACTER_SYNOPSIS'          => $row['synopsis'],
				'CHARACTER_DESCRIPTION'       => generate_text_for_display($row['description'], $row['description_uid'], $row['description_bitfield'], 7),
				'CHARACTER_PERSONALITY'       => generate_text_for_display($row['personality'], $row['personality_uid'], $row['personality_bitfield'], 7),
				'CHARACTER_EQUIPMENT'         => generate_text_for_display($row['equipment'], $row['equipment_uid'], $row['equipment_bitfield'], 7),
				'CHARACTER_HISTORY'           => generate_text_for_display($row['history'], $row['history_uid'], $row['history_bitfield'], 7),
			));
			$character_roleplay_count = 0;
			$sql = "SELECT count(*) as character_roleplay_count FROM rpg_roleplay_players WHERE character_id = ".$row['id'];
			$roleplay_result = $db->sql_query($sql);
			
			while ($roleplay_row = $db->sql_fetchrow($roleplay_result)) {
				$character_roleplay_count = $roleplay_row['character_roleplay_count'];
			}
			
			$template->assign_var('CHARACTER_ROLEPLAY_COUNT',$character_roleplay_count);
			
			$sql = "SELECT id,title FROM rpg_roleplays
			INNER JOIN rpg_roleplay_players
			ON rpg_roleplay_players.roleplay_id = rpg_roleplays.id
			WHERE character_id = ".$row['id']."
			ORDER BY id DESC";
			$roleplay_result = $db->sql_query_limit($sql,5);
			while($roleplay_row = $db->sql_fetchrow($roleplay_result)) {

				$template->assign_block_vars("roleplays", array(
						'ROLEPLAY_ID'			=> $roleplay_row['id'],
						'ROLEPLAY_TITLE'		=> $roleplay_row['title'],
					)
				);
			}
		}
		
		// Output page
		page_header($character_name.' on RolePlayGateway ');
		$template->set_filenames(array(
			'body' => 'characters_profile_body.html')
		);
		page_footer();
		
	} else {
		echo "nothing found...";
	}
} else {
	page_header('Roleplay - Character Profiles');
	$template->set_filenames(array(
		'body' => 'characters_main_body.html')
	);
	page_footer();
}

?>