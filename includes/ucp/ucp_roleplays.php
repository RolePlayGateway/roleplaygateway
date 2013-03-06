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

/**
* ucp_profile
* Changing profile settings
*
* @todo what about pertaining user_sig_options?
* @package ucp
*/
class ucp_roleplays
{
	var $u_action;
	
	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $phpbb_root_path, $phpEx;
/* 
		$allowed_users = array(
			7664,
			7669,
			12793,
			5365,
			12717,
			12609, // kris
			15855, // jyll
			5365, // Kronos
			// Twitter users
			16584, // jvalenti57 
			16593, // sdwrage
			16595, // dylanmcintosh
			16961, // Empererlou
			7687, // Frug
		);
		
		$allowed_usernames = array(
			'Sarcyn',
			'queenofdarkness',
			'SilencexGolden',
			'chicagofats',
			'Omega_Pancake',
			'ldraes',
			'Angel-Chii',
			'dreamsdontlast',
			'Sweet Angel Jocelyn',
			'Aryx Noi',
			'Myth',
			'Law',
			'Ryand-Smith',
			'Ylanne',
			// 'Kouketsu',
			'Kronos',
			'Ottoman',
			'AzricanRepublic',
			'Conumbra',
			'Conquerer_Man',
			'Andreis',
			'ShatteredSoul',
			'admiralmcgregor',
			'CuriousVisitor',
			'Alucroas',
			'Tetrino',
			'dinocular',
			'Mencith',
			'Combatant876',
			'Huge Roach',
		);
		
		if ((!$auth->acl_get('m_')) && (!in_array($user->data['user_id'], $allowed_users) && (!in_array($user->data['username'], $allowed_usernames))))
		{
			trigger_error('NOT_AUTHORISED');
		}
		 */
		$user->add_lang('posting');

		$preview	= (!empty($_POST['preview'])) ? true : false;
		$submit		= (!empty($_POST['submit'])) ? true : false;
		$delete		= (!empty($_POST['delete'])) ? true : false;
		$error = $data = array();
		$s_hidden_fields = '';

		switch ($mode) {
			case "add_place":

        if (($roleplay_id         = request_var('roleplay_id', 0)) < 1) {
          trigger_error("No roleplay selected!");
        }

        $sql = 'SELECT id,title,url,owner FROM rpg_roleplays WHERE id = '. (int) $roleplay_id ;
        $roleplay_result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($roleplay_result);
        $db->sql_freeresult($roleplay_result);

				if (in_array($user->data['user_id'], getBannedPlayers($roleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

        $game_masters = array();
        $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].'
                  OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
        $result = $db->sql_query($sql);
        while ($gm_row = $db->sql_fetchrow($result)) {
          $game_masters[] = $gm_row['user_id'];
        }
        $db->sql_freeresult($result);

        $superEditor = (($auth->acl_get('m_')) || ($roleplay['owner'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false;

        if (!$superEditor) {

          $sql = 'SELECT id,name,synopsis FROM rpg_places WHERE owner = '.(int) $user->data['user_id'] . ' AND roleplay_id = '.(int) $roleplay['id'];
          $result = $db->sql_query($sql);
          while ($row = $db->sql_fetchrow($result)) {
            $placesOwned[$row['id']] = $row;
          }
          $db->sql_freeresult($result);

          $sql = 'SELECT count(DISTINCT author_id) as count FROM rpg_content WHERE place_id IN (SELECT id FROM rpg_places WHERE owner = '.(int) $user->data['user_id'] . ' AND roleplay_id = '.(int) $roleplay['id'] .')
                    AND written >= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 14 DAY)';
          $result = $db->sql_query($sql);
          $content = $db->sql_fetchrow($result);
          $db->sql_freeresult($result);

          $count    = $content['count'];
          $sovLevel = sqrt($count);

          if (count($placesOwned) >= floor($sovLevel * 10)) {
            trigger_error('You already run '.$count.' places.  Your sovereignty level is only '. $sovLevel .', which allows you to run '
              . floor($sovLevel * 10) . ' locations.  Perhaps try giving a location away to another player?');
          }

        }

				if ($submit) {
			
					if (!$parent_id = request_var('parent_id', 0)) {
						trigger_error("No parent location selected!");
					}

					if (strlen($place_name 			= utf8_normalize_nfc(request_var('name', '', true))) < 1) {
						trigger_error("No name specified!");
					}

					if (strlen($place_synopsis 		= utf8_normalize_nfc(request_var('synopsis', '', true))) < 1) {
						trigger_error("No synopsis specified!");
					}

					if (strlen($place_description 	= utf8_normalize_nfc(request_var('description', '', true))) < 1) {
						trigger_error("No description specified!");
					}
					
					

		      	/* Not sure why this is here does nothing kingjpc 12/11/11 also changes name to title. 
		      $sql = 'SELECT id, title, owner ,url FROM rpg_roleplays WHERE id = '. (int) $roleplay_id ;
		      $roleplay_result = $db->sql_query($sql);
		      $roleplay = $db->sql_fetchrow($roleplay_result);
		      $db->sql_freeresult($roleplay_result);
			*/

					// variables to hold the parameters for submit_post
					$poll = $uid = $bitfield = $options = ''; 

					generate_text_for_storage($place_description, $uid, $bitfield, $options, true, true, true);					
					
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
										trigger_error('This image is larger than 100x100.  Resize it. <a href="#" onClick="history.go(-1)">Back</a>');
									}
						 
								}
							}
						}
						catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}
					
					
					if ($imgData) {
						$sql = "INSERT INTO rpg_places (owner,roleplay_id,parent_id,name,url,synopsis,description,description_uid,description_bitfield,image,image_type)
								VALUES (".(int) $user->data['user_id'].",'".$db->sql_escape($roleplay_id)."','".$db->sql_escape($parent_id)."','".$db->sql_escape($place_name)."','".urlify($place_name)."','".$db->sql_escape($place_synopsis)."','".$db->sql_escape($place_description)."','".$uid."','".$bitfield."','".$imgData."','".$imgSize['mime']."')";
					} else {
						$sql = "INSERT INTO rpg_places (owner,roleplay_id,parent_id,name,url,synopsis,description,description_uid,description_bitfield)
									VALUES (".(int) $user->data['user_id'].",'".$db->sql_escape($roleplay_id)."','".$db->sql_escape($parent_id)."','".$db->sql_escape($place_name)."','".urlify($place_name)."','".$db->sql_escape($place_synopsis)."','".$db->sql_escape($place_description)."','".$uid."','".$bitfield."')";
					}

					
					if ($result = $db->sql_query($sql)) {
						// TODO:  Return to the #places tab of the actual roleplay
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$roleplay['url'].'/places/'.urlify($place_name).'/');
						trigger_error("Successfully added that place to the roleplay.");
					} else {
						trigger_error("There was some sort of error adding the location to this roleplay.");
					}				
				
