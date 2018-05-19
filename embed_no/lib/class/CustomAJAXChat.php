<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 * 
 * phpBB3 integration:
 * http://www.phpbb.com/
 */
error_reporting(2);
 
class CustomAJAXChat extends AJAXChat {

	function initMessageHandling() {
		// Don't handle messages if we are not in chat view:
		if($this->getView() != 'chat') {
			return;
		}
		
		$RPG = new RPG();
		$RPG->ajaxChat = $this;
		$this->RPG = $RPG;

		// Check if we have been uninvited from a private or restricted channel:
		if(!$this->validateChannel($this->getChannel())) {
			// Switch to the default channel:
			$this->switchChannel($this->getChannelNameFromChannelID($this->getConfig('defaultChannelID')));
			return;
		}
					
		if($this->getRequestVar('text') !== null) {
			$this->insertMessage($this->getRequestVar('text'));
		}
	}

	// Initialize custom configuration settings
	function initCustomConfig() {
		global $db;
		
		// Use the existing phpBB database connection:
		$this->setConfig('dbConnection', 'link', $db->db_connect_id);
	}

	// Initialize custom request variables:
	function initCustomRequestVars() {
		global $user;

		$this->_requestVars['channelMode'] = null;
		
		// Auto-login phpBB users:
		if(!$this->getRequestVar('logout') && ($user->data['user_id'] != ANONYMOUS)) {
			$this->setRequestVar('login', true);
		}
	}

	// Replace custom template tags:
	function replaceCustomTemplateTags($tag, $tagContent) {
		global $config,$user,$db;
	
		switch($tag) {

			case 'FORUM_LOGIN_URL':
				if($user->data['is_registered']) {
					return ($this->getRequestVar('view') == 'logs') ? './?view=logs' : './';
				} else {
					return $this->htmlEncode(generate_board_url().'/ucp.php?mode=login');
				}
				
			case 'REDIRECT_URL':
				if($user->data['is_registered']) {
					return '';
				} else {
					return $this->htmlEncode($this->getRequestVar('view') == 'logs' ? $this->getChatURL().'?view=logs' : $this->getChatURL());
				}
			
			case 'CHARACTER_SELECTION':
				$sql = "SELECT name,id FROM rpg_characters WHERE owner = ".$this->getUserID();
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result)) {
					$characters[] = $row;
				}

				return 'character list not built yet';
						
				

