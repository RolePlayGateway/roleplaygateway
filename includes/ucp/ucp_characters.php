<?php

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}


function getBannedPlayers($id) {
	global $config, $db, $user, $auth, $template, $phpbb_root_path, $phpEx;

	$sql = 'SELECT player_id FROM rpg_banned_players WHERE roleplay_id = '.(int) $id;
	$bannedResult = $db->sql_query($sql);
	while ($banned = $db->sql_fetchrow($bannedResult)) {
		$bannedPlayers[] = $banned['player_id'];
	}
	$db->sql_freeresult($bannedResult);

	return (array) $bannedPlayers;

}


class ucp_characters
{
	var $u_action;
	
	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $phpbb_root_path, $phpEx;

		$user->add_lang('posting');

		$preview	= (!empty($_POST['preview'])) ? true : false;
		$submit		= (!empty($_POST['submit'])) ? true : false;
		$delete		= (!empty($_POST['delete'])) ? true : false;
		$error = $data = array();
		$s_hidden_fields = '';
		
		
		switch ($mode) {
			case 'abandon':		
				
				$character_id = request_var('character_id', 0);
				$sql = "SELECT id, name, synopsis, creator, url, roleplay_id FROM rpg_characters WHERE id = ".$db->sql_escape($character_id). " AND owner = ".(int) $user->data['user_id'];

        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result)) {

          if (empty($row['creator'])) {
            $sql = 'UPDATE rpg_characters SET creator = owner WHERE id = '.(int) $character_id;
            $db->sql_query($sql);
          }

          $sql = 'SELECT url FROM rpg_roleplays WHERE id = '.(int) $row['roleplay_id'];
          $roleplayResult = $db->sql_query($sql);
          $roleplay = $db->sql_fetchrow($roleplayResult);
          $db->sql_freeresult($roleplayResult);

        	$template->assign_vars(array(
            'ID'                 => $row['id'],
            'CHARACTER_NAME'     => $row['name'],
            'CHARACTER_SYNOPSIS' => $row['synopsis'],
            'CHARACTER_URL'      => $row['url'],
            'ROLEPLAY_URL'       => $roleplay['url']
          ));

          // Set desired template
          $this->tpl_name = 'ucp_characters_abandon';
          $this->page_title = 'Abandon Character';

        }
				
				if ($submit) {
				  // check mode
				  $isAdoptable = request_var('Adoption', 0);
					$sql = "UPDATE rpg_characters SET owner = 0, isAdoptable= ".(int) $isAdoptable." WHERE id = ".(int) $db->sql_escape($_REQUEST['character_id'])." AND owner = ".$user->data['user_id'];
					$db->sql_query($sql);
					meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=characters");
					trigger_error("Oh no! You have abandoned this character, releasing them into the unknown.");
				}
			break;
			case 'adopt':

				$character_id = intval($_REQUEST['character_id']);
			
				$sql = "SELECT name, synopsis, url, roleplay_id FROM rpg_characters WHERE id = ".$db->sql_escape($character_id). " AND isAdoptable = 1";
				$result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);				

			
				$sql = "SELECT title, url, id FROM rpg_roleplays WHERE id = ".$db->sql_escape($row['roleplay_id']);
				$result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

				if (in_array($user->data['user_id'], getBannedPlayers($roleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

				// check mode
				if (confirm_box(true)) {

					if (
							in_array($user->data['username'], array('phaloxian', 'joco3899', 'serus', 'zernin'))
						|| in_array($user->data['user_id'], array(60178, 60656, 60735, 60777))
						) {
						trigger_error("You cannot adopt characters at this time.");
					}


					if (in_array($user->data['username'], array('phaloxian', 'joco3899', 'serus', 'zernin'))) {
						trigger_error("You cannot adopt characters at this time.");
					}

					$sql = "UPDATE rpg_characters SET owner = '".$user->data['user_id']."', isAdoptable = 0 WHERE id = ".$db->sql_escape($_REQUEST['character_id']." AND isAdoptable = 1");
  				$db->sql_query($sql); 
					meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=characters");
					trigger_error("Congratulations, you are now the proud owner of this character!");
				} else {

          $row['examples'] = '<h2 style="clear:both;">Recent Posts About This Character</h2><div class="postbody" style="width:100%;">';
          $row['examples'] .= '<style type="text/css">input.button2 { font-size: 2em; padding: 5px; border-radius: 5px; margin-top: 10px;}</style>';

					$sql = "SELECT content_id FROM rpg_content_tags WHERE character_id = ".$db->sql_escape($character_id). " ORDER BY content_id DESC LIMIT 5";
					$result = $db->sql_query($sql);
          while($contentrow = $db->sql_fetchrow($result)) {
            $sql = 'SELECT text, bbcode_uid, bbcode_bitfield FROM rpg_content
                      WHERE id = '.(int) $contentrow['content_id'];
            $contentResult = $db->sql_query($sql);
            $post = $db->sql_fetchrow($contentResult);
            $db->sql_freeresult($contentResult);
          
            $row['examples'] .= '<div class="content">'.generate_text_for_display($post['text'], $post['bbcode_uid'], $post['bbcode_bitfield'], 7).'</div>';
          }
          $db->sql_freeresult($result);
          $row['examples'] .= '</div>'; 

					// I don't know of any other way to do this... but I agree, it looks ugly
					if ($confirm_key = $_REQUEST['confirm_key']) {
						meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=characters");
						trigger_error("Aww, that's so sad. You had gotten ".$row['name']."'s hopes up, but now they're going back to the \"Abandoned\" section...");					
					} else {
					   $s_hidden_fields = build_hidden_fields(array(
							'submit'    => true,
							'my_mesage' => $my_message,
							)
						);
						confirm_box(false, "Would you like to adopt this poor abandoned character as your own?  You will be expected to play this character <em>responsibly</em>, trying to portray them as accurately as possible to any existing interactions they've had.<br/><br /><img style=\"float:left; margin-right: 10px; ; margin-bottom: 10px; \" src=\"http://www.roleplaygateway.com/roleplay/".$roleplay['url']."/characters/".$row['url']."/image\" /><h2>".$row['name']."</h2><p>".$row['synopsis'].'</p><br />'.$row['examples'], $hidden_fields);
					}
				}
			break;
			case 'approve':
			
				$character_id = request_var('character_id', 0);
			
			
				$sql = "SELECT roleplay_id,r.owner as roleplay_owner,r.url,r.require_approval,c.approved,c.status FROM rpg_characters c
					INNER JOIN rpg_roleplays r ON c.roleplay_id = r.id
				WHERE c.id = ".$db->sql_escape($character_id);
			
				$result = $db->sql_query($sql);
				
				$row = $db->sql_fetchrow($result);
				$owner 				= (int) $row['roleplay_owner'];
				$approved			= (int) $row['approved'];
				$status 			= (string) $row['status'];
				$require_approval	= (int) $row['require_approval'];
                $url                = (string) $row['url'];
				
				$db->sql_freeresult($result);
				
				if ($user->data['user_id'] != $owner) {
				
					trigger_error('You do not own this roleplay. You cannot approve this character.');
				
				} else {
				
					if (($approved == 1) || ($status == 'Approved')) {
						trigger_error('This character is already approved!');
					}
					
					if ($require_approval == 0) {
						trigger_error('This roleplay does not require character approval.');
					}				
			
					// check mode
					if (confirm_box(true)) {
						$sql = "UPDATE rpg_characters SET approved = 1, status = 'Approved' WHERE id = ".$db->sql_escape($character_id);
						$db->sql_query($sql);
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$url."/");
						trigger_error("You have approved this character in the roleplay!  They are now allowed to post.");
					} else {
					
						$sql = "SELECT * FROM rpg_characters WHERE id = ".$db->sql_escape($character_id). "";
						
						$result = $db->sql_query($sql);
						
						while ($row = $db->sql_fetchrow($result)) {
							// I don't know of any other way to do this... but I agree, it looks ugly
							if ($confirm_key = $_REQUEST['confirm_key']) {
								meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$url."/");
								trigger_error("You didn't approve this character.  They will be able to edit the profile until you approve it, but will not be able to post in your roleplay.");					
							} else {
							   $s_hidden_fields = build_hidden_fields(array(
									'submit'    => true,
									'my_mesage' => $my_message,
									)
								);
								confirm_box(false, "Are you sure you want to approve this character in the roleplay?<br/><br />".$row['name']."<br />".$row['synopsis'],$hidden_fields);
							}
						}
					}
	
				}
			break;
			case 'reject':
			
				$character_id = request_var('character_id', 0);
			
			
				$sql = "SELECT roleplay_id,r.owner as roleplay_owner,r.url,r.require_approval,c.approved,c.status FROM rpg_characters c
					INNER JOIN rpg_roleplays r ON c.roleplay_id = r.id
				WHERE c.id = ".$db->sql_escape($character_id);
			
				$result = $db->sql_query($sql);
				
				$row = $db->sql_fetchrow($result);
				$owner 				= (int) $row['roleplay_owner'];
				$approved			= (int) $row['approved'];
				$status 			= (string) $row['status'];
				$require_approval	= (int) $row['require_approval'];
                $url                = (string) $row['url'];
				
				$db->sql_freeresult($result);
				
				if ($user->data['user_id'] != $owner) {
				
					trigger_error('You do not own this roleplay. You cannot manage this character.');
				
				} else {
				
					if (($approved == 1) || ($status == 'Approved')) {
						trigger_error('This character has already been approved!  You cannot deny them now.');
					}
					
					if ($require_approval == 0) {
						trigger_error('This roleplay does not require character approval.');
					}				
			
					// check mode
					if ($submit) {
					
						$reason = request_var('reason','',true);
					
						if (strlen($reason) < 1) {
							trigger_error('You must provide a reason so the owner knows what to change!');
						}

						$sql 		= 'SELECT title as roleplay,name,r.url as roleplay_url,c.owner FROM rpg_characters c INNER JOIN rpg_roleplays r ON r.id = c.roleplay_id WHERE c.id = '.(int) $character_id;
						$result 	= $db->sql_query($sql);
						$character 	= $db->sql_fetchrow($result);
						$db->sql_freeresult($result);
					
						if (!function_exists(submit_pm)) {
							include($phpbb_root_path.'includes/functions_privmsgs.' . $phpEx);
						}
						
						// note that multibyte support is enabled here 
						$my_subject = utf8_normalize_nfc($character['roleplay'].': '.$character['name'].' has been rejected.');
						$my_text    = utf8_normalize_nfc('Your character in [url=http://www.roleplaygateway.com/roleplay/'.$character['roleplay_url'].'/]'.$character['roleplay'].'[/url], "'.$character['name'].'", has been rejected: [quote]'.$reason.'[/quote]');

						// variables to hold the parameters for submit_pm
						$poll = $uid = $bitfield = $options = ''; 
						generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
						generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

						$data = array( 
							'address_list'      => array ('u' => array($character['owner'] => 'to')),
							'from_user_id'      => 0,
							'from_username'     => 'RolePlayGateway',
							'icon_id'           => 0,
							'from_user_ip'      => $user->data['user_ip'],
							 
							'enable_bbcode'     => true,
							'enable_smilies'    => true,
							'enable_urls'       => true,
							'enable_sig'        => true,

							'message'           => $my_text,
							'bbcode_bitfield'   => $bitfield,
							'bbcode_uid'        => $uid,
						);

						submit_pm('post', $my_subject, $data, false);
						
						$sql = "UPDATE rpg_characters SET status = 'Rejected' WHERE id = ".$db->sql_escape($character_id);
						$db->sql_query($sql);
					
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$url."/");
						trigger_error("You have rejected this character from the roleplay! They have been sent the information you provided, and will be allowed to update their profile until you approve it.");
					} else {
					
						$sql = "SELECT * FROM rpg_characters WHERE id = ".$db->sql_escape($character_id). "";
						
						$result = $db->sql_query($sql);
						
						while ($row = $db->sql_fetchrow($result)) {
						
							$template->assign_vars(array(
								'ID'						=> $row['id'],
								'CHARACTER_NAME'			=> $row['name'],
								'CHARACTER_SYNOPSIS'		=> $row['synopsis'],
								'CHARACTER_DESCRIPTION'		=> generate_text_for_display($row['description'], $row['description_uid'], $row['description_bitfield'], 7),
								'CHARACTER_PERSONALITY'		=> generate_text_for_display($row['personality'], $row['personality_uid'], $row['personality_bitfield'], 7),
								'CHARACTER_EQUIPMENT'		=> generate_text_for_display($row['equipment'],   $row['equipment_uid'],   $row['equipment_bitfield'], 7),
								'CHARACTER_HISTORY'			=> generate_text_for_display($row['history'],     $row['history_uid'],     $row['history_bitfield'], 7),
								'CHARACTER_ROLEPLAY_COUNT'	=> $character_roleplay_count,
								'OWNER_USERNAME'			=> get_username_string('full', $row['owner'], $owner_username),
								)
							);
							
							// Set desired template
							$this->tpl_name = 'ucp_characters_reject';
							$this->page_title = 'Reject Character';						
							
						}
					}
	
				}
			break;
			case 'view':
			
				$sql = "SELECT * FROM rpg_characters WHERE id = ".$db->sql_escape($_REQUEST['id']);
				$result = $db->sql_query($sql);
				while($row = $db->sql_fetchrow($result)) {
				
					$character_roleplay_count = 0;
				
					$sql = "SELECT count(*) as character_roleplay_count FROM rpg_roleplay_players WHERE character_id = ".$row['id'];
					$roleplay_result = $db->sql_query($sql);
					while ($roleplay_row = $db->sql_fetchrow($roleplay_result)) {
						$character_roleplay_count = $roleplay_row['character_roleplay_count'];
					}
					

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
					$sql = "SELECT username FROM gateway_users WHERE user_id = ".$row['owner'];
					$owner_result = $db->sql_query_limit($sql,1);
					while($owner_row = $db->sql_fetchrow($owner_result)) {
					
						$owner_username = $owner_row['username'];								

					}
					
					$character_synopsis = $row['synopsis'];
					strip_bbcode($character_synopsis);
								
					$template->assign_vars(array(
						'ID'						=> $row['id'],
						'CHARACTER_NAME'			=> $row['name'],
						'CHARACTER_SYNOPSIS'		=> $row['synopsis'],
						'CHARACTER_DESCRIPTION'		=> generate_text_for_display($row['description'], $row['description_uid'], $row['description_bitfield'], 7),
						'CHARACTER_PERSONALITY'		=> generate_text_for_display($row['personality'], $row['personality_uid'], $row['personality_bitfield'], 7),
						'CHARACTER_EQUIPMENT'		=> generate_text_for_display($row['equipment'],   $row['equipment_uid'],   $row['equipment_bitfield'], 7),
						'CHARACTER_HISTORY'			=> generate_text_for_display($row['history'],     $row['history_uid'],     $row['history_bitfield'], 7),
						'CHARACTER_ROLEPLAY_COUNT'	=> $character_roleplay_count,
						'OWNER_USERNAME'			=> get_username_string('full', $row['owner'], $owner_username),
						)
					);
					
					$this->page_title = $row['name'];
					
				}

				// Set desired template
				$this->tpl_name = 'ucp_characters_view';
				
							
			break;
			case 'edit':
				
				if (($character_id = request_var('character_id', '')) < 1) {
					trigger_error('You must access this page from the <a href="http://www.roleplaygateway.com/ucp.php?i=characters">main character list</a>!');
				}

				$sql = "SELECT name, synopsis, url, roleplay_id FROM rpg_characters WHERE id = ".$db->sql_escape($character_id);
				$result = $db->sql_query($sql);
        $tempRow = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);				

			
				$sql = "SELECT title, url, id FROM rpg_roleplays WHERE id = ".$db->sql_escape($tempRow['roleplay_id']);
				$result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

				if (in_array($user->data['user_id'], getBannedPlayers($roleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}
			
				if ($submit) {
				
					$character_synopsis		 = utf8_normalize_nfc(request_var('character_synopsis', ''),true);
					$character_description	 = utf8_normalize_nfc(request_var('character_description', '', true),true);
					$character_personality	 = utf8_normalize_nfc(request_var('character_personality', '', true),true);
					$character_equipment	 = utf8_normalize_nfc(request_var('character_equipment', '', true),true);
					$character_history		 = utf8_normalize_nfc(request_var('character_history', '', true),true);
					
					// check if a file was submitted
					if(isset($_FILES['userfile'])) {
					
						try {
							if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
								// check the file is less than the maximum file size
								if($_FILES['userfile']['size'] < 1500000) {

									// prepare the image for insertion
									$imgData = addslashes(file_get_contents($_FILES['userfile']['tmp_name']));
			 
									// get the image info..
									$imgSize = getimagesize($_FILES['userfile']['tmp_name']);
						 
									if (($imgSize[0] > 100) || ($imgSize[1] > 100)) {
										trigger_error('This image is larger than 100x100.  Resize it.');
									}
						 
								}
							}
						}
						catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}
					
					strip_bbcode($character_synopsis);
										
					$description_uid = $description_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$personality_uid = $personality_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$equipment_uid = $equipment_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$history_uid = $history_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					
					generate_text_for_storage($character_description, $description_uid, $description_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);					
					generate_text_for_storage($character_personality, $personality_uid, $personality_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);					
					generate_text_for_storage($character_equipment, $equipment_uid, $equipment_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);					
					generate_text_for_storage($character_history, $history_uid, $history_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

					//trigger_error($uid." " . $bitfield . " " .$character_description);					
					if ($imgData) {
					
						
					
						$sql = "UPDATE rpg_characters SET
							synopsis 					= '".$db->sql_escape($character_synopsis)."',
							description 				= '".$db->sql_escape($character_description)."',
								description_uid 		= '".$description_uid."',
								description_bitfield 	= '".$description_bitfield."',
							personality 				= '".$db->sql_escape($character_personality)."',
								personality_uid 		= '".$personality_uid."',
								personality_bitfield 	= '".$personality_bitfield."',
							equipment 					= '".$db->sql_escape($character_equipment)."',
								equipment_uid			= '".$equipment_uid."',
								equipment_bitfield 		= '".$equipment_bitfield."',
							history 					= '".$db->sql_escape($character_history)."',
								history_uid 			= '".$history_uid."',
								history_bitfield 		= '".$history_bitfield."',
							image						= '".$imgData."',
								image_type				= '".$imgSize['mime']."'
							WHERE id = '".$db->sql_escape($character_id)."'";
					} else {
						$sql = "UPDATE rpg_characters SET
							synopsis 					= '".$db->sql_escape($character_synopsis)."',
							description 				= '".$db->sql_escape($character_description)."',
								description_uid 		= '".$description_uid."',
								description_bitfield 	= '".$description_bitfield."',
							personality 				= '".$db->sql_escape($character_personality)."',
								personality_uid 		= '".$personality_uid."',
								personality_bitfield 	= '".$personality_bitfield."',
							equipment 					= '".$db->sql_escape($character_equipment)."',
								equipment_uid			= '".$equipment_uid."',
								equipment_bitfield 		= '".$equipment_bitfield."',
							history 					= '".$db->sql_escape($character_history)."',
								history_uid 			= '".$history_uid."',
								history_bitfield 		= '".$history_bitfield."'
							WHERE id = '".$db->sql_escape($character_id)."'";
					}
					$result = $db->sql_query($sql);
				
					$sql = "SELECT * FROM rpg_characters WHERE id = '".$db->sql_escape($character_id)."'";
					$result = $db->sql_query($sql);
					
					while($row = $db->sql_fetchrow($result)) {

						$character = $row;

						$sql = "SELECT id,title,url,description,introduction,introduction_bitfield,introduction_uid,owner,require_approval,type,featured,player_slots,updated,status,created FROM rpg_roleplays
								WHERE id = '".$db->sql_escape($row['roleplay_id'])."'";
						$roleplayResult = $db->sql_query($sql);
						$roleplay = $db->sql_fetchrow($roleplayResult);
						$db->sql_freeresult($roleplayResult);
					}
					meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$roleplay['url']."/characters/".$character['url']);
					trigger_error("Successfully updated character data!");
					
				} else {
				
					$sql = "SELECT * FROM rpg_characters WHERE id = '".$db->sql_escape($character_id)."'";
					$result = $db->sql_query($sql);
					
					while($row = $db->sql_fetchrow($result)) {

						$sql = "SELECT id,title,url,description,introduction,introduction_bitfield,introduction_uid,owner,require_approval,type,featured,player_slots,updated,status,created FROM rpg_roleplays
								WHERE id = '".$db->sql_escape($row['roleplay_id'])."'";
						$roleplayResult = $db->sql_query($sql);
						$roleplay = $db->sql_fetchrow($roleplayResult);
						$db->sql_freeresult($roleplayResult);

						$game_masters = array();
						$sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
						$gmresult = $db->sql_query($sql);
						while ($gm_row = $db->sql_fetchrow($gmresult)) {
						  $game_masters[] = $gm_row['user_id'];
						}
						$db->sql_freeresult($gmresult);
					
						if (!($auth->acl_get('m_')) && !($row['owner'] == $user->data['user_id']) && !(in_array($user->data['user_id'], $game_masters))) {						
							trigger_error('You do not own this character!');
						}
						
						if ($row['creator'] == $user->data['user_id']) {
						  if ($row['creator'] != $row['owner']) {
						    trigger_error('Very sorry, but you are no longer the owner of this character.');
						  }
						}
										
						$synopsis = $row['synopsis'];
						$description = $row['description'];
						$personality = $row['personality'];
						$equipment = $row['equipment'];
						$history = $row['history'];
						
						strip_bbcode($synopsis);

						decode_message($description, $row['description_uid']);
						decode_message($personality, $row['personality_uid']);
						decode_message($equipment, $row['equipment_uid']);
						decode_message($history, $row['history_uid']);
						
						$template->assign_vars(array(
							'CHARACTER_ID'	=> $row['id'],
							'NAME'			    => $row['name'],
							'SYNOPSIS'		  => $synopsis,
							'DESCRIPTION'	  => $description,
							'PERSONALITY'	  => $personality,
							'EQUIPMENT'		  => $equipment,
							'HISTORY'		    => $history,
							)
						);

					}

				}
			
				// Set desired template
				$this->tpl_name = 'ucp_characters_edit';
				$this->page_title = 'Edit Character';
			
			break;

			case 'duplicate':
			
				$roleplay_id	 		 = request_var('roleplay_id', 0);
				$character_id	 		 = request_var('character_id', 0);
				
				if (!$roleplay_id || !$character_id) {
					trigger_error('You must select both a roleplay to copy to and a character to copy from!');
				}
				
				$sql = "SELECT * FROM rpg_characters WHERE id = $character_id";
				$result = $db->sql_query($sql);
				$character = $db->sql_fetchrow($result);
			
				$sql = "SELECT count(*) as count FROM rpg_characters WHERE roleplay_id ='". $roleplay_id ."' AND url='". urlify($character['name'])."'";
				$result = $db->sql_query($sql);
				$already_there = $db->sql_fetchrow($result);

				if($already_there['count'] > 0)
				{
					trigger_error("A character with the same name already exists in this roleplay. Click back.");
				}
	
				$sql = "SELECT id FROM rpg_places WHERE roleplay_id = ".(int) $roleplay_id;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result)) {
					$placelist[] = $row['id'];
				}

				$randomKey = array_rand($placelist);
				
				if (confirm_box(true)) {
					
					$new_data = array (
						'roleplay_id'						=> $roleplay_id,
						'parent_id'							=> $character_id,
						'owner'								=> $character['owner'],
						'name'								=> $character['name'],
						'synopsis'							=> $character['synopsis'],
						'location'							=> $placelist[$randomKey],
						'description'						=> $character['description'],
						'description_bitfield'				=> $character['description_bitfield'],
						'description_uid'					=> $character['description_uid'],
						'personality'						=> $character['personality'],
						'personality_bitfield'				=> $character['personality_bitfield'],
						'personality_uid'					=> $character['personality_uid'],
						'equipment'							=> $character['equipment'],
						'equipment_bitfield'				=> $character['equipment_bitfield'],
						'equipment_uid'						=> $character['equipment_uid'],
						'history'							=> $character['history'],
						'history_bitfield'					=> $character['history_bitfield'],
						'history_uid'						=> $character['history_uid'],
						'image'								=> $character['image'],
						'image_type'						=> $character['image_type'],
						'url'								=> urlify($character['name']),
						'status'							=> 'Submitted',
						'isAdoptable'							=> $character['isAdoptable'],
						'creator'							=> $character['creator']
					); //  created,image,image_type,url
					
					$sql = 'INSERT INTO rpg_characters ' . $db->sql_build_array('INSERT', $new_data);
									
					//trigger_error($sql);
									
					$db->sql_query($sql);
					$new_id = $db->sql_nextid();
					
					meta_refresh(3,"http://www.roleplaygateway.com/ucp.php?i=characters&mode=edit&character_id=".$new_id);
					trigger_error('You have successfully copied '.$character['name'].' to that roleplay! We will redirect you to make changes momentarily...');
					
				} else {
					// I don't know of any other way to do this... but I agree, it looks ugly
					if ($confirm_key = $_REQUEST['confirm_key']) {
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/");
						trigger_error("You chose not to copy this character into the roleplay.");					
					} else {
					   $s_hidden_fields = build_hidden_fields(array(
							'submit'    => true,
							'my_mesage' => $my_message,
							)
						);
						confirm_box(false, "Are you <strong>sure</strong> that you want to duplicate ".$character['name']." into this roleplay?",$hidden_fields);
					}			
				}
					
			break;
			
			case 'new':

				if (in_array($user->data['user_id'], getBannedPlayers(request_var('roleplay_id', 1)))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

				if ($submit) {
				
					$character_name 		 = utf8_normalize_nfc(request_var('character_name', '', true));
					$roleplay_id	 		 = request_var('roleplay_id', 1);
					$character_synopsis		 = utf8_normalize_nfc(request_var('character_synopsis', '', true));
					$character_description	 = utf8_normalize_nfc(request_var('character_description', '', true));
					$character_personality	 = utf8_normalize_nfc(request_var('character_personality', '', true));
					$character_equipment	 = utf8_normalize_nfc(request_var('character_equipment', '', true));
					$character_history		 = utf8_normalize_nfc(request_var('character_history', '', true));
					
                    //Check character name contains at least one non-whitespace character.
                    if(strlen(trim($character_name)) < 1)
                    {
                        trigger_error('Invalid character name.');
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/");
                    }

					// check if a file was submitted
					if(isset($_FILES['userfile'])) {
						try {
							if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
								// check the file is less than the maximum file size
								if($_FILES['userfile']['size'] < 1500000) {

									// prepare the image for insertion
									$imgData = addslashes(file_get_contents($_FILES['userfile']['tmp_name']));
			 
									// get the image info..
									$imgSize = getimagesize($_FILES['userfile']['tmp_name']);
						 
									if (($imgSize[0] > 100) || ($imgSize[1] > 100)) {
										trigger_error('This image is larger than 100x100.  Resize it.');
									}
						 
								}
							}
						}
						catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}					
					
					strip_bbcode($character_synopsis);
					
					$description_uid = $description_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$personality_uid = $personality_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$equipment_uid = $equipment_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$history_uid = $history_bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					
					generate_text_for_storage($character_description, $description_uid, $description_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);					
					generate_text_for_storage($character_personality, $personality_uid, $personality_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);					
					generate_text_for_storage($character_equipment, $equipment_uid, $equipment_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);					
					generate_text_for_storage($character_history, $history_uid, $history_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

					
					$sql = "SELECT count(*) as count FROM rpg_characters WHERE name = '".$db->sql_escape($character_name)."' AND roleplay_id = ".$roleplay_id;					
					$result = $db->sql_query($sql);		
					while ($row = $db->sql_fetchrow($result)) {
						if ($row['count'] >= 1) {
							trigger_error("There is already a character by that name.");
						}
					}
					
					$sql = "SELECT id FROM rpg_places WHERE roleplay_id = '".$db->sql_escape($roleplay_id)."'";					
					$result = $db->sql_query($sql);		
					while ($row = $db->sql_fetchrow($result)) {
						$locations[] = $row['id'];
					}
					
					if(!$location = $locations[array_rand($locations)]) {
						trigger_error('This roleplay doesn\'t have any places in it.  Have the owner add one first.');
					}
					
					$sql = "SELECT * FROM rpg_roleplays WHERE id = '".$db->sql_escape($roleplay_id)."'";
					$result = $db->sql_query($sql);
                                        $roleplay = $db->sql_fetchrow($result);
                                        if ($roleplay['require_approval'] == 1){
						$status = 'Submitted';
					}
					else
					{
						$status = 'Approved';
                                        }

						
					//TODO: convert this into phpBB's native BUILD SQL ARRAY so we can throw away my many hours of work building this.  ~ Eric M, 1:55 AM Sunday, May 3rd 2009
					$sql = "INSERT INTO rpg_characters (name,roleplay_id,location,synopsis,owner,creator,description,personality,equipment,history,description_uid,description_bitfield,personality_uid,personality_bitfield,equipment_uid,equipment_bitfield,history_uid,history_bitfield,image,image_type,url,status) VALUES
						('".$db->sql_escape($character_name)."',
						 '".$db->sql_escape($roleplay_id)."',
						 '".$db->sql_escape($location)."',
						 '".$db->sql_escape($character_synopsis)."',
						 '".$user->data['user_id']."',
						 '".$user->data['user_id']."',
						 '".$db->sql_escape($character_description)."',
						 '".$db->sql_escape($character_personality)."',
						 '".$db->sql_escape($character_equipment)."',
						 '".$db->sql_escape($character_history)."',
						 '".$description_uid."',
						 '".$description_bitfield."',
						 '".$personality_uid."',
						 '".$personality_bitfield."',
						 '".$equipment_uid."',
						 '".$equipment_bitfield."',
						 '".$history_uid."',
						 '".$history_bitfield."',
						 '".$imgData."',
						 '".$imgSize['mime']."',
						 '".urlify($character_name)."',
						 '".$status."'
						 )";
					$db->sql_query($sql);
					
					$sql 		= 'SELECT c.id, title as roleplay,name,r.url as roleplay_url,c.url,c.owner FROM rpg_characters c INNER JOIN rpg_roleplays r ON r.id = c.roleplay_id WHERE c.id = '.(int) $db->sql_nextid();
					$result 	= $db->sql_query($sql);
					$character 	= $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
				
					include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
					
					// note that multibyte support is enabled here 
					$my_subject = utf8_normalize_nfc($character['roleplay'].': New Character - '.$character['name']);
					$my_text    = utf8_normalize_nfc('A new character has been added to [url=http://www.roleplaygateway.com/roleplay/'.$character['roleplay_url'].'/]'.$character['roleplay'].'[/url] named "[url=http://www.roleplaygateway.com/roleplay/'.$character['roleplay_url'].'/characters/'.$character['url'].'/]'.$character['name'].'[/url]".');

					// variables to hold the parameters for submit_pm
					$poll = $uid = $bitfield = $options = ''; 
					generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
					generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

					$data = array( 
						'address_list'      => array ('u' => array($roleplay['owner'] => 'to')),
						'from_user_id'      => 0,
						'from_username'     => 'RolePlayGateway',
						'icon_id'           => 0,
						'from_user_ip'      => $user->data['user_ip'],
						 
						'enable_bbcode'     => true,
						'enable_smilies'    => true,
						'enable_urls'       => true,
						'enable_sig'        => true,

						'message'           => $my_text,
						'bbcode_bitfield'   => $bitfield,
						'bbcode_uid'        => $uid,
					);

					submit_pm('post', $my_subject, $data, false);

					$sql = 'SELECT id, url, title, owner FROM rpg_roleplays 
						WHERE id = '.$roleplay_id;
					$result = $db->sql_query_limit($sql, 1);
					$roleplay = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

          $messageData = array(
              'id'       	=> $contentID
            , 'link'     	=> 'http://www.roleplaygateway.com/roleplay/' . $roleplay['url'] . '/characters/' . urlify($character_name)
            , 'roleplay' 	=> $roleplay
            , 'character'	=> $character
            , 'creator'   => array(
                  'name'  => $user->data['username']
              )
          );

					$redis = new Redis();
					$redis->pconnect('127.0.0.1', 6379);
					$redis->publish('roleplay.'.$roleplay['id'] , json_encode(array(
            'type' => 'character',
            'data' => $messageData
          ), JSON_FORCE_OBJECT));
					//$redis->publish('roleplay.'.$roleplay.'.content', json_encode($messageData, JSON_FORCE_OBJECT));
					$redis->close();
		
					meta_refresh(3, 'http://www.roleplaygateway.com/roleplay/' . $roleplay['url'] . '/characters/' . urlify($character_name));
					trigger_error("Successfully submitted your character!");
					
				} else {
					// Set desired template
					$this->tpl_name = 'ucp_characters_new';
					$this->page_title = 'Add New Character';
					
				}
			
			break;
			default:
			
				$sql = "SELECT c.*,r.title,c.id AS id FROM rpg_characters c
					INNER JOIN rpg_roleplays r ON c.roleplay_id = r.id
				WHERE c.owner = ".$user->data['user_id'];
				$result = $db->sql_query($sql);
				while($row = $db->sql_fetchrow($result)) {
					
					$character_synopsis = $row['synopsis'];
					
					strip_bbcode($character_synopsis);
								
					$template->assign_block_vars("characters", array(
						'ID'			=> $row['id'],
						'NAME'			=> $row['name'],
						'ROLEPLAY'		=> $row['title'],
						'ROLEPLAY_URL'	=> urlify($row['title']),
						'URL'			=> urlify($row['name']),
						'SYNOPSIS'		=> $character_synopsis,
						)
					);

				}


				$template->assign_vars(array(
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
					'S_UCP_ACTION'		=> $this->u_action)
				);

				// Set desired template
				$this->tpl_name = 'ucp_characters';
				$this->page_title = 'Characters';
			
			break;
		}
	}
}
?>
