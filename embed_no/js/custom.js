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
 
 ajaxChat.replaceCustomCommands = function(text, textParts) { 
	switch(textParts[0]) { 
		case '/takeover': 
			text=text.replace('/takeover', ' '); 
			return '<span class="chatBotMessage">' + text + '</span>'; 
		return text; 
	}
}

ajaxChat.handleInputFieldKeyPress = function(event) {
	if(event.keyCode == 13 && !event.shiftKey) {
		this.sendMessage();
		try {
			event.preventDefault();
		} catch(e) {
			event.returnValue = false; // IE
		}
		return false;
	}
	if(event.keyCode == 9) {
		try {
			event.preventDefault();
		} catch(e) {
			event.returnValue = false; // IE
		}
		return false;
	}
	/* this was meant to disable paste... may not be advisable (URLs) ~ Eric M 5/1/09
	TODO: Figure out how to validate a URL versus a paste?
	if((event.ctrlKey || event.keyCode == 17) && event.keyCode == 86) {
		alert("fail");
		return false;
	} */
	return true;
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
					+ '<li><a href="javascript:ajaxChat.insertMessageWrapper(\'/describe '
					+ encodedUserName
					+ ' \');">'
					+ this.lang['userMenuDescribe']
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

ajaxChat.handleChatMessages = function(messageNodes) {
	if(messageNodes.length) {
		var userNode,userName,textNode,messageText,characterNode,CharacterName;		
		for(var i=0; i<messageNodes.length; i++) {
			userNode = messageNodes[i].getElementsByTagName('username')[0];
			userName = userNode.firstChild ? userNode.firstChild.nodeValue : '';
			characterNode = messageNodes[i].getElementsByTagName('character')[0];
			characterName = characterNode.firstChild ? characterNode.firstChild.nodeValue : '';
			textNode = messageNodes[i].getElementsByTagName('text')[0];
			messageText = textNode.firstChild ? textNode.firstChild.nodeValue : '';
			this.addMessageToChatList(
					new Date(messageNodes[i].getAttribute('dateTime')),
					messageNodes[i].getAttribute('userID'),
					userName,
					messageNodes[i].getAttribute('userRole'),
					messageNodes[i].getAttribute('id'),
					messageText,
					messageNodes[i].getAttribute('channelID'),
					messageNodes[i].getAttribute('ip'),
					characterName
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

ajaxChat.addMessageToChatList = function(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip, characterName) {

	// Prevent adding the same message twice:
	if(this.getMessageNode(messageID)) {
		return;
	}		
	if(!this.onNewMessage(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip)) {
		return;
	}
	
	if (messageText.indexOf('knocked unconscious') != -1) {
		//alert("Here, we would play the victory music. Instead, I'll hum it for you... *hmmhmmhmm...*");
		this.playSound(this.settings['soundVictory']);
	}
	
	this.updateDOM(
		'chatList',
		this.getChatListMessageString(
			dateObject, userID, userName, userRole, messageID, messageText, channelID, ip, characterName
		)
	)
}

ajaxChat.getChatListMessageString = function(dateObject, userID, userName, userRole, messageID, messageText, channelID, ip, characterName) {
	var rowClass = 	this.dom['chatList'].lastChild && this.getClass(this.dom['chatList'].lastChild) == 'rowOdd'
					? 'rowEven' : 'rowOdd';
	var userClass = this.getRoleClass(userRole);
	var colon;
	var characterImage = 'http://www.roleplaygateway.com/character/' + characterName;
	var authorDiv;
	
	if(messageText.indexOf('/action') == 0 || messageText.indexOf('/me') == 0 || messageText.indexOf('/privaction') == 0) {
		userClass += ' action';
		colon = ' ';
	} else {
		colon = ' says: ';
	}
	var dateTime = this.settings['dateFormat'] ? '<span class="dateTime">'
					+ this.formatDate(this.settings['dateFormat'], dateObject) + '</span> ' : '';
					
	if (characterName != userName) {
			authorDiv 	= '<div style="padding-right:5px;" class="charactername"><strong><a class="' + userClass + '" href="http://www.roleplaygateway.com/characters/' + characterName + '" target="_new">' + characterName + '</a></strong>' + colon + '</div>'
						+ '<div style="font-size:0.7em; padding-right:5px;" class="username"><em><span class="' + userClass + '" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + userName + '</span> at ' + dateTime + '</em></div>';	
		} else {
			authorDiv 	= '<div style="font-size:0.9em; padding-right:5px;" class="username">' + dateTime + '<span class="' + userClass + '" onclick="ajaxChat.insertText(this.firstChild.nodeValue);">' + userName + '</span>' + colon + '</div>';
		}		
					
	return	'<div id="'
			+ this.getMessageDocumentID(messageID)
			+ '" class="'
			+ rowClass
			+ '" style="clear:both; padding:2px;">'
			+ this.getDeletionLink(messageID, userID, userRole, channelID)
			+ '<div style="float:left; width:200px; text-align:right;">'
			//+ '		<img style=\"height:30px;width:30px;float:left;" src="' + characterImage + '" />'
			+ '		<div>'
			+ authorDiv
			+ '		</div>'
			+ '</div>'
			+ '<div style="padding-left:200px;">' + this.replaceText(messageText) + '</div>'
			// Nasty CSS hack... TODO: fix this with ONLY css?
			+ '<hr style="display:block; clear:left; visibility:hidden; margin: 0;" />'
			+ '</div>';
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