			default:
				return null;
		}
	}
	
	function chatViewLogin() {
		$this->setChannel($this->getValidRequestChannelID());
		$this->addToOnlineList();
		
		// Add channelID and channelName to info messages:
		$this->addInfoMessage($this->getChannel(), 'channelID');
		$this->addInfoMessage($this->getChannelName(), 'channelName');
		
		// Login message:
		$text = '/login '.$this->getUserName();
		$this->insertChatBotMessage(
			$this->getChannel(),
			$text,
			null,
			1
		);
		
		$RPG = new RPG();
		$RPG->ajaxChat = $this;
		$this->RPG = $RPG;		

		// Channel description and entrance
		$this->insertChatBotMessage(
			$this->getPrivateMessageID(),
			$this->RPG->getChannelDescription($this->getChannel())
		);		
		
	}	

	// Returns true if the userID of the logged in user is identical to the userID of the authentication system
	// or the user is authenticated as guest in the chat and the authentication system
	function revalidateUserID() {
		global $user;
		
		if($this->getUserRole() === AJAX_CHAT_GUEST && $user->data['user_id'] == ANONYMOUS || ($this->getUserID() === $user->data['user_id'])) {
			return true;
		}
		return false;
	}

	// Returns an associative array containing userName, userID and userRole
	// Returns null if login is invalid
	function getValidLoginUserData() {
		global $auth,$user;
		
		// Return false if given user is a bot:
		if($user->data['is_bot']) {
			return false;
		}
		
		// Check if we have a valid registered user:
		if($user->data['is_registered']) {
			$userData = array();
			$userData['userID'] = $user->data['user_id'];

			$userData['userName'] = $this->trimUserName($user->data['username']);
			
			if($auth->acl_get('a_'))
				$userData['userRole'] = AJAX_CHAT_ADMIN;
			elseif($auth->acl_get('m_'))
				$userData['userRole'] = AJAX_CHAT_MODERATOR;
			else
				$userData['userRole'] = AJAX_CHAT_USER;

			return $userData;
			
		} else {
			// Guest users:
			return $this->getGuestUser();
		}
	}

	// Store the channels the current user has access to
	// Make sure channel names don't contain any whitespace
	function &getChannels() {
		if($this->_channels === null) {
			global $auth,$config,$user,$db;

			$this->_channels = array();

			$allChannels = $this->getAllChannels();

			foreach($allChannels as $key=>$value) {
				// Check if we have to limit the available channels:
				if($this->getConfig('limitChannelList') && !in_array($value, $this->getConfig('limitChannelList'))) {
					continue;
				}

				// Add the valid channels to the channel list (the defaultChannelID is always valid):
				//if($value == $this->getConfig('defaultChannelID') || $auth->acl_get('f_read', $value)) {
					$this->_channels[$key] = $value;
				//}
			}
		}
		return $this->_channels;
	}

	// Store all existing channels
	// Make sure channel names don't contain any whitespace
	function &getAllChannels() {
		if($this->_allChannels === null) {
			// Get all existing channels:
			$customChannels = $this->getCustomChannels();
			
			$defaultChannelFound = false;
			
			foreach($customChannels as $key=>$value) {
				$forumName = $this->trimChannelName($value);
				
				$this->_allChannels[$forumName] = $key;
				
				if($key == $this->getConfig('defaultChannelID')) {
					$defaultChannelFound = true;
				}
			}
			
			if(!$defaultChannelFound) {
				// Add the default channel as first array element to the channel list:
				$this->_allChannels = array_merge(
					array(
						$this->trimChannelName($this->getConfig('defaultChannelName'))=>$this->getConfig('defaultChannelID')
					),
					$this->_allChannels
				);
			}
		}
		return $this->_allChannels;
	}
	
	function trimChannelName($channelName) {		
		return $this->trimString($channelName, null, null, false, true);
	}	

	function switchChannel($channelName,$channelID = null,$direction = null) {
		if ($channelID == null) {
			$channelID = $this->getChannelIDFromChannelName($channelName);
		}
	
		if($channelID !== null && $this->getChannel() == $channelID) {
			// User is already in the given channel, return:
			return;
		}

		$oldChannel = $this->getChannel();

		if ((!$this->RPG->channelHasExit($oldChannel,$channelID)) && ($direction != 'teleport') && ($direction != 'ascend') && ($direction != 'descend')) {
			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				"[b]It doesn't look like you can get there from here.[/b]\nMake sure you are [b]I[/b]n [b]C[/b]haracter (IC) using [code]/character[/code], and check the current room's exits using [code]/exits[/code]."
			);
			return;
		} else {

			$this->setChannel($channelID);
			$this->updateOnlineList();
			
			if (($this->getSessionVar('InCharacter') == true) && ($oldChannel != 4) && ($direction != 'teleport') && ($direction != 'ascend') && ($direction != 'descend') ) {
				$this->insertChatBotMessage(
					$oldChannel,
					$this->RPG->getCharacterLink($this->getSessionVar('CharacterName')) . " has left the area, heading [b]".$direction."[/b] into ".$channelName."."
				);
			}

			if (($this->getSessionVar('InCharacter') == true) && ($this->getChannel() != 4) && ($direction != 'teleport')) {
				$this->insertChatBotMessage(
					$this->getChannel(),
					$this->RPG->getCharacterLink($this->getSessionVar('CharacterName')) . " has entered the area."
				);
			}		
			
			// Channel description and entrance
			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				$this->RPG->getChannelDescription($this->getChannel())."\n".$this->RPG->listExits($this->getChannel())."\n".$this->RPG->getCharactersPresent($this->getChannel())
			);

			$this->addInfoMessage($channelName, 'channelSwitch');
			$this->addInfoMessage($channelID, 'channelID');
			$this->_requestVars['lastID'] = 0;
		
		}
		
	}
	
	function setChannel($channel) {	
		
		if ($this->getSessionVar('InCharacter') == true) {
			$this->RPG->setLocation($channel,$this->getSessionVar('CharacterID'));
			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				$this->RPG->getChannelDescription($channel)." ".$this->RPG->listExits($channel)
			);			
		}
	
		$this->setSessionVar('Channel', $channel);	
		
		// Save the channel enter timestamp:
		$this->setChannelEnterTimeStamp(time());
		
		// Update the channel authentication for the socket server:
		if($this->getConfig('socketServerEnabled')) {
			$this->updateSocketAuthentication(
				$this->getUserID(),
				$this->getSocketRegistrationID(),
				array($channel,$this->getPrivateMessageID())
			);
		}

		// Reset the logs view socket authentication session var:		
		if($this->getSessionVar('logsViewSocketAuthenticated')) {
			$this->setSessionVar('logsViewSocketAuthenticated', false);
		}
	}
	
	// Method to set the style cookie depending on the phpBB user style
	function setStyle() {
		global $config,$user,$db;
		
		if(isset($_COOKIE[$this->getConfig('sessionName').'_style']) && in_array($_COOKIE[$this->getConfig('sessionName').'_style'], $this->getConfig('styleAvailable')))
			return;
		
		$styleID = (!$config['override_user_style'] && $user->data['user_id'] != ANONYMOUS) ? $user->data['user_style'] : $config['default_style'];
		$sql = 'SELECT
						style_name
					FROM
						'.STYLES_TABLE.'
					WHERE
						style_id = \''.$db->sql_escape($styleID).'\';';
		$result = $db->sql_query($sql);
		$styleName = $db->sql_fetchfield('style_name');
		$db->sql_freeresult($result);
		
		if(!in_array($styleName, $this->getConfig('styleAvailable'))) {
			$styleName = $this->getConfig('styleDefault');
		}
		
		setcookie(
			$this->getConfig('sessionName').'_style',
			$styleName,
			time()+60*60*24*$this->getConfig('sessionCookieLifeTime'),
			$this->getConfig('sessionCookiePath'),
			$this->getConfig('sessionCookieDomain'),
			$this->getConfig('sessionCookieSecure')
		);
		return;
	}
	
	function getUserName() {
		return $this->getSessionVar('UserName');
	}

	function &getCustomChannels() {
	
		global $config,$user,$db;
		
		$sql = 'SELECT * FROM rpg_places';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			$channels[$row['id']] = $row['name'];
		}
		$db->sql_freeresult($result);
		
		return $channels;
	}
	
	function promptUser($question, $answers) {
	
		$this->setSessionVar('promptQuestion',true);
		$this->setSessionVar('promptSelections',$answers);
		
		foreach ($answers as $answer) {
			$selections[] = '[b]('.$answer['location'].') [/b]'.$answer['name'];
		}
		
		$this->insertChatBotMessage($this->getPrivateMessageID(),$question."\n[b]Selections:[/b] ".implode(", ",$selections));
	
		return true;
	}
	
	function parseCustomCommands($text, $textParts) { 
	
		global $user;

		// Now... what ever shall we do?
		switch($textParts[0]) {
			case '/staff':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$this->insertChatBotMessage( $this->getPrivateMessageID(),
						"[b]Staff Commands (Only displayed for staff)[/b]
						[code]/kick <USERNAME> [time]           [/code]: kicks (and temporarily bans) the user for the specified amount of [time]. Default: ".$this->getConfig('defaultBanTime')." minutes.
						[code]/bans                             [/code]: lists the current banned usernames.
						[code]/build <DIRECTION>                [/code]: builds a new room in <DIRECTION>, with synchronous exits in both directions.
						[code]/hide <DIRECTION>                	[/code]: hides the exit from the current room to <DIRECTION>, allowing travel in that direction but not displaying the exit in [code]/exits[/code].
						[code]/channelName <NAME>               [/code]: sets the current channel's name.
						[code]/channelDescription <DESCRIPTION> [/code]: sets the current channel's description.
						[code]/teleport <ROOM ID>               [/code]: immediately warps you to the room with the ID specified.
						[code]/broadcast <MESSAGE>              [/code]: sends out a private message to ALL online users.
						[code]/lasterror                        [/code]: returns the last known error of chat.
					");
					
					return true;
				} else {
					return false;
				}			
			break;
			case '/help':
			
				$this->insertChatBotMessage( $this->getPrivateMessageID(), 
				"[b]Chat Commands[/b]
				[code]/me Text                          [/code]: Perform an action.
				[code]/ignore Username                  [/code]: Ignore messages from a username.
				[code]/msg Username Text                [/code]: Send a private message.
				[code]/join Channelname                 [/code]: Join a channel.
				[code]/join                             [/code]: Create a private room.
				[code]/invite Username                  [/code]: Invite someone (e.g. to a private room).
				[code]/uninvite Username                [/code]: Revoke invitation.
		
				[b]General Roleplay Commands[/b]
				[code]/ic <CHARACTER NAME>              [/code]: allows you to go [b]I[/b]n [b]C[/b]haracter (IC) as a character that in your [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=personal]list of characters[/url].
				[code]/ooc                              [/code]: exits the mode set by [code]/ic Character Name[/code].
				
				[b]RPG Commands[/b]
				[code]/<DIRECTION>                      [/code]: moves your character to the room in your direction, where direction is: (north, south, east, west, up, down)
				[code]/look                             [/code]: repeats the description of your current room.
				[code]/exits                            [/code]: displays the available exits in the current room.
				[code]/examine <CHARACTER NAME>         [/code]: displays the visual appearance of the target character.
				[code]/inspect <CHARACTER NAME>         [/code]: alias for /examine.
				[code]/stats <CHARACTER NAME>           [/code]: displays the detailed statistics of the target character.
				[code]/attack <CHARACTER NAME>          [/code]: launches a melee attack at the target character using your current weapon.
				" );
				return true;
			// Away from keyboard message: 
/* 			case '/afk':
				// Set the userName: 
				if ($this->getSessionVar('AwayFromKeyboard') != true) {
					$this->setUserName('[AFK]_'.$this->getUserName());
				}
				// Update the online user table: 
				$this->updateOnlineList(); 
				// Add info message to update the client-side stored userName: 
				$this->addInfoMessage($this->getUserName(), 'userName'); 
				// Store AFK status as session variable: 
				$this->setSessionVar('AwayFromKeyboard', true);
				return true; */
			case '/ic':
				
					if (strlen($textParts[1]) >= 2) {

						$character_name = $this->getSpacedParameter($textParts);
					
						$character_id = $this->RPG->getCharacterId($character_name);
						if (!$character = $this->RPG->getCharacterData($character_id)) {
							$this->RPG->spawn($character_id);
							$character = $this->RPG->getCharacterData($character_id);
						}						
						
						
						if ($character_id >= 1  && ($character['owner'] == $user->data['user_id'])) {
						
												
									
							if ($this->getSessionVar('InCharacter') == true) {
								$this->insertChatBotMessage( $character['location'], $this->getUserName() . " switches characters to ".$this->RPG->getCharacterLink($character_name).": ".$character['synopsis']);
							} else {
								$this->insertChatBotMessage( $character['location'], $this->getUserName() . " enters the multiverse, In Character (IC) as ".$this->RPG->getCharacterLink($character_name).": ".$character['synopsis']);
								$this->insertChatBotMessage( $this->getChannel(), $this->getUserName() . " enters the multiverse, In Character (IC) as ".$this->RPG->getCharacterLink($character_name).": ".$character['synopsis']);
							}
							
							$this->setSessionVar('CharacterName', $character_name);
							$this->setSessionVar('CharacterID', $character_id);
							$this->setSessionVar('InCharacter', true);
							
							$this->setChannel($character['location']);
							$this->updateOnlineList();
							$this->addInfoMessage($this->getChannelNameFromChannelID($character['location']), 'channelSwitch');
							$this->addInfoMessage($character['location'], 'channelID');
							$this->_requestVars['lastID'] = 0;
							
							// Channel description and entrance
							$this->insertChatBotMessage(
								$this->getPrivateMessageID(),
								$this->RPG->getChannelDescription($this->getChannel())."\n".$this->RPG->listExits($this->getChannel())
							);							
						
						} else {
							$this->insertChatBotMessage($this->getPrivateMessageID(),"Sorry, you don't own a character by that name. Create one by [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]adding a character[/url] to your account.");
							return true;
						}
					
					} else {
						$this->insertChatBotMessage( $this->getPrivateMessageID(), "To go [b]I[/b]n [b]C[/b]haracter (IC), you need to first [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]create a character[/url]. Once you've done this, type [code]/ic Character Name[/code] (case sensitive!) to activate the profile." );
						return true;
					}
					
					$this->updateOnlineList();
					$this->addInfoMessage($this->getUserName(), 'userName');
					$this->setSessionVar('InCharacter', true);
					
					return true;

			case '/ooc':
				if ($this->getSessionVar('InCharacter') == true) {
				
					if (count($textParts) == 1) {
					
						$this->insertChatBotMessage( 0, $this->getUserName()." was playing as ".$this->RPG->getCharacterLink($this->getSessionVar('CharacterName')).", but is now Out Of Character (OOC)." );
						$this->insertChatBotMessage( $this->getChannel(), $this->RPG->getCharacterLink($this->getSessionVar('CharacterName')) . " (".$this->getUserName().") has disappeared, is now Out Of Character (OOC)." );
						
						$this->setChannel(0);
						$this->updateOnlineList();
						$this->addInfoMessage($this->getChannelNameFromChannelID(0), 'channelSwitch');
						$this->addInfoMessage(0, 'channelID');
						$this->_requestVars['lastID'] = 0;				
						
						$this->addInfoMessage($this->getUserName(), 'userName');
						$this->setSessionVar('InCharacter', false);
						$this->setSessionVar('CharacterName', null);
					}
					return true;
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are not In Character!");
					return true;			
				}
			case '/location':
				$this->insertChatBotMessage($this->getPrivateMessageID(),"Location: ".$this->getChannel()." (".$this->getChannelNameFromChannelID($this->getChannel()).")");
				return true;
			break;
			case '/character':
				if ($this->getSessionVar('CharacterName')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are currently [b]I[/b]n [b]C[/b]haracter (IC) as ".$this->getSessionVar('CharacterName')." (".$this->getSessionVar('CharacterID').").");
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are currently [b]O[/b]ut [b]O[/b]f [b]C[/b]haracter (OOC).");
				}
				return true;
			case '/exits':
				$msg = $this->RPG->listExits($this->getChannel());
				
				$this->insertChatBotMessage($this->getPrivateMessageID(),$msg);
				return true;
			break;
			case '/north':
				if (!$destination = $this->RPG->getExit($this->getChannel(),'north')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go that direction.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'north');
				}
				return true;
			break;
			case '/south':
				if (!$destination = $this->RPG->getExit($this->getChannel(),'south')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go that direction.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'south');
				}
				return true;
			break;
			case '/east':
				if (!$destination = $this->RPG->getExit($this->getChannel(),'east')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go that direction.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'east');
				}
				return true;
			break;
			case '/west':
				if (!$destination = $this->RPG->getExit($this->getChannel(),'west')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go that direction.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'west');
				}
				return true;
			break;
			case '/up':
				if (!$destination = $this->RPG->getExit($this->getChannel(),'up')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go that direction.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'up');
				}
				return true;
			break;
			case '/down':
				if (!$destination = $this->RPG->getExit($this->getChannel(),'down')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go that direction.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'down');
				}
				return true;
			break;
			case '/ascend':
				if (!$destination = $this->RPG->getParentChannel($this->getChannel())) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go any higher.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'ascend');
				}			
				return true;
			break;
			case '/descend':
				if (!$destinations = $this->RPG->getChildChannels($this->getChannel())) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go any lower.");
				} else {
					$this->promptUser("To descend from here, you must select an arrival point.",$destinations);
				}			
				return true;
			break;
			case '/build':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$direction = $textParts[1];
					
					if ($this->RPG->getOwner($this->getChannel()) != $this->getUserID()) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You do not own this room, so you can not build from here.");
						return true;
					}
					
					if ($this->RPG->getExit($this->getChannel(),$direction)) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"There is already a room in that direction.");						
						return true;
					}

					if ($destination = $this->RPG->build($this->getChannel(),$direction,$this->getUserID())) {
					
						$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination);
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You have built a room ($destination, ".$this->getChannelNameFromChannelID($destination)."). (If the channel name is [b]not[/b] listed at left, you need to move there on your own.)  You now need to set the name ([code]/channelName[/code]) and description ([code]/channelDescription[/code]).");
						
						return true;
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"Something broke.");
						return true;
					}
				} else {
					return false;
				}
			break;
			case '/hide':
			
				
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$direction = $textParts[1];
					$this->insertChatBotMessage($this->getPrivateMessageID(),"gothere. :3 $direction");
					if ($this->RPG->getOwner($this->getChannel()) != $this->getUserID()) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You do not own this room, so you can not build from here.");
						return true;
					}

					if ($this->RPG->hideExit($this->getChannel(),$direction)) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You have hidden the [code]/".$direction."[/code] exit.");
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"Something broke. Tell Rem.");
					}						
					
					return true;					

						
						

				} else {
					return false;
				}
			break;
			case '/look':
				if ( $this->getUserRole() == AJAX_CHAT_ADMIN) {
			
					// Channel description and entrance
					$this->insertChatBotMessage(
						$this->getPrivateMessageID(),
						$this->RPG->getChannelDescription($this->getChannel())
					);				
				} else {
			
					// Channel description and entrance
					$this->insertChatBotMessage(

						$this->getPrivateMessageID(),
						$this->RPG->getChannelDescription($this->getChannel())."\n".$this->RPG->listExits($this->getChannel())."\n".$this->RPG->getCharactersPresent($this->getChannel())
					);
				}
				return true;
			break;
			case '/broadcast':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$users = $this->getOnlineUserIDs();
					
					//$this->insertChatBotMessage($this->getPrivateMessageID(),"Something broke: ".implode(",",$users));
					
					foreach ($users as $userID) {
						$this->insertChatBotMessage($this->getPrivateMessageID($userID),$this->getSpacedParameter($textParts));
					}
					
					return true;
				} else {
					return false;
				}	
			break;
			case '/teleport':
				if(($this->getUserRole() == AJAX_CHAT_ADMIN) || ($this->getUserRole() == AJAX_CHAT_MODERATOR)) {
					if ($this->getSessionVar('InCharacter') == true) {
							$this->setChannel($textParts[1]);
							$this->updateOnlineList();
							$this->addInfoMessage($this->getChannelNameFromChannelID($textParts[1]), 'channelSwitch');
							$this->addInfoMessage($textParts[1], 'channelID');
							$this->_requestVars['lastID'] = 0;
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You need to be In Character to teleport.");
					}
					return true;
				} else {
					return false;
				}			
			break;
			case '/channelName':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$this->RPG->editChannelName($this->getChannel(),$this->getSpacedParameter($textParts));
					$this->insertChatBotMessage($this->getChannel(),"Suddenly, this area begins to warp and melt - everything here begins to twist and turn, and finally evaporate... before taking shape as something completely new: ".$this->getSpacedParameter($textParts));
					return true;
				} else {
					return false;
				}			
			break;
			case '/channelDescription':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$this->RPG->editChannelDescription($this->getChannel(),$this->getSpacedParameter($textParts));
					$this->insertChatBotMessage($this->getChannel(),"Something jarrs your perception, as it feels like something here has changed.  You can't quite figure it out, but something is certainly different...");
					return true;
				} else {
					return false;
				}			
			break;
			case '/makelog':
				$this->makeLogs();
				return true;
			break;
			case '/lasterror':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"Last error: ".implode("\n",error_get_last()));
					return true;
				} else {
					return false;
				}
			break;
			case '/takeover':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$this->insertChatBotMessage( $this->getChannel(), $text );
					return true;
				} else {
					return false;
				}
			case '/moveuser':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					if ($this->moveUser( $textParts[1], $textParts[2] )) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"The movement attempt succeeded.");
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"The movement attempt failed.");
					}
					return true;
				} else {
					return false;
				}
			case '/stats':
				if (strlen($textParts[1]) >= 2) {
							
					$character_name = $this->getSpacedParameter($textParts);
					$character_id = $this->RPG->getCharacterId($character_name);
					
					if ($character_id >= 1) {
					
						$character_data = implode("\n",$this->RPG->getCharacterData($character_id));
					
						$this->insertChatBotMessage($this->getPrivateMessageID(),"Character statistics of ".$this->RPG->getCharacterLink($character_name).":
						".$character_data.".");
					
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"That character doesn't seem to exist!");
					}
					
					return true;

					
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You didn't specify whose stats you were trying to view!");
					return true;
				}
			case '/examine':
			case '/inspect':
				if (strlen($textParts[1]) >= 2) {
						
					$character_name = $this->getSpacedParameter($textParts);
					$character_id = $this->RPG->getCharacterId($character_name);
					
					if ($character_id >= 1) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You examine ".$this->RPG->getCharacterLink($character_name)." and find them to be ".$this->RPG->getCharacterCondition($character_id).".");
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"That character doesn't seem to be here!");
					}
					
					return true;

					
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You didn't specify who you were trying to look at!");
					return true;
				}
			case '/spawn':
				if($this->getUserRole() != AJAX_CHAT_ADMIN) {
					return false;
				}
				if (strlen($textParts[1]) >= 2) {
							
					$character_name = $this->getSpacedParameter($textParts);
					$character_id = $this->RPG->getCharacterId($character_name);
					
					if ($character_id > 1) {
					
						if ($this->RPG->spawn($character_id)) {
							$this->insertChatBotMessage($this->getChannel(),$this->RPG->getCharacterLink($character_name)." is spawned by ".$this->getUserName()."!");
						} else {
							$this->insertChatBotMessage($this->getPrivateMessageID(),"Sorry, couldn't spawn \"".$character_name."\" - something went wrong during the RNA sequencing process.");
						}
					
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"Sorry, couldn't spawn \"".$character_name."\" - something went wrong during the locating process.");
					}
				
				
					return true;					
				} else {
					return false;
				}				
				
			case '/revive':
				if($this->getUserRole() != AJAX_CHAT_ADMIN) {
					return false;
				}
				if (strlen($textParts[1]) >= 2) {
							
					$character_name = $this->getSpacedParameter($textParts);
					$character_id = $this->RPG->getCharacterId($character_name);
				
				
					$this->RPG->revive($character_id);
					
					$character = $this->RPG->getCharacterData($character_id);
					
					if ($character['health'] == "") {
						
						$this->insertChatBotMessage($this->getPrivateMessageID(),$this->RPG->getCharacterLink($character_name)." couldn't be revived for some reason. TODO: fix this. ID: ".$character_id." | ".implode(",",$character));

						return true;
					}

					$this->insertChatBotMessage($this->getChannel(),$this->RPG->getCharacterLink($character_name)." is revived by some unseen force! ".$character['health']." health remaining.");
				
					return true;					
				} else {
					return false;
				}
			case '/use':
				if (strlen($textParts[1]) >= 2) {
					
					$itemName = $this->getSpacedParameter($textParts);
				
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You try to use ".$itemName.", but you don't know how.");
				}
				return true;
			case '/attack':
				if($this->getUserRole() != AJAX_CHAT_ADMIN) {
					return false;
				}
				if (strlen($textParts[1]) >= 2) {
							
					$defender_name = $this->getSpacedParameter($textParts);
					
					if (!$attacker_name = $this->getSessionVar('CharacterName')) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't do this, you aren't In Character!");
						return true;
					}
					
					if (!$attacker_id = $this->RPG->getCharacterId($attacker_name)) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"It doesn't look like you have a character. Ask an admin to [code]/spawn[/code] one that you own.");
						return true;
					}
					
					if (!$defender_id = $this->RPG->getCharacterId($defender_name)) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"That isn't an actual character!");
						return true;
					}
					
					$attacker_data = $this->RPG->getCharacterData($attacker_id);			
					$defender_data = $this->RPG->getCharacterData($defender_id);

					if ($this->RPG->canAction($attacker_id) == false) {					
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't do that right now, you don't have any energy left! (".$this->RPG->timetoNextAction($attacker_id)." seconds left!)");
						//alert("You can't do that right now, you don't have any energy left! (".$this->RPG->timetoNextAction($attacker_id)." seconds left!)");
						return true;
					}
					
					if ( $attacker_data['location'] != $defender_data['location'] ) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"That character is not here, you can not attack them.");
						return true;					
					}
					
					if ($defender_data['health'] <= 0) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"That character is already unconscious, give them a rest!");
						return true;
					}

					$attack = $this->RPG->attack($defender_id,$attacker_id);
					$this->RPG->updateLastAction($attacker_id);					
					$this->RPG->damage($defender_id,$attack['damage']);
					
					$this->insertChatBotMessage( $this->getChannel(), $this->RPG->getCharacterLink($attacker_name)." attacks ".$this->RPG->getCharacterLink($defender_name)."... ".$attack['outcome']);
	
					if (($defender_data['health'] - $damage) <= 0) {
						$this->insertChatBotMessage( $this->getChannel(), $this->RPG->getCharacterLink($defender_name)." has been knocked unconscious by ".$this->RPG->getCharacterLink($attacker_name)."!" );
					}
					
					$this->RPG->damage($defender_id,$damage);

					return true;
					
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You didn't specify who you were attacking.");
					
					return true;
				}
				
				
			default:				
				return false;
		}
		
	} 

	function onNewMessage($text) {

		// Reset AFK status on first inserted message: 
		if($this->getSessionVar('AwayFromKeyboard')) { 
			$this->setUserName($this->subString($this->getUserName(), 6)); 
			$this->updateOnlineList(); 
			$this->addInfoMessage($this->getUserName(), 'userName');
			$this->setSessionVar('AwayFromKeyboard', false); 
		}

		return true; 
	}
	
	function getXMLMessages() {
		switch($this->getView()) {
			case 'chat':
				return $this->getChatViewXMLMessages();
			case 'teaser':
				return $this->getTeaserViewXMLMessages();
			case 'logs':
				return $this->getLogsViewXMLMessages();
			case 'history':
				return "Not yet available.";
			default:
				return $this->getLogoutXMLMessage();
		}
	}
	
	function getChatViewMessageXML(
		$messageID,
		$timeStamp,
		$userID,
		$userName,
		$userRole,
		$channelID,
		$text
		) {
		$message = '<message';
		$message .= ' id="'.$messageID.'"';
		$message .= ' dateTime="'.date('r', $timeStamp).'"';
		$message .= ' userID="'.$userID.'"';
		$message .= ' userRole="'.$userRole.'"';
		$message .= ' channelID="'.$channelID.'"';
		$message .= '>';
		$message .= '<character><![CDATA['.$this->encodeSpecialChars($userName).']]></character>';
		$message .= '<username><![CDATA['.$this->encodeSpecialChars($this->getNameFromID($userID)).']]></username>';
		$message .= '<text><![CDATA['.$this->encodeSpecialChars($text).']]></text>';
		$message .= '</message>';
		return $message;
	}	
	
	function insertMessage($text) {
	
		// TODO: make this if() more readible, and possibly more accurate. 
		if ((((substr($text,0,3) != "/ic")
			&& (substr($text,0,5) != "/kick")
			&& (substr($text,0,5) != "/join")
			&& (substr($text,0,6) != "/exits")
			&& (substr($text,0,6) != "/north")
			&& (substr($text,0,7) != "/invite")
			&& (substr($text,0,9) != "/takeover"))
			&& ((substr($text,0,1) != "!") && (($this->getUserRole() != AJAX_CHAT_ADMIN)) )
			)) {
	
				if(!$this->isAllowedToWriteMessage())
					return;

				if(!$this->floodControl())
					return;

				$text = $this->trimMessageText($text);	
				if($text == '')
					return;
				
				if(!$this->onNewMessage($text))
					return;
					
			}
		
		$text = $this->replaceCustomText($text);
		if (($this->getSessionVar('InCharacter') == true) && ((substr($text,0,3) == "/me") || (substr($text,0,1) != "/" ))) {
			$character_name = $this->getSessionVar('CharacterName') ? $this->getSessionVar('CharacterName') : $this->getUserName();
			
			// Do some session magic so we know something a little later on
					
			if ($this->getSessionVar('promptQuestion')) {
				//$this->insertChatBotMessage( $this->getPrivateMessageID(),"found a question... :".$this->getSessionVar('promptQuestion'));
				
				// Terminate this question
				$this->setSessionVar('promptQuestion',false);
				
				$destinations = $this->getSessionVar('promptSelections');
				
				//  Switch the channel...
				// TODO: allow this to handle other types of questions using a function
				//$this->switchChannel($this->getChannelNameFromChannelID($destinations[$text]['location']),$destinations[$text]['location'],'descend');
	
				$this->setChannel($destinations[$text]['location']);
				$this->updateOnlineList();
				$this->addInfoMessage($this->getChannelNameFromChannelID($textParts[1]), 'channelSwitch');
				$this->addInfoMessage($destinations[$text]['location'], 'channelID');
				$this->_requestVars['lastID'] = 0;				
		
			} else {				
				// Insert the message normally, because it's not an answer
				$this->insertCustomMessage($this->getUserID(), $character_name, $this->getUserRole(), $this->getChannel(), $text);
				
			}
		} else {
			$this->insertParsedMessage($text);
		}
	}
	
	function insertParsedMessage($text) {
		global $config,$user,$db;
		// If a queryUserName is set, sent all messages as private messages to this userName:
		if($this->getQueryUserName() !== null && strpos($text, '/') !== 0) {
			$text = '/msg '.$this->getQueryUserName().' '.$text;
		}	
		
		$textParts = explode(' ', $text);
		
		// Parse IRC-style commands:
		if(strpos($text, '/') === 0) {

			switch($textParts[0]) {
				
				// Channel switch:
				case '/join':
					$this->insertParsedMessageJoin($textParts);
					break;
					
				// Logout:
				case '/quit':
					$this->logout();
					break;
					
				// Private message:
				case '/msg':
				case '/describe':
					$this->insertParsedMessagePrivMsg($textParts);
					break;
				
				// Invitation:
				case '/invite':
					$this->insertParsedMessageInvite($textParts);
					break;

				// Uninvitation:
				case '/uninvite':		
					$this->insertParsedMessageUninvite($textParts);
					break;

				// Private messaging:
				case '/query':
					$this->insertParsedMessageQuery($textParts);
					break;
				
				// Kicking offending users from the chat:
				case '/kick':
					$this->insertParsedMessageKick($textParts);
					break;
				
				// Listing banned users:
				case '/bans':
					$this->insertParsedMessageBans($textParts);
					break;
				
				// Unban user (remove from ban list):
				case '/unban':
					$this->insertParsedMessageUnban($textParts);
					break;
				
				// Describing actions:
				case '/me':
				case '/action':
					$this->insertParsedMessageAction($textParts);
					break;


				// Listing online Users:
				case '/who':	
					$this->insertParsedMessageWho($textParts);
					break;
				
				// Listing available channels:
				case '/list':	
					$this->insertParsedMessageList($textParts);
					break;

				// Retrieving the channel of a User:
				case '/whereis':
					$this->insertParsedMessageWhereis($textParts);
					break;
				
				// Listing information about a User:
				case '/whois':
					$this->insertParsedMessageWhois($textParts);
					break;
				
				// Rolling dice:
				case '/roll':				
					$this->insertParsedMessageRoll($textParts);
					break;

				// Switching userName:
				case '/nick':				
					$this->insertParsedMessageNick($textParts);
					break;
			
				// Custom or unknown command:
				default:
					if(!$this->parseCustomCommands($text, $textParts)) {				
						$this->insertChatBotMessage(
							$this->getPrivateMessageID(),
							'/error UnknownCommand '.$textParts[0]
						);
					}
			}

		} else {
			// No command found, just insert the plain message:
			$this->insertCustomMessage(
				$this->getUserID(),
				$this->getUserName(),
				$this->getUserRole(),
				$this->getChannel(),
				$text
			);
		}
		
		//include("chatbot.php");
		
	}
	
	function displayUserAvatar($id,$width = 50,$height = 50) {
	
	}
	
	function getSpacedParameter($command) {
		$parameter = "";
		$j = 1;
		while ($j <= (count($command))) {
			$parameter .= $command[$j] . " ";
			$j++;
		}
		return trim($parameter);
	}
	
	function banUser($userName, $banMinutes=null, $userID=null, $banReason=null) {
	
		if($userID === null) {
			$userID = $this->getIDFromName($userName);
		}
		$ip = $this->getIPFromID($userID);
		if(!$ip || $userID === null) {
			return;
		}

		// Remove expired bans:
		$this->removeExpiredBans();
		
		$banMinutes = (int)$banMinutes;
		if(!$banMinutes) {
			// If banMinutes is not a valid integer, use the defaultBanTime:
			$banMinutes = $this->getConfig('defaultBanTime');
		}
		
		$sql = 'INSERT INTO '.$this->getDataBaseTable('bans').'(
					userID,
					userName,
					dateTime,
					ip
				)
				VALUES (
					'.$this->db->makeSafe($userID).',
					'.$this->db->makeSafe($userName).',
					DATE_ADD(NOW(), interval '.$this->db->makeSafe($banMinutes).' MINUTE),
					'.$this->db->makeSafe($this->ipToStorageFormat($ip)).'
				);';
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
	}
	
	function kickUser($userName, $banMinutes=null, $userID=null, $kickReason=null) {
		global $config,$user,$db;
	
		if($userID === null) {
			$userID = $this->getIDFromName($userName);
		}
		if($userID === null) {
			return;
		}

		$banMinutes = $banMinutes ? $banMinutes : $this->getConfig('defaultBanTime');

		if($banMinutes) {
			// Ban User for the given time in minutes:
			$this->banUser($userName, $banMinutes, $userID);
		}

		// Remove given User from online list:
		$sql = 'DELETE FROM
					'.$this->getDataBaseTable('online').'
				WHERE
					userID = '.$this->db->makeSafe($userID).';';
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}

		// Update the socket server authentication for the kicked user:
		if($this->getConfig('socketServerEnabled')) {
			$this->updateSocketAuthentication($userID);
		}
		
		$this->removeUserFromOnlineUsersData($userID);
		
		$usernote = "$userName was kicked from chat for $banMinutes minutes.";
		
		$this->insertChatBotMessage( $this->getChannel(), $usernote);
		
		include_once('/var/www/vhosts/roleplaygateway.com/httpdocs/includes/functions.php');
		
		add_log('admin', 'LOG_USER_FEEDBACK', $userName);
		add_log('mod', 0, 0, 'LOG_USER_FEEDBACK', $userName);
		add_log('user', $userID, 'LOG_USER_GENERAL', $usernote);
		
	}
	
	function moveUser($user,$destination) {
	
		$sql = "UPDATE ".$this->getDataBaseTable('online')." SET channel = ".$this->db->makeSafe($destination)." WHERE userID = ".$this->db->makeSafe($user).";";
	
		$result = $this->db->sqlQuery($sql);
		return true;
	}

	//Override default dice roll output.
	function insertParsedMessageRoll($textParts) {
		if(count($textParts) == 1) {
			// default is one d6:
			$text = '/roll '.$this->getUserName().' 1d6 '.$this->rollDice(6);
		} else {
			$diceParts = explode('d', $textParts[1]);
			if(count($diceParts) == 2) {
				//Number of times to roll
				$number = $diceParts[0];
				//Polarity of modifier to use (negative or positive)
				$polarity = '+';
				if(strstr($diceParts[1], '-'))
					$polarity = '-';
				//Split using the correct modifier; if one does not exist, you'll get only the number of sides on the die.
				$sidesParts = explode($polarity, $diceParts[1]);
				$sides = $sidesParts[0];

				// Dice number must be an integer between 1 and 100, else roll only one:
				$number = ($number > 0 && $number <= 100) ?  $number : 1;
				
				// Add the modifier to the sum if it exists, otherwise set the sum to 0:
				$sum = 0;
				if(count($sidesParts) == 2)
					$sum = $polarity == '+' ? $sidesParts[1] : -1*$sidesParts[1];
				
				// Sides must be an integer between 1 and 100, else take 6:
				$sides = ($sides > 0 && $sides <= 100) ?  $sides : 6;
				
				$text = '/roll '.$this->getUserName().' '.$textParts[1].' ';
				for($i=0; $i<$number; $i++) {
					if($i != 0)
						$text .= ',';
					$roll = $this->rollDice($sides);
					$text .= $roll;
					$sum = $sum + $roll;
					$roll = 0;
				}
				$text .= '('.$sum.') ';
			} else {
				// if dice syntax is invalid, roll one d6:
				$text = '/roll '.$this->getUserName().' 1d6 '.$this->rollDice(6);
			}
		}
		$this->insertChatBotMessage(
			$this->getChannel(),
			$text
		);
	}
	
	function purgeLogs() {
		$sql = 'INSERT INTO ajax_chat_messages_archive SELECT * FROM
					'.$this->getDataBaseTable('messages').'
				WHERE
					dateTime < DATE_SUB(NOW(), interval '.$this->getConfig('logsPurgeTimeDiff').' DAY);';
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		$sql = 'DELETE FROM
					'.$this->getDataBaseTable('messages').'
				WHERE
					dateTime < DATE_SUB(NOW(), interval '.$this->getConfig('logsPurgeTimeDiff').' DAY);';
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
	}
	
	function makeLogs() {
		//if ($purgeDelay < (time() - 3600) || !isset($purgeDelay)) {

			// SQL Query to select messages for purge:
			$sql = 'SELECT
						userName,
						channel AS channelID,
						UNIX_TIMESTAMP(dateTime) AS timeStamp,
						text
					FROM
						ajax_chat_messages
					ORDER BY id LIMIT 8000;';

			$result = $this->db->sqlQuery($sql);

			// Stop if an error occurs:
			if($result->error()) {
				echo $result->getError();
				die();
			}

			// Store result for logging:
			$logMsg = '';
			while($row = $result->fetch()) {
				$privmsg = ereg('/privmsg', $row['text']);
				if (($privmsg == false) && ($row['channelID'] == 0)) {
					$logMsg .= '('.date('r', $row['timeStamp']).') ';
					$logMsg .= $this->decodeSpecialChars($row['userName']).': ';
					$logMsg .= $this->decodeSpecialChars($row['text'])."\n";
				}
			}
			$result->free();

			// Files are rotated every week, labelled by week number, month, and year.
			// File container where all messages are logged:
			$fileContainer = AJAX_CHAT_PATH.'log/'.date("WMY").'.log';
			$fileDirectory = AJAX_CHAT_PATH.'log/';

			// Check to make sure directory is writable:
			if(is_writable($fileDirectory)) {
				// Open the said file:
				$filePointer = fopen($fileContainer,"a");

				// Write log messages to file:
				fputs($filePointer,$logMsg);

				// Close the open said file after writing:
				fclose($filePointer);
				
			}

			$purgeDelay = time();
		//}
	}

}

