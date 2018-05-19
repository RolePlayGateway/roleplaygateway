/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Overriding client side functionality:

/*
// Example - Overriding the replaceCustomCommands method:
ajaxChat.replaceCustomCommands = function(text, textParts) {
	return text;
}
 */
 
 
ajaxChat.init = function(config, lang, initSettings, initStyle, initialize, initializeFunction, finalizeFunction) {	
	this.httpRequest		= new Object();
	this.usersList			= new Array();
	this.onlineUsers		= new Array();
	this.onlineUserDetails	= new Array();
	this.onlineUsersArray	= new Array();
	this.userNamesList		= new Array();
	this.messageHistory		= new Array();
	this.viewListExpanded	= true;
	this.lastQueueID		= 0;
	this.userMenuCounter	= 0;
	this.lastID				= 0;
	this.roleplayID			= 1;
	this.roleplay			= new Object();
	this.localID			= 0;
	this.queryString		= new Querystring();
	this.lang				= lang;		
	this.initConfig(config);
	this.initDirectories();		
	if(initSettings) {
		this.initSettings();
	}
	if(initStyle) {
		this.initStyle();
	}
	if(this.queryString.contains("roleplayID")) {
		//alert("hi there. oh, the benefits of using non-public code..." + this.queryString.get("roleplayID"));
		this.roleplayID = this.queryString.get("roleplayID");
	
	}
	this.initializeFunction = initializeFunction;
	this.finalizeFunction = finalizeFunction;
	if(initialize) {
		this.setLoadHandler();
	}
}
 
ajaxChat.replaceCustomCommands = function(text, textParts) {
	switch(textParts[0]) { 
		case '/transcriptBegin': 
			text=text.replace('/transcriptBegin', ' '); 
			return '<span class="chatBotMessage">Logging has been enabled for this roleplay session. Subsequent roleplay will be automatically converted and posted to the roleplay.</span>'; 
		return text;
		case '/transcriptEnd': 
			text=text.replace('/transcriptEnd', ' '); 
			return '<span class="chatBotMessage">This roleplaying session is now marked as complete! Logging has been turned off.</span>'; 
		return text; 
		case '/takeover': 
			text=text.replace('/takeover', ' '); 
			return '<span class="chatBotMessage">' + this.replaceBBCode(text) + '</span>'; 
		return text;
		case '/say':
			text=text.replace('/say', ' ');
			return text;
		case '/describe':
			text=text.replace('/describe', ' ');
			return '<span class="describe">' + this.replaceBBCode(text) + '</span>';
		case '/ooc':
			// I don't know why the fuck I have to remove the first 9 chars, but I do.
			// TODO: fix?
			return '<span class="oocmessage">' + this.replaceBBCode(text.substr(9)) + '</span>'; 
		return text;
	}
}

ajaxChat.replaceCommandPrivMsg = function(textParts) {
	var privMsgText = textParts.slice(1).join(' ');
	privMsgText = this.replaceBBCode(privMsgText);
	privMsgText = this.replaceHyperLinks(privMsgText);
	privMsgText = this.replaceEmoticons(privMsgText);
	return privMsgText;
}

ajaxChat.replaceCommandPrivMsgTo = function(textParts) {
	var privMsgText = textParts.slice(2).join(' ');
	privMsgText = this.replaceBBCode(privMsgText);
	privMsgText = this.replaceHyperLinks(privMsgText);
	privMsgText = this.replaceEmoticons(privMsgText);
	return	privMsgText;
}

ajaxChat.replaceCommandPrivAction = function(textParts) {
	var privActionText = textParts.slice(1).join(' ');
	privActionText = this.replaceBBCode(privActionText);
	privActionText = this.replaceHyperLinks(privActionText);
	privActionText = this.replaceEmoticons(privActionText);
	return	'<span class="action">'
			+ privActionText
			+ '</span> <span class="privmsg">'
			+ this.lang['privmsg']
			+ '</span> ';
}

ajaxChat.replaceCommandPrivActionTo = function(textParts) {
	var privActionText = textParts.slice(2).join(' ');
	privActionText = this.replaceBBCode(privActionText);
	privActionText = this.replaceHyperLinks(privActionText);
	privActionText = this.replaceEmoticons(privActionText);
	return	'<span class="action">'
			+ privActionText
			+ '</span> <span class="privmsg">'
			+ this.lang['privmsgto'].replace(/%s/, textParts[1])
			+ '</span> ';		
}

ajaxChat.assignFontColorToCommandMessage = function(text, textParts) {
	switch(textParts[0]) {
		case '/msg':
			if(textParts.length > 2) {
				return	textParts[0]+' '+textParts[1]+' '
						+ '[color='+this.settings['fontColor']+']'
						+ textParts.slice(2).join(' ')
						+ '[/color]';
			}
			break;
		case '/describe':
			if(textParts.length > 1) {
				return	textParts[0]+' [color='+this.settings['fontColor']+']'+textParts[1]+' '
						+ ''
						+ textParts.slice(2).join(' ')
						+ '[/color]';
			}
			break;		
		case '/me':
		case '/action':
			if(textParts.length > 1) {
				return	textParts[0]+' '
						+ '[color='+this.settings['fontColor']+']'
						+ textParts.slice(1).join(' ')
						+ '[/color]';
			}
			break;
	}
	return text;
}

