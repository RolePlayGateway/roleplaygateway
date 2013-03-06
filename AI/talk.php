<?php

/*
    Program E
	Copyright 2002, Paul Rydell

	This file is part of Program E.
	
	Program E is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Program E is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Program E; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * HTML chat interface
 * 
 * Contains the script that outputs the HTML interface for chatting
 * @author Paul Rydell
 * @copyright 2002
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Interpreter
 * @subpackage Responder
 */
error_reporting(0);
ini_set('display_errors',true);

/**
* Include the guts of the program.
*/
//include("respond.php");

die('ehlo.');

if (isset($_REQUEST['input'])) {

	$numselects=0;

	// Start the session or get the existing session.
	session_start();
	$myuniqueid=session_id();

	// Here is where we get the reply.
	$botresponse=replybotname($_REQUEST['input'],1,$_REQUEST['botname']);

	// Print the results.
	print "<B>RESPONSE: " . $botresponse->response . "<BR></b>";
	print "<BR><BR>execution time: " . $botresponse->timer;
	print "<BR>numselects= $numselects";

	//print_r($botresponse->inputs);
	//print_r($botresponse->patternsmatched);

	// Include a form so they can say more. Note the hidden part for people that do not have trans sid on but want non-cookie users to be able to use the system.

	?>

	<html>
	<head>
	<title>Sample talk to Program E page</title>
	</head>
	<body>
	<form name="form1" method="post" action="talk.php">
	<input type="hidden" name="<? echo(session_name());?>" value="<? echo($uid); ?>">
	<input type="hidden" name="botname" value="<? echo($_REQUEST['botname']);?>">
	  Input: <input type="text" name="input" size="55">

	  <input type="submit" name="Submit" value="Submit">
	</form>
	</body>
	</html>

<?
} else {

	$availbots=array();

	// Get all the names of our bots.
	$query="select botname from bots";

    $selectcode = mysql_query($query);

    if ($selectcode) {
        if(!mysql_numrows($selectcode)) {
	} else {
            while ($q = mysql_fetch_array($selectcode)) {
                $availbots[]=$q[0];
            }
        }
    }

	?>

	<html>
	<head>
	<title>Program E Tester</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>

	<body bgcolor="#FFFFFF" text="#000000">
	<form name="form1" method="post" action="talk.php">

	Talk to: <select name="botname">
	<? 
	foreach ($availbots as $onebot){
		print "<option value=\"$onebot\">$onebot</option>";
	}
	?>
	</select><BR>

	  Input: <input type="text" name="input" size="55">

	  <input type="submit" name="Submit" value="Submit">
	</form>
	  
	</body>
	</html>

	<?

}

?>