class RPG {

	var $db;
	var $characterID;
	var $location;
	var $ajaxChat;
	
	function setLocation($location,$id) {
		global $config,$user,$db;
		
		if ($location == 0) {
			return true;
		}
		
		$sql = "UPDATE rpg_characters_stats SET location = ".$location." WHERE character_id = ".$id;
		if ($db->sql_query($sql)) {
			return true;
		} else {
			return false;
		}
		
	}
	
	function channelHasExit($channel,$destination) {
		global $config,$user,$db;
		$sql = "SELECT count(*) as exits FROM rpg_exits WHERE place_id = ".$channel." AND destination_id = ".$destination;
		
		if ($result = $db->sql_query($sql)) {
			
			if ($db->sql_fetchfield('exits') > 0) {
				$db->sql_freeresult($result);
				return true;
			} else {
				$db->sql_freeresult($result);
				return false;
			}
		}
		
		
		return false;
	}
	
	function getExits($id) {
		global $config,$user,$db;
		$sql = "SELECT name,direction,id,mode FROM rpg_exits e INNER JOIN rpg_places p ON e.destination_id = p.id WHERE place_id = ".$id;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			$exits[$row['id']]['id'] 		= $row['id'];
			$exits[$row['id']]['name'] 		= $row['name'];
			$exits[$row['id']]['direction'] = $row['direction'];
			$exits[$row['id']]['mode'] 		= $row['mode'];
		}
		$db->sql_freeresult($result);
		