					trigger_error("Got through the system!");
					
				} else {
				
					if (($roleplay_id = request_var('roleplay_id', '')) < 1) {
						trigger_error("You didn't select a roleplay.");
					}

          $parent_id = request_var('parent_id', 0);

          if ($parent_id > 0) {
            $sql = 'SELECT id,name,url,synopsis FROM rpg_places WHERE id = '. (int) $parent_id ;
            $placeResult = $db->sql_query($sql);
            $parent = $db->sql_fetchrow($placeResult);
            $db->sql_freeresult($placeResult);

            $template->assign_vars(array(
              'PARENT_ID'      => $parent_id,
              'PARENT_NAME'      => $parent['name'],
              'PARENT_URL'      => $parent['url'],
              'PARENT_SYNOPSIS'      => $parent['synopsis'],
            ));
          }

					$template->assign_var('PARENT_OPTIONS', get_parent_options($roleplay_id));
          $template->assign_vars(array(
            'ROLEPLAY_ID'     => $roleplay['id'],
            'ROLEPLAY_TITLE'  => $roleplay['title'],
            'ROLEPLAY_URL'    => $roleplay['url'],
          ));

					// Set desired template
					$this->tpl_name = 'ucp_roleplays_places_add';
					$this->page_title = 'Add Place to Roleplay';
			
				}
			break;
			case "edit_place":
			  $place_id 				= request_var('place_id', 0);

				
				$sql = 'SELECT p.url as place_url, r.owner,p.owner as place_owner,r.id,r.owner,r.url,p.parent_id,p.roleplay_id
					FROM rpg_places p
						INNER JOIN rpg_roleplays r ON p.roleplay_id = r.id
					WHERE p.id = '.$place_id;
				$result = $db->sql_query_limit($sql, 1);
				$roleplay = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (in_array($user->data['user_id'], getBannedPlayers($roleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

        $game_masters = array();
        $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
        $result = $db->sql_query($sql);
        while ($gm_row = $db->sql_fetchrow($result)) {
	        $game_masters[] = $gm_row['user_id'];
        }
        $db->sql_freeresult($result);					
		  
		  
				if ($submit) {
			
					if ($place_id < 1) {
						trigger_error("No place selected!");
					}

					if ((!$auth->acl_get('a_')) && ($roleplay['owner'] != $user->data['user_id']) && ($roleplay['place_owner'] != $user->data['user_id']) && (!in_array($user->data['user_id'], $game_masters))) {
						trigger_error('You do not own this roleplay!');
					}

					if (strlen($place_synopsis 		= utf8_normalize_nfc(request_var('synopsis', '', true))) < 1) {
						trigger_error("No synopsis specified!");
					}

					if (strlen($place_description 	= utf8_normalize_nfc(request_var('description', '', true))) < 1) {
						trigger_error("No description specified!");
					}

					// variables to hold the parameters for submit_post
					$poll = $uid = $bitfield = $options = ''; 

					generate_text_for_storage($place_description, $uid, $bitfield, $options, true, true, true);					
					
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
										trigger_error('This image is larger than 100x100.  Resize it.  <a href="#" onClick="history.go(-1)">Back</a>');
									}
						 
								}
							}
						}
						catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}
					
          /*
					if ($row['parent_id'] == $place_id) {
						trigger_error('Technically, you just broke the laws of physics. Call Rem.');
					}
					*/
					
					if ($imgData) {
						$sql = "UPDATE rpg_places SET
									synopsis 				= '".$db->sql_escape($place_synopsis)."',
									/* parent_id 				= '".(int) request_var('parent_id', 0)."', */
									description 			= '".$db->sql_escape($place_description)."',
									description_uid 		= '".$uid."',
									description_bitfield	= '".$bitfield."',
									image					= '".$imgData."',
									image_type				= '".$imgSize['mime']."'	
									
								WHERE id = ".$place_id;
					} else {
						$sql = "UPDATE rpg_places SET
									synopsis 				= '".$db->sql_escape($place_synopsis)."',
									/* parent_id 				= '".(int) request_var('parent_id', 0)."', */
									description 			= '".$db->sql_escape($place_description)."',
									description_uid 		= '".$uid."',
									description_bitfield	= '".$bitfield."'
								WHERE id = ".$place_id;					
					}
								
					if ($result = $db->sql_query($sql)) {
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$roleplay['url']."/places/".$roleplay['place_url']."/");
						trigger_error("Successfully edited that location!");
					} else {
						trigger_error("There was some sort of error editing this location.");
					}				
				
					trigger_error("Got through the system!");
					
				} else {
							
					if (($place_id = request_var('place_id', '')) < 1) {
						trigger_error("You didn't select a place to edit.");
					}
					
					
					$sql = 'SELECT p.synopsis, p.description, p.description_uid, p.description_bitfield, p.name, p.parent_id, p.owner as place_owner, r.owner, r.title, r.id
						FROM rpg_places p
							INNER JOIN rpg_roleplays r ON p.roleplay_id = r.id
						WHERE p.id = '.$place_id;
					$result = $db->sql_query_limit($sql, 1);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
					
					
					if ((!$auth->acl_get('a_')) && ($row['owner'] != $user->data['user_id']) && ($row['place_owner'] != $user->data['user_id']) && (!in_array($user->data['user_id'], $game_masters))) {
						trigger_error('You do not own this roleplay!');
					}
					
					decode_message($row['description'], $row['description_uid']);
					
					$template->assign_var('PARENT_OPTIONS', get_parent_options($row['id'],$row['parent_id'],$place_id));
					
					$template->assign_vars(array(
						'PLACE_ID' 			=> $place_id,
						'SYNOPSIS' 			=> $row['synopsis'],
						'DESCRIPTION' 		=> $row['description'],
						'NAME' 				=> $row['name'],
						'ROLEPLAY_TITLE' 	=> $row['title'],
					));
				
					// Set desired template
					$this->tpl_name = 'ucp_roleplays_places_edit';
					$this->page_title = 'Edit Place';
			
				}
			break;
			case 'add_tag':

				$sql = "SELECT title, url, id FROM rpg_roleplays WHERE id = ".$db->sql_escape(request_var('id',0));
				$result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

				if (in_array($user->data['user_id'], getBannedPlayers(request_var('id', 0)))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}


				if ($submit) {
					$tag = strtolower(request_var('tag', ''));
					$roleplay = request_var('id',0);
				    
					if (isset($tag) && isset($roleplay)) {
					
						$tags = explode(",",$tag);
						
						foreach ($tags as $this_tag) {
							$this_tag = trim(strip_tags($this_tag));
						
							$data = array(
								'topic_id' 		=> 0,
								'tag' 			=> $db->sql_escape($this_tag),
								'roleplay_id' 	=> $db->sql_escape($roleplay)
							);
							$sql = 'INSERT IGNORE INTO gateway_tags ' . $db->sql_build_array('INSERT', $data);
					        		
							$db->sql_query($sql);
						}
						
                        $sql = "SELECT url from rpg_roleplays where id=".$roleplay."";
                        $result = $db->sql_query($sql);
                        $row = $db->sql_fetchrow($result);
                        $url = $row['url'];
                        $db->sql_freeresult($result);

						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$url."/");
						trigger_error("Successfully added that tag to this roleplay!");			

				
					
					} else {
						trigger_error('You must specify the tag and the roleplay.');
					}
				} else { 
					if (($roleplay_id = request_var('id', 0)) < 1) {
						trigger_error("You didn't select a roleplay.");
					}
					
					$template->assign_var('ROLEPLAY_ID', $roleplay_id);

					$this->tpl_name = 'ucp_roleplays_tags_add';
                                        $this->page_title = 'Add Tags to Roleplay';
				}
			break;
			case 'remove_tag':
			
				if ($submit) {
					$tag = strtolower(request_var('tag', ''));
					$roleplay = request_var('id',0);
					
					if (isset($tag) && isset($roleplay)) {
					
						$tags = explode(",",$tag);
						
						foreach ($tags as $this_tag) {
							$this_tag = trim(strip_tags($this_tag));
						
							$data = array(
								'topic_id' 		=> 0,
								'tag' 			=> $db->sql_escape($this_tag),
								'roleplay_id' 	=> $db->sql_escape($roleplay)
							);
							$sql = "DELETE FROM gateway_tags WHERE roleplay_id='".$data['roleplay_id']."' AND tag='".$data['tag']."'";
							
							$db->sql_query($sql);
						}
						

						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/");
						trigger_error("Successfully removed that tag from this roleplay!");			

				
					
					} else {
						trigger_error('You must specify the tag and the roleplay.');
					}
				} else { 
					if (($roleplay_id = request_var('id', 0)) < 1) {
						trigger_error("You didn't select a roleplay.");
					}
					
					$template->assign_var('ROLEPLAY_ID', $roleplay_id);

					$this->tpl_name = 'ucp_roleplays_tags_remove';
					$this->page_title = 'Remove Tags from Roleplay';
				}
			break;
			case 'filter':
				$this->tpl_name = 'ucp_roleplays_tags_add';
				$this->page_title = 'Manage Roleplay Filters';
			break;
			case "add_thread":


				if (($roleplay_id = request_var('roleplay_id', '')) < 1) {
					trigger_error("No roleplay selected!");
				}

				$sql = "SELECT title, url, id FROM rpg_roleplays WHERE id = ".$db->sql_escape($roleplay_id);
				$result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

				if (in_array($user->data['user_id'], getBannedPlayers($roleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

				$sql = 'SELECT id FROM rpg_characters WHERE owner = '.(int) $user->data['user_id'].' AND roleplay_id = '.(int) $roleplay_id;
				$result	= $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if ($row['id'] >= 1) {

					if ($submit) {

						$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
						$allow_bbcode = $allow_urls = $allow_smilies = true;

						// note that multibyte support is enabled here 
						$subject = utf8_normalize_nfc(request_var('title', '', true));
						$text    = utf8_normalize_nfc(request_var('text', '', true));

						// variables to hold the parameters for submit_post
						$poll = $uid = $bitfield = $options = ''; 

						//generate_text_for_storage($subject, $uid, $bitfield, $options, false, false, false);
						generate_text_for_storage($text, $uid, $bitfield, $options, true, true, true);					

						$data = array( 
							// General Posting Settings
							'forum_id'            => 20,    // The forum ID in which the post will be placed. (int)
							'topic_id'            => 0,    // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
							'roleplay_id'         => $roleplay_id,
							'icon_id'            => false,    // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)

							// Defining Post Options
							'enable_bbcode'    => true,    // Enable BBcode in this post. (bool)
							'enable_smilies'    => true,    // Enabe smilies in this post. (bool)
							'enable_urls'        => true,    // Enable self-parsing URL links in this post. (bool)
							'enable_sig'        => true,    // Enable the signature of the poster to be displayed in the post. (bool)

							// Message Body
							'message'            => $text,        // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
							'message_md5'    => md5($text),// The md5 hash of your message

							// Values from generate_text_for_storage()
							'bbcode_bitfield'    => $bitfield,    // Value created from the generate_text_for_storage() function.
							'bbcode_uid'        => $uid,        // Value created from the generate_text_for_storage() function.

							// Other Options
							'post_edit_locked'    => 0,        // Disallow post editing? 1 = Yes, 0 = No
							'topic_title'        => $subject,    // Subject/Title of the topic. (string)

							// Email Notification Settings
							'notify_set'        => false,        // (bool)
							'notify'            => false,        // (bool)
							'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
							'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)

							// Indexing
							'enable_indexing'    => true,        // Allow indexing the post? (bool)
						);						

						include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
				
					
						if (!$ooc = submit_post('post',$subject, '', POST_NORMAL, $poll, $data)) {
							trigger_error('Something went wrong while creating your thread. Please try again.');
						}
				
				
						$sql = "INSERT INTO rpg_roleplay_threads (roleplay_id,thread_id,type)
									VALUES ('".$db->sql_escape($roleplay_id)."','".$db->sql_escape($data['topic_id'])."','Out Of Character')";

						if ($result = $db->sql_query($sql)) {

				      $sql = 'SELECT id,title,url FROM rpg_roleplays WHERE id = '. (int) $roleplay_id ;
				      $roleplay_result = $db->sql_query($sql);
				      $roleplay = $db->sql_fetchrow($roleplay_result);
				      $db->sql_freeresult($roleplay_result);

						
						  $chat_message = '[url=http://www.roleplaygateway.com/member-u'.$user->data['user_id'].'.html]'.$user->data['username'] . '[/url] has added a new topic titled "[url=http://www.roleplaygateway.com/viewtopic.php?t='.$data['topic_id'].']'.$db->sql_escape($subject).'[/url]" to [url=http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/#ooc]'.$roleplay['title'].'\'s forum[/url].' ;	
						
              $sql = "INSERT INTO ajax_chat_messages
              (`userId`,`userRole`,`ip`,`dateTime`,`userName`,`channel`,`text`,`roleplayID`) 
              VALUES ('2147483647','4','127.0.0.1',NOW(),'Game Master (GM)','0','".$db->sql_escape($chat_message)."',".(int) $roleplay_id.")";
              $db->sql_query($sql);
						
							meta_refresh(3, 'http://www.roleplaygateway.com/viewtopic.php?t='.$data['topic_id']);
							trigger_error("Successfully added that thread to the roleplay.");
						} else {
							trigger_error("There was some sort of error adding the thread to the roleplay.");
						}

					
					} else {
				
						if (($roleplay_id = request_var('roleplay_id', '')) < 1) {
							trigger_error("You didn't select a roleplay.");
						}
				
						$template->assign_var('ROLEPLAY_ID', $roleplay_id);
				
						$sql = "SELECT topic_title,topic_id FROM gateway_topics
									WHERE topic_poster = ".$user->data['user_id']."
									ORDER BY topic_time DESC
									";
								
						$result = $db->sql_query_limit($sql,300);

						while($row = $db->sql_fetchrow($result)) {
						
							$template->assign_block_vars('threads', array(
								'THREAD_ID'	=> $row['topic_id'],
								'THREAD_TITLE'	=> $row['topic_title'],
							));
						
						}
				
						// Set desired template
						$this->tpl_name = 'ucp_roleplays_threads_add';
						$this->page_title = 'Add Thread to Roleplay';
			
					}
				} else {				
		
					trigger_error("You haven't submitted a character to this roleplay. You must at least have some stake in it before you can create a new topic in its OOC forum.");

				}
			break;
			case "remove_thread":
			
				if (($thread_id = intval(request_var('thread_id', ''))) < 1) {
					trigger_error("No thread selected!");
				}
				if (($roleplay_id = intval(request_var('roleplay_id', ''))) < 1) {
					trigger_error("No roleplay selected!");
				}
				
				// check mode
				if (confirm_box(true)) {
					$sql = "DELETE FROM rpg_roleplay_threads WHERE
						roleplay_id = '".$db->sql_escape($roleplay_id)."' AND
						thread_id = '".$db->sql_escape($thread_id)."'";
					$db->sql_query($sql);
					meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=edit&id=".$roleplay_id);
					trigger_error("Successfully removed that thread from the roleplay.");
				} else {
				
					$sql = "SELECT topic_title FROM gateway_topics WHERE topic_id = ".$db->sql_escape($thread_id);
					
					if ($result = $db->sql_query($sql)) {
						while ($row = $db->sql_fetchrow($result)) {
							$thread_link = '<a href="http://www.roleplaygateway.com/viewtopic.php?t='.$thread_id.'">'.$row['topic_title'].'</a>';
						}
					} else {
						$thread_link = "this thread";
					}
				
					// I don't know of any other way to do this... but I agree, it looks ugly
					if ($confirm_key = $_REQUEST['confirm_key']) {
						meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=edit&id=".$roleplay_id);
						trigger_error("You chose not to remove the thread from the roleplay.");					
					} else {
					   $s_hidden_fields = build_hidden_fields(array(
							'submit'    => true,
							'my_mesage' => $my_message,
							)
						);
						confirm_box(false, "Are you <strong>sure</strong> that you want to remove ". $thread_link ." from the roleplay?",$hidden_fields);
					}
				}
			
				trigger_error("Got through the system!");
				
			break;	
			case "approve_player":
			
				if (($character_id = request_var('character_id', 0)) < 1) {
					trigger_error("No character selected! (".$character_id.")");
				}
				if (($roleplay_id = request_var('roleplay_id', 0)) < 1) {
					trigger_error("No roleplay selected!");
				}

        $sql = 'SELECT id, title, owner FROM rpg_roleplays WHERE id = '.(int) $roleplay_id;
        $result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);
				
	      $game_masters = array();
	      $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
	      $result = $db->sql_query($sql);
	      while ($gm_row = $db->sql_fetchrow($result)) {
		      $game_masters[] = $gm_row['user_id'];
	      }
	      $db->sql_freeresult($result);

        if (!in_array($user->data['user_id'], $game_masters)) {
          trigger_error('You aren\'t allowed to do this, as you do not have permissions to do so.');
        }

				$sql = "UPDATE rpg_roleplay_players
							SET approved = 1
							WHERE roleplay_id = ".$db->sql_escape($roleplay_id)."
								AND character_id = ".$db->sql_escape($character_id);

				if ($db->sql_query($sql)) {
				
					$sql 		= 'SELECT title as roleplay,name,r.url as roleplay_url,c.owner FROM rpg_characters c INNER JOIN rpg_roleplays r ON r.id = c.roleplay_id WHERE character_id = '.(int) $character_id;
					$result 	= $db->sql_query($sql);
					$character 	= $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
				
					include_once($phpbb_root_path . 'includes/functions_privmsg.' . $phpEx);
					
					// note that multibyte support is enabled here 
					$my_subject = utf8_normalize_nfc($character['roleplay'].': '.$character['name'].' has been approved.');
					$my_text    = utf8_normalize_nfc('You are now permitted to [url=http://www.roleplaygateway.com/roleplay/'.$character['roleplay_url'].'/#posting]post in '.$character['roleplay'].'[/url], as your character "'.$character['name'].'" has been approved.');

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
				
				
					meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=edit&id=".$roleplay_id);
					trigger_error("Successfully approved that character!");
				} else {
					trigger_error("There was some sort of error approving the character to play in this roleplay.");
				}				
			
				trigger_error("Got through the system!");
			break;	
			case "add_player":
			
				if (($character_id = request_var('character_id', '')) < 1) {
					trigger_error("No character selected! (".$character_id.")");
				}
				if (($roleplay_id = request_var('roleplay_id', '')) < 1) {
					trigger_error("No roleplay selected!");
				}

				$sql = "INSERT INTO rpg_roleplay_players (roleplay_id,character_id)
							VALUES ('".$db->sql_escape($roleplay_id)."','".$db->sql_escape($character_id)."')";

				if ($db->sql_query($sql)) {
					meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=edit&id=".$roleplay_id);
					trigger_error("Successfully added that character to the roleplay.");
				} else {
					trigger_error("There was some sort of error adding the character to the roleplay.");
				}				
			
				trigger_error("Got through the system!");
			break;
			case "remove_player":
			
				if (($player_id = request_var('character_id', '')) < 1) {
					trigger_error("No player selected!");
				}
				if (($roleplay_id = request_var('roleplay_id', '')) < 1) {
					trigger_error("No roleplay selected!");
				}

        $sql = 'SELECT id, title, owner FROM rpg_roleplays WHERE id = '.(int) $roleplay_id;
        $result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);
				
	      $game_masters = array();
	
	      $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
	      $result = $db->sql_query($sql);
	      while ($gm_row = $db->sql_fetchrow($result)) {
		      $game_masters[] = $gm_row['user_id'];
	      }
	      $db->sql_freeresult($result);

        if (!in_array($user->data['user_id'], $game_masters)) {
          trigger_error('You aren\'t allowed to do this, as you do not have permissions to do so.');
        }

				if (confirm_box(true)) {
					/* $sql = "DELETE FROM rpg_roleplay_players WHERE
							roleplay_id = '".$db->sql_escape($roleplay_id)."' AND
							character_id = '".$db->sql_escape($player_id)."'"; */

              $sql = 'UPDATE rpg_characters SET creator = owner, isAdoptable = 0, approved = 0, status =  "Rejected" WHERE id = '.(int) $player_id; 
							$db->sql_query($sql);
							meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays");
							trigger_error("Successfully removed that character from the roleplay.");
							
							
				} else {
				
					$sql = "SELECT name FROM rpg_characters WHERE id = ".$db->sql_escape($player_id);
					
					if ($result = $db->sql_query($sql)) {
						while ($row = $db->sql_fetchrow($result)) {
							$character_link = '<a href="http://www.roleplaygateway.com/characters/'.$row['name'].'">'.$row['name'].'</a>';
						}
					} else {
						$character_link = "this character";
					}
				
					// I don't know of any other way to do this... but I agree, it looks ugly
					if ($confirm_key = $_REQUEST['confirm_key']) {
						meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays");
						trigger_error("You chose not to remove $character_link from the roleplay.");					
					} else {
					   $s_hidden_fields = build_hidden_fields(array(
							'submit'    => true,
							'my_mesage' => $my_message,
							)
						);
						confirm_box(false, "Are you <strong>sure</strong> that you want to remove ". $character_link ." from the roleplay?",$hidden_fields);
					}
				}			
			
				trigger_error("Got through the system!");
				
			break;	
			case "invite_player":
			
				if ($submit) {
			
					if (($character_id = request_var('character_id', '')) < 1) {
						trigger_error("No character selected! (".$character_id.")");
					}
					if (($roleplay_id = request_var('roleplay_id', '')) < 1) {
						trigger_error("No roleplay selected!");
					}

					$sql = "INSERT INTO rpg_roleplay_players (roleplay_id,character_id)
								VALUES ('".$db->sql_escape($roleplay_id)."','".$db->sql_escape($character_id)."')";

					if ($db->sql_query($sql)) {
						meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=edit&id=".$roleplay_id);
						trigger_error("Successfully added that character to the roleplay.");
					} else {
						trigger_error("There was some sort of error adding the character to the roleplay.");
					}
					
					$sql = 'SELECT title FROM rpg_roleplays WHERE id = '.$roleplay_id;
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result)) {
						$roleplay_name = $row['title'];
					}
					
					include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
					
					// note that multibyte support is enabled here 
					$my_subject = 'Invitation to roleplay "'.$db->sql_escape($roleplay_name).'"';
					$my_text    = 'You have been invited to join the following roleplay: [url=http://www.roleplaygateway.com/roleplays/'.$roleplay_id.']'.$roleplay_name.'[/url].  Visit your';

					// variables to hold the parameters for submit_pm
					$poll = $uid = $bitfield = $options = ''; 
					generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
					generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

					$data = array( 
						'address_list'      => array ('u' => array($user->data['user_id'] => 'to')),
						'from_user_id'      => $user->data['user_id'],
						'from_username'     => 'test',
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
				} else {
				
					if (($roleplay_id = request_var('roleplay_id', '')) < 1) {
						trigger_error("No roleplay selected!");
					}
				
					$template->assign_var('ROLEPLAY_ID', $roleplay_id);
				
					$sql = "SELECT id,name FROM rpg_characters c
								INNER JOIN gateway_zebra z ON
									z.zebra_id = c.owner
								WHERE z.user_id = ".$user->data['user_id']."
								AND c.id NOT IN (SELECT character_id FROM rpg_roleplay_players WHERE roleplay_id = ".$db->sql_escape($roleplay_id)." )
								ORDER BY c.name
								";
								
					$result = $db->sql_query($sql,3600);

					while($row = $db->sql_fetchrow($result)) {
						
						$template->assign_block_vars('characters', array(
							'ID'	=> $row['id'],
							'NAME'	=> $row['name'],
						));
						
					}
				}
				
				// Set desired template
				$this->tpl_name = 'ucp_roleplays_invite';
				$this->page_title = 'Invite To Roleplay';

			break;
			case 'review':
			
				if ($submit) {
					$roleplay_id	 		= request_var('roleplay_id', 0);
					$characterization	 	= request_var('characterization', '');
					$plot	 				= request_var('plot', '');
					$depth	 				= request_var('depth', '');
					$style	 				= request_var('style', '');
					$mechanics	 			= request_var('mechanics', '');
					$overall	 			= request_var('overall', '');
					$commentary	 			= request_var('commentary', '', true);
					
					if (
						($roleplay_id > 0) &&
						(strlen($characterization) > 0) &&
						(strlen($plot) > 0) &&
						(strlen($depth) > 0) &&
						(strlen($style) > 0) &&
						(strlen($mechanics) > 0) &&
						(strlen($overall) > 0)					
					) {
					
						$sql_ary = array(
							'roleplay_id'      		=> $roleplay_id,
							'author'      			=> $user->data['user_id'],
							'characterization'      => $characterization,
							'plot'          		=> $plot,
							'depth'          		=> $depth,
							'style'          		=> $style,
							'mechanics'          	=> $mechanics,
							'overall'          		=> $overall,
							'commentary'         	=> $commentary,
						);

						$sql = 'INSERT INTO rpg_reviews ' . $db->sql_build_array('INSERT', $sql_ary);
						
						$db->sql_query($sql);
						meta_refresh(3,'http://www.roleplaygateway.com/roleplay/');
						trigger_error('You have successfully reviewed this roleplay.');
					} else {
					
						trigger_error('You did not fill all of the required information out!');
					}
				} else {
				
					$template->assign_vars(array(
						'ROLEPLAY_ID'				=> request_var('roleplay_id',0),
					));
				
					// Set desired template
					$this->tpl_name = 'ucp_roleplays_reviews_add';
					$this->page_title = 'Invite To Roleplay';
				}
			break;
			case 'edit':
			
				$sql = "SELECT owner,title,url,created FROM rpg_roleplays WHERE id = ".request_var('id', 0);
				$result = $db->sql_query($sql);
				$row	= $db->sql_fetchrow($result);
				$roleplay_title = $roleplay_old_title = $row['title'];
				
				if (($row['owner'] != $user->data['user_id']) && (!$auth->acl_get('m_'))) {
					trigger_error('You do not own this roleplay. Owner:'.$row['owner']);
				}
				
				
				if ($auth->acl_get('m_') || (((time() - strtotime($row['created'])) <= 86400) )) {
					$can_superedit = true;
				} else {
					$can_superedit = false;
				}
				
				
			
				if ($submit) {
				
					$roleplay_id			= request_var('id', 0);
					
					$roleplay_title				= ($can_superedit) ? request_var('roleplay_title', $row['title'], true) : $row['title'];
					$roleplay_url				= ($can_superedit) ? urlify(request_var('roleplay_title',$row['title'], true)) : $row['url'];
				
					$roleplay_description 			= utf8_normalize_nfc(request_var('roleplay_description', '', true));
					$roleplay_introduction 			= utf8_normalize_nfc(request_var('roleplay_introduction', '', true));
					$roleplay_rules 				= utf8_normalize_nfc(request_var('roleplay_rules', '', true));
					$roleplay_status				= request_var('roleplay_status', '');
					$roleplay_approval	 			= request_var('roleplay_approval', 0);
					$roleplay_characters_lock	 	= request_var('lock_characters', 0);
					$roleplay_characters_limit	 	= request_var('character_limit', null);
					$min_words	 					= request_var('min_words', 0);
					$max_words	 					= request_var('max_words', 0);
					//$roleplay_slots			= intval(request_var('roleplay_slots', ''));
					
					$text = utf8_normalize_nfc(request_var('roleplay_introduction', '', true));
					
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
										trigger_error('This image is larger than 100x100.  Resize it. <a href="#" onClick="history.go(-1)">Back</a>');
									}
						 
								}
							}
						}
						catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}
					
					
					$rules_uid = $rules_bitfield = $uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);				
					generate_text_for_storage($roleplay_rules, $rules_uid, $rules_bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);				
					
					if ($imgData) {
					
						$sql = "UPDATE rpg_roleplays SET
								title	= '".$db->sql_escape($roleplay_title)."',
								url	= '".$roleplay_url."',
								introduction = '".$db->sql_escape($text)."',
								introduction_uid = '".$db->sql_escape($uid)."',
								introduction_bitfield = '".$db->sql_escape($bitfield)."',
								rules = '".$db->sql_escape($roleplay_rules)."',
								rules_uid = '".$db->sql_escape($rules_uid)."',
								rules_bitfield = '".$db->sql_escape($rules_bitfield)."',
								description = '".$db->sql_escape($roleplay_description)."',
								require_approval = '".$roleplay_approval."',
								updated = null,
								player_slots = 0,
								image = '".$imgData."',
								image_type = '".$imgSize['mime']."'
							WHERE id = ".(int) $roleplay_id;
						
					} else {
						$sql_ary = array(
							'title'				=> $roleplay_title,
							'url'				=> $roleplay_url,
							'introduction'          	=> $text,
							'introduction_uid'      	=> $uid,
							'introduction_bitfield'  	=> $bitfield,
							'rules'			          	=> $roleplay_rules,
							'rules_uid'      			=> $rules_uid,
							'rules_bitfield'  			=> $rules_bitfield,
							'description'				=> $roleplay_description,
							'require_approval'			=> $roleplay_approval,
							'updated'					=> null,
							'player_slots'				=> 0,
						);

						$sql = 'UPDATE rpg_roleplays SET ' . $db->sql_build_array('UPDATE', $sql_ary). ' WHERE id = ' . (int) $roleplay_id;
					}
					
					if ($db->sql_query($sql)) {
					
					  if ($can_superedit == true) {
					    if ($roleplay_old_title != $roleplay_title) {
					      add_log('mod', 'LOG_ROLEPLAY_EDIT', $user->data['username'] . ' edited a roleplay\'s title.', 'Edited roleplay "'.$roleplay_old_title.'": changed title to "'.$roleplay_title.'"');
					    }
					  }
					
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".urlify($roleplay_title).'/');
						trigger_error("Successfully edited your roleplay!");
						
					} else {
						trigger_error('Something went wrong. Call the Coders!');
					}
				}
			
				if (($roleplay_id = request_var('id', '')) < 1) {
					trigger_error('You must access this page from the <a href="http://www.roleplaygateway.com/ucp.php?i=roleplays">main roleplay list</a>!');
				}

				$sql = "SELECT id,title,description,introduction,introduction_uid,rules,rules_uid,owner,player_slots,owner,username,require_approval,status FROM rpg_roleplays
							INNER JOIN gateway_users
								ON rpg_roleplays.owner = gateway_users.user_id
							WHERE rpg_roleplays.id = ".$db->sql_escape($roleplay_id);
				
				$result = $db->sql_query($sql);

				while($row = $db->sql_fetchrow($result)) {
				
					if (($row['owner'] != $user->data['user_id']) && (!$auth->acl_get('m_'))) {
						trigger_error('You cannot edit this roleplay as it does not belong to you!');
					}

					$sql = "SELECT count(*) as players FROM rpg_roleplay_players WHERE roleplay_id = '".$row['id']."'";
								
					$player_result = $db->sql_query($sql);
					
					while($player_row = $db->sql_fetchrow($player_result)) {
						$open_slots = $row['player_slots'] - $player_row['players'];
					}

					decode_message($row['introduction'], $row['introduction_uid']);
					decode_message($row['rules'], $row['rules_uid']);

					$template->assign_vars(array(
						'ROLEPLAY_ID'				=> $row['id'],
						'S_CAN_SUPEREDIT'			=> $can_superedit,
						'TITLE'						=> $row['title'],
						'DESCRIPTION'				=> $row['description'],
						'INTRODUCTION'				=> $row['introduction'],
						'RULES'					=> $row['rules'],
						'STATUS'				=> $row['status'],
						'REQUIRE_APPROVAL_CHECKED'	=> ($row['require_approval'] == 1) ? 'checked' : '',
						'ALLOW_ALL_CHECKED'			=> ($row['require_approval'] == 0) ? 'checked' : '',
						'OWNER_USERNAME'			=> get_username_string('full', $row['owner'], $row['username']),
						'OPEN_SLOTS'				=> $open_slots,
						'PLAYER_SLOTS'				=> $row['player_slots'],
					));
					
					$sql = "SELECT thread_id,topic_title,type FROM rpg_roleplay_threads
								INNER JOIN gateway_topics
									ON rpg_roleplay_threads.thread_id = gateway_topics.topic_id
								WHERE rpg_roleplay_threads.roleplay_id = '".$row['id']."'";
								
					$thread_result = $db->sql_query($sql);
					
					while($thread_row = $db->sql_fetchrow($thread_result)) {
					
						$thread_tag = ($thread_row['type'] == "In Character") ? "[IC] " : "[OOC] ";

						$template->assign_block_vars('threads', array(
							'THREAD_ID'		=> $thread_row['thread_id'],
							//'THREAD_TITLE'	=> $thread_tag . $thread_row['topic_title'],
							'THREAD_TITLE'	=> $thread_row['topic_title'], // removed tag!  Eric M., 4/9/12
						));
						
					}
					
					$sql = "SELECT  gateway_users.user_id,
									gateway_users.username,
									rpg_characters.id,
									rpg_characters.name,
									rpg_characters.synopsis,
									rpg_roleplay_players.approved
								FROM rpg_roleplay_players, gateway_users, rpg_characters
								WHERE rpg_roleplay_players.roleplay_id = '".$row['id']."'
									AND	gateway_users.user_id = rpg_characters.owner
									AND	rpg_roleplay_players.character_id = rpg_characters.id
								ORDER BY approved DESC";
								
					$player_result = $db->sql_query($sql);
					
					while($player_row = $db->sql_fetchrow($player_result)) {
					
						if ($row['require_approval'] == 1) {
							$player_approved = $player_row['approved'];
						} else {
							$player_approved = 1;
						}

						$template->assign_block_vars('players', array(
							'PLAYER_LINK'		=> get_username_string('full', $player_row['user_id'], $player_row['username']),
							'CHARACTER_NAME'	=> $player_row['name'],
							'CHARACTER_ID'		=> $player_row['id'],
							'SYNOPSIS'			=> $player_row['synopsis'],
							'APPROVED'			=> $player_approved,
						));
						
					}
					
				}
				

				// Set desired template
				$this->tpl_name = 'ucp_roleplays_edit';
				$this->page_title = 'Edit Roleplay';
				
			break;
			case "new":

				if ($submit) {
					$roleplay_title			    = request_var('roleplay_title', '', true);
					$roleplay_description 	= request_var('roleplay_description', '', true);
					$roleplay_tags 			    = request_var('roleplay_tags', '');
					$roleplay_introduction 	= request_var('roleplay_introduction', '', true);
					$roleplay_approval	 	  = request_var('roleplay_approval', 0);
					$roleplay_original	 	  = strtolower(request_var('roleplay_original', ''));
					$roleplay_citations	 	  = strtolower(request_var('roleplay_citations', ''));
					$roleplay_setting	 	    = request_var('roleplay_setting', '', true);
					//$roleplay_slots			  = intval(request_var('roleplay_slots', ''));



          $_SESSION['failedRoleplayCompletion'] = true;

          $_SESSION['newRoleplayData'] = array(
            'ROLEPLAY_TITLE'        => $roleplay_title,
            'ROLEPLAY_DESCRIPTION'  => $roleplay_description,
            'ROLEPLAY_TAGS'         => $roleplay_tags,
            'ROLEPLAY_INTRODUCTION' => $roleplay_introduction,
            'ROLEPLAY_APPROVAL'     => $roleplay_approval,
            'ROLEPLAY_SETTING'      => $roleplay_setting,
            'ROLEPLAY_CITATIONS'    => $roleplay_citations,
          );


          meta_refresh(3, 'http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=new');

          if ($roleplay_original == 'yes') {
            $roleplay_tags .= ', original';
          }

          if ($roleplay_original == 'no') {
            $roleplay_tags .= ', fanfic';
            
            if (strlen($roleplay_citations) <= 0) {
               trigger_error('If your roleplay is not original content, you MUST include some citations.');
            }
          }


          if (( strlen($roleplay_original) <= 0) || (($roleplay_original !== 'yes') && ($roleplay_original !== 'no'))) {
            trigger_error('You must specify whether your roleplay is original or not.');
          }

					if (strlen($roleplay_title) <= 0) {            
						trigger_error("Your title is too short.");
					}
 
          if (
              (stristr($roleplay_title, '('))
           || (stristr($roleplay_title, ')'))
           || (stristr($roleplay_title, '['))
           || (stristr($roleplay_title, ']'))
           || (stristr($roleplay_title, '~'))
           || (stristr($roleplay_title, '*'))
           || (stristr($roleplay_title, 'roleplay'))
           || (stristr($roleplay_title, ' rp'))
          ) {
            trigger_error("Your roleplay's title did not follow the guidelines.  You must name your roleplay as if you were titling a book, and it cannot contain any tags or references to the roleplay's status.");
          }

					
					$sql = "SELECT COUNT(id) as count FROM rpg_roleplays WHERE url='".urlify($roleplay_title)."'";
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					if($row['count']>0)
					{
						trigger_error("Roleplay title already in use.");
					}
					$db->sql_freeresult($result);
					
					if (strlen($roleplay_description) > 255) {
						trigger_error("Your description is too long.");
					}
					
					if (strlen($roleplay_setting) <= 0) {
						trigger_error("You didn't provide a setting!");
					}

					if (count($roleplay_tags_array = explode(',',$roleplay_tags)) < 3) {
						trigger_error('You must provide at least 3 tags!');
					}
					
					$text = utf8_normalize_nfc($roleplay_introduction);
					$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);					

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
										trigger_error('This image is larger than 100x100.  Resize it. <a href="#" onClick="history.go(-1)">Back</a>');
									}
						 
								}
							}
						}
						catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}		

					if ($imgData) {
					
						$sql = "INSERT INTO rpg_roleplays
							(introduction, introduction_uid, introduction_bitfield, title, description, citations, owner, require_approval, player_slots, created, url, image, image_type) VALUES
								('".$db->sql_escape($text)."',
								'".$db->sql_escape($uid)."',
								'".$db->sql_escape($bitfield)."',
								'".$db->sql_escape($roleplay_title)."',
								'".$db->sql_escape($roleplay_description)."',
								'".$db->sql_escape($roleplay_citations)."',
								'".$user->data['user_id']."',
								'".$db->sql_escape($roleplay_approval)."',
								0,
								null,
								'".urlify($roleplay_title)."',
								'".$imgData."',
								'".$imgSize['mime']."')";
					
					} else {
					
						$sql_ary = array(
							'introduction'          	=> $text,
							'introduction_uid'      	=> $uid,
							'introduction_bitfield'  	=> $bitfield,
							'title'   				 	=> $roleplay_title,
							'description'				=> $roleplay_description,
							'citations'				=> $roleplay_citations,
							'owner'						=> $user->data['user_id'],
							'require_approval'			=> $roleplay_approval,
							'player_slots'				=> 0,
							'created'					=> date('Y-m-d H:i:s'),
							'url'						=> urlify($roleplay_title),
							'image'						=> $imgData,
							'image_type'				=> $imgData['mime'],
						);

						$sql = 'INSERT INTO rpg_roleplays ' . $db->sql_build_array('INSERT', $sql_ary);
						
						//trigger_error($sql);
					}
					
					if (!$db->sql_query($sql)) {
						trigger_error('call eric.');
					}
					$roleplay_id = $db->sql_nextid();
					
					
					
					// note that multibyte support is enabled here 
					//$ooc_subject = utf8_normalize_nfc('[OOC] '.$roleplay_title);
					$ooc_subject = utf8_normalize_nfc($roleplay_title); // removed tags! Eric M. 4/9/12
					$ooc_text    = 'This is the auto-generated OOC topic for the roleplay "[url=http://www.roleplaygateway.com/roleplay/'.urlify($roleplay_title).'/]'.$roleplay_title."[/url]\"\n\nYou may edit this first post as you see fit.";

					// variables to hold the parameters for submit_post
					$poll = $uid = $bitfield = $options = ''; 

					//generate_text_for_storage($ooc_subject, $uid, $bitfield, $options, false, false, false);
					generate_text_for_storage($ooc_text, $uid, $bitfield, $options, true, true, true);					
	
					$data = array( 
						// General Posting Settings
						'forum_id'            => 20,    // The forum ID in which the post will be placed. (int)
						'topic_id'            => 0,    // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
						'roleplay_id'         => (int) $roleplay_id,
						'icon_id'            => false,    // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)

						// Defining Post Options
						'enable_bbcode'    => true,    // Enable BBcode in this post. (bool)
						'enable_smilies'    => true,    // Enabe smilies in this post. (bool)
						'enable_urls'        => true,    // Enable self-parsing URL links in this post. (bool)
						'enable_sig'        => true,    // Enable the signature of the poster to be displayed in the post. (bool)

						// Message Body
						'message'            => $ooc_text,        // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
						'message_md5'    => md5($ooc_text),// The md5 hash of your message

						// Values from generate_text_for_storage()
						'bbcode_bitfield'    => $bitfield,    // Value created from the generate_text_for_storage() function.
						'bbcode_uid'        => $uid,        // Value created from the generate_text_for_storage() function.

						// Other Options
						'post_edit_locked'    => 0,        // Disallow post editing? 1 = Yes, 0 = No
						'topic_title'        => $ooc_subject,    // Subject/Title of the topic. (string)

						// Email Notification Settings
						'notify_set'        => false,        // (bool)
						'notify'            => false,        // (bool)
						'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
						'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)

						// Indexing
						'enable_indexing'    => true,        // Allow indexing the post? (bool)
					);						
	
					include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
										
					
					if ($ooc = submit_post('post',$ooc_subject, '', POST_NORMAL, $poll, $data)) {		
						
						// Insert a thread so it is tracked on the OOC tab
						$sql_ary = array(
							'roleplay_id'       => $roleplay_id,
							'thread_id'      	=> $data['topic_id'],
							'type'  			=> 'Out Of Character',
						);
						$sql = 'INSERT INTO rpg_roleplay_threads ' . $db->sql_build_array('INSERT', $sql_ary);
						$db->sql_query($sql);
						
						generate_text_for_storage($roleplay_title, $uid, $bitfield, $options, true, true, true);
						
						// Insert a place so this roleplay has some rooms in chat...
						$sql_ary = array(
							'name'      			=> $roleplay_setting,
							'description'      		=> 'Default Location for ' . $roleplay_title,
							'description_bitfield'  => $bitfield,
							'description_uid'  		=> $uid,
							'roleplay_id'       	=> $roleplay_id,
							'parent_id'       		=> (int) '-1',
							'owner'      			=> $user->data['user_id'],
							'url'  					=> urlify($roleplay_setting),
						);
						$sql = 'INSERT INTO rpg_places ' . $db->sql_build_array('INSERT', $sql_ary);
						$db->sql_query($sql);

						# Tag addition
						foreach ($roleplay_tags_array as $this_tag) {
							$this_tag = strtolower(trim($this_tag));
						
							$data = array(
								'topic_id' 		=> 0,
								'tag' 			=> $db->sql_escape($this_tag),
								'roleplay_id' 	=> $db->sql_escape($roleplay_id)
							);
							$sql = 'INSERT IGNORE INTO gateway_tags ' . $db->sql_build_array('INSERT', $data);
							
							$db->sql_query($sql);
						}
					  
            $_SESSION['failedRoleplayCompletion'] = false;
						meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".urlify($roleplay_title).'/');
						trigger_error("Successfully created your roleplay!");
						
					
						
					} else {
						trigger_error('Something went wrong during the creation of your OOC topic. Call <a href="http://www.roleplaygateway.com/memberlist.php?mode=group&g=2637">the Coders</a>!');
					}
				}

        if (!empty($_SESSION['newRoleplayData'])) {
				  $template->assign_vars($_SESSION['newRoleplayData']);		
        }

				// Set desired template
				$this->tpl_name = 'ucp_roleplays_new';
				$this->page_title = 'New Roleplay';
				
			break;
			case 'edit_content':
			
				$content_id		= request_var('content_id', 0);
				$text	= utf8_normalize_nfc(request_var('reply_content', '', true));
			
				$sql = 'SELECT c.text,c.bbcode_uid,c.author_id,p.url as place_url,p.name as place_name,r.url as roleplay_url,r.title as roleplay_name,r.owner,r.id FROM rpg_content c
						INNER JOIN rpg_roleplays r ON c.roleplay_id = r.id
						INNER JOIN rpg_places p ON c.place_id = p.id
					WHERE c.id = '.(int) $content_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (in_array($user->data['user_id'], getBannedPlayers($row['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

        $game_masters = array();
        $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $row['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $row['id'].' AND isCoGM = 1)';
        $result = $db->sql_query($sql);
        while ($gm_row = $db->sql_fetchrow($result)) {
	        $game_masters[] = $gm_row['user_id'];
        }
        $db->sql_freeresult($result);

				if ((!$auth->acl_get('a_')) && ($row['owner'] != $user->data['user_id']) && (!in_array($user->data['user_id'], $game_masters))
						&& ($row['author_id'] != $user->data['user_id']) && (!$auth->acl_get('m_'))) {
					trigger_error('You do not own this content. Owner:'.$row['content_id']);
				}
			
				if ($submit) {
			
					$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

					$sql_ary = array(
						'id'      			=> $content_id,
						'text'          	=> $text,
						'bbcode_uid'      	=> $uid,
						'bbcode_bitfield' 	=> $bitfield,
					);

					$sql = 'UPDATE rpg_content SET ' . $db->sql_build_array('UPDATE', $sql_ary) .'WHERE id = ' . (int) $content_id;
					$db->sql_query($sql);
					
			    if (isset($_POST['taggedCharacterList'])) {
			      //die($_POST['taggedCharacterList']);
			      $taggedCharacters = explode(',', $_POST['taggedCharacterList']);
			    }
			    
          if (count($taggedCharacters) > 0) {
          
            foreach ($taggedCharacters as $characterID) {
              if (!empty($characterID)) {
                $characterSQL[] = '('.(int) $characterID.','.(int) $content_id.')';
              }
            }
            
            if (!empty($characterSQL)) {
				      $sql = 'INSERT IGNORE INTO rpg_content_tags (character_id, content_id) VALUES '.implode(',',$characterSQL) ;
				      $db->sql_query($sql);
				    }
			
          }
				
					meta_refresh(3, 'http://www.roleplaygateway.com/roleplay/'.$row['roleplay_url'].'/places/'.$row['place_url'].'/#roleplay'.$content_id);
					trigger_error("You have edited your post!");
				} else {
					
					decode_message($row['text'], $row['bbcode_uid']);			
							
					$template->assign_vars(array(
						'CONTENT_ID'	=> $content_id,
						'ROLEPLAY_LINK'	=> '<a href="http://www.roleplaygateway.com/roleplay/'.$row['roleplay_url'].'/">'.$row['roleplay_name'].'</a>',
						'URL' => $row['roleplay_url'],
						'PLACE_LINK'	=> '<a href="http://www.roleplaygateway.com/roleplay/'.$row['roleplay_url'].'/places/'.$row['place_url'].'">'.$row['place_name'].'</a>',
						'TEXT'			=> $row['text'],
					));				
					
					// Set desired template
					$this->tpl_name = 'ucp_roleplays_content_edit';
					$this->page_title = 'Edit Post';
				
				}
			break;
      case 'tag_characters':
        die(json_encode($_POST));


        if (isset($_POST['taggedCharacterList'])) {
          //die($_POST['taggedCharacterList']);
          $taggedCharacters = explode(',', $_POST['taggedCharacterList']);
        }
        
        if (count($taggedCharacters) > 0) {
        
          foreach ($taggedCharacters as $characterID) {
            if (!empty($characterID)) {
              $characterSQL[] = '('.(int) $characterID.','.(int) $content_id.')';
            }
          }
          
          if (!empty($characterSQL)) {
            $sql = 'INSERT IGNORE INTO rpg_content_tags (character_id, content_id) VALUES '.implode(',',$characterSQL) ;
            $db->sql_query($sql);
          }
    
        }
      break;
      case 'add_arc':

        $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $_REQUEST['roleplay_id'];
        $result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

				if (in_array($user->data['user_id'], getBannedPlayers($roleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

        if (empty($roleplay['id'])) {
          trigger_error('No such roleplay.');
        } else {

	        $sql = 'SELECT id, text FROM rpg_content WHERE id = '.(int) $_REQUEST['with_post'] . ' AND roleplay_id = '.(int) $roleplay['id'];
	        $result = $db->sql_query($sql);
	        $post = $db->sql_fetchrow($result);
	        $db->sql_freeresult($result);

	        if (empty($post['id'])) {
	        	trigger_error('No such post in that roleplay.');
	        }

          $template->assign_vars(array(
            'ROLEPLAY_ID'   => $roleplay['id'],
            'POST_ID'				=> $post['id'],
            'POST_CONTENT'	=> $post['text'],
          ));

          if (count($_POST) > 0) {

            if (strlen($_POST['arc_name']) > 0 && strlen($_POST['arc_description']) && strlen($_POST['with_post'])) {
              
            	$name 				= utf8_normalize_nfc(request_var('arc_name', '', true));
            	$description 	= utf8_normalize_nfc(request_var('arc_description', '', true));

              $sql = 'INSERT INTO rpg_arcs (roleplay_id, slug, name, description, creator) VALUES ('.(int) $roleplay['id'].', "'.$db->sql_escape(urlify($name)).'" , "'.$db->sql_escape($name).'", "'.$db->sql_escape($description).'", '.(int) $user->data['user_id'].')';
              $db->sql_query($sql);

							$arcID = $db->sql_nextid();
              $sql = 'INSERT INTO rpg_arc_content (arc_id, content_id) VALUES ('.(int) $arcID.', '.(int) $post['id'].')';
              $db->sql_query($sql);

              meta_refresh('3', 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/arcs/'.urlify($name) );
              trigger_error('Your Bundle has been created!  You can now add posts via any page where it is found.');

            } else {
              trigger_error("You must include both a name, description, and a post for your bundle.");
            }

          } else {
            $this->tpl_name = 'ucp_roleplays_arc_add';
            $this->page_title = 'Add Arc';
          }
        }

      break;
      case 'edit_arc':
        $this->tpl_name = 'ucp_roleplays_arc_edit';
        $this->page_title = 'Edit Arc';
      break;
      case 'move_post':

				$sql = 'SELECT id, owner FROM rpg_roleplays 
					WHERE id = '.(int) $_POST['roleplayID'];
				$result = $db->sql_query($sql);
				$roleplay = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
        $game_masters = array();
        $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
        $result = $db->sql_query($sql);
        while ($gm_row = $db->sql_fetchrow($result)) {
	        $game_masters[] = $gm_row['user_id'];
        }
        $db->sql_freeresult($result);

				if ((!$auth->acl_get('a_')) && ($roleplay['owner'] != $user->data['user_id']) && (!in_array($user->data['user_id'], $game_masters))) {
					trigger_error("You can't move this post as you do not have the permissions to do so.");
				} else {
	      	if (isset($_POST['roleplayID']) and isset($_POST['contentID']) and isset($_POST['placeID'])) {

						$sql = 'SELECT id, name FROM rpg_places 
							WHERE id = '.(int) $_POST['placeID'] . ' AND roleplay_id = '.(int) $roleplay['id'];
						$result = $db->sql_query($sql);
						$place = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						$sql = 'SELECT id FROM rpg_content
							WHERE id = '.(int) $_POST['contentID'] . ' AND roleplay_id = '.(int) $roleplay['id'];
						$result = $db->sql_query($sql);
						$content = $db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if (empty($place['id']) || empty($content['id'])) {
							trigger_error("Sorry, but you can't move that post to that location.");
						} else {

							$sql = 'UPDATE rpg_content SET place_id = '.(int) $place['id'] . ' WHERE id = '.(int) $content['id'];
							die(json_encode($sql));

						}

	      	} else {
	      		die('nope');
	      	}

				}

      break;
			case 'write':
			
				if (isset($_POST['roleplay_id']) and isset($_POST['reply_content']) and isset($_POST['place'])) {
			
					$roleplay		= request_var('roleplay_id', 0);
					$text			= utf8_normalize_nfc(request_var('reply_content', '', true));
					$oldtext = $originaltext = $text;
					$location 		= $placeID = request_var('place', 0);
					
					if (isset($_POST['taggedCharacterList'])) {
					  //die($_POST['taggedCharacterList']);
					  $taggedCharacters = explode(',', $_POST['taggedCharacterList']);
					}
          
          $sql = 'SELECT r.title,r.url,p.url as place_url,p.name as place_name FROM rpg_places p
                INNER JOIN rpg_roleplays r
                  ON p.roleplay_id = r.id
          WHERE p.id = '.(int) $location ;
          $roleplay_result = $db->sql_query($sql);
          $roleplay_data = $db->sql_fetchrow($roleplay_result);
          $db->sql_freeresult($roleplay_result);

					if (in_array($user->data['user_id'], getBannedPlayers($roleplay_data['id']))) {
						trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
					}
          
          $sql = 'SELECT id, name, url, status FROM rpg_places WHERE roleplay_id = '.(int) $roleplay . ' AND id = '.(int) $location;
          $placeResult = $db->sql_query($sql);
          $place = $db->sql_fetchrow($placeResult);
          $db->sql_freeresult($placeResult);

          if ($place['status'] == 'Locked') {
            trigger_error('You cannot post here because the Location is locked.');
          }

					$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

					$sql_ary = array(
						'roleplay_id'      	=> $roleplay,
						'text'          	=> $text,
						'bbcode_uid'      	=> $uid,
						'bbcode_bitfield' 	=> $bitfield,
						'place_id' 			=> $location,
						'author_id' 		=> $user->data['user_id'],
					);

					$sql = 'INSERT INTO rpg_content ' . $db->sql_build_array('INSERT', $sql_ary);
					$db->sql_query($sql);
					
					$contentID = $db->sql_nextid();

          if (!in_array($roleplay, array(14556, 14891, 14712, 14717))) {
          
            $wordCount = strlen(preg_replace('/[^ ]/', '', $oldtext)) + 1;
            if ($wordCount >= 500) {
              $oldtext = (strlen($oldtext) > 240) ? substr($oldtext, 0, 240) . '...' : $oldtext;

					    $chat_message = '[url=http://www.roleplaygateway.com/member-u'.$user->data['user_id'].'.html]'.$user->data['username'] . '[/url] just added [url=http://www.roleplaygateway.com/roleplay/'.$roleplay_data['url'].'/post/'.(int) $contentID.'/#roleplay'.(int) $contentID.']'.$wordCount.' words[/url] to the [url=http://www.roleplaygateway.com/roleplay/'.$roleplay_data['url'].'/]'.$roleplay_data['title'].'[/url] universe.';

					    $chat_message_place = '[url=http://www.roleplaygateway.com/member-u'.$user->data['user_id'].'.html]'.$user->data['username'] . '[/url] has just added [url=http://www.roleplaygateway.com/roleplay/'.$roleplay_data['url'].'/post/'.(int) $contentID.'/#roleplay'.(int) $contentID.']'.$wordCount.' words[/url] to this location:'."\n[quote]".$oldtext."[/quote]" ;

					    $sql = "INSERT INTO ajax_chat_messages
								    (`userId`,`userRole`,`ip`,`dateTime`,`userName`,`channel`,`text`,`roleplayID`) 
							    VALUES ('2147483647','4','127.0.0.1',NOW(),'Game Master (GM)','0','".$db->sql_escape($chat_message)."',".(int) $roleplay.")";
					    $db->sql_query($sql);
					  }
					}
          
      
          if ($location > 0) {
            $sql = "INSERT INTO ajax_chat_messages
                  (`userId`,`characterID`, `userRole`,`ip`,`dateTime`,`userName`,`channel`,`text`,`roleplayID`) 
                VALUES ('".$user->data['user_id']."','0','1','".$user->data['user_ip']."',NOW(),'".$user->data['username']."','".(int) $location."','".$db->sql_escape(html_entity_decode($originaltext))."',".(int) $roleplay.")";
            $db->sql_query($sql);
          }

          $chatID = $db->sql_nextid();
          
          $sql = 'UPDATE rpg_content SET old_chat_id = '.(int) $chatID.' WHERE id = '.(int) $contentID;
          $db->sql_query($sql);
          
          
          if (count($taggedCharacters) > 0) {
          
            foreach ($taggedCharacters as $characterID) {
              if (!empty($characterID)) {
                $characterSQL[] = '('.(int) $characterID.','.(int) $contentID.')';
              }
            }
            
            if (!empty($characterSQL)) {
				      $sql = 'INSERT IGNORE INTO rpg_content_tags (character_id, content_id) VALUES '.implode(',',$characterSQL);
				      $db->sql_query($sql);
				    }
				
          }

          $messageData = array(
              'id'       => $contentID
            , 'link'     => 'http://www.roleplaygateway.com/roleplay/' . $roleplay_data['url'] . '/post/'.$contentID.'/#roleplay'.$contentID
            , 'stats'    => array(
                  'words' => $wordCount
              )
            , 'roleplay' => array(
                  'name'  => $roleplay_data['title']
                , 'link'  => 'http://www.roleplaygateway.com/roleplay/' . $roleplay_data['url'] . '/'
              )
            , 'author'   => array(
                  'name'  => $user->data['username']
              )
            , 'place'    => array(
                  'name'  => $roleplay_data['place_name']
                , 'link'  => 'http://www.roleplaygateway.com/roleplay/' . $roleplay_data['url'] . '/places/' .$roleplay_data['place_url'] . '/'
              )
          );

					$redis = new Redis();
					$redis->pconnect('127.0.0.1', 6379);
					$redis->publish('roleplay.'.$roleplay , json_encode(array(
            'type' => 'content',
            'data' => $messageData
          ), JSON_FORCE_OBJECT));
					//$redis->publish('roleplay.'.$roleplay.'.content', json_encode($messageData, JSON_FORCE_OBJECT));
					$redis->close();

					meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$roleplay_data['url'].'/places/'.$place['url'].'/#roleplay'.$contentID);
					trigger_error("You have replied to this roleplay.");
				} else {
					trigger_error('You cannot reply to a roleplay without specifying what place you want to post to.');
				}
				
				$template->assign_vars(array(
					'ROLEPLAY_ID'		=> $row['id'],
				));				
				
				// Set desired template
				$this->tpl_name = 'ucp_roleplays_write';
				$this->page_title = 'New Roleplay';
				
			break;
			case 'tag_character':
				if (isset($_POST['content_id']) and isset($_POST['character_id'])) {
					trigger_error('Would have set.');
				} else {
					trigger_error('Would not have set.');
				}
			
			break;
      case 'add_character_to_group':
        if (isset($_POST['roleplay_id']) and isset($_POST['group_id']) and isset($_POST['character_name'])) {

          $sql = 'SELECT id,title,url FROM rpg_roleplays WHERE id = '. (int) request_var('roleplay_id', 0) ;
          $roleplay_result = $db->sql_query($sql);
          $roleplay = $db->sql_fetchrow($roleplay_result);
          $db->sql_freeresult($roleplay_result);

          $sql = 'SELECT id,name,url FROM rpg_characters WHERE name = "'.$db->sql_escape(request_var('character_name', '')).'" AND roleplay_id = '. (int) request_var('roleplay_id', 0) ;
          $character_result = $db->sql_query($sql);
          $character = $db->sql_fetchrow($character_result);
          $db->sql_freeresult($character_result);

          $sql = 'INSERT INTO rpg_group_members (group_id, character_id, status) VALUES
                      ('.(int)$group['id'] . ', '.(int) $character['id'].', "Member")';
          $db->sql_query($sql);

          die(json_encode(array('status' => 'success', 'message' => 'Added to group successfully!')));

        } else {
          die(json_encode(array('status' => 'error', 'message' => 'missing fields.')));
        }
      break;
      case 'add_characters_to_group':
        if (isset($_POST['roleplay_id']) and isset($_POST['group_id']) and isset($_POST['character_ids'])) {

          $sql = 'SELECT id,title,url FROM rpg_roleplays WHERE id = '. (int) request_var('roleplay_id', 0) ;
          $roleplay_result = $db->sql_query($sql);
          $roleplay = $db->sql_fetchrow($roleplay_result);
          $db->sql_freeresult($roleplay_result);

          $sql = 'SELECT id FROM rpg_groups WHERE id = '.(int) $_POST['group_id'].' AND roleplay_id = ' . (int) $roleplay['id'] ;
          $group_result = $db->sql_query($sql);
          $group = $db->sql_fetchrow($group_result);
          $db->sql_freeresult($group_result);

          foreach ($_POST['character_ids'] as $characterID) {
            $characterIDs[] = (int) $characterID;

            $sql = 'SELECT id,name,url FROM rpg_characters WHERE id = '.(int) $characterID.' AND roleplay_id = '. (int) $roleplay['id'] ;
            $character_result = $db->sql_query($sql);
            $character = $db->sql_fetchrow($character_result);
            $db->sql_freeresult($character_result);

            if (!empty($character['id'])) {
              $sql = 'INSERT IGNORE INTO rpg_group_members (group_id, character_id, status) VALUES
                          ('.(int)$group['id'] . ', '.(int) $character['id'].', "Member")';
              $db->sql_query($sql);
            }

          }

          header('Content-Type: application/json');
          die(json_encode(array('status' => 'success', 'message' => 'Added to group successfully!')));

        } else {
          header('Content-Type: application/json');
          die(json_encode(array('status' => 'error', 'message' => 'missing fields.')));
        }
      break;
			case 'add_group':

        $sql = 'SELECT id,title,url,owner FROM rpg_roleplays WHERE id = '. (int) $_REQUEST['roleplay_id'] ;
        $roleplay_result = $db->sql_query($sql);
        $roleplay = $db->sql_fetchrow($roleplay_result);
        $db->sql_freeresult($roleplay_result);

				if (in_array($user->data['user_id'], getBannedPlayers($roleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

				if (isset($_POST['roleplay_id']) and isset($_POST['group_name']) and isset($_POST['group_synopsis'])) {
			
					$roleplay		= request_var('roleplay_id', 0);
					$group_name		= utf8_normalize_nfc(request_var('group_name', '', true));
					$group_synopsis		= utf8_normalize_nfc(request_var('group_synopsis', '', true));
					$group_description	= utf8_normalize_nfc(request_var('group_description', '', true));

					$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($group_description, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

					// check if a file was submitted
					if(isset($_FILES['userfile'])) {
						try {
							if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
								// check the file is less than the maximum file size
								if($_FILES['userfile']['size'] < 1500000) {

									// prepare the image for insertion
									$imgData = file_get_contents($_FILES['userfile']['tmp_name']);
			 
									// get the image info..
									$imgSize = getimagesize($_FILES['userfile']['tmp_name']);
						 
									if (($imgSize[0] > 100) || ($imgSize[1] > 100)) {
										trigger_error('This image is larger than 100x100.  Resize it. <a href="#" onClick="history.go(-1)">Back</a>');
									}
						 
								}
							}
						}
						catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}

					if (!empty($imgData)) {
						$sql_ary = array(
							'roleplay_id'      			=> $roleplay,
							'name'          				=> $group_name,
							'slug'									=> urlify($group_name),
							'synopsis'          		=> $group_synopsis,
							'description'          	=> $group_description,
							'description_uid'      	=> $uid,
							'description_bitfield' 	=> $bitfield,
							'founder' 							=> $user->data['user_id'],
							'owner' 								=> $user->data['user_id'],
						  'image'                 => $imgData,
						  'image_type'           	=> $imgSize['mime'],
						);
					} else {
						$sql_ary = array(
							'roleplay_id'      			=> $roleplay,
							'name'          				=> $group_name,
							'slug'									=> urlify($group_name),
							'synopsis'          		=> $group_synopsis,
							'description'          	=> $group_description,
							'description_uid'      	=> $uid,
							'description_bitfield' 	=> $bitfield,
							'founder' 							=> $user->data['user_id'],
							'owner' 								=> $user->data['user_id'],
						);
					}

					$sql = 'INSERT INTO rpg_groups ' . $db->sql_build_array('INSERT', $sql_ary);
					$db->sql_query($sql);

		      $sql = 'SELECT id,title,url FROM rpg_roleplays WHERE id = '. (int) request_var('roleplay_id', 0) ;
		      $roleplay_result = $db->sql_query($sql);
		      $roleplay = $db->sql_fetchrow($roleplay_result);
		      $db->sql_freeresult($roleplay_result);
				
					meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$roleplay['url'].'/groups/'.urlify($group_name));
					trigger_error("You have created a group.");
				} elseif (count($_POST) > 0) {
					trigger_error('You cannot create a group without specifying all required fields.');
				} else {

		      $sql = 'SELECT id,title,url FROM rpg_roleplays WHERE id = '. (int) request_var('roleplay_id', 0) ;
		      $roleplay_result = $db->sql_query($sql);
		      $roleplay = $db->sql_fetchrow($roleplay_result);
		      $db->sql_freeresult($roleplay_result);


					$template->assign_vars(array(
						'ROLEPLAY_ID'		=> $roleplay['id'],
						'ROLEPLAY_NAME'		=> $roleplay['title'],
					));

					// Set desired template
					$this->tpl_name = 'ucp_roleplays_group_new';
					$this->page_title = 'Create Group';

				}				
			break;
			case 'edit_group':

				$roleplay		= request_var('roleplay_id', 0);
				$group_id		= request_var('group_id', 0);			

        				
				$sql = 'SELECT id,url FROM rpg_roleplays WHERE id = '.(int) $roleplay;
        $roleplayResult = $db->sql_query($sql);
        while ($roleplayObject = $db->sql_fetchrow($roleplayResult)) {
          $thisRoleplay = $roleplayObject;
        }

				if (in_array($user->data['user_id'], getBannedPlayers($thisRoleplay['id']))) {
					trigger_error('You have been banned from participation in this roleplay.  Please contact one of the Game Masters for more information.');
				}

        $game_masters = array();
        $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $thisRoleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $thisRoleplay['id'].' AND isCoGM = 1)';
        $result = $db->sql_query($sql);
        while ($gm_row = $db->sql_fetchrow($result)) {
	        $game_masters[] = $gm_row['user_id'];
        }
        $db->sql_freeresult($result);

				$sql = 'SELECT * FROM rpg_groups WHERE id = '.(int) $group_id;
        $groupResult = $db->sql_query($sql);
        while ($groupObject = $db->sql_fetchrow($groupResult)) {
          if (($user->data['user_id'] != 4) && ($user->data['user_id'] != $groupObject['owner'])) {
            trigger_error('You do not have permissions in this roleplay to edit this group.');
          }
          $group = $groupObject;
        }

				if ((!$auth->acl_get('a_')) && ($group['owner'] != $user->data['user_id']) && (!in_array($user->data['user_id'], $game_masters))
						&& (!$auth->acl_get('m_'))) {
					trigger_error('You do not own this content. Owner:'.$group['owner']);
				}
			
				if (isset($_REQUEST['roleplay_id']) and isset($_REQUEST['group_id']) and $submit) {

					// $group_name		= utf8_normalize_nfc(request_var('group_name', '', true));
					// $group_name		= utf8_normalize_nfc(request_var('group_name', '', true));
					$group_synopsis		= utf8_normalize_nfc(request_var('group_synopsis', '', true));
					$group_description	= utf8_normalize_nfc(request_var('group_description', '', true));

					// check if a file was submitted
					if(isset($_FILES['userfile'])) {
						try {
							if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
								// check the file is less than the maximum file size
								if($_FILES['userfile']['size'] < 1500000) {

									// prepare the image for insertion
									$imgData = file_get_contents($_FILES['userfile']['tmp_name']);
			 
									// get the image info..
									$imgSize = getimagesize($_FILES['userfile']['tmp_name']);
						 
									if (($imgSize[0] > 100) || ($imgSize[1] > 100)) {
										trigger_error('This image is larger than 100x100.  Resize it. <a href="#" onClick="history.go(-1)">Back</a>');
										die('debug: '. $imgData);

									}
						 
								}
							}
						}	catch(Exception $e) {
							trigger_error('Sorry, could not upload file: '.$e->getMessage());
						}
					}					


					$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
					$allow_bbcode = $allow_urls = $allow_smilies = true;
					generate_text_for_storage($group_description, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

          if (!empty($imgData)) {
					  $sql_ary = array(
						  'synopsis'          	=> $group_synopsis,
						  'description'          	=> $group_description,
						  'description_uid'      	=> $uid,
						  'description_bitfield' 	=> $bitfield,
						  'image'                 => $imgData,
						  'image_type'           	=> $imgSize['mime'],
					  );           
          } else {
        
					  $sql_ary = array(
						  'synopsis'          	=> $group_synopsis,
						  'description'          	=> $group_description,
						  'description_uid'      	=> $uid,
						  'description_bitfield' 	=> $bitfield,
					  );          
          }
					
					$sql = 'UPDATE rpg_groups SET ' . $db->sql_build_array('UPDATE', $sql_ary) .'WHERE id = ' . (int) $group_id;
					$db->sql_query($sql);
				
					meta_refresh(3, "http://www.roleplaygateway.com/roleplay/".$thisRoleplay['url'].'/groups/'.$group['slug']);
					trigger_error("You have updated this group.");
				} elseif ($submit) {
					trigger_error('You cannot update a group without specifying all required fields.');
				} else {
				
				  decode_message($group['description'], $group['description_uid']);
				
          $template->assign_vars(array(
            'GROUP_ID' => $group['id'],
            'GROUP_NAME' => $group['name'],
            'ROLEPLAY_ID' => $thisRoleplay['id'],
            'SYNOPSIS' => $group['synopsis'],
            'DESCRIPTION' => $group['description'],
				  ));
					
					// Set desired template
					$this->tpl_name = 'ucp_roleplays_group_edit';
					$this->page_title = 'Edit Group';									  
				}
			break;
			case 'join_group':
				if (isset($_POST['roleplay_id']) and isset($_POST['group_id'])) {

				} else {
					trigger_error('No group specified.');
				}
			break;
      case 'add_gm':

      break;
      case 'remove_gm':

      break;
      case 'remove_player':
				$roleplay_id = request_var('roleplay_id', 0);
				$roleplay_id = request_var('player_id', 0);
				$sql = "SELECT id, title, description, owner FROM rpg_roleplays WHERE id =".(int) $db->sql_escape($roleplay_id). "";

        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result)) {

        	$template->assign_vars(array(
                	'ID'                           	=> $row['id'],
                        'ROLEPLAY_NAME'               	=> $row['title'],
                        'ROLEPLAY_SYNOPSIS'            => $row['description'],
  'S_IS_OWNER'			=> ($row['owner'] == $user->data['user_id']) ? true : false,
                ));

                // Set desired template
                $this->tpl_name = 'ucp_roleplays_abandon';
                $this->page_title = 'Abandon Roleplay';

        }
				
				if ($submit) {
					// check mode
					$isAdoptable = request_var('Adoption', 0);
					$sql = "UPDATE rpg_characters SET owner = 0, isAdoptable= ".(int) $isAdoptable." WHERE roleplay_id = ".(int) $roleplay_id." AND owner = ".$user->data['user_id'];
					$db->sql_query($sql);
					meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays");
					trigger_error("You've released all characters in this roleplay.  It should no longer appear in your list.");
				}
      break;
			case "view":
			
				if (($roleplay_id = request_var('id', '')) < 1) {
					trigger_error('You must access this page from the <a href="http://www.roleplaygateway.com/ucp.php?i=roleplays">main roleplay list</a>!');
				}

				$sql = "SELECT id,title,description,owner,player_slots,owner,username FROM rpg_roleplays
							INNER JOIN gateway_users
								ON rpg_roleplays.owner = gateway_users.user_id
							WHERE rpg_roleplays.id = ".$db->sql_escape($roleplay_id);
				
				$result = $db->sql_query($sql);

				while($row = $db->sql_fetchrow($result)) {

					$sql = "SELECT count(*) as players FROM rpg_roleplay_players WHERE roleplay_id = '".$row['id']."'";
								
					$player_result = $db->sql_query($sql);
					
					while($player_row = $db->sql_fetchrow($player_result)) {
						$open_slots = $row['player_slots'] - $player_row['players'];
					}

					$template->assign_vars(array(
						'ROLEPLAY_ID'		=> $row['id'],
						'TITLE'				=> $row['title'],
						'DESCRIPTION'		=> $row['description'],
						'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
						'OPEN_SLOTS'		=> $open_slots,
						'PLAYER_SLOTS'		=> $row['player_slots'],
					));
					
					
					$sql = "SELECT thread_id,topic_title FROM rpg_roleplay_threads
								INNER JOIN gateway_topics
									ON rpg_roleplay_threads.thread_id = gateway_topics.topic_id
								WHERE rpg_roleplay_threads.roleplay_id = '".$row['id']."'
									AND rpg_roleplay_threads.type = 'In Character'
								ORDER BY gateway_topics.topic_last_post_time DESC";
								
					$thread_result = $db->sql_query_limit($sql, 10);
					
					while($thread_row = $db->sql_fetchrow($thread_result)) {

						$template->assign_block_vars('ic_threads', array(
							'THREAD_ID'	=> $thread_row['thread_id'],
							'THREAD_TITLE'	=> $thread_row['topic_title'],
						));
						
					}

					$sql = "SELECT thread_id,topic_title FROM rpg_roleplay_threads
								INNER JOIN gateway_topics
									ON rpg_roleplay_threads.thread_id = gateway_topics.topic_id
								WHERE rpg_roleplay_threads.roleplay_id = '".$row['id']."'
									AND rpg_roleplay_threads.type = 'Out Of Character'
								ORDER BY gateway_topics.topic_last_post_time DESC";
								
					$thread_result = $db->sql_query_limit($sql, 10);
					
					while($thread_row = $db->sql_fetchrow($thread_result)) {

						$template->assign_block_vars('ooc_threads', array(
							'THREAD_ID'	=> $thread_row['thread_id'],
							'THREAD_TITLE'	=> $thread_row['topic_title'],
						));
						
					}					
					
					
					$sql = "SELECT  gateway_users.user_id,
									gateway_users.username,
									rpg_characters.id,
									rpg_characters.name,
									rpg_characters.synopsis
								FROM rpg_roleplay_players, gateway_users, rpg_characters
								WHERE rpg_roleplay_players.roleplay_id = '".$row['id']."'
									AND	gateway_users.user_id = rpg_characters.owner
									AND	rpg_roleplay_players.character_id = rpg_characters.id
								ORDER BY rpg_roleplay_players.character_id ASC";
								
					$player_result = $db->sql_query($sql);
					
					while($player_row = $db->sql_fetchrow($player_result)) {

						$template->assign_block_vars('characters', array(
							'PLAYER_LINK'		=> get_username_string('full', $player_row['user_id'], $player_row['username']),
							'CHARACTER_NAME'	=> $player_row['name'],
							'CHARACTER_ID'		=> $player_row['id'],
							'SYNOPSIS'			=> $player_row['synopsis'],
						));
						
					}
					
					$sql = "SELECT name,id FROM rpg_characters WHERE owner = ".$user->data['user_id']." AND
						id NOT IN (SELECT character_id as id FROM rpg_roleplay_players WHERE roleplay_id = ".$row['id'].") ORDER BY id ASC";
								
					$character_result = $db->sql_query($sql);
					
					while($character_row = $db->sql_fetchrow($character_result)) {

						$template->assign_block_vars('user_characters', array(

							'CHARACTER_NAME'	=> $character_row['name'],
							'CHARACTER_ID'		=> $character_row['id'],

						));
						
					}
					
					// BE WARNED; this query is massive and may cause load issues in the future!
					$sql = "SELECT post_subject,post_id,post_text,post_time,bbcode_uid,bbcode_bitfield,enable_smilies,enable_magic_url FROM gateway_posts
								JOIN rpg_roleplay_threads ON rpg_roleplay_threads.thread_id = gateway_posts.topic_id
								WHERE rpg_roleplay_threads.roleplay_id = ".$row['id']."
								ORDER BY gateway_posts.post_time DESC";
								

					$activity_result = $db->sql_query_limit($sql,10);
					
					while($activity_row = $db->sql_fetchrow($activity_result)) {
					
						include_once("/var/www/vhosts/roleplaygateway.com/httpdocs/includes/functions_content.php");
					
						$row['bbcode_options'] = (($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
							(($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
							(($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
						$post_text = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);
									

						$template->assign_block_vars('activities', array(			

							'TIME'		=> $user->format_date($activity_row['post_time']),
							'CLASS'		=> "post",
							'URL'		=> "http://www.roleplaygateway.com/post".$activity_row['post_id'].".html#p".$activity_row['post_id'],
							'TITLE'		=> $activity_row['post_subject'],
							'CONTENT'	=> $post_text,

						));
					}
			
				}
				

				// Set desired template
				$this->tpl_name = 'ucp_roleplays_view';
				$this->page_title = 'View Roleplay';
				
									
			
			break;
			case 'flag':

				if (($roleplay_id = request_var('id', '')) < 1) {
					trigger_error('You must specify a roleplay.');
				}

				$sql ="SELECT owner, status FROM rpg_roleplays WHERE id='".$roleplay_id."'";
				$result=$db->sql_query($sql);
				$row=$db->sql_fetchrow($result);
				if ((!$auth->acl_get('m_')) and ($row['owner']!=$user->data['user_id'])) {
					trigger_error('You must be a moderator or owner of the RP.');
				}

				if(($row['status'] == 'Closed' or $row['status'] == 'Completed') and (!$auth->acl_get('m_')))
				{
					trigger_error('This RP is flagged as Closed or Completed so you cannot change the flag. Contact Admin.');

				}

				if (isset($_REQUEST['action'])) {

					switch($_REQUEST['action']) {
	                                        case 'open':
                                                        $sql = 'UPDATE rpg_roleplays SET status = "Open" WHERE id = '.(int) $roleplay_id;
                                                        if ($db->sql_query($sql)) {
                                                                meta_refresh(3, "http://www.roleplaygateway.com/mcp.php?i=queue&mode=unclosed_roleplays");
                                                                trigger_error('Successfully re-opened this roleplay.');
                                                        } else {
                                                                trigger_error('Couldn\'t re-open this roleplay. Call Rem, give him the URL.');
                                                        }

                                                break;
	                                        case 'closed':
                                                        $sql = 'UPDATE rpg_roleplays SET status = "Closed" WHERE id = '.(int) $roleplay_id;
                                                        if ($db->sql_query($sql)) {
                                                                meta_refresh(3, "http://www.roleplaygateway.com/mcp.php?i=queue&mode=unclosed_roleplays");
                                                                trigger_error('Successfully closed this roleplay.');
                                                        } else {
                                                                trigger_error('Couldn\'t close this roleplay. Call Rem, give him the URL.');
                                                        }

                                                break;
						case 'completed':
							$sql = 'UPDATE rpg_roleplays SET status = "Completed" WHERE id = '.(int) $roleplay_id;
							if ($db->sql_query($sql)) {
								meta_refresh(3, "http://www.roleplaygateway.com/mcp.php?i=queue&mode=unreviewed_roleplays");
								trigger_error('Successfully completed this roleplay.');
							} else {
								trigger_error('Couldn\'t complete this roleplay. Call Rem, give him the URL.');
							}

						break;
						case 'review':
							$sql = 'UPDATE rpg_roleplays SET status = "Pending Review" WHERE id = '.(int) $roleplay_id;
							if ($db->sql_query($sql)) {
								meta_refresh(3, "http://www.roleplaygateway.com/roleplay/");
								trigger_error('Successfully submited this roleplay for completion.');
							} else {
								trigger_error('Couldn\'t submit this roleplay for completion. Call Rem, give him the URL.');
							}

						break;
						case 'close':
							$sql = 'UPDATE rpg_roleplays SET status = "Pending Closure" WHERE id = '.(int) $roleplay_id;
							if ($db->sql_query($sql)) {
								meta_refresh(3, "http://www.roleplaygateway.com/roleplay/");
								trigger_error('Successfully submited this roleplay for closure.');
							} else {
								trigger_error('Couldn\'t submit this roleplay for closure. Call Rem, give him the URL.');
							}

						break;
					}


				} else {

				}

				$template->assign_vars(array(
					'ROLEPLAY_ID'		=> $roleplay_id,
					'IS_MOD'		=> $auth->acl_get('m_')
				));			


				// Set desired template
				$this->tpl_name = 'ucp_roleplays_flag';
				$this->page_title = 'Flag Roleplay';
			break;
			case 'abandon':
				$roleplay_id = request_var('roleplay_id', 0);
				$sql = "SELECT id, title, description, owner FROM rpg_roleplays WHERE id =".(int) $db->sql_escape($roleplay_id). "";

        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result)) {

        	$template->assign_vars(array(
                	'ID'                           	=> $row['id'],
                        'ROLEPLAY_NAME'               	=> $row['title'],
                        'ROLEPLAY_SYNOPSIS'            => $row['description'],
  'S_IS_OWNER'			=> ($row['owner'] == $user->data['user_id']) ? true : false,
                ));

                // Set desired template
                $this->tpl_name = 'ucp_roleplays_abandon';
                $this->page_title = 'Abandon Roleplay';

        }
				
				if ($submit) {
					// check mode
					$isAdoptable = request_var('Adoption', 0);
					$sql = "UPDATE rpg_characters SET owner = 0, isAdoptable= ".(int) $isAdoptable." WHERE roleplay_id = ".(int) $roleplay_id." AND owner = ".$user->data['user_id'];
					$db->sql_query($sql);
					meta_refresh(3, "http://www.roleplaygateway.com/ucp.php?i=roleplays");
					trigger_error("You've released all characters in this roleplay.  It should no longer appear in your list.");
				}
			break;
			default:
				
				// includes roleplays in which the user has a character
				$sql = "SELECT id,title,description,owner,player_slots,owner,username,require_approval,url FROM rpg_roleplays
							INNER JOIN gateway_users
								ON rpg_roleplays.owner = gateway_users.user_id
							WHERE rpg_roleplays.status != 'Closed' AND
								( owner = ".$user->data['user_id']. "
								OR id IN (SELECT DISTINCT roleplay_id FROM rpg_characters WHERE owner = ".$user->data['user_id']."))";
				
				$result = $db->sql_query($sql);
								
				while($row = $db->sql_fetchrow($result)) {
				
					$my_roleplays[] = (int) $row['id'];

					$sql = "SELECT count(*) as players FROM rpg_characters WHERE roleplay_id = '".$row['id']."' ORDER BY roleplay_id ASC";
								
					$player_result = $db->sql_query($sql);
					
					while($player_row = $db->sql_fetchrow($player_result)) {
						$open_slots = $row['player_slots'] - $player_row['players'];
					}

					$template->assign_block_vars('roleplays', array(
						'S_CAN_MANAGE'		=> ($row['owner'] == $user->data['user_id']) ? true : false,
						'ID'				=> $row['id'],
						'TITLE'				=> $row['title'],
						'DESCRIPTION'		=> $row['description'],
						'URL'				=> $row['url'],
						'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
						'OPEN_SLOTS'		=> $open_slots,
						'TOTAL_SLOTS'		=> $row['player_slots'],
					));
					
					$sql = "SELECT name,id,url FROM rpg_places
								WHERE roleplay_id = '".$row['id']."'";
								
					$places_result = $db->sql_query_limit($sql, 10);
					
					while($places_row = $db->sql_fetchrow($places_result)) {

						$template->assign_block_vars('roleplays.places', array(
							'ID'		=> $places_row['id'],
							'NAME'		=> $places_row['name'],
							'URL'		=> $places_row['url'],
						));
						
					}
				
					if ($row['owner'] == $user->data['user_id']) {
					
						$sql = "SELECT  gateway_users.user_id,
										gateway_users.username,
										rpg_characters.id,
										rpg_characters.name,
										rpg_characters.url,
										rpg_characters.approved,
										rpg_characters.synopsis
									FROM gateway_users, rpg_characters
									WHERE rpg_characters.roleplay_id = '".$row['id']."'
										AND	gateway_users.user_id = rpg_characters.owner
									ORDER BY rpg_characters.id ASC";
								
						$player_result = $db->sql_query_limit($sql,128);
					
						while($player_row = $db->sql_fetchrow($player_result)) {

							$template->assign_block_vars('roleplays.characters', array(
								'PLAYER_LINK'		=> get_username_string('full', $player_row['user_id'], $player_row['username']),
								'CHARACTER_NAME'	=> $player_row['name'],
								'CHARACTER_ID'		=> $player_row['id'],
								'URL'				=> $player_row['url'],
								'SYNOPSIS'			=> $player_row['synopsis'],
								'APPROVE'			=> (($row['require_approval'] == 1) && ($player_row['approved'] == 0)) ? '<a href="http://www.roleplaygateway.com/ucp.php?i=characters&mode=approve&character_id='.$player_row['id'].'" style="float:right;">Approve</a>' : '',
							));
						
						}
					}
					
					
					$sql = "SELECT  thread_id,topic_title
								FROM rpg_roleplay_threads r
									INNER JOIN gateway_topics t ON r.thread_id = t.topic_id
								WHERE t.roleplay_id = ".(int) $row['id']."
									AND type = 'Out Of Character'";
								
					$ooc_result = $db->sql_query_limit($sql,128);
					
					while($ooc_row = $db->sql_fetchrow($ooc_result)) {

						$template->assign_block_vars('roleplays.ooc', array(
							'TOPIC_TITLE'		=> $ooc_row['topic_title'],
							'THREAD_ID'			=> $ooc_row['thread_id'],
						));
						
					}
					$db->sql_freeresult($ooc_result);
					
				}
				$db->sql_freeresult($result);
				
				
/* 				$sql = 'SELECT text FROM ajax_chat_messages  USE INDEX (dateTime) WHERE roleplayID IN ('.implode(',',$my_roleplays).') AND roleplayID IS NOT NULL AND channel = 0 AND userID = 2147483647 AND text NOT LIKE "/%" ORDER BY dateTime DESC LIMIT 25';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result)) {
					$template->assign_block_vars('roleplay_activity', array(
						'MESSAGE'		=> $row['text'],
					));				
				
				}
				$db->sql_freeresult($result); */

       /* $sql = 'SELECT c.id, c.written, c.author_id, c.roleplay_id, c.place_id, c.character_id, s.word_count FROM rpg_content c
                  INNER JOIN rpg_content_stats s ON s.content_id = c.id
                  WHERE c.roleplay_id IN (SELECT DISTINCT roleplay_id FROM rpg_characters WHERE owner = '.(int) $user->data['user_id'].') AND (c.old_chat_id IS NULL OR s.word_count > 250) ORDER BY written DESC LIMIT 25'; */
        

      $sql = 'SELECT c.id, c.written, c.author_id, c.roleplay_id, c.place_id, c.character_id FROM rpg_content c
                  WHERE c.roleplay_id IN (SELECT DISTINCT roleplay_id FROM rpg_characters WHERE owner = '.(int) $user->data['user_id'].') ORDER BY written DESC LIMIT 25';
        
        $sql = 'SELECT id, written, author_id, roleplay_id, place_id, character_id FROM rpg_content ORDER BY written DESC LIMIT 25';


        $sql = 'SELECT DISTINCT roleplay_id FROM rpg_characters WHERE owner = '.(int) $user->data['user_id'].'';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result)) {
				
			      
			    $sql = 'SELECT title, url as roleplayURL FROM rpg_roleplays WHERE id = '. (int) $row['roleplay_id'] ;
			    $thisResult = $db->sql_query($sql);
			    $row = array_merge($row,  $db->sql_fetchrow($thisResult));
			    $db->sql_freeresult($thisResult);
				
				  $sql = 'SELECT id, written, author_id, roleplay_id, place_id, character_id FROM rpg_content WHERE roleplay_id = '.$row['roleplay_id'].' ORDER BY written DESC LIMIT 5';
				  
				    $contentResult = $db->sql_query($sql);
				    while ($contentRow = $db->sql_fetchrow($contentResult)) {
				

              $sql = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $contentRow['author_id'];
              $thisResult = $db->sql_query($sql);
              $contentRow['username'] = $db->sql_fetchfield('username');
              $db->sql_freeresult($thisResult);

              $sql = 'SELECT name, url FROM rpg_characters WHERE id = '.(int) $contentRow['character_id'];
              $thisResult = $db->sql_query($sql);
              while ($characterRow = $db->sql_fetchrow($thisResult)) {
                $contentRow['characterName'] = $characterRow['name'];
                $contentRow['characterURL'] = $characterRow['url'];
              }
              $db->sql_freeresult($thisResult);

              $sql = 'SELECT name, url FROM rpg_places WHERE id = '.(int) $contentRow['place_id'];
              $thisResult = $db->sql_query($sql);
              while ($placeRow = $db->sql_fetchrow($thisResult)) {
                $contentRow['placeName'] = $placeRow['name'];
                $contentRow['placeURL'] = $placeRow['url'];
              }
              $db->sql_freeresult($thisResult);
              
              $messages[$contentRow['id']]['timestamp'] = strtotime($contentRow['written']);
              $messages[$contentRow['id']]['message'] = '[<a href="http://www.roleplaygateway.com/roleplay/'.$row['roleplayURL'].'/">'.$row['title'] .'</a>] ' .$contentRow['username'] . ' posted in <a href="http://www.roleplaygateway.com/roleplay/'.$row['roleplayURL'].'/places/'.$contentRow['placeURL'].'/">'.$contentRow['placeName'].'</a> about <a href="http://www.roleplaygateway.com/roleplay/'.$row['roleplayURL'].'/post/'.$contentRow['id'].'/#roleplay'.$contentRow['id'].'">' . timeAgo($messages[$contentRow['id']]['timestamp']). ' &raquo;</a>';
              

              /* 
                as <a href="http://www.roleplaygateway.com/roleplay/'.$row['roleplayURL'].'/characters/'.$row['characterURL'].'/">'.$row['characterName'].'</a>
              */
				    }
				
				}
				$db->sql_freeresult($result);

				
				uasort($messages, 'sortRoleplayStream');
				
        foreach ($messages as $message) {
        
			    $template->assign_block_vars('roleplay_activity', array(
				    'MESSAGE'		=> $message['message'],
			    ));
			    
			  }
				
				
				
				// Set desired template
				$this->tpl_name = 'ucp_roleplays';
				$this->page_title = 'Roleplays';
			
			break;
		}
	}
}

function sortRoleplayStream($a, $b) {
    if ($a['timestamp'] == $b['timestamp']) {
        return 0;
    }
    return ($a['timestamp'] < $b['timestamp']) ? 1 : -1;
}


function get_place_children($id,$roleplay) {
	global $db;
	
	$sql 	= 'SELECT id,name FROM rpg_places WHERE parent_id = '.$id.' AND roleplay_id = '.$roleplay;
	
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {
		$children[$row['id']]['id'] 		= $row['id'];
		$children[$row['id']]['name'] 		= $row['name'];
		$children[$row['id']]['parent'] 	= $id;
		$children[$row['id']]['children'] 	= get_place_children($row['id'],$roleplay);
	}
	
	return @$children;

}

function get_place_children_list($id) {
	global $db;
	
	$sql 	= 'SELECT id FROM rpg_places WHERE parent_id = '.$id;
	
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {
		if (@in_array($row['id'],$children)) continue;  // we must already have it.
		$children[]		= $row['id'];
	}
	
	return @$children;
}

function get_parent_options($roleplay,$current_parent = null,$id = 0) {
	global $db;
	
	
	
	$sql = 'SELECT id,name FROM rpg_places WHERE roleplay_id = '.$roleplay.' AND id > 0 AND id <> '.$id.' ORDER BY id ASC';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {
	
		if (@in_array($row['id'],get_place_children_list($id))) continue;
	
		$list .= display_place_option($row['id'],$row['name'],$current_parent);
	}

	return $list;

}

function display_place_option($place,$name,$current_parent = null) {
	if ($place != $current_parent) {
		$output = '<option value="'.$place.'"'.(($current_parent == $place) ? ' selected="selected"' : '').'>'.$name.'</option>';
	
		return $output;
	} else {
		return false;
	}
}

?>