ajaxChat.makeRequest = function(url, method, data) {
	
	ajaxChat.setStatus('On');
	ajaxChat.retryTimer = setTimeout("ajaxChat.updateChat(null); ajaxChat.setStatus('Alert');", this.retryTimerDelay);
	try {
		var identifier;
		var start = (new Date).getTime();
		this.createCookie('chatRequestStart',start);
		
		
		
		if(data) {
			// Create up to 50 HTTPRequest objects:
			if(!arguments.callee.identifier || arguments.callee.identifier > 50) {
				arguments.callee.identifier = 1;
			} else {
				arguments.callee.identifier++;
			}
			identifier = arguments.callee.identifier;
		} else {
			identifier = 0;
		}
		
		
		if(method == 'GET') {
			identifier = 0;
			this.getHttpRequest(identifier).abort();
		}
		
		// url = url + '&roleplayID=' + this.roleplayID;
		
		this.getHttpRequest(identifier).open(method, url, true);
		
		this.getHttpRequest(identifier).onreadystatechange = function() {
		
			if (this.readyState == 4) {
				var lag = ((new Date).getTime() - ajaxChat.readCookie('chatRequestStart')) / 1000;
				var database = '';

				lag = lag  + 's';
				
				if (this.status == 503) {
					//database = ' + <span style="color:#f00;">db overload!</span>';
					//lag = '<span style="color:#f00; font-size: 0.7em;">overloaded! (lag:' + lag + ', load: ' + Math.round(this.responseText*100) / 4  + '%)</span>';
				
					// alert('You have been disconnected. Network issues?');
				}
				
				try {
					ajaxChat.updateDOM('chatLag', lag, null, true);
				} catch (e) {
					
				}
			} else {
				
			}
			
			
		
		
			try {
				ajaxChat.handleResponse(identifier);
			} catch(e) {
				try {
					clearTimeout(ajaxChat.timer);
				} catch(e) {
					//alert(e);
				}
				try {
					if(data) {
						ajaxChat.addChatBotMessageToChatList('/error ConnectionTimeout');
						ajaxChat.setStatus('Alert');
						ajaxChat.updateChatlistView();
					}
				} catch(e) {
					//alert(e);
				}
				try {				
					ajaxChat.timer = setTimeout('ajaxChat.updateChat(null);', ajaxChat.timerRate);
				} catch(e) {
					//alert(e);
				}
			}
		};
		
		if(method == 'POST') {
			this.getHttpRequest(identifier).setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		}
		
		this.getHttpRequest(identifier).send(data);
		
	} catch(e) {
		clearTimeout(this.timer);
		if(data) {
			this.addChatBotMessageToChatList('/error ConnectionTimeout');
			ajaxChat.setStatus('Alert');
			this.updateChatlistView();
		}
		this.timer = setTimeout('ajaxChat.updateChat(null);', this.timerRate);
		
	}	
	
}


ajaxChat.handleResponse = function(identifier) {


	if (this.getHttpRequest(identifier).readyState == 4) {
	
		if (this.getHttpRequest(identifier).status == 200) {
			clearTimeout(ajaxChat.retryTimer);
			var xmlDoc = this.getHttpRequest(identifier).responseXML;
			ajaxChat.setStatus('Off');		
		} else {
			// Connection status 0 can be ignored.
			if (this.getHttpRequest(identifier).status == 0) {
				ajaxChat.setStatus('On');
				this.updateChatlistView();
				return false;
			} else {
				//this.addChatBotMessageToChatList('/error ConnectionStatus '+this.getHttpRequest(identifier).status+' - It looks like we are having trouble connecting to the server. Give us 30 seconds and we will attempt to resolve this.');
				ajaxChat.setStatus('Alert');
				this.updateChatlistView();				
				return false;
			}
		}
	}
	if(!xmlDoc) {
		return false;
	}
	this.handleXML(xmlDoc);
	return true;
}

ajaxChat.switchChannel = function(channel) {
	if(!this.chatStarted) {
		this.clearChatList();
		this.channelSwitch = true;
		this.loginChannelID = null;
		this.loginChannelName = channel;
		this.requestTeaserContent();
		return;
	}
	clearTimeout(this.timer);	
	var message = 	'lastID='
					+ this.lastID
					+ '&channelName='
					+ this.encodeText(channel);		
	this.makeRequest(this.ajaxURL,'POST',message);
	if(this.dom['inputField'] && this.settings['autoFocus']) {
		this.dom['inputField'].focus();
	}
}

ajaxChat.switchChannelByID = function(channelID) {
	if(!this.chatStarted) {
		this.clearChatList();
		this.channelSwitch = true;
		this.loginChannelID = channelID;
		this.requestTeaserContent();
		return;
	}
	clearTimeout(this.timer);
	
	// $('#placeSynopsis').text($('#placeSynopsis_'+channelID).text());
	
	var message = 	'lastID='
					+ this.lastID
					+ '&channelID='
					+ this.encodeText(channelID);		
	this.makeRequest(this.ajaxURL,'POST',message);
	if(this.dom['inputField'] && this.settings['autoFocus']) {
		this.dom['inputField'].focus();
	}
	
	$('#placesControlContainer').dialog('close');
	
	// document.getElementById('placeCurrent').innerHTML = (channelID == 0) ? 'Places List &raquo;' : 'placename...';
	
}

ajaxChat.switchCharacterByID = function(characterID) {
	
	$('#characterSelect_'+ characterID).css({ opacity: '1.0' });
	
/* 	$('#chatList').animate({
		opacity: 0
	}, {
		complete: function () {
			this.sendMessageWrapper('/cid '+ characterID);
		}
	}).$('#chatList').animate({
		opacity: 1.0
	});	 */
	$('#chatList').css({
		opacity: 0
	});
	this.sendMessageWrapper('/cid '+ characterID);
	$('#chatList').animate({
		opacity: 1.0
	});
}

