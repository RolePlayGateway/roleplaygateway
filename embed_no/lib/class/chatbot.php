<?php



switch (true) {

	// BEGIN responses STAFF
	
	case stristr($text,"!checkmoderators"):

		$sql = 'SELECT userID FROM ajax_chat_online WHERE userRole = 2 OR userRole = 4';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			$moderators[] = $row['userID'];
		}
		$db->sql_freeresult($result);

		foreach ($moderators as $myID) {
			$this->insertChatBotMessage($this->getPrivateMessageID($myID),"[b]MODERATOR ALERT:[/b] Alert triggered (who said that using /broadcast?) ".$this->getChannel()." - teleport there using: [code]/teleport .".$this->getChannel().".[/code] ");
		}
	break;

	// BEGIN responses PRIVATE

	case stristr($text,"help me"):
		$this->insertChatBotMessage($this->getPrivateMessageID(),"If you're looking for help, start by contacting our [url=http://www.roleplaygateway.com/memberlist.php?mode=group&g=2625]Global Moderators[/url] (GMs for short).");
	break;
	case (strlen($text) >= 512):
		$this->insertChatBotMessage($this->getPrivateMessageID(),"Please don't forget that the chat is for shorter and faster-paced roleplay, while the forum is for the longer and more descriptive posts.  If you keep your chat messages between one and two sentences, that lets other people interact with you more frequently, making for a better roleplay!  Then everyone doesn't have to wait so long for posts.");
	break;
	
	// BEGIN responses PUBLIC
	
	case stristr($text,"!occ"):
		$this->insertChatBotMessage($this->getChannel(),"Make sure you use the acronym 'OOC' correctly! [b]O[/b]ut [b]O[/b]f [b]C[/b]haracter (OOC).  The other word, 'OCC', actually means [b]O[/b]riginal [b]C[/b]anon [b]C[/b]haracter, something completely different.");
	break;
	case stristr($text,"!test"):
		$this->insertChatBotMessage($this->getChannel(),$db->sql_escape("EVE Online is an MMORPG that doesn't have levels or experience points (no grinding!) - it focuses on [url=http://www.youtube.com/watch?v=08hmqyejCYU]'emergent' gameplay (YouTube Video)[/url]. You can play with Remæus on a [url=https://secure.eve-online.com/ft/?aid=103657]free 14 day trial[/url], or [url=http://www.roleplaygateway.com/ucp.php?i=pm&mode=compose&u=4]send him a PM[/url] to see if he has any 21 day trials available."));
	break;
	case stristr($text,"!eve"):
		$this->insertChatBotMessage($this->getPrivateMessageID(),"EVE Online is an MMORPG that doesn't have levels or experience points (no grinding!) - it focuses on [url=http://www.youtube.com/watch?v=08hmqyejCYU]'emergent' gameplay (YouTube Video)[/url]. You can play with Remæus on a [url=https://secure.eve-online.com/ft/?aid=103657]free 14 day trial[/url], or [url=http://www.roleplaygateway.com/ucp.php?i=pm&mode=compose&u=4]send him a PM[/url] to see if he has any 21 day trials available.");
	break;
	case stristr($text,"!last.fm"):
		$this->insertChatBotMessage($this->getChannel(),"RolePlayGateway can be found on [url=http://last.fm]Last.fm[/url], you should join the [url=http://www.last.fm/group/RolePlay+Gateway]RolePlayGateway group on Last.fm[/url]!");
	break;
	case stristr($text,"!facebook"):
		$this->insertChatBotMessage($this->getChannel(),"RolePlayGateway can be found on [url=http://www.facebook.com]Facebook[/url], you should [url=http://www.facebook.com/pages/RolePlayGateway/27605290251]become a fan of RolePlayGateway[/url]!");
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
	
		$searchText = "";

		$j = 1;
		while ($j <= (count($textParts))) {
			$searchText .= $textParts[$j] . "+";
			$j++;
		}
		
		$this->insertChatBotMessage($this->getChannel(),"Before asking questions of anyone, make sure you [url=http://www.google.com/search?q=".$searchText."]ask Google[/url] first!");

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
		$this->insertChatBotMessage($this->getChannel(),"I'm sorry, I can't do that right now.");
	break;	
	
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