<?php


require('/var/www/html/config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);



		//if ($purgeDelay < (time() - 3600) || !isset($purgeDelay)) {

			// SQL Query to select messages for purge:
			$sql = 'SELECT
						userName,
						channel AS channelID,
						UNIX_TIMESTAMP(dateTime) AS timeStamp,
						text,
						dateTime
					FROM
						ajax_chat_messages
					WHERE channel = 0
					ORDER BY id';

			$result = $mysqli->query($sql);


			// Store result for logging:
			$logMsg = '';
			while($row = $result->fetch_assoc()) {

        if ($row['username'] == 'HAL') { continue; }

				$row['username'] = str_replace(" ","_",$row['username']);					


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
						$logMsg .= '* '.$row['userName'].' ';
						$logMsg .= preg_replace("#^/me #","",$row['text'])."\n";					
					
					} else {						
						$logMsg .= '<'.$row['userName'].'> ';
						$logMsg .= $row['text']."\n";
					}
				}
			}
			$result->close();
			$mysqli->close();

			// Files are rotated every week, labelled by week number, month, and year.
			// File container where all messages are logged:
			$fileContainer = '/var/www/stats.roleplaygateway.com/chat.log';
			$fileDirectory = '/var/www/stats.roleplaygateway.com/';

			// Check to make sure directory is writable:
			if(is_writable($fileDirectory)) {
				// Open the said file:
				$filePointer = fopen($fileContainer,"w+");

				// Write log messages to file:
				fputs($filePointer,$logMsg);

				// Close the open said file after writing:
				fclose($filePointer);
				
			} else {
				die('cannot write to directory');
			}

			$purgeDelay = time();
		//}

?>