ajaxChat.switchCharacter = function(characterName,characterID) {
	this.sendMessageWrapper('/ic '+ characterName);
	document.getElementById('characterCurrent').innerHTML = '<img src="http://www.roleplaygateway.com/images/character_avatar.php?character_id='+ characterID +'" />' + characterName;
}



ajaxChat.switchRoleplayByID	= function(roleplayID) {

	$('.viewListCharacter').css({ opacity: '0.6' });
	$('#viewList_channel_ooc').removeClass('highlight newMessages');

	this.clearChatList();
	this.roleplayID = roleplayID;
	this.lastID =	0;
	this.updateChat(null);
}

ajaxChat.exitCharacter = function () {
	document.getElementById('characterCurrent').innerHTML = 'You are currently <abbr title="Out Of Character">OOC</abbr>.';
}

ajaxChat.updateCharacterSelection = function () {
	
}

ajaxChat.toggleViewList = function () {
	if (this.viewListExpanded == false) {
		this.viewListExpanded = true;
		
		document.getElementById('viewListToggler').innerHTML = '&laquo; collapse';
		
		$('#chatListColumn').animate({ left: '180' });
		
		this.updateChatlistView();
		
		
	} else {
		this.viewListExpanded = false;
		
		document.getElementById('viewListToggler').innerHTML = '&raquo;';
		$('#chatListColumn').animate({ left: '30' });
		
		this.updateChatlistView();
	}
}

