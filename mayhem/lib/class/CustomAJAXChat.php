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

ini_set('display_errors',false);
error_reporting(0);
 
class CustomAJAXChat extends AJAXChat {

	function initMessageHandling() {
		// Don't handle messages if we are not in chat view:
		if($this->getView() != ('chat' || 'roleplay')) {
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
	
	function startSession() {	
		if(!session_id()) {
			// Set the session name:
			if ($roleplayID = $this->getRequestVar('roleplayID')) {
				session_name($this->getConfig('sessionName').'_r'.$roleplayID);
			} else {
				session_name($this->getConfig('sessionName'));
			}
			
			// Set session cookie parameters:
			session_set_cookie_params(
				0, // The session is destroyed on logout anyway, so no use to set this
				$this->getConfig('sessionCookiePath'),
				$this->getConfig('sessionCookieDomain'),
				$this->getConfig('sessionCookieSecure')
			);

			// Start the session:
			session_start();
			
			// We started a new session:
			$this->_sessionNew = true;
		}
	}	

	// Initialize custom configuration settings
	function initCustomConfig() {
		global $db;
		
		if ($roleplayID = $this->getRequestVar('roleplayID')) {
			$this->setConfig('sessionName', session_name($this->getConfig('sessionName').'_r'.$roleplayID));
		}	
		
		// Use the existing phpBB database connection:
		$this->setConfig('dbConnection', 'link', $db->db_connect_id);
		
		
		//$this->setConfig('');
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
	
	function initSession() {
		// Start the PHP session (if not already started):
		$this->startSession();
		
	//	if (!$this->getRequestVar('roleplayID')) {
		//if (($this->getSessionIP() == '12.107.192.178') && (!$this->isLoggedIn())) {
			return;
	//	} else {

			if($this->isLoggedIn()) {
				// Logout if we receive a logout request, the chat has been closed or the userID could not be revalidated:
				if($this->getRequestVar('logout') || !$this->isChatOpen() || !$this->revalidateUserID()) {
					$this->logout();
					return;
				}
				// Logout if the Session IP is not the same when logged in and ipCheck is enabled:
				if($this->getConfig('ipCheck') && ($this->getSessionIP() === null || $this->getSessionIP() != $_SERVER['REMOTE_ADDR'])) {
					$this->logout('IP');
					return;
				}
			} else if(
				// Login if auto-login enabled or a login, userName or shoutbox parameter is given:
				$this->getConfig('forceAutoLogin') ||
				$this->getRequestVar('login') ||
				$this->getRequestVar('userName') ||
				$this->getRequestVar('shoutbox')
				) {
				$this->login();
			}
	//	}
		
		
		// Initialize the view:
		$this->initView();

		if($this->getView() == 'chat') {
			$this->initChatViewSession();
		} else if($this->getView() == 'logs') {
			$this->initLogsViewSession();
		}

		if(!$this->getRequestVar('ajax') && !headers_sent()) {
			// Set style cookie:
			$this->setStyle();
			// Set langCode cookie:
			$this->setLangCodeCookie();
		}
		
		$this->initCustomSession();
	}
	
	function initChannel() {
	
		if(($this->getUserRole() == AJAX_CHAT_ADMIN) || ($this->getUserRole() == AJAX_CHAT_MODERATOR)) {
			$this->setConfig('requestMessagesPriorChannelEnter', true);
			$this->setConfig('requestMessagesLimit', 50);		
		}
	
		$channelID = $this->getRequestVar('channelID');
		$channelName = $this->getRequestVar('channelName');
		if($channelID !== null) {
			$this->switchChannel($this->getChannelNameFromChannelID($channelID));			
		} else if($channelName !== null) {
			if($this->getChannelIDFromChannelName($channelName) === null) {
				// channelName might need encoding conversion:
				$channelName = $this->trimChannelName($channelName, $this->getConfig('contentEncoding'));
			}		
			$this->switchChannel($channelName);	
		}
	}	
	
	function login() {
		// Retrieve valid login user data (from request variables or session data):
		$userData = $this->getValidLoginUserData();
		
		if(!$userData) {
			$this->addInfoMessage('errorInvalidUser');
			return false;
		}

		// If the chat is closed, only the admin may login:
		if(!$this->isChatOpen() && $userData['userRole'] != AJAX_CHAT_ADMIN) {
			$this->addInfoMessage('errorChatClosed');
			return false;
		}
		
		if(!$this->getConfig('allowGuestLogins') && $userData['userRole'] == AJAX_CHAT_GUEST) {
			return false;
		}

		// Check if userID or userName are already listed online:
		if($this->isUserOnline($userData['userID']) || $this->isUserNameInUse($userData['userName'])) {
			if($userData['userRole'] == AJAX_CHAT_USER || $userData['userRole'] == AJAX_CHAT_MODERATOR || $userData['userRole'] == AJAX_CHAT_ADMIN) {
				// Set the registered user inactive and remove the inactive users so the user can be logged in again:
				//$this->setInactive($userData['userID'], $userData['userName']);
				$this->removeInactive();
			} else {
				$this->addInfoMessage('errorUserInUse');
				return false;
			}
		}
		
		// Check if user is banned:
		if($userData['userRole'] != AJAX_CHAT_ADMIN && $this->isUserBanned($userData['userName'], $userData['userID'], $_SERVER['REMOTE_ADDR'])) {
			$this->addInfoMessage('errorBanned');
			return false;
		}
		
		// Check if the max number of users is logged in (not affecting moderators or admins):
		if(!($userData['userRole'] == AJAX_CHAT_MODERATOR || $userData['userRole'] == AJAX_CHAT_ADMIN) && $this->isMaxUsersLoggedIn()) {
			$this->addInfoMessage('errorMaxUsersLoggedIn');
			return false;
		}

		// Use a new session id (if session has been started by the chat):
		$this->regenerateSessionID();

		// Log in:
		$this->setUserID($userData['userID']);
		$this->setUserName($userData['userName']);
		$this->setLoginUserName($userData['userName']);
		$this->setUserRole($userData['userRole']);
		$this->setLoggedIn(true);	
		$this->setLoginTimeStamp(time());

		// IP Security check variable:
		$this->setSessionIP($_SERVER['REMOTE_ADDR']);

		// The client authenticates to the socket server using a socketRegistrationID:
		if($this->getConfig('socketServerEnabled')) {
			$this->setSocketRegistrationID(
				md5(uniqid(rand(), true))
			);
		}

		// Add userID, userName and userRole to info messages:
		$this->addInfoMessage($this->getUserID(), 'userID');
		$this->addInfoMessage($this->getUserName(), 'userName');
		$this->addInfoMessage($this->getUserRole(), 'userRole');

		// Purge logs:
		if($this->getConfig('logsPurgeLogs')) {
			$this->purgeLogs();
		}

		return true;
	}

	function removeInactive() {
		$sql = 'SELECT
					userID,
					userName,
					channel
				FROM
					'.$this->getDataBaseTable('online').'
				WHERE
					NOW() > DATE_ADD(dateTime, interval '.$this->getConfig('inactiveTimeout').' MINUTE);';
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			echo $result->getError();
			die();
		}
		
		if($result->numRows() > 0) {
			$condition = '';
			while($row = $result->fetch()) {
				if(!empty($condition))
					$condition .= ' OR ';
				// Add userID to condition for removal:
				$condition .= 'userID='.$this->db->makeSafe($row['userID']);

				// Update the socket server authentication for the kicked user:
				if($this->getConfig('socketServerEnabled')) {
					$this->updateSocketAuthentication($row['userID']);
				}

				$this->removeUserFromOnlineUsersData($row['userID']);
				
				// Insert logout timeout message:
				/* $text = '/logout '.$row['userName'].' Timeout';
				$this->insertChatBotMessage(
					$row['channel'],
					$text,
					null,
					1
				); */ 
			}
			
			$result->free();
			
			$sql = 'DELETE FROM
						'.$this->getDataBaseTable('online').'
					WHERE
						'.$condition.';';
			
			// Create a new SQL query:
			$result = $this->db->sqlQuery($sql);
			
			// Stop if an error occurs:
			if($result->error()) {
				echo $result->getError();
				die();
			}
		}
	}

	
	function getMessageCondition() {
		$condition = '';
				

		

			if ($this->getRequestVar('lastID') > 0) {
				$condition .= 	'
					id > '.$this->db->makeSafe($this->getRequestVar('lastID')).'
					AND
					(
						(
							(
								channel = '.$this->db->makeSafe($this->getChannel()).'
								OR
								channel = '.$this->db->makeSafe($this->getPrivateMessageID()).'
								OR
								(
									roleplayID = '.$this->getSessionVar('roleplayID').'
										AND
									text LIKE "/ooc %"
								)
							)
							AND
							(
								roleplayID IS NULL
								OR 
								roleplayID = '.$this->getSessionVar('roleplayID').'
								OR
									channel = 0
							)
						
						
						)
						
						OR
						(
							roleplayID = '.$this->getSessionVar('roleplayID').'
							AND
								text LIKE "/ooc %"
						)
					
					)
					AND userName <> "HAL"
					';		
			} else {
				$condition .= 	'
				
					dateTime >= "'.date('Y-m-d H:i:s', strtotime('-24 hours')).'"
					
					AND
					(
					(
					(
						channel = '.$this->db->makeSafe($this->getChannel()).'
						OR
						channel = '.$this->db->makeSafe($this->getPrivateMessageID()).'
						OR
						(
							roleplayID = '.$this->getSessionVar('roleplayID').'
								AND
							text LIKE "/ooc %"
						)
					)
					AND
					(
						roleplayID IS NULL
						OR 
						roleplayID = '.$this->getSessionVar('roleplayID').'
						OR
							channel = 0
					)
					
					
					)
					
					OR
						(roleplayID = '.$this->getSessionVar('roleplayID').'
							AND
						text LIKE "/ooc %")
					
					)
					AND userName <> "HAL"
					';
			}		

		return $condition;
	}
	
	function initRequestVars() {
		$this->_requestVars = array();
		$this->_requestVars['ajax']			= isset($_REQUEST['ajax'])			? true							: false;
		$this->_requestVars['userID']		= isset($_REQUEST['userID'])		? (int)$_REQUEST['userID']		: null;
		$this->_requestVars['userName']		= isset($_REQUEST['userName'])		? $_REQUEST['userName']			: null;
		$this->_requestVars['channelID']	= isset($_REQUEST['channelID'])		? (int)$_REQUEST['channelID']	: null;
		$this->_requestVars['roleplayID']	= isset($_REQUEST['roleplayID'])	? (int)$_REQUEST['roleplayID']	: null;
		$this->_requestVars['channelName']	= isset($_REQUEST['channelName'])	? $_REQUEST['channelName']		: null;
		$this->_requestVars['text']			= isset($_REQUEST['text'])			? $_REQUEST['text']				: null;
		$this->_requestVars['lastID']		= isset($_REQUEST['lastID'])		? (int)$_REQUEST['lastID']		: 0;
		$this->_requestVars['login']		= isset($_REQUEST['login'])			? true							: false;
		$this->_requestVars['logout']		= isset($_REQUEST['logout'])		? true							: false;
		$this->_requestVars['password']		= isset($_REQUEST['password'])		? $_REQUEST['password']			: null;
		$this->_requestVars['view']			= isset($_REQUEST['view'])			? $_REQUEST['view']				: null;
		$this->_requestVars['year']			= isset($_REQUEST['year'])			? (int)$_REQUEST['year']		: null;
		$this->_requestVars['month']		= isset($_REQUEST['month'])			? (int)$_REQUEST['month']		: null;
		$this->_requestVars['day']			= isset($_REQUEST['day'])			? (int)$_REQUEST['day']			: null;
		$this->_requestVars['hour']			= isset($_REQUEST['hour'])			? (int)$_REQUEST['hour']		: null;
		$this->_requestVars['search']		= isset($_REQUEST['search'])		? $_REQUEST['search']			: null;
		$this->_requestVars['shoutbox']		= isset($_REQUEST['shoutbox'])		? true							: false;
		$this->_requestVars['getInfos']		= isset($_REQUEST['getInfos'])		? $_REQUEST['getInfos']			: null;
		$this->_requestVars['lang']			= isset($_REQUEST['lang'])			? $_REQUEST['lang']				: null;
		$this->_requestVars['delete']		= isset($_REQUEST['delete'])		? (int)$_REQUEST['delete']		: null;
		
		// Initialize custom request variables:
		$this->initCustomRequestVars();
		
		// Remove slashes which have been added to user input strings if magic_quotes_gpc is On:
		if(get_magic_quotes_gpc()) {
			// It is safe to remove the slashes as we escape user data ourself
			array_walk(
				$this->_requestVars,
				create_function(
					'&$value, $key',
					'if(is_string($value)) $value = stripslashes($value);'
				)
			);
		}
	}
	
	// Override to add custom session code right after the session has been started:
	function initCustomSession() {
	
		// TODO: Check why the chat actually works even though this IF statement is broken 
		// It should probably have two = signs...?
		if($roleplayID = $this->getRequestVar('roleplayID')) {
			$this->setSessionVar('roleplayID',$roleplayID);
		} else {
			// $this->setSessionVar('roleplayID',1);
		}
		
		if (!$this->getSessionVar('roleplayID')) {
			if ($this->getUserID() == 4) {
				$this->setSessionVar('roleplayID', null);
			} else {
				$this->setSessionVar('roleplayID', 1);
			}
		}		
	}
	
	function getTemplateFileName() {
	
		switch($this->getView()) {
			case 'chat':
				return AJAX_CHAT_PATH.'lib/template/shoutbox.html';
			case 'logs':
				return AJAX_CHAT_PATH.'lib/template/logs.html';
			case 'roleplay':
				return AJAX_CHAT_PATH.'lib/template/loggedIn.html';
			default:
				return AJAX_CHAT_PATH.'lib/template/loggedOut.html';
		}
	}

	function hasAccessTo($view) {
		switch($view) {
			case 'chat':
			case 'teaser':
			
			
				return true;
			
				if($this->isLoggedIn()) {
					return true;	
				}
				return false;
			case 'logs':
				if($this->isLoggedIn() && ($this->getUserRole() == AJAX_CHAT_ADMIN ||
					($this->getConfig('logsUserAccess') &&
					($this->getUserRole() == AJAX_CHAT_MODERATOR || $this->getUserRole() == AJAX_CHAT_USER))
					)) {
					return true;
				}
				return false;
			default:
				return false;
		}
	}

	function getBannedUsersData($key=null, $value=null) {
		if($this->_bannedUsersData === null) {
			$this->_bannedUsersData = array();

			$sql = 'SELECT
						userID,
						userName,
						ip,
						type
					FROM
						'.$this->getDataBaseTable('bans').'
					WHERE
						NOW() < dateTime;';
			
			// Create a new SQL query:
			$result = $this->db->sqlQuery($sql);
			
			// Stop if an error occurs:
			if($result->error()) {
				echo $result->getError();
				die();
			}
			
			while($row = $result->fetch()) {
				$row['ip'] = $this->ipFromStorageFormat($row['ip']);
				array_push($this->_bannedUsersData, $row);
			}
			
			$result->free();
		}
		
		if($key) {
			$bannedUsersData = array();		
			foreach($this->_bannedUsersData as $bannedUserData) {
				if(!isset($bannedUserData[$key])) {
					return $bannedUsersData;
				}
				if($value) {
					if($bannedUserData[$key] == $value) {
						array_push($bannedUsersData, $bannedUserData);
					} else {
						continue;
					}
				} else {
					array_push($bannedUsersData, $bannedUserData[$key]);	
				}
			}
			return $bannedUsersData;
		}
		
		return $this->_bannedUsersData;
	}
	
	function isUserBanned($userName, $userID=null, $ip=null) {
		if($userID !== null) {
			$bannedUserDataArray = $this->getBannedUsersData('userID',$userID);
			if($bannedUserDataArray && isset($bannedUserDataArray[0]) && ($bannedUserDataArray['type'] != 'Mute')) {
				return true;
			}
		}
		if($ip !== null) {
			$bannedUserDataArray = $this->getBannedUsersData('ip',$ip);
			if($bannedUserDataArray && isset($bannedUserDataArray[0]) && ($bannedUserDataArray['type'] != 'Mute')) {
				return true;
			}
		}
		$bannedUserDataArray = $this->getBannedUsersData('userName',$userName);
		if($bannedUserDataArray && isset($bannedUserDataArray[0]) && ($bannedUserDataArray['type'] != 'Mute')) {
			return true;
		}	
		return false;
	}
	
	function isUserMuted($userID) {
		$sql = 'SELECT userID FROM ajax_chat_bans WHERE userID = '.(int) $userID .' AND type = "Mute"';
		
		$result = $this->db->sqlQuery($sql);
		$row = $result->fetch();
		$result->free();

		if ($row['userID'] == $userID) {
			return true;
		} else {
			return false;
		}
		
	}
	
	// Replace custom template tags:
	function replaceCustomTemplateTags($tag, $tagContent) {
		global $config,$user,$db;
		
		$RPG = new RPG();
		$RPG->ajaxChat = $this;
		$this->RPG = $RPG;	
	
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
				
			case 'VIEW_LIST':

				$viewList .= '<div id="viewListToggler" class="ui-state-default" onclick="ajaxChat.toggleViewList();">&laquo; collapse</div><div id="viewListContainer">';
				
				$sql = 'SELECT DISTINCT roleplay_id FROM rpg_characters c WHERE roleplay_id = '.(int) $this->getSessionVar('roleplayID').' AND owner = '.$this->getUserID(). '';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result)) {
				
					$characters[] = $row;					
				}
				$db->sql_freeresult($result);
				
				if ((count($characters) == 0) && $user->data['is_registered'] ) {
					$viewList .= '<script type="text/javascript">
					
						$("#tutorialNewUser_1").dialog({
							open: function(event, ui) {$("#tutorialCharacterNameInput").focus();},
							position: ["top","center"],
							title: "OMG, PANIC",
							width: 450,
							height: 350,
							buttons: {
								"Alright, let\'s do that.": function () {self.parent.location.href = "http://www.roleplaygateway.com/roleplay/'.$this->RPG->getRoleplayURL($this->getSessionVar('roleplayID')).'/#join"},
								"Not right now.": function () {$("#tutorialNewUser_1").dialog("close");}
							},
							modal: true,
							zIndex: 99999
						});
						
					</script>';
				} elseif ((count($characters) == 0)) {
					$viewList .= '<p style="padding:10px; padding-left:30px;">This would be your character list, if you were registered. :)</p>';
				
				}else {
					
					foreach ($characters as $row) {
					
						$viewList .= '
						<div class="viewListRoleplay ui-state-default">
							<div id="viewList_channel_ooc" class="viewListRoleplayOOC" onclick="ajaxChat.sendMessage(\'/ooc\'); ajaxChat.switchRoleplayByID('.$row['roleplay_id'].'); ">
								<img src="http://www.roleplaygateway.com/images/roleplay_thumbnail.php?roleplay_id='.$row['roleplay_id'].'" title="'.$this->htmlEncode($this->RPG->getRoleplayName($row['roleplay_id'])).'\'s OOC chat..." />
								<h4 style="margin:0px;">'.$this->htmlEncode($this->RPG->getRoleplayName($row['roleplay_id'])).'</h4>
								<span>Out Of Character</span>
							</div>
							<div class="viewListCharactersContainer">
						';
						
						$sql = 'SELECT id,name,url,location FROM rpg_characters WHERE owner = "'.$user->data['user_id'].'" AND roleplay_id = "'.$row['roleplay_id'].'"';
						$characterResult = $db->sql_query($sql);
						while ($character = $db->sql_fetchrow($characterResult)) {
						
							$place = $this->RPG->getPlaceData($character['location']);
						
							$viewList .= '
							<div class="viewListCharacter" id="characterSelect_'.$character['id'].'" onclick="ajaxChat.switchRoleplayByID('.$row['roleplay_id'].'); ajaxChat.switchCharacterByID('.$character['id'].');">
								<img src="http://www.roleplaygateway.com/images/character_avatar.php?character_id='.$character['id'].'" title="'.$this->htmlEncode($character['name']).'\'s persepective..." />
								<h4 style="margin:0px;">'.$this->htmlEncode(substr($character['name'],0,22)).'</h4>
								<span>'.$this->htmlEncode(substr($place['name'],0,22)).'</span>
							</div>';
						}
						
						$viewList .= '	</div>
						</div>';				
					}
				}
				
				$viewList .= '
				<div class="viewListCharacter ui-state-default" onclick="self.parent.location.href=\'http://www.roleplaygateway.com/roleplay/'.$this->RPG->getRoleplayURL($this->getSessionVar('roleplayID')).'/#join\';">
					<img src="http://www.roleplaygateway.com/images/plus.gif" />
					<h4 style="margin:0px;">Add New &raquo;</h4>
					<span>New Character</span>
				</div>';
				
				$viewList .= '</div>';

				
				return $viewList;

			
			case 'PLACES_SELECTION':

				$placeName = ($this->getChannel() == 0) ? 'Places in &ldquo;'.$this->htmlEncode($this->RPG->getRoleplayName($this->getSessionVar('roleplayID'))).' &rdquo; (click!) &raquo;' : $this->getChannelName();
				
				
				$places_selection = '<div id="placeCurrent" class="ui-state-default" onclick="openModal(\'placesControlContainer\');" id="placeIndicator">'.$placeName.'</div><div id="placesControlContainer" style="display:none;">
					<h3>Places in &ldquo;'.$this->RPG->getRoleplayName($this->getSessionVar('roleplayID')).'&rdquo;:</h3>
					<div id="placesList">
					';
					
					

					$places = $this->RPG->getPlaces($this->getSessionVar('roleplayID'), 100);
					
					foreach ($places as $place) {
						$onlineUsers[] = number_format($place['onlineUsers']);
						$lastMessage[] = number_format($place['lastMessage']);
					}
					
					array_multisort($onlineUsers, SORT_DESC, $lastMessage, SORT_DESC, $places);
					
					foreach ($places as $place) {
					
						$synopsisLimit = 255;
					
						$place['synopsis'] = (strlen($place['synopsis']) > $synopsisLimit) ? substr($place['synopsis'],0,$synopsisLimit) . '[...]' : $place['synopsis'];
					
						$places_selection .= '<div style="clear:both;" class="place ui-state-default" onclick="ajaxChat.switchChannelByID('.$place['id'].');">
						<div style="float:right;">
							'. number_format($place['onlineUsers']) .'
						</div>
						<img style="float:left;" src="http://www.roleplaygateway.com/images/places_thumbnail.php?place_id='.$place['id'].'" /><strong>'.$place['name'].'</strong><p id="placeSynopsis_'.$place['id'].'">'. $place['synopsis'] .'</p></div>';
					}
				
				$places_selection .= '</div></div>';
				
				return $places_selection;
			break;				
			case 'ROLEPLAY_OPTIONS':
				if ($this->getUserID() <> 4) return '';
				
				
				$places_selection = '<div onclick="openModal(\'roleplayControlContainer\');">ohai!</div><div id="roleplayControlContainer" style="display:none;">
					<h3>Your Roleplays:</h3>
					';
					
					$sql = 'SELECT DISTINCT roleplay_id FROM rpg_characters c WHERE owner = '.$this->getUserID();
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result)) {
					
						$places_selection .= '<div class="characterSelectionOption ui-state-default" onclick="window.location = \'http://www.roleplaygateway.com/embed/?roleplayID='.$row['roleplay_id'].'\';">'.$this->RPG->getRoleplayName($row['roleplay_id']).'
							<div class="placesContainer">
						';
						
						$places_selection .= '</div></div>';
						
					}
				
				$places_selection .= '</div>';
				
				return $places_selection;
			break;
			case 'CHANNEL_SELECTION':
			
				$channel_selection = '<select style="max-width:200px;" onchange="ajaxChat.switchChannelByID(this.options[this.selectedIndex].value);">';
				$channel_selection .= '<optgroup label="OOC Channels:">';
				$channel_selection .= '<option value="0">Out Of Character (OOC)</option>';
				
				if($this->isLoggedIn() && $this->isAllowedToCreatePrivateChannel()) {
					// Add the private channel of the user to the options list:
					if(!$channelSelected && $this->getPrivateChannelID() == $this->getChannel()) {
						$selected = ' selected="selected"';
						$channelSelected = true;
					} else {
						$selected = '';
					}
					$privateChannelName = $this->getPrivateChannelName();
					$channel_selection .= '<option value="'.$this->getPrivateChannelID().'"'.$selected.'>'.$this->htmlEncode($privateChannelName).'</option>';
				}				
				
				
				$channel_selection .= '</optgroup>';
				$channel_selection .= '</select>';
			
				return $channel_selection;
			break;
			
			case 'CHARACTER_SELECTION':
			
				$roleplay_name = $this->RPG->getRoleplayName($this->getSessionVar('roleplayID'));
			
				$sql = "SELECT c.name,c.id,r.url,r.title FROM rpg_characters c
							INNER JOIN rpg_roleplays r
								ON r.id = c.roleplay_id
							WHERE c.owner = ".$this->getUserID()." AND c.roleplay_id = ".$this->getSessionVar('roleplayID') ." ";
				$result = $db->sql_query($sql);
				$characterOptions = '<div class="characterSelectionOption ui-state-default" onclick="ajaxChat.sendMessageWrapper(\'/ooc\'); ajaxChat.exitCharacter();">Out Of Character (OOC)</div>';
				$characterOptions .= '<h3>Your Characters in &ldquo;'.$roleplay_name.'&rdquo;:</h3>';
				while ($row = $db->sql_fetchrow($result)) {
					$characterOptions .= '<div class="characterSelectionOption ui-state-default" onclick="ajaxChat.switchCharacter(\''.$row['name'].'\','.$row['id'].');"><img src="http://www.roleplaygateway.com/images/character_avatar.php?character_id=' . $row['id'] . '" />'.$this->htmlEncode($row['name']).'</div>';
				}
				
				$characterOptions .= '<div class="characterSelectionOption ui-state-default" style="text-align:right; font-weight: bold;" onclick="window.open(\'http://www.roleplaygateway.com/roleplay/'.$this->RPG->urlify($roleplay_name).'/#join\');">Create New Character &raquo;</div>';
				$db->sql_freeresult($result);				
				
				return $characterOptions;
				
			case 'ROLEPLAY_SELECTION':
			
				if ($user->data['user_id'] > 1) {
				
				
					$sql = 'SELECT title,id,description,url FROM rpg_roleplays where id = 1 LIMIT 1';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result)) {
						$roleplays[$row['id']] = $row;
						//$roleplays[] = '<div onclick="handleLogin(); window.location = \'http://chat.roleplaygateway.com/embed/?roleplayID='.$row['id'].'\';" style="background:url(\'http://www.roleplaygateway.com/images/roleplay_thumbnail.php?roleplay_id='.$row['id'].'\') no-repeat;"><h1>'.$row['title'].'</h1><p>'.$row['description'].'</p></div>';
					}
					
					$sql = 'SELECT title,id,description,url FROM rpg_roleplays WHERE id IN (SELECT roleplay_id FROM rpg_characters WHERE owner = '.(int) $user->data['user_id'].' AND roleplay_id <> 1) AND status = "Open" LIMIT 10';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result)) {
						$roleplays[$row['id']] = $row;
						//$roleplays[] = '<li onclick="handleLogin(); window.location = \'http://chat.roleplaygateway.com/embed/?roleplayID='.$row['id'].'\';" style="background:url(\'http://www.roleplaygateway.com/images/roleplay_thumbnail.php?roleplay_id='.$row['id'].'\') no-repeat;"><h1>'.$row['title'].'</h1><p>'.$row['description'].'</p></li>';
					}
					
					$db->sql_freeresult($result);

					// print_r($roleplays);

					$roleplayOptions = '<ul>';
					
					foreach ($roleplays as $roleplay) {
						// Ingore the Multiverse
						// if ($roleplay['id'] == 1) continue;
					
						$roleplayOptions .= '<li onclick="window.parent.location = \'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/#chat\';"><img src="http://www.roleplaygateway.com/images/roleplay_thumbnail.php?roleplay_id='.$roleplay['id'].'" /><h1>'.$roleplay['title'].'</h1><p>'.$roleplay['description'].'</p><span class="stats">'.$this->RPG->showRoleplayStats($roleplay['id']).'</span></li>';
					
					}
					
					$roleplayOptions .= '</ul>';

			
			/* 		
					
					
					
					if (count($roleplays) >= 1) {
						$roleplayOptions = implode("\n",$roleplays).'	<script type="text/javascript">
			$("#roleplays").easySlider({
			auto: true, 
			continuous: true,
			pause: 7500,
			numeric: true
		});
		</script>';
					} elseif (count($roleplayOptions < 1)) {
						// HAX
						$roleplayOptions = 'You aren\'t part of any roleplays. <a href="http://www.roleplaygateway.com/roleplay/">Join a Roleplay &raquo;</a>';
						
					} else {
						$roleplayOptions = implode("\n",$roleplays);
					}
		 */
					
					return $roleplayOptions;
				} else {
					return '<strong>You are not logged in!</strong>';
				}
				

			default:
				return null;
		}
	}	
	
	function chatViewLogin() {
		$this->setChannel($this->getValidRequestChannelID(),$this->getSessionVar('roleplayID'));
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
		
		// After checking iniCustomSession and finding that didn't work, I did this:
		$this->setSessionVar('roleplayID', $this->_requestVars['roleplayID']);

		// Channel description and entrance
		//$this->insertChatBotMessage(
		//	$this->getPrivateMessageID(),
		//	$this->RPG->getChannelDescription($this->getChannel())
		//);		
		
	}
	
	function chatViewLogout($type) {
		$this->removeFromOnlineList();
		if($type !== null) {
			$type = ' '.$type;
		}
		
		if (strlen($channel = $this->getChannel()) > 0) {
			// Logout message
			$text = '/logout '.$this->getUserName().$type;
			$this->insertChatBotMessage(
				$channel,
				$text,
				null,
				1
			);
		} 
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

	function &getCustomChannels() {
		global $config,$user,$db;

		//$sql = 'SELECT * FROM rpg_places';
		//$sql = 'SELECT * FROM rpg_places WHERE (roleplay_id = '. $this->getSessionVar('roleplayID') . ' OR id = 0) AND length(name) > 1 AND visible = true ORDER BY parent_id ASC, name ASC';
		$sql = 'SELECT id,name
				FROM rpg_places p
					WHERE p.roleplay_id = "'.$this->getSessionVar('roleplayID').'"
						ORDER BY id ASC';

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			$channels[$row['id']] = $row['name'];
		}
		$db->sql_freeresult($result);
		
		return $channels;
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
	
	function getChannelOptionTags() {
		$channelOptions = '';
		$channelSelected = false;
		foreach($this->ajaxChat->getChannels() as $key=>$value) {
			if($this->ajaxChat->isLoggedIn()) {
				$selected = ($value == $this->ajaxChat->getChannel()) ? ' selected="selected"' : '';
			} else {
				$selected = ($value == $this->ajaxChat->getConfig('defaultChannelID')) ? ' selected="selected"' : '';
			}
			if($selected) {
				$channelSelected = true;
			}
			$channelOptions .= '<option value="'.$this->ajaxChat->htmlEncode($key).'"'.$selected.'>'.$this->ajaxChat->htmlEncode($key).'</option>';
		}
		if($this->ajaxChat->isLoggedIn() && $this->ajaxChat->isAllowedToCreatePrivateChannel()) {
			// Add the private channel of the user to the options list:
			if(!$channelSelected && $this->ajaxChat->getPrivateChannelID() == $this->ajaxChat->getChannel()) {
				$selected = ' selected="selected"';
				$channelSelected = true;
			} else {
				$selected = '';
			}
			$privateChannelName = $this->ajaxChat->getPrivateChannelName();
			$channelOptions .= '<option value="'.$this->ajaxChat->htmlEncode($privateChannelName).'"'.$selected.'>'.$this->ajaxChat->htmlEncode($privateChannelName).'</option>';
		}
		// If current channel is not in the list, try to retrieve the channelName:
		if(!$channelSelected) {
			$channelName = $this->ajaxChat->getChannelName();
			if($channelName !== null) {
				$channelOptions .= '<option value="'.$this->ajaxChat->htmlEncode($channelName).'" selected="selected">'.$this->ajaxChat->htmlEncode($channelName).'</option>';
			} else {
				// Show an empty selection:
				$channelOptions .= '<option value="" selected="selected">---</option>';
			}
		}
		return $channelOptions;
	}	
	
	function getNameFromID($userID) {

		$userDataArray = $this->getOnlineUsersData(null,'userID',$userID);
		if($userDataArray && isset($userDataArray[0])) {
			return $userDataArray[0]['userName'];
		}
		
		global $user,$db;
		
		
		$sql = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $userID;
		$result = $db->sql_query($sql);
		$username = $db->sql_fetchfield('username');
		$db->sql_freeresult($result);
		return $username;
		
		return null;
	}	
	
	function validateChannel($channelID) {
		if($channelID === null) {
			return false;
		}
		
		// Return true for normal channels the user has acces to:
		if(in_array($channelID, $this->getChannels())) {
			return true;
		}
		// Return true if the user is allowed to join his own private channel:
		if($channelID == $this->getPrivateChannelID() && $this->isAllowedToCreatePrivateChannel()) {
			return true;
		}
		// Return true if the user has been invited to a restricted or private channel:
		if(in_array($channelID, $this->getInvitations())) {
			return true;	
		}
		// No valid channel, return false:
		return false;
	}
	
	function trimChannelName($channelName) {		
		return $this->trimString($channelName, null, null, false, true);
	}	

	function switchChannel($channelName = null,$channelID = null,$direction = null) {
		
		if ($channelName == null && $channelID = null) {
			return false;
		}
		
		if ($channelID == null) {
			$channelID = $this->getChannelIDFromChannelName($channelName);
		}
		
		if ($channelName == null) {
			$channelName == $this->getChannelNameFromChannelID($channelID);
		}
	
		if($channelID !== null && $this->getChannel() == $channelID) {
			// User is already in the given channel, return:
			return;
		}
		
 		if (($channelID == 0) and ($this->getSessionVar('InCharacter') == true)) {
/* 			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				'You cannot enter the real world while you are [b]In Character (IC)[/b]!'
			);	 */
			
			$this->addInfoMessage($this->getUserName(), 'userName');
			$this->setSessionVar('InCharacter', false);
			$this->setSessionVar('CharacterID', null);
			$this->setSessionVar('CharacterName', null);	
			
			return;
		}

		$oldChannel = $this->getChannel();		

		$this->setChannel($channelID,$this->getSessionVar('roleplayID'));
		$this->updateOnlineList();

		/*
		// Channel leave message
		$text = '/channelLeave '.$this->getUserName();
		$this->insertChatBotMessage(
			$oldChannel,
			$text,
			null,
			1
		);

		// Channel enter message
		$text = '/channelEnter '.$this->getUserName();
		$this->insertChatBotMessage(
			$this->getChannel(),
			$text,
			null,
			1
		);
		*/
		
		if ($direction) {
			if (($this->getSessionVar('InCharacter') == true) && ($direction != 'teleport')) {
				$this->insertChatBotMessage(
					$oldChannel,
					$this->RPG->getCharacterLink($this->getSessionVar('CharacterName'),$this->getSessionVar('roleplayID')) . " has [b]left[/b] the area, heading [b]".$direction."[/b] into ".$channelName."."
				);
			}

			if (($this->getSessionVar('InCharacter') == true) && ($direction != 'teleport')) {
				$this->insertChatBotMessage(
					$this->getChannel(),
					$this->RPG->getCharacterLink($this->getSessionVar('CharacterName'),$this->getSessionVar('roleplayID')) . " has [b]entered[/b] the area, coming from [b]".$this->getChannelNameFromChannelID($oldChannel)." (".$this->RPG->returningDirection($direction).")[/b]."
				);
			}
		}
		

/* 		if ($this->RPG->isPlace($channelID)) {
			// Channel description and entrance
			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				$this->RPG->getChannelDescription($channelID)
			);
		} */
		

		$this->addInfoMessage($channelName, 'channelSwitch');
		$this->addInfoMessage($channelID, 'channelID');
		// $this->addInfoMessage($this->RPG->getChannelDescription($channelID), 'channelSynopsis');
		
		$this->_requestVars['lastID'] = 0;
		
	}
	
	function setChannel($channel,$roleplay) {
	
		$RPG = new RPG();
		$RPG->ajaxChat = $this;
		$this->RPG = $RPG;
	
		if ($this->getSessionVar('InCharacter') == true) {
			$this->RPG->setLocation($channel,$this->getSessionVar('CharacterID'),$roleplay);
			
			/*
			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				$this->RPG->getChannelDescription($channel)
			);
				*/
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
	
	function updateOnlineList() {
	
		//$characterID 	= $this->getSessionVar('CharacterID');
		//$roleplayID 	= $this->getSessionVar('roleplayID');
		
		/*
		if ($this->getUserID() == 4) {
			$this->insertChatBotMessage($this->getPrivateMessageID(),"You are: r".$roleplayID." and c".$characterID);
		} 
		*/
		/*
		if ($this->getSessionVar('InCharacter') && ($this->getUserID() == 4)) {
		
			if ($characterID && $roleplayID) {
				$sql = 'UPDATE
							'.$this->getDataBaseTable('online').'
						SET
							userName 	= '.$this->db->makeSafe($this->getUserName()).',
							channel 	= '.$this->db->makeSafe($this->getChannel()).',
							characterID	= '.$this->db->makeSafe($characterID).',
							roleplayID	= '.$this->db->makeSafe($roleplayID).',
							dateTime 	= NOW(),
							ip			= '.$this->db->makeSafe($this->ipToStorageFormat($_SERVER['REMOTE_ADDR'])).'
						WHERE
							userID = '.$this->db->makeSafe($this->getUserID()).' AND
							roleplayID = '.$this->db->makeSafe($roleplayID).';';		
			} else {
				$sql = 'UPDATE
							'.$this->getDataBaseTable('online').'
						SET
							userName 	= '.$this->db->makeSafe($this->getUserName()).',
							channel 	= '.$this->db->makeSafe($this->getChannel()).',
							roleplayID	= '.$this->db->makeSafe($roleplayID).',
							dateTime 	= NOW(),
							ip			= '.$this->db->makeSafe($this->ipToStorageFormat($_SERVER['REMOTE_ADDR'])).'
						WHERE
							userID = '.$this->db->makeSafe($this->getUserID()).' AND
							roleplayID = '.$this->db->makeSafe($roleplayID).';';					
			}

		} else {
			$sql = 'UPDATE
						'.$this->getDataBaseTable('online').'
					SET
						userName 	= '.$this->db->makeSafe($this->getUserName()).',
						channel 	= '.$this->db->makeSafe($this->getChannel()).',
						roleplayID	= '.$this->db->makeSafe($roleplayID).',
						characterID	= null,
						dateTime 	= NOW(),
						ip			= '.$this->db->makeSafe($this->ipToStorageFormat($_SERVER['REMOTE_ADDR'])).'
					WHERE
						userID = '.$this->db->makeSafe($this->getUserID()).' AND
						roleplayID = '.$this->db->makeSafe($roleplayID).';';				
		}
			*/
			
		$sql = 'UPDATE
					'.$this->getDataBaseTable('online').'
				SET
					userName 	= '.$this->db->makeSafe($this->getUserName()).',
					channel 	= '.$this->db->makeSafe($this->getChannel()).',
					roleplayID 	= '.$this->db->makeSafe($this->getSessionVar('roleplayID')).',	
					characterID = '.(int) $this->db->makeSafe($this->getSessionVar('characterID')).',	
					dateTime 	= NOW(),
					ip			= '.$this->db->makeSafe($this->ipToStorageFormat($_SERVER['REMOTE_ADDR'])).'
				WHERE
					userID = '.$this->db->makeSafe($this->getUserID()).'
						;';
						
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			header('HTTP/1.1 503 Service Unavailable');
			echo $result->getError();
			die();
		}
		
		$this->resetOnlineUsersData();
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
	
	function getOnlineUsersData($channelIDs=null, $key=null, $value=null) {
		if($this->_onlineUsersData === null) {
			$this->_onlineUsersData = array();
			
			$sql = 'SELECT
						o.userID,
						o.userName,
						o.userRole,
						o.channel,
						o.characterID,
						o.roleplayID,
						UNIX_TIMESTAMP(o.dateTime) AS timeStamp,
						o.ip,
						u.user_warnings,
						u.user_notes,
						u.group_id
					FROM
						'.$this->getDataBaseTable('online').' o
						LEFT JOIN gateway_users u
							ON o.userID = u.user_id
					GROUP BY o.userID,o.channel
					ORDER BY
						o.userRole DESC, o.userName;';					
			
			// Create a new SQL query:
			$result = $this->db->sqlQuery($sql);

			// Stop if an error occurs:
			if($result->error()) {
				header('HTTP/1.1 503 Service Unavailable');
				echo $result->getError();
				die();
			}
			
			while($row = $result->fetch()) {
				$row['ip'] = $this->ipFromStorageFormat($row['ip']);
				array_push($this->_onlineUsersData, $row);
			}
			
			$result->free();
		}
		
		if($channelIDs || $key) {
			$onlineUsersData = array();		
			foreach($this->_onlineUsersData as $userData) {
				if($channelIDs && !in_array($userData['channel'], $channelIDs)) {
					continue;
				}
				if($key) {
					if(!isset($userData[$key])) {
						return $onlineUsersData;
					}
					if($value !== null) {
						if($userData[$key] == $value) {
							array_push($onlineUsersData, $userData);
						} else {
							continue;
						}
					} else {
						array_push($onlineUsersData, $userData[$key]);	
					}
				} else {
					array_push($onlineUsersData, $userData);
				}
			}
			return $onlineUsersData;
		}
		
		return $this->_onlineUsersData;
	}

	function getOnlineRoleplayers($roleplayID) {

			
		$sql = 'SELECT
					o.userID,
					o.userName,
					o.userRole,
					o.channel,
					o.characterID,
					o.roleplayID,
					UNIX_TIMESTAMP(o.dateTime) AS timeStamp,
					o.ip,
					u.user_warnings,
					u.user_notes,
					u.group_id
				FROM
					'.$this->getDataBaseTable('online').' o
					LEFT JOIN gateway_users u
						ON o.userID = u.user_id
				WHERE
					o.roleplayID = '.(int) $roleplayID.'
				GROUP BY o.userID, o.roleplayID
				ORDER BY
					o.userRole DESC, o.userName;';					
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);

		// Stop if an error occurs:
		if($result->error()) {
			header('HTTP/1.1 503 Service Unavailable');
			echo $result->getError();
			die();
		}
		
		while($row = $result->fetch()) {
			$row['ip'] = $this->ipFromStorageFormat($row['ip']);
			$roleplayers[] = $row;
		}
		
		$result->free();

		return $roleplayers;
	}
	
	function getChatViewOnlineUsersXML($channelIDs) {
		// Get the online users for the given channels:
		$onlineUsersData = $this->getOnlineUsersData($channelIDs);
		
		$showAllRoleplayers = true;
		
		
		
		if (($this->getUserID() == 4) || ($showAllRoleplayers == true)) {
			$onlineRoleplayers = $this->getOnlineRoleplayers($this->getSessionVar('roleplayID'));
			
			
			$xml = '<users>';
			foreach($onlineRoleplayers as $onlineRoleplayerData) {
				$xml .= '<user';
				$xml .= ' userID="'.$onlineRoleplayerData['userID'].'"';
				$xml .= ' userRole="'.$onlineRoleplayerData['userRole'].'"';

				
				if (in_array($onlineRoleplayerData['userID'], $this->RPG->getGameMasters($this->getSessionVar('roleplayID')))) {
					$xml .= ' isGameMaster="1"';
				} else {
					$xml .= ' isGameMaster="0"';
				}
				
				$xml .= ' channelID="'.$onlineRoleplayerData['channel'].'"';
				$xml .= ' roleplayID="'.$onlineRoleplayerData['roleplayID'].'"';
				$xml .= ' characterID="'.$onlineRoleplayerData['characterID'].'"';
				$xml .= ' groupID="'.$onlineRoleplayerData['group_id'].'"';
				
				// Staff Only
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$xml .= ' warnings="'.number_format($onlineRoleplayerData['user_warnings']).'"';
					$xml .= ' notes="'.number_format($onlineRoleplayerData['user_notes']).'"';
				}
				
				$xml .= '>';
				$xml .= '<![CDATA['.$this->encodeSpecialChars($onlineRoleplayerData['userName']).']]>';
				$xml .= '</user>';
			}
			$xml .= '</users>';			
			
		} else {
		
		// Nope. Gimme roleplay IDs. :)
		//$onlineUsersData = $this->getOnlineUsersData(null,'roleplayID');
			
			$xml = '<users>';
			foreach($onlineUsersData as $onlineUserData) {
				$xml .= '<user';
				$xml .= ' userID="'.$onlineUserData['userID'].'"';
				$xml .= ' userRole="'.$onlineUserData['userRole'].'"';
				$xml .= ' channelID="'.$onlineUserData['channel'].'"';
				$xml .= ' roleplayID="'.$onlineUserData['roleplayID'].'"';
				$xml .= ' characterID="'.$onlineUserData['characterID'].'"';
				$xml .= ' groupID="'.$onlineUserData['group_id'].'"';
				
				// Staff Only
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$xml .= ' warnings="'.number_format($onlineUserData['user_warnings']).'"';
					$xml .= ' notes="'.number_format($onlineUserData['user_notes']).'"';
				}
				
				$xml .= '>';
				$xml .= '<![CDATA['.$this->encodeSpecialChars($onlineUserData['userName']).']]>';
				$xml .= '</user>';
			}
			$xml .= '</users>';
		
		}
		return $xml;
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
	
		global $db,$user;

		// Now... what ever shall we do?
		switch($textParts[0]) {
			case '/staff':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
					$this->insertChatBotMessage( $this->getPrivateMessageID(),
						"[b]Staff Commands (Only displayed for staff)[/b]
						[code]/kick <USERNAME> [time] [reason]  [/code]: kicks (and temporarily bans) the user for the specified amount of [time], and adds [reason] to the log. Default: ".$this->getConfig('defaultBanTime')." minutes.
						[code]/bans                             [/code]: lists the current banned usernames.
						[code]/build <DIRECTION>                [/code]: builds a new room in <DIRECTION>, with synchronous exits in both directions.
						[code]/hide <DIRECTION>                	[/code]: hides the exit from the current room to <DIRECTION>, allowing travel in that direction but not displaying the exit in [code]/exits[/code].
						[code]/channelName <NAME>               [/code]: sets the current channel's name.
						[code]/channelDescription <DESCRIPTION> [/code]: sets the current channel's description.
						[code]/owner                            [/code]: reports the owner of the current channel
						[code]/teleport <ROOM ID>               [/code]: immediately warps you to the room with the ID specified.
						[code]/broadcast <MESSAGE>              [/code]: sends out a private message to ALL online users.
						[code]/lasterror                        [/code]: returns the last known error of chat.
						[code]/modcast                          [/code]: sends a broadcast to all online moderators.
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
			case '/cid':
				if ($character_id = $textParts[1]) {
					
					$roleplay_id  = $this->getSessionVar('roleplayID');
					if (!$character = $this->RPG->getCharacterData($character_id,$roleplay_id)) {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"Something went wrong during the character acquisition process. (error codes $character_id and $roleplay_id)");
						return true;
					}

					if ($character_id >= 1  && ($character['owner'] == $user->data['user_id'])) {
					
						if ($this->getSessionVar('InCharacter') == true) {
							// $this->insertChatBotMessage( $character['location'], $this->getUserName() . " switches characters to ".$this->RPG->getCharacterLink($character_name,$roleplay_id).": ".$character['synopsis']);
						} else {
						
							if (!$roleplayName = $this->RPG->getRoleplayName($roleplay_id)) {
								$this->insertChatBotMessage($this->getPrivateMessageID(),"no roleplay name");
							} else {
/* 							
								$message = $this->getUserName() . " enters [url=http://www.roleplaygateway.com/roleplay/".$this->RPG->urlify($roleplayName)."/]".$roleplayName. "[/url], In Character (IC) as ".$this->RPG->getCharacterLink($character_name,$roleplay_id).": ".$character['synopsis'];

								$this->insertChatBotMessage( $character['location'], $message);
								if ($character['location'] != $this->getChannel()) {
									$this->insertChatBotMessage( $this->getChannel(), $message);
								}  */
							}
						}
						
						$this->setSessionVar('CharacterName', $character['name']);
						$this->setSessionVar('CharacterID', $character_id);
						$this->setSessionVar('InCharacter', true);
		
		
						$this->switchChannel($this->getChannelNameFromChannelID($character['location']),$character['location']);
						
						

						
						// Channel description and entrance
/* 						$this->insertChatBotMessage(
							$this->getPrivateMessageID(),
							$this->RPG->getChannelDescription($this->getChannel())
						); */							
					
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"Sorry, you don't own a character by that name. Create one by [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]adding a character[/url] to your account.");
						return true;
					}
					
					return true;

						
				} else {
					$this->insertChatBotMessage( $this->getPrivateMessageID(), "To go [b]I[/b]n [b]C[/b]haracter (IC), you need to first [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]create a character[/url]. Once you've done this, type [code]/ic Character Name[/code] (case sensitive!) to activate the profile." );
					return true;
				}
			break;
			case '/ic':
				
					if ($textParts[1]) {
						
						$character_name = $this->getSpacedParameter($textParts);
					
						$roleplay_id  = $this->getSessionVar('roleplayID');
						
						
						if (!$character_id = $this->RPG->getCharacterID($character_name,$roleplay_id)) {
							$this->insertChatBotMessage($this->getPrivateMessageID(),"You do not own a character in [url=http://www.roleplaygateway.com/roleplay/".$this->RPG->urlify($this->RPG->getRoleplayName($roleplay_id))."/]".$this->RPG->getRoleplayName($roleplay_id)."[/url] named \xe2\x80\x9c".$character_name."\xe2\x80\x9d.\nYou can [url=http://www.roleplaygateway.com/roleplay/".$this->RPG->urlify($this->RPG->getRoleplayName($roleplay_id))."/#join]submit a profile[/url] if you'd like?");
							return true;
						}
						
						if (!$character = $this->RPG->getCharacterData($character_id,$roleplay_id)) {
							$this->insertChatBotMessage($this->getPrivateMessageID(),"Something went wrong during the character acquisition process. (error codes $character_id and $roleplay_id)");
							return true;
						}	
	
											
						
						if ($character_id >= 1  && ($character['owner'] == $user->data['user_id'])) {
						
							if ($this->getSessionVar('InCharacter') == true) {
								// $this->insertChatBotMessage( $character['location'], $this->getUserName() . " switches characters to ".$this->RPG->getCharacterLink($character_name,$roleplay_id).": ".$character['synopsis']);
							} else {
							
								if (!$roleplayName = $this->RPG->getRoleplayName($roleplay_id)) {
									$this->insertChatBotMessage($this->getPrivateMessageID(),"no roleplay name");
								} else {
/* 							
									$message = $this->getUserName() . " enters [url=http://www.roleplaygateway.com/roleplay/".$this->RPG->urlify($roleplayName)."/]".$roleplayName. "[/url], In Character (IC) as ".$this->RPG->getCharacterLink($character_name,$roleplay_id).": ".$character['synopsis'];

									$this->insertChatBotMessage( $character['location'], $message);
									if ($character['location'] != $this->getChannel()) {
										$this->insertChatBotMessage( $this->getChannel(), $message);
									}  */
								}
							}
							
							$this->setSessionVar('CharacterName', $character['name']);
							$this->setSessionVar('CharacterID', $character_id);
							$this->setSessionVar('InCharacter', true);
			
			
							$this->switchChannel($this->getChannelNameFromChannelID($character['location']),$character['location']);
							
							

							
							// Channel description and entrance
							$this->insertChatBotMessage(
								$this->getPrivateMessageID(),
								$this->RPG->getChannelDescription($this->getChannel())
							);							
						
						} else {
							$this->insertChatBotMessage($this->getPrivateMessageID(),"Sorry, you don't own a character by that name. Create one by [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]adding a character[/url] to your account.");
							return true;
						}
					
					} else {
					
					
						// $this->insertChatBotMessage( $this->getPrivateMessageID(), "Show this to Rem: ".var_export($textParts,true));
						$this->insertChatBotMessage( $this->getPrivateMessageID(), "To go [b]I[/b]n [b]C[/b]haracter (IC), you need to first [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]create a character[/url]. Once you've done this, type [code]/ic Character Name[/code] (case sensitive!) to activate the profile." );
						return true;
					}
					
					$this->updateOnlineList();
					$this->addInfoMessage($this->getUserName(), 'userName');
					$this->setSessionVar('InCharacter', true);
					
					return true;

			case '/ooc':
				if (count($textParts) == 1) {
				
					if ($this->getSessionVar('InCharacter') == true) {
						// $this->insertChatBotMessage( $this->getChannel(), $this->RPG->getCharacterLink($this->getSessionVar('CharacterName'),$this->getSessionVar('roleplayID')) . " has suddenly and miraculously disappeared!" );				
						$this->addInfoMessage($this->getUserName(), 'userName');
						$this->setSessionVar('InCharacter', false);
						$this->setSessionVar('CharacterID', null);
						$this->setSessionVar('CharacterName', null);					
						$this->switchChannel($this->getChannelNameFromChannelID(0),0);
						return true;
					} else {
						//$this->insertChatBotMessage($this->getPrivateMessageID(),"You are not In Character!");
						return true;			
					}
			
				} else {
					$this->insertCustomMessage($this->getUserID(), $this->getUserName(), $this->getUserRole(), $this->getChannel(), '/ooc '.$text);
					return true;
				}
			break;
			case '/describe':
				if ($this->getSessionVar('InCharacter') == true) {
					$this->insertCustomMessage($this->getUserID(), $this->getUserName(), $this->getUserRole(), $this->getChannel(), $text);
					return true;
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are not In Character!");
					return true;	
				}
			break;
			case '/say':
				if ($this->getSessionVar('InCharacter') == true) {
					$this->insertCustomMessage($this->getUserID(), $this->getUserName(), $this->getUserRole(), $this->getChannel(), $text);
					return true;
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are not In Character!");
					return true;	
				}
			break;
			case '/nic':
				if ($this->getSessionVar('InCharacter') == true) {
					$this->insertChatBotMessage( $this->getChannel(), $this->RPG->getCharacterLink($this->getSessionVar('CharacterName'),$this->getSessionVar('roleplayID')) . " has suddenly and miraculously disappeared!" );
					$this->addInfoMessage($this->getUserName(), 'userName');
					$this->setSessionVar('InCharacter', false);
					$this->setSessionVar('CharacterID', null);
					$this->setSessionVar('CharacterName', null);
					return true;
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are not In Character!");
					return true;	
				}
			break;
			case '/transcriptBegin':
				if ($this->RPG->getOwner($this->getChannel()) != $this->getUserID()) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You do not have control of this room or roleplay, so you cannot enable transcript mode.");
					return true;
				} else {
				
					if (($this->getChannel() > 0) && ($this->getChannel() < 500000000)) {
				
						$this->insertChatBotMessage(
							$this->getChannel(),
							'/transcriptBegin '.$this->getSessionVar('roleplayID').' '.$this->getChannel(),
							null,
							1
						);
						
						return true;
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't do that in the OOC room or private rooms.");
						return true;						
					}
				}
			break;
			case '/transcriptEnd':
				if ($this->RPG->getOwner($this->getChannel()) != $this->getUserID()) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You do not have control of this room or roleplay, so you cannot end transcript mode.");
					return true;
				} else {
				
					if (($this->getChannel() > 0) && ($this->getChannel() < 500000000)) {
				
						$this->insertChatBotMessage(
							$this->getChannel(),
							'/transcriptEnd '.$this->getSessionVar('roleplayID').' '.$this->getChannel(),
							null,
							1
						);
						
						$this->insertChatBotMessage($this->getPrivateMessageID(),"Uh oh, looks like something went wrong when logging your transcript. Don't worry, we'll come back and try again later.");
						
						return true;
					} else {
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't do that in the OOC room or private rooms.");
						return true;						
					}
				}
			break;
			case '/session':
				$this->insertChatBotMessage($this->getPrivateMessageID(),"Session: ".session_name());
				return true;
			break;
			case '/location':
				$this->insertChatBotMessage($this->getPrivateMessageID(),"Location: ".$this->getChannel()." (".$this->getChannelNameFromChannelID($this->getChannel()).")");
				return true;
			break;
			case '/amimuted':
				$this->insertChatBotMessage($this->getPrivateMessageID(),($this->isUserMuted($this->getUserID())) ? 'Yes.' : 'No.');
				return true;				
			break;
			case '/mute':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
						
					if(count($textParts) == 1) {
						$this->insertChatBotMessage(
							$this->getPrivateMessageID(),
							'/error MissingUserName'
						);
					} else {
						// Get UserID from UserName:
						$muteUserID = $this->getIDFromName($textParts[1]);
						if($muteUserID === null) {
							$this->insertChatBotMessage(
								$this->getPrivateMessageID(),
								'/error UserNameNotFound '.$textParts[1]
							);
						} else {
							// Check the role of the user to mute:
							$muteUserRole = $this->getRoleFromID($muteUserID);
							if($muteUserRole == AJAX_CHAT_ADMIN || ($muteUserRole == AJAX_CHAT_MODERATOR && $this->getUserRole() != AJAX_CHAT_ADMIN)) {
								// Admins and moderators may not be muteed:
								$this->insertChatBotMessage(
									$this->getPrivateMessageID(),
									'You cannot mute an admin or a moderator.'
								);
							} else {
								// mute user and insert message:
								$channel = $this->getChannelFromID($muteUserID);
								$muteMinutes = (count($textParts) > 2) ? $textParts[2] : null;
								$muteReason	= (count($textParts) > 3) ? implode(' ',array_slice($textParts,3)) : null;
								$this->muteUser($textParts[1], $muteMinutes, $muteUserID, $muteReason);
								// If no channel found, user logged out before he could be muteed
								if($channel !== null) {
									$this->insertChatBotMessage(
										$channel,
										$textParts[1]. ' has been muted for '. $muteMinutes. ' minutes. '.$muteReason,
										null,
										1
									);
									// Send a copy of the message to the current user, if not in the channel:
									if($channel != $this->getChannel()) {
										$this->insertChatBotMessage(
											$this->getPrivateMessageID(),
											$textParts[1]. ' has been muted for '. $muteMinutes. ' minutes. '.$muteReason,
											null,
											1
										);
									}
								}
							}
						}
					}

				
					return true;
				} else {
					return false;
				}
			break;
			case '/summonModerator':
			
				$this->setInsertedMessagesRate($this->getConfig('maxMessageRate'));
			
				$sql = 'SELECT DISTINCT userID FROM ajax_chat_online WHERE userRole = 2 OR userRole = 3 OR userRole = 4';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result)) {
					$moderators[] = $row['userID'];
				}
				$db->sql_freeresult($result);

				foreach ($moderators as $myID) {
					$this->insertChatBotMessage($this->getPrivateMessageID($myID),"[b]MODERATOR ALERT:[/b] ".$this->getUserName()." needs a moderator in ".$this->getChannelNameFromChannelID($this->getChannel())." - teleport there using: [code]/teleport ".$this->getChannel()."[/code]. (this is broadcasted to all online moderators)");
				}

				return true;
			break;
			case '/character':
				if ($this->getSessionVar('CharacterName')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are currently [b]I[/b]n [b]C[/b]haracter (IC) as ".$this->getSessionVar('CharacterName')." (".$this->getSessionVar('CharacterID').").");
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You are currently [b]O[/b]ut [b]O[/b]f [b]C[/b]haracter (OOC).");
				}
				return true;
			case '/characters':
				$this->insertChatBotMessage($this->getPrivateMessageID(),$this->RPG->getCharacters($this->getUserID(),$this->getSessionVar('roleplayID')));
				return true;
			case '/places':
				$this->insertChatBotMessage($this->getPrivateMessageID(),'Places in this roleplay: '.implode(', ',$this->RPG->getPlaces($this->getSessionVar('roleplayID'))));
				return true;
			case '/roleplay':

				$this->insertChatBotMessage($this->getPrivateMessageID(),"You are currently in the roleplay, ".$this->RPG->getRoleplayName($this->getSessionVar('roleplayID')).'.');

				return true;
			case '/exits':
				$msg = $this->RPG->listExits($this->getChannel());
				
				$this->insertChatBotMessage($this->getPrivateMessageID(),$msg);
				return true;
			break;
			case '/gms':
				$this->insertChatBotMessage($this->getPrivateMessageID(),implode(', ',$this->RPG->getGameMasters($this->getSessionVar('roleplayID'))));
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
				if (!$destination = $this->RPG->getExit($this->getChannel(),'ascend')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go any higher.");
				} else {
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'ascend');
				}			
				return true;
			break;
			case '/descend':
				if (!$destination = $this->RPG->getExit($this->getChannel(),'descend')) {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You can't go any lower.");
				} else {
					//$this->promptUser("To descend from here, you must select an arrival point.",$destinations);
					
					$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination,'descend');
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
					
						//$this->switchChannel($this->getChannelNameFromChannelID($destination),$destination);
						$this->insertChatBotMessage($this->getPrivateMessageID(),"You have built a room ($destination, ".$this->getChannelNameFromChannelID($destination).").");
						
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
			case '/modcast':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
				
					$sql = 'SELECT DISTINCT userID FROM ajax_chat_online WHERE userRole = 2 OR userRole = 3 OR userRole = 4';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result)) {
						$moderators[] = $row['userID'];
					}
					$db->sql_freeresult($result);

					foreach ($moderators as $userID) {
						$this->insertChatBotMessage($this->getPrivateMessageID($userID),'[i][code]/modcast[/code] from [b]'.$this->getUserName().'[/b][/i]: '. $this->getSpacedParameter($textParts));
					}
					
					return true;
				} else {
					return false;
				}	
			break;
			case '/gamecast':
			case '/oocast':
			case '/ooccast':
				if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR || $this->RPG->getOwner($this->getChannel()) == $this->getUserID()) {
				
					$sql = 'SELECT DISTINCT userID FROM ajax_chat_online WHERE userID IN (SELECT owner FROM rpg_characters WHERE roleplay_id = '.(int) $this->getSessionVar('roleplayID').')';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result)) {
						$roleplayers[] = $row['userID'];
					}
					$db->sql_freeresult($result);

					foreach ($roleplayers as $userID) {
					
						// TODO: make this use 
					
						$this->insertChatBotMessage($this->getPrivateMessageID($userID), $this->getSpacedParameter($textParts));
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
				if(($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) || ($this->RPG->getOwner($this->getChannel()) == $this->getUserID())) {
					$this->RPG->editChannelName($this->getChannel(),$this->getSpacedParameter($textParts));
					$this->insertChatBotMessage($this->getChannel(),"Suddenly, this area begins to warp and melt - everything here begins to twist and turn, and finally evaporate... before taking shape as something completely new: ".$this->getSpacedParameter($textParts));
					return true;
				} else {
					return false;
				}			
			break;
			case '/channelDescription':
				if(($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) || ($this->RPG->getOwner($this->getChannel()) == $this->getUserID())) {
					$this->RPG->editChannelDescription($this->getChannel(),$this->getSpacedParameter($textParts));
					$this->insertChatBotMessage($this->getChannel(),"Something jarrs your perception, as it feels like something here has changed.  You can't quite figure it out, but something is certainly different...");
					return true;
				} else {
					return false;
				}			
			break;
			case '/owner':
				$this->insertChatBotMessage($this->getPrivateMessageID(),"Channel owner: ".$this->getNameFromID($this->RPG->getOwner($this->getChannel())));
				return true;
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
			case 'places':
				return $this->getPlacelistXML();
			case 'teaser':
				return $this->getTeaserViewXMLMessages();
			case 'logs':
				return $this->getLogsViewXMLMessages();
			case 'history':
				return "Not yet available.";
			case 'roleplay':
				return "Not yet available.";
			default:
				return $this->getLogoutXMLMessage();
		}
	}
	
	function getChatViewMessagesXML() {

		// Get the last messages in descending order (this optimises the LIMIT usage):
		$sql = 'SELECT
					id,
					userID,
					userName,
					userRole,
					channel AS channelID,
					UNIX_TIMESTAMP(dateTime) AS timeStamp,
					text,
					characterID,
					roleplayID
				FROM
					'.$this->getDataBaseTable('messages').'
				WHERE
					'.$this->getMessageCondition().'
					'.$this->getMessageFilter().'
				ORDER BY
					id
					DESC
				LIMIT '.$this->getConfig('requestMessagesLimit').';';
				
			$result = $this->db->sqlQuery($sql);
			
			$RPG = new RPG();
			$RPG->ajaxChat = $this;
			$this->RPG = $RPG;	

			// Stop if an error occurs:
			if($result->error()) {
				header('HTTP/1.1 503 Service Unavailable');
				echo $result->getError();
				die();
			}
			
			$messages = '';
			
			// Add the messages in reverse order so it is ascending again:
			while($row = $result->fetch()) {			
				$message = $this->getChatViewMessageXML(
					$row['id'],
					$row['timeStamp'],
					$row['userID'],
					$row['userName'],
					$row['userRole'],
					$row['channelID'],
					$row['text'],
					$row['characterID'],
					$this->RPG->getCharacterURL($row['characterID']),
					$row['roleplayID'],
					$this->RPG->getRoleplayURL($row['roleplayID']),
					$this->RPG->getCharacterName($row['characterID'])
				);		
				$messages = $message.$messages;
			}
			$result->free();
			
						
			
	


				
				
		//$this->insertChatBotMessage(500000004,"[code]".$sql."[/code]");
		
		$messages = '<messages>'.$messages.'</messages>';
		
		
		
		
		return $messages;
	}
	
	function getChatViewMessageXML(
		$messageID,
		$timeStamp,
		$userID,
		$userName,
		$userRole,
		$channelID,
		$text,
		$characterID = 0,
		$characterURL = 0,
		$roleplayID = '',
		$roleplayURL = '',
		$characterName = ''
		) {
		
		if ($userID == $this->getConfig('chatBotID')) {
			$characterID = $this->getConfig('chatBotID');
			$characterName = $userName;
		}
		
		if (
		
			($userID <> $characterID) &&
			(strpos($text,'/ooc') === false) &&
			(strlen($characterName) > 0) &&
			($channelID <> $this->getPrivateMessageID())
			
			) {
			
			
			$InCharacter = true;
			$userName = $this->getNameFromID($userID);
		
		} else {
		
			if ($this->getUserID() == 4) {
				$channelID = 0;
			}
		
			$InCharacter = false;
		}
		
		$message = '<message';
		$message .= ' id="'.$messageID.'"';
		$message .= ' dateTime="'.date('r', $timeStamp).'"';
		$message .= ' userID="'.$userID.'"';
		$message .= ' userRole="'.$userRole.'"';
		$message .= ' channelID="'.$channelID.'"';
		
		if (strpos($text,'/ooc') !== false) {
			$message .= ' channelName="'.$this->RPG->getPlaceName($channelID).'"';
		}
		
		if ($InCharacter == true) {
			$message .= ' characterID="'.(int) $characterID.'"';
			$message .= ' roleplayID="'.(int) $roleplayID.'"';
			$message .= ' characterURL="'.(string) $characterURL.'"';
			$message .= ' roleplayURL="'.(string) $roleplayURL.'"';
		}
		
		$message .= '>';
		
		//if ($characterID > 0) {

		if ($InCharacter == true) {
			$message .= '<character><![CDATA['.$this->encodeSpecialChars($characterName).']]></character>';
		}
		//}
		$message .= '<username><![CDATA['.$this->encodeSpecialChars($userName).']]></username>';
		$message .= '<text><![CDATA['.$this->encodeSpecialChars($text).']]></text>';
		$message .= '</message>';
		return $message;
	}
	
	
	function getPlacelistXML() {
		$places = $this->RPG->getPlaces($this->getSessionVar('roleplayID'));
		
		foreach ($places as $place) {
			$placelist .= '<place><!CDATA['.$place['name'].']]></place>';
		}
		
		return $placelist;
	}
	
	function insertMessage($text) {
	
		if ($this->isUserMuted($this->getUserID())) {
		
			$this->insertChatBotMessage($this->getPrivateMessageID(),"You cannot speak because you have been muted.");
			return;
		
		}
	
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
	
				if(!$this->isAllowedToWriteMessage()) {

					$this->insertChatBotMessage($this->getPrivateMessageID(),"[b]You cannot participate because you are not logged in as a registered user. You should [url=http://www.roleplaygateway.com/ucp.php?mode=register]register[/url] or [url=http://www.roleplaygateway.com/ucp.php?mode=login]log in[/url].[/b]");
					return;
				}

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
	
				$this->setChannel($destinations[$text]['location'],$this->getSessionVar('roleplayID'));
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
	
	function insertCustomMessage($userID, $userName, $userRole, $channelID, $text, $ip=null, $mode=0) {
		// The $mode parameter is used for socket updates:
		// 0 = normal messages
		// 1 = channel messages (e.g. login/logout, channel enter/leave, kick)
		// 2 = messages with online user updates (nick)
		
		$ip = $ip ? $ip : $_SERVER['REMOTE_ADDR'];
		
		
		// TODO: fix inconsistencies in session vars.
		// This is case sensitive, so we will need to correct ALL of them.
		if ($channelID >= $this->getConfig('privateChannelDiff')) {
			$userName = $this->getNameFromID($userID);
			
			if ((strlen($userName) <= 0) and ($userID == $this->getConfig('chatBotID'))) { $userName = $this->getConfig('chatBotName'); }
		} else {
			$roleplayID = $this->getSessionVar('roleplayID');
			$characterID = $this->getSessionVar('CharacterID');
		}
		
		

		if (
				$roleplayID && $characterID && 
				($channelID < $this->getConfig('privateChannelDiff'))) {
		
			$sql = 'INSERT INTO '.$this->getDataBaseTable('messages').'(
									userID,
									userName,
									userRole,
									channel,
									dateTime,
									ip,
									text,
									characterID,
									roleplayID
								)
					VALUES (
						'.$this->db->makeSafe($userID).',
						'.$this->db->makeSafe($userName).',
						'.$this->db->makeSafe($userRole).',
						'.$this->db->makeSafe($channelID).',
						NOW(),
						'.$this->db->makeSafe($this->ipToStorageFormat($ip)).',
						'.$this->db->makeSafe($text).',
						'.$this->db->makeSafe($characterID).',
						'.$this->db->makeSafe($roleplayID).'
					);';		

		
		} else {
			$sql = 'INSERT INTO '.$this->getDataBaseTable('messages').'(
									userID,
									userName,
									userRole,
									channel,
									dateTime,
									ip,
									text
								)
					VALUES (
						'.$this->db->makeSafe($userID).',
						'.$this->db->makeSafe($userName).',
						'.$this->db->makeSafe($userRole).',
						'.$this->db->makeSafe($channelID).',
						NOW(),
						'.$this->db->makeSafe($this->ipToStorageFormat($ip)).',
						'.$this->db->makeSafe($text).'
					);';	
		}

		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			header('HTTP/1.1 503 Service Unavailable');
			echo $result->getError();
			die();
		}
		
		if($this->getConfig('socketServerEnabled')) {
			$this->sendSocketMessage(
				$this->getSocketBroadcastMessage(
					$this->db->getLastInsertedID(),
					time(),
					$userID,
					$userName,
					$userRole,
					$channelID,
					$text,
					$mode
				)
			);	
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
		
		include("chatbot.php");
		
		// $this->insertChatBotMessage(1000000004,$text);
		
/* 		
		$text = $this->stripBBCode($text);
		
		if (stristr($text,'hal') === FALSE) {
			$botProbability = 100;
		} elseif (($this->getSessionVar('botConversation') == true) && ($this->getSessionVar('botConversationLength') <= 5)) {
			$botProbability = 1;
		} elseif (stripos($text,'hal') == 0) {
			$botProbability = 2;
		} else {
			$botProbability = 3;
		}
		
		
		if ($this->getUserID() == 4) {
			$botProbability = 1;
		}
		
		// $botProbability = 100;
		
		// BEGIN HAL
		if (
				(($this->getChannel() == 0) &&
				(strpos($text, '/') <> 0) &&
				(strlen($text) > 0) &&
    // 				(strpos($text, '[color') <> 0) && 
				(mt_rand(1,$botProbability) == 1)) ||
				
				$this->getChannel() == 500000004
		) {

			if (include "/var/www/html/AI/respond.php") {
				
				
				// $this->insertChatBotMessage(1000000004,$text);
				
				if ($botresponse = replybotname($text,$this->getUserID(),'HAL')) {
					$this->setSessionVar('botConversation',true);
					$this->setSessionVar('botConversationLength',$this->getSessionVar('botConversationLength') + 1);
	
				//if (strlen($botresponse) > 0) {

					// $this->insertCustomMessage($this->getConfig('chatBotID'), 'HAL', AJAX_CHAT_CHATBOT, $this->getChannel(), $botresponse->response . "\n". var_export($botresponse,true));
					$this->insertCustomMessage($this->getConfig('chatBotID'), 'HAL', AJAX_CHAT_CHATBOT, $this->getChannel(), preg_replace("/\n/",' ',str_replace('<br/>','  ',$botresponse->response)) );
					
				//} else {
				//	$this->insertChatBotMessage($this->getChannel(),"I'm sorry, I can't do that right now.");
				//}
				} else {
					 $this->insertChatBotMessage(1000000004,'tried to bot, failed');
				}
				
				
				
			} else {
					 $this->insertChatBotMessage(1000000004,'tried to bot, failed');
			}
		}		
		 */
	}
	
	function displayUserAvatar($id,$width = 50,$height = 50) {
	
	}
	
	function getSpacedParameter($command) {
		$parameter = "";
		$j = 1;
		while ($j <= (count($command))) {
			$parameter .= @$command[$j] . " ";
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
			header('HTTP/1.1 503 Service Unavailable');
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
			header('HTTP/1.1 503 Service Unavailable');
			echo $result->getError();
			die();
		}

		// Update the socket server authentication for the kicked user:
		if($this->getConfig('socketServerEnabled')) {
			$this->updateSocketAuthentication($userID);
		}
		
		$this->removeUserFromOnlineUsersData($userID);
		
		if ($kickReason) {
			$kickReason = ' Reason given: '.$kickReason;
		}
		
		$usernote = "$userName was kicked from chat for $banMinutes minutes.$kickReason";
		
		$this->insertChatBotMessage( $this->getChannel(), $usernote);
		
		include_once('/var/www/html/includes/functions.php');
		
		add_log('admin', 'LOG_USER_FEEDBACK', $userName);
		add_log('mod', 0, 0, 'LOG_USER_FEEDBACK', $userName);
		add_log('user', $userID, 'LOG_USER_GENERAL', $usernote);
		
	}
	
	function muteUser($userName, $muteMinutes=null, $userID=null, $muteReason=null) {
		global $config,$user,$db;
	
		if($userID === null) {
			$userID = $this->getIDFromName($userName);
		}
		if($userID === null) {
			return;
		}

		$muteMinutes = $muteMinutes ? $muteMinutes : $this->getConfig('defaultBanTime');

		// Update the socket server authentication for the kicked user:
		if($this->getConfig('socketServerEnabled')) {
			$this->updateSocketAuthentication($userID);
		}
		
		if ($muteReason) {
			$muteReason = ' Reason given: '.$muteReason;
		}
		
		$sql = 'INSERT INTO '.$this->getDataBaseTable('bans').'(
					userID,
					userName,
					dateTime,
					ip,
					type
				)
				VALUES (
					'.$this->db->makeSafe($userID).',
					'.$this->db->makeSafe($userName).',
					DATE_ADD(NOW(), interval '.$this->db->makeSafe($muteMinutes).' MINUTE),
					'.$this->db->makeSafe($this->ipToStorageFormat($ip)).',
					"Mute"
				);';
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			header('HTTP/1.1 503 Service Unavailable');
			echo $result->getError();
			die();
		}
		
		$usernote = "$userName was muted in the chat for $muteMinutes minutes.$muteReason";
		
		$this->insertChatBotMessage( $this->getChannel(), $usernote);
		
		include_once('/var/www/html/includes/functions.php');
		
		add_log('admin', 'LOG_USER_FEEDBACK', $userName);
		add_log('mod', 0, 0, 'LOG_USER_FEEDBACK', $userName);
		add_log('user', $userID, 'LOG_USER_GENERAL', $usernote);
		
	}
	
	function insertParsedMessageKick($textParts) {
		// Only moderators/admins may kick users:
		if($this->getUserRole() == AJAX_CHAT_ADMIN || $this->getUserRole() == AJAX_CHAT_MODERATOR) {
			if(count($textParts) == 1) {
				$this->insertChatBotMessage(
					$this->getPrivateMessageID(),
					'/error MissingUserName'
				);
			} else {
				// Get UserID from UserName:
				$kickUserID = $this->getIDFromName($textParts[1]);
				if($kickUserID === null) {
					$this->insertChatBotMessage(
						$this->getPrivateMessageID(),
						'/error UserNameNotFound '.$textParts[1]
					);
				} else {
					// Check the role of the user to kick:
					$kickUserRole = $this->getRoleFromID($kickUserID);
					if($kickUserRole == AJAX_CHAT_ADMIN || ($kickUserRole == AJAX_CHAT_MODERATOR && $this->getUserRole() != AJAX_CHAT_ADMIN)) {
						// Admins and moderators may not be kicked:
						$this->insertChatBotMessage(
							$this->getPrivateMessageID(),
							'/error KickNotAllowed '.$textParts[1]
						);
					} else {
						// Kick user and insert message:
						$channel = $this->getChannelFromID($kickUserID);
						$banMinutes = (count($textParts) > 2) ? $textParts[2] : null;
						$kickReason	= (count($textParts) > 3) ? implode(' ',array_slice($textParts,3)) : null;
						$this->kickUser($textParts[1], $banMinutes, $kickUserID, $kickReason);
						// If no channel found, user logged out before he could be kicked
						if($channel !== null) {
							$this->insertChatBotMessage(
								$channel,
								'/kick '.$textParts[1],
								null,
								1
							);
							// Send a copy of the message to the current user, if not in the channel:
							if($channel != $this->getChannel()) {
								$this->insertChatBotMessage(
									$this->getPrivateMessageID(),
									'/kick '.$textParts[1],
									null,
									1
								);
							}
						}
					}
				}
			}
		} else {
			$this->insertChatBotMessage(
				$this->getPrivateMessageID(),
				'/error CommandNotAllowed '.$textParts[0]
			);
		}
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
		$sql = 'SELECT id FROM
					'.$this->getDataBaseTable('messages').'
				WHERE
					dateTime < DATE_SUB(NOW(), interval '.$this->getConfig('logsPurgeTimeDiff').' DAY);';
		
		// Create a new SQL query:
		$result = $this->db->sqlQuery($sql);
		
		// Stop if an error occurs:
		if($result->error()) {
			header('HTTP/1.1 503 Service Unavailable');
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
					ORDER BY id LIMIT 80000;';

			$result = $this->db->sqlQuery($sql);

			// Stop if an error occurs:
			if($result->error()) {
				header('HTTP/1.1 503 Service Unavailable');
				$this->insertChatBotMessage($this->getPrivateMessageID(),$result->getError());
				die();
			}

			// Store result for logging:
			$logMsg = '';
			while($row = $result->fetch()) {



				$bbcode = array('[b]','[/b]','[i]','[/i]');
				$replacements = array ('','','','');
				$row['text'] = str_replace($bbcode,$replacements,$row['text']);


				$bbcode = array ('/(\[url=)(.+)(\])(.+)(\[\/url\])/','/(\[color=)(.+)(\])(.+)(\[\/color\])/');
				$replacements = array ('\\4','\\4');
				$row['text'] = preg_replace($bbcode, $replacements, $row['text']);


				$privmsg = ereg('/privmsg', $row['text']);
				$join = ereg('/channelEnter', $row['text']);
				$part = ereg('/channelLeave', $row['text']);
				$logout = ereg('/logout', $row['text']);
				$delete = ereg('/delete', $row['text']);
				if (($privmsg == false) && ($join == false) && ($part == false) && ($logout == false) && ($delete == false) && ($row['channelID'] == 0)) {
				
					$logMsg .= date('M d H:i:s', $row['timeStamp']). ' ';
				
					if (ereg('/me', $row['text'])) {
						$logMsg .= '* '.$this->decodeSpecialChars($row['userName']).' ';
						$logMsg .= $this->decodeSpecialChars($row['text'])."\n";					
					
					} else {						
						$logMsg .= '<'.$this->decodeSpecialChars($row['userName']).'> ';
						$logMsg .= $this->decodeSpecialChars($row['text'])."\n";
					}
				}
			}
			$result->free();

			// Files are rotated every week, labelled by week number, month, and year.
			// File container where all messages are logged:
			$fileContainer = AJAX_CHAT_PATH.'log/chat.log';
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
	
	function removeBBCode($text) {
		$bbcode = array('[b]','[/b]','[i]','[/i]','[s]','[/s]','[u]','[/u]');
		$replacements = array ('','','','','','','','');
		$text = str_replace($bbcode,$replacements,$text);

		$bbcode = array ('/(\[url=)(.+)(\])(.+)(\[\/url\])/','/(\[color=)(.+)(\])(.+)(\[\/color\])/');
		$replacements = array ('\\4','\\4');
		$text = preg_replace($bbcode, $replacements, $text);		
		
		return $text;
	}
	
	function stripBBCode($text_to_search) {
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace($pattern, $replace, $text_to_search);
	}	

}

class RPG {

	var $db;
	var $characterID;
	var $roleplayID;
	var $characterURL;
	var $roleplayURL;
	var $location;
	var $ajaxChat;
	
	function setLocation($location,$id,$roleplay) {
		global $config,$user,$db;
		
		if ($location == 0) {
			return true;
		}
		
		$sql = "UPDATE rpg_characters SET location = ".$location." WHERE id = ".$id." AND roleplay_id = ".$roleplay;
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
	
	function isPlace($id) {
		global $config,$user,$db;
		
		if ($id > $this->ajaxChat->getConfig('privateChannelDiff')) return false;
		
		return true;
	}
	
	function getChannelDescription($id) {
		global $config,$user,$db;

		$sql = "SELECT synopsis FROM rpg_places WHERE id = ".$id;

		if (!$db->sql_query($sql)) {	
			return false;
		} else {
			return $db->sql_fetchfield('synopsis');
		}	
	
	}
	
	function editChannelDescription($id,$description) {
		global $config,$user,$db;
		$sql = "UPDATE rpg_places SET synopsis = '".$db->sql_escape($description)."' WHERE id = $id";
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

		
		$sql = "INSERT INTO rpg_places (`name`,`synopsis`,`owner`,`roleplay_id`) VALUES ('".md5(time())."','You have entered the void - an black expanse with absolutely nothing in it.','".$owner."','1')";
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
	
	function getCharacterData($id,$roleplay) {

	
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
		
		$id 		= intval($id);
		$roleplay 	= intval($roleplay);
		
		$sql = "SELECT name,synopsis,c.location,owner FROM rpg_characters c
					WHERE c.id = ".$id.' AND c.roleplay_id = '.$roleplay;
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result)) {
			$character_data['name'] 		= $row['name'];
			$character_data['synopsis'] 	= preg_replace("@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@","[color=red](( [b]Notice:[/b] URL removed. Please use the [img] tag in your character's \"Description\", instead of using the \"Synopsis\" field. ))[/color]",$row['synopsis']);
			$character_data['location']		= $row['location'];
		/*	$character_data['health']		= $row['health'];
			$character_data['healthMax'] 	= $row['healthMax'];
			$character_data['dexterity'] 	= $row['dexterity'];
			$character_data['strength'] 	= $row['strength']; */
			$character_data['owner'] 		= $row['owner'];
		}
		
		$db->sql_freeresult($result);
		
		return $character_data;
	

	}
	
	function getCharacters($owner,$roleplay = 1) {
		global $config,$user,$db;	
		$sql = "SELECT name,synopsis,owner FROM rpg_characters c
					INNER JOIN rpg_characters_stats s ON c.id = s.character_id
					WHERE c.owner = $owner AND s.roleplay_id = $roleplay";

	
		if ($result = $db->sql_query($sql)) {
	
			while ($row = $db->sql_fetchrow($result)) {

				$characters[] = $this->getCharacterLink($row['name'],$roleplay);

			}	
		
			if (count($characters) >= 1) {
				$msg = "[i][b]Your characters[/b] ";
			
				$characterlist = implode(", ",$characters);
			
			} else {
				$msg = "[i][b]You have no characters in this roleplay. ($sql)[/b]";
			}

		} else {
			return false;
		}
		$db->sql_freeresult($result);
		
		$msg .= $characterlist."[/i]";
		
		
		return $msg;			
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
	
	function getCharacterLink($name,$roleplay) {
		return "[url=http://www.roleplaygateway.com/roleplay/".$this->urlify($this->getRoleplayName($roleplay))."/characters/".$this->urlify($name)."/]".$name."[/url]";
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
	
	function getCharacterId($name,$roleplay_id) {
		global $config,$user,$db;
		
		$roleplay = $this->getRoleplayData($roleplay_id);
		
		if ($roleplay['require_approval'] == 1) {
			$sql = "SELECT id FROM rpg_characters WHERE name = '".$db->sql_escape($name)."' AND roleplay_id = ".$db->sql_escape($roleplay_id). " AND approved = 1";
		} else {
			$sql = "SELECT id FROM rpg_characters WHERE name = '".$db->sql_escape($name)."' AND roleplay_id = ".$db->sql_escape($roleplay_id);
		}
		
		
		if (!$db->sql_query($sql)) {	
			return false;
		} else {
			return (int) $db->sql_fetchfield('id');
		}
	}
	
	function getPlaceData($id) {
		global $config,$user,$db;
		$sql = 'SELECT name,synopsis FROM rpg_places WHERE id = '.(int) $id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		return $row;
	}
	
	function getPlaceName($id) {
		global $config,$user,$db;
		$sql = 'SELECT name FROM rpg_places WHERE id = '.(int) $id;
		$result = $db->sql_query($sql);
		$url = $db->sql_fetchfield('name');
		$db->sql_freeresult($result);
		return $url;
	}
	
	function getCharacterName($id) {
		global $config,$user,$db;
		$sql = 'SELECT name FROM rpg_characters WHERE id = '.(int) $id;
		$result = $db->sql_query($sql);
		$url = $db->sql_fetchfield('name');
		$db->sql_freeresult($result);
		return $url;
	}
	
	function getCharacterURL($id) {
		global $config,$user,$db;
		$sql = 'SELECT url FROM rpg_characters WHERE id = '.(int) $id;
		$result = $db->sql_query($sql);
		$url = $db->sql_fetchfield('url');
		$db->sql_freeresult($result);
		return $url;
	}	
	
	function getRoleplayURL($id) {
		global $config,$user,$db;
		$sql = 'SELECT url FROM rpg_roleplays WHERE id = '.(int) $id;
		$result = $db->sql_query($sql);
		$url = $db->sql_fetchfield('url');
		$db->sql_freeresult($result);
		return $url;
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
	
	function getPlaces($id,$limit = 10) {
		global $config,$user,$db;
		$sql = "SELECT id,name,owner,parent_id,synopsis FROM rpg_places
				WHERE roleplay_id = '".$db->sql_escape($id)."' AND visibility <> 'Hidden' LIMIT ". (int) $limit;
		if (!$result = $db->sql_query($sql)) {	
			return false;
		} else {
		
			while ($row = $db->sql_fetchrow($result)) {
			
				
			
				$places[$row['id']] = $row;
			
				$sql = 'SELECT count(userID) as onlineUsers FROM ajax_chat_online WHERE channel = '.(int) $row['id'] .'';
				$players_result = $db->sql_query($sql);
				$places[$row['id']]['onlineUsers'] = $db->sql_fetchfield('onlineUsers');
				$db->sql_freeresult($players_result);
			
				$sql = 'SELECT max(dateTime) as lastMessage FROM ajax_chat_messages WHERE channel = '.(int) $row['id'] .'';
				$players_result = $db->sql_query($sql);
				$places[$row['id']]['lastMessage'] = $db->sql_fetchfield('lastMessage');
				$db->sql_freeresult($players_result);
			
			}
			
			$db->sql_freeresult($result);
		
			return (array) $places;
		}			
	}
	
	function getParentPlaces($id,$limit = 10) {
		global $config,$user,$db;
		$sql = "SELECT id,name FROM rpg_places WHERE roleplay_id = '".$db->sql_escape($id)."' AND parent_id = -1 LIMIT ". (int) $limit;
		if (!$result = $db->sql_query($sql)) {	
			return false;
		} else {
		
			while ($row = $db->sql_fetchrow($result)) {
				$places[$row['id']] = $row;
			}
		
			return (array) $places;
		}			
	}
	
	function getPlaceChildren($id,$limit = 10) {
		global $config,$user,$db;
		$sql = "SELECT id,name FROM rpg_places WHERE parent_id = '".$db->sql_escape($id)."' LIMIT ". (int) $limit;
		if (!$result = $db->sql_query($sql)) {	
			return false;
		} else {
		
			while ($row = $db->sql_fetchrow($result)) {
				$places[$row['id']] = $row;
			}
		
			return (array) $places;
		}			
	}
	
	function getTopPlaces($id) {
		global $config,$user,$db;
		$sql = "SELECT id,name FROM rpg_places WHERE roleplay_id = '".$db->sql_escape($id)."' ORDER BY id ASC LIMIT 5";
		if (!$result = $db->sql_query($sql)) {	
			return false;
		} else {
		
			while ($row = $db->sql_fetchrow($result)) {
				if ($row['id'] == 0) continue;
				$places[$row['id']] = $row;
			}
		
			return (array) $places;
		}			
	}
	
	function getRoleplayName($id) {
		global $config,$user,$db;
		$sql = "SELECT title FROM rpg_roleplays WHERE id = '".$db->sql_escape($id)."'";
		if (!$db->sql_query($sql)) {	
			return false;
		} else {
			return (string) $db->sql_fetchfield('title');
		}		
	}
	
	function getRoleplayData($id) {
		global $config,$user,$db;
		$sql = "SELECT * FROM rpg_roleplays WHERE id = '".$db->sql_escape($id)."'";
		if (!$result = $db->sql_query($sql)) {	
			return false;
		} else {
			return (array) $db->sql_fetchrow($result);
		}		
	}
	
	function getGameMasters($id) {
		global $config,$user,$db;
		$sql = "SELECT owner FROM rpg_roleplays WHERE id = '".$db->sql_escape($id)."'";
		$result = $db->sql_query($sql);
		$gms[] = (int) $db->sql_fetchfield('owner');

		
		$sql = "SELECT user_id FROM rpg_permissions WHERE roleplay_id = '".$db->sql_escape($id)."' and isCoGM = true";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			$gms[] = (int) $row['user_id'];
		}
		
		return (array) $gms;
	}
	
	
	
	function spawn($id,$roleplay = 1) {
		global $config,$user,$db;
							
		$sql = "INSERT INTO rpg_characters_stats (character_id,roleplay_id) VALUES (".$id.",".$roleplay.")";

		if (!$db->sql_query($sql)) {
			return false;
		} else {
			return true;
		}
	}
	
	function showRoleplayStats ($id) {
		global $config,$user,$db;
		
		$sql = '';
		
		$placesArray = $this->getTopPlaces($id);
		
		foreach ($placesArray as $place) {
			$placeList[] = $place['name'];
		}
		
		return '<strong>Stats:</strong> Soon&trade;. <strong>Example Locations:</strong> '.implode(', ',$placeList);
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
			case 'ascend':
				$return = 'descend';
			break;
			case 'descend':
				$return = 'ascend';
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
	

	function urlify($string) {
		return $this->sanitize_title_with_dashes($string);
	}

	function sanitize_title_with_dashes($title) {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

		$title = $this->remove_accents($title);
		if ($this->seems_utf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			$title = $this->utf8_uri_encode($title, 200);
		}

		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = str_replace('.', '-', $title);
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');

		return $title;
	}

	function remove_accents($string) {
		if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;

		if ($this->seems_utf8($string)) {
			$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '');

			$string = strtr($string, $chars);
		} else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}

		return $string;
	}

	function seems_utf8($str) {
		$length = strlen($str);
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

	function utf8_uri_encode( $utf8_string, $length = 0 ) {
		$unicode = '';
		$values = array();
		$num_octets = 1;
		$unicode_length = 0;

		$string_length = strlen( $utf8_string );
		for ($i = 0; $i < $string_length; $i++ ) {

			$value = ord( $utf8_string[ $i ] );

			if ( $value < 128 ) {
				if ( $length && ( $unicode_length >= $length ) )
					break;
				$unicode .= chr($value);
				$unicode_length++;
			} else {
				if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

				$values[] = $value;

				if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
					break;
				if ( count( $values ) == $num_octets ) {
					if ($num_octets == 3) {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
						$unicode_length += 9;
					} else {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
						$unicode_length += 6;
					}

					$values = array();
					$num_octets = 1;
				}
			}
		}

		return $unicode;
	}
	
	
	
	
	
}

function get_parent_places($id) {
	global $db;
	
	$sql 	= 'SELECT p.id,p.name,p.url,p.synopsis,p.owner,r.owner as roleplay_owner FROM rpg_places p INNER JOIN rpg_roleplays r on p.roleplay_id = r.id WHERE p.roleplay_id = '.$id.' and (p.parent_id = "-1") AND p.id <> 0';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {
		$children[$row['id']]['id'] 					= $row['id'];
		$children[$row['id']]['name'] 					= $row['name'];
		$children[$row['id']]['url'] 					= $row['url'];
		$children[$row['id']]['owner'] 					= $row['owner'];
		$children[$row['id']]['roleplay_owner'] 		= $row['roleplay_owner'];
		$children[$row['id']]['synopsis'] 				= $row['synopsis'];
		$children[$row['id']]['parent'] 				= $id;
		$children[$row['id']]['children'] 				= get_place_children($row['id']);
	}
	
	return $children;
}

function get_place_children($id) {
	global $db;
	
	$sql 	= 'SELECT p.id,p.name,p.url,p.synopsis,p.owner,r.owner as roleplay_owner FROM rpg_places p INNER JOIN rpg_roleplays r on p.roleplay_id = r.id WHERE p.parent_id = '.$id.' AND p.id <> 0';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {
		$children[$row['id']]['id'] 					= $row['id'];
		$children[$row['id']]['name'] 					= $row['name'];
		$children[$row['id']]['url'] 					= $row['url'];
		$children[$row['id']]['owner'] 					= $row['owner'];
		$children[$row['id']]['roleplay_owner'] 		= $row['roleplay_owner'];
		$children[$row['id']]['synopsis'] 				= $row['synopsis'];
		$children[$row['id']]['parent'] 				= $id;
		$children[$row['id']]['children'] 				= get_place_children($row['id']);
	}
	$db->sql_freeresult($result);
	
	return @$children;
}

function display_place_item($item,$roleplay) {
	global $user, $auth;

	$output = '<div style="padding-left:20px; clear:both; height:auto;" class="place">';
	
	$output .= '<div class="controls" style="float:right;">';
	$output .= '<a href="javascript:toggleDiv(\'place_children_'.$item['id'].'\');">Toggle</a>';
	$output .= '</div>';
	
	$output .= '<div id="place_'.$item['id'].'">';
	$output .= '<img id="place_img_'.$item['id'].'" style="float:left;" src="http://www.roleplaygateway.com/images/places_thumbnail.php?place_id='.$item['id'].'" alt="'.$item['name'].' Thumbnail" />';
	$output .= '<div style="margin-left:115px;">';
	$output .= '<h3>';
	$output .= '<a href="http://www.roleplaygateway.com/roleplay/'.$roleplay.'/places/'.$item['url'].'/">'.$item['name'].'</a>';
	if (($item['roleplay_owner'] == $user->data['user_id']) || ($item['owner'] == $user->data['user_id']) || ($auth->acl_get('a_'))) {
		$output .= ' (<a href="http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=edit_place&place_id='.$item['id'].'">Edit &raquo;</a>)';
	}
	$output .= '</h3>';
	$output .= '<p id="place_synopsis_'.$item['id'].'">'.$item['synopsis'].'</p>';
	$output .= '</div>';
	
	if ($item['children']) {
		$output .= '<div id="place_children_'.$item['id'].'" style="padding-left:20px; clear:both; height:auto;" class="place">';
		foreach ($item['children'] as $place) {
			$output .= display_place_item($place,$roleplay);
		}
		$output .= '</div>';
	}
	
	$output .= '</div>';
	$output .= '</div>';
	
	return $output;
}

function display_list_item($item) {
	echo '<li>';
	echo $item['name'];
	
	if ($item['children']) {
	
		echo '<ul>';
		
		foreach ($item['children'] as $place) {
			echo display_list_item($place);
		}
		
		echo '</ul>';
	}
	
	echo '</li>';
}


?>
