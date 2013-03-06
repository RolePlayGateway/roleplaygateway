<?php



switch (true) {

	// BEGIN responses PRIVATE

	case stristr($text,"help me"):
		$this->insertChatBotMessage($this->getPrivateMessageID(),"If you're looking for help, start by contacting our [url=http://www.roleplaygateway.com/memberlist.php?mode=group&g=2625]Global Moderators[/url] (GMs for short).");
	break;
	case (strlen($text) >= 512):
		$this->insertChatBotMessage($this->getPrivateMessageID(),"Please don't forget that the chat is for shorter and faster-paced roleplay, while the forum is for the longer and more descriptive posts.  If you keep your chat messages between one and two sentences, that lets other people interact with you more frequently, making for a better roleplay!  Then everyone doesn't have to wait so long for posts.");
	break;
	
	// BEGIN responses PUBLIC
	case stristr($text,"!quote"):
		$sql = 'SELECT author,text FROM rpg_quotes ORDER BY rand() LIMIT 1';
		$result = $db->sql_query($sql);
		$quote = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$this->insertChatBotMessage($this->getChannel(),'[quote="'.$quote['author'].'"]'.$quote['text'].'[/quote]');
	break;
	case preg_match('/I((\'?m)|( am))(.*)bored/i',$text):
		$this->insertChatBotMessage($this->getChannel(),"Boredom is a sign of one's inability to be imaginative!  Go [url=http://www.roleplaygateway.com/roleplay/]browse open roleplays[/url] or [url=http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=new]create a new one[/url].");
	break;
	case preg_match('/chat d((ea)|(ie))d/i',$text):
		$this->insertChatBotMessage($this->getChannel(),"No, chat isn't dead. People are just busy [url=http://www.roleplaygateway.com/viewonline.php]roleplaying[/url].");
	break;
	case stristr($text,"!grammar"):
		$this->insertChatBotMessage($this->getChannel(),"Please be respectful of our adult sanity and use proper grammar. If you don't, we have a hard time understanding what you're trying to say.");
	break;	
	case stristr($text,"!kris"):
		$this->insertChatBotMessage($this->getChannel(),"Our long time friend and GM [b]kris0the0girl[/b] has left RolePlayGateway to go away to nursing school, handle work, and enjoy her upcoming marriage. [url=http://www.youtube.com/watch?v=wYU11zNUhNI]Watch the video[/url] and send her your support and best wishes!");
	break;		
	case stristr($text,"!question"):
		$this->insertChatBotMessage($this->getChannel(),"So you want to ask a question?  Don't ask to ask, just [b]ask[/b]!");
	break;			
/* 	case stristr($text,"!9000"):
		$this->insertChatBotMessage($this->getChannel(),"[img]http://www.roleplaygateway.com/images/over_9000.gif[/img]");
	break;		 */
/* 	case stristr($text,"!troll"):
		$this->insertChatBotMessage($this->getChannel(),"[url=http://www.youtube.com/watch?v=6bMLrA_0O5I][img]http://i195.photobucket.com/albums/z91/Pheloz/boxxy-trolling.jpg[/img][/url]");
	break;	 */	
	case stristr($text,"!newuser"):
		$this->insertChatBotMessage($this->getChannel(),"As a new user, you should check out the following links: \n- [url=http://www.roleplaygateway.com/the-official-roleplaygateway-rules-t1369.html]RPG Rules[/url] (we only have five!)\n- [url=http://www.roleplaygateway.com/role-play-academy-f125.html]The RolePlayAcademy[/url]: great for roleplaying help!\n- [url=http://www.roleplaygateway.com/help-f11.html]The Help Forum[/url] - useful for getting answers to specific questions.\n- [url=http://www.roleplaygateway.com/simple-beginner-guide-roleplaygateway-chat-system-t32024.html]The Beginner's guide to RPG Chat[/url]: explains the special roleplaying features in this chat.");
	break;		
/* 	case stristr($text,"!thegame"):
		$this->insertChatBotMessage($this->getChannel(),"[url=http://epicponyz.files.wordpress.com/2009/06/sorry-you-just-lost-the-game.jpg]You lost it.[/url]");
	break;		 */	
/* 	case stristr($text,"!barrelroll"):
		$this->insertChatBotMessage($this->getChannel(),"[img]http://rookery5.aviary.com/storagev12/3392500/3392668_3894_625x625.jpg[/img]");
	break; */
	/* case stristr($text,"!sad"):
		$this->insertChatBotMessage($this->getChannel(),"Awesomeness: When you get sad, [i]stop[/i] being sad and [b]be awesome instead[/b].\n[img]http://www.roleplaygateway.com/images/motivationals/awesomeness.jpg[/img]");
	break; */		
	case stristr($text,"!feedback"):
		$this->insertChatBotMessage($this->getChannel(),"Please direct all feedback/suggestions to [url=http://rpg.uservoice.com]our feedback forum[/url]; we [i]love[/i] to hear them, but we aren't fast enough to handle everyone's suggestions in chat.");
	break;
	case stristr($text,"!autocomplete"):
		$this->insertChatBotMessage($this->getChannel(),"If you want someone's attention, make sure to include their [b]full name[/b] in your message [u][b]and[/b] include the question/statement[/u]. This highlights the line on their screen and makes it so once you have their attention, they don't have to wait to find out what you actually want from them.  You can type the first few letters in their name and hit [TAB] to automatically put it in your chat box, or just click the name on one of their messages to insert it.");
	break;	
	case stristr($text,"!occ"):
		$this->insertChatBotMessage($this->getChannel(),"Make sure you use the acronym 'OOC' correctly! [b]O[/b]ut [b]O[/b]f [b]C[/b]haracter (OOC).  The other word, 'OCC', actually means [b]O[/b]riginal [b]C[/b]anon [b]C[/b]haracter, something completely different.");
	break;
	case stristr($text,"!hor"):
		$this->insertChatBotMessage($this->getChannel(),"[url=http://www.thegrandtournament.com]The Hall of Records (HoR)[/url] is the permenent repository for the results of text-based fighting.  Every two years, the top fighters from this recordkeeper are invited to \"The Grand Tournament\" to determine who is the internet's best fighter.");
	break;
	case stristr($text,"!eve"):
		$this->insertChatBotMessage($this->getChannel(),"EVE Online is an MMORPG that doesn't have levels or experience points (no grinding!) - it focuses on [url=http://www.youtube.com/watch?v=08hmqyejCYU]'emergent' gameplay (YouTube Video)[/url] and actual (!) roleplay. You can play with Remaeus on a [url=https://secure.eve-online.com/ft/?aid=103657]free 14 day trial[/url], or [url=http://www.roleplaygateway.com/ucp.php?i=pm&mode=compose&u=4]send him a PM[/url] to see if he has any 21 day trials available.  [b]If you already play, join the in-game channel \"RolePlayGateway\"![/b]");
	break;
	case stristr($text,"!last.fm"):
		$this->insertChatBotMessage($this->getChannel(),"RolePlayGateway can be found on [url=http://last.fm]Last.fm[/url], you should join the [url=http://www.last.fm/group/RolePlay+Gateway]RolePlayGateway group on Last.fm[/url]!  (You can also [url=http://last.fm/user/Remaeus/]spy on the site owner's listening habits[/url] and make fun of his music selection)");
	break;
	case stristr($text,"!facebook"):
		$this->insertChatBotMessage($this->getChannel(),"RolePlayGateway can be found on [url=http://www.facebook.com]Facebook[/url], you should [url=http://www.facebook.com/pages/RolePlayGateway/27605290251]become a fan of RolePlayGateway[/url]!");
	break;
	case stristr($text,"!meetup"):
		$this->insertChatBotMessage($this->getChannel(),"Interested in meeting up with fellow RolePlayGateway members?  Register on [url=http://www.meetup.com/RolePlayGateway/]our meetup group[/url] to get notified of any earth-shattering parties that we're having.");
	break;
	case stristr($text,"!twitter"):
		$this->insertChatBotMessage($this->getChannel(),"Hey, Twitter!  Come follow [url=http://twitter.com/RolePlayGateway]RolePlayGateway on Twitter[/url]!");
	break;
	case stristr($text,"!myspace"):
		$this->insertChatBotMessage($this->getChannel(),"Can has MySpaces? If you're one of the [url=http://siteanalytics.compete.com/facebook.com+myspace.com/]stubborn few[/url] still using MySpace, you can [url=http://groups.myspace.com/index.cfm?fuseaction=groups.groupProfile&groupID=105745464]join RolePlayGateway's MySpace group.[/url]");
	break;
	case stristr($text,"!youtube"):
		$this->insertChatBotMessage($this->getChannel(),"If you've got an account on YouTube, you can [url=http://www.youtube.com/group/roleplaygateway]join the RolePlayGateway group[/url] and share your favorite videos with everyone, or even post videos you've made.");
	break;
	case stristr($text,"!google"):
	
		unset($textParts[0]);
		$searchText = implode("+",$textParts);
		$searchText = $this->removeBBCode($searchText);
		
		$engineStrings[] = 'http://lmgtfy.com/?q='.$searchText;
		$engineStrings[] = 'http://www.dinoogle.com/results/?cx=000479422399193962880:wnemoiokw0a&cof=FORID:10&ie=UTF-8&q='.$searchText.'&sa=Search';
		$engineStrings[] = 'http://www.google.com/?q='.$searchText;
		
		$engineKey = array_rand($engineStrings);
		
		$this->insertChatBotMessage($this->getChannel(),"Before asking questions of anyone, make sure you ask Google first.  ([url=".$engineStrings[$engineKey]."]direct link to search results here![/url])");

	break;
 	case stristr($text,"!latex"):
	
		unset($textParts[0]);
		$latexCode = implode(" ",$textParts);
		
		$latexCode = preg_replace("/\[\/color]/","",$latexCode);
		
		$this->insertChatBotMessage($this->getChannel(),"[img]http://latex.codecogs.com/gif.latex?".$latexCode."[/img]");

	break;
	
	case stristr($text,"!advertising"):
		$this->insertChatBotMessage($this->getChannel(),"We not only allow advertising here, but we [i]encourage[/i] it. Our goal is to help other sites grow, too. Just make sure you're not spamming; post it in the [url=http://www.roleplaygateway.com/directory/]RPG Directory[/url]!");
	break;
	case stristr($text,"gt league"):
		$this->insertChatBotMessage($this->getChannel(),"Please remember that the GT League no longer exists; it ended after Season 2.  It has been replaced with the [url=http://www.thegrandtournament.com]Hall of Records[/url], and [b]all[/b] turn-based fights can be recorded there, no matter how old they are.");
	break;
	case stristr($text,"new account"):
		$this->insertChatBotMessage($this->getChannel(),"Please don't make multiple accounts; it's not against our rules, but it works much better when you [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]create your characters[/url] all on one account. If you need multiple accounts merged or other account help, [url=http://www.roleplaygateway.com/memberlist.php?mode=group&g=2626]PM the Administrator group[/url] and we will help you.");
	break;
	case stristr($text,"Connection status: 500"):
		$this->insertChatBotMessage($this->getChannel(),"Error 500 means that you were checking the server for messages at the [i]exact[/i] time that the config was being updated. Just type any command (like [code]/list[/code] and you will automatically reconnect.");
	break;
	case stristr($text,"Connection status: 0"):
		$this->insertChatBotMessage($this->getChannel(),"Error 0 means that the server has gone away. Just type any command (like [code]/list[/code]) and you will automatically reconnect.");
	break;
	case stristr($text,"pen the pod bay doors"):
		$this->insertCustomMessage($this->getConfig('chatBotID'), 'HAL', AJAX_CHAT_CHATBOT, $this->getChannel(), "I'm sorry, I can't do that right now." );		
	break;
	
	
	
	
	
/* 	// BEGIN HAL
	
	case stristr($text,'HAL'):

	break; */
	
}

/**
 * This function passes our data to NuSOAP, and
 * returns the search results:
 */
		
	function getResultArray($id, $site, $baseurl) {
		// Get the parameters:
		//$params = setParams($id, $site);
		
		return false;
		
		// Include the library:
		include_once("libs/nusoap.php");
		// Create a instance of the SOAP client object
		$soapclient = new soapclient($baseurl);
		$data = $soapclient->call("doGoogleSearch", $params,
		"urn:GoogleSearch", "urn:GoogleSearch"); 
		return $data;
	}
	
?>