ajaxChat.socketSecurityErrorHandler = function(event) {
	// setTimeout is needed to avoid calling the flash interface recursively (e.g. sound on new messages):
	setTimeout('', 0);
	setTimeout('ajaxChat.updateChatlistView()', 1);
}

	ajaxChat.getUserNodeStringItems = function(encodedUserName, userID) {
		var menu;
		menu = this.getCustomUserMenuItems(encodedUserName, userID);
		if(encodedUserName != this.encodedUserName) {
			menu 	+= '<li><a href="javascript:ajaxChat.insertMessageWrapper(\'/msg '
					+ encodedUserName
					+ ' \');">'
					+ this.lang['userMenuSendPrivateMessage']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/query '
					+ encodedUserName
					+ '\');">'
					+ this.lang['userMenuOpenPrivateChannel']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/query\');">'
					+ this.lang['userMenuClosePrivateChannel']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/invite '
					+ encodedUserName
					+ '\');">'
					+ this.lang['userMenuInvite']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/uninvite '
					+ encodedUserName
					+ '\');">'
					+ this.lang['userMenuUninvite']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/ignore '
					+ encodedUserName
					+ '\');">'
					+ this.lang['userMenuIgnore']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/whereis '
					+ encodedUserName
					+ '\');">'
					+ this.lang['userMenuWhereis']
					+ '</a></li>';			
			if(this.userRole == 2 || this.userRole == 3) {
				menu	+= '<li><a href="javascript:ajaxChat.insertMessageWrapper(\'/kick '
						+ encodedUserName
						+ ' \');">'
						+ this.lang['userMenuKick']
						+ '</a></li>'
						+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/whois '
						+ encodedUserName
						+ '\');">'
						+ this.lang['userMenuWhois']
						+ '</a></li>';
			}
		} else {
			menu 	+= '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/ic\');">In Character</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/ooc\');">Out Of Character</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/who\');">'
					+ this.lang['userMenuWho']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/ignore\');">'
					+ this.lang['userMenuIgnoreList']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/list\');">'
					+ this.lang['userMenuList']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.insertMessageWrapper(\'/me \');">'
					+ this.lang['userMenuAction']
					+ '</a></li>'
					+ '<li><a href="javascript:ajaxChat.insertMessageWrapper(\'/roll \');">'
					+ this.lang['userMenuRoll']
					+ '</a></li>';
			if(this.userRole == 1 || this.userRole == 2 || this.userRole == 3) {
				menu	+= '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/join\');">'
						+ this.lang['userMenuEnterPrivateRoom']
						+ '</a></li>';
				if(this.userRole == 2 || this.userRole == 3) {
					menu	+= '<li><a href="javascript:ajaxChat.sendMessageWrapper(\'/bans\');">'
							+ this.lang['userMenuBans']
							+ '</a></li>';
				}
			}
		}
		return menu;
	}

	
ajaxChat.getCustomUserMenuItems = function(encodedUserName, userID) {
	return '<li><a href="http://www.roleplaygateway.com/member-u'+ userID +'.html" target="_parent">View Profile</a></li>';
}

ajaxChat.getCharacterName = function(encodedUserName, userID) {

}

ajaxChat.isAllowedToDeleteMessage = function(messageID, userID, userRole, channelID) {
	if((((this.userRole == 1 && this.allowUserMessageDelete && (userID == this.userID ||
		parseInt(channelID) == parseInt(this.userID)+this.privateMessageDiff ||
		parseInt(channelID) == parseInt(this.userID)+this.privateChannelDiff)) ||
		this.userRole == 2) && userRole != 3 && userRole != 4) || this.userRole == 3
			|| ((this.userRole == 2) && userRole != 3)
		
		) {
		return true;
	}
	return false;
}

ajaxChat.handleChatMessages = function(messageNodes) {
	if(messageNodes.length) {
	
		
	
		var userNode,userName,textNode,messageText,characterNode,CharacterName,isInCharacter,characterID,roleplayID,characterURL,roleplayURL;		
		for(var i=0; i<messageNodes.length; i++) {
			userNode = messageNodes[i].getElementsByTagName('username')[0];
			userName = userNode.firstChild ? userNode.firstChild.nodeValue : '';
			textNode = messageNodes[i].getElementsByTagName('text')[0];
			messageText = textNode.firstChild ? textNode.firstChild.nodeValue : '';
			
			characterNode = messageNodes[i].getElementsByTagName('character')[0];
			try {
				// This is wrapped in a try/catch because Internet Explorer fucking blows
				if (typeof(characterNode) == 'undefined') {
				
					if (messageText.indexOf('/say') == 0) {
					
						isInCharacter = false;
						
						characterName = 'desc CNAME';
						characterID = 'desc ID ';
						roleplayID = 'desc RID ';
						characterURL = 'desc CURL ';
						roleplayURL = 'desc RURL ';								
					
					} else {
						isInCharacter = false;
						
						characterName = null;
						characterID = null;
						roleplayID = null;
						characterURL = null;
						roleplayURL = null;						
					}
					
					
					

				} else {
				
					isInCharacter = true;
					
					characterName = characterNode.firstChild.nodeValue;

					characterID 	= 	messageNodes[i].getAttribute('characterID');
					roleplayID 		=	messageNodes[i].getAttribute('roleplayID');
					characterURL 	= 	messageNodes[i].getAttribute('characterURL');
					roleplayURL 	=	messageNodes[i].getAttribute('roleplayURL');					
				
				}
			} catch (e) {
				
					isInCharacter = false;
					
					
					
					characterName = null;
					characterID = null;
					roleplayID = null;
					characterURL = null;
					roleplayURL = null;
			}
			

						
			// characterNode = messageNodes[i].getElementsByTagName('character') ? messageNodes[i].getElementsByTagName('character')[0] : '';
/*   			if (characterNode) { 
				
				isInCharacter = true;
				
				characterName = characterNode.firstChild ? characterNode.firstChild.nodeValue : '';

				characterID 	= 	messageNodes[i].getAttribute('characterID');
				roleplayID 		=	messageNodes[i].getAttribute('roleplayID');
				characterURL 	= 	messageNodes[i].getAttribute('characterURL');
				roleplayURL 	=	messageNodes[i].getAttribute('roleplayURL');			
				
				
			} else {
				isInCharacter = false;
			}
			
			if (characterName.length) {
				characterName = userName;
			} */
			
			if (this.settings['blind'] == true) {
				messageText = this.stripBBCodeTags(messageText);
				messageText = messageText.toLowerCase();
				
				
				messageText = messageText.replace(/(.)\1{3,}/g,"$1");
				messageText = messageText.replace(/(.)\1{3,}/g,"$1");
				
				
				
				messageText = messageText.slice(0,1).toUpperCase() + messageText.slice(1);
				
				//messageText = messageText.replace(/](.)/,']$1');
				messageText = messageText.replace(/ i /ig,' I ');
				
				// Cloasse reports that this is wrong.
				//messageText = messageText.toProperCase();
			}
			
			this.addMessageToChatList(
					new Date(messageNodes[i].getAttribute('dateTime')),
					messageNodes[i].getAttribute('userID'),
					userName,
					messageNodes[i].getAttribute('userRole'),
					messageNodes[i].getAttribute('id'),
					messageText,
					messageNodes[i].getAttribute('channelID'),
					messageNodes[i].getAttribute('ip'),
					characterName,
					characterID,
					roleplayID,
					characterURL,
					roleplayURL
			);
		}
		this.updateChatlistView();		
		this.lastID = messageNodes[messageNodes.length-1].getAttribute('id');
	}
}

ajaxChat.addChatBotMessageToChatList = function(messageText) {
	this.addMessageToChatList(
		new Date(),
		this.chatBotID,
		this.getEncodedChatBotName(),
		4,
		null,
		messageText,
		null,
		"Bartender"
	);
}

ajaxChat.addMessageToChatList = function(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip, characterName, characterID, roleplayID, characterURL, roleplayURL) {

	// Prevent adding the same message twice:
	if(this.getMessageNode(messageID)) {
		return;
	}		
	if(!this.onNewMessage(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip)) {
		return;
	}
	
	if (messageText.indexOf('knocked unconscious') != -1) {
		//alert("Here, we would play the victory music. Instead, I'll hum it for you... *hmmhmmhmm...*");
		// this.playSound(this.settings['soundVictory']);
	}
	
	this.updateDOM(
		'chatList',
		this.getChatListMessageString(
			dateObject, userID, userName, userRole, messageID, messageText, channelID, ip, characterName, characterID, roleplayID, characterURL, roleplayURL
		)
	)
}

ajaxChat.getChatListMessageString = function(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip, characterName, characterID, roleplayID, characterURL, roleplayURL) {
	var rowClass = 	this.dom['chatList'].lastChild && this.getClass(this.dom['chatList'].lastChild) == 'rowOdd'
					? 'rowEven' : 'rowOdd';
	var userClass = this.getRoleClass(userRole);
	var colon;
	var characterImage = '';
	var authorDiv;
	var textParts = messageText.split(' ');
	var groupClass = (this.onlineUsersArray[userID]) ? this.onlineUsersArray[userID]['groupClass'] : '';
	var messageType = '';
	
	if(messageText.indexOf('/action') == 0 || messageText.indexOf('/me') == 0 || messageText.indexOf('/privaction') == 0) {
		userClass += ' action';
		colon = ' ';
	} else if  (messageText.indexOf('/privmsgto') == 0) {
		rowClass += ' private';
		userClass += ' action';
		colon = ' <em>(to <strong>' + textParts[1] + '</strong>)</em>: ';
	} else if (messageText.indexOf('/privmsg') == 0) {
		rowClass += ' private';
		userClass += ' action';
		colon = ' <em>(whispers)</em>: ';
	} else if (messageText.indexOf('/ooc') == 0) {
		colon = ' says: ';
	} else if (messageText.indexOf('/say') == 0) {
		userClass += ' action';
		messageType = 'Speech';
		colon = ' says:';	
	} else {
		if (channelID == 0) {
			colon = ' says: ';
		} else {
			colon = '';
		}
	}
	var dateTime = this.settings['dateFormat'] ? '<span class="dateTime">'
					+ this.formatDate(this.settings['dateFormat'], dateObject) + '</span> ' : '';
		
		
		var myCharacterName = 'barbeequeue!';
					
	//if (userID != this.userID) {
		if (messageText.match(this.userName) || messageText.match(myCharacterName)) {
			rowClass += ' highlight';
			
			// messageText = messageText.replace(this.userName,'<strong>'+this.userName+'</strong>');
			ajaxChat.setStatus('Alert');
		}
	//}				
		
		
		// Link to hashtags
		// WARNING: THIS MAY CAUSE PROBLEMS
		// TODO: Make it work better, safer
		// TODO: Test.
		// messageText = this.decodeSpecialChars(messageText);
		// messageText = messageText.parseHashtag();
		// messageText = this.encodeSpecialChars(messageText);		

		// Hashtags
		messageText = messageText.replace(/(^| )#([A-Za-z0-9_-]+)(?![A-Za-z0-9_\]-])/g, "$1<a href=\"http://www.roleplaygateway.com/tag/$2\">#$2</a>");
		
		if ((characterID != userID) && (characterName != null) && (userID != 2147483647) && (channelID != 0)) {
		
		
			characterImage = '<a href="http://www.roleplaygateway.com/roleplay/' + roleplayURL + '/characters/' + characterURL + '/" target="_new"><img style=\"height:30px;width:30px;float:left; padding-right: 10px; padding-bottom: 10px;" src="http://www.roleplaygateway.com/images/character_avatar.php?character_url=' + characterURL + '" alt="' + characterName + '\'s Portrait" title="' + characterName + '\'s Portrait (Click to view full profile)" /></a>';
			
			authorDiv 	= '<div style="padding-right:5px;" class="charactername"><span class="character" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + characterName + '</span>' + colon + '</div>'
						+ '<div style="font-size:0.7em; padding-right:5px;" class="username"><em>played by <span class="' + userClass + groupClass + '" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + userName + '</span></em></div>';	
// 		} elseif (roleplayURL.length() > 0) {


			var icMessage = '';
			
			//icMessage = '<span style="float:left; text-align:right; width: 180px; padding-right: 5px; font-size: 0.7em;" class="username"><em><span class="' + userClass + groupClass + '" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + userName + '</span> writes:</em></span>';
			
			icMessage += '<div style="clear:none; padding-left: 10px;">';
			
			if ((messageText.indexOf('/me') == 0) || messageType == "Speech") {
				icMessage += '' + characterImage + '<span style="font-style: italic;"><span class="character" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">'+ characterName + '</span>' + colon + ' </span>';
			} else {
				icMessage += '<span class="character" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + characterImage + '</span>';				
			}
			
			if (messageType == 'Speech') {
				icMessage += '<blockquote class="dialogue-bubble" style="margin-left:50px;"><span class="pointer"> </span>&ldquo;';
			}

			icMessage += this.replaceText(messageText);
					
			if (messageType == 'Speech') {
				icMessage += '&rdquo;</blockquote>';
			}
			
			icMessage += '</div>';


			return	'<div id="'
					+ this.getMessageDocumentID(messageID)
					+ '" class="'
					+ rowClass
					+ '" style="clear:both; padding:2px;">'
					+ this.getDeletionLink(messageID, userID, userRole, channelID)
					+ '<div style="float:right; padding-bottom: 10px;"><span class="timestamp">' + dateTime + '</span>'
					+ '<span style="float:right; text-align:right; padding-right: 5px; font-size: 0.7em;" class="username"><em><span class="' + userClass + groupClass + '" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + userName + '</span></em></span></div>'

					+ icMessage
				
					// Nasty CSS hack... TODO: fix this with ONLY css?
					+ '<hr style="display:block; clear:left; visibility:hidden; margin: 0;" />'
					+ '</div>';
			
			
			
			
		} else {

			if (userID == 2147483647) {
				userClass = 'chatBot';
				// userName = characterName;
			}

			// Hardcoded. :(
			var channelLimit = 500000000;

			if ((this.channelID > 0 && channelID < channelLimit) && (messageText.indexOf('/say') != 0)) {
				rowClass += ' ooc';
			}
			
			authorDiv 	= '<div style="font-size:0.9em; padding-right:5px;" class="username">' + '<span class="' + userClass + groupClass + '" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + userName + '</span>' + colon + '</div>';
		}
					
	return	'<div id="'
			+ this.getMessageDocumentID(messageID)
			+ '" class="'
			+ rowClass
			+ '" style="clear:both; padding:2px;">'
			+ this.getDeletionLink(messageID, userID, userRole, channelID)
			+ '<span class="timestamp">' + dateTime + '</span>'
			+ '<div style="float:left; width:180px; text-align:right;">'
			+ characterImage
			+ '		<div>'
			+ authorDiv
			+ '		</div>'
			+ '</div>'
			+ '<div style="padding-left:180px;">' + this.replaceText(messageText) + '</div>'
			// Nasty CSS hack... TODO: fix this with ONLY css?
			+ '<hr style="display:block; clear:left; visibility:hidden; margin: 0;" />'
			+ '</div>';
}

ajaxChat.getUserNodeString = function(userID, userName, userRole, groupClass) {
	if(this.userNodeString && userID == this.userID) {
		return this.userNodeString;
	} else {
		var encodedUserName = this.scriptLinkEncode(userName);
		
		var extraDetails = "";
		
		if(this.userRole == 2 || this.userRole == 3) {
		
				if (this.onlineUserDetails[userID][0] > 0 || this.onlineUserDetails[userID][1] > 0) {
		
					extraDetails = '<abbr style="color:red;" title="'
						+ this.onlineUserDetails[userID][0] + ' warnings, '
						+ this.onlineUserDetails[userID][1] + ' notes'
						+ '">(!)</abbr> ';
				}
		}	
		
		var str	= '<div id="'
				+ this.getUserDocumentID(userID)
				+ '"><a href="javascript:ajaxChat.toggleUserMenu(\''
				+ this.getUserMenuDocumentID(userID)
				+ '\', \''
				+ encodedUserName
				+ '\', '
				+ userID
				+ ');" class="'
				+ this.getRoleClass(userRole)
				+ groupClass
				+ '" title="'
				+ this.lang['toggleUserMenu'].replace(/%s/, userName)
				+ '">'
				+ extraDetails
				+ userName
				+ '</a>'
				+ '<ul class="userMenu" id="'
				+ this.getUserMenuDocumentID(userID)
				+ '"'
				+ ((userID == this.userID) ?
					' style="display:none;">'+this.getUserNodeStringItems(encodedUserName, userID, false) :
					' style="display:none;">')
				+ '</ul>'
				+'</div>';
				
/* 		if (this.userID == 4) {
		
			str	= '<div id="'
				+ this.getUserDocumentID(userID)
				+ '"><a href="javascript:ajaxChat.toggleUserMenu(\''
				+ this.getUserMenuDocumentID(userID)
				+ '\', \''
				+ encodedUserName
				+ '\', '
				+ userID
				+ ');" class="'
				+ this.getRoleClass(userRole)
				+ '" title="'
				+ 'Roleplay: \n'
				+ 'Character: \n\n'
				+ '(' + this.lang['toggleUserMenu'].replace(/%s/, userName) + ' by clicking)'
				+ '">'
				+ userName
				+ '</a>'
				+ '<ul class="userMenu" id="'
				+ this.getUserMenuDocumentID(userID)
				+ '" style="display:none;">'
				+ '</ul>'
				+'</div>';
		
		} */
		
		
		if(userID == this.userID) {
			this.userNodeString = str;
		}
		return str;	
	}
}

ajaxChat.onNewMessage = function(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip) {
	if(!this.customOnNewMessage(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip)) {
		return false;
	}
	if(this.ignoreMessage(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip)) {
		return false;
	}
	if(this.parseDeleteMessageCommand(messageText)) {
		return false;
	}
	this.blinkOnNewMessage(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip);
	this.playSoundOnNewMessage(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip);
	return true;
}

ajaxChat.blinkOnNewMessage = function(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip) {
	if(this.settings['blink'] && this.lastID && !this.channelSwitch && userID != this.userID) {
		clearInterval(this.blinkInterval);
		this.blinkInterval = setInterval(
			'ajaxChat.blinkUpdate(\''+this.addSlashes(this.decodeSpecialChars(userName))+'\')',
			this.settings['blinkInterval']
		);
	}
	
	if ((channelID == 0) && this.lastID) {
		$('#viewList_channel_'+channelID).addClass('highlight newMessages');
		$('#viewList_channel_ooc').addClass('highlight newMessages');
	}
}

ajaxChat.replaceBBCodeImage = function(url) {
	if(this.settings['bbCodeImages']) {
		if (!arguments.callee.regExpUrl) {
			arguments.callee.regExpUrl = new RegExp(
				this.regExpMediaUrl,
				''
			);
		}
		if(!url || !url.match(arguments.callee.regExpUrl))
			return url;
		url = url.replace(/\s/gm, this.encodeText(' '));
		var maxWidth = 250;
		var maxHeight = 250;
		return	'<a href="'
				+url
				+'" onclick="window.open(this.href); return false;">'
				+'<img class="bbCodeImage" style="max-width:'
				+maxWidth
				+'px; max-height:'
				+maxHeight
				+'px;" src="'
				+url
				+'" alt="" onload="ajaxChat.updateChatlistView();"/></a>';
	}
	return url;
}

  ajaxChat.replaceBBCodeStrikethrough = function(content) {
          return  '<span style="text-decoration: line-through;">'
                          + this.replaceBBCode(content)
                          + '</span>';
  }
  ajaxChat.replaceBBCodeCallback = function(str, p1, p2, p3) {
          // Only replace predefined BBCode tags:
          if(!ajaxChat.inArray(ajaxChat.bbCodeTags, p1)) {
                  return str;
          }
          // Avoid invalid XHTML (unclosed tags):
          if(ajaxChat.containsUnclosedTags(p3)) {
                  return str;
          }
          switch(p1) {
                  case 'color':
                          return ajaxChat.replaceBBCodeColor(p3, p2);
                  case 'url':
                          return ajaxChat.replaceBBCodeUrl(p3, p2);
                  case 'img':
                          return ajaxChat.replaceBBCodeImage(p3);
                  case 'quote':
                          return ajaxChat.replaceBBCodeQuote(p3, p2);
                  case 'code':
                          return ajaxChat.replaceBBCodeCode(p3);
                  case 'u':
                          return ajaxChat.replaceBBCodeUnderline(p3);
                  case 's':
                          return ajaxChat.replaceBBCodeStrikethrough(p3);
                  default:
                          return ajaxChat.replaceCustomBBCode(p1, p2, p3);
          }
  }
	
//Override the handleInfoMessage function for welcome messages
ajaxChat.handleInfoMessage = function(infoType, infoData) {
	switch(infoType) {
		case 'channelSwitch':
			this.clearChatList();
			this.clearOnlineUsersList();
			this.setSelectedChannel(infoData);
			this.channelName = infoData;
			this.channelSwitch = true;
			break;			
		case 'channelName':
			this.setSelectedChannel(infoData);
			this.channelName = infoData;
			break;
		case 'channelID':
			this.channelID = infoData;
			break;
		case 'userID':
			this.userID = infoData;
			break;			
		case 'userName':
			this.userName = infoData;
			this.encodedUserName = this.scriptLinkEncode(this.userName);
			this.userNodeString = null;
			break;
		case 'userRole':
			this.userRole = infoData;
			break;				
		case 'logout':
			this.handleLogout(infoData);
			return;
		case 'socketRegistrationID':
			this.socketRegistrationID = infoData;
			this.socketRegister();
		default:
			this.handleCustomInfoMessage(infoType, infoData);
	}
}

ajaxChat.updateChatlistView = function() {		
	if(this.dom['chatList'].childNodes && this.settings['maxMessages']) {
		while(this.dom['chatList'].childNodes.length > this.settings['maxMessages']) {
			this.dom['chatList'].removeChild(this.dom['chatList'].firstChild);
		}
	}
	
	this.updateChatListRowClasses();
	
	if(this.settings['autoScroll']) {
	
		$('#chatList').animate({
			scrollTop: this.dom['chatList'].scrollHeight
		});
	
	}
}

ajaxChat.updateChatListRowClasses = function(node) {
	if(!node) {
		node = this.dom['chatList'].firstChild;
	}
	if(node) {
		var previousNode = node.previousSibling;
		var rowEven = (previousNode && this.getClass(previousNode) == 'rowOdd') ? true : false;
		while(node) {
			var nodeclass = this.getClass(node);
			var ooc = "";
			var highlight = "";

			if (nodeclass.match('ooc')) {
				ooc = " ooc ";
			}
			
			if (nodeclass.match('highlight')) {
				highlight = " highlight";
			}

			this.setClass(node, (rowEven ? 'rowEven' + ooc + highlight: 'rowOdd' + ooc + highlight));
			
			if (nodeclass.match('ooc')) {
			
				if (this.settings['oocChat'] == false) {
					node.style.display = 'none';
				} else {
					node.style.display = 'block';
				}

			}
			
			node = node.nextSibling;
			rowEven = !rowEven;
		}
	}
}

ajaxChat.handleResponse = function(identifier) {
	if (this.getHttpRequest(identifier).readyState == 4) {
		if (this.getHttpRequest(identifier).status == 200) {
			clearTimeout(ajaxChat.retryTimer);
			var xmlDoc = this.getHttpRequest(identifier).responseXML;
			ajaxChat.setStatus('Off');
		} else {
			// Connection status 0 can be ignored.
			if (this.getHttpRequest(identifier).status == 0) {
				ajaxChat.setStatus('On');
				this.updateChatlistView();
				return false;
			} else {
				// this.addChatBotMessageToChatList('/error ConnectionStatus '+this.getHttpRequest(identifier).status+' - It looks like we are having trouble connecting to the server. Give us 30 seconds and we will attempt to resolve this.');
				ajaxChat.setStatus('Alert');
				this.updateChatlistView();				
				return false;
			}
		}
	}
	if(!xmlDoc) {
		return false;
	}
	this.handleXML(xmlDoc);
	return true;
}

ajaxChat.getGroupClass = function(groupID) {
		switch (groupID) {
			// These must be strings
			case '2637':
				groupClass= ' coders';
			break;
			case '2635':
				groupClass= ' designers';
			break;
			case '2629':
				groupClass= ' mentors';
			break;
			default:
				groupClass = '';
			break;
		}
		
		return groupClass;
}

ajaxChat.handleOnlineUsers = function(userNodes) {

	if(userNodes.length) {
		var index,userID,userName,userRole,groupID,groupClass;
		var onlineUsers = new Array();
		for(var i=0; i<userNodes.length; i++) {
			userID = userNodes[i].getAttribute('userID');
			userName = userNodes[i].firstChild ? userNodes[i].firstChild.nodeValue : '';
			userRole = userNodes[i].getAttribute('userRole');
			isGameMaster = userNodes[i].getAttribute('isGameMaster');
			groupID  = userNodes[i].getAttribute('groupID');
			groupClass = this.getGroupClass(groupID);
			
			this.onlineUsersArray[userID] = new Object();
			this.onlineUsersArray[userID]['groupClass'] = groupClass;

			// User Notes and Warnings
			if(this.userRole == 2 || this.userRole == 3) {
				this.onlineUserDetails[userID] = new Array(2);
				this.onlineUserDetails[userID][0] = userNodes[i].getAttribute('warnings');
				this.onlineUserDetails[userID][1] = userNodes[i].getAttribute('notes');
				
				if (this.userID == 4) {
					//alert(userNodes[i].getAttribute('notes'));
				}
			}
			
			this.onlineUsers[i] = userName;
			
			onlineUsers.push(userID);
			index = this.arraySearch(userID, this.usersList);
			if(index === false) {
				this.addUserToOnlineList(
					userID,
					userName,
					userRole,
					groupClass
				);
			} else if(this.userNamesList[index] != userName) {
				this.removeUserFromOnlineList(userID, index);
				this.addUserToOnlineList(
					userID,
					userName,
					userRole,
					groupClass
				);
			}
		}
		// Clear the offline users from the online users list:
		for(var i=0; i<this.usersList.length; i++) {
			if(!this.inArray(onlineUsers, this.usersList[i])) {
				this.removeUserFromOnlineList(this.usersList[i], i);
			}
		}	
		this.setOnlineListRowClasses();		
	}	
}

ajaxChat.addUserToOnlineList = function(userID, userName, userRole, groupClass) {
	this.usersList.push(userID);
	this.userNamesList.push(userName);	
	if(this.dom['onlineList']) {
		this.updateDOM(
			'onlineList',
			this.getUserNodeString(userID, userName, userRole, groupClass),
			(this.userID == userID)
		);
	}
}

ajaxChat.summonModerator = function() {
	var answer = confirm("Are you sure you want to summon a moderator to come to this room?\n\nYou will be unable to chat for about one minute (to prevent abuse of this feature).");
	if (answer) {
		this.sendMessage('/summonModerator');
	}
}

ajaxChat.handleInputFieldKeyPress = function(event) {
	if(event.keyCode == 13 && !event.shiftKey) {
	
		var thisMessage = this.dom['inputField'].value;
		
		var i = this.messageHistory.length; // This is INTENTIONALLY not subtracting "1" because we want our insertion ID to be one above the last one. Intentionally. Intentionally. 
		this.lastQueueID = 0; // Set lastID back to 0
		this.messageHistory[i] = thisMessage;
	
		this.sendMessage();
		try {
			event.preventDefault();
		} catch(e) {
			event.returnValue = false; // IE
		}
		return false;
	}
	
	if (event.keyCode == 9) {
		try {
			event.preventDefault();
		} catch(e) {
			event.returnValue = false; // IE
		}
		this.autocomplete(this.dom['inputField'], this.onlineUsers); 
		return false;
	}
	
	if ((event.keyCode == 38) && (this.dom['inputField'].value.length > 0)) {
		try {
			event.preventDefault();
		} catch(e) {
			event.returnValue = false; // IE
		}
		// this.autocomplete(this.dom['inputField'], this.onlineUsers); 
		
		if (this.lastQueueID == 0) {
			var messageQueueID = this.messageHistory.length - 1;
		} else {
			var messageQueueID = this.lastQueueID - 1;
		}
		// this.dom['inputField'].value = this.messageHistory[messageQueueID];
		
		return false;
	}
	
	return true;
}

// takes a text field and an array of strings for autocompletion
ajaxChat.autocomplete = function(input, data) {
    var index = input.value.lastIndexOf(" ") + 1;
    var name = input.value.slice(index)
    var old = input.value.slice(0, index)
    var candidates = []
    // filter data to find only strings that start with existing value
    for (var i in data) {
      if (data[i].indexOf(name) == 0 && data[i].length > name.length)
        candidates.push(data[i])
    }

    if (candidates.length > 0) {   
      // some candidates for autocompletion are found
      if (candidates.length == 1) {
        input.value = old + candidates[0]
        if (old.length == 0) {
            input.value += ":"
        }
        input.value += " "
      }
      else input.value = old + this.longestInCommon(candidates, name.length)
      return true
    }
  return false
}

// finds the longest common substring in the given data set.
// takes an array of strings and a starting index
ajaxChat.longestInCommon = function(candidates, index) {
  var i, ch, memo
  do {
    memo = null
    for (i=0; i < candidates.length; i++) {
      ch = candidates[i].charAt(index)
      if (!ch) break
      if (!memo) memo = ch
      else if (ch != memo) break
    }
  } while (i == candidates.length && ++index)

  return candidates[0].slice(0, index)
}

ajaxChat.popOut = function() {
	window.location = 'http://www.roleplaygateway.com/embed/poppedOut.html';
	ajaxChat.popout = window.open(window.location, "RPGChat-popout", "menubar=no,status=no,width=700,height=550,toolbar=no,location=no");
}

/* Client-side access to querystring name=value pairs
	Version 1.3
	28 May 2008
	
	License (Simplified BSD):
	http://adamv.com/dev/javascript/qslicense.txt
*/
function Querystring(qs) { // optionally pass a querystring to parse
	this.params = {};
	
	if (qs == null) qs = location.search.substring(1, location.search.length);
	if (qs.length == 0) return;

// Turn <plus> back to <space>
// See: http://www.w3.org/TR/REC-html40/interact/forms.html#h-17.13.4.1
	qs = qs.replace(/\+/g, ' ');
	var args = qs.split('&'); // parse out name/value pairs separated via &
	
// split out each name=value pair
	for (var i = 0; i < args.length; i++) {
		var pair = args[i].split('=');
		var name = decodeURIComponent(pair[0]);
		
		var value = (pair.length==2)
			? decodeURIComponent(pair[1])
			: name;
		
		this.params[name] = value;
	}
}

Querystring.prototype.get = function(key, default_) {
	var value = this.params[key];
	return (value != null) ? value : default_;
}

Querystring.prototype.contains = function(key) {
	var value = this.params[key];
	return (value != null);
}

String.prototype.toProperCase = function()
{
  return this.toLowerCase().replace(/^(.)|\s(.)/g, 
      function($1) { return $1.toUpperCase(); });
}

String.prototype.parseHashtag = function() {
	return this.replace(/[^&][#]+[A-Za-z0-9-_]+(?!])/, function(t) {
		var tag = t.replace("#","")
		return t.link("http://www.roleplaygateway.com/tag/"+tag);
	});
}