		return $exits;
	}	
	
	function listExits($id) {

		$exits = $this->getExits($id);
		
		if (count($exits) >= 1) {
			$msg = "[i][b]Visible exits:[/b] ";
			
			foreach ($exits as $exit) {
				if ($exit['mode'] != 'hidden') {
					$exitlist[] = $exit['name'] . " (".$exit['direction'].")";
				}
			}
			$exitlist = implode(", ",$exitlist);
		
		} else {
			$msg = "[i][b]There are no visible exits![/b]";
		}

		$msg .= $exitlist."[/i]";	
		
		return $msg;	
	}

	function getExit($id,$direction) {
		global $config,$user,$db;
		$sql = "SELECT destination_id FROM rpg_exits e
					INNER JOIN rpg_places p
						ON e.destination_id = p.id
					WHERE place_id = ".$id." AND direction = '".$direction."'";
		
		if ($result = $db->sql_query($sql)) {
			if ($row = $db->sql_fetchrow($result)) {
				
				$db->sql_freeresult($result);
				return $row['destination_id'];
			}
			$db->sql_freeresult($result);
		}
		
		return false;
	}

	function getParentChannel($id) {
		global $config,$user,$db;
		$sql = "SELECT parent_id FROM rpg_places 
					WHERE id = ".$id;
		
		if (!$db->sql_query($sql)) {	
			return false;
		} else {
			return (int) $db->sql_fetchfield('parent_id');
		}
		return false;
	}
	
	function getChildChannels($id) {
		global $config,$user,$db;
		$sql = "SELECT id,name FROM rpg_places 
					WHERE parent_id = ".$id;
		
		if (!$result = $db->sql_query($sql)) {	
			return false;
		} else {
			while ($row = $db->sql_fetchrow($result)) {
				$children[$row['id']]['location'] 	= $row['id'];
				$children[$row['id']]['name'] 		= $row['name'];
			}
			$db->sql_freeresult($result);
			return (array) $children;
		}
		return false;
	}
	
	function getChannelDescription($id) {
		global $config,$user,$db;

		$sql = "SELECT description FROM rpg_places WHERE id = ".$id;

		if (!$db->sql_query($sql)) {	
			return false;
		} else {
			return (string) $db->sql_fetchfield('description');
		}
		return false;		
	
	}
	
	function editChannelDescription($id,$description) {
		global $config,$user,$db;
		$sql = "UPDATE rpg_places SET description = '".$db->sql_escape($description)."' WHERE id = $id";
		$db->sql_query($sql);
		return;
	}
	
	function editChannelName($id,$name) {
		global $config,$user,$db;
		$sql = "UPDATE rpg_places SET name = '".$db->sql_escape($name)."' WHERE id = $id";
		$db->sql_query($sql);
		return;
	}
	
	function hideExit($from,$direction) {
		global $config,$user,$db;
		$sql = "UPDATE rpg_exits SET mode = 'hidden' WHERE place_id = ".$from." AND direction = '".$direction."'";
		if ($db->sql_query($sql)) {
			return true;
		} else {
			return false;
		}
	}
	
	function addExit($from,$to,$direction) {
		global $config,$user,$db;
		$sql = "INSERT INTO rpg_exits (`place_id`,`destination_id`,`direction`) VALUES ('".$from."','".$to."','".$direction."')";
		if ($db->sql_query($sql)) {
			return true;
		} else {
			return false;
		}
	}
	
	function build($from,$direction,$owner = 0,$synchronous = true) {
		global $config,$user,$db;
		
		$sql = "INSERT INTO rpg_places (`name`,`description`,`owner`,`roleplay_id`) VALUES ('".md5(time())."','You have entered the void - an black expanse with absolutely nothing in it.','".$owner."','1')";
		if (!$db->sql_query($sql)) {
			return false;
		}
		
		if (!$destination = $db->sql_nextid()) {
			return false;
		}
		
		if (!$this->addExit($from,$destination,$direction)) {
			return false;
		}
		
		if ($synchronous) {
			$this->addExit($destination,$from,$this->returningDirection($direction));		
		}
		
		return $destination;
		
	}
	
	function getOwner($id) {
		global $config,$user,$db;
		
		$sql = "SELECT owner FROM rpg_places WHERE id = ".$id;
		if (!$db->sql_query($sql)) {	
			return false;
		} else {
			return (int) $db->sql_fetchfield('owner');
		}
		return false;
	}
	
	function getCharacterData($id) {

	
	/*
		$xmlDoc = new DOMDocument();
		$xmlDoc->load("http://www.roleplaygateway.com/api/character/".$id);
		
		$characterNode = $xmlDoc->getElementsByTagName( "characterdata" );
		
		if (count($characterNode) < 1) {
			return false;
		}
		
		foreach ($characterNode as $characterNode) {
		
			$synopsis = $characterNode->getElementsByTagName( "synopsis" );
			$characterData['synopsis'] = $synopsis->item(0)->nodeValue;
		
			$health = $characterNode->getElementsByTagName( "health" );
			$characterData['health'] = $health->item(0)->nodeValue;
			
			$healthMax = $characterNode->getElementsByTagName( "healthMax" );
			$characterData['healthMax'] = $healthMax->item(0)->nodeValue;		
			
			$dexterity = $characterNode->getElementsByTagName( "dexterity" );
			$characterData['dexterity'] = $dexterity->item(0)->nodeValue;	
			
			$strength = $characterNode->getElementsByTagName( "strength" );
			$characterData['strength'] = $strength->item(0)->nodeValue;

		}
		
		*/
		
		global $config,$user,$db;
		
		$id = intval($id);
		
		$sql = "SELECT name,synopsis,location,health,healthMax,dexterity,strength,owner FROM rpg_characters c
					INNER JOIN rpg_characters_stats s ON c.id = s.character_id
					WHERE c.id = ".$id;
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result)) {
			$character_data['name'] 		= $row['name'];
			$character_data['synopsis'] 	= preg_replace("@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@","[color=red](( [b]Notice:[/b] URL removed. Please use the [img] tag in your character's \"Description\", instead of using the \"Synopsis\" field. ))[/color]",$row['synopsis']);
			$character_data['location']		= $row['location'];
			$character_data['health']		= $row['health'];
			$character_data['healthMax'] 	= $row['healthMax'];
			$character_data['dexterity'] 	= $row['dexterity'];
			$character_data['strength'] 	= $row['strength'];
			$character_data['owner'] 		= $row['owner'];
		}
		
		$db->sql_freeresult($result);
		
		return $character_data;
	

	}
	
	function getCharactersPresent($id) {
		global $config,$user,$db;
		// TODO: convert this function to use DBAL
		//$mysqli = new mysqli("localhost", "dbuser_gateway", "Wr?Traxe5ra8wepU", "db_gateway");		
		$sql = "SELECT name,synopsis,owner FROM rpg_characters c
					INNER JOIN rpg_characters_stats s ON c.id = s.character_id
					WHERE s.location = ".$id;

	
		if ($result = $db->sql_query($sql)) {
	
			while ($row = $db->sql_fetchrow($result)) {
				if (
						($this->ajaxChat->isUserOnline($row['owner'])) &&
						(
							in_array(
								$row['owner'],
								$this->ajaxChat->getOnlineUserIDs(
									array($id)
								)
							)
						)
					)
				{
					$characters[] = $this->getCharacterLink($row['name']);
				}
			}	
		
			if (count($characters) >= 1) {
				$msg = "[i][b]Characters here:[/b] ";
			
				$characterlist = implode(", ",$characters);
			
			} else {
				$msg = "[i][b]There is no one else here.[/b]";
			}

		} else {
			return false;
		}
		$db->sql_freeresult($result);
		
		$msg .= $characterlist."[/i]";
		
		
		return $msg;		
	}
	
	function getCharacterLink($name) {
		return "[url=http://www.roleplaygateway.com/characters/".$name."]".$name."[/url]";
	}
	
	function getCharacterCondition($id) {
	
		$characterData = $this->getCharacterData($id);
		
		$percentage = $characterData['health'] / $characterData['healthMax'];
		
		switch ($percentage) {
			case ($percentage == 1):
				$condition = "in perfect health";
			break;
			case ($percentage >= 0.9):
				$condition = "in good health";
			break;
			case ($percentage >= 0.5):
				$condition = "in fair health";
			break;
			case ($percentage >= 0.25):
				$condition = "hurt and bleeding";
			break;
			case ($percentage > 0):
				$condition = "in horrible condition, almost knocked out";
			break;
			case ($percentage <= 0):
				$condition = "knocked completely out";
			break;
			default:
				$condition = "in some sort of condition";
			break;
		}
		
		return $condition;
	}
	
	function getCharacterId($name) {
		global $config,$user,$db;
		$sql = "SELECT id FROM rpg_characters WHERE name = '".$db->sql_escape($name)."'";
		if (!$db->sql_query($sql)) {	
			return false;
		} else {
			return (int) $db->sql_fetchfield('id');
		}
	}
	
	function updateLastAction ($id) {
		global $config,$user,$db;
		$sql = "UPDATE rpg_characters_stats SET lastAction = ".time()." WHERE character_id = ".$id;
		
		if (!$db->sql_query($sql)) {
			return false;
		} else {
			return true;
		}		
	}
	
	function canAction ($id) {
		$characterData = $this->getCharacterData($id);
		
		$speed_modifier = $this->getModifier($characterData['speed']);

		// TODO: Add logic and underlying structure that calculates different rates of speed for different characters.
		$time = time(); 
		// The next action can take place 10 seconds after their last action, give or take some extra modifiers based on the character's speed.
		$nextAction = ($characterData['lastAction'] + (10 + $speed_modifier));
		
		if ((($time > $nextAction)) && ($characterData['health'] > 0)) {
			return true;
		} else {
			return false;
		}
	}
	
	function timetoNextAction ($id) {
		$characterData = $this->getCharacterData($id);
		
		$speed_modifier = $this->getModifier($characterData['speed']);

		// TODO: Add logic and underlying structure that calculates different rates of speed for different characters.
		$time = time(); 
		
		// This is a hack. But I don't know why it's necessary. Don't ask.
		$nextAction = ($characterData['lastAction'] + ((10 - $speed_modifier) - 10));
		
		return $nextAction - $time;
		
	}
	
	function rollDice($sides) {
		// seed with microseconds since last "whole" second:
		mt_srand((double)microtime()*1000000);
		
		return mt_rand(1, $sides);
	}
	
	function attack($target,$attacker) {
	
		$attacker_data = $this->getCharacterData($attacker);
		$defender_data = $this->getCharacterData($target);
		
		$attack = $this->rollDice(20);
		$defense = $this->rollDice(20);

		$damage = $this->rollDice(10);
		$damage += $this->rollDice(10);
		
		$damage += $this->getModifier($attacker_data['strength']);
		
		if ($damage < 0) {
			$damage = 0;
		}
		
		if (($attack == 20) || (($attacker == 1) && $attack >= 16)) {
		
			$damage *= 2;
			$defender_data['health'] = $defender_data['health'] - $damage;
			$outcome = "and scores a critical hit, dealing $damage damage! (".$defender_data['health']." health remaining.)";

		} else {
		
			$defense += $this->getModifier($defender_data['dexterity']);
			$attack += $this->getModifier($attacker_data['dexterity']);
	
			if (($attack > $defense)) {
				$defender_data['health'] = $defender_data['health'] - $damage;			
				$outcome = "and hits, dealing $damage damage. (".$defender_data['health']." health remaining.)";		
			} else {
				$outcome = "but misses.";
			}
		}
		
		$return['outcome'] = $outcome;
		$return['damage'] = $damage;

		return $return;
		
	}
	
	function damage($id,$amount) {
		global $config,$user,$db;
		$sql = "UPDATE rpg_characters_stats SET health = health - ".$amount." WHERE character_id = ".$id;
		
		if (!$db->sql_query($sql)) {
			return false;
		} else {
			return true;
		}
	}
	
	function revive($id) {
		global $config,$user,$db;	
		$sql = "UPDATE rpg_characters_stats SET health = healthMax WHERE character_id = ".$id;
		
		if (!$db->sql_query($sql)) {
			return false;
		} else {
			return true;
		}
	}
	
	function spawn($id) {
		global $config,$user,$db;
							
		$sql = "INSERT INTO rpg_characters_stats (character_id) VALUES (".$id.")";

		if (!$db->sql_query($sql)) {
			return false;
		} else {
			return true;
		}
	}
	
	function returningDirection($direction) {
		switch ($direction) {
			case 'north':
				$return = 'south';
			break;
			case 'south':
				$return = 'north';
			break;
			case 'east':
				$return = 'west';
			break;
			case 'west':
				$return = 'east';
			break;
			case 'up':
				$return = 'down';
			break;
			case 'down':
				$return = 'up';
			break;
		}
		return $return;
	}
	
	function getModifier($score) {
		switch ($score) {
			case ($score >= 1):
				$modifier = -5;
			break;
			case ($score >= 2):
				$modifier = -4;
			break;
			case ($score >= 4):
				$modifier = -3;
			break;
			case ($score >= 6):
				$modifier = -2;
			break;
			case ($score >= 8):
				$modifier = -1;
			break;
			case ($score >= 10):
				$modifier = 0;
			break;
			case ($score >= 12):
				$modifier = 1;
			break;
			case ($score >= 14):
				$modifier = 2;
			break;
			case ($score >= 16):
				$modifier = 3;
			break;
			case ($score >= 18):
				$modifier = 4;
			break;
			case ($score >= 20):
				$modifier = 5;
			break;
			case ($score >= 22):
				$modifier = 6;
			break;
			case ($score >= 24):
				$modifier = 7;
			break;
			case ($score >= 26):
				$modifier = 8;
			break;
			case ($score >= 28):
				$modifier = 9;
			break;
			case ($score >= 30):
				$modifier = 10;
			break;
			default:
				$modifier = 0;
			break;
		}
		
		return $modifier;
	}
}

?>